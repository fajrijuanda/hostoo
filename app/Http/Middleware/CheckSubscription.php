<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Allow Admin to bypass
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check for active subscription
        if (!$user->hasActiveSubscription()) {
            // Redirect to plans section on landing page or a specific upgrade page
            // Assuming landing page has id="plans"
            return redirect('/#plans')->with('warning', 'Please select a hosting plan to get started and access your dashboard.');
        }

        return $next($request);
    }
}
