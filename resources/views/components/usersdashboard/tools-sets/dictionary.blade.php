



<!-- DICTIONARY / WORD LOOKUP POPUP -->
<div x-show="showTool === 'dictionary'" 
     x-transition.opacity
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div x-data="dictionaryApp()" x-init="init()" class="bg-white rounded-xl shadow-xl w-[500px] p-6 flex flex-col">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Dictionary</h2>
            <button @click="showTool = null" class="text-gray-500 hover:text-gray-700">✖ Close</button>
        </div>

        <div class="flex gap-2 mb-4">
            <input type="text" x-model="query" @keydown.enter="searchWord"
                   placeholder="Enter word..." class="flex-1 border rounded px-3 py-2">
            <button @click="searchWord" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">🔍 Search</button>
        </div>

        <template x-if="loading">
            <div class="text-center text-gray-500 py-4">Searching...</div>
        </template>

        <template x-if="error">
            <div class="text-center text-red-500 py-4" x-text="error"></div>
        </template>

        <template x-if="definition">
            <div class="space-y-2">
                <div class="font-medium text-lg" x-text="definition.word"></div>
                <div class="text-gray-700" x-html="definition.meaning"></div>
                <template x-if="definition.audio">
                    <audio :src="definition.audio" controls class="w-full mt-2"></audio>
                </template>
            </div>
        </template>
    </div>
</div>



<script>




/* ================= DICTIONARY / WORD LOOKUP APP ================= */
function dictionaryApp() {
    return {
        query:'',
        definition:null,
        loading:false,
        error:null,
        init() {},
        async searchWord() {
            if(!this.query) return;
            this.loading=true;
            this.error=null;
            this.definition=null;
            try {
                const res = await fetch(`https://api.dictionaryapi.dev/api/v2/entries/en/${this.query}`);
                if(!res.ok) throw new Error("Word not found");
                const data = await res.json();
                const first = data[0];
                this.definition = {
                    word:first.word,
                    meaning:first.meanings.map(m => `<strong>${m.partOfSpeech}:</strong> ${m.definitions.map(d=>d.definition).join(', ')}`).join('<br>'),
                    audio:first.phonetics.find(p=>p.audio)?.audio || null
                };
            } catch(err) { this.error=err.message; }
            finally { this.loading=false; }
        },
        playAudio() { if(this.definition?.audio) new Audio(this.definition.audio).play(); }
    }
}
</script>
