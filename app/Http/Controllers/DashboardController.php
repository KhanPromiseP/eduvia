<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Category;

class DashboardController extends Controller
{
 

     public function index(Course $course)
    {
        // get all courses (or only active ones)
        $courses = Course::all();

        $course->load(['modules' => function($query) {
            $query->orderBy('order');
        }, 'modules.attachments']);

        $categories = Category::all(); // Fetch all categories

        return view('dashboard', compact('courses', 'course', 'categories'));
    }
}