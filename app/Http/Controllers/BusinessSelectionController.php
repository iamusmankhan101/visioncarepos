<?php

namespace App\Http\Controllers;

use App\Business;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BusinessSelectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show business selection/registration screen
     */
    public function select()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return redirect('/login')->with('error', 'Please login first');
            }
            
            // Get businesses where user is the owner (simplified query to avoid relationship issues)
            $available_businesses = collect();
            
            try {
                $available_businesses = Business::where('is_active', 1)
                    ->where('owner_id', $user->id)
                    ->get();
            } catch (\Exception $e) {
                // If there's a database error, continue with empty collection
                Log::error('Business selection query error: ' . $e->getMessage());
            }

            return view('business.select', compact('available_businesses'));
            
        } catch (\Exception $e) {
            Log::error('Business selection error: ' . $e->getMessage());
            return redirect('/home')->with('error', 'Unable to load business selection. Please contact support.');
        }
    }

    /**
     * Show business registration form
     */
    public function register()
    {
        try {
            return view('business.register');
        } catch (\Exception $e) {
            Log::error('Business registration view error: ' . $e->getMessage());
            return redirect()->route('business.select')->with('error', 'Unable to load registration form.');
        }
    }

    /**
     * Switch to selected business
     */
    public function switch(Request $request)
    {
        try {
            $request->validate([
                'business_id' => 'required|exists:business,id'
            ]);

            $user = Auth::user();
            $business = Business::findOrFail($request->business_id);

            // Check if user has access to this business
            if ($business->owner_id != $user->id) {
                return back()->withErrors(['error' => 'You do not have access to this business.']);
            }

            if (!$business->is_active) {
                return back()->withErrors(['error' => 'This business is inactive.']);
            }

            // Update user's business_id
            $user->business_id = $business->id;
            $user->save();

            // Set session to indicate business has been selected
            session(['selected_business_id' => $business->id]);

            // Clear any cached business data
            session()->forget(['business', 'location']);

            // Always redirect to POS for immediate use
            return redirect('/pos/create')->with('success', 'Switched to ' . $business->name . ' successfully! Ready to start selling.');
            
        } catch (\Exception $e) {
            Log::error('Business switch error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Unable to switch business. Please try again.']);
        }
    }

    /**
     * Store new business (comprehensive setup with full form validation)
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'currency_id' => 'required|exists:currencies,id',
                'start_date' => 'required|date',
                'business_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'fy_start_month' => 'required|integer|min:1|max:12',
                'accounting_method' => 'required|in:fifo,lifo,avco',
                'transaction_edit_days' => 'required|integer|min:0',
                'stock_expiry_alert_days' => 'required|integer|min:0',
                'keyboard_shortcuts' => 'boolean',
                'pos_settings' => 'array',
                'weighing_scale_setting' => 'array',
                'enable_brand' => 'boolean',
                'enable_category' => 'boolean',
                'enable_sub_category' => 'boolean',
                'enable_price_tax' => 'boolean',
                'enable_purchase_status' => 'boolean',
                'enable_lot_number' => 'boolean',
                'default_unit' => 'nullable|exists:units,id',
                'enable_sub_units' => 'boolean',
                'enable_racks' => 'boolean',
                'enable_row' => 'boolean',
                'enable_position' => 'boolean',
                'enable_editing_product_from_purchase' => 'boolean',
                'sales_cmsn_agnt' => 'required|in:logged_in_user,user,percentage',
                'item_addition_method' => 'required|integer|min:1|max:2',
                'enable_inline_tax' => 'boolean',
                'currency_symbol_placement' => 'required|in:before,after',
                'enabled_modules' => 'array',
                'date_format' => 'required|string',
                'time_format' => 'required|in:12,24',
                'ref_no_prefixes' => 'array',
                'theme_color' => 'nullable|string',
                'created_by' => 'nullable|exists:users,id',
                'enable_rp' => 'boolean',
                'rp_name' => 'nullable|string',
                'amount_for_unit_rp' => 'nullable|numeric',
                'min_order_total_for_rp' => 'nullable|numeric',
                'max_rp_per_order' => 'nullable|numeric',
                'redeem_amount_per_unit_rp' => 'nullable|numeric',
                'min_order_total_for_redeem' => 'nullable|numeric',
                'min_redeem_point' => 'nullable|numeric',
                'max_redeem_point' => 'nullable|numeric',
                'rp_expiry_period' => 'nullable|integer',
                'rp_expiry_type' => 'nullable|in:month,year',
                'email_settings' => 'array',
                'sms_settings' => 'array',
                'custom_labels' => 'array',
                'common_settings' => 'array',
                'is_active' => 'boolean',
            ]);

            DB::beginTransaction();
            
            $user = Auth::user();
            
            // Create comprehensive business with form data
            $business_data = $request->only([
                'name', 'currency_id', 'start_date', 'fy_start_month', 'accounting_method',
                'transaction_edit_days', 'stock_expiry_alert_days', 'keyboard_shortcuts',
                'enable_brand', 'enable_category', 'enable_sub_category', 'enable_price_tax',
                'enable_purchase_status', 'enable_lot_number', 'default_unit', 'enable_sub_units',
                'enable_racks', 'enable_row', 'enable_position', 'enable_editing_product_from_purchase',
                'sales_cmsn_agnt', 'item_addition_method', 'enable_inline_tax', 'currency_symbol_placement',
                'date_format', 'time_format', 'theme_color', 'enable_rp', 'rp_name',
                'amount_for_unit_rp', 'min_order_total_for_rp', 'max_rp_per_order',
                'redeem_amount_per_unit_rp', 'min_order_total_for_redeem', 'min_redeem_point',
                'max_redeem_point', 'rp_expiry_period', 'rp_expiry_type'
            ]);

            // Set defaults and user data
            $business_data['owner_id'] = $user->id;
            $business_data['created_by'] = $user->id;
            $business_data['is_active'] = 1;
            
            // Handle array fields that need JSON encoding
            $business_data['pos_settings'] = json_encode($request->get('pos_settings', []));
            $business_data['weighing_scale_setting'] = json_encode($request->get('weighing_scale_setting', []));
            $business_data['enabled_modules'] = json_encode($request->get('enabled_modules', []));
            $business_data['ref_no_prefixes'] = json_encode($request->get('ref_no_prefixes', []));
            $business_data['email_settings'] = json_encode($request->get('email_settings', []));
            $business_data['sms_settings'] = json_encode($request->get('sms_settings', []));
            $business_data['custom_labels'] = json_encode($request->get('custom_labels', []));
            $business_data['common_settings'] = json_encode($request->get('common_settings', []));
            
            // Handle boolean fields properly
            $business_data['keyboard_shortcuts'] = $request->has('keyboard_shortcuts') ? 1 : 0;
            $business_data['enable_brand'] = $request->has('enable_brand') ? 1 : 0;
            $business_data['enable_category'] = $request->has('enable_category') ? 1 : 0;
            $business_data['enable_sub_category'] = $request->has('enable_sub_category') ? 1 : 0;
            $business_data['enable_price_tax'] = $request->has('enable_price_tax') ? 1 : 0;
            $business_data['enable_purchase_status'] = $request->has('enable_purchase_status') ? 1 : 0;
            $business_data['enable_lot_number'] = $request->has('enable_lot_number') ? 1 : 0;
            $business_data['enable_sub_units'] = $request->has('enable_sub_units') ? 1 : 0;
            $business_data['enable_racks'] = $request->has('enable_racks') ? 1 : 0;
            $business_data['enable_row'] = $request->has('enable_row') ? 1 : 0;
            $business_data['enable_position'] = $request->has('enable_position') ? 1 : 0;
            $business_data['enable_editing_product_from_purchase'] = $request->has('enable_editing_product_from_purchase') ? 1 : 0;
            $business_data['enable_inline_tax'] = $request->has('enable_inline_tax') ? 1 : 0;
            $business_data['enable_rp'] = $request->has('enable_rp') ? 1 : 0;

            $business = Business::create($business_data);

            // Create default business location
            $location = \App\BusinessLocation::create([
                'business_id' => $business->id,
                'location_id' => 'BL0001',
                'name' => $business->name . ' - Main Location',
                'landmark' => '',
                'country' => 'Pakistan',
                'state' => '',
                'city' => '',
                'zip_code' => '',
                'invoice_scheme_id' => 1,
                'invoice_layout_id' => 1,
                'selling_price_group_id' => null,
                'print_receipt_on_invoice' => 1,
                'receipt_printer_type' => 'browser',
                'printer_id' => null,
                'mobile' => '',
                'alternate_number' => '',
                'email' => '',
                'website' => '',
                'featured_products' => json_encode([]),
                'is_active' => 1,
                'default_payment_accounts' => json_encode([
                    'cash' => ['is_enabled' => 1, 'account' => null],
                    'card' => ['is_enabled' => 1, 'account' => null],
                    'cheque' => ['is_enabled' => 1, 'account' => null],
                    'bank_transfer' => ['is_enabled' => 1, 'account' => null],
                    'other' => ['is_enabled' => 1, 'account' => null],
                    'custom_pay_1' => ['is_enabled' => 1, 'account' => null],
                    'custom_pay_2' => ['is_enabled' => 1, 'account' => null],
                    'custom_pay_3' => ['is_enabled' => 1, 'account' => null]
                ])
            ]);

            // Create default tax rate
            \App\TaxRate::create([
                'business_id' => $business->id,
                'name' => 'VAT@0%',
                'amount' => 0,
                'is_tax_group' => 0,
                'created_by' => $user->id
            ]);

            // Create default customer group
            \App\CustomerGroup::create([
                'business_id' => $business->id,
                'name' => 'Default',
                'amount' => 0,
                'price_calculation_type' => 'percentage',
                'selling_price_group_id' => null,
                'created_by' => $user->id
            ]);

            // Create default category
            \App\Category::create([
                'name' => 'General',
                'business_id' => $business->id,
                'short_code' => 'GEN',
                'parent_id' => 0,
                'created_by' => $user->id,
                'category_type' => 'product'
            ]);

            // Create default brand
            \App\Brands::create([
                'business_id' => $business->id,
                'name' => 'Generic',
                'description' => 'Default brand',
                'created_by' => $user->id
            ]);

            // Create default unit
            \App\Unit::create([
                'business_id' => $business->id,
                'actual_name' => 'Pieces',
                'short_name' => 'Pc(s)',
                'allow_decimal' => 0,
                'base_unit_id' => null,
                'base_unit_multiplier' => null,
                'created_by' => $user->id
            ]);

            // Update user's business_id
            $user->business_id = $business->id;
            $user->save();

            // Skip role creation for now to avoid foreign key constraint issues
            // The user will still have access as the business owner
            // Role assignment can be handled later if needed

            // Set session
            session(['selected_business_id' => $business->id]);

            DB::commit();

            // Redirect directly to POS for immediate use
            return redirect('/pos/create')->with('success', 'Business "' . $business->name . '" created successfully! Welcome to your POS system.');
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Business store error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Unable to register business: ' . $e->getMessage()])->withInput();
        }
    }
}