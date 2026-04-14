@extends('layouts.app')
@section('title', __('lang_v1.import_contacts'))

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('lang_v1.import_contacts') — Preview</h1>
</section>

<section class="content">
    @component('components.widget', ['class' => 'box-primary'])
        <p class="text-muted">Showing up to 100 rows. Review the data below then click <strong>Confirm Import</strong> to proceed.</p>

        <div style="overflow-x: auto; max-height: 450px;">
            <table class="table table-condensed table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        @foreach($headers as $header)
                            <th>{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $i => $row)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            @foreach($row as $cell)
                                <td>{{ $cell }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="row" style="margin-top: 16px;">
            <div class="col-sm-12">
                {!! Form::open(['url' => action([\App\Http\Controllers\ContactController::class, 'postImportContacts']), 'method' => 'post']) !!}
                    {!! Form::hidden('file_name', $file_name) !!}
                    <a href="{{ route('contacts.import') }}" class="tw-dw-btn tw-dw-btn-outline">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                    &nbsp;
                    <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">
                        <i class="fa fa-check"></i> Confirm Import
                    </button>
                {!! Form::close() !!}
            </div>
        </div>
    @endcomponent
</section>
@endsection
