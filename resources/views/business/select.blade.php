@extends('layouts.auth')

@section('title', __('Select Business'))

@section('content')
<div class="tw-min-h-screen tw-bg-gradient-to-br tw-from-blue-900 tw-via-blue-800 tw-to-indigo-900 tw-flex tw-items-center tw-justify-center tw-py-12 tw-px-4 sm:tw-px-6 lg:tw-px-8">
    <div class="tw-max-w-md tw-w-full tw-space-y-8">
        <!-- Vision Care Header -->
        <div class="tw-text-center">
            <h2 class="tw-text-3xl tw-font-bold tw-text-white tw-mb-2">Vision Care POS</h2>
            <p class="tw-text-blue-200 tw-text-lg">Select Your Business</p>
            <p class="tw-text-blue-300 tw-text-sm tw-mt-2">Choose your business to continue to POS system</p>
        </div>

        <!-- Main Content Card -->
        <div class="tw-bg-white tw-rounded-2xl tw-shadow-2xl tw-space-y-8" style="padding: 52px;">
            
            @if(session('success'))
                <div class="tw-bg-green-50 tw-border tw-border-green-200 tw-rounded-lg tw-p-4">
                    <div class="tw-flex">
                        <div class="tw-flex-shrink-0">
                            <svg class="tw-h-5 tw-w-5 tw-text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="tw-ml-3">
                            <p class="tw-text-sm tw-text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="tw-bg-red-50 tw-border tw-border-red-200 tw-rounded-lg tw-p-4">
                    <div class="tw-flex">
                        <div class="tw-flex-shrink-0">
                            <svg class="tw-h-5 tw-w-5 tw-text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="tw-ml-3">
                            <p class="tw-text-sm tw-text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="tw-bg-red-50 tw-border tw-border-red-200 tw-rounded-lg tw-p-4">
                    <div class="tw-flex">
                        <div class="tw-flex-shrink-0">
                            <svg class="tw-h-5 tw-w-5 tw-text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="tw-ml-3">
                            <ul class="tw-text-sm tw-text-red-800 tw-list-disc tw-list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if($available_businesses->count() > 0)
                <!-- Business Selection Section -->
                <div class="tw-space-y-8">
                    <div class="tw-text-center">
                        <h3 class="tw-text-xl tw-font-semibold tw-text-gray-900 tw-mb-2">Your Businesses</h3>
                        <p class="tw-text-gray-600 tw-text-sm">Select a business to access your POS system</p>
                    </div>
                    
                    <form method="POST" action="{{ route('business.switch') }}" class="tw-space-y-6">
                        @csrf
                        <div class="tw-space-y-3">
                            <label for="business_id" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700">
                                Select Business:
                            </label>
                            <select name="business_id" id="business_id" class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-focus:ring-2 tw-focus:ring-blue-500 tw-focus:border-blue-500 tw-transition-colors tw-bg-white tw-text-black tw-text-base" required>
                                <option value="" class="tw-text-gray-500">Choose a business...</option>
                                @foreach($available_businesses as $business)
                                    <option value="{{ $business->id }}" class="tw-text-black">{{ $business->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <button type="submit" class="tw-w-full tw-bg-blue-600 tw-text-white tw-py-3 tw-px-4 tw-rounded-lg tw-font-semibold tw-hover:bg-blue-700 tw-focus:outline-none tw-focus:ring-2 tw-focus:ring-blue-500 tw-focus:ring-offset-2 tw-transition-colors tw-duration-200 tw-flex tw-items-center tw-justify-center">
                            <svg class="tw-w-5 tw-h-5 tw-mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            Enter Business
                        </button>
                    </form>
                </div>

                <!-- Divider -->
                <div class="tw-relative tw-my-12">
                    <div class="tw-absolute tw-inset-0 tw-flex tw-items-center">
                        <div class="tw-w-full tw-border-t tw-border-gray-300"></div>
                    </div>
                    <div class="tw-relative tw-flex tw-justify-center tw-text-sm tw-pt-5">
                        <span class="tw-px-4 tw-bg-white tw-text-gray-500 tw-font-medium">OR</span>
                    </div>
                </div>
            @endif

            <!-- Register New Business Section -->
            <div class="tw-space-y-6">
                <div class="tw-text-center">
                    <h3 class="tw-text-xl tw-font-semibold tw-text-gray-900 tw-mb-2">
                        @if($available_businesses->count() > 0) Create New Business @else Get Started @endif
                    </h3>
                    <p class="tw-text-gray-600 tw-text-sm">
                        @if($available_businesses->count() > 0) 
                            Add another business to your account
                        @else 
                            Create your first business to start using Vision Care POS
                        @endif
                    </p>
                </div>
                
                <div class="tw-flex tw-justify-center">
                    <a href="/business/register" class="tw-bg-blue-600 tw-text-white tw-py-3 tw-px-8 tw-rounded-lg tw-font-semibold tw-hover:bg-blue-700 tw-focus:outline-none tw-focus:ring-2 tw-focus:ring-blue-500 tw-focus:ring-offset-2 tw-transition-colors tw-duration-200 tw-flex tw-items-center tw-justify-center tw-min-w-0">
                        <svg class="tw-w-5 tw-h-5 tw-mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Register New Business
                    </a>
                </div>
            </div>

            <!-- Logout Section -->
            <div class="tw-pt-8 tw-border-t tw-border-gray-200">
                <a href="{{ route('logout') }}" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="tw-w-full tw-text-gray-600 tw-hover:text-gray-800 tw-py-2 tw-px-4 tw-rounded-lg tw-font-medium tw-transition-colors tw-duration-200 tw-flex tw-items-center tw-justify-center">
                    <svg class="tw-w-4 tw-h-4 tw-mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Logout
                </a>
                
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
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