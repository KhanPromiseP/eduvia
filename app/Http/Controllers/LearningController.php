<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LearningController extends Controller
{
    public function show(Course $course)
    {
        // Check if user has purchased this course
        if (!$course->isPurchasedBy(Auth::user())) {
            return redirect()->route('courses.show', $course)
                             ->with('error', 'You need to purchase this course to access the content.');
        }
        
        $course->load(['modules' => function($query) {
            $query->orderBy('order');
        }, 'modules.attachments']);
        
        return view('courses.learn', compact('course'));
    }
}