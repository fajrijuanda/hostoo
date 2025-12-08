<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyOtpMail;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class FullSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_registration_generates_otp_and_sends_email()
    {
        Mail::fake();

        // Use Session facade
        Session::put('captcha_code', 'ABCDE');

        $response = $this->post(route('register.post'), [
            'name' => 'Test User',
            'email' => 'test@hostoo.io',
            'password' => 'password',
            'password_confirmation' => 'password',
            'captcha' => 'ABCDE'
        ]);

        if ($response->getSession()->has('errors')) {
            dump($response->getSession()->get('errors')->all());
        }
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'test@hostoo.io',
            'role' => 'user'
        ]);

        $user = User::where('email', 'test@hostoo.io')->first();
        $this->assertNotNull($user->otp);
        $this->assertNotNull($user->otp_expires_at);

        Mail::assertSent(VerifyOtpMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) && $mail->otp == $user->otp;
        });
    }

    public function test_unverified_user_is_redirected_to_verification_page()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
            'otp' => '123456',
            'otp_expires_at' => Carbon::now()->addMinutes(10),
            'password' => Hash::make('password')
        ]);

        Session::put('captcha_code', 'ABCDE');
        
        $this->post(route('login.post'), [
                'email' => $user->email,
                'password' => 'password',
                'captcha' => 'ABCDE'
             ])
             ->assertRedirect(route('verification.notice'));
    }

    public function test_user_can_verify_email_with_correct_otp()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
            'otp' => '123456',
            'otp_expires_at' => Carbon::now()->addMinutes(10)
        ]);

        $response = $this->actingAs($user)->post(route('verification.verify'), [
            'otp' => '123456'
        ]);

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->otp);

        $response->assertViewHas('verified', true);
    }

    public function test_user_cannot_verify_with_incorrect_otp()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
            'otp' => '123456',
            'otp_expires_at' => Carbon::now()->addMinutes(10)
        ]);

        $response = $this->actingAs($user)->post(route('verification.verify'), [
            'otp' => '000000'
        ]);

        $user->refresh();
        $this->assertNull($user->email_verified_at);
        $response->assertSessionHas('error');
    }

    public function test_resend_otp_generates_new_code()
    {
        $this->withoutExceptionHandling();
        Mail::fake();
        $user = User::factory()->create([
            'email_verified_at' => null,
            'otp' => '123456'
        ]);

        $this->actingAs($user)->post(route('verification.resend'));

        $user->refresh();
        $this->assertNotEquals('123456', $user->otp);
        Mail::assertSent(VerifyOtpMail::class);
    }

    public function test_google_login_auto_verifies_email()
    {
        $this->withoutExceptionHandling();
        
        // Mock Socialite
        $abstractUser = \Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getId')->andReturn(rand());
        $abstractUser->shouldReceive('getName')->andReturn('Google User');
        $abstractUser->shouldReceive('getEmail')->andReturn('google@example.com');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://en.gravatar.com/userimage');

        $provider = \Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        \Laravel\Socialite\Facades\Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        // Perform Callback
        $this->get('/auth/google/callback');

        $user = User::where('email', 'google@example.com')->first();
        $this->assertNotNull($user, 'Google User not found');
        $this->assertNotNull($user->email_verified_at, 'Google User not verified');
    }

    public function test_admin_dashboard_counts_exclude_admin()
    {
        $user = User::factory()->create(['role' => 'admin']);
        User::factory()->count(2)->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        $response->assertViewHas('totalUsers', 2);
    }
    
    public function test_profile_update_works()
    {
        $user = User::factory()->create(['role' => 'user', 'email_verified_at' => now()]);
        
        $response = $this->actingAs($user)->put(route('profile.update'), [
             'name' => 'New Name',
             'address' => 'New Address',
             'phone' => '08123456789',
        ]);
        
        if ($response->getSession()->has('errors')) {
            dump($response->getSession()->get('errors')->all());
        }
        $response->assertSessionHasNoErrors();
        
        $user->refresh();
        $this->assertEquals('New Name', $user->name);
        $this->assertEquals('New Address', $user->address);
    }
}
