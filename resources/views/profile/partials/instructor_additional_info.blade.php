@if($instructor)
<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Instructor Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your instructor profile information to showcase your expertise to students.") }}
        </p>
    </header>

    <form method="post" action="{{ route('instructor.profile.update') }}" class="mt-6 space-y-6" id="instructor-profile-form">
        @csrf
        @method('patch')

        <!-- Success Message -->
        @if (session('status') === 'instructor-profile-updated')
            <div class="bg-green-50 border border-green-200 rounded-md p-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <p class="text-sm text-green-600 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ __('Profile saved successfully!') }}
                </p>
            </div>
        @endif

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium text-red-800">{{ __('Please fix the following errors:') }}</span>
                </div>
                <ul class="text-sm text-red-600 list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Headline -->
        <div>
            <x-input-label for="headline" :value="__('Professional Headline')" />
            <x-text-input id="headline" name="headline" type="text" class="mt-1 block w-full" 
                :value="old('headline', $instructor->headline ?? '')" 
                placeholder="e.g., Senior Web Developer & Instructor" 
                autocomplete="headline" />
            <p class="mt-1 text-sm text-gray-500">{{ __('A catchy title that describes your expertise') }}</p>
            <x-input-error class="mt-2" :messages="$errors->get('headline')" />
        </div>

        <!-- Bio -->
        <div>
            <x-input-label for="bio" :value="__('Bio')" />
            <textarea id="bio" name="bio" rows="6" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Tell students about your experience, teaching style, and expertise. Share what makes you unique as an instructor..."
                oninput="updateBioCounter(this)">{{ old('bio', $instructor->bio ?? '') }}</textarea>
            <div class="mt-1 flex justify-between items-center">
                <p class="text-sm text-gray-500">{{ __('Share your story and expertise with students') }}</p>
                <p class="text-sm {{ strlen(old('bio', $instructor->bio ?? '')) < 100 ? 'text-red-500' : 'text-green-500' }}" id="bio-counter">
                    {{ strlen(old('bio', $instructor->bio ?? '')) }}/2000 characters
                </p>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        <!-- Expertise -->
        <div>
            <x-input-label for="expertise" :value="__('Area of Expertise')" />
            <x-text-input id="expertise" name="expertise" type="text" class="mt-1 block w-full" 
                :value="old('expertise', $instructor->expertise ?? '')" 
                placeholder="e.g., Web Development, Data Science, Digital Marketing" 
                autocomplete="expertise" />
            <p class="mt-1 text-sm text-gray-500">{{ __('Your primary field or specialty') }}</p>
            <x-input-error class="mt-2" :messages="$errors->get('expertise')" />
        </div>

        <!-- Skills -->
        <div>
            <x-input-label for="skills" :value="__('Skills')" />
            <div class="mt-2">
                <div id="skills-container" class="flex flex-wrap gap-2 mb-3 min-h-12 border border-gray-300 rounded-md p-3 bg-gray-50">
                    @php
                        $skillsArray = [];
                        if (isset($instructor->skills)) {
                            if (is_array($instructor->skills)) {
                                $skillsArray = $instructor->skills;
                            } elseif (is_string($instructor->skills)) {
                                $decoded = json_decode($instructor->skills, true);
                                $skillsArray = is_array($decoded) ? $decoded : [];
                            }
                        }
                        $oldSkills = old('skills', $skillsArray);
                        $oldSkills = is_array($oldSkills) ? array_filter($oldSkills) : [];
                    @endphp
                    
                    @foreach($oldSkills as $skill)
                        @if(!empty(trim($skill ?? '')))
                            <span class="skill-tag inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 border border-indigo-200">
                                {{ $skill }}
                                <button type="button" class="ml-1.5 remove-skill text-indigo-600 hover:text-indigo-800 focus:outline-none" onclick="removeSkill(this)">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </span>
                        @endif
                    @endforeach
                    
                    @if(empty($oldSkills))
                        <p class="text-gray-400 text-sm">{{ __('No skills added yet') }}</p>
                    @endif
                </div>
                
                <div class="flex gap-2 mb-2">
                    <x-text-input id="skill-input" type="text" class="flex-1" 
                        placeholder="Add a skill (e.g., PHP, React, Python, Machine Learning...)" 
                        onkeypress="handleSkillKeypress(event)" />
                    <x-secondary-button type="button" onclick="addSkill()" class="whitespace-nowrap">
                        {{ __('Add Skill') }}
                    </x-secondary-button>
                </div>
                
                <input type="hidden" name="skills" id="skills-input" value="{{ json_encode($oldSkills) }}">
                
                <div class="flex justify-between items-center">
                    <p class="text-sm text-gray-500">{{ __('Add relevant skills (minimum 3)') }}</p>
                    <p class="text-sm {{ count($oldSkills) < 3 ? 'text-red-500' : 'text-green-500' }}" id="skills-counter">
                        {{ count($oldSkills) }}/∞ skills
                    </p>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('skills')" />
        </div>

        <!-- Languages -->
        <div>
            <x-input-label for="languages" :value="__('Languages')" />
            <div class="mt-2">
                <div id="languages-container" class="flex flex-wrap gap-2 mb-3 min-h-12 border border-gray-300 rounded-md p-3 bg-gray-50">
                    @php
                        $languagesArray = [];
                        if (isset($instructor->languages)) {
                            if (is_array($instructor->languages)) {
                                $languagesArray = $instructor->languages;
                            } elseif (is_string($instructor->languages)) {
                                $decoded = json_decode($instructor->languages, true);
                                $languagesArray = is_array($decoded) ? $decoded : [];
                            }
                        }
                        $oldLanguages = old('languages', $languagesArray);
                        $oldLanguages = is_array($oldLanguages) ? array_filter($oldLanguages) : [];
                    @endphp
                    
                    @foreach($oldLanguages as $language)
                        @if(!empty(trim($language ?? '')))
                            <span class="language-tag inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200">
                                {{ $language }}
                                <button type="button" class="ml-1.5 remove-language text-green-600 hover:text-green-800 focus:outline-none" onclick="removeLanguage(this)">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </span>
                        @endif
                    @endforeach
                    
                    @if(empty($oldLanguages))
                        <p class="text-gray-400 text-sm">{{ __('No languages added yet') }}</p>
                    @endif
                </div>
                
                <div class="flex gap-2 mb-2">
                    <select id="language-select" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">{{ __('Select a language') }}</option>
                        <option value="English">English</option>
                        <option value="Spanish">Spanish</option>
                        <option value="French">French</option>
                        <option value="German">German</option>
                        <option value="Italian">Italian</option>
                        <option value="Portuguese">Portuguese</option>
                        <option value="Russian">Russian</option>
                        <option value="Chinese">Chinese</option>
                        <option value="Japanese">Japanese</option>
                        <option value="Arabic">Arabic</option>
                        <option value="Hindi">Hindi</option>
                        <option value="Other">{{ __('Other...') }}</option>
                    </select>
                    <x-secondary-button type="button" onclick="addLanguage()" class="whitespace-nowrap">
                        {{ __('Add Language') }}
                    </x-secondary-button>
                </div>
                
                <input type="hidden" name="languages" id="languages-input" value="{{ json_encode($oldLanguages) }}">
                
                <div class="flex justify-between items-center">
                    <p class="text-sm text-gray-500">{{ __('Languages you can teach in') }}</p>
                    <p class="text-sm {{ count($oldLanguages) < 1 ? 'text-red-500' : 'text-green-500' }}" id="languages-counter">
                        {{ count($oldLanguages) }}/∞ languages
                    </p>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('languages')" />
        </div>

        <!-- Social Links -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- LinkedIn -->
            <div>
                <x-input-label for="linkedin_url" :value="__('LinkedIn Profile')" />
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                        </svg>
                    </div>
                    <x-text-input id="linkedin_url" name="linkedin_url" type="url" class="block w-full pl-10" 
                        :value="old('linkedin_url', $instructor->linkedin_url ?? '')" 
                        placeholder="https://linkedin.com/in/username" />
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('linkedin_url')" />
            </div>

            <!-- Website -->
            <div>
                <x-input-label for="website_url" :value="__('Personal Website')" />
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                        </svg>
                    </div>
                    <x-text-input id="website_url" name="website_url" type="url" class="block w-full pl-10" 
                        :value="old('website_url', $instructor->website_url ?? '')" 
                        placeholder="https://yourwebsite.com" />
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('website_url')" />
            </div>
        </div>

        <!-- Video Introduction -->
        <div>
            <x-input-label for="video_intro" :value="__('Video Introduction URL')" />
            <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <x-text-input id="video_intro" name="video_intro" type="url" class="block w-full pl-10" 
                    :value="old('video_intro', $instructor->video_intro ?? '')" 
                    placeholder="https://youtube.com/embed/video-id or https://vimeo.com/video-id" />
            </div>
            <p class="mt-1 text-sm text-gray-500">{{ __('Optional: Add a short video introduction to help students get to know you') }}</p>
            <x-input-error class="mt-2" :messages="$errors->get('video_intro')" />
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
            <div class="text-sm text-gray-500">
                {{ __('All fields are optional except bio and skills') }}
            </div>
            <div class="flex items-center gap-4">
                <x-secondary-button type="button" onclick="resetForm()">
                    {{ __('Reset') }}
                </x-secondary-button>
                <x-primary-button type="submit" id="submit-button">
                    {{ __('Save Profile') }}
                </x-primary-button>
            </div>
        </div>
    </form>
