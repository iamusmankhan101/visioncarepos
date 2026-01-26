<?php

namespace App\Http\Controllers;

use App\User;
use App\Utils\Util;
use DataTables;
use DB;
use Illuminate\Http\Request;

class SalesCommissionAgentController extends Controller
{
    /**
     * Constructor
     *
     * @param  Util  $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil)
    {
        $this->commonUtil = $commonUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('user.view') && ! auth()->user()->can('user.create')) {
            abort(403, 'Unauthorized action.');
        }

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
                // Get sales commission agents with their performance (same as dashboard)
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
                        DB::raw("TRIM(CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) as full_name"),
                        'u.email',
                        'u.contact_no',
                        'u.address',
                        'u.cmmsn_percent',
                        'u.condition',
                        DB::raw('COUNT(t.id) as total_sales'),
                        DB::raw('COALESCE(SUM(t.final_total), 0) as total_amount'),
                        DB::raw('COALESCE(SUM(t.final_total * u.cmmsn_percent / 100), 0) as total_commission')
                    )
                    ->groupBy('u.id', 'u.surname', 'u.first_name', 'u.last_name', 'u.email', 'u.contact_no', 'u.address', 'u.cmmsn_percent', 'u.condition');
                } else {
                    $query->select(
                        'u.id',
                        DB::raw("TRIM(CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) as full_name"),
                        'u.email',
                        'u.contact_no',
                        'u.address',
                        'u.cmmsn_percent',
                        DB::raw("'' as condition"),
                        DB::raw('COUNT(t.id) as total_sales'),
                        DB::raw('COALESCE(SUM(t.final_total), 0) as total_amount'),
                        DB::raw('COALESCE(SUM(t.final_total * u.cmmsn_percent / 100), 0) as total_commission')
                    )
                    ->groupBy('u.id', 'u.surname', 'u.first_name', 'u.last_name', 'u.email', 'u.contact_no', 'u.address', 'u.cmmsn_percent');
                }

                $query->orderBy('total_amount', 'desc');

                return Datatables::of($query)
                    ->editColumn('full_name', function ($row) {
                        return $row->full_name ?: 'N/A';
                    })
                    ->editColumn('contact_no', function ($row) {
                        return $row->contact_no ?: 'N/A';
                    })
                    ->editColumn('email', function ($row) {
                        return $row->email ?: 'N/A';
                    })
                    ->editColumn('address', function ($row) {
                        return $row->address ?: 'N/A';
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
                        // Performance indicator based on sales count (same as dashboard)
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
                    ->addColumn(
                        'action',
                        '@can("user.update")
                        <button type="button" data-href="{{action(\'App\Http\Controllers\SalesCommissionAgentController@edit\', [$id])}}" data-container=".commission_agent_modal" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  btn-modal tw-dw-btn-primary"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                            &nbsp;
                            @endcan
                            @can("user.delete")
                            <button data-href="{{action(\'App\Http\Controllers\SalesCommissionAgentController@destroy\', [$id])}}" class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-error delete_commsn_agnt_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                            @endcan'
                    )
                    ->filterColumn('full_name', function ($query, $keyword) {
                        $query->whereRaw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) like ?", ["%{$keyword}%"]);
                    })
                    ->removeColumn('id')
                    ->rawColumns(['total_amount', 'total_commission', 'performance', 'action'])
                    ->make(true);
                    
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

        return view('sales_commission_agent.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! auth()->user()->can('user.create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('sales_commission_agent.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('user.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['surname', 'first_name', 'last_name', 'email', 'address', 'contact_no', 'cmmsn_percent', 'condition']);
            $input['cmmsn_percent'] = $this->commonUtil->num_uf($input['cmmsn_percent']);
            $business_id = $request->session()->get('user.business_id');
            $input['business_id'] = $business_id;
            $input['allow_login'] = 0;
            $input['is_cmmsn_agnt'] = 1;

            $user = User::create($input);

            $output = ['success' => true,
                'msg' => __('lang_v1.commission_agent_added_success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('user.update')) {
            abort(403, 'Unauthorized action.');
        }

        $user = User::findOrFail($id);

        return view('sales_commission_agent.edit')
                    ->with(compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (! auth()->user()->can('user.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['surname', 'first_name', 'last_name', 'email', 'address', 'contact_no', 'cmmsn_percent', 'condition']);
                $input['cmmsn_percent'] = $this->commonUtil->num_uf($input['cmmsn_percent']);
                $business_id = $request->session()->get('user.business_id');

                $user = User::where('id', $id)
                            ->where('business_id', $business_id)
                            ->where('is_cmmsn_agnt', 1)
                            ->first();
                $user->update($input);

                $output = ['success' => true,
                    'msg' => __('lang_v1.commission_agent_updated_success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('user.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                User::where('id', $id)
                    ->where('business_id', $business_id)
                    ->where('is_cmmsn_agnt', 1)
                    ->delete();

                $output = ['success' => true,
                    'msg' => __('lang_v1.commission_agent_deleted_success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }
}
