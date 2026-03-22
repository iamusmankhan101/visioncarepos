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
<div style="background-color: #f0f8ff; padding: 15px; border-radius: 8px; margin-top: 15px; border: 1px solid #48b2ee;" id="related-customers-section">
    <h5 style="color: #48b2ee; margin-top: 0;">
        <i class="fa fa-users"></i> Related Customers
        @if(auth()->user()->can('customer.create'))
        <button type="button" class="btn btn-xs btn-success pull-right" id="show-add-related-form-btn">
            <i class="fa fa-plus-circle"></i> Add Related Customer
        </button>
        @endif
    </h5>

    {{-- Inline Add Form --}}
    @if(auth()->user()->can('customer.create'))
    <div id="show-add-related-form" style="display:none; background:#fff; padding:15px; border-radius:6px; margin-bottom:15px; border:1px solid #48b2ee;">
        <h6 style="color:#48b2ee; margin-top:0;"><i class="fa fa-user-plus"></i> Add New Related Customer</h6>
        <hr style="margin:8px 0 12px;">
        <div class="row">
            <div class="col-xs-6">
                <div class="form-group">
                    <label>Relationship:</label>
                    <select class="form-control" id="show_related_relationship_type">
                        <option value="">Select Relationship</option>
                        <option value="spouse">Spouse</option>
                        <option value="child">Child</option>
                        <option value="parent">Parent</option>
                        <option value="sibling">Sibling</option>
                        <option value="relative">Other Relative</option>
                        <option value="friend">Friend</option>
                    </select>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="form-group">
                    <label>Name: <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="show_related_first_name" placeholder="Enter customer name">
                </div>
            </div>
            <div class="col-xs-12">
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" class="form-control" id="show_related_email" placeholder="Enter email address">
                </div>
            </div>
        </div>

        <div style="margin-bottom:10px;">
            <label style="font-weight:600;"><i class="fa fa-file-medical"></i> Prescription Source:</label>
            <div style="margin-top:5px;">
                <label class="radio-inline" style="margin-right:15px;">
                    <input type="radio" name="show_related_prescription_source" value="vision_care">
                    <span style="color:#48b2ee;"><i class="fa fa-check-circle"></i> Prescription by Vision Care</span>
                </label>
                <label class="radio-inline">
                    <input type="radio" name="show_related_prescription_source" value="not_vision_care">
                    <span style="color:#666;"><i class="fa fa-times-circle"></i> Not by Vision Care</span>
                </label>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-condensed" style="background:#fff; margin-bottom:10px;">
                <thead style="background-color:#48b2ee; color:white;">
                    <tr>
                        <th>Eye</th><th>Type</th><th>Sph.</th><th>Cyl.</th><th>Axis</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="2" style="vertical-align:middle; font-weight:bold; background:#f8f9fa;"><i class="fa fa-arrow-right" style="color:#48b2ee;"></i> RIGHT</td>
                        <td><strong>Dist.</strong></td>
                        <td><input type="text" class="form-control input-sm" id="show_rc_cf1" placeholder="-2.00"></td>
                        <td><input type="text" class="form-control input-sm" id="show_rc_cf2" placeholder="-1.00"></td>
                        <td><input type="text" class="form-control input-sm" id="show_rc_cf3" placeholder="180"></td>
                    </tr>
                    <tr>
                        <td><strong>Near</strong></td>
                        <td><input type="text" class="form-control input-sm" id="show_rc_cf4" placeholder="-2.00"></td>
                        <td><input type="text" class="form-control input-sm" id="show_rc_cf5" placeholder="-1.00"></td>
                        <td><input type="text" class="form-control input-sm" id="show_rc_cf6" placeholder="180"></td>
                    </tr>
                    <tr>
                        <td rowspan="2" style="vertical-align:middle; font-weight:bold; background:#f8f9fa;"><i class="fa fa-arrow-left" style="color:#48b2ee;"></i> LEFT</td>
                        <td><strong>Dist.</strong></td>
                        <td><input type="text" class="form-control input-sm" id="show_rc_cf7" placeholder="-2.00"></td>
                        <td><input type="text" class="form-control input-sm" id="show_rc_cf8" placeholder="-1.00"></td>
                        <td><input type="text" class="form-control input-sm" id="show_rc_cf9" placeholder="180"></td>
                    </tr>
                    <tr>
                        <td><strong>Near</strong></td>
                        <td><input type="text" class="form-control input-sm" id="show_rc_cf10" placeholder="-2.00"></td>
                        <td><input type="text" class="form-control input-sm" id="show_rc_scf1" placeholder="-1.00"></td>
                        <td><input type="text" class="form-control input-sm" id="show_rc_scf2" placeholder="180"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-default btn-sm" id="cancel-add-related-form-btn">
                <i class="fa fa-times"></i> Cancel
            </button>
            <button type="button" class="btn btn-primary btn-sm" id="save-show-related-customer"
                    data-contact-id="{{ $contact->id }}">
                <i class="fa fa-save"></i> Save Related Customer
            </button>
        </div>
    </div>
    @endif

    {{-- Existing related customers list --}}
    <div id="related-customers-list">
        @if(!empty($related_customers))
            @foreach($related_customers as $related)
                <div style="background-color:#fff; padding:10px; border-radius:5px; margin-bottom:10px; border-left:3px solid #48b2ee; position:relative;">
                    <strong>{{ $related['name'] }}</strong>
                    <span class="label label-info" style="margin-left:5px;">{{ ucfirst($related['relationship_type']) }}</span>
                    <a href="{{ action([\App\Http\Controllers\ContactController::class, 'show'], [$related['id']]) }}" class="btn btn-xs btn-default pull-right" title="View Full Details">
                        <i class="fa fa-eye"></i>
                    </a>
                    <br>
                    <small class="text-muted">Contact ID: {{ $related['contact_id'] }}</small>
                    @if(!empty($related['prescription']))
                        <div style="margin-top:8px; border-top:1px solid #f1f1f1; padding-top:5px;">
                            <div class="row">
                                <div class="col-xs-6" style="padding-right:5px;">
                                    <small style="font-size:11px;"><strong>R:</strong>
                                    {{ $related['prescription']['right_eye']['distance']['sph'] ?? '-' }}/{{ $related['prescription']['right_eye']['distance']['cyl'] ?? '-' }}x{{ $related['prescription']['right_eye']['distance']['axis'] ?? '-' }}</small>
                                </div>
                                <div class="col-xs-6" style="padding-left:5px;">
                                    <small style="font-size:11px;"><strong>L:</strong>
                                    {{ $related['prescription']['left_eye']['distance']['sph'] ?? '-' }}/{{ $related['prescription']['left_eye']['distance']['cyl'] ?? '-' }}x{{ $related['prescription']['left_eye']['distance']['axis'] ?? '-' }}</small>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <p class="text-muted" id="no-related-customers-msg" style="margin:0;">
                <i class="fa fa-info-circle"></i> No related customers yet.
            </p>
        @endif
    </div>
