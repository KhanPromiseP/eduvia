<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\VideoProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttachmentController extends Controller
{
    /**
     * Mark attachment as completed
     */
    public function markComplete(Request $request)
    {
        try {
            $request->validate([
                'attachment_id' => 'required|exists:attachments,id',
                'completed_at' => 'required|date'
            ]);

            $userId = Auth::id();
            $attachment = Attachment::findOrFail($request->attachment_id);

            // For non-video attachments, mark as viewed
            if ($attachment->file_type !== 'secure_video') {
                VideoProgress::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'attachment_id' => $request->attachment_id
                    ],
                    [
                        'completed' => true,
                        'completed_at' => now(),
                        'last_watched_at' => now(),
                        'progress_percentage' => 100
                    ]
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Attachment marked as viewed'
                ]);
            }

            // For videos, completion is handled by progress tracking
            return response()->json([
                'success' => false,
                'message' => 'Video completion is handled by progress tracking'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark attachment as complete'
            ], 500);
        }
    }

    /**
     * Get attachment analytics
     */
    public function getAnalytics($attachmentId)
    {
        try {
            $attachment = Attachment::findOrFail($attachmentId);
            
            // Check if user has access to this attachment's course
            $hasAccess = \App\Models\CourseEnrollment::where('user_id', Auth::id())
                ->where('course_id', $attachment->module->course_id)
                ->where('status', 'active')
                ->exists();

            if (!$hasAccess && !Auth::user()->isAdmin()) {
                abort(403, 'Access denied');
            }

            $totalViews = VideoProgress::where('attachment_id', $attachmentId)->count();
            $completedViews = VideoProgress::where('attachment_id', $attachmentId)
                ->where('completed', true)
                ->count();
            
            $averageProgress = VideoProgress::where('attachment_id', $attachmentId)
                ->avg('progress_percentage');

            $recentViews = VideoProgress::where('attachment_id', $attachmentId)
                ->with('user')
                ->orderBy('last_watched_at', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'analytics' => [
                    'total_views' => $totalViews,
                    'completed_views' => $completedViews,
                    'completion_rate' => $totalViews > 0 ? ($completedViews / $totalViews) * 100 : 0,
                    'average_progress' => round($averageProgress, 2),
                    'recent_views' => $recentViews
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch analytics'
            ], 500);
        }
    }

    /**
     * Get user's progress for attachments in a module
     */
    public function getUserModuleProgress($moduleId)
    {
        try {
            $userId = Auth::id();
            $module = \App\Models\CourseModule::findOrFail($moduleId);

            // Check access
            $hasAccess = \App\Models\CourseEnrollment::where('user_id', $userId)
                ->where('course_id', $module->course_id)
                ->where('status', 'active')
                ->exists();

            if (!$hasAccess) {
                abort(403, 'Access denied');
            }

            $attachments = $module->attachments()
                ->with(['progress' => function($query) use ($userId) {
                    $query->where('user_id', $userId);
                }])
                ->get()
                ->map(function($attachment) {
                    return [
                        'id' => $attachment->id,
                        'title' => $attachment->title,
                        'file_type' => $attachment->file_type,
                        'is_secure' => $attachment->is_secure,
                        'progress' => $attachment->progress->first(),
                        'completed' => $attachment->progress->first()?->completed ?? false
                    ];
                });

            return response()->json([
                'success' => true,
                'attachments' => $attachments
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch progress'
            ], 500);
        }
    }
}