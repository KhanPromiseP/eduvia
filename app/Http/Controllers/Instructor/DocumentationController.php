<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    /**
     * Display comprehensive instructor documentation
     */
    public function index()
    {
        return view('instructor.documentation');
    }

    /**
     * Mark section as read
     */
    public function markAsRead(Request $request)
    {
        $request->validate([
            'section' => 'required|string'
        ]);

        // Store in user's progress
        $progress = $request->user()->documentation_progress ?? [];
        if (!in_array($request->section, $progress)) {
            $progress[] = $request->section;
            $request->user()->update(['documentation_progress' => $progress]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Get user's documentation progress
     */
    public function getProgress(Request $request)
    {
        $progress = $request->user()->documentation_progress ?? [];
        $totalSections = 12; // Total documentation sections
        
        return response()->json([
            'progress' => $progress,
            'percentage' => count($progress) / $totalSections * 100
        ]);
    }
}