@extends('layouts.auth2')
@section('title', config('app.name', 'ultimatePOS'))
@inject('request', 'Illuminate\Http\Request')
@section('content')
<div class="col-md-12 col-sm-12 col-xs-12 right-col tw-pt-20 tw-pb-10 tw-px-5 tw-flex tw-flex-col tw-items-center tw-justify-center">
    <div class="tw-flex tw-flex-col tw-items-center tw-justify-center tw-text-center tw-px-8 tw-py-10 tw-rounded-2xl tw-shadow-2xl" style="backdrop-filter: blur(12px); background-color: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.25); max-width: 600px; width: 100%;">

        <div class="tw-text-6xl tw-mb-4">⚠️</div>

        <h1 class="tw-text-4xl tw-font-extrabold tw-text-white tw-mb-3">
            Hosting Expired
        </h1>

        <p class="tw-text-lg tw-text-white tw-mb-6" style="opacity: 0.9;">
            The hosting plan for <strong>Vision Care POS</strong> has expired.<br>
            Please renew your hosting to restore access.
        </p>

        <div class="tw-rounded-xl tw-px-6 tw-py-4 tw-text-sm tw-text-white" style="background-color: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2);">
            If you are the administrator, please contact your hosting provider to renew your plan.
        </div>
    </div>
</div>

@endsection