<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CourseCard extends Component
{
    public $course;

    public function __construct($course)
    {
        $this->course = $course;
    }

    public function render()
    {
        return view('components.course-card');
    }
}
