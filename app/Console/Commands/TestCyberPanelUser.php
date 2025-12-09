<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CyberPanelService;
use Illuminate\Support\Facades\Http;

class TestCyberPanelUser extends Command
{
    protected $signature = 'test:cp-user';
    protected $description = 'Test CyberPanel User Creation';

    public function handle()
    {
        $service = new CyberPanelService();
        $username = 'testuser' . rand(100,999);
        $email = $username . '@example.com';
        $password = 'Password123!';
        $name = 'Test User';

        $this->info("Attempting to create user: $username");
        
        // Manual call to debug raw response
        $url = config('services.cyberpanel.url');
        $endpoint = rtrim($url, '/') . '/api/submitUserCreation';
        $this->info("Endpoint: $endpoint");

        $response = Http::withoutVerifying()->post($endpoint, [
            'adminUser' => config('services.cyberpanel.username'),
            'adminPass' => config('services.cyberpanel.password'),
            'firstName' => $name,
            'lastName' => '.',
            'email' => $email,
            'userName' => $username,
            'password' => $password,
            'acl' => 'user',
            'websitesLimit' => 1,
            'selectedACL' => 'user'
        ]);

        $this->info("Status: " . $response->status());
        $this->info("Body: " . $response->body());
        
        // Also try the other common endpoint if that failed
        if ($response->status() != 200 || strpos($response->body(), 'error') !== false) {
             $endpoint2 = rtrim($url, '/') . '/api/createUsers';
             $this->info("--- Retrying with /api/createUsers ---");
             $this->info("Endpoint: $endpoint2");
             $response2 = Http::withoutVerifying()->post($endpoint2, [
                'adminUser' => config('services.cyberpanel.username'),
                'adminPass' => config('services.cyberpanel.password'),
                'firstName' => $name,
                'lastName' => '.',
                'email' => $email,
                'userName' => $username,
                'password' => $password,
                'acl' => 'user',
                'websitesLimit' => 1,
                'selectedACL' => 'user'
            ]);
            $this->info("Status: " . $response2->status());
            $this->info("Body: " . $response2->body());
        }
    }
}
