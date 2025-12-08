<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PhpManagerController extends Controller
{
    public function index()
    {
        return view('dashboard.php_manager', [
            'currentVersion' => '8.2',
            'availableVersions' => ['7.4', '8.0', '8.1', '8.2', '8.3'],
            'extensions' => [
                'bcmath' => true,
                'ctype' => true,
                'fileinfo' => true,
                'json' => true,
                'mbstring' => true,
                'openssl' => true,
                'pdo' => true,
                'tokenizer' => true,
                'xml' => true,
                'mysqli' => false,
                'gd' => true,
                'intl' => false,
                'zip' => true,
                'curl' => true,
                'soap' => false,
                'imagick' => false,
                'pdo_mysql' => true,
                'pdo_sqlite' => true,
                'redis' => false,
                'memcached' => false,
                'imap' => false,
                'ldap' => false,
                'pgsql' => false,
                'xsl' => false,
                'dom' => true,
                'ftp' => true,
                'sodium' => true,
                'iconv' => true,
                'simplexml' => true,
            ]
        ]);
    }

    public function update(Request $request)
    {
        // Logic to update PHP config would go here (e.g. writing to a file or calling an API)
        return back()->with('success', 'PHP Configuration updated successfully!');
    }
}
