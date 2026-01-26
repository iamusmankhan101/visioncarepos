<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Charts\CommonChart;
use App\Currency;
use App\Media;
use App\Transaction;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\RestaurantUtil;
use App\Utils\TransactionUtil;
use App\Utils\ProductUtil;
use App\Utils\Util;
use App\VariationLocationDetails;
use Datatables;
use DB;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class HomeController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $businessUtil;

    protected $transactionUtil;

    protected $moduleUtil;

    protected $commonUtil;

    protected $restUtil;
    protected $productUtil;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        BusinessUtil $businessUtil,
        TransactionUtil $transactionUtil,
        ModuleUtil $moduleUtil,
        Util $commonUtil,
        RestaurantUtil $restUtil,
        ProductUtil $productUtil,
    ) {
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
        $this->commonUtil = $commonUtil;
        $this->restUtil = $restUtil;
        $this->productUtil = $productUtil;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        if ($user->user_type == 'user_customer') {
            // CRM module not available, redirect to regular dashboard
            // return redirect()->action([\Modules\Crm\Http\Controllers\DashboardController::class, 'index']);
        }

        $business_id = request()->session()->get('user.business_id');

        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! auth()->user()->can('dashboard.data')) {
            return view('home.index');
        }

        $all_locations = BusinessLocation::forDropdown($business_id)->toArray();
        
        // Set default location if not set in session
        if (!session('user.current_location_id') && !empty($all_locations)) {
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $default_location_id = is_array($permitted_locations) ? $permitted_locations[0] : $permitted_locations;
            } else {
                $default_location_id = array_key_first($all_locations);
            }
            
            if ($default_location_id && isset($all_locations[$default_location_id])) {
                session(['user.current_location_id' => $default_location_id]);
                session(['user.current_location_name' => $all_locations[$default_location_id]]);
            }
        }
        
        $common_settings = ! empty(session('business.common_settings')) ? session('business.common_settings') : [];
        
        // Get Dashboard widgets from module
        $module_widgets = $this->moduleUtil->getModuleData('dashboard_widget');
        $widgets = [];
        foreach ($module_widgets as $widget_array) {
            if (! empty($widget_array['position'])) {
                $widgets[$widget_array['position']][] = $widget_array['widget'];
            }
        }

        // Generate charts
        $sells_chart_1 = null;
        $sells_chart_2 = null;
        
        if (!empty($all_locations)) {
            try {
                // Sales Last 30 Days Chart
                $sells_chart_1 = $this->generateSalesChart($business_id, 30);
                
                // Sales Current Financial Year Chart
                $sells_chart_2 = $this->generateSalesChartFY($business_id);
            } catch (\Exception $e) {
                // If chart generation fails, continue without charts
                \Log::warning('Chart generation failed: ' . $e->getMessage());
            }
        }

        return view('home.index', compact('widgets', 'all_locations', 'common_settings', 'is_admin'))
            ->with('sells_chart_1', $sells_chart_1)
            ->with('sells_chart_2', $sells_chart_2);
    }

    /**
     * Retrieves purchase and sell details for a given time period.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTotals()
    {
        if (request()->ajax()) {
            $start = request()->start;
            $end = request()->end;
            $location_id = request()->location_id;
            
            // If no location_id provided, use current location from session
            if (empty($location_id)) {
                $location_id = session('user.current_location_id');
            }
            
            $business_id = request()->session()->get('user.business_id');

            // get user id parameter
            $created_by = request()->user_id;

            $purchase_details = $this->transactionUtil->getPurchaseTotals($business_id, $start, $end, $location_id, $created_by);

            $sell_details = $this->transactionUtil->getSellTotals($business_id, $start, $end, $location_id, $created_by);

            $total_ledger_discount = $this->transactionUtil->getTotalLedgerDiscount($business_id, $start, $end);

            $purchase_details['purchase_due'] = $purchase_details['purchase_due'] - $total_ledger_discount['total_purchase_discount'];

            $transaction_types = [
                'purchase_return', 'sell_return', 'expense',
            ];

            $transaction_totals = $this->transactionUtil->getTransactionTotals(
                $business_id,
                $transaction_types,
                $start,
                $end,
                $location_id,
                $created_by
            );

            $total_purchase_inc_tax = ! empty($purchase_details['total_purchase_inc_tax']) ? $purchase_details['total_purchase_inc_tax'] : 0;
            $total_purchase_return_inc_tax = $transaction_totals['total_purchase_return_inc_tax'];

            $output = $purchase_details;
            $output['total_purchase'] = $total_purchase_inc_tax;
            $output['total_purchase_return'] = $total_purchase_return_inc_tax;
            $output['total_purchase_return_paid'] = $this->transactionUtil->getTotalPurchaseReturnPaid($business_id, $start, $end, $location_id);

            $total_sell_inc_tax = ! empty($sell_details['total_sell_inc_tax']) ? $sell_details['total_sell_inc_tax'] : 0;
            $total_sell_return_inc_tax = ! empty($transaction_totals['total_sell_return_inc_tax']) ? $transaction_totals['total_sell_return_inc_tax'] : 0;
            $output['total_sell_return_paid'] = $this->transactionUtil->getTotalSellReturnPaid($business_id, $start, $end, $location_id);

            $output['total_sell'] = $total_sell_inc_tax;
            $output['total_sell_return'] = $total_sell_return_inc_tax;

            $output['invoice_due'] = $sell_details['invoice_due'] - $total_ledger_discount['total_sell_discount'];
            $output['total_expense'] = $transaction_totals['total_expense'];

            //NET = TOTAL SALES - INVOICE DUE - EXPENSE
            $output['net'] = $output['total_sell'] - $output['invoice_due'] - $output['total_expense'];

            return $output;
        }
    }

    /**
     * Retrieves sell products whose available quntity is less than alert quntity.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductStockAlert()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $permitted_locations = auth()->user()->permitted_locations();
            $products = $this->productUtil->getProductAlert($business_id, $permitted_locations);

            return Datatables::of($products)
                ->editColumn('product', function ($row) {
                    if ($row->type == 'single') {
                        return $row->product.' ('.$row->sku.')';
                    } else {
                        return $row->product.' - '.$row->product_variation.' - '.$row->variation.' ('.$row->sub_sku.')';
                    }
                })
                ->editColumn('stock', function ($row) {
                    $stock = $row->stock ? $row->stock : 0;

                    return '<span data-is_quantity="true" class="display_currency" data-currency_symbol=false>'.(float) $stock.'</span> '.$row->unit;
                })
                ->removeColumn('sku')
                ->removeColumn('sub_sku')
                ->removeColumn('unit')
                ->removeColumn('type')
                ->removeColumn('product_variation')
                ->removeColumn('variation')
                ->rawColumns([2])
                ->make(false);
        }
    }

    /**
     * Retrieves payment dues for the purchases.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPurchasePaymentDues()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $today = \Carbon::now()->format('Y-m-d H:i:s');

            $query = Transaction::join(
                'contacts as c',
                'transactions.contact_id',
                '=',
                'c.id'
            )
                    ->leftJoin(
                        'transaction_payments as tp',
                        'transactions.id',
                        '=',
                        'tp.transaction_id'
                    )
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'purchase')
                    ->where('transactions.payment_status', '!=', 'paid')
                    ->whereRaw("DATEDIFF( DATE_ADD( transaction_date, INTERVAL IF(transactions.pay_term_type = 'days', transactions.pay_term_number, 30 * transactions.pay_term_number) DAY), '$today') <= 7");

            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
            }

            if (! empty(request()->input('location_id'))) {
                $query->where('transactions.location_id', request()->input('location_id'));
            }

            $dues = $query->select(
                'transactions.id as id',
                'c.name as supplier',
                'c.supplier_business_name',
                'ref_no',
                'final_total',
                DB::raw('SUM(tp.amount) as total_paid')
            )
                        ->groupBy('transactions.id');

            return Datatables::of($dues)
                ->addColumn('due', function ($row) {
                    $total_paid = ! empty($row->total_paid) ? $row->total_paid : 0;
                    $due = $row->final_total - $total_paid;

                    return '<span class="display_currency" data-currency_symbol="true">'.
                    $due.'</span>';
                })
                ->addColumn('action', '@can("purchase.create") <a href="{{action([\App\Http\Controllers\TransactionPaymentController::class, \'addPayment\'], [$id])}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-accent add_payment_modal"><i class="fas fa-money-bill-alt"></i> @lang("purchase.add_payment")</a> @endcan')
                ->removeColumn('supplier_business_name')
                ->editColumn('supplier', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$supplier}}')
                ->editColumn('ref_no', function ($row) {
                    if (auth()->user()->can('purchase.view')) {
                        return  '<a href="#" data-href="'.action([\App\Http\Controllers\PurchaseController::class, 'show'], [$row->id]).'"
                                    class="btn-modal" data-container=".view_modal">'.$row->ref_no.'</a>';
                    }

                    return $row->ref_no;
                })
                ->removeColumn('id')
                ->removeColumn('final_total')
                ->removeColumn('total_paid')
                ->rawColumns([0, 1, 2, 3])
                ->make(false);
        }
    }

    /**
     * Retrieves payment dues for the purchases.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSalesPaymentDues()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $today = \Carbon::now()->format('Y-m-d H:i:s');

            $query = Transaction::join(
                'contacts as c',
                'transactions.contact_id',
                '=',
                'c.id'
            )
                    ->leftJoin(
                        'transaction_payments as tp',
                        'transactions.id',
                        '=',
                        'tp.transaction_id'
                    )
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell')
                    ->where('transactions.payment_status', '!=', 'paid')
                    ->whereNotNull('transactions.pay_term_number')
                    ->whereNotNull('transactions.pay_term_type')
                    ->whereRaw("DATEDIFF( DATE_ADD( transaction_date, INTERVAL IF(transactions.pay_term_type = 'days', transactions.pay_term_number, 30 * transactions.pay_term_number) DAY), '$today') <= 7");

            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
            }

            if (! empty(request()->input('location_id'))) {
                $query->where('transactions.location_id', request()->input('location_id'));
            }

            $dues = $query->select(
                'transactions.id as id',
                'c.name as customer',
                'c.supplier_business_name',
                'transactions.invoice_no',
                'final_total',
                DB::raw('SUM(tp.amount) as total_paid')
            )
                        ->groupBy('transactions.id');

            return Datatables::of($dues)
                ->addColumn('due', function ($row) {
                    $total_paid = ! empty($row->total_paid) ? $row->total_paid : 0;
                    $due = $row->final_total - $total_paid;

                    return '<span class="display_currency" data-currency_symbol="true">'.
                    $due.'</span>';
                })
                ->editColumn('invoice_no', function ($row) {
                    if (auth()->user()->can('sell.view')) {
                        return  '<a href="#" data-href="'.action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]).'"
                                    class="btn-modal" data-container=".view_modal">'.$row->invoice_no.'</a>';
                    }

                    return $row->invoice_no;
                })
                ->addColumn('action', '@if(auth()->user()->can("sell.create") || auth()->user()->can("direct_sell.access")) <a href="{{action([\App\Http\Controllers\TransactionPaymentController::class, \'addPayment\'], [$id])}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-accent add_payment_modal"><i class="fas fa-money-bill-alt"></i> @lang("purchase.add_payment")</a> @endif')
                ->editColumn('customer', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$customer}}')
                ->removeColumn('supplier_business_name')
                ->removeColumn('id')
                ->removeColumn('final_total')
                ->removeColumn('total_paid')
                ->rawColumns([0, 1, 2, 3])
                ->make(false);
        }
    }

    /**
     * Get sales commission agents performance data
     *
     * @return \Illuminate\Http\Response
     */
    public function getSalesCommissionAgents()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $location_id = request()->input('location_id');
            
            // If no location_id provided, use current location from session
            if (empty($location_id)) {
                $location_id = session('user.current_location_id');
            }
            
            // Get date range (default to current month)
            $start_date = request()->input('start_date', \Carbon::now()->startOfMonth()->format('Y-m-d'));
            $end_date = request()->input('end_date', \Carbon::now()->endOfMonth()->format('Y-m-d'));
            
            try {
                // Get sales commission agents with their performance
                $query = DB::table('users as u')
                    ->leftJoin('transactions as t', function($join) use ($start_date, $end_date, $location_id) {
                        $join->on('u.id', '=', 't.commission_agent')
                             ->where('t.type', 'sell')
                             ->where('t.status', 'final')
                             ->whereBetween('t.transaction_date', [$start_date, $end_date]);
                        
                        if ($location_id) {
                            $join->where('t.location_id', $location_id);
                        }
                    })
                    ->where('u.business_id', $business_id)
                    ->where('u.is_cmmsn_agnt', 1)
                    ->whereNull('u.deleted_at');

                // Check if condition column exists
                $columns = DB::select("SHOW COLUMNS FROM users LIKE 'condition'");
                $has_condition_column = !empty($columns);

                if ($has_condition_column) {
                    $query->select(
                        'u.id',
                        DB::raw("TRIM(CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) as name"),
                        'u.email',
                        'u.contact_no',
                        'u.cmmsn_percent',
                        'u.condition',
                        DB::raw('COUNT(t.id) as total_sales'),
                        DB::raw('COALESCE(SUM(t.final_total), 0) as total_amount'),
                        DB::raw('COALESCE(SUM(t.final_total * u.cmmsn_percent / 100), 0) as total_commission')
                    )
                    ->groupBy('u.id', 'u.surname', 'u.first_name', 'u.last_name', 'u.email', 'u.contact_no', 'u.cmmsn_percent', 'u.condition');
                } else {
                    $query->select(
                        'u.id',
                        DB::raw("TRIM(CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) as name"),
                        'u.email',
                        'u.contact_no',
                        'u.cmmsn_percent',
                        DB::raw("'' as condition"),
                        DB::raw('COUNT(t.id) as total_sales'),
                        DB::raw('COALESCE(SUM(t.final_total), 0) as total_amount'),
                        DB::raw('COALESCE(SUM(t.final_total * u.cmmsn_percent / 100), 0) as total_commission')
                    )
                    ->groupBy('u.id', 'u.surname', 'u.first_name', 'u.last_name', 'u.email', 'u.contact_no', 'u.cmmsn_percent');
                }

                $query->orderBy('total_amount', 'desc');

                return Datatables::of($query)
                    ->editColumn('name', function ($row) {
                        return $row->name ?: 'N/A';
                    })
                    ->editColumn('contact_no', function ($row) {
                        return $row->contact_no ?: 'N/A';
                    })
                    ->editColumn('email', function ($row) {
                        return $row->email ?: 'N/A';
                    })
                    ->editColumn('total_amount', function ($row) {
                        return '<span class="display_currency" data-currency_symbol="true">' . number_format($row->total_amount, 2) . '</span>';
                    })
                    ->editColumn('total_commission', function ($row) {
                        return '<span class="display_currency" data-currency_symbol="true">' . number_format($row->total_commission, 2) . '</span>';
                    })
                    ->editColumn('cmmsn_percent', function ($row) {
                        return ($row->cmmsn_percent ?: 0) . '%';
                    })
                    ->editColumn('condition', function ($row) {
                        return $row->condition ?: 'None';
                    })
                    ->addColumn('performance', function ($row) {
                        // Simple performance indicator based on sales count
                        if ($row->total_sales >= 10) {
                            return '<span class="badge badge-success">Excellent</span>';
                        } elseif ($row->total_sales >= 5) {
                            return '<span class="badge badge-warning">Good</span>';
                        } elseif ($row->total_sales > 0) {
                            return '<span class="badge badge-info">Fair</span>';
                        } else {
                            return '<span class="badge badge-secondary">No Sales</span>';
                        }
                    })
                    ->rawColumns(['total_amount', 'total_commission', 'performance'])
                    ->make(false);
                    
            } catch (\Exception $e) {
                \Log::error('Sales Commission Agents DataTable Error: ' . $e->getMessage());
                
                // Return empty DataTable response
                return response()->json([
                    'draw' => request()->input('draw', 1),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'Error loading commission agents data'
                ]);
            }
        }
    }

    public function loadMoreNotifications()
    {
        $notifications = auth()->user()->notifications()->orderBy('created_at', 'DESC')->paginate(10);

        if (request()->input('page') == 1) {
            auth()->user()->unreadNotifications->markAsRead();
        }
        $notifications_data = $this->commonUtil->parseNotifications($notifications);

        return view('layouts.partials.notification_list', compact('notifications_data'));
    }

    /**
     * Function to count total number of unread notifications
     *
     * @return json
     */
    public function getTotalUnreadNotifications()
    {
        $unread_notifications = auth()->user()->unreadNotifications;
        $total_unread = $unread_notifications->count();

        $notification_html = '';
        $modal_notifications = [];
        foreach ($unread_notifications as $unread_notification) {
            if (isset($data['show_popup'])) {
                $modal_notifications[] = $unread_notification;
                $unread_notification->markAsRead();
            }
        }
        if (! empty($modal_notifications)) {
            $notification_html = view('home.notification_modal')->with(['notifications' => $modal_notifications])->render();
        }

        return [
            'total_unread' => $total_unread,
            'notification_html' => $notification_html,
        ];
    }

    private function __chartOptions($title)
    {
        return [
            'yAxis' => [
                'title' => [
                    'text' => $title,
                ],
            ],
            'legend' => [
                'align' => 'right',
                'verticalAlign' => 'top',
                'floating' => true,
                'layout' => 'vertical',
                'padding' => 20,
            ],
        ];
    }

    public function getCalendar()
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->restUtil->is_admin(auth()->user(), $business_id);
        $is_superadmin = auth()->user()->can('superadmin');
        if (request()->ajax()) {
            $data = [
                'start_date' => request()->start,
                'end_date' => request()->end,
                'user_id' => ($is_admin || $is_superadmin) && ! empty(request()->user_id) ? request()->user_id : auth()->user()->id,
                'location_id' => ! empty(request()->location_id) ? request()->location_id : null,
                'business_id' => $business_id,
                'events' => request()->events ?? [],
                'color' => '#007FFF',
            ];
            $events = [];

            if (in_array('bookings', $data['events'])) {
                $events = $this->restUtil->getBookingsForCalendar($data);
            }

            $module_events = $this->moduleUtil->getModuleData('calendarEvents', $data);

            foreach ($module_events as $module_event) {
                $events = array_merge($events, $module_event);
            }

            return $events;
        }

        $all_locations = BusinessLocation::forDropdown($business_id)->toArray();
        $users = [];
        if ($is_admin) {
            $users = User::forDropdown($business_id, false);
        }

        $event_types = [
            'bookings' => [
                'label' => __('restaurant.bookings'),
                'color' => '#007FFF',
            ],
        ];
        $module_event_types = $this->moduleUtil->getModuleData('eventTypes');
        foreach ($module_event_types as $module_event_type) {
            $event_types = array_merge($event_types, $module_event_type);
        }

        return view('home.calendar')->with(compact('all_locations', 'users', 'event_types'));
    }

    public function showNotification($id)
    {
        $notification = DatabaseNotification::find($id);

        $data = $notification->data;

        $notification->markAsRead();

        return view('home.notification_modal')->with([
            'notifications' => [$notification],
        ]);
    }

    public function attachMediasToGivenModel(Request $request)
    {
        if ($request->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $model_id = $request->input('model_id');
                $model = $request->input('model_type');
                $model_media_type = $request->input('model_media_type');

                DB::beginTransaction();

                //find model to which medias are to be attached
                $model_to_be_attached = $model::where('business_id', $business_id)
                                        ->findOrFail($model_id);

                Media::uploadMedia($business_id, $model_to_be_attached, $request, 'file', false, $model_media_type);

                DB::commit();

                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.success'),
                ];
            } catch (Exception $e) {
                DB::rollBack();

                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    public function getUserLocation($latlng)
    {
        $latlng_array = explode(',', $latlng);

        $response = $this->moduleUtil->getLocationFromCoordinates($latlng_array[0], $latlng_array[1]);

        return ['address' => $response];
    }

    /**
     * Switch user's current location and update session
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function switchLocation(Request $request)
    {
        if ($request->ajax()) {
            $business_id = $request->session()->get('user.business_id');
            $location_id = $request->input('location_id');
            
            // Validate that the location belongs to the business and user has access
            $permitted_locations = auth()->user()->permitted_locations();
            
            if ($permitted_locations != 'all' && !in_array($location_id, $permitted_locations)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to this location'
                ], 403);
            }
            
            // Verify location exists and belongs to business
            $location = BusinessLocation::where('business_id', $business_id)
                                      ->where('id', $location_id)
                                      ->where('is_active', 1)
                                      ->first();
            
            if (!$location) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found'
                ], 404);
            }
            
            // Update session with new location
            $request->session()->put('user.current_location_id', $location_id);
            $request->session()->put('user.current_location_name', $location->name);
            
            return response()->json([
                'success' => true,
                'message' => 'Location switched successfully',
                'location' => [
                    'id' => $location->id,
                    'name' => $location->name
                ]
            ]);
        }
        
        return response()->json(['success' => false, 'message' => 'Invalid request'], 400);
    }

    /**
     * Generate sales chart for last N days
     */
    private function generateSalesChart($business_id, $days = 30)
    {
        $start_date = \Carbon::now()->subDays($days)->startOfDay();
        $end_date = \Carbon::now()->endOfDay();
        
        $location_id = session('user.current_location_id');
        
        // Get sales data for the period
        $sales_data = DB::table('transactions')
            ->select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw('SUM(final_total) as total')
            )
            ->where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->whereBetween('transaction_date', [$start_date, $end_date]);
            
        if ($location_id) {
            $sales_data->where('location_id', $location_id);
        }
        
        $sales_data = $sales_data->groupBy(DB::raw('DATE(transaction_date)'))
            ->orderBy('date')
            ->get();
        
        // Prepare chart data
        $dates = [];
        $amounts = [];
        
        // Fill in missing dates with 0
        $current_date = $start_date->copy();
        while ($current_date <= $end_date) {
            $date_str = $current_date->format('Y-m-d');
            $dates[] = $current_date->format('M d');
            
            $found = $sales_data->firstWhere('date', $date_str);
            $amounts[] = $found ? (float)$found->total : 0;
            
            $current_date->addDay();
        }
        
        $chart = new CommonChart;
        $chart->title(__('home.sells_last_30_days'));
        $chart->labels($dates);
        $chart->dataset(__('home.total_sell'), 'line', $amounts);
        $chart->options($this->__chartOptions(__('home.total_sell')));
        
        return $chart;
    }

    /**
     * Generate sales chart for current financial year
     */
    private function generateSalesChartFY($business_id)
    {
        $fy = $this->businessUtil->getCurrentFinancialYear($business_id);
        $start_date = $fy['start'];
        $end_date = $fy['end'];
        
        $location_id = session('user.current_location_id');
        
        // Get monthly sales data
        $sales_data = DB::table('transactions')
            ->select(
                DB::raw('YEAR(transaction_date) as year'),
                DB::raw('MONTH(transaction_date) as month'),
                DB::raw('SUM(final_total) as total')
            )
            ->where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->whereBetween('transaction_date', [$start_date, $end_date]);
            
        if ($location_id) {
            $sales_data->where('location_id', $location_id);
        }
        
        $sales_data = $sales_data->groupBy(DB::raw('YEAR(transaction_date)'), DB::raw('MONTH(transaction_date)'))
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        
        // Prepare chart data
        $months = [];
        $amounts = [];
        
        $current_date = \Carbon::parse($start_date);
        $end_carbon = \Carbon::parse($end_date);
        
        while ($current_date <= $end_carbon) {
            $months[] = $current_date->format('M Y');
            
            $found = $sales_data->where('year', $current_date->year)
                               ->where('month', $current_date->month)
                               ->first();
            $amounts[] = $found ? (float)$found->total : 0;
            
            $current_date->addMonth();
        }
        
        $chart = new CommonChart;
        $chart->title(__('home.sells_current_fy'));
        $chart->labels($months);
        $chart->dataset(__('home.total_sell'), 'column', $amounts);
        $chart->options($this->__chartOptions(__('home.total_sell')));
        
        return $chart;
    }
}