</section>

<script>
// Initialize when document is ready
document.addEventListener('DOMContentLoaded', function() {
    updateBioCounter(document.getElementById('bio'));
    updateSkillsCounter();
    updateLanguagesCounter();
});

// Bio character counter
function updateBioCounter(textarea) {
    const counter = document.getElementById('bio-counter');
    const length = textarea.value.length;
    counter.textContent = `${length}/2000 characters`;
    
    if (length < 100) {
        counter.classList.remove('text-green-500');
        counter.classList.add('text-red-500');
    } else {
        counter.classList.remove('text-red-500');
        counter.classList.add('text-green-500');
    }
}

// Skills management
function addSkill() {
    const input = document.getElementById('skill-input');
    const skill = input.value.trim();
    
    if (skill && !isSkillExists(skill)) {
        addTag('skills-container', 'skill-tag', skill, 'removeSkill');
        updateSkillsInput();
        updateSkillsCounter();
        input.value = '';
        input.focus();
    }
}

function removeSkill(button) {
    button.closest('.skill-tag').remove();
    updateSkillsInput();
    updateSkillsCounter();
}

function isSkillExists(skill) {
    const skills = document.querySelectorAll('#skills-container .skill-tag');
    return Array.from(skills).some(tag => {
        const skillText = tag.textContent.replace(/×/g, '').trim();
        return skillText.toLowerCase() === skill.toLowerCase();
    });
}

