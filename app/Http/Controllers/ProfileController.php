<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Category;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $categories = Category::all();
        
        return view('profile.edit', [
            'user' => $request->user(),
            'categories' => $categories,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's additional information.
     */
    public function updateAdditional(Request $request): RedirectResponse
    {
        $request->validate([
            'country' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'preferred_language' => ['nullable', 'string', 'max:10'],
            'learning_interests' => ['nullable', 'array'],
            'learning_interests.*' => ['exists:categories,id'],
        ]);

        $user = $request->user();
        
        $user->update([
            'country' => $request->country,
            'city' => $request->city,
            'preferred_language' => $request->preferred_language,
        ]);

        // Sync learning interests
        if ($request->has('learning_interests')) {
            $user->categories()->sync($request->learning_interests);
        } else {
            $user->categories()->detach();
        }

        return Redirect::route('profile.edit')->with('status', 'additional-info-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        // Detach categories before deleting user
        $user->categories()->detach();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
 * Auto-detect user location
 */
public function detectLocation(Request $request): RedirectResponse
{
    try {
        $ip = $request->ip();
        $location = $this->getLocationFromIP($ip);
        
        $user = $request->user();
        $user->update([
            'country' => $location['country'] ?? $user->country,
            'city' => $location['city'] ?? $user->city,
        ]);
        
        return Redirect::route('profile.edit')->with('status', 'location-detected');
    } catch (\Exception $e) {
        return Redirect::route('profile.edit')->with('error', 'Unable to detect location');
    }
}

private function getLocationFromIP($ip): array
{
    // Your IP location detection logic from earlier
    if ($ip === '127.0.0.1' || $ip === '::1') {
        return [
            'country' => 'United States',
            'city' => 'New York'
        ];
    }

    try {
        $response = Http::get("http://ipapi.co/{$ip}/json/");
        
        if ($response->successful()) {
            $data = $response->json();
            return [
                'country' => $data['country_name'] ?? null,
                'city' => $data['city'] ?? null
            ];
        }
    } catch (\Exception $e) {
        \Log::error('Failed to get location from IP: ' . $e->getMessage());
    }

    return [
        'country' => null,
        'city' => null
    ];
}
}