@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <div class="text-sm font-medium text-blue-600">Step 2 of 4</div>
                <div class="text-sm text-gray-500">Skills & Languages</div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full" style="width: 50%"></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Your Skills & Languages</h1>
                <p class="text-gray-600">What can you teach and in which languages?</p>
            </div>

            <form action="{{ route('instructor.apply.step2.store') }}" method="POST" id="step2Form">
                @csrf
                
                <!-- Skills -->
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 mb-4">
                        Skills & Technologies You Can Teach *
                        <span class="text-sm font-normal text-gray-500">(Select at least 3)</span>
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-4" id="skillsContainer">
                        @php
                            $commonSkills = [
                                'Laravel', 'React', 'Vue.js', 'Node.js', 'Python', 'JavaScript',
                                'PHP', 'HTML/CSS', 'MySQL', 'MongoDB', 'AWS', 'Docker',
                                'React Native', 'Flutter', 'Swift', 'Kotlin', 'Java', 'C#',
                                'Photoshop', 'Illustrator', 'Figma', 'UI/UX Design',
                                'Digital Marketing', 'SEO', 'Social Media Marketing',
                                'Data Analysis', 'Machine Learning', 'Deep Learning'
                            ];
                        @endphp
                        @foreach($commonSkills as $skill)
                            <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-300 cursor-pointer transition skill-checkbox">
                                <input type="checkbox" name="skills[]" value="{{ $skill }}" 
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 skill-input"
                                    {{ in_array($skill, old('skills', [])) ? 'checked' : '' }}>
                                <span class="ml-3 text-sm text-gray-700">{{ $skill }}</span>
                            </label>
                        @endforeach
                    </div>
                    
                    <!-- Custom Skills Input -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Add Custom Skills
                        </label>
                        <div class="flex gap-2">
                            <input type="text" id="customSkillInput" 
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Enter a skill (e.g., WordPress)">
                            <button type="button" id="addCustomSkill" 
                                class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                                Add
                            </button>
                        </div>
                    </div>
                    
                    <!-- Selected Skills Display -->
                    <div id="selectedSkills" class="flex flex-wrap gap-2 mb-2">
                        @foreach(old('skills', []) as $skill)
                            @if(in_array($skill, $commonSkills))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                {{ $skill }}
                                <button type="button" class="ml-1 text-blue-600 hover:text-blue-800 remove-skill" data-skill="{{ $skill }}">
                                    ×
                                </button>
                            </span>
                            @endif
                        @endforeach
                    </div>
                    
                    @error('skills')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Languages -->
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 mb-4">
                        Languages You Can Teach In *
                        <span class="text-sm font-normal text-gray-500">(Select at least 1)</span>
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3" id="languagesContainer">
                        @php
                            $languages = [
                                'English', 'Spanish', 'French', 'German', 'Chinese', 'Arabic',
                                'Hindi', 'Portuguese', 'Russian', 'Japanese', 'Korean'
                            ];
                        @endphp
                        @foreach($languages as $language)
                            <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-300 cursor-pointer transition">
                                <input type="checkbox" name="languages[]" value="{{ $language }}" 
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 language-input"
                                    {{ in_array($language, old('languages', [])) ? 'checked' : '' }}>
                                <span class="ml-3 text-sm text-gray-700">{{ $language }}</span>
                            </label>
                        @endforeach
                    </div>
                    
                    <!-- Selected Languages Display -->
                    <div id="selectedLanguages" class="flex flex-wrap gap-2 mt-3">
                        @foreach(old('languages', []) as $language)
                            @if(in_array($language, $languages))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                {{ $language }}
                            </span>
                            @endif
                        @endforeach
                    </div>
                    
                    @error('languages')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Validation Summary -->
                <div id="validationErrors" class="hidden bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                        <div>
                            <h4 class="font-semibold text-red-800">Please fix the following:</h4>
                            <ul id="errorList" class="text-sm text-red-700 mt-1 list-disc list-inside"></ul>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="flex justify-between">
                    <a href="{{ route('instructor.apply') }}" 
                        class="px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back
                    </a>
                    <button type="submit" id="submitButton"
                        class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200">
                        Continue to Step 3
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('step2Form');
    const skillsInputs = document.querySelectorAll('.skill-input');
    const languageInputs = document.querySelectorAll('.language-input');
    const customSkillInput = document.getElementById('customSkillInput');
    const addCustomSkillBtn = document.getElementById('addCustomSkill');
    const selectedSkills = document.getElementById('selectedSkills');
    const selectedLanguages = document.getElementById('selectedLanguages');
    const validationErrors = document.getElementById('validationErrors');
    const errorList = document.getElementById('errorList');
    const submitButton = document.getElementById('submitButton');

    // Update selected skills display
    function updateSelectedSkills() {
        const selected = Array.from(skillsInputs)
            .filter(input => input.checked)
            .map(input => input.value);
        
        selectedSkills.innerHTML = selected.map(skill => `
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                ${skill}
                <button type="button" class="ml-1 text-blue-600 hover:text-blue-800 remove-skill" data-skill="${skill}">
                    ×
                </button>
            </span>
        `).join('');

        // Add event listeners to remove buttons
        document.querySelectorAll('.remove-skill').forEach(btn => {
            btn.addEventListener('click', function() {
                const skillToRemove = this.getAttribute('data-skill');
                const checkbox = document.querySelector(`.skill-input[value="${skillToRemove}"]`);
                if (checkbox) {
                    checkbox.checked = false;
                    updateSelectedSkills();
                }
            });
        });
    }

    // Update selected languages display
    function updateSelectedLanguages() {
        const selected = Array.from(languageInputs)
            .filter(input => input.checked)
            .map(input => input.value);
        
        selectedLanguages.innerHTML = selected.map(language => `
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-green-100 text-green-800">
                ${language}
            </span>
        `).join('');
    }

    // Add custom skill
    addCustomSkillBtn.addEventListener('click', function() {
        const customSkill = customSkillInput.value.trim();
        if (customSkill && !Array.from(skillsInputs).some(input => input.value === customSkill)) {
            // Create a hidden input for the custom skill
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'skills[]';
            hiddenInput.value = customSkill;
            form.appendChild(hiddenInput);

            // Add to display
            const skillSpan = document.createElement('span');
            skillSpan.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs bg-blue-100 text-blue-800';
            skillSpan.innerHTML = `
                ${customSkill}
                <button type="button" class="ml-1 text-blue-600 hover:text-blue-800 remove-skill" data-skill="${customSkill}">
                    ×
                </button>
            `;
            selectedSkills.appendChild(skillSpan);

            // Add remove functionality
            skillSpan.querySelector('.remove-skill').addEventListener('click', function() {
                hiddenInput.remove();
                skillSpan.remove();
            });

            customSkillInput.value = '';
        }
    });

    // Allow Enter key to add custom skill
    customSkillInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            addCustomSkillBtn.click();
        }
    });

    // Form validation
    form.addEventListener('submit', function(e) {
        const selectedSkills = Array.from(skillsInputs).filter(input => input.checked).length;
        const customSkills = Array.from(form.querySelectorAll('input[name="skills[]"][type="hidden"]')).length;
        const totalSkills = selectedSkills + customSkills;
        const selectedLanguages = Array.from(languageInputs).filter(input => input.checked).length;

        const errors = [];

        if (totalSkills < 3) {
            errors.push('Please select at least 3 skills');
        }

        if (selectedLanguages < 1) {
            errors.push('Please select at least 1 language');
        }

        if (errors.length > 0) {
            e.preventDefault();
            errorList.innerHTML = errors.map(error => `<li>${error}</li>`).join('');
            validationErrors.classList.remove('hidden');
            validationErrors.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    // Initialize displays
    updateSelectedSkills();
    updateSelectedLanguages();

    // Add event listeners for checkboxes
    skillsInputs.forEach(input => {
        input.addEventListener('change', updateSelectedSkills);
    });

    languageInputs.forEach(input => {
        input.addEventListener('change', updateSelectedLanguages);
    });
});
</script>

<style>
.skill-checkbox:hover {
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

.remove-skill {
    cursor: pointer;
    font-weight: bold;
    font-size: 14px;
}

#validationErrors {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endsection