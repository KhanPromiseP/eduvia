<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Instructor;
use App\Models\InstructorDocument;
use App\Models\InstructorApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AdminInstructorController extends Controller
{
    // Show all instructor applications
    public function applications()
    {
        $applications = InstructorApplication::with(['user', 'reviewer'])
            ->latest()
            ->paginate(10);

        $pendingCount = InstructorApplication::where('status', 'pending')->count();
        $approvedCount = InstructorApplication::where('status', 'approved')->count();
        $rejectedCount = InstructorApplication::where('status', 'rejected')->count();

        return view('admin.instructors.applications', compact('applications', 'pendingCount', 'approvedCount', 'rejectedCount'));
    }

    // Show single application
    public function showApplication(InstructorApplication $application)
    {
        $application->load(['user', 'reviewer']);
        $instructor = Instructor::where('user_id', $application->user_id)->first();
        
        return view('admin.instructors.application-show', compact('application', 'instructor'));
    }

    // Approve application
    public function approveApplication(Request $request, InstructorApplication $application)
    {
        // Validate the request
        $validated = $request->validate([
            'review_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($application, $validated) {
                // Update application status
                $application->update([
                    'status' => 'approved',
                    'reviewed_by' => Auth::id(),
                    'review_notes' => $validated['review_notes'] ?? null,
                ]);

                // Activate instructor profile
                $instructor = Instructor::where('user_id', $application->user_id)->first();
                if ($instructor) {
                    $instructor->update([
                        'is_verified' => true,
                        'headline' => $instructor->headline ?? 'Instructor at ' . config('app.name'),
                    ]);
                } else {
                    // Create instructor profile if it doesn't exist
                    Instructor::create([
                        'user_id' => $application->user_id,
                        'headline' => 'Instructor at ' . config('app.name'),
                        'bio' => $application->bio,
                        'skills' => [],
                        'languages' => [],
                        'is_verified' => true,
                    ]);
                }

                // Assign instructor role to user
                $user = User::find($application->user_id);
                if (!$user->hasRole('instructor')) {
                    $user->assignRole('instructor');
                }

                // You can add notification logic here later
                // $user->notify(new InstructorApplicationApproved($application));
            });

            return redirect()->route('admin.instructors.applications')
                ->with('success', 'Instructor application approved successfully! The user can now access instructor features.');

        } catch (\Exception $e) {
            \Log::error('Failed to approve instructor application: ' . $e->getMessage());
            return back()->with('error', 'Failed to approve application: ' . $e->getMessage());
        }
    }

    // Reject application
    public function rejectApplication(Request $request, InstructorApplication $application)
    {
        // Validate the request
        $validated = $request->validate([
            'review_notes' => 'required|string|min:10|max:1000',
        ]);

        try {
            $application->update([
                'status' => 'rejected',
                'reviewed_by' => Auth::id(),
                'review_notes' => $validated['review_notes'],
            ]);

            // You can add notification logic here later
            // $application->user->notify(new InstructorApplicationRejected($application));

            return redirect()->route('admin.instructors.applications')
                ->with('success', 'Instructor application rejected successfully.');

        } catch (\Exception $e) {
            \Log::error('Failed to reject instructor application: ' . $e->getMessage());
            return back()->with('error', 'Failed to reject application: ' . $e->getMessage());
        }
    }

    // List all approved instructors
    public function index()
    {
        $instructors = Instructor::with(['user', 'user.courses'])
            ->where('is_verified', true)
            ->latest()
            ->paginate(10);

        return view('admin.instructors.index', compact('instructors'));
    }

    

    // Show instructor details
    public function show(Instructor $instructor)
    {
        $instructor->load('user', 'user.courses');
        return view('admin.instructors.show', compact('instructor'));
    }

    /**
 * Suspend an instructor
 */
public function suspend(Request $request, Instructor $instructor)
{
    $validated = $request->validate([
        'reason' => 'required|string|min:10|max:1000',
    ]);

    try {
        DB::transaction(function () use ($instructor, $validated) {
            // DO NOT remove the instructor role - just mark as suspended
            $instructor->update([
                'suspended_at' => now(),
                'suspension_reason' => $validated['reason'],
            ]);

            // Log the suspension
            \Log::info("Instructor suspended: {$instructor->user->name}. Reason: {$validated['reason']}");
            
            // You can add notification logic here
            // $instructor->user->notify(new InstructorSuspended($validated['reason']));
        });

        return redirect()->route('admin.instructors.applications')
            ->with('success', 'Instructor suspended successfully. They can no longer access instructor features.');

    } catch (\Exception $e) {
        \Log::error('Failed to suspend instructor: ' . $e->getMessage());
        return back()->with('error', 'Failed to suspend instructor: ' . $e->getMessage());
    }
}

public function approveDocument(InstructorDocument $document)
{
    try {
        $document->update([
            'status' => 'approved',
            'verified_by' => Auth::id(),
        ]);

        return back()->with('success', 'Document approved successfully.');
    } catch (\Exception $e) {
        \Log::error('Failed to approve document: ' . $e->getMessage());
        return back()->with('error', 'Failed to approve document.');
    }
}

public function rejectDocument(InstructorDocument $document)
{
    try {
        $document->update([
            'status' => 'rejected', 
            'verified_by' => Auth::id(),
        ]);

        return back()->with('success', 'Document rejected successfully.');
    } catch (\Exception $e) {
        \Log::error('Failed to reject document: ' . $e->getMessage());
        return back()->with('error', 'Failed to reject document.');
    }
}

/**
 * Reactivate a suspended instructor
 */
public function reactivate(Instructor $instructor)
{
    try {
        DB::transaction(function () use ($instructor) {
            // Just clear the suspension fields - the role is still there
            $instructor->update([
                'suspended_at' => null,
                'suspension_reason' => null,
            ]);

            // Log the reactivation
            \Log::info("Instructor reactivated: {$instructor->user->name}");
            
            // You can add notification logic here
            // $instructor->user->notify(new InstructorReactivated());
        });

        return redirect()->route('admin.instructors.applications')
            ->with('success', 'Instructor reactivated successfully. They can now access instructor features again.');

    } catch (\Exception $e) {
        \Log::error('Failed to reactivate instructor: ' . $e->getMessage());
        return back()->with('error', 'Failed to reactivate instructor: ' . $e->getMessage());
    }
}
}