@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <div class="text-sm font-medium text-blue-600">Step 5 of 5</div>
                <div class="text-sm text-gray-500">Payout Setup</div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full" style="width: 100%"></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Payout Setup</h1>
                <p class="text-gray-600">Configure how you want to receive your earnings</p>
            </div>

            <!-- Application Review -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
                <h3 class="font-semibold text-green-800 mb-4 flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    Documents Uploaded Successfully!
                </h3>
                <p class="text-sm text-green-700">
                    Your documents have been uploaded and are under review. Now set up your payout method to receive earnings from your courses.
                </p>
            </div>

            <form action="{{ route('instructor.apply.step5.store') }}" method="POST" id="payoutForm">
                @csrf
                
                <!-- ADD THIS ERROR DISPLAY SECTION -->
                @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-red-800 mb-2 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Please fix the following errors:
                    </h3>
                    <ul class="text-sm text-red-700 list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
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
                            <div class="w-full p-4 border-2 border-gray-300 rounded-lg hover:border-blue-400 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition">
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
                            <div class="w-full p-4 border-2 border-gray-300 rounded-lg hover:border-blue-400 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition">
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
                            <div class="w-full p-4 border-2 border-gray-300 rounded-lg hover:border-blue-400 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition">
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

                <!-- Dynamic Form Fields Based on Selection -->
                <div id="formFields" class="space-y-6 mb-8">
                    <!-- Default/Empty state -->
                    <div class="text-center py-8 text-gray-500" id="defaultState">
                        <i class="fas fa-wallet text-4xl mb-3"></i>
                        <p>Select a payout method to continue</p>
                    </div>

                    <!-- Mobile Money Fields -->
                    <div id="mobileMoneyFields" class="hidden space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="mobile_operator" class="block text-sm font-medium text-gray-700 mb-2">
                                    Mobile Operator *
                                </label>
                                <select name="operator" id="mobile_operator" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Operator</option>
                                    <option value="MTN" {{ old('operator') === 'MTN' ? 'selected' : '' }}>MTN Mobile Money</option>
                                    <option value="Orange" {{ old('operator') === 'Orange' ? 'selected' : '' }}>Orange Money</option>
                                </select>
                            </div>
                            <div>
                                <label for="mobile_currency" class="block text-sm font-medium text-gray-700 mb-2">
                                    Currency *
                                </label>
                                <select name="currency" id="mobile_currency" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    <option value="XAF" selected>XAF (Central African CFA Franc)</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="mobile_account_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Account Holder Name *
                                </label>
                                <input type="text" name="account_name" id="mobile_account_name" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="As it appears on your mobile money account"
                                    value="{{ old('account_name', $payout->account_name ?? '') }}">
                            </div>
                            <div>
                                <label for="mobile_account_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Mobile Number *
                                </label>
                                <input type="text" name="account_number" id="mobile_account_number" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="e.g., 237674000000"
                                    value="{{ old('account_number', $payout->account_number ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Bank Account Fields -->
                    <div id="bankAccountFields" class="hidden space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="bank_operator" class="block text-sm font-medium text-gray-700 mb-2">
                                    Bank Name *
                                </label>
                                <select name="operator" id="bank_operator" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Bank</option>
                                    <option value="Afriland First Bank">Afriland First Bank</option>
                                    <option value="BICEC">BICEC</option>
                                    <option value="Société Générale">Société Générale</option>
                                    <option value="UBA">UBA Cameroon</option>
                                    <option value="ECOBANK">ECOBANK</option>
                                    <option value="Other">Other Bank</option>
                                </select>
                            </div>
                            <div>
                                <label for="bank_currency" class="block text-sm font-medium text-gray-700 mb-2">
                                    Currency *
                                </label>
                                <select name="currency" id="bank_currency" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    <option value="XAF" selected>XAF</option>
                                    <option value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="bank_account_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Account Holder Name *
                                </label>
                                <input type="text" name="account_name" id="bank_account_name" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="As it appears on your bank account"
                                    value="{{ old('account_name', $payout->account_name ?? '') }}">
                            </div>
                            <div>
                                <label for="bank_account_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Bank Account Number *
                                </label>
                                <input type="text" name="account_number" id="bank_account_number" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="23-digit CEMAC account number"
                                    value="{{ old('account_number', $payout->account_number ?? '') }}">
                            </div>
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
                        
                        <!-- ADD THESE HIDDEN FIELDS -->
                        <input type="hidden" name="account_name" value="{{ Auth::user()->name }}">
                        <input type="hidden" name="account_number" value="tranzak_wallet_{{ Auth::id() }}">
                        <input type="hidden" name="operator" value="Tranzak">
                        
                        <div>
                            <label for="wallet_currency" class="block text-sm font-medium text-gray-700 mb-2">
                                Preferred Currency *
                            </label>
                            <select name="currency" id="wallet_currency" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="XAF" selected>XAF</option>
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Payout Settings -->
                <div class="bg-gray-50 rounded-lg p-6 mb-8">
                    <h3 class="font-semibold text-gray-700 mb-4">Payout Settings</h3>
                    
                    <div class="space-y-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="auto_payout" value="1" 
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                {{ old('auto_payout', $payout->auto_payout ?? true) ? 'checked' : '' }}>
                            <span class="ml-3 text-sm text-gray-700">
                                Enable automatic monthly payouts
                            </span>
                        </label>

                        <div>
                            <label for="payout_threshold" class="block text-sm font-medium text-gray-700 mb-2">
                                Minimum Payout Threshold
                            </label>
                            <input type="number" name="payout_threshold" id="payout_threshold" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                placeholder="0"
                                value="{{ old('payout_threshold', $payout->payout_threshold ?? 0) }}"
                                min="0" step="1000">
                            <p class="text-sm text-gray-500 mt-1">
                                Minimum amount required for automatic payout (set to 0 for no minimum)
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Terms Agreement -->
                <div class="mb-8">
                    <label class="flex items-start">
                        <input type="checkbox" name="agree_terms" value="1" 
                            class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            {{ old('agree_terms') ? 'checked' : '' }} required>
                        <span class="ml-3 text-sm text-gray-700">
                            I agree to the 
                            <a href="#" class="text-blue-600 hover:text-blue-500">Payout Terms</a>
                            and confirm that the provided account details are correct. 
                            I understand that incorrect information may delay payouts.
                        </span>
                    </label>
                    @error('agree_terms')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payout Information -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8">
                    <h3 class="font-semibold text-yellow-800 mb-3 flex items-center">
                        <i class="fas fa-lightbulb mr-2"></i>
                        How Payouts Work
                    </h3>
                    <ul class="text-sm text-yellow-700 space-y-2">
                        <li>• You earn <strong>70%</strong> of each course sale</li>
                        <li>• Platform fee: <strong>30%</strong> for payment processing and maintenance</li>
                        <li>• Payouts are processed automatically on the <strong>last day of each month</strong></li>
                        <li>• Funds are transferred to your selected payout method</li>
                        <li>• You can update your payout settings anytime from your dashboard</li>
                    </ul>
                </div>

                <!-- Navigation -->
                <div class="flex justify-between">
                    <a href="{{ route('instructor.apply.step4') }}" 
                        class="px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Documents
                    </a>
                    <button type="submit" id="submitButton"
                        class="px-8 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-check-circle mr-2"></i>
                        Complete Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const payoutMethods = document.querySelectorAll('input[name="payout_method"]');
    const formFields = document.getElementById('formFields');
    const defaultState = document.getElementById('defaultState');
    
    const mobileMoneyFields = document.getElementById('mobileMoneyFields');
    const bankAccountFields = document.getElementById('bankAccountFields');
    const tranzakWalletFields = document.getElementById('tranzakWalletFields');

    function showFields(method) {
        // Hide all fields first
        defaultState.classList.add('hidden');
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
            default:
                defaultState.classList.remove('hidden');
        }
    }

    // Add event listeners to radio buttons
    payoutMethods.forEach(radio => {
        radio.addEventListener('change', function() {
            showFields(this.value);
            
            // Auto-populate for Tranzak Wallet
            if (this.value === 'tranzak_wallet') {
                // You can auto-populate the account fields here if needed
                console.log('Tranzak wallet selected');
            }
        });
    });

    // Show fields if method is already selected (on page load or validation error)
    const selectedMethod = document.querySelector('input[name="payout_method"]:checked');
    if (selectedMethod) {
        showFields(selectedMethod.value);
    }
});
</script>

<style>
.peer:checked ~ div {
    border-color: #3b82f6;
    background-color: #eff6ff;
}

input[type="radio"]:checked ~ div {
    border-color: #3b82f6;
    background-color: #eff6ff;
}
</style>
@endsection