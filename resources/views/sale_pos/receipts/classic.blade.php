<!-- business information here -->

<script>
// Hide URL from print
window.addEventListener('beforeprint', function() {
    // Hide any elements that might show URLs
    var urlElements = document.querySelectorAll('[href], .url-display');
    urlElements.forEach(function(el) {
        el.style.display = 'none';
    });
});

window.addEventListener('afterprint', function() {
    // Restore elements after printing
    var urlElements = document.querySelectorAll('[href], .url-display');
    urlElements.forEach(function(el) {
        el.style.display = '';
    });
});
</script>

<div class="row" style="color: #000000 !important;">
		<!-- Logo -->
		@if(empty($receipt_details->letter_head))
			@if(!empty($receipt_details->logo))
				<img style="max-height: 120px; width: auto;" src="{{$receipt_details->logo}}" class="img img-responsive center-block">
			@endif

			<!-- Header text -->
			@if(!empty($receipt_details->header_text))
				<div class="col-xs-12">
					{!! $receipt_details->header_text !!}
				</div>
			@endif

			<!-- business information here -->
			<div class="col-xs-12 text-center">
				<h2 class="text-center">
					<!-- Shop & Location Name  -->
					@if(!empty($receipt_details->display_name))
						{{$receipt_details->display_name}}
					@endif
				</h2>

				<!-- Address -->
				<p>
				@if(!empty($receipt_details->address))
						<small class="text-center">
						{!! $receipt_details->address !!}
						</small>
				@endif
				@if(!empty($receipt_details->contact))
					<br/>{!! $receipt_details->contact !!}
				@endif	
				{{-- Website URL removed --}}
				{{-- @if(!empty($receipt_details->contact) && !empty($receipt_details->website))
					, 
				@endif
				@if(!empty($receipt_details->website))
					{{ $receipt_details->website }}
				@endif --}}
				@if(!empty($receipt_details->location_custom_fields))
					<br>{{ $receipt_details->location_custom_fields }}
				@endif
				</p>
				<p>
				@if(!empty($receipt_details->sub_heading_line1))
					{{ $receipt_details->sub_heading_line1 }}
				@endif
				@if(!empty($receipt_details->sub_heading_line2))
					<br>{{ $receipt_details->sub_heading_line2 }}
				@endif
				@if(!empty($receipt_details->sub_heading_line3))
					<br>{{ $receipt_details->sub_heading_line3 }}
				@endif
				@if(!empty($receipt_details->sub_heading_line4))
					<br>{{ $receipt_details->sub_heading_line4 }}
				@endif		
				@if(!empty($receipt_details->sub_heading_line5))
					<br>{{ $receipt_details->sub_heading_line5 }}
				@endif
				</p>
				<p>
				@if(!empty($receipt_details->tax_info1))
					<b>{{ $receipt_details->tax_label1 }}</b> {{ $receipt_details->tax_info1 }}
				@endif

				@if(!empty($receipt_details->tax_info2))
					<b>{{ $receipt_details->tax_label2 }}</b> {{ $receipt_details->tax_info2 }}
				@endif
				</p>
			@endif

		</div>
		@if(!empty($receipt_details->letter_head))
			<div class="col-xs-12 text-center">
				<img style="width: 100%;margin-bottom: 10px;" src="{{$receipt_details->letter_head}}">
			</div>
		@endif
	<div class="col-xs-12">
		<!-- Invoice number, Customer, Date in first row -->
		<p style="width: 100% !important; margin-bottom: 5px;">
			<span style="display: inline-block; margin-right: 20px;">
				@if(!empty($receipt_details->invoice_no_prefix))
					<b>{!! $receipt_details->invoice_no_prefix !!}</b>
				@endif
				<b>{{$receipt_details->invoice_no}}</b>
			</span>
			
			@if(!empty($receipt_details->customer_info))
				<span style="display: inline-block; margin-right: 20px;">
					<b>{{ $receipt_details->customer_label }}</b> 
					@php
						// Extract customer name without mobile
						$customer_info = strip_tags($receipt_details->customer_info);
						$customer_name = preg_replace('/\s*Mobile:.*/', '', $customer_info);
					@endphp
					<span style="margin-left: 10px;">{{ $customer_name }}</span>
				</span>
			@endif
			
			<span style="display: inline-block; float: right;">
				<b>{{$receipt_details->date_label}}</b> {{$receipt_details->invoice_date}}
			</span>
		</p>

		<!-- Mobile number in second row -->
		@if(!empty($receipt_details->customer_info))
			@php
				// Extract mobile number
				$customer_info = strip_tags($receipt_details->customer_info);
				preg_match('/Mobile:\s*(.+)/', $customer_info, $mobile_matches);
				$mobile_number = isset($mobile_matches[1]) ? trim($mobile_matches[1]) : '';
			@endphp
			@if(!empty($mobile_number))
				<p style="width: 100% !important; margin-bottom: 10px; margin-top: 0;">
					<span style="display: inline-block;">
						<b>Mobile:</b> {{ $mobile_number }}
					</span>
				</p>
			@endif
		@endif

		@if(!empty($receipt_details->types_of_service))
			<p style="width: 100% !important">
				<span class="pull-left text-left">
					<strong>{!! $receipt_details->types_of_service_label !!}:</strong>
					{{$receipt_details->types_of_service}}
					<!-- Waiter info -->
					@if(!empty($receipt_details->types_of_service_custom_fields))
						@foreach($receipt_details->types_of_service_custom_fields as $key => $value)
							<br><strong>{{$key}}: </strong> {{$value}}
						@endforeach
					@endif
				</span>
			</p>
		@endif

		<!-- Table information-->
		@if(!empty($receipt_details->table_label) || !empty($receipt_details->table))
			<p style="width: 100% !important">
				<span class="pull-left text-left">
					@if(!empty($receipt_details->table_label))
						<b>{!! $receipt_details->table_label !!}</b>
					@endif
					{{$receipt_details->table}}
				</span>
			</p>
		@endif

		<!-- Additional customer info if needed -->
		@if(!empty($receipt_details->additional_customers))
			<p style="width: 100% !important">
				<span style="font-size: 0.9em; color: #666;">
					<i>(Also for: {{ $receipt_details->additional_customers }})</i>
				</span>
			</p>
		@endif
	</div>
	
	<div class="col-xs-12">
		<p style="width: 100% !important" class="word-wrap">
			<span class="pull-left text-left word-wrap">
				@if(!empty($receipt_details->client_id_label))
					<br/>
					<b>{{ $receipt_details->client_id_label }}</b> {{ $receipt_details->client_id }}
				@endif
				@if(!empty($receipt_details->customer_tax_label))
					<br/>
					<b>{{ $receipt_details->customer_tax_label }}</b> {{ $receipt_details->customer_tax_number }}
				@endif
				@if(!empty($receipt_details->customer_custom_fields))
					<br/>{!! $receipt_details->customer_custom_fields !!}
				@endif
				@if(!empty($receipt_details->sales_person_label))
					<br/>
					<b>{{ $receipt_details->sales_person_label }}</b> {{ $receipt_details->sales_person }}
				@endif
				@if(!empty($receipt_details->commission_agent_label))
					<br/>
					<strong>{{ $receipt_details->commission_agent_label }}</strong> {{ $receipt_details->commission_agent }}
				@endif
				@if(!empty($receipt_details->customer_rp_label))
					<br/>
					<strong>{{ $receipt_details->customer_rp_label }}</strong> {{ $receipt_details->customer_total_rp }}
				@endif
			</span>

			<span class="pull-right text-left">
				@if(!empty($receipt_details->due_date_label))
					<b>{{$receipt_details->due_date_label}}</b> {{$receipt_details->due_date ?? ''}}
					<br>
				@endif

				@if(!empty($receipt_details->brand_label) || !empty($receipt_details->repair_brand))
					<br>
					@if(!empty($receipt_details->brand_label))
						<b>{!! $receipt_details->brand_label !!}</b>
					@endif
					{{$receipt_details->repair_brand}}
		        @endif


		        @if(!empty($receipt_details->device_label) || !empty($receipt_details->repair_device))
					<br>
					@if(!empty($receipt_details->device_label))
						<b>{!! $receipt_details->device_label !!}</b>
					@endif
					{{$receipt_details->repair_device}}
		        @endif

				@if(!empty($receipt_details->model_no_label) || !empty($receipt_details->repair_model_no))
					<br>
					@if(!empty($receipt_details->model_no_label))
						<b>{!! $receipt_details->model_no_label !!}</b>
					@endif
					{{$receipt_details->repair_model_no}}
		        @endif

				@if(!empty($receipt_details->serial_no_label) || !empty($receipt_details->repair_serial_no))
					<br>
					@if(!empty($receipt_details->serial_no_label))
						<b>{!! $receipt_details->serial_no_label !!}</b>
					@endif
					{{$receipt_details->repair_serial_no}}<br>
		        @endif
				@if(!empty($receipt_details->repair_status_label) || !empty($receipt_details->repair_status))
					@if(!empty($receipt_details->repair_status_label))
						<b>{!! $receipt_details->repair_status_label !!}</b>
					@endif
					{{$receipt_details->repair_status}}<br>
		        @endif
		        
		        @if(!empty($receipt_details->repair_warranty_label) || !empty($receipt_details->repair_warranty))
					@if(!empty($receipt_details->repair_warranty_label))
						<b>{!! $receipt_details->repair_warranty_label !!}</b>
					@endif
					{{$receipt_details->repair_warranty}}
					<br>
		        @endif
		        
				<!-- Waiter info -->
				@if(!empty($receipt_details->service_staff_label) || !empty($receipt_details->service_staff))
		        	<br/>
					@if(!empty($receipt_details->service_staff_label))
						<b>{!! $receipt_details->service_staff_label !!}</b>
					@endif
					{{$receipt_details->service_staff}}
		        @endif
		        @if(!empty($receipt_details->shipping_custom_field_1_label))
					<br><strong>{!!$receipt_details->shipping_custom_field_1_label!!} :</strong> {!!$receipt_details->shipping_custom_field_1_value ?? ''!!}
				@endif

				@if(!empty($receipt_details->shipping_custom_field_2_label))
					<br><strong>{!!$receipt_details->shipping_custom_field_2_label!!}:</strong> {!!$receipt_details->shipping_custom_field_2_value ?? ''!!}
				@endif

				@if(!empty($receipt_details->shipping_custom_field_3_label))
					<br><strong>{!!$receipt_details->shipping_custom_field_3_label!!}:</strong> {!!$receipt_details->shipping_custom_field_3_value ?? ''!!}
				@endif

				@if(!empty($receipt_details->shipping_custom_field_4_label))
					<br><strong>{!!$receipt_details->shipping_custom_field_4_label!!}:</strong> {!!$receipt_details->shipping_custom_field_4_value ?? ''!!}
				@endif

				@if(!empty($receipt_details->shipping_custom_field_5_label))
					<br><strong>{!!$receipt_details->shipping_custom_field_2_label!!}:</strong> {!!$receipt_details->shipping_custom_field_5_value ?? ''!!}
				@endif
				{{-- sale order --}}
				@if(!empty($receipt_details->sale_orders_invoice_no))
					<br>
					<strong>@lang('restaurant.order_no'):</strong> {!!$receipt_details->sale_orders_invoice_no ?? ''!!}
				@endif

				@if(!empty($receipt_details->sale_orders_invoice_date))
					<br>
					<strong>@lang('lang_v1.order_dates'):</strong> {!!$receipt_details->sale_orders_invoice_date ?? ''!!}
				@endif

				@if(!empty($receipt_details->sell_custom_field_1_value))
					<br>
					<strong>{{ $receipt_details->sell_custom_field_1_label }}:</strong> {!!$receipt_details->sell_custom_field_1_value ?? ''!!}
				@endif

				@if(!empty($receipt_details->sell_custom_field_2_value))
					<br>
					<strong>{{ $receipt_details->sell_custom_field_2_label }}:</strong> {!!$receipt_details->sell_custom_field_2_value ?? ''!!}
				@endif

				@if(!empty($receipt_details->sell_custom_field_3_value))
					<br>
					<strong>{{ $receipt_details->sell_custom_field_3_label }}:</strong> {!!$receipt_details->sell_custom_field_3_value ?? ''!!}
				@endif

				@if(!empty($receipt_details->sell_custom_field_4_value))
					<br>
					<strong>{{ $receipt_details->sell_custom_field_4_label }}:</strong> {!!$receipt_details->sell_custom_field_4_value ?? ''!!}
				@endif

			</span>
		</p>
	</div>
