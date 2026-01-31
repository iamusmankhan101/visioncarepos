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
                    <!-- DEBUG SECTION - Remove this after testing -->
                    <div style="background: rgba(255,0,0,0.8); color: white; padding: 15px; margin: 10px 0; border-radius: 5px;">
                        <h4>🔧 DEBUG: Delete Functionality Test</h4>
                        <p><strong>Available Businesses Count:</strong> {{ $available_businesses->count() }}</p>
                        @if($available_businesses->count() > 0)
                            <p><strong>Businesses Found:</strong></p>
                            @foreach($available_businesses as $business)
                                <div style="border: 1px solid white; padding: 8px; margin: 5px 0; border-radius: 3px;">
                                    <strong>{{ $business->name }}</strong> (ID: {{ $business->id }})
                                    <button type="button" 
                                            class="btn btn-danger btn-sm" 
                                            style="margin-left: 10px; padding: 4px 8px;"
                                            onclick="alert('Delete button works for: {{ $business->name }}')">
                                        🗑️ TEST DELETE
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <p style="color: yellow;">⚠️ No businesses found! This is why delete buttons aren't showing.</p>
                        @endif
                    </div>
                    <!-- END DEBUG SECTION -->

                    @if($available_businesses->count() > 0)
                        <div class="business-selection-section">
                            <form method="POST" action="/business/switch">
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

                            <!-- Business Management Section -->
                            <div class="business-management-section mt-4">
                                <h5 class="text-center mb-3">@lang('Manage Your Businesses')</h5>
                                
                                @foreach($available_businesses as $business)
                                    <div class="business-item mb-2 p-3" style="display: flex; justify-content: space-between; align-items: center;">
                                        <div class="business-info">
                                            <strong>{{ $business->name }}</strong>
                                            <small class="d-block text-muted">
                                                Created: {{ $business->created_at->format('M d, Y') }}
                                            </small>
                                        </div>
                                        <div class="business-actions">
                                            <button type="button" 
                                                    class="btn btn-danger btn-sm delete-business-btn" 
                                                    data-business-id="{{ $business->id }}"
                                                    data-business-name="{{ $business->name }}"
                                                    title="Delete Business">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="text-center my-4">
                            <span class="text-muted">@lang('OR')</span>
                        </div>
                    @endif

                    <div class="business-registration-section">
                        <div class="text-center">
                            <a href="/business/register" class="btn btn-success btn-block">
                                <i class="fa fa-plus"></i> @lang('Register New Business')
                            </a>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="/logout" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                           class="btn btn-link">
                            <i class="fa fa-sign-out"></i> @lang('Logout')
                        </a>
                        
                        <form id="logout-form" action="/logout" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteBusinessModal" tabindex="-1" role="dialog" aria-labelledby="deleteBusinessModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteBusinessModalLabel">
                    <i class="fa fa-warning"></i> @lang('Delete Business')
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i>
                    <strong>@lang('Warning!'):</strong> @lang('This action cannot be undone.')
                </div>
                
                <p>@lang('Are you sure you want to delete the business') "<strong id="businessNameToDelete"></strong>"?</p>
                
                <p class="text-muted small">
                    @lang('This will permanently delete:')
                </p>
                <ul class="text-muted small">
                    <li>@lang('All business data and settings')</li>
                    <li>@lang('All products and inventory')</li>
                    <li>@lang('All customers and suppliers')</li>
                    <li>@lang('All sales and purchase records')</li>
                    <li>@lang('All financial data')</li>
                </ul>
                
                <div class="form-group mt-3">
                    <label for="confirmBusinessName">
                        @lang('Type the business name to confirm deletion:')
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="confirmBusinessName" 
                           placeholder="@lang('Enter business name')"
                           autocomplete="off">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> @lang('Cancel')
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                    <i class="fa fa-trash"></i> @lang('Delete Business')
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for delete submission -->
<form id="deleteBusinessForm" method="POST" action="{{ route('business.delete') }}" style="display: none;">
    @csrf
    @method('DELETE')
    <input type="hidden" name="business_id" id="deleteBusinessId">
