<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CyberPanelService
{
    protected $url;
    protected $adminUsername;
    protected $adminPassword;

    public function __construct()
    {
        $this->url = config('services.cyberpanel.url');
        $this->adminUsername = config('services.cyberpanel.username');
        $this->adminPassword = config('services.cyberpanel.password');
    }

    protected function login()
    {
        // CyberPanel API usually requires admin user/pass in the body or header?
        // Actually, CyberPanel docs say tokens or Basic Auth won't work easily without setup.
        // Standard API: /api/createWebsite
        // Body: { "adminUser": "...", "adminPass": "...", "domainName": "...", ... }
        return; 
    }

    public function createWebsite($domainName, $package = 'Default', $ownerEmail, $ownerName = 'admin')
    {
        // Endpoint: /api/createWebsite
        $endpoint = rtrim($this->url, '/') . '/api/createWebsite';

        // CyberPanel API typically requires these fields.
        // We disable SSL verification (verify => false) because using IP/Port usually has self-signed cert.
        $response = Http::withoutVerifying()->post($endpoint, [
            'adminUser' => $this->adminUsername,
            'adminPass' => $this->adminPassword,
            'domainName' => $domainName,
            'packageName' => $package,
            'adminEmail' => $ownerEmail, // 'adminEmail' is often used as the owner email in the API docs
            'phpSelection' => 'PHP 8.2',
            'websiteOwner' => $ownerName, 
            'acl' => 'user', // Default ACL
        ]);

        Log::info("CyberPanel Create Website [$domainName]: " . $response->body());

        return $response->json();
    }

    public function createCyberPanelUser($name, $username, $email, $password)
    {
         // Endpoint: /api/submitUserCreation
         // Note: Endpoint might be 'createUsers' or 'submitUserCreation' depending on version. 
         // Most docs say: /api/createUsers or /api/submitUserCreation
         // Let's assume /api/createUsers for now or check if we need to verify.
         // Common: /api/submitUserCreation
         $endpoint = rtrim($this->url, '/') . '/api/submitUserCreation';

         $response = Http::withoutVerifying()->post($endpoint, [
            'adminUser' => $this->adminUsername,
            'adminPass' => $this->adminPassword,
            'firstName' => $name,
            'lastName' => '.', // Optional or dot
            'email' => $email,
            'userName' => $username,
            'password' => $password,
            'acl' => 'user',
            'websitesLimit' => 10, // Global limit for this user
            'selectedACL' => 'user'
         ]);
         
         Log::info("CyberPanel Create User [$username]: " . $response->body());
         return $response->json();
    }

    public function createFtpAccount($domainName, $username, $password)
    {
        // Endpoint: /api/createFTPAccount
        // Note: CyberPanel API docs say 'createFTPAccount'
        $endpoint = rtrim($this->url, '/') . '/api/createFTPAccount';

        $response = Http::withoutVerifying()->post($endpoint, [
            'adminUser' => $this->adminUsername,
            'adminPass' => $this->adminPassword,
            'domainName' => $domainName,
            'userName' => $username,
            'password' => $password,
            // 'ownerName' => 'admin' // If website is owned by user, FTP should probably default to that? 
            // Usually FTP is created under the website owner automatically or we specify.
            // API Doc: ownerName is required? Let's keep it optional/admin if needed, but if website is owned by client, 
            // admin might still be able to create it. 
            // Safest: Don't change this yet unless broken.
        ]);

        Log::info("CyberPanel Create FTP [$username]: " . $response->body());

        return $response->json();
    }
    
    public function createDatabase($websiteName, $dbName, $dbUser, $dbPass)
    {
        // Endpoint: /api/createDatabase
        $endpoint = rtrim($this->url, '/') . '/api/createDatabase';

        $response = Http::withoutVerifying()->post($endpoint, [
            'adminUser' => $this->adminUsername,
            'adminPass' => $this->adminPassword,
            'websiteName' => $websiteName,
            'dbName' => $dbName,
            'dbUser' => $dbUser,
            'dbPassword' => $dbPass,
        ]);

        Log::info("CyberPanel Create Database [$dbName] on [$websiteName]: " . $response->body());

        return $response->json();
    }

    public function deleteDatabase($dbName)
    {
        // Endpoint: /api/deleteDatabase
        $endpoint = rtrim($this->url, '/') . '/api/deleteDatabase';

        $response = Http::withoutVerifying()->post($endpoint, [
            'adminUser' => $this->adminUsername,
            'adminPass' => $this->adminPassword,
            'dbName' => $dbName,
        ]);

        Log::info("CyberPanel Delete Database [$dbName]: " . $response->body());

        return $response->json();
    }

    public function createEmail($websiteName, $username, $password)
    {
        // Endpoint: /api/createEmail
        $endpoint = rtrim($this->url, '/') . '/api/createEmail';

        $response = Http::withoutVerifying()->post($endpoint, [
            'adminUser' => $this->adminUsername,
            'adminPass' => $this->adminPassword,
            'websiteName' => $websiteName,
            'userName' => $username, // CyberPanel usually expects just 'user' part or full? createEmail typically wants username part
            'password' => $password,
        ]);

        Log::info("CyberPanel Create Email [$username] on [$websiteName]: " . $response->body());

        return $response->json();
    }

    public function deleteEmail($websiteName, $email)
    {
        // Endpoint: /api/deleteEmail
        $endpoint = rtrim($this->url, '/') . '/api/deleteEmail';

        $response = Http::withoutVerifying()->post($endpoint, [
            'adminUser' => $this->adminUsername,
            'adminPass' => $this->adminPassword,
            'websiteName' => $websiteName,
            'email' => $email,
        ]);

        Log::info("CyberPanel Delete Email [$email] on [$websiteName]: " . $response->body());

        return $response->json();
    }
}
