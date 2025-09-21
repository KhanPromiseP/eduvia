<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class DashboardController extends Controller
{
 

     public function index()
    {
        // get all courses (or only active ones)
        $courses = Course::all();

        return view('dashboard', compact('courses'));
    }
}