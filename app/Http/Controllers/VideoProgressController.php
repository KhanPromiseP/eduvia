<?php

namespace App\Http\Controllers;

use App\Models\VideoProgress;
use App\Models\VideoEvent;
use App\Models\VideoViewSession;
use App\Models\ModuleProgress;
use App\Models\CourseProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VideoProgressController extends Controller
{
    /**
     * Track video progress
     */
    public function track(Request $request)
    {
        try {
            $request->validate([
                'attachment_id' => 'required|exists:attachments,id',
                'current_time' => 'required|numeric|min:0',
                'total_duration' => 'required|numeric|min:0',
                'session_id' => 'required|string',
                'quality' => 'nullable|string',
                'total_watched' => 'nullable|numeric'
            ]);

            $attachment = \App\Models\Attachment::findOrFail($request->attachment_id);
            $userId = Auth::id();

            // Calculate progress percentage
            $progressPercentage = $request->total_duration > 0 
                ? ($request->current_time / $request->total_duration) * 100 
                : 0;

            // Mark as completed if watched more than 90%
            $isCompleted = $progressPercentage >= 90;

            // Update or create progress record
            $progress = VideoProgress::updateOrCreate(
                [
                    'user_id' => $userId,
                    'attachment_id' => $request->attachment_id
                ],
                [
                    'current_time' => $request->current_time,
                    'total_duration' => $request->total_duration,
                    'progress_percentage' => $progressPercentage,
                    'completed' => $isCompleted,
                    'last_watched_at' => now(),
                    'total_watched_time' => DB::raw('COALESCE(total_watched_time, 0) + ' . ($request->total_watched ?: 0.1)),
                    'quality' => $request->quality,
                    'session_id' => $request->session_id
                ]
            );

            // Update video view session
            $this->updateVideoViewSession($userId, $attachment->id, $request->session_id, $progressPercentage, $isCompleted);

            // Update module progress if this completes the video
            if ($isCompleted && !$progress->wasRecentlyCreated) {
                $this->updateModuleProgress($attachment->module_id, $userId);
            }

            return response()->json([
                'success' => true,
                'progress' => $progressPercentage,
                'completed' => $isCompleted
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Progress tracking failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update video view session
     */
    private function updateVideoViewSession($userId, $attachmentId, $sessionId, $progressPercentage, $isCompleted)
    {
        try {
            $viewSession = VideoViewSession::firstOrCreate(
                [
                    'session_id' => $sessionId
                ],
                [
                    'user_id' => $userId,
                    'attachment_id' => $attachmentId,
                    'video_id' => \App\Models\Attachment::find($attachmentId)->video_id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'watch_time' => 0,
                    'completion_rate' => $progressPercentage,
                    'started_at' => now(),
                    'completed' => $isCompleted
                ]
            );

            // Update existing session
            if (!$viewSession->wasRecentlyCreated) {
                $viewSession->update([
                    'completion_rate' => $progressPercentage,
                    'completed' => $isCompleted,
                    'watch_time' => DB::raw('watch_time + 0.1') // Increment watch time
                ]);

                // Set ended_at if completed
                if ($isCompleted) {
                    $viewSession->update(['ended_at' => now()]);
                }
            }

        } catch (\Exception $e) {
            \Log::error('Video view session update failed: ' . $e->getMessage());
        }
    }

    /**
     * Track video events
     */
    public function trackEvent(Request $request)
    {
        try {
            $request->validate([
                'attachment_id' => 'required|exists:attachments,id',
                'event' => 'required|string',
                'session_id' => 'required|string',
                'timestamp' => 'required|numeric',
                'current_time' => 'nullable|numeric',
                'quality' => 'nullable|string'
            ]);

            // Track event in video_events table
            VideoEvent::create([
                'user_id' => Auth::id(),
                'attachment_id' => $request->attachment_id,
                'session_id' => $request->session_id,
                'event_type' => $request->event,
                'current_time' => $request->current_time,
                'quality' => $request->quality,
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip(),
                'occurred_at' => now()
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Event tracking failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark video as completed
     */
    public function markComplete(Request $request)
    {
        try {
            $request->validate([
                'attachment_id' => 'required|exists:attachments,id',
                'session_id' => 'required|string',
                'total_watched' => 'nullable|numeric',
                'completed_at' => 'required|date'
            ]);

            $userId = Auth::id();
            $attachment = \App\Models\Attachment::findOrFail($request->attachment_id);

            // Update progress as completed
            VideoProgress::updateOrCreate(
                [
                    'user_id' => $userId,
                    'attachment_id' => $request->attachment_id
                ],
                [
                    'current_time' => $request->total_duration ?? 0,
                    'total_duration' => $request->total_duration ?? 0,
                    'progress_percentage' => 100,
                    'completed' => true,
                    'completed_at' => now(),
                    'last_watched_at' => now(),
                    'total_watched_time' => $request->total_watched ?: 0,
                    'session_id' => $request->session_id
                ]
            );

            // Update module progress
            $this->updateModuleProgress($attachment->module_id, $userId);

            return response()->json([
                'success' => true,
                'message' => 'Video marked as completed'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Completion tracking failed'
            ], 500);
        }
    }

    /**
     * Update module progress when videos are completed
     */
    private function updateModuleProgress($moduleId, $userId)
    {
        try {
            $module = \App\Models\CourseModule::findOrFail($moduleId);
            $totalVideos = $module->attachments()->where('file_type', 'secure_video')->count();
            $completedVideos = VideoProgress::where('user_id', $userId)
                ->whereHas('attachment', function($query) use ($moduleId) {
                    $query->where('module_id', $moduleId)
                          ->where('file_type', 'secure_video');
                })
                ->where('completed', true)
                ->count();

            // Update module progress
            $moduleProgress = \App\Models\ModuleProgress::updateOrCreate(
                [
                    'user_id' => $userId,
                    'module_id' => $moduleId
                ],
                [
                    'completed_attachments' => $completedVideos,
                    'total_attachments' => $totalVideos,
                    'progress_percentage' => $totalVideos > 0 ? ($completedVideos / $totalVideos) * 100 : 0,
                    'completed' => $completedVideos >= $totalVideos && $totalVideos > 0,
                    'last_accessed_at' => now()
                ]
            );

            // Update course progress
            $this->updateCourseProgress($module->course_id, $userId);

        } catch (\Exception $e) {
            \Log::error('Module progress update failed: ' . $e->getMessage());
        }
    }

    /**
     * Update course progress
     */
    private function updateCourseProgress($courseId, $userId)
    {
        try {
            $course = \App\Models\Course::findOrFail($courseId);
            $totalModules = $course->modules()->count();
            $completedModules = \App\Models\ModuleProgress::where('user_id', $userId)
                ->whereHas('module', function($query) use ($courseId) {
                    $query->where('course_id', $courseId);
                })
                ->where('completed', true)
                ->count();

            // Update course progress
            $courseProgress = \App\Models\CourseProgress::updateOrCreate(
                [
                    'user_id' => $userId,
                    'course_id' => $courseId
                ],
                [
                    'completed_modules' => $completedModules,
                    'total_modules' => $totalModules,
                    'progress_percentage' => $totalModules > 0 ? ($completedModules / $totalModules) * 100 : 0,
                    'completed' => $completedModules >= $totalModules && $totalModules > 0,
                    'last_accessed_at' => now()
                ]
            );

        } catch (\Exception $e) {
            \Log::error('Course progress update failed: ' . $e->getMessage());
        }
    }

    /**
     * Get user progress for a course
     */
    public function getUserProgress(Request $request, $courseId)
    {
        try {
            $userId = Auth::id();
            
            $progress = \App\Models\CourseProgress::where('user_id', $userId)
                ->where('course_id', $courseId)
                ->first();

            $moduleProgress = \App\Models\ModuleProgress::where('user_id', $userId)
                ->whereHas('module', function($query) use ($courseId) {
                    $query->where('course_id', $courseId);
                })
                ->with('module')
                ->get();

            $videoProgress = VideoProgress::where('user_id', $userId)
                ->whereHas('attachment.module', function($query) use ($courseId) {
                    $query->where('course_id', $courseId);
                })
                ->with('attachment')
                ->get();

            return response()->json([
                'success' => true,
                'course_progress' => $progress,
                'module_progress' => $moduleProgress,
                'video_progress' => $videoProgress
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch progress'
            ], 500);
        }
    }
}