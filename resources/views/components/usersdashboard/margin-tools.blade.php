<div x-data="{ showTool: null }" class="fixed top-1/3 right-8 z-50 flex flex-col space-y-4">

    <!-- TOOL BUTTONS -->
    <template x-for="tool in [
        {name:'calculator', icon:'fa-calculator', color:'bg-indigo-600', tooltip:'Calculator'},
        {name:'notes', icon:'fa-sticky-note', color:'bg-green-600', tooltip:'Notes'},
        {name:'coding', icon:'fa-code', color:'bg-purple-600', tooltip:'Code Editor'},
        {name:'whiteboard', icon:'fa-chalkboard', color:'bg-yellow-500', tooltip:'Whiteboard'},
        {name:'timer', icon:'fa-hourglass-half', color:'bg-red-600', tooltip:'Timer'},
        {name:'dictionary', icon:'fa-book', color:'bg-pink-600', tooltip:'Dictionary'},
        {name:'quiz', icon:'fa-question-circle', color:'bg-teal-600', tooltip:'Quick Quiz'}
    ]" :key="tool.name">
        <div class="relative group">
            <button 
                @click="showTool = showTool === tool.name ? null : tool.name"
                :class="tool.color + ' text-white p-4 rounded-full shadow-xl hover:scale-110 transition-transform duration-200 flex items-center justify-center w-14 h-14'">
                <i :class="'fas ' + tool.icon + ' text-xl'"></i>
            </button>
            <div class="absolute right-full mr-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200 top-1/2 -translate-y-1/2 bg-black text-white text-sm px-3 py-1 rounded whitespace-nowrap">
                <span x-text="tool.tooltip"></span>
            </div>
        </div>
    </template>

    @include('components.usersdashboard.tools-sets.calculator')
    @include('components.usersdashboard.tools-sets.notes')
    @include('components.usersdashboard.tools-sets.code-editor')
    @include('components.usersdashboard.tools-sets.whiteboard')
    @include('components.usersdashboard.tools-sets.timer')
    @include('components.usersdashboard.tools-sets.quiz')
    @include('components.usersdashboard.tools-sets.dictionary')

</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs/loader.min.js"></script>