</form>

<style>
.business-selection-section, .business-registration-section {
    padding: 20px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    margin-bottom: 20px;
    background-color: rgba(255, 255, 255, 0.1);
}

.login-header {
    margin-bottom: 30px;
}

.login-header h3 {
    color: white !important;
    font-weight: 600;
}

.login-header p {
    color: white !important;
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
    color: #333 !important;
    background-color: white !important;
}

/* Ensure dropdown/select fields have black text */
select.form-control, select.form-control option {
    color: #333 !important;
    background-color: white !important;
}

.alert {
    margin-bottom: 20px;
}

/* Make all labels white */
label, .form-group label {
    color: white !important;
    font-weight: 500;
}

/* Make all text white */
.login-content, .login-content p, .text-center, .text-muted {
    color: white !important;
}

/* Keep buttons readable */
.btn, .btn * {
    color: white !important;
}

.btn-link {
    color: rgba(255, 255, 255, 0.8) !important;
}

.btn-link:hover {
    color: white !important;
}

/* Business management styles */
.business-management-section {
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    padding-top: 20px;
}

.business-item {
    background-color: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 6px;
    transition: background-color 0.2s;
}

.business-item:hover {
    background-color: rgba(255, 255, 255, 0.15);
}

.business-info strong {
    color: white !important;
    font-size: 16px;
}

.business-info small {
    color: rgba(255, 255, 255, 0.7) !important;
    font-size: 12px;
}

.delete-business-btn {
    background-color: #dc3545;
    border-color: #dc3545;
    padding: 6px 10px;
    font-size: 12px;
}

.delete-business-btn:hover {
    background-color: #c82333;
    border-color: #bd2130;
}

/* Modal styles */
.modal-content {
    background-color: #fff !important;
    color: #333 !important;
}

.modal-header.bg-danger {
    background-color: #dc3545 !important;
}

.modal-body, .modal-footer {
    color: #333 !important;
}

.modal-body p, .modal-body li, .modal-body label {
    color: #333 !important;
}

.alert-warning {
    background-color: #fff3cd;
    border-color: #ffeaa7;
    color: #856404;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let businessIdToDelete = null;
    let businessNameToDelete = '';
    
    // Handle delete button clicks
    document.querySelectorAll('.delete-business-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            businessIdToDelete = this.getAttribute('data-business-id');
            businessNameToDelete = this.getAttribute('data-business-name');
            
            document.getElementById('businessNameToDelete').textContent = businessNameToDelete;
            document.getElementById('deleteBusinessId').value = businessIdToDelete;
            document.getElementById('confirmBusinessName').value = '';
            document.getElementById('confirmDeleteBtn').disabled = true;
            
            $('#deleteBusinessModal').modal('show');
        });
    });
    
    // Handle business name confirmation
    document.getElementById('confirmBusinessName').addEventListener('input', function() {
        const enteredName = this.value.trim();
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        
        if (enteredName === businessNameToDelete) {
            confirmBtn.disabled = false;
            confirmBtn.classList.remove('btn-secondary');
            confirmBtn.classList.add('btn-danger');
        } else {
            confirmBtn.disabled = true;
            confirmBtn.classList.remove('btn-danger');
            confirmBtn.classList.add('btn-secondary');
        }
    });
    
    // Handle confirm delete
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (!this.disabled) {
            document.getElementById('deleteBusinessForm').submit();
        }
    });
    
    // Reset modal when closed
    $('#deleteBusinessModal').on('hidden.bs.modal', function() {
        document.getElementById('confirmBusinessName').value = '';
        document.getElementById('confirmDeleteBtn').disabled = true;
        businessIdToDelete = null;
        businessNameToDelete = '';
    });
});
</script>
@endsection