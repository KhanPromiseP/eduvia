<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get purchased courses with ordered modules and attachments
        $purchasedCourses = $user->courses() // Changed from purchasedCourses() to courses()
            ->with(['modules' => function($query) {
                $query->orderBy('order');
            }, 'modules.attachments'])
            ->orderBy('purchased_at', 'desc')
            ->get();

        $selectedCourse = null;
        $selectedModule = null;
        $moduleIndex = null;

        // Check if a specific course is selected
        if ($request->has('course')) {
            $selectedCourse = $purchasedCourses->firstWhere('id', $request->course);
            
            // Check if a specific module is selected
            if ($request->has('module') && $selectedCourse) {
                $selectedModule = $selectedCourse->modules->firstWhere('id', $request->module);
                
                // Find the index of the selected module for navigation
                if ($selectedModule) {
                    $moduleIndex = $selectedCourse->modules->search(function($module) use ($selectedModule) {
                        return $module->id === $selectedModule->id;
                    });
                }
            }
        }

        return view('userdashboard', compact(
            'purchasedCourses',
            'selectedCourse',
            'selectedModule',
            'moduleIndex'
        ));
    }
}