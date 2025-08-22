<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Attachment;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // Create a sample course
        $course = Course::create([
            'title' => 'Web Development Bootcamp',
            'description' => 'Learn full-stack web development from scratch with HTML, CSS, JavaScript, Node.js, and more.',
            'objectives' => "• Build responsive websites with HTML and CSS\n• Create interactive web applications with JavaScript\n• Develop server-side applications with Node.js\n• Work with databases and APIs",
            'target_audience' => 'Beginners who want to learn web development from scratch.',
            'requirements' => 'Basic computer skills. No prior programming experience required.',
            'price' => 99.99,
            'duration' => 40,
            'level' => 1,
            'is_published' => true,
        ]);

        // Create modules for the course
        $modules = [
            [
                'title' => 'Introduction to Web Development',
                'description' => 'Get an overview of web development and set up your development environment.',
                'order' => 1,
                'is_free' => true,
                'attachments' => [
                    [
                        'title' => 'Course Syllabus PDF',
                        'file_path' => 'syllabus.pdf',
                        'file_type' => 'pdf',
                        'file_size' => 1024,
                        'order' => 1,
                    ]
                ]
            ],
            [
                'title' => 'HTML Fundamentals',
                'description' => 'Learn the building blocks of web pages with HTML.',
                'order' => 2,
                'is_free' => false,
                'attachments' => [
                    [
                        'title' => 'HTML Cheat Sheet',
                        'file_path' => 'html-cheat-sheet.pdf',
                        'file_type' => 'pdf',
                        'file_size' => 512,
                        'order' => 1,
                    ],
                    [
                        'title' => 'HTML Basics Video',
                        'file_path' => 'html-basics.mp4',
                        'file_type' => 'mp4',
                        'file_size' => 10240,
                        'order' => 2,
                    ]
                ]
            ],
            [
                'title' => 'CSS Styling',
                'description' => 'Make your web pages beautiful with CSS.',
                'order' => 3,
                'is_free' => false,
                'attachments' => [
                    [
                        'title' => 'CSS Reference Guide',
                        'file_path' => 'css-reference.pdf',
                        'file_type' => 'pdf',
                        'file_size' => 768,
                        'order' => 1,
                    ]
                ]
            ]
        ];

        foreach ($modules as $moduleData) {
            $attachments = $moduleData['attachments'];
            unset($moduleData['attachments']);
            
            $module = $course->modules()->create($moduleData);
            
            foreach ($attachments as $attachmentData) {
                $module->attachments()->create($attachmentData);
            }
        }
    }
}