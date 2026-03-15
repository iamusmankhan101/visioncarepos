@php
    $custom_labels = json_decode(session('business.custom_labels'), true);
@endphp

{{-- Prescription Display --}}
<div style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
    <h5 style="color: #48b2ee; margin-top: 0;">
        <i class="fa fa-eye"></i> Prescription Details
    </h5>

    @if(!empty($contact->shipping_custom_field_details['prescription_source']))
        <div style="margin-bottom: 15px; background-color: #fff; padding: 10px; border-radius: 4px; border: 1px solid #eee;">
            <strong><i class="fa fa-file-medical"></i> @lang('Prescription Source'):</strong>
            <div style="margin-top: 5px;">
                @if($contact->shipping_custom_field_details['prescription_source'] == 'vision_care')
                    <span style="color: #48b2ee; font-weight: 600;">
                        <i class="fa fa-check-circle"></i> Prescription by Vision Care
                    </span>
                @else
                    <span style="color: #666; font-weight: 600;">
                        <i class="fa fa-times-circle"></i> Prescription not by Vision Care
                    </span>
                @endif
            </div>
        </div>
    @endif
    
    <div class="table-responsive">
        <table class="table table-bordered" style="background-color: #fff; margin-bottom: 0;">
            <thead style="background-color: #48b2ee; color: white;">
                <tr>
                    <th style="width: 15%;">Eye</th>
                    <th style="width: 15%;">Type</th>
                    <th style="width: 23%;">Sph.</th>
                    <th style="width: 23%;">Cyl.</th>
                    <th style="width: 24%;">Axis</th>
                </tr>
            </thead>
            <tbody>
                <!-- RIGHT EYE - Distance -->
                <tr>
                    <td rowspan="2" style="vertical-align: middle; font-weight: bold; background-color: #f8f9fa;">
                        <i class="fa fa-arrow-right" style="color: #48b2ee;"></i> RIGHT EYE
                    </td>
                    <td style="font-weight: 600;">Distance</td>
                    <td>{{ $contact->custom_field1 ?? '-' }}</td>
                    <td>{{ $contact->custom_field2 ?? '-' }}</td>
                    <td>{{ $contact->custom_field3 ?? '-' }}</td>
                </tr>
                <!-- RIGHT EYE - Near -->
                <tr>
                    <td style="font-weight: 600;">Near</td>
                    <td>{{ $contact->custom_field4 ?? '-' }}</td>
                    <td>{{ $contact->custom_field5 ?? '-' }}</td>
                    <td>{{ $contact->custom_field6 ?? '-' }}</td>
                </tr>
                <!-- LEFT EYE - Distance -->
                <tr>
                    <td rowspan="2" style="vertical-align: middle; font-weight: bold; background-color: #f8f9fa;">
                        <i class="fa fa-arrow-left" style="color: #48b2ee;"></i> LEFT EYE
                    </td>
                    <td style="font-weight: 600;">Distance</td>
                    <td>{{ $contact->custom_field7 ?? '-' }}</td>
                    <td>{{ $contact->custom_field8 ?? '-' }}</td>
                    <td>{{ $contact->custom_field9 ?? '-' }}</td>
                </tr>
                <!-- LEFT EYE - Near -->
                <tr>
                    <td style="font-weight: 600;">Near</td>
                    <td>{{ $contact->custom_field10 ?? '-' }}</td>
                    <td>{{ !empty($contact->shipping_custom_field_details['shipping_custom_field_1']) ? $contact->shipping_custom_field_details['shipping_custom_field_1'] : '-' }}</td>
                    <td>{{ !empty($contact->shipping_custom_field_details['shipping_custom_field_2']) ? $contact->shipping_custom_field_details['shipping_custom_field_2'] : '-' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Related Customers section --}}
@if(!empty($related_customers))
    <div style="background-color: #f0f8ff; padding: 15px; border-radius: 8px; margin-top: 15px; border: 1px solid #48b2ee;">
        <h5 style="color: #48b2ee; margin-top: 0;">
            <i class="fa fa-users"></i> Related Customers
        </h5>
        @foreach($related_customers as $related)
            <div style="background-color: #fff; padding: 10px; border-radius: 5px; margin-bottom: 10px; border-left: 3px solid #48b2ee; position: relative;">
                <strong>{{ $related['name'] }}</strong> 
                <span class="label label-info" style="margin-left: 5px;">{{ ucfirst($related['relationship_type']) }}</span>
                <a href="{{ action([\App\Http\Controllers\ContactController::class, 'show'], [$related['id']]) }}" class="btn btn-xs btn-default pull-right" title="View Full Details">
                    <i class="fa fa-eye"></i>
                </a>
                <br>
                <small class="text-muted">Contact ID: {{ $related['contact_id'] }}</small>
                
                @if(!empty($related['prescription']))
                    <div style="margin-top: 8px; border-top: 1px solid #f1f1f1; padding-top: 5px;">
                        <div class="row">
                            <div class="col-xs-6" style="padding-right: 5px;">
                                <small style="font-size: 11px;"><strong>R:</strong> 
                                {{ $related['prescription']['right_eye']['distance']['sph'] ?? '-' }}/{{ $related['prescription']['right_eye']['distance']['cyl'] ?? '-' }}x{{ $related['prescription']['right_eye']['distance']['axis'] ?? '-' }}</small>
                            </div>
                            <div class="col-xs-6" style="padding-left: 5px;">
                                <small style="font-size: 11px;"><strong>L:</strong> 
                                {{ $related['prescription']['left_eye']['distance']['sph'] ?? '-' }}/{{ $related['prescription']['left_eye']['distance']['cyl'] ?? '-' }}x{{ $related['prescription']['left_eye']['distance']['axis'] ?? '-' }}</small>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endif