</div>

<div class="row" style="color: #000000 !important;">
	@includeIf('sale_pos.receipts.partial.common_repair_invoice')
</div>

{{-- Prescription Form - Side by Side Layout --}}
@php
	// Get contact from transaction - use the actual contact relationship
	$contact = null;
	if(!empty($receipt_details->contact_id)) {
		try {
			// First try to find by contact_id field (string like CO0071)
			$contact = \App\Contact::where('contact_id', $receipt_details->contact_id)->first();
			
			// If not found, it might be a database ID, try that
			if(!$contact && is_numeric($receipt_details->contact_id)) {
				$contact = \App\Contact::find($receipt_details->contact_id);
			}
			
			// Last resort: get from transaction
			if(!$contact) {
				$transaction = \App\Transaction::find($receipt_details->transaction_id ?? null);
				if($transaction && $transaction->contact) {
					$contact = $transaction->contact;
				}
			}
		} catch (\Exception $e) {
			\Log::error('Error fetching contact for prescription: ' . $e->getMessage());
			$contact = null;
		}
	}
@endphp

{{-- Main Customer Prescription --}}
<div class="row" style="color: #000000 !important; margin-top: 5px;">
	<div class="col-xs-12">
		@if($contact)
			<h4 style="margin-bottom: 5px; color: #48b2ee; font-size: 14px;">
				<i class="fa fa-eye"></i> Prescription - {{ $contact->name }}
				@if($contact->contact_id)
					(ID: {{ $contact->contact_id }})
				@endif
				@if(!empty($receipt_details->multiple_customers_data))
					<span class="label label-primary" style="margin-left: 10px; font-size: 10px;">Primary</span>
				@endif
			</h4>
		@endif
		<table width="100%" style="border-collapse: collapse;">
			<tr>
				<!-- RIGHT EYE TABLE -->
				<td style="width: 48%; vertical-align: top; padding-right: 10px;">
					<strong style="font-size: 12px;">RIGHT</strong>
					<table class="table table-bordered table-condensed" style="margin-top: 2px; margin-bottom: 0; border: 1px solid #000 !important; border-collapse: collapse !important;">
						<thead>
							<tr style="background-color: #f0f0f0;">
								<th style="width: 25%; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;"></th>
								<th style="width: 25%; text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">Sph.</th>
								<th style="width: 25%; text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">Cyl.</th>
								<th style="width: 25%; text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">Axis.</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="font-weight: 600; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">Distance</td>
								<td style="text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">{{ optional($contact)->custom_field1 }}</td>
								<td style="text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">{{ optional($contact)->custom_field2 }}</td>
								<td style="text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">{{ optional($contact)->custom_field3 }}</td>
							</tr>
							<tr>
								<td style="font-weight: 600; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">Near</td>
								<td style="text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">{{ optional($contact)->custom_field4 }}</td>
								<td style="text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">{{ optional($contact)->custom_field5 }}</td>
								<td style="text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">{{ optional($contact)->custom_field6 }}</td>
							</tr>
						</tbody>
					</table>
				</td>
				
				<!-- LEFT EYE TABLE -->
				<td style="width: 48%; vertical-align: top; padding-left: 10px;">
					<strong style="font-size: 12px;">Left</strong>
					<table class="table table-bordered table-condensed" style="margin-top: 2px; margin-bottom: 0; border: 1px solid #000 !important; border-collapse: collapse !important;">
						<thead>
							<tr style="background-color: #f0f0f0;">
								<th style="width: 25%; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;"></th>
								<th style="width: 25%; text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">Sph.</th>
								<th style="width: 25%; text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">Cyl.</th>
								<th style="width: 25%; text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">Axis.</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="font-weight: 600; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">Distance</td>
								<td style="text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">{{ optional($contact)->custom_field7 }}</td>
								<td style="text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">{{ optional($contact)->custom_field8 }}</td>
								<td style="text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">{{ optional($contact)->custom_field9 }}</td>
							</tr>
							<tr>
								<td style="font-weight: 600; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">Near</td>
								<td style="text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">{{ optional($contact)->custom_field10 }}</td>
								<td style="text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">{{ $contact && !empty($contact->shipping_custom_field_details['shipping_custom_field_1']) ? $contact->shipping_custom_field_details['shipping_custom_field_1'] : '' }}</td>
								<td style="text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">{{ $contact && !empty($contact->shipping_custom_field_details['shipping_custom_field_2']) ? $contact->shipping_custom_field_details['shipping_custom_field_2'] : '' }}</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</table>
	</div>
