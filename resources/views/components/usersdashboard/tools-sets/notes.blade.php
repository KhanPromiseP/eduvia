<!-- NOTES -->
    <div x-show="showTool === 'notes'" x-transition.opacity x-data="notesApp()" x-init="init()"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-[500px] p-6 flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold">Notes</h2>
                <button @click="showTool = null" class="text-gray-500 hover:text-gray-700 text-xl">✖</button>
            </div>
            <input type="text" x-model="noteTitle" placeholder="Sheet name" class="border p-2 rounded w-full mb-2">
            <textarea x-model="noteContent" placeholder="Write your notes..." class="w-full border p-2 rounded h-40 mb-3"></textarea>
            <div class="flex gap-2">
                <button @click="saveNote" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">Save</button>
                <button @click="clearNote" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 transition">Clear</button>
            </div>
            <div class="mt-4 overflow-y-auto max-h-40">
                <template x-for="(n, i) in notes" :key="i">
                    <div class="flex justify-between items-center bg-gray-100 p-2 mb-1 rounded">
                        <span x-text="n.title"></span>
                        <button @click="deleteNote(i)" class="text-red-600 hover:text-red-800 text-sm">✖</button>
                    </div>
                </template>
            </div>
        </div>
    </div>

<script>
    /* ================= NOTES APP ================= */
function notesApp() {
    return {
        noteTitle: '',
        noteContent: '',
        notes: JSON.parse(localStorage.getItem('userNotes') || '[]'),
        init() {
            // Load notes from localStorage on init
            this.notes = JSON.parse(localStorage.getItem('userNotes') || '[]');
        },
        saveNote() {
            if(this.noteTitle.trim() === '' || this.noteContent.trim() === '') {
                alert('Please enter both a title and content for the note.');
                return;
            }
            this.notes.push({ title: this.noteTitle, content: this.noteContent });
            localStorage.setItem('userNotes', JSON.stringify(this.notes));
            this.noteTitle = '';
            this.noteContent = '';
        },
        clearNote() {
            this.noteTitle = '';
            this.noteContent = '';
        },
        deleteNote(index) {
            this.notes.splice(index, 1);
            localStorage.setItem('userNotes', JSON.stringify(this.notes));
        }
    }
}
</script>
