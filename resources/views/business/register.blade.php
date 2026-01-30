@extends('layouts.auth')

@section('title', __('Register Business'))

@section('content')
<div class="login-form col-md-12 col-xs-12 right-col-content-register">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="login-content">
                <div class="login-header">
                    <h3 class="text-center">@lang('Register New Business')</h3>
                    <p class="text-center text-muted">Fill in the details to create your business</p>
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

                <form method="POST" action="{{ route('business.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">@lang('Business Name') <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" 
                                       value="{{ old('name') }}" required>
                            </div>
                        </div>
                        
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
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_date">@lang('Business Start Date') <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="start_date" class="form-control" 
                                       value="{{ old('start_date', date('Y-m-d')) }}" required>
                            </div>
                        </div>
                        
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
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="accounting_method">@lang('Accounting Method') <span class="text-danger">*</span></label>
                                <select name="accounting_method" id="accounting_method" class="form-control" required>
                                    <option value="fifo" {{ old('accounting_method', 'fifo') == 'fifo' ? 'selected' : '' }}>FIFO</option>
                                    <option value="lifo" {{ old('accounting_method') == 'lifo' ? 'selected' : '' }}>LIFO</option>
                                    <option value="avco" {{ old('accounting_method') == 'avco' ? 'selected' : '' }}>AVCO</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="transaction_edit_days">@lang('Transaction Edit Days') <span class="text-danger">*</span></label>
                                <input type="number" name="transaction_edit_days" id="transaction_edit_days" 
                                       class="form-control" value="{{ old('transaction_edit_days', 30) }}" min="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_format">@lang('Date Format') <span class="text-danger">*</span></label>
                                <select name="date_format" id="date_format" class="form-control" required>
                                    <option value="d-m-Y" {{ old('date_format', 'd-m-Y') == 'd-m-Y' ? 'selected' : '' }}>dd-mm-yyyy</option>
                                    <option value="m-d-Y" {{ old('date_format') == 'm-d-Y' ? 'selected' : '' }}>mm-dd-yyyy</option>
                                    <option value="d/m/Y" {{ old('date_format') == 'd/m/Y' ? 'selected' : '' }}>dd/mm/yyyy</option>
                                    <option value="m/d/Y" {{ old('date_format') == 'm/d/Y' ? 'selected' : '' }}>mm/dd/yyyy</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="time_format">@lang('Time Format') <span class="text-danger">*</span></label>
                                <select name="time_format" id="time_format" class="form-control" required>
                                    <option value="12" {{ old('time_format', '12') == '12' ? 'selected' : '' }}>12 Hour</option>
                                    <option value="24" {{ old('time_format') == '24' ? 'selected' : '' }}>24 Hour</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="currency_symbol_placement">@lang('Currency Symbol Placement') <span class="text-danger">*</span></label>
                                <select name="currency_symbol_placement" id="currency_symbol_placement" class="form-control" required>
                                    <option value="before" {{ old('currency_symbol_placement', 'before') == 'before' ? 'selected' : '' }}>Before Amount</option>
                                    <option value="after" {{ old('currency_symbol_placement') == 'after' ? 'selected' : '' }}>After Amount</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sales_cmsn_agnt">@lang('Sales Commission Agent') <span class="text-danger">*</span></label>
                                <select name="sales_cmsn_agnt" id="sales_cmsn_agnt" class="form-control" required>
                                    <option value="logged_in_user" {{ old('sales_cmsn_agnt', 'logged_in_user') == 'logged_in_user' ? 'selected' : '' }}>Logged in User</option>
                                    <option value="user" {{ old('sales_cmsn_agnt') == 'user' ? 'selected' : '' }}>User</option>
                                    <option value="percentage" {{ old('sales_cmsn_agnt') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="item_addition_method">@lang('Item Addition Method') <span class="text-danger">*</span></label>
                                <select name="item_addition_method" id="item_addition_method" class="form-control" required>
                                    <option value="1" {{ old('item_addition_method', '1') == '1' ? 'selected' : '' }}>Add to End</option>
                                    <option value="2" {{ old('item_addition_method') == '2' ? 'selected' : '' }}>Add to Beginning</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="stock_expiry_alert_days">@lang('Stock Expiry Alert Days') <span class="text-danger">*</span></label>
                                <input type="number" name="stock_expiry_alert_days" id="stock_expiry_alert_days" 
                                       class="form-control" value="{{ old('stock_expiry_alert_days', 30) }}" min="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h4>@lang('Features')</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="enable_brand" value="1" {{ old('enable_brand', 1) ? 'checked' : '' }}>
                                            @lang('Enable Brand')
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="enable_category" value="1" {{ old('enable_category', 1) ? 'checked' : '' }}>
                                            @lang('Enable Category')
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="enable_sub_category" value="1" {{ old('enable_sub_category', 1) ? 'checked' : '' }}>
                                            @lang('Enable Sub Category')
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="enable_price_tax" value="1" {{ old('enable_price_tax', 1) ? 'checked' : '' }}>
                                            @lang('Enable Price & Tax')
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="enable_racks" value="1" {{ old('enable_racks') ? 'checked' : '' }}>
                                            @lang('Enable Racks')
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
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

                    <div class="form-group text-center mt-4">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fa fa-check"></i> @lang('Register Business')
                        </button>
                        
                        <a href="{{ route('business.select') }}" class="btn btn-default btn-lg ml-2">
                            <i class="fa fa-arrow-left"></i> @lang('Back')
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.login-header {
    margin-bottom: 30px;
}

.login-header h3 {
    color: #333;
    font-weight: 600;
}

.form-group {
    margin-bottom: 20px;
}

.form-control {
    padding: 10px;
    font-size: 14px;
}

.btn-lg {
    padding: 12px 30px;
    font-size: 16px;
    font-weight: 500;
}

.checkbox {
    margin-bottom: 10px;
}

.checkbox label {
    font-weight: normal;
}

.text-danger {
    color: #dc3545;
}

.alert {
    margin-bottom: 20px;
}

h4 {
    color: #333;
    margin-bottom: 15px;
    border-bottom: 1px solid #e0e0e0;
    padding-bottom: 5px;
}
</style>
@endsection