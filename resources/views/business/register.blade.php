@extends('layouts.auth')

@section('title', __('Register Business'))

@section('content')
<div class="login-form col-md-12 col-xs-12 right-col-content-register">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="login-content">
                <div class="login-header">
                    <h3 class="text-center">@lang('Register New Business')</h3>
                    <p class="text-center text-muted">Complete the steps to create your business</p>
                </div>

                <!-- Progress Steps -->
                <div class="progress-steps">
                    <div class="step active" data-step="1">
                        <div class="step-number">1</div>
                        <div class="step-title">Basic Info</div>
                    </div>
                    <div class="step" data-step="2">
                        <div class="step-number">2</div>
                        <div class="step-title">Financial Settings</div>
                    </div>
                    <div class="step" data-step="3">
                        <div class="step-number">3</div>
                        <div class="step-title">Preferences</div>
                    </div>
                    <div class="step" data-step="4">
                        <div class="step-number">4</div>
                        <div class="step-title">Features</div>
                    </div>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="/business/store" enctype="multipart/form-data" id="businessForm">
                    @csrf
                    
                    <!-- Step 1: Basic Information -->
                    <div class="form-step active" id="step-1">
                        <h4 class="step-heading">📋 Basic Business Information</h4>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">@lang('Business Name') <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" 
                                           value="{{ old('name') }}" required placeholder="Enter your business name">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="currency_id">@lang('Currency') <span class="text-danger">*</span></label>
                                    <select name="currency_id" id="currency_id" class="form-control" required>
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
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">@lang('Business Start Date') <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" 
                                           value="{{ old('start_date', date('Y-m-d')) }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Financial Settings -->
                    <div class="form-step" id="step-2">
                        <h4 class="step-heading">💰 Financial Settings</h4>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fy_start_month">@lang('Financial Year Start Month') <span class="text-danger">*</span></label>
                                    <select name="fy_start_month" id="fy_start_month" class="form-control" required>
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ old('fy_start_month', 1) == $i ? 'selected' : '' }}>
                                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="accounting_method">@lang('Accounting Method') <span class="text-danger">*</span></label>
                                    <select name="accounting_method" id="accounting_method" class="form-control" required>
                                        <option value="fifo" {{ old('accounting_method', 'fifo') == 'fifo' ? 'selected' : '' }}>FIFO (First In, First Out)</option>
                                        <option value="lifo" {{ old('accounting_method') == 'lifo' ? 'selected' : '' }}>LIFO (Last In, First Out)</option>
                                        <option value="avco" {{ old('accounting_method') == 'avco' ? 'selected' : '' }}>AVCO (Average Cost)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="currency_symbol_placement">@lang('Currency Symbol Placement') <span class="text-danger">*</span></label>
                                    <select name="currency_symbol_placement" id="currency_symbol_placement" class="form-control" required>
                                        <option value="before" {{ old('currency_symbol_placement', 'before') == 'before' ? 'selected' : '' }}>Before Amount ($100)</option>
                                        <option value="after" {{ old('currency_symbol_placement') == 'after' ? 'selected' : '' }}>After Amount (100$)</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="transaction_edit_days">@lang('Transaction Edit Days') <span class="text-danger">*</span></label>
                                    <input type="number" name="transaction_edit_days" id="transaction_edit_days" 
                                           class="form-control" value="{{ old('transaction_edit_days', 30) }}" min="0" required
                                           placeholder="Number of days">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Preferences -->
                    <div class="form-step" id="step-3">
                        <h4 class="step-heading">⚙️ System Preferences</h4>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_format">@lang('Date Format') <span class="text-danger">*</span></label>
                                    <select name="date_format" id="date_format" class="form-control" required>
                                        <option value="d-m-Y" {{ old('date_format', 'd-m-Y') == 'd-m-Y' ? 'selected' : '' }}>dd-mm-yyyy (31-12-2024)</option>
                                        <option value="m-d-Y" {{ old('date_format') == 'm-d-Y' ? 'selected' : '' }}>mm-dd-yyyy (12-31-2024)</option>
                                        <option value="d/m/Y" {{ old('date_format') == 'd/m/Y' ? 'selected' : '' }}>dd/mm/yyyy (31/12/2024)</option>
                                        <option value="m/d/Y" {{ old('date_format') == 'm/d/Y' ? 'selected' : '' }}>mm/dd/yyyy (12/31/2024)</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="time_format">@lang('Time Format') <span class="text-danger">*</span></label>
                                    <select name="time_format" id="time_format" class="form-control" required>
                                        <option value="12" {{ old('time_format', '12') == '12' ? 'selected' : '' }}>12 Hour (2:30 PM)</option>
                                        <option value="24" {{ old('time_format') == '24' ? 'selected' : '' }}>24 Hour (14:30)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sales_cmsn_agnt">@lang('Sales Commission Agent') <span class="text-danger">*</span></label>
                                    <select name="sales_cmsn_agnt" id="sales_cmsn_agnt" class="form-control" required>
                                        <option value="logged_in_user" {{ old('sales_cmsn_agnt', 'logged_in_user') == 'logged_in_user' ? 'selected' : '' }}>Logged in User</option>
                                        <option value="user" {{ old('sales_cmsn_agnt') == 'user' ? 'selected' : '' }}>Specific User</option>
                                        <option value="percentage" {{ old('sales_cmsn_agnt') == 'percentage' ? 'selected' : '' }}>Percentage Based</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="item_addition_method">@lang('Item Addition Method') <span class="text-danger">*</span></label>
                                    <select name="item_addition_method" id="item_addition_method" class="form-control" required>
                                        <option value="1" {{ old('item_addition_method', '1') == '1' ? 'selected' : '' }}>Add to End</option>
                                        <option value="2" {{ old('item_addition_method') == '2' ? 'selected' : '' }}>Add to Beginning</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="stock_expiry_alert_days">@lang('Stock Expiry Alert Days') <span class="text-danger">*</span></label>
                                    <input type="number" name="stock_expiry_alert_days" id="stock_expiry_alert_days" 
                                           class="form-control" value="{{ old('stock_expiry_alert_days', 30) }}" min="0" required
                                           placeholder="Days before expiry to show alert">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Features -->
                    <div class="form-step" id="step-4">
                        <h4 class="step-heading">🚀 Business Features</h4>
                        <p class="text-muted">Select the features you want to enable for your business</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="feature-group">
                                    <h5>Product Management</h5>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="enable_brand" value="1" {{ old('enable_brand', 1) ? 'checked' : '' }}>
                                            @lang('Enable Brand Management')
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="enable_category" value="1" {{ old('enable_category', 1) ? 'checked' : '' }}>
                                            @lang('Enable Category Management')
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="enable_sub_category" value="1" {{ old('enable_sub_category', 1) ? 'checked' : '' }}>
                                            @lang('Enable Sub Category')
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="feature-group">
                                    <h5>System Features</h5>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="enable_price_tax" value="1" {{ old('enable_price_tax', 1) ? 'checked' : '' }}>
                                            @lang('Enable Price & Tax Management')
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="enable_racks" value="1" {{ old('enable_racks') ? 'checked' : '' }}>
                                            @lang('Enable Rack Management')
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="keyboard_shortcuts" value="1" {{ old('keyboard_shortcuts', 1) ? 'checked' : '' }}>
                                            @lang('Enable Keyboard Shortcuts')
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="form-navigation">
                        <button type="button" class="btn btn-secondary" id="prevBtn" style="display: none;">
                            <i class="fa fa-arrow-left"></i> Previous
                        </button>
                        
                        <button type="button" class="btn btn-primary" id="nextBtn">
                            Next <i class="fa fa-arrow-right"></i>
                        </button>
                        
                        <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                            <i class="fa fa-check"></i> Register Business
                        </button>
                        
                        <a href="/business/select" class="btn btn-default">
                            <i class="fa fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Progress Steps */
