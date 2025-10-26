// Enhanced Study Room Features
class StudyRoomEnhanced {
    constructor() {
        this.transcriptPanel = null;
        this.microQuiz = null;
        this.aiNotes = null;
        this.gamification = null;
        this.init();
    }

    init() {
        this.initializeComponents();
        this.setupAdvancedFeatures();
        this.setupAnalytics();
    }

    initializeComponents() {
        // Initialize transcript panel
        if (typeof transcriptPanel === 'function') {
            this.transcriptPanel = Alpine.reactive(transcriptPanel());
        }

        // Initialize micro quiz system
        if (typeof microQuiz === 'function') {
            this.microQuiz = Alpine.reactive(microQuiz());
        }

        // Initialize AI notes
        if (typeof aiNotes === 'function') {
            this.aiNotes = Alpine.reactive(aiNotes());
        }

        // Initialize gamification
        if (typeof gamification === 'function') {
            this.gamification = Alpine.reactive(gamification());
        }
    }

    setupAdvancedFeatures() {
        // Auto-show transcript when video starts (if enabled)
        this.setupTranscriptAutoShow();
        
        // Setup quiz triggers at chapter ends
        this.setupQuizTriggers();
        
        // Setup AI note suggestions
        this.setupAISuggestions();
        
        // Setup social features
        this.setupSocialFeatures();
    }

    setupTranscriptAutoShow() {
        const video = document.querySelector('video');
        if (video && this.transcriptPanel) {
            video.addEventListener('play', () => {
                // Auto-show transcript after 30 seconds if user hasn't interacted
                setTimeout(() => {
                    if (!this.transcriptPanel.transcriptVisible) {
                        const preferences = JSON.parse(localStorage.getItem('transcript_preferences') || '{}');
                        if (preferences.autoShow) {
                            this.transcriptPanel.transcriptVisible = true;
                        }
                    }
                }, 30000);
            });
        }
    }

    setupQuizTriggers() {
        // Trigger micro-quiz at natural breakpoints
        const video = document.querySelector('video');
        if (video && this.microQuiz) {
            video.addEventListener('timeupdate', () => {
                // Check if we've reached a quiz trigger point
                this.checkQuizTriggers(video.currentTime);
            });
        }
    }

    checkQuizTriggers(currentTime) {
        // Define quiz trigger points (in seconds)
        const quizTriggers = [300, 600, 900]; // 5, 10, 15 minutes
        
        quizTriggers.forEach(triggerTime => {
            if (Math.abs(currentTime - triggerTime) < 2) { // 2-second window
                this.showContextualQuiz(triggerTime);
            }
        });
    }

    showContextualQuiz(timestamp) {
        // Only show if user hasn't completed this quiz recently
        const completedQuizzes = JSON.parse(localStorage.getItem('completed_quizzes') || '[]');
        const quizId = `quiz_${Math.floor(timestamp / 60)}`; // Quiz ID based on minute
        
        if (!completedQuizzes.includes(quizId)) {
            // Show gentle prompt for quiz
            this.showQuizPrompt(quizId, timestamp);
        }
    }