</div>

{{-- Additional Customers' Prescriptions --}}
@if(!empty($receipt_details->multiple_customers_data))
	@foreach($receipt_details->multiple_customers_data as $additional_customer)
		<div class="row" style="color: #000000 !important; margin-top: 8px; border-top: 1px solid #ddd; padding-top: 5px;">
			<div class="col-xs-12">
				<h4 style="margin-bottom: 5px; color: #48b2ee; font-size: 14px;">
					<i class="fa fa-eye"></i> Prescription - {{ $additional_customer['name'] }}
					@if($additional_customer['contact_id'])
						(ID: {{ $additional_customer['contact_id'] }})
					@endif
				</h4>
				<table width="100%" style="border-collapse: collapse;">
					<tr>
						<!-- RIGHT EYE TABLE -->
						<td style="width: 48%; vertical-align: top; padding-right: 10px;">
							<strong style="font-size: 12px;">RIGHT</strong>
							<table class="table table-bordered table-condensed" style="margin-top: 2px; margin-bottom: 0; border: 1px solid #000 !important; border-collapse: collapse !important;">
								<thead>
									<tr style="background-color: #f0f0f0;">
										<th style="width: 25%; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;"></th>
										<th style="width: 25%; text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">Sph.</th>
										<th style="width: 25%; text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">Cyl.</th>
										<th style="width: 25%; text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">Axis.</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="font-weight: 600; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">Distance</td>
										<td style="text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">{{ $additional_customer['prescription']['right_eye']['distance']['sph'] ?? '' }}</td>
										<td style="text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">{{ $additional_customer['prescription']['right_eye']['distance']['cyl'] ?? '' }}</td>
										<td style="text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">{{ $additional_customer['prescription']['right_eye']['distance']['axis'] ?? '' }}</td>
									</tr>
									<tr>
										<td style="font-weight: 600; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">Near</td>
										<td style="text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">{{ $additional_customer['prescription']['right_eye']['near']['sph'] ?? '' }}</td>
										<td style="text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">{{ $additional_customer['prescription']['right_eye']['near']['cyl'] ?? '' }}</td>
										<td style="text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">{{ $additional_customer['prescription']['right_eye']['near']['axis'] ?? '' }}</td>
									</tr>
								</tbody>
							</table>
						</td>
						
						<!-- LEFT EYE TABLE -->
						<td style="width: 48%; vertical-align: top; padding-left: 10px;">
							<strong style="font-size: 12px;">Left</strong>
							<table class="table table-bordered table-condensed" style="margin-top: 2px; margin-bottom: 0; border: 1px solid #000 !important; border-collapse: collapse !important;">
								<thead>
									<tr style="background-color: #f0f0f0;">
										<th style="width: 25%; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;"></th>
										<th style="width: 25%; text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">Sph.</th>
										<th style="width: 25%; text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">Cyl.</th>
										<th style="width: 25%; text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">Axis.</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="font-weight: 600; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">Distance</td>
										<td style="text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">{{ $additional_customer['prescription']['left_eye']['distance']['sph'] ?? '' }}</td>
										<td style="text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">{{ $additional_customer['prescription']['left_eye']['distance']['cyl'] ?? '' }}</td>
										<td style="text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">{{ $additional_customer['prescription']['left_eye']['distance']['axis'] ?? '' }}</td>
									</tr>
									<tr>
										<td style="font-weight: 600; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">Near</td>
										<td style="text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">{{ $additional_customer['prescription']['left_eye']['near']['sph'] ?? '' }}</td>
										<td style="text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">{{ $additional_customer['prescription']['left_eye']['near']['cyl'] ?? '' }}</td>
										<td style="text-align: center; border: 1px solid #000 !important; padding: 1px; height: 18px; font-size: 10px;">{{ $additional_customer['prescription']['left_eye']['near']['axis'] ?? '' }}</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</table>
			</div>
		</div>
	@endforeach
@endif

<div class="row" style="color: #000000 !important;">
	<div class="col-xs-12">
		<br/>
		@php
			$p_width = 45;
		@endphp
		@if(!empty($receipt_details->item_discount_label))
			@php
				$p_width -= 10;
			@endphp
		@endif
		@if(!empty($receipt_details->discounted_unit_price_label))
			@php
				$p_width -= 10;
			@endphp
		@endif
		<table class="table table-responsive table-slim">
			<thead>
				<tr>
					<th width="{{$p_width}}%">{{$receipt_details->table_product_label}}</th>
					<th class="text-right" width="15%">{{$receipt_details->table_qty_label}}</th>
					<th class="text-right" width="15%">{{$receipt_details->table_unit_price_label}}</th>
					@if(!empty($receipt_details->discounted_unit_price_label))
						<th class="text-right" width="10%">{{$receipt_details->discounted_unit_price_label}}</th>
					@endif
					@if(!empty($receipt_details->item_discount_label))
						<th class="text-right" width="10%">{{$receipt_details->item_discount_label}}</th>
					@endif
					<th class="text-right" width="15%">{{$receipt_details->table_subtotal_label}}</th>
				</tr>
			</thead>
			<tbody>
				@forelse($receipt_details->lines as $line)
					<tr>
						<td>
							@if(!empty($line['image']))
								<img src="{{$line['image']}}" alt="Image" width="50" style="float: left; margin-right: 8px;">
							@endif
                            {{$line['name']}} {{$line['product_variation']}} {{$line['variation']}} 
                            @if(!empty($line['sub_sku'])), {{$line['sub_sku']}} @endif @if(!empty($line['brand'])), {{$line['brand']}} @endif @if(!empty($line['cat_code'])), {{$line['cat_code']}}@endif
                            @if(!empty($line['product_custom_fields'])), {{$line['product_custom_fields']}} @endif
                            @if(!empty($line['product_description']))
                            	<small>
                            		{!!$line['product_description']!!}
                            	</small>
                            @endif 
                            @if(!empty($line['sell_line_note']))
                            <br>
                            <small>
                            	{!!$line['sell_line_note']!!}
                            </small>
                            @endif 
                            @if(!empty($line['lot_number']))<br> {{$line['lot_number_label']}}:  {{$line['lot_number']}} @endif 
                            @if(!empty($line['product_expiry'])), {{$line['product_expiry_label']}}:  {{$line['product_expiry']}} @endif

                            @if(!empty($line['warranty_name'])) <br><small>{{$line['warranty_name']}} </small>@endif @if(!empty($line['warranty_exp_date'])) <small>- {{@format_date($line['warranty_exp_date'])}} </small>@endif
                            @if(!empty($line['warranty_description'])) <small> {{$line['warranty_description'] ?? ''}}</small>@endif

                            @if($receipt_details->show_base_unit_details && $line['quantity'] && $line['base_unit_multiplier'] !== 1)
                            <br><small>
                            	1 {{$line['units']}} = {{$line['base_unit_multiplier']}} {{$line['base_unit_name']}} <br>
                            	{{$line['base_unit_price']}} x {{$line['orig_quantity']}} = {{$line['line_total']}}
                            </small>
                            @endif
                        </td>
						<td class="text-right">
							{{$line['quantity']}} {{$line['units']}} 

							@if($receipt_details->show_base_unit_details && $line['quantity'] && $line['base_unit_multiplier'] !== 1)
                            <br><small>
                            	{{$line['quantity']}} x {{$line['base_unit_multiplier']}} = {{$line['orig_quantity']}} {{$line['base_unit_name']}}
                            </small>
                            @endif
						</td>
						<td class="text-right">{{$line['unit_price_before_discount']}}</td>
						@if(!empty($receipt_details->discounted_unit_price_label))
							<td class="text-right">
								{{$line['unit_price_inc_tax']}} 
							</td>
						@endif
						@if(!empty($receipt_details->item_discount_label))
							<td class="text-right">
								{{$line['total_line_discount'] ?? '0.00'}}

								@if(!empty($line['line_discount_percent']))
								 	({{$line['line_discount_percent']}}%)
								@endif
							</td>
						@endif
						<td class="text-right">{{$line['line_total']}}</td>
					</tr>
					@if(!empty($line['modifiers']))
						@foreach($line['modifiers'] as $modifier)
							<tr>
								<td>
		                            {{$modifier['name']}} {{$modifier['variation']}} 
		                            @if(!empty($modifier['sub_sku'])), {{$modifier['sub_sku']}} @endif @if(!empty($modifier['cat_code'])), {{$modifier['cat_code']}}@endif
		                            @if(!empty($modifier['sell_line_note']))({!!$modifier['sell_line_note']!!}) @endif 
		                        </td>
								<td class="text-right">{{$modifier['quantity']}} {{$modifier['units']}} </td>
								<td class="text-right">{{$modifier['unit_price_inc_tax']}}</td>
								@if(!empty($receipt_details->discounted_unit_price_label))
									<td class="text-right">{{$modifier['unit_price_exc_tax']}}</td>
								@endif
								@if(!empty($receipt_details->item_discount_label))
									<td class="text-right">0.00</td>
								@endif
								<td class="text-right">{{$modifier['line_total']}}</td>
							</tr>
						@endforeach
					@endif
				@empty
					<tr>
						<td colspan="4">&nbsp;</td>
						@if(!empty($receipt_details->discounted_unit_price_label))
    					<td></td>
    					@endif
    					@if(!empty($receipt_details->item_discount_label))
    					<td></td>
    					@endif
					</tr>
				@endforelse
			</tbody>
		</table>
	</div>
</div>

<div class="row" style="color: #000000 !important;">
	<div class="col-md-12"><hr/></div>
	<div class="col-xs-6">

		<table class="table table-slim">

			@if(!empty($receipt_details->payments))
				@foreach($receipt_details->payments as $payment)
					<tr>
						<td>{{$payment['method']}}</td>
						<td class="text-right" >{{$payment['amount']}}</td>
						<td class="text-right">{{$payment['date']}}</td>
					</tr>
				@endforeach
			@endif

			<!-- Total Paid-->
			@if(!empty($receipt_details->total_paid))
				<tr>
					<th>
						{!! $receipt_details->total_paid_label !!}
					</th>
					<td class="text-right">
						{{$receipt_details->total_paid}}
					</td>
				</tr>
			@endif

			<!-- Total Due-->
			@if(!empty($receipt_details->total_due) && !empty($receipt_details->total_due_label))
			<tr>
				<th>
					{!! $receipt_details->total_due_label !!}
				</th>
				<td class="text-right">
					{{$receipt_details->total_due}}
				</td>
			</tr>
			@endif
			@if(!empty($receipt_details->total_previous_due))
			<tr>
				<th>
					{!! $receipt_details->total_previous_due_label !!}
				</th>
				<td class="text-right">
					{{$receipt_details->total_previous_due}}
				</td>
			</tr>
			@endif
			@if(!empty($receipt_details->all_due))
			<tr>
				<th>
					{!! $receipt_details->all_bal_label !!}
				</th>
				<td class="text-right">
					{{$receipt_details->all_due}}
				</td>
			</tr>
			@endif
		</table>
	</div>

	<div class="col-xs-6">
        <div class="table-responsive">
          	<table class="table table-slim">
				<tbody>
					@if(!empty($receipt_details->total_quantity_label))
						<tr>
							<th style="width:70%">
								{!! $receipt_details->total_quantity_label !!}
							</th>
							<td class="text-right">
								{{$receipt_details->total_quantity}}
							</td>
						</tr>
					@endif

					@if(!empty($receipt_details->total_items_label))
						<tr>
							<th style="width:70%">
								{!! $receipt_details->total_items_label !!}
							</th>
							<td class="text-right">
								{{$receipt_details->total_items}}
							</td>
						</tr>
					@endif
					<tr>
						<th style="width:70%">
							{!! $receipt_details->subtotal_label !!}
						</th>
						<td class="text-right">
							{{$receipt_details->subtotal}}
						</td>
					</tr>
					@if(!empty($receipt_details->total_exempt_uf))
					<tr>
						<th style="width:70%">
							@lang('lang_v1.exempt')
						</th>
						<td class="text-right">
							{{$receipt_details->total_exempt}}
						</td>
					</tr>
					@endif
					<!-- Shipping Charges -->
					@if(!empty($receipt_details->shipping_charges))
						<tr>
							<th style="width:70%">
								{!! $receipt_details->shipping_charges_label !!}
							</th>
							<td class="text-right">
								{{$receipt_details->shipping_charges}}
							</td>
						</tr>
					@endif

					@if(!empty($receipt_details->packing_charge))
						<tr>
							<th style="width:70%">
								{!! $receipt_details->packing_charge_label !!}
							</th>
							<td class="text-right">
								{{$receipt_details->packing_charge}}
							</td>
						</tr>
					@endif

					<!-- Show voucher discount -->
					@if( !empty($receipt_details->voucher_discount) && $receipt_details->voucher_discount > 0 )
						@php
							// Get voucher details for display
							$voucher = null;
							if (!empty($receipt_details->voucher_code)) {
								try {
									$voucher = \App\Voucher::where('code', $receipt_details->voucher_code)->first();
								} catch (\Exception $e) {
									// Handle error silently
								}
							}
						@endphp
						<tr>
							<th>
								@if($voucher)
									Voucher: ({{ $voucher->name }} {{ $voucher->discount_value }}{{ $voucher->discount_type === 'percentage' ? '%' : '' }})
								@else
									Voucher: @if(!empty($receipt_details->voucher_code))({{$receipt_details->voucher_code}})@endif
								@endif
							</th>
							<td class="text-right">
								(-) {{$receipt_details->voucher_discount}}
							</td>
						</tr>
					@endif

					<!-- Show regular discount - check if it's different from voucher discount -->
					@if( isset($receipt_details->discount) && $receipt_details->discount > 0 )
						@php
							$showRegularDiscount = true;
							// If voucher is applied, only show regular discount if it's different from voucher discount
							if (!empty($receipt_details->voucher_discount) && $receipt_details->voucher_discount > 0) {
								// Convert both to numbers for comparison (remove currency formatting)
								$voucherAmount = (float) preg_replace('/[^\d\.]/', '', $receipt_details->voucher_discount);
								$discountAmount = (float) preg_replace('/[^\d\.]/', '', $receipt_details->discount);
								
								// If amounts are the same (within small tolerance), don't show regular discount
								if (abs($voucherAmount - $discountAmount) < 0.01) {
									$showRegularDiscount = false;
								}
							}
						@endphp
						
						@if($showRegularDiscount)
							<tr>
								<th>
									{!! $receipt_details->discount_label ?? 'Discount' !!}
								</th>
								<td class="text-right">
									(-) {{$receipt_details->discount}}
								</td>
							</tr>
						@endif
					@endif

					@if( !empty($receipt_details->total_line_discount) )
						<tr>
							<th>
								{!! $receipt_details->line_discount_label !!}
							</th>
							<td class="text-right">
								(-) {{$receipt_details->total_line_discount}}
							</td>
						</tr>
					@endif

					@if( !empty($receipt_details->additional_expenses) )
						@foreach($receipt_details->additional_expenses as $key => $val)
							<tr>
								<td>
									{{$key}}:
								</td>

								<td class="text-right">
									(+) {{$val}}
								</td>
							</tr>
						@endforeach
					@endif

					@if( !empty($receipt_details->reward_point_label) )
						<tr>
							<th>
								{!! $receipt_details->reward_point_label !!}
							</th>

							<td class="text-right">
								(-) {{$receipt_details->reward_point_amount}}
							</td>
						</tr>
					@endif

					<!-- Tax -->
					@if( !empty($receipt_details->tax) )
						<tr>
							<th>
								{!! $receipt_details->tax_label !!}
							</th>
							<td class="text-right">
								(+) {{$receipt_details->tax}}
							</td>
						</tr>
					@endif

					@if( $receipt_details->round_off_amount > 0)
						<tr>
							<th>
								{!! $receipt_details->round_off_label !!}
							</th>
							<td class="text-right">
								{{$receipt_details->round_off}}
							</td>
						</tr>
					@endif

					<!-- Total -->
					<tr>
						<th>
							{!! $receipt_details->total_label !!}
						</th>
						<td class="text-right">
							{{$receipt_details->total}}
							@if(!empty($receipt_details->total_in_words))
								<br>
								<small>({{$receipt_details->total_in_words}})</small>
							@endif
						</td>
					</tr>
				</tbody>
        	</table>
        </div>
    </div>

    <div class="border-bottom col-md-12">
	    @if(empty($receipt_details->hide_price) && !empty($receipt_details->tax_summary_label) )
	        <!-- tax -->
	        @if(!empty($receipt_details->taxes))
	        	<table class="table table-slim table-bordered">
	        		<tr>
	        			<th colspan="2" class="text-center">{{$receipt_details->tax_summary_label}}</th>
	        		</tr>
	        		@foreach($receipt_details->taxes as $key => $val)
	        			<tr>
	        				<td class="text-center"><b>{{$key}}</b></td>
	        				<td class="text-center">{{$val}}</td>
	        			</tr>
	        		@endforeach
	        	</table>
	        @endif
	    @endif
	</div>

	@if(!empty($receipt_details->additional_notes))
	    <div class="col-xs-12">
	    	@php
	    		// Filter out voucher debug information from additional notes
	    		$filtered_notes = $receipt_details->additional_notes;
	    		$filtered_notes = preg_replace('/\s*\|\s*Voucher:\s*[^,]+,\s*Discount:\s*[\d\.]+/', '', $filtered_notes);
	    		$filtered_notes = preg_replace('/^Voucher:\s*[^,]+,\s*Discount:\s*[\d\.]+\s*\|\s*/', '', $filtered_notes);
	    		$filtered_notes = preg_replace('/^Voucher:\s*[^,]+,\s*Discount:\s*[\d\.]+$/', '', $filtered_notes);
	    		$filtered_notes = trim($filtered_notes);
	    	@endphp
	    	@if(!empty($filtered_notes))
	    		<p>{!! nl2br($filtered_notes) !!}</p>
	    	@endif
	    </div>
    @endif
    
</div>
<div class="row" style="color: #000000 !important;">
	<!-- Footer Debug: '{{ $receipt_details->footer_text ?? 'NULL' }}' ({{ empty($receipt_details->footer_text) ? 'empty' : 'not empty' }}) -->
	@if(!empty($receipt_details->footer_text))
	<div class="@if($receipt_details->show_barcode || $receipt_details->show_qr_code) col-xs-8 @else col-xs-12 @endif">
		{!! $receipt_details->footer_text !!}
	</div>
	@endif
	
	{{-- Custom Footer Text - ALWAYS VISIBLE --}}
	<div class="col-xs-12" style="page-break-inside: avoid; margin-top: 15px;">
		<hr style="border-top: 1px solid #000;">
		<div style="padding: 8px; font-size: 9px; text-align: center; background-color: #f0f0f0; border: 1px solid #000;">
			<strong style="font-size: 10px;">TERMS & CONDITIONS</strong><br>
			<strong>• No Order will process without 50% Advance payment.</strong><br>
			<strong>• Orders with 100% Payment will be prioritized.</strong><br>
			<strong>• No refunds, but we can give you a voucher or exchange it within 3 days.</strong>
		</div>
		<hr style="border-top: 1px solid #000;">
	</div>
	@if($receipt_details->show_barcode || $receipt_details->show_qr_code)
		<div class="@if(!empty($receipt_details->footer_text)) col-xs-4 @else col-xs-12 @endif text-center">
			@if($receipt_details->show_barcode)
				{{-- Barcode --}}
				<img class="center-block" src="data:image/png;base64,{{DNS1D::getBarcodePNG($receipt_details->invoice_no, 'C128', 2,30,array(39, 48, 54), true)}}">
			@endif
			
			@if($receipt_details->show_qr_code && !empty($receipt_details->qr_code_text))
				<img class="center-block mt-5" src="data:image/png;base64,{{DNS2D::getBarcodePNG($receipt_details->qr_code_text, 'QRCODE', 3, 3, [39, 48, 54])}}">
			@endif
		</div>
	@endif
</div>

<style>
@media print {
	/* Hide URLs that browsers add automatically */
	@page {
		margin-bottom: 0;
	}
	
	/* Hide any URL footers */
	body::after {
		display: none !important;
	}
	
	/* Hide browser-generated URLs */
	a[href]:after {
		content: none !important;
	}
	
	/* Hide any automatic URL display */
	.url-display {
		display: none !important;
	}
}
</style>