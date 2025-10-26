@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Become an Instructor</h1>
            <p class="text-xl text-gray-600 mb-8">Share your knowledge and inspire learners worldwide</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
            <div class="grid md:grid-cols-3 gap-8 mb-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chalkboard-teacher text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">Create Courses</h3>
                    <p class="text-gray-600">Build engaging courses with videos, quizzes, and assignments</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">Reach Students</h3>
                    <p class="text-gray-600">Connect with thousands of eager learners worldwide</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-money-bill-wave text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">Earn Income</h3>
                    <p class="text-gray-600">Generate revenue by sharing your expertise</p>
                </div>
            </div>

            @if($existingApplication)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                    <h4 class="font-semibold text-yellow-800 mb-2">Application Status: 
                        <span class="capitalize">{{ $existingApplication->status }}</span>
                    </h4>
                    <p class="text-yellow-700 mb-4">
                        @if($existingApplication->status === 'pending')
                            Your application is under review. We'll notify you once it's processed.
                        @elseif($existingApplication->status === 'approved')
                            Congratulations! Your application has been approved.
                        @else
                            Your application was not approved at this time.
                        @endif
                    </p>
                    <a href="{{ route('instructor.application.status') }}" 
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        View Application Status
                    </a>
                </div>
            @else
                <div class="text-center">
                    <p class="text-gray-600 mb-6">Ready to start your journey as an instructor?</p>
                    <a href="{{ route('instructor.apply') }}" 
                       class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-rocket mr-3"></i>
                        Start Application Process
                    </a>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h2 class="text-2xl font-bold text-center mb-6">Requirements</h2>
            <ul class="grid md:grid-cols-2 gap-4 text-gray-700">
                <li class="flex items-center">
                    <i class="fas fa-check text-green-500 mr-3"></i>
                    Professional expertise in your field
                </li>
                <li class="flex items-center">
                    <i class="fas fa-check text-green-500 mr-3"></i>
                    Ability to create engaging content
                </li>
                <li class="flex items-center">
                    <i class="fas fa-check text-green-500 mr-3"></i>
                    Good communication skills
                </li>
                <li class="flex items-center">
                    <i class="fas fa-check text-green-500 mr-3"></i>
                    Commitment to student success
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection