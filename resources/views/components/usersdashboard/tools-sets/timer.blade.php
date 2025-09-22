
<!-- TIMER / FOCUS POPUP -->
<div x-show="showTool === 'timer'" 
     x-transition.opacity
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl w-[400px] p-6 flex flex-col items-center">
        <div class="flex justify-between items-center w-full mb-4">
            <h2 class="text-lg font-semibold">Timer / Focus</h2>
            <button @click="showTool = null" class="text-gray-500 hover:text-gray-700">✖ Close</button>
        </div>
        <div class="w-full mb-4 flex flex-col items-center">
            <label class="mb-1 font-medium">Set Minutes:</label>
            <input type="number" x-model="timerMinutes" min="1" max="180" class="border p-2 rounded w-1/2 text-center">
        </div>
        <div class="text-4xl font-bold mb-4" x-text="formattedTime"></div>
        <div class="flex gap-4">
            <button @click="startTimer" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">▶ Start</button>
            <button @click="pauseTimer" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">⏸ Pause</button>
            <button @click="resetTimer" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">⏹ Reset</button>
        </div>
    </div>
</div>

<script>

    /* ================= TIMER / FOCUS APP ================= */
function timerApp() {
    return {
        timerMinutes: 25,
        timerSeconds: 0,
        interval: null,
        startSound: null,
        init() {
            this.startSound = new Audio('https://actions.google.com/sounds/v1/alarms/alarm_clock.ogg');
        },
        get formattedTime() {
            let min = String(this.timerMinutes).padStart(2,'0');
            let sec = String(this.timerSeconds).padStart(2,'0');
            return `${min}:${sec}`;
        },
        startTimer() {
            if(this.interval) return; // already running
            this.interval = setInterval(()=>{
                if(this.timerSeconds===0){
                    if(this.timerMinutes===0){
                        clearInterval(this.interval);
                        this.interval=null;
                        this.startSound.play();
                        alert("⏰ Time's up!");
                        return;
                    } else { this.timerMinutes--; this.timerSeconds=59; }
                } else { this.timerSeconds--; }
            },1000);
        },
        pauseTimer() { if(this.interval){ clearInterval(this.interval); this.interval=null; } },
        resetTimer() { this.pauseTimer(); this.timerMinutes=25; this.timerSeconds=0; }
    }
}
</script>