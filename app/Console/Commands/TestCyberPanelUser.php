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

        $this->info("1. Attempting to create user: $username using Service");
        $result = $service->createCyberPanelUser($name, $username, $email, $password);
        $this->info("Result: " . json_encode($result, JSON_PRETTY_PRINT));

        if (isset($result['status']) && $result['status'] == 1) {
            $this->info("2. Attempting to Enable API Access for: $username");
            $apiResult = $service->enableApiAccess($username);
            $this->info("Result: " . json_encode($apiResult, JSON_PRETTY_PRINT));
        }

        $packageName = 'test-pkg-' . rand(100,999);
        $this->info("3. Attempting to Create Package: $packageName");
        // Package Specs: 1GB, 0 BW, 1 FTP, 1 DB, 1 Email, 1 Domain
        $pkgResult = $service->createPackage($packageName, 1000, 0, 1, 1, 1, 1);
        $this->info("Result: " . json_encode($pkgResult, JSON_PRETTY_PRINT));
    }
}
