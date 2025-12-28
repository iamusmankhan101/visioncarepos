@php
    $custom_labels = json_decode(session('business.custom_labels'), true);
@endphp

{{-- Prescription Display --}}
<div style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
    <h5 style="color: #48b2ee; margin-top: 0;">
        <i class="fa fa-eye"></i> Prescription Details
    </h5>
    
    {{-- RIGHT EYE --}}
    <div style="margin-bottom: 15px;">
        <strong style="color: #333;"><i class="fa fa-arrow-right"></i> RIGHT EYE</strong>
        <table class="table table-condensed table-bordered" style="margin-top: 5px;">
            <tr>
                <th style="width: 20%; background-color: #fff;">Distance</th>
                <td>
                    <strong>Sph:</strong> {{ $contact->custom_field1 ?? '-' }} | 
                    <strong>Cyl:</strong> {{ $contact->custom_field2 ?? '-' }} | 
                    <strong>Axis:</strong> {{ $contact->custom_field3 ?? '-' }}
                </td>
            </tr>
            <tr>
                <th style="background-color: #fff;">Near</th>
                <td>
                    <strong>Sph:</strong> {{ $contact->custom_field4 ?? '-' }} | 
                    <strong>Cyl:</strong> {{ $contact->custom_field5 ?? '-' }} | 
                    <strong>Axis:</strong> {{ $contact->custom_field6 ?? '-' }}
                </td>
            </tr>
        </table>
    </div>
    
    {{-- LEFT EYE --}}
    <div>
        <strong style="color: #333;"><i class="fa fa-arrow-left"></i> LEFT EYE</strong>
        <table class="table table-condensed table-bordered" style="margin-top: 5px;">
            <tr>
                <th style="width: 20%; background-color: #fff;">Distance</th>
                <td>
                    <strong>Sph:</strong> {{ $contact->custom_field7 ?? '-' }} | 
                    <strong>Cyl:</strong> {{ $contact->custom_field8 ?? '-' }} | 
                    <strong>Axis:</strong> {{ $contact->custom_field9 ?? '-' }}
                </td>
            </tr>
            <tr>
                <th style="background-color: #fff;">Near</th>
                <td>
                    <strong>Sph:</strong> {{ $contact->custom_field10 ?? '-' }} | 
                    <strong>Cyl:</strong> {{ !empty($contact->shipping_custom_field_details['shipping_custom_field_1']) ? $contact->shipping_custom_field_details['shipping_custom_field_1'] : '-' }} | 
                    <strong>Axis:</strong> {{ !empty($contact->shipping_custom_field_details['shipping_custom_field_2']) ? $contact->shipping_custom_field_details['shipping_custom_field_2'] : '-' }}
                </td>
            </tr>
        </table>
    </div>
</div>