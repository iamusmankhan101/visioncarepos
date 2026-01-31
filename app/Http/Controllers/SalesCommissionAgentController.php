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

            $users = User::where('business_id', $business_id)
                        ->where('is_cmmsn_agnt', 1)
                        ->select(['id',
                            DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"),
                            'email', 'contact_no', 'address', 'cmmsn_percent', 'condition', 
                            'target_type', 'target_amount', 'commission_applies_when', 'bonus_percent', 'target_reset_date']);

            return Datatables::of($users)
                ->addColumn('target_status', function ($row) {
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
                    
                    return '<span class="label ' . $labelClass . '">' . 
                           number_format($progress, 1) . '% (' . 
                           number_format($currentSales) . '/' . number_format($targetAmount) . ')' .
                           '</span>';
                })
                ->addColumn('commission_applicable', function ($row) {
                    if (empty($row->commission_applies_when) || $row->commission_applies_when === 'always') {
                        return '<span class="label label-success">' . __('lang_v1.always_apply_commission') . '</span>';
                    }
                    
                    if (empty($row->target_type) || $row->target_type === 'none') {
                        return '<span class="label label-success">' . __('lang_v1.always_apply_commission') . '</span>';
                    }
                    
                    $currentSales = $this->calculateCurrentPeriodSales($row);
                    $targetAmount = $row->target_amount ?: 0;
                    
                    if ($targetAmount <= 0) {
                        return '<span class="label label-success">' . __('lang_v1.always_apply_commission') . '</span>';
                    }
                    
                    $targetMet = $currentSales >= $targetAmount;
                    $targetExceeded = $currentSales > $targetAmount;
                    
                    if ($row->commission_applies_when === 'target_met') {
                        $applicable = $targetMet;
                        $text = $applicable ? __('lang_v1.commission_applicable') : __('lang_v1.target_not_met');
                    } else { // target_exceeded
                        $applicable = $targetExceeded;
                        $text = $applicable ? __('lang_v1.commission_applicable') : __('lang_v1.target_not_exceeded');
                    }
                    
                    $labelClass = $applicable ? 'label-success' : 'label-danger';
                    return '<span class="label ' . $labelClass . '">' . $text . '</span>';
                })
                ->editColumn('condition', function ($row) {
                    if (empty($row->condition)) {
                        return '<span class="text-muted">' . __('lang_v1.no_condition') . '</span>';
                    }
                    return $row->condition;
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
                    $query->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->removeColumn('id')
                ->rawColumns(['action', 'target_status', 'commission_applicable', 'condition'])
                ->make(true);
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
            $input = $request->only([
                'surname', 'first_name', 'last_name', 'email', 'address', 'contact_no', 
                'cmmsn_percent', 'condition', 'target_type', 'target_amount', 
                'commission_applies_when', 'bonus_percent', 'commission_notes'
            ]);
            
            $input['cmmsn_percent'] = $this->commonUtil->num_uf($input['cmmsn_percent']);
            
            // Handle target amount
            if (!empty($input['target_amount'])) {
                $input['target_amount'] = $this->commonUtil->num_uf($input['target_amount']);
            }
            
            // Handle bonus percent
            if (!empty($input['bonus_percent'])) {
                $input['bonus_percent'] = $this->commonUtil->num_uf($input['bonus_percent']);
            }
            
            // Calculate target reset date based on target type
            if (!empty($input['target_type']) && $input['target_type'] !== 'none') {
                $input['target_reset_date'] = $this->calculateTargetResetDate($input['target_type']);
            }
            
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
        
        // Calculate current period sales if target is set
        if (!empty($user->target_type) && $user->target_type !== 'none') {
            $user->current_period_sales = $this->calculateCurrentPeriodSales($user);
            $user->target_completion_status = $this->getTargetCompletionStatus($user);
        }

        return view('sales_commission_agent.edit')
                    ->with(compact('user'));
    }

    /**
     * Calculate target reset date based on target type
     */
    private function calculateTargetResetDate($target_type)
    {
        $now = now();
        
        switch ($target_type) {
            case 'monthly':
                return $now->copy()->addMonth()->startOfMonth()->format('Y-m-d');
            case 'quarterly':
                $currentQuarter = ceil($now->month / 3);
                $nextQuarter = $currentQuarter + 1;
                if ($nextQuarter > 4) {
                    return $now->copy()->addYear()->startOfYear()->format('Y-m-d');
                } else {
                    $nextQuarterMonth = ($nextQuarter - 1) * 3 + 1;
                    return $now->copy()->month($nextQuarterMonth)->startOfMonth()->format('Y-m-d');
                }
            case 'yearly':
                return $now->copy()->addYear()->startOfYear()->format('Y-m-d');
            default:
                return null;
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
     * Get target completion status
     */
    private function getTargetCompletionStatus($user)
    {
        if (empty($user->target_amount) || $user->target_amount <= 0) {
            return 'no_target';
        }

        $currentSales = $user->current_period_sales ?? $this->calculateCurrentPeriodSales($user);
        
        if ($currentSales >= $user->target_amount) {
            return 'achieved';
        } else {
            return 'pending';
        }
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
                $input = $request->only([
                    'surname', 'first_name', 'last_name', 'email', 'address', 'contact_no', 
                    'cmmsn_percent', 'condition', 'target_type', 'target_amount', 
                    'commission_applies_when', 'bonus_percent', 'commission_notes'
                ]);
                
                $input['cmmsn_percent'] = $this->commonUtil->num_uf($input['cmmsn_percent']);
                
                // Handle target amount
                if (!empty($input['target_amount'])) {
                    $input['target_amount'] = $this->commonUtil->num_uf($input['target_amount']);
                }
                
                // Handle bonus percent
                if (!empty($input['bonus_percent'])) {
                    $input['bonus_percent'] = $this->commonUtil->num_uf($input['bonus_percent']);
                }
                
                $business_id = $request->session()->get('user.business_id');

                $user = User::where('id', $id)
                            ->where('business_id', $business_id)
                            ->where('is_cmmsn_agnt', 1)
                            ->first();
                            
                // Calculate target reset date if target type changed
                if (!empty($input['target_type']) && $input['target_type'] !== 'none') {
                    if ($user->target_type !== $input['target_type']) {
                        $input['target_reset_date'] = $this->calculateTargetResetDate($input['target_type']);
                    }
                } else {
                    $input['target_reset_date'] = null;
                }
                
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
