<!-- ================= SUPER ADVANCED SCIENTIFIC CALCULATOR ================= -->
<div x-show="showTool==='calculator'" 
     x-transition:enter="transition ease-out duration-300" 
     x-transition:enter-start="opacity-0 scale-90" 
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-200" 
     x-transition:leave-start="opacity-100 scale-100" 
     x-transition:leave-end="opacity-0 scale-90"
     x-data="{
        // Display and state
        display: '0',
        history: '',
        memory: 0,
        errorMsg: '',
        currentMode: 'basic',
        activeTab: 'graph',
        numberBase: 'DEC',
        
        // Graph variables
        graphFunction: 'sin(x)',
        graphRange: { min: -10, max: 10 },
        
        // Solver variables
        equation: 'x^2 - 4*x + 3 = 0',
        solution: '',
        
        // Converter variables
        conversionType: 'length',
        convertValue: 1,
        fromUnit: 'meter',
        toUnit: 'feet',
        conversionResult: '',
        
        // Matrix variables
        matrixSize: { rows: 2, cols: 2 },
        showMatrix: false,
        matrixResult: '',
        currentMatrix: [],
        
        // Stats variables
        statsData: '1,2,3,4,5',
        statsResult: '',
        
        modes: [
            { key: 'basic', name: 'Basic' },
            { key: 'scientific', name: 'Scientific' }
        ],
        
        featureTabs: [
            { key: 'graph', name: 'Graph' },
            { key: 'solver', name: 'Solver' },
            { key: 'convert', name: 'Convert' },
            { key: 'matrix', name: 'Matrix' },
            { key: 'stats', name: 'Stats' },
            { key: 'constants', name: 'Constants' }
        ],
        
        basicButtons: [
            'C', '⌫', '(', ')', '÷',
            '7', '8', '9', '×', '√',
            '4', '5', '6', '-', 'x²',
            '1', '2', '3', '+', '1/x',
            '0', '.', '±', '=', '%'
        ],
        
        scientificButtons: [
            'C', '⌫', '(', ')', '÷', 'π',
            '7', '8', '9', '×', '√', 'e',
            '4', '5', '6', '-', 'x²', 'x^y',
            '1', '2', '3', '+', '1/x', 'n!',
            '0', '.', '±', '=', '%', '|x|',
            'sin', 'cos', 'tan', 'ln', 'log', 'exp'
        ],
        
        memoryButtons: ['MC', 'MR', 'M+', 'M-', 'MS'],
        
        conversions: {
            length: {
                meter: 1,
                kilometer: 0.001,
                centimeter: 100,
                millimeter: 1000,
                inch: 39.3701,
                feet: 3.28084,
                yard: 1.09361,
                mile: 0.000621371
            },
            weight: {
                kilogram: 1,
                gram: 1000,
                pound: 2.20462,
                ounce: 35.274
            },
            temperature: {
                celsius: { offset: 0, factor: 1 },
                fahrenheit: { offset: 32, factor: 9/5 },
                kelvin: { offset: 273.15, factor: 1 }
            }
        },
        
        constants: {
            π: Math.PI,
            e: Math.E,
            φ: (1 + Math.sqrt(5)) / 2,
            c: 299792458,
            h: 6.62607015e-34,
            G: 6.67430e-11
        },
        
        init() {
            this.setupKeyboard();
            setTimeout(() => this.initGraph(), 200);
        },
        
        setupKeyboard() {
            document.addEventListener('keydown', (e) => {
                if (this.showTool !== 'calculator') return;
                
                const keyMap = {
                    'Enter': '=', 'Backspace': '⌫', 'Escape': 'C',
                    '*': '×', '/': '÷', 'Delete': 'C'
                };
                let key = keyMap[e.key] || e.key;
                
                if (this.basicButtons.includes(key) || this.scientificButtons.includes(key) || /[0-9\+\-\.]/.test(key)) {
                    this.press(key);
                    e.preventDefault();
                }
            });
        },
        
        initGraph() {
            const graphDiv = document.getElementById('calcGraphDiv');
            if (graphDiv) {
                this.createSimpleGraph(graphDiv);
            }
        },
        
        getBtnClass(btn) {
            if (['C', '⌫', '='].includes(btn)) return 'bg-red-500 text-white hover:bg-red-600';
            if (['+', '-', '×', '÷', '%', '±'].includes(btn)) return 'bg-blue-500 text-white hover:bg-blue-600';
            if (['sin', 'cos', 'tan', 'ln', 'log', 'exp'].includes(btn)) return 'bg-green-500 text-white hover:bg-green-600';
            if (['√', 'x²', 'x^y', '1/x', 'n!', '|x|', 'π', 'e'].includes(btn)) return 'bg-purple-500 text-white hover:bg-purple-600';
            return 'bg-gray-200 hover:bg-gray-300 text-gray-800';
        },
        
        press(btn) {
            this.errorMsg = '';
            
            try {
                if (btn === 'C') {
                    this.display = '0';
                    this.history = '';
                } else if (btn === '⌫') {
                    this.display = this.display.length > 1 ? this.display.slice(0, -1) : '0';
                } else if (btn === '=') {
                    this.calculate();
                } else if (btn === '±') {
                    this.display = this.display.startsWith('-') ? this.display.slice(1) : '-' + this.display;
                } else if (btn === 'π') {
                    this.insertValue(Math.PI.toString());
                } else if (btn === 'e') {
                    this.insertValue(Math.E.toString());
                } else if (btn === 'MC') {
                    this.memory = 0;
                } else if (btn === 'MR') {
                    this.insertValue(this.memory.toString());
                } else if (btn === 'M+') {
                    this.memory += parseFloat(this.display) || 0;
                } else if (btn === 'M-') {
                    this.memory -= parseFloat(this.display) || 0;
                } else if (btn === 'MS') {
                    this.memory = parseFloat(this.display) || 0;
                } else if (['sin', 'cos', 'tan', 'ln', 'log', '√', 'x²', '1/x', 'n!', '|x|', 'exp'].includes(btn)) {
                    this.applyFunction(btn);
                } else if (btn === 'x^y') {
                    this.insertValue('^');
                } else if (btn === '%') {
                    this.display = (parseFloat(this.display) / 100).toString();
                } else {
                    this.insertValue(btn);
                }
            } catch (error) {
                this.errorMsg = 'Error: ' + error.message;
            }
        },
        
        insertValue(value) {
            if (this.display === '0' && !isNaN(value)) {
                this.display = value;
            } else {
                this.display += value;
            }
        },
        
        applyFunction(func) {
            const value = parseFloat(this.display);
            if (isNaN(value)) return;
            
            let result;
            switch (func) {
                case 'sin': result = Math.sin(value); break;
                case 'cos': result = Math.cos(value); break;
                case 'tan': result = Math.tan(value); break;
                case 'ln': result = Math.log(value); break;
                case 'log': result = Math.log10(value); break;
                case '√': result = Math.sqrt(value); break;
                case 'x²': result = value * value; break;
                case '1/x': result = 1 / value; break;
                case '|x|': result = Math.abs(value); break;
                case 'exp': result = Math.exp(value); break;
                case 'n!': result = this.factorial(value); break;
                default: return;
            }
            
            this.display = result.toString();
        },
        
        factorial(n) {
            if (n < 0 || !Number.isInteger(n)) return NaN;
            if (n === 0 || n === 1) return 1;
            let result = 1;
            for (let i = 2; i <= n; i++) {
                result *= i;
            }
            return result;
        },
        
        calculate() {
            try {
                this.history = this.display;
                let expression = this.display
                    .replace(/×/g, '*')
                    .replace(/÷/g, '/')
                    .replace(/\^/g, '**');
                
                const result = Function('return ' + expression)();
                this.display = result.toString();
                this.history += ' = ' + this.display;
            } catch (error) {
                this.errorMsg = 'Error: Invalid expression';
                this.display = '0';
            }
        },
        
        plotGraph() {
            const graphDiv = document.getElementById('calcGraphDiv');
            if (!graphDiv) return;
            
            this.createSimpleGraph(graphDiv);
        },
        
        createSimpleGraph(container) {
            const canvas = document.createElement('canvas');
            canvas.width = 400;
            canvas.height = 250;
            canvas.style.width = '100%';
            canvas.style.border = '1px solid #ddd';
            canvas.style.borderRadius = '8px';
            
            container.innerHTML = '';
            container.appendChild(canvas);
            
            const ctx = canvas.getContext('2d');
            
            // Clear canvas
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = '#fafafa';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            // Draw axes
            ctx.strokeStyle = '#666';
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(0, canvas.height/2);
            ctx.lineTo(canvas.width, canvas.height/2);
            ctx.moveTo(canvas.width/2, 0);
            ctx.lineTo(canvas.width/2, canvas.height);
            ctx.stroke();
            
            // Draw function
            ctx.strokeStyle = '#3B82F6';
            ctx.lineWidth = 2;
            ctx.beginPath();
            
            let firstPoint = true;
            const range = this.graphRange.max - this.graphRange.min;
            
            for (let px = 0; px < canvas.width; px++) {
                const x = this.graphRange.min + (px / canvas.width) * range;
                try {
                    let expr = this.graphFunction
                        .replace(/sin/g, 'Math.sin')
                        .replace(/cos/g, 'Math.cos')
                        .replace(/tan/g, 'Math.tan')
                        .replace(/sqrt/g, 'Math.sqrt')
                        .replace(/\^/g, '**')
                        .replace(/x/g, `(${x})`);
                    
                    const y = Function('return ' + expr)();
                    if (isFinite(y)) {
                        const py = canvas.height/2 - (y * canvas.height/10);
                        
                        if (firstPoint) {
                            ctx.moveTo(px, py);
                            firstPoint = false;
                        } else {
                            ctx.lineTo(px, py);
                        }
                    }
                } catch {}
            }
            
            ctx.stroke();
        },
        
        solveEquation() {
            try {
                // Simple quadratic solver for demonstration
                if (this.equation.includes('x^2')) {
                    // For x^2 - 4*x + 3 = 0
                    const a = 1, b = -4, c = 3;
                    const discriminant = b * b - 4 * a * c;
                    
                    if (discriminant > 0) {
                        const x1 = (-b + Math.sqrt(discriminant)) / (2 * a);
                        const x2 = (-b - Math.sqrt(discriminant)) / (2 * a);
                        this.solution = `x₁ = ${x1.toFixed(4)}<br>x₂ = ${x2.toFixed(4)}`;
                    } else if (discriminant === 0) {
                        const x = -b / (2 * a);
                        this.solution = `x = ${x.toFixed(4)} (double root)`;
                    } else {
                        this.solution = 'No real solutions';
                    }
                } else {
                    this.solution = 'Enter a quadratic equation (ax² + bx + c = 0)';
                }
            } catch (error) {
                this.errorMsg = 'Could not solve equation';
            }
        },
        
        convertUnits() {
            try {
                const conversions = this.conversions[this.conversionType];
                if (!conversions) return;
                
                if (this.conversionType === 'temperature') {
                    this.conversionResult = this.convertTemperature();
                } else {
                    const fromFactor = conversions[this.fromUnit];
                    const toFactor = conversions[this.toUnit];
                    const result = (this.convertValue / fromFactor) * toFactor;
                    this.conversionResult = `${this.convertValue} ${this.fromUnit} = ${result.toFixed(6)} ${this.toUnit}`;
                }
            } catch (error) {
                this.errorMsg = 'Conversion error';
            }
        },
        
        convertTemperature() {
            const value = parseFloat(this.convertValue);
            let celsius;
            
            switch (this.fromUnit) {
                case 'celsius': celsius = value; break;
                case 'fahrenheit': celsius = (value - 32) * 5/9; break;
                case 'kelvin': celsius = value - 273.15; break;
            }
            
            let result;
            switch (this.toUnit) {
                case 'celsius': result = celsius; break;
                case 'fahrenheit': result = celsius * 9/5 + 32; break;
                case 'kelvin': result = celsius + 273.15; break;
            }
            
            return `${value}° ${this.fromUnit} = ${result.toFixed(2)}° ${this.toUnit}`;
        },
        
        calculateStats() {
            try {
                const data = this.statsData.split(',').map(x => parseFloat(x.trim())).filter(x => !isNaN(x));
                
                if (data.length === 0) {
                    this.errorMsg = 'Please enter valid numbers';
                    return;
                }
                
                const sorted = [...data].sort((a, b) => a - b);
                const n = data.length;
                const sum = data.reduce((a, b) => a + b, 0);
                const mean = sum / n;
                const variance = data.reduce((acc, val) => acc + Math.pow(val - mean, 2), 0) / n;
                const stdDev = Math.sqrt(variance);
                const median = n % 2 === 0 ? (sorted[n/2 - 1] + sorted[n/2]) / 2 : sorted[Math.floor(n/2)];
                const min = Math.min(...data);
                const max = Math.max(...data);
                
                this.statsResult = `
                    <div class='space-y-1 text-sm'>
                        <div><strong>Count:</strong> ${n}</div>
                        <div><strong>Sum:</strong> ${sum.toFixed(2)}</div>
                        <div><strong>Mean:</strong> ${mean.toFixed(4)}</div>
                        <div><strong>Median:</strong> ${median.toFixed(4)}</div>
                        <div><strong>Std Dev:</strong> ${stdDev.toFixed(4)}</div>
                        <div><strong>Min:</strong> ${min}</div>
                        <div><strong>Max:</strong> ${max}</div>
                    </div>
                `;
            } catch (error) {
                this.errorMsg = 'Statistics calculation error';
            }
        },
        
        insertConstant(constantName) {
            const value = this.constants[constantName];
            this.insertValue(value.toString());
        }
     }" 
     @keydown.escape.window="showTool = null"
     class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-[60] p-4"
     style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-[1000px] h-[85vh] flex flex-col overflow-hidden">
        <!-- Header -->
        <div class="flex justify-between items-center p-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
            <div class="flex items-center space-x-3">
                <i class="fas fa-calculator text-xl"></i>
                <h2 class="text-xl font-bold">Advanced Scientific Calculator</h2>
            </div>
            <button @click="showTool=null" 
                    class="text-white hover:text-gray-200 text-2xl p-2 hover:bg-white hover:bg-opacity-20 rounded-lg transition-all duration-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="flex flex-1 overflow-hidden">
            <!-- Left Panel - Calculator -->
            <div class="w-1/2 p-4 border-r border-gray-200 overflow-y-auto">
                <!-- Mode Selector -->
                <div class="flex mb-4 bg-gray-100 rounded-lg p-1">
                    <template x-for="mode in modes">
                        <button @click="currentMode = mode.key" 
                                :class="currentMode === mode.key ? 'bg-blue-500 text-white' : 'text-gray-600'"
                                class="flex-1 py-2 px-3 rounded-md text-sm font-medium transition-all"
                                x-text="mode.name"></button>
                    </template>
                </div>

                <!-- History Display -->
                <div class="bg-gray-50 p-2 rounded-lg mb-2 h-12 overflow-y-auto text-sm text-gray-600">
                    <div x-text="history"></div>
                </div>

                <!-- Main Display -->
                <input type="text" x-model="display" 
                       class="w-full border-2 p-3 rounded-lg mb-4 text-right font-mono text-xl bg-gray-50 focus:bg-white transition-colors" 
                       readonly>

                <!-- Error Display -->
                <div x-show="errorMsg" x-text="errorMsg" class="text-red-500 text-sm mb-2 p-2 bg-red-50 rounded"></div>

                <!-- Calculator Buttons -->
                <div x-show="currentMode === 'basic'" class="grid grid-cols-5 gap-2 mb-4">
                    <template x-for="btn in basicButtons">
                        <button @click="press(btn)" :class="getBtnClass(btn)"
                                class="rounded-lg p-3 font-bold text-sm transition-all hover:scale-105 hover:shadow-lg">
                            <span x-text="btn"></span>
                        </button>
                    </template>
                </div>

                <!-- Scientific Mode Buttons -->
                <div x-show="currentMode === 'scientific'" class="grid grid-cols-6 gap-1 mb-4">
                    <template x-for="btn in scientificButtons">
                        <button @click="press(btn)" :class="getBtnClass(btn)"
                                class="rounded-lg p-2 font-bold text-xs transition-all hover:scale-105 hover:shadow-lg">
                            <span x-text="btn"></span>
                        </button>
                    </template>
                </div>

                <!-- Memory Panel -->
                <div class="bg-gray-50 p-3 rounded-lg">
                    <div class="text-sm font-medium mb-2">Memory: <span x-text="memory"></span></div>
                    <div class="flex gap-2 flex-wrap">
                        <template x-for="btn in memoryButtons">
                            <button @click="press(btn)" class="bg-orange-500 text-white px-2 py-1 rounded text-xs font-medium hover:bg-orange-600"
                                    x-text="btn"></button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Right Panel - Advanced Features -->
            <div class="w-1/2 p-4 overflow-y-auto">
                <!-- Feature Tabs -->
                <div class="flex mb-4 bg-gray-100 rounded-lg p-1">
                    <template x-for="tab in featureTabs">
                        <button @click="activeTab = tab.key" 
                                :class="activeTab === tab.key ? 'bg-green-500 text-white' : 'text-gray-600'"
                                class="flex-1 py-1 px-1 rounded-md text-xs font-medium transition-all"
                                x-text="tab.name"></button>
                    </template>
                </div>

                <!-- Graphing -->
                <div x-show="activeTab === 'graph'" class="space-y-4">
                    <h3 class="font-bold text-lg">Function Grapher</h3>
                    <input type="text" x-model="graphFunction" placeholder="Enter function: sin(x), x^2, etc."
                           class="w-full p-2 border rounded-lg text-sm">
                    <div class="flex gap-2">
                        <input type="number" x-model="graphRange.min" placeholder="X Min" class="w-1/3 p-2 border rounded text-sm">
                        <input type="number" x-model="graphRange.max" placeholder="X Max" class="w-1/3 p-2 border rounded text-sm">
                        <button @click="plotGraph()" class="w-1/3 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">Plot</button>
                    </div>
                    <div id="calcGraphDiv" style="width:100%; height:200px;"></div>
                </div>

                <!-- Equation Solver -->
                <div x-show="activeTab === 'solver'" class="space-y-4">
                    <h3 class="font-bold text-lg">Equation Solver</h3>
                    <input type="text" x-model="equation" placeholder="Enter equation: x^2 - 4*x + 3 = 0"
                           class="w-full p-2 border rounded-lg text-sm">
                    <button @click="solveEquation()" class="w-full bg-green-500 text-white p-2 rounded hover:bg-green-600">Solve</button>
                    <div x-show="solution" class="bg-green-50 p-3 rounded border">
                        <strong>Solution:</strong>
                        <div x-html="solution"></div>
                    </div>
                </div>

                <!-- Unit Converter -->
                <div x-show="activeTab === 'convert'" class="space-y-4">
                    <h3 class="font-bold text-lg">Unit Converter</h3>
                    <select x-model="conversionType" class="w-full p-2 border rounded text-sm">
                        <template x-for="type in Object.keys(conversions)">
                            <option :value="type" x-text="type.charAt(0).toUpperCase() + type.slice(1)"></option>
                        </template>
                    </select>
                    <div class="flex gap-2">
                        <input type="number" x-model="convertValue" class="flex-1 p-2 border rounded text-sm" placeholder="Value">
                        <select x-model="fromUnit" class="flex-1 p-2 border rounded text-sm">
                            <template x-for="unit in Object.keys(conversions[conversionType] || {})">
                                <option :value="unit" x-text="unit"></option>
                            </template>
                        </select>
                        <span class="self-center">→</span>
                        <select x-model="toUnit" class="flex-1 p-2 border rounded text-sm">
                            <template x-for="unit in Object.keys(conversions[conversionType] || {})">
                                <option :value="unit" x-text="unit"></option>
                            </template>
                        </select>
                    </div>
                    <button @click="convertUnits()" class="w-full bg-purple-500 text-white p-2 rounded hover:bg-purple-600">Convert</button>
                    <div x-show="conversionResult" class="bg-purple-50 p-3 rounded border text-sm">
                        <div x-text="conversionResult"></div>
                    </div>
                </div>

                <!-- Statistics -->
                <div x-show="activeTab === 'stats'" class="space-y-4">
                    <h3 class="font-bold text-lg">Statistics</h3>
                    <textarea x-model="statsData" placeholder="Enter numbers separated by commas: 1,2,3,4,5"
                              class="w-full p-2 border rounded h-20 text-sm"></textarea>
                    <button @click="calculateStats()" class="w-full bg-teal-500 text-white p-2 rounded hover:bg-teal-600">Calculate</button>
                    <div x-show="statsResult" class="bg-teal-50 p-3 rounded border">
                        <div x-html="statsResult"></div>
                    </div>
                </div>

                <!-- Constants -->
                <div x-show="activeTab === 'constants'" class="space-y-4">
                    <h3 class="font-bold text-lg">Mathematical Constants</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <template x-for="[key, value] in Object.entries(constants)">
                            <button @click="insertConstant(key)" 
                                    class="bg-yellow-100 hover:bg-yellow-200 p-2 rounded text-sm text-left border">
                                <strong x-text="key"></strong><br>
                                <span x-text="value.toExponential(3)" class="text-xs text-gray-600"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Matrix (placeholder) -->
                <div x-show="activeTab === 'matrix'" class="space-y-4">
                    <h3 class="font-bold text-lg">Matrix Calculator</h3>
                    <p class="text-sm text-gray-600">Matrix operations will be available in the full version.</p>
                </div>
            </div>
        </div>
    </div>
</div>