function updateSkillsInput() {
    const skills = Array.from(document.querySelectorAll('#skills-container .skill-tag'))
        .map(tag => tag.textContent.replace(/×/g, '').trim())
        .filter(skill => skill);
    document.getElementById('skills-input').value = JSON.stringify(skills);
}

function updateSkillsCounter() {
    const counter = document.getElementById('skills-counter');
    const skills = document.querySelectorAll('#skills-container .skill-tag');
    const count = skills.length;
    
    counter.textContent = `${count}/∞ skills`;
    
    if (count < 3) {
        counter.classList.remove('text-green-500');
        counter.classList.add('text-red-500');
    } else {
        counter.classList.remove('text-red-500');
        counter.classList.add('text-green-500');
    }
}

function handleSkillKeypress(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        addSkill();
    }
}

// Languages management
function addLanguage() {
    const select = document.getElementById('language-select');
    const language = select.value.trim();
    
    if (language && language !== 'Other' && !isLanguageExists(language)) {
        addTag('languages-container', 'language-tag', language, 'removeLanguage');
        updateLanguagesInput();
        updateLanguagesCounter();
        select.value = '';
    } else if (language === 'Other') {
        const customLanguage = prompt('{{ __("Please enter the language:") }}');
        if (customLanguage && customLanguage.trim() && !isLanguageExists(customLanguage.trim())) {
            addTag('languages-container', 'language-tag', customLanguage.trim(), 'removeLanguage');
            updateLanguagesInput();
            updateLanguagesCounter();
        }
        select.value = '';
    }
}

