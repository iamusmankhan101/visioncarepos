<?php

namespace App\Http\Controllers;

use App\Business;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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
        $user = Auth::user();
        
        // Get businesses where user can access (either owner or has access)
        $available_businesses = Business::where('is_active', 1)
            ->where(function($query) use ($user) {
                $query->where('owner_id', $user->id)
                      ->orWhereHas('locations.users', function($q) use ($user) {
                          $q->where('users.id', $user->id);
                      });
            })
            ->get();

        return view('business.select', compact('available_businesses'));
    }

    /**
     * Show business registration form
     */
    public function register()
    {
        return view('business.register');
    }

    /**
     * Store new business
     */
    public function store(Request $request)
    {
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

        try {
            DB::beginTransaction();

            $user = Auth::user();
            
            // Create business
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

            // Set defaults
            $business_data['owner_id'] = $user->id;
            $business_data['created_by'] = $user->id;
            $business_data['is_active'] = 1;
            $business_data['pos_settings'] = $request->get('pos_settings', []);
            $business_data['weighing_scale_setting'] = $request->get('weighing_scale_setting', []);
            $business_data['enabled_modules'] = $request->get('enabled_modules', []);
            $business_data['ref_no_prefixes'] = $request->get('ref_no_prefixes', []);
            $business_data['email_settings'] = $request->get('email_settings', []);
            $business_data['sms_settings'] = $request->get('sms_settings', []);
            $business_data['custom_labels'] = $request->get('custom_labels', []);
            $business_data['common_settings'] = $request->get('common_settings', []);

            $business = Business::create($business_data);

            // Update user's business_id
            $user->business_id = $business->id;
            $user->save();

            // Set session to indicate business has been selected
            session(['selected_business_id' => $business->id]);

            DB::commit();

            // Redirect based on user permissions after successful registration
            if (!$user->can('dashboard.data') && $user->can('sell.create')) {
                return redirect('/pos/create')->with('success', 'Business registered successfully! Welcome to your POS.');
            }

            return redirect('/home')->with('success', 'Business registered successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to register business: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Switch to selected business
     */
    public function switch(Request $request)
    {
        $request->validate([
            'business_id' => 'required|exists:business,id'
        ]);

        $user = Auth::user();
        $business = Business::findOrFail($request->business_id);

        // Check if user has access to this business
        $has_access = $business->owner_id == $user->id || 
                     $business->locations()->whereHas('users', function($q) use ($user) {
                         $q->where('users.id', $user->id);
                     })->exists();

        if (!$has_access) {
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

        // Redirect based on user permissions
        if (!$user->can('dashboard.data') && $user->can('sell.create')) {
            return redirect('/pos/create')->with('success', 'Switched to ' . $business->name . ' successfully!');
        }

        return redirect('/home')->with('success', 'Switched to ' . $business->name . ' successfully!');
    }
}