.progress-steps {
    display: flex;
    justify-content: center;
    margin-bottom: 40px;
    position: relative;
}

.progress-steps::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 25%;
    right: 25%;
    height: 2px;
    background: rgba(255, 255, 255, 0.3);
    z-index: 1;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 2;
    margin: 0 20px;
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-bottom: 8px;
    transition: all 0.3s ease;
}

.step-title {
    color: rgba(255, 255, 255, 0.7);
    font-size: 12px;
    text-align: center;
    transition: all 0.3s ease;
}

.step.active .step-number {
    background: #007bff;
    color: white;
}

.step.active .step-title {
    color: white;
    font-weight: 500;
}

.step.completed .step-number {
    background: #28a745;
    color: white;
}

.step.completed .step-title {
    color: #28a745;
}

/* Form Steps */
.form-step {
    display: none;
    animation: fadeIn 0.3s ease-in-out;
}

.form-step.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateX(20px); }
    to { opacity: 1; transform: translateX(0); }
}

.step-heading {
    color: white !important;
    margin-bottom: 25px;
    padding-bottom: 10px;
    border-bottom: 2px solid rgba(255, 255, 255, 0.3);
    font-size: 18px;
}

/* Feature Groups */
.feature-group {
    background: rgba(255, 255, 255, 0.1);
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.feature-group h5 {
    color: white !important;
    margin-bottom: 15px;
    font-weight: 600;
}

/* Form Navigation */
.form-navigation {
    text-align: center;
    margin-top: 40px;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.3);
}

