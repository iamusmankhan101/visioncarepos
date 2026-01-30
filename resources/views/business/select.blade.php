@extends('layouts.auth')

@section('title', __('Select Business'))

@section('content')
<div class="login-form col-md-12 col-xs-12 right-col-content-register">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="login-content">
                <div class="login-header">
                    <h3 class="text-center">@lang('Welcome! Select Your Business')</h3>
                    <p class="text-center text-muted">Choose your business to continue to POS system</p>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="login-form-container">
                    @if($available_businesses->count() > 0)
                        <div class="business-selection-section">
                            <form method="POST" action="{{ route('business.switch') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="business_id">@lang('Select Business'):</label>
                                    <select name="business_id" id="business_id" class="form-control" required>
                                        <option value="">@lang('Choose a business...')</option>
                                        @foreach($available_businesses as $business)
                                            <option value="{{ $business->id }}">
                                                {{ $business->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fa fa-sign-in"></i> @lang('Enter Business')
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="text-center my-4">
                            <span class="text-muted">@lang('OR')</span>
                        </div>
                    @endif

                    <div class="business-registration-section">
                        <div class="text-center">
                            <a href="{{ route('business.register') }}" class="btn btn-success btn-block">
                                <i class="fa fa-plus"></i> @lang('Register New Business')
                            </a>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('logout') }}" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                           class="btn btn-link">
                            <i class="fa fa-sign-out"></i> @lang('Logout')
                        </a>
                        
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.business-selection-section, .business-registration-section {
    padding: 20px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    margin-bottom: 20px;
    background-color: #f9f9f9;
}

.login-header {
    margin-bottom: 30px;
}

.login-header h3 {
    color: #333;
    font-weight: 600;
}

.btn-block {
    padding: 12px;
    font-size: 16px;
    font-weight: 500;
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-success {
    background-color: #28a745;
    border-color: #28a745;
}

.form-control {
    padding: 10px;
    font-size: 14px;
}

.alert {
    margin-bottom: 20px;
}
</style>
@endsection