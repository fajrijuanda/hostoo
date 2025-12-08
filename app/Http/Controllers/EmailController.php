<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmailController extends Controller
{
    protected $cyberPanel;

    public function __construct(\App\Services\CyberPanelService $cyberPanel)
    {
        $this->cyberPanel = $cyberPanel;
    }

    public function index()
    {
        // Restriction
        if (!Auth::user()->domains()->exists()) {
             return redirect()->route('domains.index')->with('error', 'Please connect a domain before managing emails.');
        }

        $emails = Auth::user()->emails()->orderBy('created_at', 'desc')->get();
        // Pass user domains for the creation form
        $domains = Auth::user()->domains()->get(); 
        
        return view('dashboard.emails.index', compact('emails', 'domains'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email_prefix' => 'required|alpha_dash|max:50',
            // Verify domain belongs to user
            'domain' => [
                'required',
                Rule::exists('domains', 'domain_name')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                }),
            ],
            'password' => 'required|min:8',
        ]);

        $fullEmail = $request->email_prefix . '@' . $request->domain;

        // Check Local Existence
        if (EmailAccount::where('email', $fullEmail)->exists()) {
             return back()->with('error', 'Email address already exists.');
        }

        try {
            // Call CyberPanel API
            $result = $this->cyberPanel->createEmail($request->domain, $request->email_prefix, $request->password);
            
             if (isset($result['error_message']) && $result['error_message'] != 'None') {
                  if ($result['createEmail'] === 0) { // Assuming typical response structure
                     throw new \Exception("CyberPanel Error: " . ($result['error_message'] ?? 'Unknown Error'));
                  }
            }

            EmailAccount::create([
                'user_id' => Auth::id(),
                'email' => $fullEmail,
                'password' => Hash::make($request->password), // Storing hashed password
            ]);

            return back()->with('success', 'Email account created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create email account: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $email = Auth::user()->emails()->findOrFail($id);
        
        // Extract domain from email (user@example.com)
        $parts = explode('@', $email->email);
        $domainName = isset($parts[1]) ? $parts[1] : null;

        if (!$domainName) {
             return back()->with('error', 'Invalid email format, cannot delete from server.');
        }

        try {
            // Call CyberPanel API
            $this->cyberPanel->deleteEmail($domainName, $email->email);
            
            // Delete Local
            $email->delete();

            return back()->with('success', 'Email account deleted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete email from server: ' . $e->getMessage());
        }
    }
}