    showQuizPrompt(quizId, timestamp) {
        const prompt = document.createElement('div');
        prompt.className = 'fixed bottom-20 right-6 bg-white rounded-xl shadow-lg border border-gray-200 p-4 max-w-sm z-50 transform transition-all duration-300';
        prompt.innerHTML = `
            <div class="flex items-start space-x-3">
                <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-brain text-indigo-600"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-900 text-sm">Quick Check</h4>
                    <p class="text-gray-600 text-sm mt-1">Test your understanding of what you just learned.</p>
                    <div class="flex space-x-2 mt-3">
                        <button class="flex-1 px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition-colors" onclick="this.parentElement.parentElement.parentElement.remove()">
                            Later
                        </button>
                        <button class="flex-1 px-3 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition-colors" onclick="window.studyRoomEnhanced.startQuizNow('${quizId}')">
                            Start Quiz
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(prompt);
        
        // Auto-remove after 10 seconds
        setTimeout(() => {
            if (prompt.parentElement) {
                prompt.remove();
            }
        }, 10000);
    }

    startQuizNow(quizId) {
        // Start the micro-quiz
        if (this.microQuiz) {
            this.microQuiz.startQuiz();
            
            // Mark as shown to prevent repeated prompts
            const completedQuizzes = JSON.parse(localStorage.getItem('completed_quizzes') || '[]');
            if (!completedQuizzes.includes(quizId)) {
                completedQuizzes.push(quizId);
                localStorage.setItem('completed_quizzes', JSON.stringify(completedQuizzes));
            }
        }
    }

    setupAISuggestions() {
        // Monitor learning patterns for AI suggestions
        this.setupLearningPatternAnalysis();
    }

    setupLearningPatternAnalysis() {
        // Analyze user behavior to provide personalized suggestions
        const analysisData = {
            watchTimes: [],
            notePatterns: [],
            quizPerformance: [],
            preferredLearningTime: null
        };

        // Collect data points
        setInterval(() => {
            this.collectLearningData(analysisData);
        }, 60000); // Every minute
    }

    collectLearningData(analysisData) {
        const video = document.querySelector('video');
        if (video && !video.paused) {
            analysisData.watchTimes.push({
                timestamp: new Date().toISOString(),
                duration: video.currentTime,
                speed: video.playbackRate
            });
            
            // Keep only last 100 data points
            if (analysisData.watchTimes.length > 100) {
                analysisData.watchTimes.shift();
            }
            
            // Save for personalization
            localStorage.setItem('learning_analysis', JSON.stringify(analysisData));
        }
    }

    setupSocialFeatures() {
        // Initialize social learning features
        this.setupStudyGroups();
        this.setupPeerLearning();
    }

    setupStudyGroups() {
        // Setup virtual study groups
        if ('BroadcastChannel' in window) {
            this.studyChannel = new BroadcastChannel('study_room');
            
            this.studyChannel.addEventListener('message', (event) => {
                this.handleStudyGroupMessage(event.data);
            });
        }
    }

    handleStudyGroupMessage(message) {
        switch (message.type) {
            case 'user_online':
                this.showPeerPresence(message.user);
                break;
            case 'note_shared':
                this.showSharedNote(message.note);
                break;
            case 'question_asked':
                this.showPeerQuestion(message.question);
                break;
        }
    }

    showPeerPresence(user) {
        // Show notification when peers are studying the same content
        console.log('Peer online:', user.name, 'is studying', user.course);
    }

    setupAnalytics() {
        // Setup advanced learning analytics
        this.trackLearningMetrics();
    }

    trackLearningMetrics() {
        const metrics = {
            totalWatchTime: 0,
            completionRate: 0,
            engagementScore: 0,
            retentionRate: 0
        };

        // Track various learning metrics
        setInterval(() => {
            this.updateLearningMetrics(metrics);
        }, 30000); // Every 30 seconds
    }

    updateLearningMetrics(metrics) {
        // Calculate and update learning metrics
        // This would integrate with your analytics backend
        console.log('Updating learning metrics:', metrics);
    }

    // Public API methods
    showTranscript() {
        if (this.transcriptPanel) {
            this.transcriptPanel.transcriptVisible = true;
        }
    }

    startQuiz(questions = null) {
        if (this.microQuiz) {
            this.microQuiz.startQuiz(questions);
        }
    }

    takeNote(timestamp = null, lessonTitle = '') {
        if (this.aiNotes) {
            this.aiNotes.openModal(timestamp, lessonTitle);
        }
    }

    awardXP(amount, reason = 'Learning activity') {
        // Dispatch event for gamification system
        window.dispatchEvent(new CustomEvent('award-xp', {
            detail: { amount, reason }
        }));
    }

    unlockAchievement(achievementId) {
        window.dispatchEvent(new CustomEvent('unlock-achievement', {
            detail: { achievementId }
        }));
    }
}

// Initialize enhanced features when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.studyRoomEnhanced = new StudyRoomEnhanced();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = StudyRoomEnhanced;
}