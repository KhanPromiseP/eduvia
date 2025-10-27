@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Payout Setup</h1>
                    <p class="text-gray-600 mt-2">Configure how you want to receive your earnings</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('instructor.earnings') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Earnings
                    </a>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 animate-fade-in">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <div>
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if(session('warning'))
        <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4 animate-fade-in">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>
                <div>
                    <p class="text-yellow-800 font-medium">{{ session('warning') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4 animate-fade-in">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                <div>
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Current Payout Status -->
                @if($payout->exists)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Current Payout Setup</h2>
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $payout->is_verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $payout->is_verified ? 'Verified' : 'Pending Verification' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm text-gray-500">Payout Method</span>
                                <p class="font-medium text-gray-900 capitalize">
                                    @if($payout->payout_method === 'mobile_money')
                                        <i class="fas fa-mobile-alt mr-2 text-blue-600"></i>Mobile Money
                                    @elseif($payout->payout_method === 'bank_account')
                                        <i class="fas fa-university mr-2 text-green-600"></i>Bank Transfer
                                    @else
                                        <i class="fas fa-wallet mr-2 text-purple-600"></i>Tranzak Wallet
                                    @endif
                                </p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Account Name</span>
                                <p class="font-medium text-gray-900">{{ $payout->account_name }}</p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm text-gray-500">Account Number</span>
                                <p class="font-medium text-gray-900">{{ $payout->account_number }}</p>
                            </div>
                            @if($payout->operator)
                            <div>
                                <span class="text-sm text-gray-500">Operator/Bank</span>
                                <p class="font-medium text-gray-900">{{ $payout->operator }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Settings -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-500">Auto Payout</span>
                                <p class="font-medium text-gray-900">
                                    {{ $payout->auto_payout ? 'Enabled' : 'Disabled' }}
                                </p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Payout Threshold</span>
                                <p class="font-medium text-gray-900">
                                    ${{ number_format($payout->payout_threshold, 2) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Payout Setup Form -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">
                        {{ $payout->exists ? 'Update Payout Method' : 'Setup Payout Method' }}
                    </h2>

                    <form action="{{ route('instructor.payout.setup.store') }}" method="POST" id="payoutForm">
                        @csrf

                        <!-- Payout Method Selection -->
                        <div class="mb-8">
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                Payout Method *
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="payoutMethods">
                                <!-- Mobile Money -->
                                <label class="relative flex cursor-pointer">
                                    <input type="radio" name="payout_method" value="mobile_money" 
                                        class="peer hidden" 
                                        {{ old('payout_method', $payout->payout_method ?? '') === 'mobile_money' ? 'checked' : '' }} required>
                                    <div class="w-full p-4 border-2 border-gray-300 rounded-lg hover:border-blue-400 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all duration-200">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-mobile-alt text-blue-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">Mobile Money</p>
                                                <p class="text-sm text-gray-500">MTN, Orange Money</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <!-- Bank Transfer -->
                                <label class="relative flex cursor-pointer">
                                    <input type="radio" name="payout_method" value="bank_account" 
                                        class="peer hidden"
                                        {{ old('payout_method', $payout->payout_method ?? '') === 'bank_account' ? 'checked' : '' }}>
                                    <div class="w-full p-4 border-2 border-gray-300 rounded-lg hover:border-blue-400 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all duration-200">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-university text-green-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">Bank Transfer</p>
                                                <p class="text-sm text-gray-500">CEMAC Bank Accounts</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <!-- Tranzak Wallet -->
                                <label class="relative flex cursor-pointer">
                                    <input type="radio" name="payout_method" value="tranzak_wallet" 
                                        class="peer hidden"
                                        {{ old('payout_method', $payout->payout_method ?? '') === 'tranzak_wallet' ? 'checked' : '' }}>
                                    <div class="w-full p-4 border-2 border-gray-300 rounded-lg hover:border-blue-400 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all duration-200">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-wallet text-purple-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">Tranzak Wallet</p>
                                                <p class="text-sm text-gray-500">Keep funds in platform</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @error('payout_method')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dynamic Form Fields -->
                        <div id="formFields" class="space-y-6">
                            <!-- Mobile Money Fields -->
                            <div id="mobileMoneyFields" class="hidden space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="mobile_operator" class="block text-sm font-medium text-gray-700 mb-2">
                                            Mobile Operator *
                                        </label>
                                        <select name="operator" id="mobile_operator" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition">
                                            <option value="">Select Operator</option>
                                            <option value="MTN" {{ old('operator', $payout->operator ?? '') === 'MTN' ? 'selected' : '' }}>MTN Mobile Money</option>
                                            <option value="Orange" {{ old('operator', $payout->operator ?? '') === 'Orange' ? 'selected' : '' }}>Orange Money</option>
                                        </select>
                                        @error('operator')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="mobile_account_name" class="block text-sm font-medium text-gray-700 mb-2">
                                            Account Holder Name *
                                        </label>
                                        <input type="text" name="account_name" id="mobile_account_name" 
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition"
                                            placeholder="As it appears on your mobile money account"
                                            value="{{ old('account_name', $payout->account_name ?? '') }}">
                                        @error('account_name')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div>
                                    <label for="mobile_account_number" class="block text-sm font-medium text-gray-700 mb-2">
                                        Mobile Number *
                                    </label>
                                    <input type="text" name="account_number" id="mobile_account_number" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition"
                                        placeholder="e.g., 237674000000"
                                        value="{{ old('account_number', $payout->account_number ?? '') }}">
                                    <p class="text-sm text-gray-500 mt-1">Enter your mobile number with country code (e.g., 237 for Cameroon)</p>
                                    @error('account_number')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Bank Account Fields -->
                            <div id="bankAccountFields" class="hidden space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="bank_operator" class="block text-sm font-medium text-gray-700 mb-2">
                                            Bank Name *
                                        </label>
                                        <select name="operator" id="bank_operator" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition">
                                            <option value="">Select Bank</option>
                                            <option value="Afriland First Bank" {{ old('operator', $payout->operator ?? '') === 'Afriland First Bank' ? 'selected' : '' }}>Afriland First Bank</option>
                                            <option value="BICEC" {{ old('operator', $payout->operator ?? '') === 'BICEC' ? 'selected' : '' }}>BICEC</option>
                                            <option value="Société Générale" {{ old('operator', $payout->operator ?? '') === 'Société Générale' ? 'selected' : '' }}>Société Générale</option>
                                            <option value="UBA" {{ old('operator', $payout->operator ?? '') === 'UBA' ? 'selected' : '' }}>UBA Cameroon</option>
                                            <option value="ECOBANK" {{ old('operator', $payout->operator ?? '') === 'ECOBANK' ? 'selected' : '' }}>ECOBANK</option>
                                            <option value="Other" {{ old('operator', $payout->operator ?? '') === 'Other' ? 'selected' : '' }}>Other Bank</option>
                                        </select>
                                        @error('operator')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="bank_account_name" class="block text-sm font-medium text-gray-700 mb-2">
                                            Account Holder Name *
                                        </label>
                                        <input type="text" name="account_name" id="bank_account_name" 
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition"
                                            placeholder="As it appears on your bank account"
                                            value="{{ old('account_name', $payout->account_name ?? '') }}">
                                        @error('account_name')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div>
                                    <label for="bank_account_number" class="block text-sm font-medium text-gray-700 mb-2">
                                        Bank Account Number *
                                    </label>
                                    <input type="text" name="account_number" id="bank_account_number" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition"
                                        placeholder="23-digit CEMAC account number"
                                        value="{{ old('account_number', $payout->account_number ?? '') }}">
                                    <p class="text-sm text-gray-500 mt-1">Enter your 23-digit CEMAC bank account number</p>
                                    @error('account_number')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Tranzak Wallet Fields -->
                            <div id="tranzakWalletFields" class="hidden space-y-4">
                                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-info-circle text-purple-500 mr-3"></i>
                                        <div>
                                            <p class="text-sm text-purple-700">
                                                Funds will be kept in your Tranzak wallet. You can transfer to other methods anytime.
                                                No additional setup required.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="account_name" value="{{ auth()->user()->name }}">
                                <input type="hidden" name="account_number" value="tranzak_wallet_{{ auth()->id() }}">
                                <input type="hidden" name="operator" value="Tranzak">
                            </div>
                        </div>

                        <!-- Payout Settings -->
                        <div class="bg-gray-50 rounded-lg p-6 mt-8">
                            <h3 class="font-semibold text-gray-700 mb-4">Payout Settings</h3>
                            
                            <div class="space-y-4">
                                <label class="flex items-center">
                                    <input type="checkbox" name="auto_payout" value="1" 
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 transition"
                                        {{ old('auto_payout', $payout->auto_payout ?? true) ? 'checked' : '' }}>
                                    <span class="ml-3 text-sm text-gray-700">
                                        Enable automatic monthly payouts
                                    </span>
                                </label>

                                <div>
                                    <label for="payout_threshold" class="block text-sm font-medium text-gray-700 mb-2">
                                        Minimum Payout Threshold ($)
                                    </label>
                                    <input type="number" name="payout_threshold" id="payout_threshold" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition"
                                        placeholder="0.00"
                                        value="{{ old('payout_threshold', $payout->payout_threshold ?? 0) }}"
                                        min="0" step="0.01">
                                    <p class="text-sm text-gray-500 mt-1">
                                        Minimum amount required for automatic payout (set to 0 for no minimum)
                                    </p>
                                    @error('payout_threshold')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8">
                            <button type="submit" 
                                class="w-full md:w-auto px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-save mr-2"></i>
                                {{ $payout->exists ? 'Update Payout Settings' : 'Setup Payout Method' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Earnings Summary -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Earnings Summary</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Earnings</span>
                            <span class="font-semibold text-green-600">${{ number_format($totalEarnings, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Pending Payout</span>
                            <span class="font-semibold text-yellow-600">${{ number_format($pendingEarnings, 2) }}</span>
                        </div>
                        <div class="pt-4 border-t border-gray-200">
                            <a href="{{ route('instructor.earnings') }}" 
                               class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition">
                                <i class="fas fa-chart-bar mr-2"></i>
                                View Detailed Earnings
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Payout Information -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-3 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        How Payouts Work
                    </h3>
                    <ul class="text-sm text-blue-800 space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-600 mt-1 mr-2 text-xs"></i>
                            <span>You earn <strong>70%</strong> of each course sale</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-600 mt-1 mr-2 text-xs"></i>
                            <span>Platform fee: <strong>30%</strong> for maintenance</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-600 mt-1 mr-2 text-xs"></i>
                            <span>Payouts processed on the <strong>last day of each month</strong></span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-600 mt-1 mr-2 text-xs"></i>
                            <span>Funds transferred to your selected method</span>
                        </li>
                    </ul>
                </div>

                <!-- Security Notice -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-yellow-900 mb-3 flex items-center">
                        <i class="fas fa-shield-alt mr-2"></i>
                        Security & Privacy
                    </h3>
                    <p class="text-sm text-yellow-800">
                        Your payout information is encrypted and stored securely. We comply with data protection regulations and will never share your financial details with third parties.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


<script>
document.addEventListener('DOMContentLoaded', function() {
    const payoutMethods = document.querySelectorAll('input[name="payout_method"]');
    const formFields = document.getElementById('formFields');
    
    const mobileMoneyFields = document.getElementById('mobileMoneyFields');
    const bankAccountFields = document.getElementById('bankAccountFields');
    const tranzakWalletFields = document.getElementById('tranzakWalletFields');

    function showFields(method) {
        // Hide all fields first
        mobileMoneyFields.classList.add('hidden');
        bankAccountFields.classList.add('hidden');
        tranzakWalletFields.classList.add('hidden');

        // Show selected method's fields
        switch(method) {
            case 'mobile_money':
                mobileMoneyFields.classList.remove('hidden');
                break;
            case 'bank_account':
                bankAccountFields.classList.remove('hidden');
                break;
            case 'tranzak_wallet':
                tranzakWalletFields.classList.remove('hidden');
                break;
        }
    }

    // Add event listeners to radio buttons
    payoutMethods.forEach(radio => {
        radio.addEventListener('change', function() {
            showFields(this.value);
        });
    });

    // Show fields if method is already selected (on page load or validation error)
    const selectedMethod = document.querySelector('input[name="payout_method"]:checked');
    if (selectedMethod) {
        showFields(selectedMethod.value);
    }

    // Form validation
    const form = document.getElementById('payoutForm');
    form.addEventListener('submit', function(e) {
        const selectedMethod = document.querySelector('input[name="payout_method"]:checked');
        if (!selectedMethod) {
            e.preventDefault();
            alert('Please select a payout method');
            return;
        }
    });
});
</script>

<style>
.peer:checked ~ div {
    border-color: #3b82f6;
    background-color: #eff6ff;
    transform: scale(1.02);
}

input[type="radio"]:checked ~ div {
    border-color: #3b82f6;
    background-color: #eff6ff;
    transform: scale(1.02);
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Smooth transitions */
.bg-white, input, select, button {
    transition: all 0.2s ease-in-out;
}
</style>
