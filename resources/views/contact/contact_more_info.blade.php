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
                    <td>{{ $contact->r_dist_sph ?? '-' }}</td>
                    <td>{{ $contact->r_dist_cyl ?? '-' }}</td>
                    <td>{{ $contact->r_dist_axis ?? '-' }}</td>
                </tr>
                <!-- RIGHT EYE - Near -->
                <tr>
                    <td style="font-weight: 600;">Near</td>
                    <td>{{ $contact->r_near_sph ?? '-' }}</td>
                    <td>{{ $contact->r_near_cyl ?? '-' }}</td>
                    <td>{{ $contact->r_near_axis ?? '-' }}</td>
                </tr>
                <!-- LEFT EYE - Distance -->
                <tr>
                    <td rowspan="2" style="vertical-align: middle; font-weight: bold; background-color: #f8f9fa;">
                        <i class="fa fa-arrow-left" style="color: #48b2ee;"></i> LEFT EYE
                    </td>
                    <td style="font-weight: 600;">Distance</td>
                    <td>{{ $contact->l_dist_sph ?? '-' }}</td>
                    <td>{{ $contact->l_dist_cyl ?? '-' }}</td>
                    <td>{{ $contact->l_dist_axis ?? '-' }}</td>
                </tr>
                <!-- LEFT EYE - Near -->
                <tr>
                    <td style="font-weight: 600;">Near</td>
                    <td>{{ $contact->l_near_sph ?? '-' }}</td>
                    <td colspan="2" style="background-color: #f8f9fa; text-align: center; color: #999;">
                        <small><i class="fa fa-info-circle"></i> L-Near-Cyl and L-Near-Axis not currently tracked</small>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Related Customers section --}}
<div class="rc-section" style="background-color: #f0f8ff; padding: 15px; border-radius: 8px; margin-top: 15px; border: 1px solid #48b2ee;">
    <h5 style="color: #48b2ee; margin-top: 0;">
        <i class="fa fa-users"></i> Related Customers
        @if(auth()->user()->can('customer.create'))
        <button type="button" class="btn btn-xs btn-success pull-right rc-show-form-btn">
            <i class="fa fa-plus-circle"></i> Add Related Customer
        </button>
        @endif
    </h5>

    {{-- Inline Add Form --}}
    @if(auth()->user()->can('customer.create'))
    <div class="rc-add-form" style="display:none; background:#fff; padding:15px; border-radius:6px; margin-bottom:15px; border:1px solid #48b2ee;">
        <h6 style="color:#48b2ee; margin-top:0;"><i class="fa fa-user-plus"></i> Add New Related Customer</h6>
        <hr style="margin:8px 0 12px;">
        <div class="row">
            <div class="col-xs-6">
                <div class="form-group">
                    <label>Relationship:</label>
                    <select class="form-control rc-relationship">
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
                    <input type="text" class="form-control rc-name" placeholder="Enter customer name">
                </div>
            </div>
            <div class="col-xs-12">
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" class="form-control rc-email" placeholder="Enter email address">
                </div>
            </div>
        </div>

        <div style="margin-bottom:10px;">
            <label style="font-weight:600;"><i class="fa fa-file-medical"></i> Prescription Source:</label>
            <div style="margin-top:5px;">
                <label class="radio-inline" style="margin-right:15px;">
                    <input type="radio" class="rc-prx-source" value="vision_care">
                    <span style="color:#48b2ee;"><i class="fa fa-check-circle"></i> Prescription by Vision Care</span>
                </label>
                <label class="radio-inline">
                    <input type="radio" class="rc-prx-source" value="not_vision_care">
                    <span style="color:#666;"><i class="fa fa-times-circle"></i> Not by Vision Care</span>
                </label>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-condensed" style="background:#fff; margin-bottom:10px;">
                <thead style="background-color:#48b2ee; color:white;">
                    <tr><th>Eye</th><th>Type</th><th>Sph.</th><th>Cyl.</th><th>Axis</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="2" style="vertical-align:middle; font-weight:bold; background:#f8f9fa;"><i class="fa fa-arrow-right" style="color:#48b2ee;"></i> RIGHT</td>
                        <td><strong>Dist.</strong></td>
                        <td><input type="text" class="form-control input-sm rc-cf1" placeholder="-2.00"></td>
                        <td><input type="text" class="form-control input-sm rc-cf2" placeholder="-1.00"></td>
                        <td><input type="text" class="form-control input-sm rc-cf3" placeholder="180"></td>
                    </tr>
                    <tr>
                        <td><strong>Near</strong></td>
                        <td><input type="text" class="form-control input-sm rc-cf4" placeholder="-2.00"></td>
                        <td><input type="text" class="form-control input-sm rc-cf5" placeholder="-1.00"></td>
                        <td><input type="text" class="form-control input-sm rc-cf6" placeholder="180"></td>
                    </tr>
                    <tr>
                        <td rowspan="2" style="vertical-align:middle; font-weight:bold; background:#f8f9fa;"><i class="fa fa-arrow-left" style="color:#48b2ee;"></i> LEFT</td>
                        <td><strong>Dist.</strong></td>
                        <td><input type="text" class="form-control input-sm rc-cf7" placeholder="-2.00"></td>
                        <td><input type="text" class="form-control input-sm rc-cf8" placeholder="-1.00"></td>
                        <td><input type="text" class="form-control input-sm rc-cf9" placeholder="180"></td>
                    </tr>
                    <tr>
                        <td><strong>Near</strong></td>
                        <td><input type="text" class="form-control input-sm rc-cf10" placeholder="-2.00"></td>
                        <td><input type="text" class="form-control input-sm rc-scf1" placeholder="-1.00"></td>
                        <td><input type="text" class="form-control input-sm rc-scf2" placeholder="180"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-default btn-sm rc-cancel-btn">
                <i class="fa fa-times"></i> Cancel
            </button>
            <button type="button" class="btn btn-primary btn-sm rc-save-btn" data-contact-id="{{ $contact->id }}">
                <i class="fa fa-save"></i> Save Related Customer
            </button>
        </div>
    </div>
    @endif

    {{-- Existing related customers list --}}
    <div class="rc-list">
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
            <p class="text-muted rc-empty-msg" style="margin:0;">
                <i class="fa fa-info-circle"></i> No related customers yet.
            </p>
        @endif
    </div>
</div>

