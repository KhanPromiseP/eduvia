<?php

namespace App\Http\Controllers;

use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // ✅ Eager-load courses, modules, attachments, and user progress
        $purchasedCourses = $user->courses()
            ->with([
                'modules.attachments',
                'userProgress' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }
            ])
            ->get();

        $selectedCourse = null;
        $selectedModule = null;
        $moduleIndex    = 0;

        if ($request->filled('course')) {
            $selectedCourse = $purchasedCourses->firstWhere('id', $request->course);

            if ($selectedCourse && $request->filled('module')) {
                $selectedModule = $selectedCourse->modules->firstWhere('id', $request->module);

                if ($selectedModule) {
                    $this->trackModuleProgress($selectedModule->id);

                    $moduleIndex = $selectedCourse->modules
                        ->search(fn($module) => $module->id === $selectedModule->id);
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

    private function trackModuleProgress($moduleId)
    {
        $user = Auth::user();

        UserProgress::firstOrCreate(
            [
                'user_id'   => $user->id,
                'module_id' => $moduleId,
            ],
            [
                'viewed_at' => now(),
            ]
        );
    }

    public function markAsComplete($moduleId)
    {
        $user = Auth::user();

        UserProgress::updateOrCreate(
            [
                'user_id'   => $user->id,
                'module_id' => $moduleId,
            ],
            [
                'completed'    => true,
                'completed_at' => now(),
                'viewed_at'    => now(),
            ]
        );

        return response()->json(['success' => true]);
    }
}
