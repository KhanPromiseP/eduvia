<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::where('is_published', true)->get();
        
        return view('courses.index', compact('courses'));
    }

    public function show(Course $course)
    {
        if (!$course->is_published) {
            abort(404);
        }
        
        $course->load(['modules' => function($query) {
            $query->orderBy('order');
        }, 'modules.attachments']);
        
        $userHasPurchased = auth()->check() ? $course->isPurchasedBy(auth()->user()) : false;
        
        return view('courses.show', compact('course', 'userHasPurchased'));
    }
}