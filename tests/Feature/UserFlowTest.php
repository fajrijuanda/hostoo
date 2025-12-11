<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Subscription;
use App\Services\CyberPanelService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Mockery\MockInterface;

class UserFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_flow_with_manual_api_enable_and_late_package_creation()
    {
        // =========================================================================
        // Step 1: User Registration
        // =========================================================================
        
        Session::put('captcha_code', 'ABCDE');
        
        $password = 'password123';
        $userData = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => $password,
            'password_confirmation' => $password,
            'captcha' => 'ABCDE'
        ];

        $this->post(route('register.post'), $userData);
        
        $user = User::where('email', 'testuser@example.com')->first();
        $user->email_verified_at = now();
        $user->save();

        // =========================================================================
        // Step 2: Admin Approval 
        // Logic: Create User on CP -> Update DB -> Redirect to CP (for manual API enable)
        // =========================================================================

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_type' => 'starter',
            'price' => 50000,
            'status' => 'pending',
            'payment_method' => 'manual',
            'payment_proof' => 'proof.jpg'
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        // Mock CyberPanelService
        $this->mock(CyberPanelService::class, function (MockInterface $mock) use ($user) {
            
            // 1. Create User (Happens on Admin Approval)
            $mock->shouldReceive('createCyberPanelUser')
                 ->once()
                 ->andReturn(['status' => 1, 'error_message' => 'None']);

            // NOTE: We do NOT expect enableApiAccess or createPackage here anymore.
            
            // 2. Create Package (Happens on Domain Connection by User)
            $mock->shouldReceive('createPackage')
                 ->once()
                 ->withArgs(function($pkgName, $disk, $bw, $ftp, $db, $email, $domains, $creds) {
                     // Must use USER credentials from DB
                     return isset($creds['username']) && str_contains($creds['username'], 'testuser');
                 })
                 ->andReturn(['status' => 1]);

             // 3. Create Website (Happens on Domain Connection)
             $mock->shouldReceive('createWebsite')->once()->andReturn(['status' => 1]);
             $mock->shouldReceive('createFtpAccount')->once()->andReturn(['status' => 1]);
             $mock->shouldReceive('createNameServer')->once()->andReturn(['status' => 1]);
        });

        // ACTION: Admin Approves
        $response = $this->actingAs($admin)
                         ->post(route('admin.subscriptions.approve', $subscription->id));
        
        // Assert Redirect to external CyberPanel URL
        $cpUrl = config('services.cyberpanel.url');
        $response->assertRedirect($cpUrl);
        
        // Verify User has CP Password stored
        $user->refresh();
        $this->assertNotNull($user->cp_password);

        // =========================================================================
        // Step 3: User Connects Domain -> Triggers Package Creation & Website Creation
        // =========================================================================
        
        $this->actingAs($user); 

        $domainData = [
            'domain_name' => 'test-domain.com'
        ];

        // This route should now trigger createPackage BEFORE createWebsite
        $response = $this->post(route('domains.store'), $domainData);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('domains', [
            'domain_name' => 'test-domain.com',
            'status' => 'pending'
        ]);
    }
}
