@extends('layouts.auth')

@section('title', __('Register Business'))

@section('content')
<div class="tw-min-h-screen tw-bg-gradient-to-br tw-from-blue-900 tw-via-blue-800 tw-to-indigo-900 tw-flex tw-items-center tw-justify-center tw-py-12 tw-px-4 sm:tw-px-6 lg:tw-px-8">
    <div class="tw-max-w-4xl tw-w-full tw-space-y-8">
        <!-- Vision Care Header -->
        <div class="tw-text-center">
            <div class="tw-mx-auto tw-h-20 tw-w-20 tw-flex tw-items-center tw-justify-center tw-bg-white tw-rounded-full tw-shadow-lg tw-mb-6">
                <img src="{{ asset('img/logo-small.png') }}" alt="Vision Care" class="tw-h-12 tw-w-12 tw-object-contain" />
            </div>
            <h2 class="tw-text-3xl tw-font-bold tw-text-white tw-mb-2">Vision Care POS</h2>
            <p class="tw-text-blue-200 tw-text-lg">Register New Business</p>
            <p class="tw-text-blue-300 tw-text-sm tw-mt-2">Fill in the details to create your business</p>
        </div>

        <!-- Main Content Card -->
        <div class="tw-bg-white tw-rounded-2xl tw-shadow-2xl tw-p-8 tw-space-y-6">
            <div class="login-content">
                <div class="login-header tw-text-center tw-mb-6">
                    <h3 class="tw-text-2xl tw-font-semibold tw-text-gray-900 tw-mb-2">Business Registration</h3>
                    <p class="tw-text-gray-600">Set up your business to get started with Vision Care POS</p>
                </div>

                @if($errors->any())
                    <div class="tw-bg-red-50 tw-border tw-border-red-200 tw-rounded-lg tw-p-4 tw-mb-6">
                        <div class="tw-flex">
                            <div class="tw-flex-shrink-0">
                                <svg class="tw-h-5 tw-w-5 tw-text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="tw-ml-3">
                                <ul class="tw-text-sm tw-text-red-800 tw-list-disc tw-list-inside tw-mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('business.store') }}" enctype="multipart/form-data" class="tw-space-y-6">
                    @csrf
                    
                    <!-- Basic Information Section -->
                    <div class="tw-bg-gray-50 tw-rounded-lg tw-p-6">
                        <h4 class="tw-text-lg tw-font-semibold tw-text-gray-900 tw-mb-4 tw-flex tw-items-center">
                            <svg class="tw-w-5 tw-h-5 tw-mr-2 tw-text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Basic Information
                        </h4>
                        
                        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6">
                            <div class="form-group">
                                <label for="name" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">
                                    @lang('Business Name') <span class="tw-text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" 
                                       class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-focus:ring-2 tw-focus:ring-blue-500 tw-focus:border-blue-500 tw-transition-colors" 
                                       value="{{ old('name') }}" required placeholder="Enter your business name">
                            </div>
                            
                            <div class="form-group">
                                <label for="currency_id" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">
                                    @lang('Currency') <span class="tw-text-red-500">*</span>
                                </label>
                                <select name="currency_id" id="currency_id" 
                                        class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-focus:ring-2 tw-focus:ring-blue-500 tw-focus:border-blue-500 tw-transition-colors" required>
                                    <option value="">@lang('Select Currency')</option>
                                    @php
                                        $currencies = \App\Currency::all();
                                    @endphp
                                    @foreach($currencies as $currency)
                                        <option value="{{ $currency->id }}" {{ old('currency_id') == $currency->id ? 'selected' : '' }}>
                                            {{ $currency->code }} - {{ $currency->currency }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6 tw-mt-6">
                            <div class="form-group">
                                <label for="start_date" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">
                                    @lang('Business Start Date') <span class="tw-text-red-500">*</span>
                                </label>
                                <input type="date" name="start_date" id="start_date" 
                                       class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-focus:ring-2 tw-focus:ring-blue-500 tw-focus:border-blue-500 tw-transition-colors" 
                                       value="{{ old('start_date', date('Y-m-d')) }}" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="fy_start_month" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">
                                    @lang('Financial Year Start Month') <span class="tw-text-red-500">*</span>
                                </label>
                                <select name="fy_start_month" id="fy_start_month" 
                                        class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-focus:ring-2 tw-focus:ring-blue-500 tw-focus:border-blue-500 tw-transition-colors" required>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ old('fy_start_month', 1) == $i ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Business Settings Section -->
                    <div class="tw-bg-gray-50 tw-rounded-lg tw-p-6">
                        <h4 class="tw-text-lg tw-font-semibold tw-text-gray-900 tw-mb-4 tw-flex tw-items-center">
                            <svg class="tw-w-5 tw-h-5 tw-mr-2 tw-text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Business Settings
                        </h4>
                        
                        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6">
                            <div class="form-group">
                                <label for="accounting_method" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">
                                    @lang('Accounting Method') <span class="tw-text-red-500">*</span>
                                </label>
                                <select name="accounting_method" id="accounting_method" 
                                        class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-focus:ring-2 tw-focus:ring-blue-500 tw-focus:border-blue-500 tw-transition-colors" required>
                                    <option value="fifo" {{ old('accounting_method', 'fifo') == 'fifo' ? 'selected' : '' }}>FIFO</option>
                                    <option value="lifo" {{ old('accounting_method') == 'lifo' ? 'selected' : '' }}>LIFO</option>
                                    <option value="avco" {{ old('accounting_method') == 'avco' ? 'selected' : '' }}>AVCO</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="transaction_edit_days" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">
                                    @lang('Transaction Edit Days') <span class="tw-text-red-500">*</span>
                                </label>
                                <input type="number" name="transaction_edit_days" id="transaction_edit_days" 
                                       class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-focus:ring-2 tw-focus:ring-blue-500 tw-focus:border-blue-500 tw-transition-colors" 
                                       value="{{ old('transaction_edit_days', 30) }}" min="0" required placeholder="30">
                            </div>
                        </div>

                        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6 tw-mt-6">
                            <div class="form-group">
                                <label for="date_format" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">
                                    @lang('Date Format') <span class="tw-text-red-500">*</span>
                                </label>
                                <select name="date_format" id="date_format" 
                                        class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-focus:ring-2 tw-focus:ring-blue-500 tw-focus:border-blue-500 tw-transition-colors" required>
                                    <option value="d-m-Y" {{ old('date_format', 'd-m-Y') == 'd-m-Y' ? 'selected' : '' }}>dd-mm-yyyy</option>
                                    <option value="m-d-Y" {{ old('date_format') == 'm-d-Y' ? 'selected' : '' }}>mm-dd-yyyy</option>
                                    <option value="d/m/Y" {{ old('date_format') == 'd/m/Y' ? 'selected' : '' }}>dd/mm/yyyy</option>
                                    <option value="m/d/Y" {{ old('date_format') == 'm/d/Y' ? 'selected' : '' }}>mm/dd/yyyy</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="time_format" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">
                                    @lang('Time Format') <span class="tw-text-red-500">*</span>
                                </label>
                                <select name="time_format" id="time_format" 
                                        class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-focus:ring-2 tw-focus:ring-blue-500 tw-focus:border-blue-500 tw-transition-colors" required>
                                    <option value="12" {{ old('time_format', '12') == '12' ? 'selected' : '' }}>12 Hour</option>
                                    <option value="24" {{ old('time_format') == '24' ? 'selected' : '' }}>24 Hour</option>
                                </select>
                            </div>
                        </div>

                        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6 tw-mt-6">
                            <div class="form-group">
                                <label for="currency_symbol_placement" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">
                                    @lang('Currency Symbol Placement') <span class="tw-text-red-500">*</span>
                                </label>
                                <select name="currency_symbol_placement" id="currency_symbol_placement" 
                                        class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-focus:ring-2 tw-focus:ring-blue-500 tw-focus:border-blue-500 tw-transition-colors" required>
                                    <option value="before" {{ old('currency_symbol_placement', 'before') == 'before' ? 'selected' : '' }}>Before Amount</option>
                                    <option value="after" {{ old('currency_symbol_placement') == 'after' ? 'selected' : '' }}>After Amount</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="sales_cmsn_agnt" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">
                                    @lang('Sales Commission Agent') <span class="tw-text-red-500">*</span>
                                </label>
                                <select name="sales_cmsn_agnt" id="sales_cmsn_agnt" 
                                        class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-focus:ring-2 tw-focus:ring-blue-500 tw-focus:border-blue-500 tw-transition-colors" required>
                                    <option value="logged_in_user" {{ old('sales_cmsn_agnt', 'logged_in_user') == 'logged_in_user' ? 'selected' : '' }}>Logged in User</option>
                                    <option value="user" {{ old('sales_cmsn_agnt') == 'user' ? 'selected' : '' }}>User</option>
                                    <option value="percentage" {{ old('sales_cmsn_agnt') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                </select>
                            </div>
                        </div>

                        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6 tw-mt-6">
                            <div class="form-group">
                                <label for="item_addition_method" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">
                                    @lang('Item Addition Method') <span class="tw-text-red-500">*</span>
                                </label>
                                <select name="item_addition_method" id="item_addition_method" 
                                        class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-focus:ring-2 tw-focus:ring-blue-500 tw-focus:border-blue-500 tw-transition-colors" required>
                                    <option value="1" {{ old('item_addition_method', '1') == '1' ? 'selected' : '' }}>Add to End</option>
                                    <option value="2" {{ old('item_addition_method') == '2' ? 'selected' : '' }}>Add to Beginning</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="stock_expiry_alert_days" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">
                                    @lang('Stock Expiry Alert Days') <span class="tw-text-red-500">*</span>
                                </label>
                                <input type="number" name="stock_expiry_alert_days" id="stock_expiry_alert_days" 
                                       class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-focus:ring-2 tw-focus:ring-blue-500 tw-focus:border-blue-500 tw-transition-colors" 
                                       value="{{ old('stock_expiry_alert_days', 30) }}" min="0" required placeholder="30">
                            </div>
                        </div>
                    </div>

                    <!-- Features Section -->
                    <div class="tw-bg-gray-50 tw-rounded-lg tw-p-6">
                        <h4 class="tw-text-lg tw-font-semibold tw-text-gray-900 tw-mb-4 tw-flex tw-items-center">
                            <svg class="tw-w-5 tw-h-5 tw-mr-2 tw-text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Features
                        </h4>
                        
                        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-4">
                            <div class="tw-flex tw-items-center tw-space-x-3">
                                <input type="checkbox" name="enable_brand" value="1" 
                                       class="tw-h-4 tw-w-4 tw-text-blue-600 tw-focus:ring-blue-500 tw-border-gray-300 tw-rounded" 
                                       {{ old('enable_brand', 1) ? 'checked' : '' }}>
                                <label class="tw-text-sm tw-font-medium tw-text-gray-700">
                                    @lang('Enable Brand')
                                </label>
                            </div>
                            
                            <div class="tw-flex tw-items-center tw-space-x-3">
                                <input type="checkbox" name="enable_category" value="1" 
                                       class="tw-h-4 tw-w-4 tw-text-blue-600 tw-focus:ring-blue-500 tw-border-gray-300 tw-rounded" 
                                       {{ old('enable_category', 1) ? 'checked' : '' }}>
                                <label class="tw-text-sm tw-font-medium tw-text-gray-700">
                                    @lang('Enable Category')
                                </label>
                            </div>
                            
                            <div class="tw-flex tw-items-center tw-space-x-3">
                                <input type="checkbox" name="enable_sub_category" value="1" 
                                       class="tw-h-4 tw-w-4 tw-text-blue-600 tw-focus:ring-blue-500 tw-border-gray-300 tw-rounded" 
                                       {{ old('enable_sub_category', 1) ? 'checked' : '' }}>
                                <label class="tw-text-sm tw-font-medium tw-text-gray-700">
                                    @lang('Enable Sub Category')
                                </label>
                            </div>
                            
                            <div class="tw-flex tw-items-center tw-space-x-3">
                                <input type="checkbox" name="enable_price_tax" value="1" 
                                       class="tw-h-4 tw-w-4 tw-text-blue-600 tw-focus:ring-blue-500 tw-border-gray-300 tw-rounded" 
                                       {{ old('enable_price_tax', 1) ? 'checked' : '' }}>
                                <label class="tw-text-sm tw-font-medium tw-text-gray-700">
                                    @lang('Enable Price & Tax')
                                </label>
                            </div>
                            
                            <div class="tw-flex tw-items-center tw-space-x-3">
                                <input type="checkbox" name="enable_racks" value="1" 
                                       class="tw-h-4 tw-w-4 tw-text-blue-600 tw-focus:ring-blue-500 tw-border-gray-300 tw-rounded" 
                                       {{ old('enable_racks') ? 'checked' : '' }}>
                                <label class="tw-text-sm tw-font-medium tw-text-gray-700">
                                    @lang('Enable Racks')
                                </label>
                            </div>
                            
                            <div class="tw-flex tw-items-center tw-space-x-3">
                                <input type="checkbox" name="keyboard_shortcuts" value="1" 
                                       class="tw-h-4 tw-w-4 tw-text-blue-600 tw-focus:ring-blue-500 tw-border-gray-300 tw-rounded" 
                                       {{ old('keyboard_shortcuts', 1) ? 'checked' : '' }}>
                                <label class="tw-text-sm tw-font-medium tw-text-gray-700">
                                    @lang('Enable Keyboard Shortcuts')
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="tw-flex tw-flex-col sm:tw-flex-row tw-gap-4 tw-pt-6">
                        <button type="submit" class="tw-flex-1 tw-bg-blue-600 tw-text-white tw-py-3 tw-px-6 tw-rounded-lg tw-font-semibold tw-hover:bg-blue-700 tw-focus:outline-none tw-focus:ring-2 tw-focus:ring-blue-500 tw-focus:ring-offset-2 tw-transition-colors tw-duration-200 tw-flex tw-items-center tw-justify-center">
                            <svg class="tw-w-5 tw-h-5 tw-mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            @lang('Register Business')
                        </button>
                        
                        <a href="{{ route('business.select') }}" class="tw-flex-1 tw-bg-gray-600 tw-text-white tw-py-3 tw-px-6 tw-rounded-lg tw-font-semibold tw-hover:bg-gray-700 tw-focus:outline-none tw-focus:ring-2 tw-focus:ring-gray-500 tw-focus:ring-offset-2 tw-transition-colors tw-duration-200 tw-flex tw-items-center tw-justify-center">
                            <svg class="tw-w-5 tw-h-5 tw-mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            @lang('Back')
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="tw-text-center tw-text-blue-200 tw-text-sm">
            <p>&copy; {{ date('Y') }} Vision Care POS. All rights reserved.</p>
        </div>
    </div>
</div>
@endsection