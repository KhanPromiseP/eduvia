<!-- ================= ONLINE QUIZ POPUP ================= -->
<div x-show="showTool==='quiz'" x-transition.opacity class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
  <div x-data="onlineQuizApp()" x-init="init()" class="bg-white rounded-xl shadow-xl w-full max-w-[800px] max-h-[90vh] p-6 flex flex-col overflow-y-auto">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-bold">Online Quiz</h2>
      <button @click="showTool=null" class="text-gray-500 hover:text-gray-700 text-lg font-bold">✖</button>
    </div>

    <!-- Topic input -->
    <div class="mb-4 flex flex-col md:flex-row gap-2 items-center">
      <input type="text" x-model="topic" placeholder="Enter topic (e.g., Science, History, Coding)" class="border rounded px-3 py-2 flex-1">
      <input type="number" x-model="numQuestions" min="1" max="20" placeholder="Questions" class="border rounded px-3 py-2 w-24 text-center">
      <button @click="fetchQuiz" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Start Quiz</button>
    </div>

    <!-- Loading -->
    <template x-if="loading">
      <div class="text-center text-gray-500 py-8">Fetching questions online...</div>
    </template>

    <!-- Error -->
    <template x-if="error">
      <div class="text-center text-red-500 py-4" x-text="error"></div>
    </template>

    <!-- Question display -->
    <template x-if="questions.length>0">
      <div class="mb-6 border-b pb-4" x-data="{ showExplanation:false }">
        <div class="font-medium mb-2" x-html="`Q${currentIndex+1}: ${questions[currentIndex].question}`"></div>

        <template x-for="(option,i) in questions[currentIndex].options" :key="i">
          <button @click="selectAnswer(i)"
                  :class="{
                    'bg-green-600 text-white': questions[currentIndex].userAnswer===i && i===questions[currentIndex].correctAnswer,
                    'bg-red-600 text-white': questions[currentIndex].userAnswer===i && i!==questions[currentIndex].correctAnswer,
                    'bg-gray-200 hover:bg-gray-300': questions[currentIndex].userAnswer===null
                  }"
                  class="w-full text-left px-3 py-2 rounded mb-1 transition">
            <span x-html="option"></span>
          </button>
        </template>

        <!-- Show correct answer if wrong -->
        <div class="mt-2 text-sm text-gray-700" x-show="questions[currentIndex].userAnswer!==null && questions[currentIndex].userAnswer!==questions[currentIndex].correctAnswer">
          Correct Answer: <span class="font-semibold" x-html="questions[currentIndex].options[questions[currentIndex].correctAnswer]"></span>
        </div>

        <div class="mt-4 flex justify-between">
          <button @click="prevQuestion" class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400" :disabled="currentIndex===0">⬅ Previous</button>
          <button @click="nextQuestion" class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400" :disabled="currentIndex===questions.length-1">Next ➡</button>
        </div>
      </div>

      <!-- Final Score -->
      <div class="mt-4 border-t pt-4 flex flex-col items-center gap-2" x-show="allAnswered()">
        <div class="font-semibold text-lg">Score: <span x-text="score+'/'+totalQuestions()"></span> (<span x-text="getScorePercentage()+'%'"></span>)</div>
        <button @click="resetQuiz" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Reset Quiz</button>
      </div>
    </template>

  </div>
</div>

<script>
function onlineQuizApp(){
  return {
    topic:'',
    numQuestions:5,
    questions:[],
    currentIndex:0,
    score:0,
    loading:false,
    error:null,

    init(){},

    async fetchQuiz(){
      if(!this.topic.trim()){ this.error="Enter a topic"; return; }
      this.loading=true; this.error=null; this.questions=[]; this.score=0; this.currentIndex=0;

      try{
        // Example using Open Trivia DB
        let url = `https://opentdb.com/api.php?amount=${this.numQuestions}&category=9&type=multiple`;
        // For more advanced, you can map topic to category ID dynamically

        const res = await fetch(url);
        if(!res.ok) throw new Error("Failed to fetch questions online");
        const data = await res.json();
        if(data.response_code!==0) throw new Error("No questions found for this topic");

        // Map API response to our format
        this.questions = data.results.map(q=>{
          let options=[...q.incorrect_answers];
          options.push(q.correct_answer);
          // Shuffle options
          options.sort(()=>Math.random()-0.5);
          return {
            question:q.question,
            options:options,
            correctAnswer:options.indexOf(q.correct_answer),
            userAnswer:null
          };
        });

      }catch(e){ this.error=e.message; }
      finally{ this.loading=false; }
    },

    selectAnswer(i){
      let q=this.questions[this.currentIndex];
      if(q.userAnswer===null){
        q.userAnswer=i;
        if(i===q.correctAnswer) this.score++;
      }
    },

    nextQuestion(){ if(this.currentIndex<this.questions.length-1) this.currentIndex++; },
    prevQuestion(){ if(this.currentIndex>0) this.currentIndex--; },
    allAnswered(){ return this.questions.every(q=>q.userAnswer!==null); },
    resetQuiz(){ this.questions.forEach(q=>q.userAnswer=null); this.score=0; this.currentIndex=0; },
    totalQuestions(){ return this.questions.length; },
    getScorePercentage(){ return ((this.score/this.totalQuestions())*100).toFixed(0); }
  }
}
</script>
