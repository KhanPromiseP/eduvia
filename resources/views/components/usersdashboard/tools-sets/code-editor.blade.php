
    <!-- CODE EDITOR -->
    <div x-show="showTool === 'coding'" x-transition.opacity x-data="codeEditorApp()" x-init="init()"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-[90%] h-[80%] p-4 flex flex-col">
            <div class="flex justify-between items-center mb-2 gap-2">
                <input x-model="newTitle" placeholder="Snippet Title" class="border rounded px-2 py-1 w-1/3">
                <select x-model="language" class="border rounded px-2 py-1">
                    <option value="javascript">JavaScript</option>
                    <option value="html">HTML</option>
                    <option value="css">CSS</option>
                    <option value="python">Python</option>
                    <option value="php">PHP</option>
                    <option value="cpp">C++</option>
                </select>
                <div class="flex gap-2">
                    <button @click="saveSnippet" class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 transition">💾 Save</button>
                    <button @click="runCode" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 transition">▶ Run</button>
                    <button @click="showTool = null" class="text-gray-500 px-3 py-1 rounded hover:bg-gray-200 transition">✖ Close</button>
                </div>
            </div>
            <div class="flex flex-1 border rounded overflow-hidden mt-2">
                <div id="editor" class="w-1/2 h-full"></div>
                <iframe id="preview" class="w-1/2 h-full bg-white border-l"></iframe>
            </div>
        </div>
    </div>

    <script>
        /* ================= CODE EDITOR APP ================= */
function codeEditorApp() {
    return {
        editor: null,
        newTitle: '',
        language: 'javascript',
        snippets: [],
        init() {
            require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs' }});
            require(["vs/editor/editor.main"], () => {
                this.editor = monaco.editor.create(document.getElementById("editor"), {
                    value: "// Start coding...",
                    language: this.language,
                    theme: "vs-dark",
                    automaticLayout: true,
                });
            });
        },
        runCode() {
            let code = this.editor.getValue();
            let iframe = document.getElementById("preview");
            let doc = iframe.contentDocument || iframe.contentWindow.document;
            if(['html','javascript','css'].includes(this.language)){
                doc.open(); doc.write(code); doc.close();
            } else {
                doc.open(); doc.write(`<pre>Cannot run ${this.language} in browser</pre>`); doc.close();
            }
        },
        saveSnippet() {
            let code = this.editor.getValue();
            if(!this.newTitle) return alert("Enter snippet title");
            let snippet={title:this.newTitle, code:this.editor.getValue(), language:this.language};
            this.snippets.unshift(snippet);
            this.newTitle='';
            alert("Snippet saved!");
            console.log(snippet);
        }
    }
}
</script>