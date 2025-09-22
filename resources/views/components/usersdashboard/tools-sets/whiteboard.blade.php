 <!-- WHITEBOARD -->
    <div x-show="showTool === 'whiteboard'" x-transition.opacity x-data="whiteboardApp()" x-init="init()"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-[90%] h-[80%] p-4 flex flex-col">
            <div class="flex justify-between items-center mb-2">
                <h2 class="text-lg font-bold">Whiteboard</h2>
                <div class="flex gap-2">
                    <input type="color" x-model="color" class="p-1 rounded border">
                    <input type="range" x-model="lineWidth" min="1" max="10" class="w-24">
                    <button @click="undo" class="bg-gray-300 px-2 py-1 rounded hover:bg-gray-400">↩ Undo</button>
                    <button @click="clearWhiteboard" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">🗑 Clear</button>
                    <button @click="saveWhiteboard" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">💾 Save</button>
                    <button @click="showTool = null" class="text-gray-500 px-3 py-1 rounded hover:bg-gray-200">✖ Close</button>
                </div>
            </div>
            <canvas id="whiteboard" class="flex-1 border rounded bg-gray-50"></canvas>
        </div>
    </div>
<script>


/* ================= WHITEBOARD APP ================= */
function whiteboardApp() {
    return {
        canvas: null,
        ctx: null,
        drawing: false,
        color: '#1f2937',
        lineWidth: 2,
        history: [],
        redoStack: [],
        init() {
            this.canvas = document.getElementById('whiteboard');
            this.ctx = this.canvas.getContext('2d');
            this.resizeCanvas();
            window.addEventListener('resize', () => this.resizeCanvas());
            this.canvas.addEventListener('mousedown', e => this.start(e));
            this.canvas.addEventListener('mousemove', e => this.draw(e));
            this.canvas.addEventListener('mouseup', () => this.stop());
            this.canvas.addEventListener('mouseout', () => this.stop());
        },
        resizeCanvas() {
            const data = this.canvas.toDataURL();
            this.canvas.width = this.canvas.parentElement.clientWidth;
            this.canvas.height = this.canvas.parentElement.clientHeight - 60;
            let img = new Image();
            img.src = data;
            img.onload = () => this.ctx.drawImage(img,0,0);
        },
        start(e) { this.drawing=true; this.ctx.beginPath(); this.ctx.moveTo(e.offsetX,e.offsetY); },
        draw(e) { if(!this.drawing) return; this.ctx.strokeStyle=this.color; this.ctx.lineWidth=this.lineWidth; this.ctx.lineJoin='round'; this.ctx.lineCap='round'; this.ctx.lineTo(e.offsetX,e.offsetY); this.ctx.stroke(); },
        stop() { 
            if(this.drawing){
                this.history.push(this.ctx.getImageData(0,0,this.canvas.width,this.canvas.height));
                this.drawing=false; 
                this.redoStack=[];
            }
        },
        clearWhiteboard() { this.ctx.clearRect(0,0,this.canvas.width,this.canvas.height); this.history=[]; this.redoStack=[]; },
        saveWhiteboard() { let link=document.createElement('a'); link.href=this.canvas.toDataURL('image/png'); link.download='whiteboard.png'; link.click(); alert("Whiteboard saved!"); },
        undo() { 
            if(this.history.length>1){ 
                this.redoStack.push(this.history.pop());
                this.ctx.putImageData(this.history[this.history.length-1],0,0);
            } else this.clearWhiteboard();
        },
        redo() {
            if(this.redoStack.length>0){
                let img = this.redoStack.pop();
                this.history.push(img);
                this.ctx.putImageData(img,0,0);
            }
        }
    }
}


</script>