.form-navigation .btn {
    margin: 0 10px;
    padding: 12px 25px;
    font-weight: 500;
}

/* Basic Styling */
.login-header {
    margin-bottom: 30px;
}

.login-header h3 {
    color: white;
    font-weight: 600;
}

.login-header p {
    color: white;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    color: white !important;
    font-weight: 500;
    font-size: 14px;
}

.form-control {
    padding: 12px;
    font-size: 14px;
    border-radius: 6px;
    border: 1px solid #ddd;
}

.checkbox {
    margin-bottom: 12px;
}

.checkbox label {
    font-weight: normal;
    color: white !important;
    padding-left: 25px;
}

.text-danger {
    color: #ff6b6b !important;
}

.alert {
    margin-bottom: 20px;
}

/* Additional white text styling */
.login-content {
    color: white;
}

.login-content * {
    color: white;
}

/* Override any dark text */
label, .checkbox label, h4, h5, .login-header h3, .login-header p {
    color: white !important;
}

/* Keep form controls readable */
.form-control, .btn {
    color: #333 !important;
    background-color: white !important;
}

/* Ensure dropdown/select fields have black text */
select.form-control, select.form-control option {
    color: #333 !important;
    background-color: white !important;
}

/* Input fields should also have black text */
input.form-control {
    color: #333 !important;
    background-color: white !important;
}

/* Make required asterisks more visible */
.text-danger {
    color: #ff6b6b !important;
    font-weight: bold;
}

/* Button Styling */
.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-success {
    background-color: #28a745;
    border-color: #28a745;
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-default {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    color: #495057 !important;
}

/* Responsive */
@media (max-width: 768px) {
    .progress-steps {
        flex-wrap: wrap;
    }
    
    .step {
        margin: 10px;
    }
    
    .progress-steps::before {
        display: none;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 4;
    
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    // Show/hide steps
    function showStep(step) {
        // Hide all steps
        document.querySelectorAll('.form-step').forEach(s => s.classList.remove('active'));
        document.querySelectorAll('.step').forEach(s => s.classList.remove('active', 'completed'));
        
        // Show current step
        document.getElementById(`step-${step}`).classList.add('active');
        
        // Update progress
        for (let i = 1; i <= totalSteps; i++) {
            const stepEl = document.querySelector(`[data-step="${i}"]`);
            if (i < step) {
                stepEl.classList.add('completed');
            } else if (i === step) {
                stepEl.classList.add('active');
            }
        }
        
        // Update buttons
        prevBtn.style.display = step === 1 ? 'none' : 'inline-block';
        nextBtn.style.display = step === totalSteps ? 'none' : 'inline-block';
        submitBtn.style.display = step === totalSteps ? 'inline-block' : 'none';
    }
    
    // Validate current step
    function validateStep(step) {
        const currentStepEl = document.getElementById(`step-${step}`);
        const requiredFields = currentStepEl.querySelectorAll('[required]');
        
        for (let field of requiredFields) {
            if (!field.value.trim()) {
                field.focus();
                field.style.borderColor = '#dc3545';
                setTimeout(() => {
                    field.style.borderColor = '#ddd';
                }, 3000);
                return false;
            }
        }
        return true;
    }
    
    // Next button click
    nextBtn.addEventListener('click', function() {
        if (validateStep(currentStep)) {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        }
    });
    
    // Previous button click
    prevBtn.addEventListener('click', function() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });
    
    // Allow clicking on step numbers to navigate
    document.querySelectorAll('.step').forEach(step => {
        step.addEventListener('click', function() {
            const stepNum = parseInt(this.dataset.step);
            if (stepNum < currentStep || validateStep(currentStep)) {
                currentStep = stepNum;
                showStep(currentStep);
            }
        });
    });
    
    // Initialize
    showStep(1);
});
</script>
@endsection