</div>

<script>
$(function() {
    $('#show-add-related-form-btn').on('click', function() {
        $('#show-add-related-form').slideDown();
        $(this).hide();
    });

    $('#cancel-add-related-form-btn').on('click', function() {
        $('#show-add-related-form').slideUp();
        $('#show-add-related-form-btn').show();
    });

    $('#save-show-related-customer').on('click', function() {
        var $btn = $(this);
        var contactId = $btn.data('contact-id');
        var name = $('#show_related_first_name').val().trim();

        if (!name) {
            alert('Please enter a customer name.');
            $('#show_related_first_name').focus();
            return;
        }

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

        var data = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            related_first_name: name,
            related_relationship_type: $('#show_related_relationship_type').val(),
            related_email: $('#show_related_email').val(),
            related_prescription_source: $('input[name="show_related_prescription_source"]:checked').val() || '',
            custom_field1: $('#show_rc_cf1').val(),
            custom_field2: $('#show_rc_cf2').val(),
            custom_field3: $('#show_rc_cf3').val(),
            custom_field4: $('#show_rc_cf4').val(),
            custom_field5: $('#show_rc_cf5').val(),
            custom_field6: $('#show_rc_cf6').val(),
            custom_field7: $('#show_rc_cf7').val(),
            custom_field8: $('#show_rc_cf8').val(),
            custom_field9: $('#show_rc_cf9').val(),
            custom_field10: $('#show_rc_cf10').val(),
            related_shipping_custom_field_1: $('#show_rc_scf1').val(),
            related_shipping_custom_field_2: $('#show_rc_scf2').val(),
        };

        $.ajax({
            url: '/contacts/' + contactId + '/store-related-customer',
            method: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.msg || 'Related customer added successfully');
                    }

                    // Append new card to list
                    var relName = response.data.name;
                    var relId = response.data.id;
                    var relContactId = response.data.contact_id;
                    var relType = $('#show_related_relationship_type').val() || 'relative';

                    var card = '<div style="background-color:#fff; padding:10px; border-radius:5px; margin-bottom:10px; border-left:3px solid #48b2ee; position:relative;">' +
                        '<strong>' + relName + '</strong>' +
                        '<span class="label label-info" style="margin-left:5px;">' + relType.charAt(0).toUpperCase() + relType.slice(1) + '</span>' +
                        '<a href="/contacts/' + relId + '" class="btn btn-xs btn-default pull-right" title="View Full Details"><i class="fa fa-eye"></i></a>' +
                        '<br><small class="text-muted">Contact ID: ' + relContactId + '</small>' +
                        '</div>';

                    $('#no-related-customers-msg').remove();
                    $('#related-customers-list').append(card);

                    // Reset form
                    $('#show_related_first_name').val('');
                    $('#show_related_relationship_type').val('');
                    $('#show_related_email').val('');
                    $('input[name="show_related_prescription_source"]').prop('checked', false);
                    $('#show-add-related-form').find('input[type="text"], input[type="email"]').val('');
                    $('#show-add-related-form').slideUp();
                    $('#show-add-related-form-btn').show();
                } else {
                    alert('Error: ' + (response.msg || 'Unknown error'));
                }
                $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Related Customer');
            },
            error: function(xhr) {
                var msg = 'Failed to save related customer';
                if (xhr.responseJSON && xhr.responseJSON.msg) msg = xhr.responseJSON.msg;
                alert(msg);
                $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Related Customer');
            }
        });
    });
});
</script>