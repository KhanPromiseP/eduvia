@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <div class="text-sm font-medium text-blue-600">Step 4 of 5</div>
                <div class="text-sm text-gray-500">Document Verification</div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full" style="width: 80%"></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Document Verification</h1>
                <p class="text-gray-600">Upload required documents for identity verification</p>
            </div>

            <!-- Requirements Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                <h3 class="font-semibold text-blue-800 mb-3 flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    Document Requirements
                </h3>
                <ul class="text-sm text-blue-700 space-y-2">
                    <li>• All documents must be clear and readable</li>
                    <li>• Accepted formats: JPG, PNG, PDF</li>
                    <li>• Maximum file size: 2MB per document</li>
                    <li>• Documents will be verified within 2-3 business days</li>
                </ul>
            </div>

            <form action="{{ route('instructor.apply.step4.store') }}" method="POST" enctype="multipart/form-data" id="documentForm">
                @csrf
                
                <!-- Government ID Card -->
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 mb-4">
                        Government Issued ID Card *
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition">
                        <input type="file" name="id_card" id="id_card" 
                            class="hidden" accept=".jpg,.jpeg,.png,.pdf" required>
                        
                        <div id="idCardPreview" class="hidden mb-4">
                            <img id="idCardPreviewImg" class="mx-auto h-32 rounded-lg shadow-md">
                            <p id="idCardFileName" class="text-sm text-gray-600 mt-2"></p>
                        </div>
                        
                        <div id="idCardPlaceholder">
                            <i class="fas fa-id-card text-4xl text-gray-400 mb-3"></i>
                            <p class="text-sm text-gray-600 mb-2">
                                Upload your government ID card (Driver's License, National ID, etc.)
                            </p>
                            <button type="button" onclick="document.getElementById('id_card').click()" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Choose File
                            </button>
                        </div>
                    </div>
                    @error('id_card')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Professional Certificate -->
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 mb-4">
                        Professional Certificate or Diploma *
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition">
                        <input type="file" name="certificate" id="certificate" 
                            class="hidden" accept=".jpg,.jpeg,.png,.pdf" required>
                        
                        <div id="certificatePreview" class="hidden mb-4">
                            <img id="certificatePreviewImg" class="mx-auto h-32 rounded-lg shadow-md">
                            <p id="certificateFileName" class="text-sm text-gray-600 mt-2"></p>
                        </div>
                        
                        <div id="certificatePlaceholder">
                            <i class="fas fa-graduation-cap text-4xl text-gray-400 mb-3"></i>
                            <p class="text-sm text-gray-600 mb-2">
                                Upload your professional certificate, diploma, or degree
                            </p>
                            <button type="button" onclick="document.getElementById('certificate').click()" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Choose File
                            </button>
                        </div>
                    </div>
                    @error('certificate')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Passport Photo (Optional) -->
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 mb-4">
                        Passport Photo (Optional)
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition">
                        <input type="file" name="passport" id="passport" 
                            class="hidden" accept=".jpg,.jpeg,.png,.pdf">
                        
                        <div id="passportPreview" class="hidden mb-4">
                            <img id="passportPreviewImg" class="mx-auto h-32 rounded-lg shadow-md">
                            <p id="passportFileName" class="text-sm text-gray-600 mt-2"></p>
                        </div>
                        
                        <div id="passportPlaceholder">
                            <i class="fas fa-user text-4xl text-gray-400 mb-3"></i>
                            <p class="text-sm text-gray-600 mb-2">
                                Upload a passport-style photo (optional)
                            </p>
                            <button type="button" onclick="document.getElementById('passport').click()" 
                                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                                Choose File
                            </button>
                        </div>
                    </div>
                    @error('passport')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Privacy Notice -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <i class="fas fa-shield-alt text-yellow-500 mt-1 mr-3"></i>
                        <div>
                            <h4 class="font-semibold text-yellow-800">Privacy & Security</h4>
                            <p class="text-sm text-yellow-700 mt-1">
                                Your documents are stored securely and will only be used for verification purposes. 
                                We comply with data protection regulations and will delete documents after verification is complete.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="flex justify-between">
                    <a href="{{ route('instructor.apply.step3') }}" 
                        class="px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back
                    </a>
                    <button type="submit" id="submitButton"
                        class="px-8 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-200">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Submit Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // File input change handlers
    setupFileInput('id_card', 'idCardPreview', 'idCardPlaceholder', 'idCardPreviewImg', 'idCardFileName');
    setupFileInput('certificate', 'certificatePreview', 'certificatePlaceholder', 'certificatePreviewImg', 'certificateFileName');
    setupFileInput('passport', 'passportPreview', 'passportPlaceholder', 'passportPreviewImg', 'passportFileName');

    // Form validation
    const form = document.getElementById('documentForm');
    form.addEventListener('submit', function(e) {
        const idCard = document.getElementById('id_card').files.length;
        const certificate = document.getElementById('certificate').files.length;
        
        if (!idCard || !certificate) {
            e.preventDefault();
            alert('Please upload both required documents before submitting.');
        }
    });
});

function setupFileInput(inputId, previewId, placeholderId, previewImgId, fileNameId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    const placeholder = document.getElementById(placeholderId);
    const previewImg = document.getElementById(previewImgId);
    const fileName = document.getElementById(fileNameId);

    input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Show file name
            fileName.textContent = file.name;
            
            // Show preview for images
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                // For PDFs, show a document icon
                previewImg.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjY0IiBoZWlnaHQ9IjY0IiByeD0iOCIgZmlsbD0iI0VFRjNGNiIvPgo8cGF0aCBkPSJNMzIgMTZIMzZWMzJIMzJWMzZIMjRWMzJIMjBWMjBIMzJWMjRIMzZWMzJINDBWMjBIMzJWMxY2WiIgZmlsbD0iIzM4NTFFNiIvPgo8L3N2Zz4K';
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            }
        } else {
            preview.classList.add('hidden');
            placeholder.classList.remove('hidden');
        }
    });
}
</script>

<style>
.border-dashed:hover {
    border-color: #3b82f6;
    transition: border-color 0.2s ease;
}

input[type="file"] {
    border: 0;
    clip: rect(0, 0, 0, 0);
    height: 1px;
    overflow: hidden;
    padding: 0;
    position: absolute !important;
    white-space: nowrap;
    width: 1px;
}
</style>
@endsection