function removeLanguage(button) {
    button.closest('.language-tag').remove();
    updateLanguagesInput();
    updateLanguagesCounter();
}

function isLanguageExists(language) {
    const languages = document.querySelectorAll('#languages-container .language-tag');
    return Array.from(languages).some(tag => {
        const languageText = tag.textContent.replace(/×/g, '').trim();
        return languageText.toLowerCase() === language.toLowerCase();
    });
}

function updateLanguagesInput() {
    const languages = Array.from(document.querySelectorAll('#languages-container .language-tag'))
        .map(tag => tag.textContent.replace(/×/g, '').trim())
        .filter(language => language);
    document.getElementById('languages-input').value = JSON.stringify(languages);
}

function updateLanguagesCounter() {
    const counter = document.getElementById('languages-counter');
    const languages = document.querySelectorAll('#languages-container .language-tag');
    const count = languages.length;
    
    counter.textContent = `${count}/∞ languages`;
    
    if (count < 1) {
        counter.classList.remove('text-green-500');
        counter.classList.add('text-red-500');
    } else {
        counter.classList.remove('text-red-500');
        counter.classList.add('text-green-500');
    }
}

// Helper function to add tags
function addTag(containerId, tagClass, text, removeFunction) {
    const container = document.getElementById(containerId);
    
    // Remove empty state message if present
    const emptyMessage = container.querySelector('p.text-gray-400');
    if (emptyMessage) {
        emptyMessage.remove();
    }
    
    const tag = document.createElement('span');
    tag.className = `${tagClass} inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${
        tagClass.includes('skill') ? 'bg-indigo-100 text-indigo-800 border border-indigo-200' : 'bg-green-100 text-green-800 border border-green-200'
    }`;
    
    tag.innerHTML = `
        ${text}
        <button type="button" class="ml-1.5 remove-${tagClass.split('-')[0]} ${
            tagClass.includes('skill') ? 'text-indigo-600 hover:text-indigo-800' : 'text-green-600 hover:text-green-800'
        } focus:outline-none" onclick="${removeFunction}(this)">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>
    `;
    
    container.appendChild(tag);
}

// Form reset functionality
function resetForm() {
    if (confirm('{{ __("Are you sure you want to reset all changes?") }}')) {
        document.getElementById('instructor-profile-form').reset();
        // Reset skills and languages to original state
        const originalSkills = @json($skillsArray ?? []);
        const originalLanguages = @json($languagesArray ?? []);
        
        document.getElementById('skills-input').value = JSON.stringify(originalSkills);
        document.getElementById('languages-input').value = JSON.stringify(originalLanguages);
        
        // Reload the page to reset everything properly
        location.reload();
    }
}

// Form submission enhancement
document.getElementById('instructor-profile-form').addEventListener('submit', function(e) {
    const submitButton = document.getElementById('submit-button');
    submitButton.disabled = true;
    submitButton.innerHTML = '{{ __("Saving...") }}';
    
    // Optional: Add a small delay to show the loading state
    setTimeout(() => {
        submitButton.innerHTML = '{{ __("Save Profile") }}';
        submitButton.disabled = false;
    }, 3000);
});
</script>
@endif