<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Category;
use App\Models\Instructor;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $categories = Category::all();
        $instructor = null;
        
        // Check if user is instructor and load instructor data
        if (Auth::user()->hasRole('instructor') || Auth::user()->isInstructor()) {
            $instructor = Instructor::where('user_id', Auth::id())->first();
        }

        return view('profile.edit', [
            'user' => $request->user(),
            'categories' => $categories,
            'instructor' => $instructor,
        ]);
    }

   public function update(ProfileUpdateRequest $request): RedirectResponse
{
    $user = $request->user();

    $validated = $request->validated();

    // Handle profile_path (file upload)
    if ($request->hasFile('profile_path')) {
        $path = $request->file('profile_path')->store('profile_images', 'public');
        $validated['profile_path'] = $path;
    }

    // If email changed, reset verification
    if ($user->email !== $validated['email']) {
        $user->email_verified_at = null;
    }

    $user->fill($validated);
    $user->save();

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
     * Update the instructor's profile information.
     */
   public function updateInstructorProfile(Request $request): RedirectResponse
{
    // Check if user is an instructor
    if (!(Auth::user()->hasRole('instructor') || Auth::user()->isInstructor())) {
        return Redirect::route('profile.edit')->with('error', 'You are not authorized to update instructor profile.');
    }

    $instructor = Instructor::where('user_id', Auth::id())->firstOrFail();

    $request->validate([
        'headline' => 'nullable|string|max:255',
        'bio' => 'required|min:100|max:2000',
        'expertise' => 'required|string|max:255', // Add this line
        'skills' => 'required|array|min:3',
        'skills.*' => 'string|max:50',
        'languages' => 'required|array|min:1',
        'languages.*' => 'string|max:50',
        'linkedin_url' => 'nullable|url',
        'website_url' => 'nullable|url',
        'video_intro' => 'nullable|url',
    ]);

    $instructor->update([
        'headline' => $request->headline,
        'bio' => $request->bio,
        'expertise' => $request->expertise, // Add this line
        'skills' => $request->skills,
        'languages' => $request->languages,
        'linkedin_url' => $request->linkedin_url,
        'website_url' => $request->website_url,
        'video_intro' => $request->video_intro,
    ]);

    return Redirect::route('profile.edit')->with('status', 'instructor-profile-updated');
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
        
        // Delete instructor record if exists
        if ($user->instructor) {
            $user->instructor->delete();
        }
        
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