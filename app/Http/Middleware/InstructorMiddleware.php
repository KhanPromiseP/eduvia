<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstructorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user has instructor role
        if (!$user->hasRole('instructor')) {
            return redirect()->route('instructor.welcome')
                ->with('error', 'You need to be an approved instructor to access this page.');
        }

        // Check if instructor is suspended
        $instructor = $user->instructor;
        if ($instructor && $instructor->isSuspended()) {
            return redirect()->route('dashboard')
                ->with('error', 'Your instructor account has been suspended. Please contact support.');
        }

        return $next($request);
    }
}