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

            // Check if enhanced columns exist
            $hasEnhancedColumns = $this->checkEnhancedColumnsExist();
            
            $selectFields = [
                'id',
                DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"),
                'email', 'contact_no', 'address', 'cmmsn_percent', 'condition'
            ];
            
            // Add enhanced fields if they exist
            if ($hasEnhancedColumns) {
                $selectFields = array_merge($selectFields, [
                    'target_type', 'target_amount', 'commission_applies_when', 
                    'bonus_percent', 'target_reset_date', 'commission_notes'
                ]);
            }

            $users = User::where('business_id', $business_id)
                        ->where('is_cmmsn_agnt', 1)
                        ->select($selectFields);

            $datatable = Datatables::of($users);
            
            // Add enhanced columns if available
            if ($hasEnhancedColumns) {
                $datatable->addColumn('target_status', function ($row) {
                    if (empty($row->target_type) || $row->target_type === 'none') {
                        return '<span class="label label-default">' . __('lang_v1.no_target') . '</span>';
                    }
                    
                    $currentSales = $this->calculateCurrentPeriodSales($row);
                    $targetAmount = $row->target_amount ?: 0;
                    
                    if ($targetAmount <= 0) {
                        return '<span class="label label-default">' . __('lang_v1.no_target') . '</span>';
                    }
                    
                    $progress = ($currentSales / $targetAmount) * 100;
                    $status = $currentSales >= $targetAmount ? 'achieved' : 'pending';
                    $labelClass = $status === 'achieved' ? 'label-success' : 'label-warning';
                    
                    $targetTypeText = [
                        'monthly' => __('lang_v1.monthly_target'),
                        'quarterly' => __('lang_v1.quarterly_target'),
                        'yearly' => __('lang_v1.yearly_target')
                    ];
                    
                    return '<div class="target-info">' .
                           '<span class="label ' . $labelClass . '">' . 
                           number_format($progress, 1) . '% ' . ($status === 'achieved' ? '✓' : '') .
                           '</span><br>' .
                           '<small class="text-muted">' . 
                           ($targetTypeText[$row->target_type] ?? $row->target_type) . ': ' .
                           number_format($currentSales) . '/' . number_format($targetAmount) .
                           '</small></div>';
                });
                
                $datatable->addColumn('commission_applicable', function ($row) {
                    if (empty($row->commission_applies_when) || $row->commission_applies_when === 'always') {
                        return '<span class="label label-success"><i class="fa fa-check"></i> ' . __('lang_v1.always') . '</span>';
                    }
                    
                    if (empty($row->target_type) || $row->target_type === 'none') {
                        return '<span class="label label-success"><i class="fa fa-check"></i> ' . __('lang_v1.always') . '</span>';
                    }
                    
                    $currentSales = $this->calculateCurrentPeriodSales($row);
                    $targetAmount = $row->target_amount ?: 0;
                    
                    if ($targetAmount <= 0) {
                        return '<span class="label label-success"><i class="fa fa-check"></i> ' . __('lang_v1.always') . '</span>';
                    }
                    
                    $targetMet = $currentSales >= $targetAmount;
                    $targetExceeded = $currentSales > $targetAmount;
                    
                    if ($row->commission_applies_when === 'target_met') {
                        $applicable = $targetMet;
                        $text = $applicable ? __('lang_v1.applicable') : __('lang_v1.target_not_met');
                        $icon = $applicable ? 'fa-check' : 'fa-times';
                    } else { // target_exceeded
                        $applicable = $targetExceeded;
                        $text = $applicable ? __('lang_v1.applicable') : __('lang_v1.target_not_exceeded');
                        $icon = $applicable ? 'fa-check' : 'fa-times';
                    }
                    
                    $labelClass = $applicable ? 'label-success' : 'label-danger';
                    return '<span class="label ' . $labelClass . '"><i class="fa ' . $icon . '"></i> ' . $text . '</span>';
                });
                
                $datatable->editColumn('condition', function ($row) {
                    if (empty($row->condition) && (empty($row->target_type) || $row->target_type === 'none')) {
                        return '<span class="text-muted"><i class="fa fa-minus"></i> ' . __('lang_v1.no_condition') . '</span>';
                    }
                    
                    $html = '';
                    
                    // Show basic condition
                    if (!empty($row->condition)) {
                        $html .= '<div class="condition-text">' . $row->condition . '</div>';
                    }
                    
                    // Show target condition
                    if (!empty($row->target_type) && $row->target_type !== 'none') {
                        $targetText = [
                            'monthly' => __('lang_v1.monthly'),
                            'quarterly' => __('lang_v1.quarterly'), 
                            'yearly' => __('lang_v1.yearly')
                        ];
                        
                        $appliesWhenText = [
                            'always' => __('lang_v1.always_apply'),
                            'target_met' => __('lang_v1.when_target_met'),
                            'target_exceeded' => __('lang_v1.when_target_exceeded')
                        ];
                        
                        $html .= '<small class="text-info">';
                        $html .= '<i class="fa fa-target"></i> ' . ($targetText[$row->target_type] ?? $row->target_type);
                        if ($row->target_amount > 0) {
                            $html .= ': ' . number_format($row->target_amount);
                        }
                        $html .= '<br>';
                        $html .= '<i class="fa fa-cog"></i> ' . ($appliesWhenText[$row->commission_applies_when] ?? $row->commission_applies_when);
                        if ($row->bonus_percent > 0) {
                            $html .= '<br><i class="fa fa-plus"></i> +' . number_format($row->bonus_percent, 2) . '% ' . __('lang_v1.bonus');
                        }
                        $html .= '</small>';
                    }
                    
                    return $html ?: '<span class="text-muted"><i class="fa fa-minus"></i> ' . __('lang_v1.no_condition') . '</span>';
                });
                
                $rawColumns = ['action', 'target_status', 'commission_applicable', 'condition'];
            } else {
                // Basic version - just format condition column
                $datatable->editColumn('condition', function ($row) {
                    return !empty($row->condition) ? $row->condition : '<span class="text-muted">' . __('lang_v1.no_condition') . '</span>';
                });
                
                // Add placeholder columns for enhanced features
                $datatable->addColumn('target_status', function ($row) {
                    return '<span class="text-muted"><i class="fa fa-info-circle"></i> ' . __('lang_v1.upgrade_for_targets') . '</span>';
                });
                
                $datatable->addColumn('commission_applicable', function ($row) {
                    return '<span class="label label-success"><i class="fa fa-check"></i> ' . __('lang_v1.always') . '</span>';
                });
                
                $rawColumns = ['action', 'target_status', 'commission_applicable', 'condition'];
            }

            return $datatable
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
                    $query->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->removeColumn('id')
                ->rawColumns($rawColumns)
                ->make(true);
        }

        return view('sales_commission_agent.index');
    }

    /**
     * Check if enhanced commission columns exist
     */
    private function checkEnhancedColumnsExist()
    {
        try {
            $columns = DB::select("SHOW COLUMNS FROM users LIKE 'target_type'");
            return !empty($columns);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Calculate current period sales for an agent
     */
    private function calculateCurrentPeriodSales($user)
    {
        if (empty($user->target_type) || $user->target_type === 'none') {
            return 0;
        }

        $now = now();
        $startDate = null;

        switch ($user->target_type) {
            case 'monthly':
                $startDate = $now->copy()->startOfMonth();
                break;
            case 'quarterly':
                $currentQuarter = ceil($now->month / 3);
                $quarterStartMonth = ($currentQuarter - 1) * 3 + 1;
                $startDate = $now->copy()->month($quarterStartMonth)->startOfMonth();
                break;
            case 'yearly':
                $startDate = $now->copy()->startOfYear();
                break;
        }

        if (!$startDate) {
            return 0;
        }

        // Get total sales for this agent in the current period
        $sales = DB::table('transactions')
            ->where('created_by', $user->id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $now)
            ->sum('final_total');

        return $sales ?: 0;
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
