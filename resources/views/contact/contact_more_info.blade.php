@php
    $custom_labels = json_decode(session('business.custom_labels'), true);
@endphp

{{-- Prescription Display --}}
<div style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
    <h5 style="color: #48b2ee; margin-top: 0;">
        <i class="fa fa-eye"></i> Prescription Details
    </h5>
    
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