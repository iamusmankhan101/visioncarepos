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
     * Whether the uploaded file contained a recognisable header row.
     */
    protected $header_row_detected = true;

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
            $match_array = $this->__matchHeadersToFields($parsed_array[0], $import_fields);
            $header_row_detected = $this->header_row_detected;

            $business_locations = BusinessLocation::forDropdown($business_id);

            //Pre-select the invoice number column for grouping, grouping by any other column
            //would merge different sales sharing the same value into a single sale.
            $default_group_by = array_search('invoice_no', $match_array);
            $default_group_by = $default_group_by === false ? null : $default_group_by;

            return view('import_sales.preview')->with(compact('parsed_array', 'import_fields', 'file_name', 'business_locations', 'match_array', 'default_group_by', 'header_row_detected'));
        }
    }

    public function __parseData($file_name)
    {
        $array = Excel::toArray([], public_path('uploads/temp/'.$file_name))[0];

        //Drop completely empty rows
        $rows = [];
        foreach ($array as $row) {
            $has_data = false;
            foreach ($row as $cell) {
                if (! is_null($cell) && trim((string) $cell) !== '') {
                    $has_data = true;
                    break;
                }
            }
            if ($has_data) {
                $rows[] = $row;
            }
        }

        if (empty($rows)) {
            $this->header_row_detected = false;

            return [[]];
        }

        //Exported files often begin with a title row (eg. "All sales - <business name>")
        //and some exports carry no header row at all. Locate the row that really
        //holds the column names instead of blindly using the first row.
        $header_index = $this->__detectHeaderRow($rows);

        if ($header_index === null) {
            //No header row: keep every data row and label the columns generically
            //so nothing is lost and the columns can still be mapped by hand.
            $this->header_row_detected = false;

            //Leading title rows are still dropped
            $rows = array_slice($rows, $this->__firstContentRow($rows));

            $column_count = 0;
            foreach ($rows as $row) {
                $column_count = max($column_count, count($row));
            }

            $headers = [];
            for ($i = 0; $i < $column_count; $i++) {
                $headers[$i] = 'Column '.($i + 1);
            }

            return array_merge([$headers], $rows);
        }

        $this->header_row_detected = true;

        //Everything above the header row is title/decoration and is dropped
        return array_merge([$rows[$header_index]], array_slice($rows, $header_index + 1));
    }

    /**
     * Index of the first row that is not a title/decoration row, ie. the first
     * row filling more than one cell.
     *
     * @param  array  $rows
     * @return int
     */
    private function __firstContentRow($rows)
    {
        foreach ($rows as $index => $row) {
            $filled = 0;
            foreach ($row as $cell) {
                if (trim((string) $cell) !== '') {
                    $filled++;
                }
            }
            if ($filled >= 2) {
                return $index;
            }
        }

        return 0;
    }

    /**
     * Find the index of the row holding the column names.
     * Returns null when the file has no header row.
     *
     * @param  array  $rows
     * @return int|null
     */
    private function __detectHeaderRow($rows)
    {
        $aliases = $this->__fieldAliases();
        $limit = min(count($rows), 10);

        for ($i = 0; $i < $limit; $i++) {
            $filled = 0;
            $matches = 0;
            $value_cells = 0;

            foreach ($rows[$i] as $cell) {
                $value = trim((string) $cell);
                if ($value === '') {
                    continue;
                }

                $filled++;

                if ($this->__matchFieldForHeader($value, $aliases) !== null) {
                    $matches++;
                }

                //Amounts, dates, invoice numbers etc. all start with a digit -
                //column names practically never do.
                if (preg_match('/^\d/', $value)) {
                    $value_cells++;
                }
            }

            //Title rows ("All sales - ...") only fill a single cell - keep looking
            if ($filled < 2) {
                continue;
            }

            if ($matches >= 2) {
                return $i;
            }

            //A row of plain text with no values is still a header, even when the
            //column names are custom or translated
            if ($value_cells === 0 && ($matches >= 1 || $filled >= 3)) {
                return $i;
            }

            //First row with real content is data - the file has no header row
            return null;
        }

        return null;
    }

    /**
     * Map each column of the header row to an import field.
     *
     * @param  array  $headers
     * @param  array  $import_fields  [field_key => label]
     * @return array  [column_index => field_key|null]
     */
    private function __matchHeadersToFields($headers, $import_fields)
    {
        $aliases = $this->__fieldAliases();
        $match_array = [];
        $already_matched = [];

        //Pass 1: known column names and their common variations
        foreach ($headers as $key => $value) {
            $match_array[$key] = null;

            $matched = $this->__matchFieldForHeader($value, $aliases);
            if ($matched !== null && isset($import_fields[$matched]) && ! in_array($matched, $already_matched)) {
                $match_array[$key] = $matched;
                $already_matched[] = $matched;
            }
        }

        //Pass 2: fuzzy match the columns left over
        foreach ($headers as $key => $value) {
            if (! empty($match_array[$key])) {
                continue;
            }

            $value = trim((string) $value);
            if ($value === '') {
                continue;
            }

            $match_percentage = [];
            foreach ($import_fields as $k => $v) {
                similar_text(strtolower($value), strtolower($v), $percentage);
                $match_percentage[$k] = $percentage;
            }
            arsort($match_percentage);

            foreach ($match_percentage as $k => $pct) {
                // Only assign a strong match that no other column is using
                if ($pct >= 70 && ! in_array($k, $already_matched)) {
                    $match_array[$key] = $k;
                    $already_matched[] = $k;
                    break;
                }
            }
        }

        return $match_array;
    }

    /**
     * Resolve a single column name to an import field key.
     *
     * @param  string  $header
     * @param  array  $aliases
     * @return string|null
     */
    private function __matchFieldForHeader($header, $aliases)
    {
        $header = $this->__normalizeHeader($header);
        if ($header === '') {
            return null;
        }

        foreach ($aliases as $field => $field_aliases) {
            if (in_array($header, $field_aliases, true)) {
                return $field;
            }
        }

        return null;
    }

    /**
     * Known column names for every import field, including the names used by the
     * sales export and the translated labels.
     *
     * @return array
     */
    private function __fieldAliases()
    {
        $aliases = [
            'invoice_no' => ['invoice no', 'invoice number', 'invoice', 'bill no', 'reference no', 'ref no'],
            'customer_name' => ['customer name', 'customer', 'client name', 'client', 'contact name'],
            'customer_phone_number' => ['customer phone number', 'contact number', 'phone number', 'mobile number', 'mobile', 'phone', 'contact no'],
            'customer_email' => ['customer email', 'email', 'email address'],
            'date' => ['date', 'sale date', 'sell date', 'transaction date', 'invoice date', 'date time'],
            'location' => ['location', 'business location', 'branch', 'store', 'outlet'],
            'product' => ['product', 'product name', 'item', 'item name'],
            'sku' => ['sku', 'product sku', 'item code', 'product code', 'barcode'],
            'quantity' => ['quantity', 'qty'],
            'unit' => ['unit', 'product unit', 'uom'],
            'unit_price' => ['unit price', 'price', 'rate', 'unit cost'],
            'item_tax' => ['item tax', 'tax', 'tax amount', 'line tax'],
            'item_discount' => ['item discount', 'discount', 'discount amount', 'line discount'],
            'item_description' => ['item description', 'line description', 'product description', 'note', 'notes'],
            'order_total' => ['order total', 'total', 'total amount', 'final total', 'grand total', 'net total'],
            'total_paid' => ['total paid', 'paid', 'amount paid', 'paid amount'],
            'payment_method' => ['payment method', 'payment mode', 'payment type', 'method'],
            'payment_status' => ['payment status', 'paid status'],
            'shipping_status' => ['shipping status', 'order status', 'delivery status'],
            'additional_customer_phones' => ['additional customer phones', 'additional customers', 'additional phones', 'related customers'],
            'types_of_service' => ['types of service', 'type of service', 'service type'],
            'service_custom_field1' => ['service custom field 1'],
            'service_custom_field2' => ['service custom field 2'],
            'service_custom_field3' => ['service custom field 3'],
            'service_custom_field4' => ['service custom field 4'],
        ];

        //Also accept the translated labels and the raw field keys
        foreach ($this->__importFields() as $key => $field) {
            if (! empty($field['label'])) {
                $aliases[$key][] = $field['label'];
            }
            $aliases[$key][] = str_replace('_', ' ', $key);
        }

        $normalized = [];
        foreach ($aliases as $key => $values) {
            $normalized[$key] = [];
            foreach ($values as $value) {
                $normalized_value = $this->__normalizeHeader($value);
                if ($normalized_value !== '') {
                    $normalized[$key][] = $normalized_value;
                }
            }
            $normalized[$key] = array_values(array_unique($normalized[$key]));
        }

        return $normalized;
    }

    /**
     * Lowercase a column name and strip everything but letters and digits, so
     * "Invoice No." and "invoice_no" resolve to the same thing.
     *
     * @param  string  $value
     * @return string
     */
    private function __normalizeHeader($value)
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower(trim((string) $value)));
    }

    /**
     * Resolve a location name from the file to a business location id.
     * Falls back to the location selected on the preview screen.
     *
     * @param  string|null  $location_name
     * @param  int  $business_id
     * @param  int  $default_location_id
     * @return int
     */
    private function __resolveLocationId($location_name, $business_id, $default_location_id)
    {
        $location_name = trim((string) $location_name);
        if ($location_name === '') {
            return $default_location_id;
        }

        $location = BusinessLocation::where('business_id', $business_id)
                        ->where(function ($query) use ($location_name) {
                            $query->where('name', $location_name)
                                ->orWhere('location_id', $location_name);
                        })
                        ->first();

        if (empty($location)) {
            \Log::warning('Sales Import: Location not found, using the selected location', [
                'location' => $location_name,
            ]);

            return $default_location_id;
        }

        return $location->id;
    }

    /**
     * Normalize a payment status from the file. Returns null when unrecognised.
     *
     * @param  string|null  $value
     * @return string|null
     */
    private function __resolvePaymentStatus($value)
    {
        $value = $this->__normalizeHeader($value);
        if ($value === '') {
            return null;
        }

        $statuses = [
            'paid' => 'paid',
            'fullypaid' => 'paid',
            'partial' => 'partial',
            'partiallypaid' => 'partial',
            'due' => 'due',
            'unpaid' => 'due',
            'overdue' => 'due',
        ];

        return $statuses[$value] ?? null;
    }

    /**
     * Normalize a shipping/order status from the file. Returns null when unrecognised.
     *
     * @param  string|null  $value
     * @return string|null
     */
    private function __resolveShippingStatus($value)
    {
        $value = $this->__normalizeHeader($value);
        if ($value === '') {
            return null;
        }

        foreach (array_keys($this->transactionUtil->shipping_statuses()) as $status) {
            if ($this->__normalizeHeader($status) === $value) {
                return $status;
            }
        }

        //Also accept the translated labels shown in the sales list
        foreach ($this->transactionUtil->shipping_statuses() as $status => $label) {
            if ($this->__normalizeHeader($label) === $value) {
                return $status;
            }
        }

        \Log::warning('Sales Import: Unknown shipping status', ['status' => $value]);

        return null;
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

                // Skip CSV header rows and summary/total rows from exports
                // Check if invoice_no contains common summary/header text
                $invoice_no_val = strtolower(trim($line_data['invoice_no'] ?? ''));
                $metadata_in_invoice = ['total', 'invoice no', 'sub total', 'grand total',
                    'summary', 'header', 'customer name', 'contact number'];
                $is_metadata = false;
                foreach ($metadata_in_invoice as $keyword) {
                    if ($invoice_no_val === $keyword || str_starts_with($invoice_no_val, $keyword)) {
                        $is_metadata = true;
                        break;
                    }
                }
                if ($is_metadata) {
                    $row_index++;
                    continue;
                }

                // Also check if product name is clearly a CSV column header, not a real product
                $product_name = strtolower(trim($line_data['product'] ?? ''));
                $header_keywords = ['customer name', 'contact number', 'invoice no', 'invoice no.',
                    'date', 'location', 'payment status', 'payment method', 'total amount',
                    'total paid', 'sell due', 'sell return due', 'order status', 'total items'];
                if (in_array($product_name, $header_keywords)) {
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

            //A mapped location column overrides the location selected on the preview screen
            $sale_location_id = $this->__resolveLocationId($first_sell_line['location'] ?? null, $business_id, $location_id);

            $sale_data = [
                'invoice_no' => $first_sell_line['invoice_no'],
                'location_id' => $sale_location_id,
                'status' => 'final',
                'contact_id' => $contact->id,
                'final_total' => ! empty($first_sell_line['order_total']) ? $first_sell_line['order_total'] : $order_total,
                'transaction_date' => $this->__parseImportDate($first_sell_line['date'] ?? null, $now),
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

            $this->transactionUtil->createOrUpdateSellLines($transaction, $sell_lines, $sale_location_id, false, null, [], false);

            //Shipping/order status from the file
            $shipping_status = $this->__resolveShippingStatus($first_sell_line['shipping_status'] ?? null);
            if (! empty($shipping_status)) {
                $transaction->shipping_status = $shipping_status;
                $transaction->save();
            }

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
            $paid_amount = 0;
            if (!empty($first_sell_line['total_paid']) && $first_sell_line['total_paid'] > 0) {
                $paid_amount = $first_sell_line['total_paid'];
            } elseif (empty($first_sell_line['total_paid_mapped'])
                && $this->__resolvePaymentStatus($first_sell_line['payment_status'] ?? null) == 'paid') {
                //No paid amount in the file, but it is marked as paid - record the full amount
                //so the sale does not show up as due.
                $paid_amount = $transaction->final_total;
            }

            if ($paid_amount > 0) {
                //Exports write the label ("Cash"), payments store the key ("cash")
                $payment_method = ! empty($first_sell_line['payment_method'])
                    ? str_replace(' ', '_', strtolower(trim($first_sell_line['payment_method'])))
                    : 'cash';

                $payment_data = [
                    'amount' => $paid_amount,
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
                        $sale_location_id,
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
                            $sale_location_id
                        );
                }
            }

            //Update payment status
            $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);

            $business_details = $this->businessUtil->getDetails($business_id);
            $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

            $business = ['id' => $business_id,
                'accounting_method' => request()->session()->get('business.accounting_method'),
                'location_id' => $sale_location_id,
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
        $payment_status_key = array_search('payment_status', $import_fields);
        $shipping_status_key = array_search('shipping_status', $import_fields);
        $location_key = array_search('location', $import_fields);
        $unit_key = array_search('unit', $import_fields);
        $tos_key = array_search('types_of_service', $import_fields);
        $service_custom_field1_key = array_search('service_custom_field1', $import_fields);
        $service_custom_field2_key = array_search('service_custom_field2', $import_fields);
        $service_custom_field3_key = array_search('service_custom_field3', $import_fields);
        $service_custom_field4_key = array_search('service_custom_field4', $import_fields);
        $additional_customer_phones_key = array_search('additional_customer_phones', $import_fields);

        $row_index = 2;
        foreach ($imported_data as $key => $value) {
            $formatted_array[$key]['invoice_no'] = $invoice_number_key !== false ? ($value[$invoice_number_key] ?? null) : null;
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
            $formatted_array[$key]['payment_status'] = $payment_status_key !== false ? ($value[$payment_status_key] ?? null) : null;
            $formatted_array[$key]['shipping_status'] = $shipping_status_key !== false ? ($value[$shipping_status_key] ?? null) : null;
            $formatted_array[$key]['location'] = $location_key !== false ? ($value[$location_key] ?? null) : null;
            $formatted_array[$key]['total_paid_mapped'] = $total_paid_key !== false;
            $formatted_array[$key]['unit'] = $unit_key !== false ? ($value[$unit_key] ?? null) : null;
            $formatted_array[$key]['types_of_service'] = $tos_key !== false ? ($value[$tos_key] ?? null) : null;
            $formatted_array[$key]['service_custom_field1'] = $service_custom_field1_key !== false ? ($value[$service_custom_field1_key] ?? null) : null;
            $formatted_array[$key]['service_custom_field2'] = $service_custom_field2_key !== false ? ($value[$service_custom_field2_key] ?? null) : null;
            $formatted_array[$key]['service_custom_field3'] = $service_custom_field3_key !== false ? ($value[$service_custom_field3_key] ?? null) : null;
            $formatted_array[$key]['service_custom_field4'] = $service_custom_field4_key !== false ? ($value[$service_custom_field4_key] ?? null) : null;
            $formatted_array[$key]['additional_customer_phones'] = $additional_customer_phones_key !== false ? ($value[$additional_customer_phones_key] ?? null) : null;
            //Build the key used to group rows into a single sale.
            //Rows are grouped by the user selected column, but rows having different invoice
            //numbers are never merged together - otherwise two different sales sharing the same
            //customer/date/location (which is the case for most exports) would collapse into one sale.
            $group_by_value = trim((string) ($value[$group_by] ?? ''));
            $invoice_no_value = trim((string) ($formatted_array[$key]['invoice_no'] ?? ''));

            if ($group_by_value === '' && $invoice_no_value === '') {
                //Nothing to group on, keep the row as its own sale instead of merging all such rows.
                $formatted_array[$key]['group_by'] = 'row::'.$key;
            } else {
                $formatted_array[$key]['group_by'] = 'group::'.$group_by_value.'||invoice::'.$invoice_no_value;
            }

            //check empty - all validations removed, import will proceed with whatever data is provided

            $row_index++;
        }
        $formatted_data = [];
        foreach ($formatted_array as $array) {
            $formatted_data[$array['group_by']][] = $array;
        }

        return array_values($formatted_data);
    }

    private function __importFields()
    {
        $fields = [
            'invoice_no' => ['label' => __('sale.invoice_no')],
            'customer_name' => ['label' => __('sale.customer_name')],
            'customer_phone_number' => ['label' => __('lang_v1.customer_phone_number'), 'instruction' => __('lang_v1.either_cust_email_or_phone_required')],
            'customer_email' => ['label' => __('lang_v1.customer_email'), 'instruction' => __('lang_v1.either_cust_email_or_phone_required')],
            'date' => ['label' => __('sale.sale_date'), 'instruction' => __('lang_v1.date_format_instruction')],
            'location' => ['label' => __('business.business_location'), 'instruction' => 'Optional. Matched on the location name. The location selected above is used when this is empty or unknown.'],
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
            'payment_status' => ['label' => 'Payment Status', 'instruction' => 'Optional. Paid / Partial / Due. Only used when Total Paid is not mapped - a "Paid" row is then recorded as fully paid.'],
            'shipping_status' => ['label' => 'Shipping Status', 'instruction' => 'Optional. Ordered / Packed / Shipped / Delivered / Cancelled.'],
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

    /**
     * Parse a date string from import CSV into a valid Carbon datetime.
     * Handles common formats like M/d/Y, Y-m-d, d/m/Y, d-M-Y, etc.
     * Returns the current datetime if parsing fails or the year is unreasonably low.
     *
     * @param  string|null  $dateString
     * @param  string  $fallback
     * @return string
     */
    private function __parseImportDate($dateString, $fallback)
    {
        if (empty($dateString)) {
            return $fallback;
        }

        $dateString = trim($dateString);

        // Try multiple common date formats
        $formats = [
            'Y-m-d',           // 2024-11-30
            'Y-m-d H:i:s',     // 2024-11-30 12:00:00
            'd/m/Y',           // 30/11/2024
            'd/m/Y H:i:s',     // 30/11/2024 12:00:00
            'm/d/Y',           // 11/30/2024
            'm/d/Y H:i:s',     // 11/30/2024 12:00:00
            'd-M-Y',           // 30-Nov-2024
            'd-M-Y H:i:s',     // 30-Nov-2024 12:00:00
            'd M Y',           // 30 Nov 2024
            'M d, Y',           // Nov 30, 2024
            'd/m/y',           // 30/11/24 (2-digit year)
            'm/d/y',           // 11/30/24
        ];

        foreach ($formats as $format) {
            try {
                $parsed = \Carbon\Carbon::createFromFormat($format, $dateString, 'UTC');
                if ($parsed && $parsed->year >= 2000 && $parsed->year <= 2100) {
                    return $parsed->toDateTimeString();
                }
            } catch (\Exception $e) {
                // Format didn't match, try next one
                continue;
            }
        }

        // Last resort: try Carbon::parse() which handles many formats
        try {
            $parsed = \Carbon\Carbon::parse($dateString, 'UTC');
            if ($parsed && $parsed->year >= 2000 && $parsed->year <= 2100) {
                return $parsed->toDateTimeString();
            }
        } catch (\Exception $e) {
            // ignore
        }

        \Log::warning('Import Sales: Could not parse date, using fallback', [
            'raw_date' => $dateString,
            'fallback' => $fallback,
        ]);

        return $fallback;
    }
}
