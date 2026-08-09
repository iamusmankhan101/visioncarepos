<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Contact;
use App\Product;
use App\TaxRate;
use App\Transaction;
use App\TypesOfService;
use App\Unit;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Variation;
use DB;
use Excel;
use Illuminate\Http\Request;

class ImportSalesController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $productUtil;

    protected $businessUtil;

    protected $transactionUtil;

    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(
        ProductUtil $productUtil,
        BusinessUtil $businessUtil,
        TransactionUtil $transactionUtil,
        ModuleUtil $moduleUtil
    ) {
        $this->productUtil = $productUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $imported_sales = Transaction::where('business_id', $business_id)
                            ->where('type', 'sell')
                            ->whereNotNull('import_batch')
                            ->with(['sales_person'])
                            ->select('id', 'import_batch', 'import_time', 'invoice_no', 'created_by')
                            ->orderBy('import_batch', 'desc')
                            ->get();

        $imported_sales_array = [];
        foreach ($imported_sales as $sale) {
            $imported_sales_array[$sale->import_batch]['import_time'] = $sale->import_time;
            $imported_sales_array[$sale->import_batch]['created_by'] = $sale->sales_person->user_full_name;
            $imported_sales_array[$sale->import_batch]['invoices'][] = $sale->invoice_no;
        }

        $import_fields = $this->__importFields();

        return view('import_sales.index')->with(compact('imported_sales_array', 'import_fields'));
    }

    /**
     * Preview imported data and map columns with sale fields
     *
     * @return \Illuminate\Http\Response
     */
    public function preview(Request $request)
    {
        if (! auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        // Set temp directory using multiple methods
        $temp_dir = storage_path('app/temp');
        if (!is_dir($temp_dir)) {
            mkdir($temp_dir, 0755, true);
        }
        ini_set('upload_tmp_dir', $temp_dir);
        ini_set('sys_temp_dir', $temp_dir);
        putenv('TMPDIR=' . $temp_dir);

        $notAllowed = $this->businessUtil->notAllowedInDemo();
        if (! empty($notAllowed)) {
            return $notAllowed;
        }

        $business_id = request()->session()->get('user.business_id');

        if ($request->hasFile('sales')) {
            $file = $request->file('sales');
            $extension = strtolower($file->getClientOriginalExtension());
            if (! in_array($extension, ['xls', 'xlsx', 'csv'])) {
                $output = ['success' => 0, 'msg' => 'Invalid file format. Please upload a .xls, .xlsx, or .csv file.'];
                return redirect()->back()->with('notification', $output);
            }

            $file_name = time().'_'.$file->getClientOriginalName();
            
            $upload_path = public_path('uploads/temp');
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true);
            }
            $file->move($upload_path, $file_name);

            $parsed_array = $this->__parseData($file_name);

            $import_fields = $this->__importFields();
            foreach ($import_fields as $key => $value) {
                $import_fields[$key] = $value['label'];
            }

            //Evaluate highest matching field with the header to pre select from dropdown
            $headers = $parsed_array[0];
            $match_array = [];
            $already_matched = [];
            foreach ($headers as $key => $value) {
                $match_percentage = [];
                foreach ($import_fields as $k => $v) {
                    similar_text($value, $v, $percentage);
                    $match_percentage[$k] = $percentage;
                }
                // Sort descending by match percentage
                arsort($match_percentage);
                $matched = null;
                foreach ($match_percentage as $k => $pct) {
                    // Only assign if >= 50% and not already used by another column
                    if ($pct >= 50 && ! in_array($k, $already_matched)) {
                        $matched = $k;
                        $already_matched[] = $k;
                        break;
                    }
                }
                $match_array[$key] = $matched;
            }

            $business_locations = BusinessLocation::forDropdown($business_id);

            return view('import_sales.preview')->with(compact('parsed_array', 'import_fields', 'file_name', 'business_locations', 'match_array'));
        }
    }

    public function __parseData($file_name)
    {
        $array = Excel::toArray([], public_path('uploads/temp/'.$file_name))[0];

        // Don't filter headers - just use them as-is
        $headers = $array[0];

        //Remove header row
        unset($array[0]);
        $parsed_array[] = $headers;
        
        // Add all data rows, skip completely empty rows
        foreach ($array as $row) {
            // Skip if row is completely empty
            $has_data = false;
            foreach ($row as $cell) {
                if (!empty($cell) && trim($cell) !== '') {
                    $has_data = true;
                    break;
                }
            }
            if ($has_data) {
                $parsed_array[] = $row;
            }
        }

        return $parsed_array;
    }

    /**
     * Import sales to database
     *
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        if (! auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Set temp directory using multiple methods
            $temp_dir = storage_path('app/temp');
            if (!is_dir($temp_dir)) {
                mkdir($temp_dir, 0755, true);
            }
            ini_set('upload_tmp_dir', $temp_dir);
            ini_set('sys_temp_dir', $temp_dir);
            putenv('TMPDIR=' . $temp_dir);
            
            DB::beginTransaction();

            $file_name = $request->input('file_name');
            $import_fields = $request->input('import_fields');
            $group_by = $request->input('group_by');
            $location_id = $request->input('location_id');
            $business_id = $request->session()->get('user.business_id');

            $file_path = public_path('uploads/temp/'.$file_name);
            $parsed_array = $this->__parseData($file_name);
            //Remove header row
            unset($parsed_array[0]);
            $formatted_sales_data = $this->__formatSaleData($parsed_array, $import_fields, $group_by);
            //Set maximum php execution time
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);

            $import_stats = $this->__importSales($formatted_sales_data, $business_id, $location_id);

            DB::commit();

            // Build detailed success message
            $msg = __('lang_v1.sales_imported_successfully');
            if ($import_stats['imported_count'] > 0) {
                $msg .= ' (' . $import_stats['imported_count'] . ' sales imported)';
            }
            if ($import_stats['skipped_count'] > 0) {
                $msg .= ' - ' . $import_stats['skipped_count'] . ' rows skipped. Check logs for details.';
            }
            if ($import_stats['imported_count'] == 0) {
                $msg = 'Import completed but no sales were created. ';
                if ($import_stats['skipped_count'] > 0) {
                    $msg .= $import_stats['skipped_count'] . ' rows were skipped. Common reason: products not found in the system. Check Laravel logs for details.';
                }
            }

            $output = ['success' => 1,
                'msg' => $msg,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => $e->getMessage(),
            ];

            @unlink($file_path);

            return redirect('import-sales')->with('notification', $output);
        }

        @unlink($file_path);

        return redirect('import-sales')->with('status', $output);
    }

    private function __importSales($formated_data, $business_id, $location_id)
    {
        $import_batch = Transaction::where('business_id', $business_id)->max('import_batch');

        if (empty($import_batch)) {
            $import_batch = 1;
        } else {
            $import_batch = $import_batch + 1;
        }

        $now = \Carbon::now()->toDateTimeString();
        $row_index = 2;
        $skipped_rows = [];
        $imported_count = 0;
        
        foreach ($formated_data as $data) {
            $order_total = 0;
            $sell_lines = [];
            foreach ($data as $line_data) {
                // Skip rows that have no product name and no SKU (header-only rows from export)
                if (empty($line_data['sku']) && empty($line_data['product'])) {
                    $row_index++;
                    continue;
                }

                if (! empty($line_data['sku'])) {
                    
                    $variation = Variation::where('sub_sku', $line_data['sku'])
                    ->whereHas('product', function ($query) use ($business_id) {
                        $query->where('business_id', $business_id);
                    })
                    ->with(['product'])
                    ->first();

                    $product = ! empty($variation) ? $variation->product : null;
                } else {
                    $product = Product::where('business_id', $business_id)
                                    ->where('name', $line_data['product'])
                                    ->with(['variations'])
                                    ->first();
                    $variation = ! empty($product) ? $product->variations->first() : null;
                }

                if (empty($variation)) {
                    // Log skipped product
                    $skipped_rows[] = "Row {$row_index}: Product not found - SKU: {$line_data['sku']}, Name: {$line_data['product']}";
                    \Log::warning("Sales Import - Product not found", [
                        'row' => $row_index,
                        'sku' => $line_data['sku'],
                        'product_name' => $line_data['product']
                    ]);
                    continue;
                }

                $tax_id = null;
                $item_tax = 0;
                $line_discount = ! empty($line_data['item_discount']) ? $line_data['item_discount'] : 0;

                // Allow zero-price products (e.g. complimentary items or exported POS sales with no price)
                $unit_price = isset($line_data['unit_price']) ? (float)$line_data['unit_price'] : 0;

                $price_before_tax = $unit_price - $line_discount;
                $price_inc_tax = $price_before_tax;
                if (! empty($line_data['item_tax'])) {
                    $tax = TaxRate::where('business_id', $business_id)
                                ->where('name', $line_data['item_tax'])
                                ->first();

                    if (empty($tax)) {
                        // Skip tax if not found
                        $tax_id = null;
                        $item_tax = 0;
                    } else {
                        $tax_id = $tax->id;
                        $item_tax = $this->transactionUtil->calc_percentage($price_before_tax, $tax->amount);
                        $price_inc_tax = $price_before_tax + $item_tax;
                    }
                }

                //check if date is correct
                if (! empty($line_data['date'])) {
                    try {
                        \Carbon::parse($line_data['date']);
                    } catch (\Exception $e) {
                        // Skip invalid date, will use current date
                    }
                }

                $temp = [
                    'product_id' => $variation->product_id,
                    'variation_id' => $variation->id,
                    'quantity' => $line_data['quantity'] ?? 1,
                    'unit_price' => $unit_price,
                    'unit_price_inc_tax' => $price_inc_tax,
                    'line_discount_type' => 'fixed',
                    'line_discount_amount' => $line_discount,
                    'item_tax' => $item_tax,
                    'tax_id' => $tax_id,
                    'sell_line_note' => $line_data['item_description'] ?? null,
                    'product_unit_id' => $product->unit_id,
                    'enable_stock' => $product->enable_stock,
                    'type' => $product->type,
                    'combo_variations' => $product->type == 'combo' ? $variation->combo_variations : [],
                ];

                $line_quantity = $line_data['quantity'] ?? 1;
                if (! empty($line_data['unit'])) {
                    $unit_name = trim($line_data['unit']);
                    $unit = Unit::where('actual_name', $unit_name)
                                ->orWhere('short_name', $unit_name)
                                ->first();

                    if (empty($unit)) {
                        // Skip unit conversion if not found
                    } else {
                        //Check if sub unit
                        if ($unit->id != $product->unit_id) {
                            $temp['sub_unit_id'] = $unit->id;
                            $temp['base_unit_multiplier'] = $unit->base_unit_multiplier;
                            $line_quantity = ($line_quantity * $unit->base_unit_multiplier);
                        }
                    }
                }
                $order_total += ($temp['unit_price_inc_tax'] * $line_quantity);

                $sell_lines[] = $temp;

                $row_index++;
            }

            $first_sell_line = $data[0];

            // Skip creating a transaction if there are no sell lines AND no order_total to record
            // (this prevents ghost/empty transactions from header or unmapped rows)
            $has_order_total = !empty($first_sell_line['order_total']) && (float)$first_sell_line['order_total'] > 0;
            if (empty($sell_lines) && !$has_order_total) {
                $skipped_rows[] = "Invoice: {$first_sell_line['invoice_no']} - Skipped: no products and no order total found";
                continue;
            }
            //get contact
            $contact = null;
            if (! empty($first_sell_line['customer_phone_number'])) {
                $contact = Contact::where('business_id', $business_id)
                                ->where('mobile', $first_sell_line['customer_phone_number'])
                                ->first();
            } elseif (! empty($first_sell_line['customer_email'])) {
                $contact = Contact::where('business_id', $business_id)
                                ->where('email', $first_sell_line['customer_email'])
                                ->first();
            }
            if (empty($contact)) {
                $customer_name = ! empty($first_sell_line['customer_name']) ? $first_sell_line['customer_name'] : $first_sell_line['customer_phone_number'];
                if (! empty($customer_name) || ! empty($first_sell_line['customer_phone_number']) || ! empty($first_sell_line['customer_email'])) {
                    $contact = Contact::create([
                        'business_id' => $business_id,
                        'type' => 'customer',
                        'name' => $customer_name ?? 'Walk-in Customer',
                        'email' => $first_sell_line['customer_email'],
                        'mobile' => $first_sell_line['customer_phone_number'],
                        'created_by' => auth()->user()->id,
                    ]);
                } else {
                    // Fall back to the default walk-in customer for this business
                    $contact = Contact::where('business_id', $business_id)
                                    ->where('type', 'customer')
                                    ->first();
                    if (empty($contact)) {
                        // Create a default walk-in customer
                        $contact = Contact::create([
                            'business_id' => $business_id,
                            'type' => 'customer',
                            'name' => 'Walk-in Customer',
                            'created_by' => auth()->user()->id,
                        ]);
                    }
                }
            }

            $sale_data = [
                'invoice_no' => $first_sell_line['invoice_no'],
                'location_id' => $location_id,
                'status' => 'final',
                'contact_id' => $contact->id,
                'final_total' => ! empty($first_sell_line['order_total']) ? $first_sell_line['order_total'] : $order_total,
                'transaction_date' => ! empty($first_sell_line['date']) ? $first_sell_line['date'] : $now,
                'discount_amount' => 0,
                'import_batch' => $import_batch,
                'import_time' => $now,
                'commission_agent' => null,
            ];

            $is_types_service_enabled = $this->moduleUtil->isModuleEnabled('types_of_service');
            if ($is_types_service_enabled && ! empty($first_sell_line['types_of_service'])) {
                $types_of_service = TypesOfService::where('business_id', $business_id)
                                                ->where('name', $first_sell_line['types_of_service'])
                                                ->first();

                if (empty($types_of_service)) {
                    // Skip types of service if not found
                } else {
                    $sale_data['types_of_service_id'] = $types_of_service->id;
                    $sale_data['service_custom_field_1'] = ! empty($first_sell_line['service_custom_field1']) ? $first_sell_line['service_custom_field1'] : null;
                    $sale_data['service_custom_field_2'] = ! empty($first_sell_line['service_custom_field2']) ? $first_sell_line['service_custom_field2'] : null;
                    $sale_data['service_custom_field_3'] = ! empty($first_sell_line['service_custom_field3']) ? $first_sell_line['service_custom_field3'] : null;
                    $sale_data['service_custom_field_4'] = ! empty($first_sell_line['service_custom_field4']) ? $first_sell_line['service_custom_field4'] : null;
                }
            }

            $invoice_total = [
                'total_before_tax' => ! empty($first_sell_line['order_total']) ? $first_sell_line['order_total'] : $order_total,
                'tax' => 0,
            ];

            $transaction = $this->transactionUtil->createSellTransaction($business_id, $sale_data, $invoice_total, auth()->user()->id, false);

            $this->transactionUtil->createOrUpdateSellLines($transaction, $sell_lines, $location_id, false, null, [], false);

            // Restore additional customers (comma-separated contact IDs) into additional_notes
            if (!empty($first_sell_line['additional_customer_phones'])) {
                $raw_ids = trim($first_sell_line['additional_customer_phones']);
                $extra_ids = array_filter(explode(',', $raw_ids), 'is_numeric');
                if (!empty($extra_ids)) {
                    $extra_names = [];
                    foreach ($extra_ids as $eid) {
                        $ec = Contact::where('business_id', $business_id)->where('id', (int)$eid)->first();
                        if ($ec) $extra_names[] = $ec->name;
                    }
                    $all_names = array_merge([$contact->name], $extra_names);
                    $notes = 'MULTI_INVOICE_CUSTOMERS:' . implode(',', $extra_ids) . "\n"
                           . 'Multiple Customers: ' . implode(', ', $all_names);
                    $transaction->additional_notes = (!empty($transaction->additional_notes) ? $transaction->additional_notes . "\n" : '') . $notes;
                    $transaction->save();
                }
            }

            // Handle payment if total_paid is provided
            if (!empty($first_sell_line['total_paid']) && $first_sell_line['total_paid'] > 0) {
                $payment_method = $first_sell_line['payment_method'] ?? 'cash';
                
                $payment_data = [
                    'amount' => $first_sell_line['total_paid'],
                    'method' => $payment_method,
                    'paid_on' => !empty($first_sell_line['date']) ? $first_sell_line['date'] : $now,
                    'transaction_id' => $transaction->id,
                    'business_id' => $business_id,
                    'created_by' => auth()->user()->id,
                ];
                
                \App\TransactionPayment::create($payment_data);
            }

            foreach ($sell_lines as $line) {
                if ($line['enable_stock']) {
                    $this->productUtil->decreaseProductQuantity(
                        $line['product_id'],
                        $line['variation_id'],
                        $location_id,
                        $line['quantity']
                    );
                }

                if ($line['type'] == 'combo') {
                    $line_total_quantity = $line['quantity'];
                    if (! empty($line['base_unit_multiplier'])) {
                        $line_total_quantity = $line_total_quantity * $line['base_unit_multiplier'];
                    }

                    //Decrease quantity of combo as well.
                    $combo_details = [];
                    foreach ($line['combo_variations'] as $combo_variation) {
                        $combo_variation_obj = Variation::find($combo_variation['variation_id']);

                        //Multiply both subunit multiplier of child product and parent product to the quantity
                        $combo_variation_quantity = $combo_variation['quantity'];
                        if (! empty($combo_variation['unit_id'])) {
                            $combo_variation_unit = Unit::find($combo_variation['unit_id']);
                            if (! empty($combo_variation_unit->base_unit_multiplier)) {
                                $combo_variation_quantity = $combo_variation_quantity * $combo_variation_unit->base_unit_multiplier;
                            }
                        }

                        $combo_details[] = [
                            'product_id' => $combo_variation_obj->product_id,
                            'variation_id' => $combo_variation['variation_id'],
                            'quantity' => $combo_variation_quantity * $line_total_quantity,
                        ];
                    }

                    $this->productUtil
                        ->decreaseProductQuantityCombo(
                            $combo_details,
                            $location_id
                        );
                }
            }

            //Update payment status
            $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);

            $business_details = $this->businessUtil->getDetails($business_id);
            $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

            $business = ['id' => $business_id,
                'accounting_method' => request()->session()->get('business.accounting_method'),
                'location_id' => $location_id,
                'pos_settings' => $pos_settings,
            ];
            $this->transactionUtil->mapPurchaseSell($business, $transaction->sell_lines, 'purchase');
            
            $imported_count++;
        }
        
        // Log import summary
        \Log::info("Sales Import Summary", [
            'imported_count' => $imported_count,
            'skipped_count' => count($skipped_rows),
            'skipped_details' => $skipped_rows,
            'import_batch' => $import_batch
        ]);
        
        // Return statistics
        return [
            'imported_count' => $imported_count,
            'skipped_count' => count($skipped_rows),
            'skipped_details' => $skipped_rows,
            'import_batch' => $import_batch
        ];
    }

    private function __formatSaleData($imported_data, $import_fields, $group_by)
    {
        $formatted_array = [];
        $invoice_number_key = array_search('invoice_no', $import_fields);
        $customer_name_key = array_search('customer_name', $import_fields);
        $customer_phone_key = array_search('customer_phone_number', $import_fields);
        $customer_email_key = array_search('customer_email', $import_fields);
        $date_key = array_search('date', $import_fields);
        $product_key = array_search('product', $import_fields);
        $sku_key = array_search('sku', $import_fields);
        $quantity_key = array_search('quantity', $import_fields);
        $unit_price_key = array_search('unit_price', $import_fields);
        $item_tax_key = array_search('item_tax', $import_fields);
        $item_discount_key = array_search('item_discount', $import_fields);
        $item_description_key = array_search('item_description', $import_fields);
        $order_total_key = array_search('order_total', $import_fields);
        $total_paid_key = array_search('total_paid', $import_fields);
        $payment_method_key = array_search('payment_method', $import_fields);
        $unit_key = array_search('unit', $import_fields);
        $tos_key = array_search('types_of_service', $import_fields);
        $service_custom_field1_key = array_search('service_custom_field1', $import_fields);
        $service_custom_field2_key = array_search('service_custom_field2', $import_fields);
        $service_custom_field3_key = array_search('service_custom_field3', $import_fields);
        $service_custom_field4_key = array_search('service_custom_field4', $import_fields);
        $additional_customer_phones_key = array_search('additional_customer_phones', $import_fields);

        // These values are common in summary/header rows that should not be imported as sales
        $skip_invoice_values = [
            'total:', 'total', 'invoice no.', 'invoice no', 'invoice_no', '#', 'invoice',
            'customer name', 'customer_name', 'contact number', 'contact no',
        ];

        $row_index = 2;
        foreach ($imported_data as $key => $value) {
            // Detect and skip Total/header summary rows by checking the invoice_no column value
            $raw_invoice = $invoice_number_key !== false ? ($value[$invoice_number_key] ?? null) : null;
            if (!empty($raw_invoice) && in_array(strtolower(trim((string)$raw_invoice)), $skip_invoice_values)) {
                $row_index++;
                continue;
            }

            $formatted_array[$key]['invoice_no'] = $raw_invoice;
            $formatted_array[$key]['customer_name'] = $customer_name_key !== false ? ($value[$customer_name_key] ?? null) : null;
            $formatted_array[$key]['customer_phone_number'] = $customer_phone_key !== false ? ($value[$customer_phone_key] ?? null) : null;
            $formatted_array[$key]['customer_email'] = $customer_email_key !== false ? ($value[$customer_email_key] ?? null) : null;
            $formatted_array[$key]['date'] = $date_key !== false ? ($value[$date_key] ?? null) : null;
            $formatted_array[$key]['product'] = $product_key !== false ? ($value[$product_key] ?? null) : null;
            $formatted_array[$key]['sku'] = $sku_key !== false ? ($value[$sku_key] ?? null) : null;
            $formatted_array[$key]['quantity'] = $quantity_key !== false ? ($value[$quantity_key] ?? null) : null;
            $formatted_array[$key]['unit_price'] = $unit_price_key !== false ? ($value[$unit_price_key] ?? null) : null;
            $formatted_array[$key]['item_tax'] = $item_tax_key !== false ? ($value[$item_tax_key] ?? null) : null;
            $formatted_array[$key]['item_discount'] = $item_discount_key !== false ? ($value[$item_discount_key] ?? null) : null;
            $formatted_array[$key]['item_description'] = $item_description_key !== false ? ($value[$item_description_key] ?? null) : null;
            $formatted_array[$key]['order_total'] = $order_total_key !== false ? ($value[$order_total_key] ?? null) : null;
            $formatted_array[$key]['total_paid'] = $total_paid_key !== false ? ($value[$total_paid_key] ?? null) : null;
            $formatted_array[$key]['payment_method'] = $payment_method_key !== false ? ($value[$payment_method_key] ?? null) : null;
            $formatted_array[$key]['unit'] = $unit_key !== false ? ($value[$unit_key] ?? null) : null;
            $formatted_array[$key]['types_of_service'] = $tos_key !== false ? ($value[$tos_key] ?? null) : null;
            $formatted_array[$key]['service_custom_field1'] = $service_custom_field1_key !== false ? ($value[$service_custom_field1_key] ?? null) : null;
            $formatted_array[$key]['service_custom_field2'] = $service_custom_field2_key !== false ? ($value[$service_custom_field2_key] ?? null) : null;
            $formatted_array[$key]['service_custom_field3'] = $service_custom_field3_key !== false ? ($value[$service_custom_field3_key] ?? null) : null;
            $formatted_array[$key]['service_custom_field4'] = $service_custom_field4_key !== false ? ($value[$service_custom_field4_key] ?? null) : null;
            $formatted_array[$key]['additional_customer_phones'] = $additional_customer_phones_key !== false ? ($value[$additional_customer_phones_key] ?? null) : null;
            $formatted_array[$key]['group_by'] = $value[$group_by] ?? null;

            $row_index++;
        }
        // Determine the grouping key:
        // If invoice_no is mapped, always group by invoice_no (most reliable unique key per sale).
        // Otherwise fall back to whatever column was selected as group_by.
        $use_invoice_as_group = ($invoice_number_key !== false);

        $formatted_data = [];
        foreach ($formatted_array as $array) {
            if ($use_invoice_as_group) {
                $group_val = $array['invoice_no'];
            } else {
                $group_val = $array['group_by'];
            }

            // Skip grouping key values that look like summary/header rows
            if (!empty($group_val) && in_array(strtolower(trim((string)$group_val)), [
                'total:', 'total', 'invoice no.', 'invoice no', 'invoice_no', '#',
                'customer name', 'customer_name', 'contact number', 'contact no',
            ])) {
                continue;
            }
            $formatted_data[$group_val][] = $array;
        }

        return $formatted_data;
    }

    private function __importFields()
    {
        $fields = [
            'invoice_no' => ['label' => __('sale.invoice_no')],
            'customer_name' => ['label' => __('sale.customer_name')],
            'customer_phone_number' => ['label' => __('lang_v1.customer_phone_number'), 'instruction' => __('lang_v1.either_cust_email_or_phone_required')],
            'customer_email' => ['label' => __('lang_v1.customer_email'), 'instruction' => __('lang_v1.either_cust_email_or_phone_required')],
            'date' => ['label' => __('sale.sale_date'), 'instruction' => __('lang_v1.date_format_instruction')],
            'product' => ['label' => __('product.product_name'), 'instruction' => __('lang_v1.either_product_name_or_sku_required')],
            'sku' => ['label' => __('lang_v1.product_sku'), 'instruction' => __('lang_v1.either_product_name_or_sku_required')],
            'quantity' => ['label' => __('lang_v1.quantity'), 'instruction' => __('lang_v1.required')],
            'unit' => ['label' => __('lang_v1.product_unit')],
            'unit_price' => ['label' => __('sale.unit_price')],
            'item_tax' => ['label' => __('lang_v1.item_tax')],
            'item_discount' => ['label' => __('lang_v1.item_discount')],
            'item_description' => ['label' => __('lang_v1.item_description')],
            'order_total' => ['label' => __('lang_v1.order_total')],
            'total_paid' => ['label' => 'Total Paid'],
            'payment_method' => ['label' => 'Payment Method'],
            'additional_customer_phones' => ['label' => 'Additional Customer Phones'],
        ];

        $is_types_service_enabled = $this->moduleUtil->isModuleEnabled('types_of_service');

        if ($is_types_service_enabled) {
            $fields['types_of_service'] = ['label' => __('lang_v1.types_of_service')];
            $fields['service_custom_field1'] = ['label' => __('lang_v1.service_custom_field_1')];
            $fields['service_custom_field2'] = ['label' => __('lang_v1.service_custom_field_2')];
            $fields['service_custom_field3'] = ['label' => __('lang_v1.service_custom_field_3')];
            $fields['service_custom_field4'] = ['label' => __('lang_v1.service_custom_field_4')];
        }

        return $fields;
    }

    /**
     * Deletes all sales from a batch
     *
     * @return \Illuminate\Http\Response
     */
    public function revertSaleImport($batch)
    {
        if (! auth()->user()->can('sell.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            $sales = Transaction::where('business_id', $business_id)
                                ->where('type', 'sell')
                                ->where('import_batch', $batch)
                                ->get();
            //Begin transaction
            DB::beginTransaction();
            foreach ($sales as $sale) {
                $this->transactionUtil->deleteSale($business_id, $sale->id);
            }

            DB::commit();

            $output = ['success' => 1, 'msg' => __('lang_v1.import_reverted_successfully')];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => trans('messages.something_went_wrong'),
            ];
        }

        return redirect('import-sales')->with('status', $output);
    }
}
