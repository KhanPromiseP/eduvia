<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CoursePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('instructor') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Course $course): bool
    {
        return $user->hasRole('admin') || $course->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('instructor');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Course $course): bool
    {
        return $user->hasRole('admin') || $course->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Course $course): bool
    {
        return $user->hasRole('admin') || $course->user_id === $user->id;
    }

    /**
     * Determine whether the user can review courses.
     */
    public function review(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('reviewer');
    }

    /**
     * Determine whether the user can publish courses.
     */
    public function publish(User $user): bool
    {
        return $user->hasRole('admin');
    }
}