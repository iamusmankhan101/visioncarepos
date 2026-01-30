@extends('layouts.auth')

@section('title', __('Select Business'))

@section('content')
<div class="tw-min-h-screen tw-bg-gradient-to-br tw-from-slate-900 tw-via-blue-900 tw-to-indigo-900 tw-flex tw-items-center tw-justify-center tw-py-8 tw-px-4 sm:tw-px-6 lg:tw-px-8 tw-relative tw-overflow-hidden">
    <!-- Animated Background Elements -->
    <div class="tw-absolute tw-inset-0 tw-overflow-hidden">
        <div class="tw-absolute tw-top-1/4 tw-left-1/4 tw-w-96 tw-h-96 tw-bg-blue-500 tw-rounded-full tw-mix-blend-multiply tw-filter tw-blur-xl tw-opacity-20 tw-animate-pulse"></div>
        <div class="tw-absolute tw-top-3/4 tw-right-1/4 tw-w-96 tw-h-96 tw-bg-purple-500 tw-rounded-full tw-mix-blend-multiply tw-filter tw-blur-xl tw-opacity-20 tw-animate-pulse tw-animation-delay-2000"></div>
        <div class="tw-absolute tw-bottom-1/4 tw-left-1/3 tw-w-96 tw-h-96 tw-bg-indigo-500 tw-rounded-full tw-mix-blend-multiply tw-filter tw-blur-xl tw-opacity-20 tw-animate-pulse tw-animation-delay-4000"></div>
    </div>

    <div class="tw-max-w-lg tw-w-full tw-space-y-8 tw-relative tw-z-10">
        <!-- Vision Care Header -->
        <div class="tw-text-center tw-space-y-4">
            <div class="tw-inline-flex tw-items-center tw-justify-center tw-w-20 tw-h-20 tw-bg-gradient-to-r tw-from-blue-500 tw-to-indigo-600 tw-rounded-2xl tw-shadow-lg tw-mb-4">
                <svg class="tw-w-10 tw-h-10 tw-text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <h1 class="tw-text-4xl tw-font-bold tw-text-white tw-mb-2">Vision Care POS</h1>
            <p class="tw-text-blue-200 tw-text-lg tw-font-medium">Welcome Back</p>
            <p class="tw-text-blue-300 tw-text-sm tw-max-w-md tw-mx-auto">Choose your business to access your point of sale system and manage your operations</p>
        </div>

        <!-- Main Content Card -->
        <div class="tw-bg-white/95 tw-backdrop-blur-sm tw-rounded-3xl tw-shadow-2xl tw-border tw-border-white/20 tw-overflow-hidden">
            <div class="tw-p-8 tw-space-y-8">
                
                @if(session('success'))
                    <div class="tw-bg-emerald-50 tw-border tw-border-emerald-200 tw-rounded-xl tw-p-4 tw-shadow-sm">
                        <div class="tw-flex tw-items-center">
                            <div class="tw-flex-shrink-0">
                                <svg class="tw-h-5 tw-w-5 tw-text-emerald-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="tw-ml-3">
                                <p class="tw-text-sm tw-text-emerald-800 tw-font-medium">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="tw-bg-red-50 tw-border tw-border-red-200 tw-rounded-xl tw-p-4 tw-shadow-sm">
                        <div class="tw-flex tw-items-center">
                            <div class="tw-flex-shrink-0">
                                <svg class="tw-h-5 tw-w-5 tw-text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="tw-ml-3">
                                <p class="tw-text-sm tw-text-red-800 tw-font-medium">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="tw-bg-red-50 tw-border tw-border-red-200 tw-rounded-xl tw-p-4 tw-shadow-sm">
                        <div class="tw-flex tw-items-start">
                            <div class="tw-flex-shrink-0">
                                <svg class="tw-h-5 tw-w-5 tw-text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="tw-ml-3">
                                <ul class="tw-text-sm tw-text-red-800 tw-space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li class="tw-flex tw-items-center">
                                            <span class="tw-w-1 tw-h-1 tw-bg-red-500 tw-rounded-full tw-mr-2"></span>
                                            {{ $error }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                @if($available_businesses->count() > 0)
                    <!-- Business Selection Section -->
                    <div class="tw-space-y-6">
                        <div class="tw-text-center tw-space-y-2">
                            <div class="tw-inline-flex tw-items-center tw-justify-center tw-w-12 tw-h-12 tw-bg-blue-100 tw-rounded-xl tw-mb-3">
                                <svg class="tw-w-6 tw-h-6 tw-text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <h3 class="tw-text-2xl tw-font-bold tw-text-gray-900">Your Businesses</h3>
                            <p class="tw-text-gray-600">Select a business to access your POS system</p>
                        </div>
                        
                        <form method="POST" action="{{ route('business.switch') }}" class="tw-space-y-6">
                            @csrf
                            <div class="tw-space-y-4">
                                <label for="business_id" class="tw-block tw-text-sm tw-font-semibold tw-text-gray-700 tw-mb-3">
                                    Choose Your Business:
                                </label>
                                <div class="tw-relative">
                                    <select name="business_id" id="business_id" class="tw-w-full tw-px-4 tw-py-4 tw-border-2 tw-border-gray-200 tw-rounded-xl tw-focus:ring-4 tw-focus:ring-blue-500/20 tw-focus:border-blue-500 tw-transition-all tw-duration-200 tw-bg-white tw-text-gray-900 tw-text-base tw-font-medium tw-shadow-sm hover:tw-border-gray-300" required>
                                        <option value="" class="tw-text-gray-500">Choose a business...</option>
                                        @foreach($available_businesses as $business)
                                            <option value="{{ $business->id }}" class="tw-text-gray-900 tw-py-2">{{ $business->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="tw-absolute tw-inset-y-0 tw-right-0 tw-flex tw-items-center tw-pr-3 tw-pointer-events-none">
                                        <svg class="tw-w-5 tw-h-5 tw-text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="tw-w-full tw-bg-gradient-to-r tw-from-blue-600 tw-to-indigo-600 tw-text-white tw-py-4 tw-px-6 tw-rounded-xl tw-font-bold tw-text-lg tw-hover:from-blue-700 tw-hover:to-indigo-700 tw-focus:outline-none tw-focus:ring-4 tw-focus:ring-blue-500/30 tw-transform tw-transition-all tw-duration-200 hover:tw-scale-105 tw-shadow-lg hover:tw-shadow-xl tw-flex tw-items-center tw-justify-center tw-space-x-3">
                                <svg class="tw-w-6 tw-h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Enter Business</span>
                            </button>
                        </form>
                    </div>

                    <!-- Elegant Divider -->
                    <div class="tw-relative tw-py-8">
                        <div class="tw-absolute tw-inset-0 tw-flex tw-items-center">
                            <div class="tw-w-full tw-border-t tw-border-gray-200"></div>
                        </div>
                        <div class="tw-relative tw-flex tw-justify-center">
                            <span class="tw-bg-white tw-px-6 tw-py-2 tw-text-sm tw-font-medium tw-text-gray-500 tw-rounded-full tw-border tw-border-gray-200 tw-shadow-sm">OR</span>
                        </div>
                    </div>
                @endif

                <!-- Register New Business Section -->
                <div class="tw-space-y-6">
                    <div class="tw-text-center tw-space-y-2">
                        <div class="tw-inline-flex tw-items-center tw-justify-center tw-w-12 tw-h-12 tw-bg-emerald-100 tw-rounded-xl tw-mb-3">
                            <svg class="tw-w-6 tw-h-6 tw-text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <h3 class="tw-text-2xl tw-font-bold tw-text-gray-900">
                            @if($available_businesses->count() > 0) Create New Business @else Get Started @endif
                        </h3>
                        <p class="tw-text-gray-600 tw-max-w-sm tw-mx-auto">
                            @if($available_businesses->count() > 0) 
                                Expand your operations by adding another business to your account
                            @else 
                                Create your first business and start managing your sales with Vision Care POS
                            @endif
                        </p>
                    </div>
                    
                    <div class="tw-flex tw-justify-center">
                        <a href="/business/register" class="tw-bg-gradient-to-r tw-from-emerald-600 tw-to-teal-600 tw-text-white tw-py-4 tw-px-8 tw-rounded-xl tw-font-bold tw-text-lg tw-hover:from-emerald-700 tw-hover:to-teal-700 tw-focus:outline-none tw-focus:ring-4 tw-focus:ring-emerald-500/30 tw-transform tw-transition-all tw-duration-200 hover:tw-scale-105 tw-shadow-lg hover:tw-shadow-xl tw-flex tw-items-center tw-space-x-3 tw-group">
                            <svg class="tw-w-6 tw-h-6 tw-group-hover:tw-rotate-90 tw-transition-transform tw-duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span>Register New Business</span>
                        </a>
                    </div>
                </div>

                <!-- Logout Section -->
                <div class="tw-pt-8 tw-border-t tw-border-gray-100">
                    <div class="tw-flex tw-justify-center">
                        <a href="{{ route('logout') }}" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                           class="tw-text-gray-500 tw-hover:text-gray-700 tw-py-3 tw-px-6 tw-rounded-lg tw-font-medium tw-transition-colors tw-duration-200 tw-flex tw-items-center tw-space-x-2 tw-group">
                            <svg class="tw-w-4 tw-h-4 tw-group-hover:tw-translate-x-1 tw-transition-transform tw-duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Sign Out</span>
                        </a>
                    </div>
                    
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="tw-text-center tw-text-blue-200/80 tw-text-sm">
            <p>&copy; {{ date('Y') }} Vision Care POS. All rights reserved.</p>
        </div>
    </div>
</div>

<style>
@keyframes pulse {
    0%, 100% { opacity: 0.2; }
    50% { opacity: 0.3; }
}

.tw-animation-delay-2000 {
    animation-delay: 2s;
}

.tw-animation-delay-4000 {
    animation-delay: 4s;
}

.tw-animate-pulse {
    animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
@endsection