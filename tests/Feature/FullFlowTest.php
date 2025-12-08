<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Subscription;

class FullFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_landing_page()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Hostoo');
    }

    public function test_user_can_register_and_login()
    {
        $response = $this->post('/register', [
            'name' => 'New User',
            'email' => 'new@user.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_user_can_select_plan_and_create_subscription()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Simulate clicking "Select Plan"
        $response = $this->get(route('plan.select', ['plan_type' => '1_month', 'price' => 100000]));
        
        // Should create a pending subscription
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'plan_type' => '1_month',
            'status' => 'pending',
        ]);

        // Should redirect to whatever external URL (WhatsApp) - checking status code 302
        $response->assertStatus(302);
    }

    public function test_admin_can_activate_subscription()
    {
        // 1. Setup User and Subscription
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_type' => '1_month',
            'price' => 100000,
            'status' => 'pending',
        ]);

        // 2. Setup Admin
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // 3. Admin activates subscription
        $response = $this->post(route('admin.activate', $subscription->id));
        
        $response->assertSessionHas('success');
        
        // 4. Verify Database
        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'status' => 'active',
        ]);
    }

    public function test_user_can_upload_file()
    {
        Storage::fake('public');
        
        $user = User::factory()->create();
        $this->actingAs($user);

        $file = UploadedFile::fake()->create('test.php', 100);

        $response = $this->post(route('dashboard.upload'), [
            'type' => 'file',
            'php_file' => $file,
        ]);

        $response->assertSessionHas('success');
        
        // Check file exists
        // Since controller renames file with time(), we can't guess exact name easily in mock.
        // We verify the directory exists and has files.
        $files = Storage::disk('public')->files('uploads/' . $user->id);
        $this->assertNotEmpty($files);
    }
    
    public function test_admin_middleware_blocks_normal_user()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);
        
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(302); // Redirects back
        $response->assertSessionHas('error');
    }
}
