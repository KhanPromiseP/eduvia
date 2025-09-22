<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $categories = Category::all();
        return view('auth.register', compact('categories'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'preferred_language' => ['nullable', 'string', 'max:10'],
            'learning_interests' => ['nullable', 'array'],
            'learning_interests.*' => ['exists:categories,id'],
        ]);

        // Get user location based on IP
        $ip = $request->ip();
        $location = $this->getLocationFromIP($ip);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'country' => $location['country'] ?? null,
            'city' => $location['city'] ?? null,
            'preferred_language' => $request->preferred_language ?? 'en',
        ]);

        // Attach selected categories if any
        if ($request->has('learning_interests')) {
            $user->categories()->attach($request->learning_interests);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('userdashboard', absolute: false));
    }

    /**
     * Get location from IP address
     */
    private function getLocationFromIP($ip): array
    {
        try {
            // For local development or testing, use a fallback
            if ($ip === '127.0.0.1' || $ip === '::1') {
                return [
                    'country' => 'United States',
                    'city' => 'New York'
                ];
            }

            // Use ipapi.co service (free tier available)
            $response = Http::get("http://ipapi.co/{$ip}/json/");
            
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'country' => $data['country_name'] ?? null,
                    'city' => $data['city'] ?? null
                ];
            }
        } catch (\Exception $e) {
            // Log error or handle silently
            \Log::error('Failed to get location from IP: ' . $e->getMessage());
        }

        return [
            'country' => null,
            'city' => null
        ];
    }
}