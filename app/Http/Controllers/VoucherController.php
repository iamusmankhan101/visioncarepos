<?php

namespace App\Http\Controllers;

use App\Voucher;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('tax_rate.view') && !auth()->user()->can('tax_rate.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $vouchers = Voucher::where('business_id', $business_id)
                        ->select(['code', 'name', 'discount_type', 'discount_value', 'min_amount', 'max_discount', 'usage_limit', 'used_count', 'is_active', 'expires_at', 'id']);

            return Datatables::of($vouchers)
                ->addColumn(
                    'action',
                    '@can("tax_rate.update")
                        <button data-href="{{route(\'tax-rates.edit\', [$id])}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-primary btn-modal" data-container=".voucher_modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                    @endcan
                    @can("tax_rate.delete")
                        <button data-href="{{route(\'tax-rates.destroy\', [$id])}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-error delete_voucher_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endcan'
                )
                ->editColumn('discount_type', function ($row) {
                    return $row->discount_type == 'percentage' ? __('lang_v1.percentage') : __('lang_v1.fixed');
                })
                ->editColumn('discount_value', function ($row) {
                    return $row->discount_type == 'percentage' ? $row->discount_value . '%' : number_format($row->discount_value, 2);
                })
                ->editColumn('is_active', function ($row) {
                    return $row->is_active ? '<span class="label label-success">' . __('lang_v1.active') . '</span>' : '<span class="label label-danger">' . __('lang_v1.inactive') . '</span>';
                })
                ->editColumn('expires_at', function ($row) {
                    return $row->expires_at ? $row->expires_at->format('Y-m-d') : __('lang_v1.no_expiry');
                })
                ->rawColumns(['action', 'is_active'])
                ->make(true);
        }

        return view('voucher.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('tax_rate.create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('voucher.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('tax_rate.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Validate the request
            $request->validate([
                'code' => 'required|string|max:255|unique:vouchers,code',
                'name' => 'required|string|max:255',
                'discount_type' => 'required|in:percentage,fixed',
                'discount_value' => 'required|numeric|min:0',
                'min_amount' => 'nullable|numeric|min:0',
                'max_discount' => 'nullable|numeric|min:0',
                'usage_limit' => 'nullable|integer|min:1',
                'expires_at' => 'nullable|date_format:m/d/Y'
            ]);

            $input = $request->only(['code', 'name', 'discount_type', 'discount_value', 'min_amount', 'max_discount', 'usage_limit', 'expires_at', 'is_active']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['used_count'] = 0;
            $input['is_active'] = isset($input['is_active']) ? 1 : 0;

            if (!empty($input['expires_at'])) {
                $input['expires_at'] = \Carbon\Carbon::createFromFormat('m/d/Y', $input['expires_at']);
            }

            Voucher::create($input);

            $output = ['success' => true,
                'msg' => __('lang_v1.voucher_added_success')
            ];
        } catch (\Illuminate\Validation\ValidationException $e) {
            $output = ['success' => false,
                'msg' => 'Validation Error: ' . implode(', ', $e->validator->errors()->all())
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = ['success' => false,
                'msg' => 'Error: ' . $e->getMessage()
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Not implemented for vouchers
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('tax_rate.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $voucher = Voucher::where('business_id', $business_id)->find($id);

        return view('voucher.edit')
            ->with(compact('voucher'));
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
        if (!auth()->user()->can('tax_rate.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Validate the request
            $request->validate([
                'code' => 'required|string|max:255|unique:vouchers,code,' . $id,
                'name' => 'required|string|max:255',
                'discount_type' => 'required|in:percentage,fixed',
                'discount_value' => 'required|numeric|min:0',
                'min_amount' => 'nullable|numeric|min:0',
                'max_discount' => 'nullable|numeric|min:0',
                'usage_limit' => 'nullable|integer|min:1',
                'expires_at' => 'nullable|date_format:m/d/Y'
            ]);

            $input = $request->only(['code', 'name', 'discount_type', 'discount_value', 'min_amount', 'max_discount', 'usage_limit', 'expires_at', 'is_active']);
            $input['is_active'] = isset($input['is_active']) ? 1 : 0;

            if (!empty($input['expires_at'])) {
                $input['expires_at'] = \Carbon\Carbon::createFromFormat('m/d/Y', $input['expires_at']);
            }

            $business_id = $request->session()->get('user.business_id');
            Voucher::where('business_id', $business_id)->where('id', $id)->update($input);

            $output = ['success' => true,
                'msg' => __('lang_v1.voucher_updated_success')
            ];
        } catch (\Illuminate\Validation\ValidationException $e) {
            $output = ['success' => false,
                'msg' => 'Validation Error: ' . implode(', ', $e->validator->errors()->all())
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = ['success' => false,
                'msg' => 'Error: ' . $e->getMessage()
            ];
        }

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('tax_rate.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            Voucher::where('business_id', $business_id)->where('id', $id)->delete();

            $output = ['success' => true,
                'msg' => __('lang_v1.voucher_deleted_success')
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * Get active vouchers for dropdown
     *
     * @return \Illuminate\Http\Response
     */
    public function getActiveVouchers(Request $request)
    {
        if (!auth()->user()->can('tax_rate.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            
            $vouchers = Voucher::where('business_id', $business_id)
                        ->where('is_active', 1)
                        ->where(function($query) {
                            $query->whereNull('expires_at')
                                  ->orWhere('expires_at', '>', now());
                        })
                        ->where(function($query) {
                            $query->whereNull('usage_limit')
                                  ->orWhereRaw('COALESCE(used_count, 0) < COALESCE(usage_limit, 999999)');
                        })
                        ->select('id', 'code', 'name', 'discount_type', 'discount_value', 'min_amount', 'max_discount')
                        ->orderBy('name')
                        ->get();

            return response()->json([
                'success' => true,
                'vouchers' => $vouchers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Error fetching vouchers: ' . $e->getMessage()
            ]);
        }
    }
}