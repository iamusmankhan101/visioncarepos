<?php

namespace App\Http\Controllers;

use App\Business;
use App\User;
use App\BusinessLocation;
use App\TaxRate;
use App\CustomerGroup;
use App\Category;
use App\Brands;
use App\Unit;
use App\Contact;
use App\Product;
use App\Transaction;
use App\Voucher;
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

            // If user has only one business, automatically select it
            if ($available_businesses->count() === 1) {
                $business = $available_businesses->first();
                
                // Set the selected business in session
                session(['selected_business_id' => $business->id]);
                
                // Set up business data in session
                session([
                    'business' => [
                        'id' => $business->id,
                        'name' => $business->name,
                        'currency_id' => $business->currency_id,
                        'start_date' => $business->start_date,
                        'enabled_modules' => $business->enabled_modules,
                        'currency_precision' => $business->currency_precision ?? 2,
                        'quantity_precision' => $business->quantity_precision ?? 2,
                        'time_zone' => $business->time_zone ?? 'UTC',
                    ]
                ]);
                
                // Update user's business_id
                $user->business_id = $business->id;
                $user->save();
                
                // Redirect to home/dashboard
                return redirect('/home')->with('success', 'Welcome to ' . $business->name . '!');
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

            // IMPORTANT: Clear ALL session data to force refresh
            session()->forget(['user', 'business', 'currency', 'financial_year', 'location']);
            session(['selected_business_id' => $business->id]);

            // Force refresh session data by setting up business data immediately
            $business_util = new \App\Utils\BusinessUtil();
            
            // Set user session data
            $session_data = [
                'id' => $user->id,
                'surname' => $user->surname,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'business_id' => $user->business_id,
                'language' => $user->language,
            ];

            // Ensure enabled_modules is properly formatted
            if (is_string($business->enabled_modules)) {
                $business->enabled_modules = json_decode($business->enabled_modules, true) ?: [];
            }
            if (!is_array($business->enabled_modules)) {
                $business->enabled_modules = [];
            }

            // Set currency data
            $currency = $business->currency;
            $currency_data = [
                'id' => $currency->id,
                'code' => $currency->code,
                'symbol' => $currency->symbol,
                'thousand_separator' => $currency->thousand_separator,
                'decimal_separator' => $currency->decimal_separator,
            ];

            // Set financial year
            $financial_year = $business_util->getCurrentFinancialYear($business->id);

            // Put all data into session
            session([
                'user' => $session_data,
                'business' => $business,
                'currency' => $currency_data,
                'financial_year' => $financial_year
            ]);

            // Ensure user has proper permissions for this business
            try {
                // Check if user has admin role for this business
                $adminRole = \Spatie\Permission\Models\Role::where('name', 'Admin#' . $business->id)->first();
                
                if ($adminRole && !$user->hasRole($adminRole)) {
                    // Assign admin role if user doesn't have it
                    $user->assignRole($adminRole);
                }
                
                // If no role exists, give essential permissions directly
                if (!$adminRole) {
                    $essentialPermissions = ['sell.create', 'superadmin'];
                    foreach ($essentialPermissions as $permName) {
                        $permission = \Spatie\Permission\Models\Permission::firstOrCreate([
                            'name' => $permName,
                            'guard_name' => 'web'
                        ]);
                        
                        if (!$user->hasPermissionTo($permission)) {
                            $user->givePermissionTo($permission);
                        }
                    }
                }
                
            } catch (\Exception $permissionException) {
                Log::error('Permission assignment failed during business switch: ' . $permissionException->getMessage());
                // Continue anyway - permissions might not be critical for basic functionality
            }

            // Always redirect to home first, then let middleware handle routing
            return redirect('/home')->with('success', 'Switched to ' . $business->name . ' successfully!');
            
        } catch (\Exception $e) {
            Log::error('Business switch error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Unable to switch business. Please try again.']);
        }
    }

    /**
     * Delete a business
     */
    public function delete(Request $request)
    {
        try {
            $request->validate([
                'business_id' => 'required|exists:business,id'
            ]);

            $user = Auth::user();
            $business = Business::findOrFail($request->business_id);

            // Check if user is the owner of this business
            if ($business->owner_id != $user->id) {
                return back()->withErrors(['error' => 'You can only delete businesses you own.']);
            }

            // Check if this is the user's current business
            if ($user->business_id == $business->id) {
                // Clear the user's current business selection
                $user->business_id = null;
                $user->save();
                
                // Clear session
                session()->forget(['selected_business_id', 'business', 'location']);
            }

            DB::beginTransaction();

            try {
                // Delete related data in proper order to avoid foreign key constraints
                
                // Delete business locations
                \App\BusinessLocation::where('business_id', $business->id)->delete();
                
                // Delete tax rates
                \App\TaxRate::where('business_id', $business->id)->delete();
                
                // Delete customer groups
                \App\CustomerGroup::where('business_id', $business->id)->delete();
                
                // Delete categories
                \App\Category::where('business_id', $business->id)->delete();
                
                // Delete brands
                \App\Brands::where('business_id', $business->id)->delete();
                
                // Delete units
                \App\Unit::where('business_id', $business->id)->delete();
                
                // Delete contacts (customers/suppliers)
                \App\Contact::where('business_id', $business->id)->delete();
                
                // Delete products
                \App\Product::where('business_id', $business->id)->delete();
                
                // Delete transactions
                \App\Transaction::where('business_id', $business->id)->delete();
                
                // Delete vouchers
                \App\Voucher::where('business_id', $business->id)->delete();
                
                // Delete roles associated with this business
                \Spatie\Permission\Models\Role::where('name', 'like', '%#' . $business->id)->delete();
                
                // Finally delete the business itself
                $business->delete();

                DB::commit();

                return redirect()->route('business.select')->with('success', 'Business deleted successfully.');
                
            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Business deletion error: ' . $e->getMessage());
                return back()->withErrors(['error' => 'Unable to delete business. Some data may still be associated with it.']);
            }
            
        } catch (\Exception $e) {
            Log::error('Business delete validation error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Unable to delete business. Please try again.']);
        }
    }

    /**
     * Store new business (comprehensive setup)
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'currency_id' => 'required|exists:currencies,id',
                'start_date' => 'required|date',
                'fy_start_month' => 'required|integer|min:1|max:12',
                'accounting_method' => 'required|in:fifo,lifo,avco',
                'transaction_edit_days' => 'required|integer|min:0',
                'stock_expiry_alert_days' => 'required|integer|min:0',
                'date_format' => 'required|string',
                'time_format' => 'required|in:12,24',
                'currency_symbol_placement' => 'required|in:before,after',
                'sales_cmsn_agnt' => 'required|in:logged_in_user,user,percentage',
                'item_addition_method' => 'required|integer|min:1|max:2',
            ]);

            DB::beginTransaction();
            
            $user = Auth::user();
            
            // Create comprehensive business with all necessary settings
            $business = Business::create([
                'name' => $request->name,
                'owner_id' => $user->id,
                'created_by' => $user->id,
                'is_active' => 1,
                'currency_id' => $request->currency_id,
                'start_date' => $request->start_date,
                'fy_start_month' => $request->fy_start_month,
                'accounting_method' => $request->accounting_method,
                'transaction_edit_days' => $request->transaction_edit_days,
                'stock_expiry_alert_days' => $request->stock_expiry_alert_days,
                'date_format' => $request->date_format,
                'time_format' => $request->time_format,
                'currency_symbol_placement' => $request->currency_symbol_placement,
                'sales_cmsn_agnt' => $request->sales_cmsn_agnt,
                'item_addition_method' => $request->item_addition_method,
                'enable_brand' => $request->has('enable_brand') ? 1 : 0,
                'enable_category' => $request->has('enable_category') ? 1 : 0,
                'enable_sub_category' => $request->has('enable_sub_category') ? 1 : 0,
                'enable_price_tax' => $request->has('enable_price_tax') ? 1 : 0,
                'enable_purchase_status' => $request->has('enable_purchase_status') ? 1 : 0,
                'enable_lot_number' => $request->has('enable_lot_number') ? 1 : 0,
                'enable_sub_units' => $request->has('enable_sub_units') ? 1 : 0,
                'enable_racks' => $request->has('enable_racks') ? 1 : 0,
                'enable_row' => $request->has('enable_row') ? 1 : 0,
                'enable_position' => $request->has('enable_position') ? 1 : 0,
                'enable_editing_product_from_purchase' => $request->has('enable_editing_product_from_purchase') ? 1 : 0,
                'enable_inline_tax' => $request->has('enable_inline_tax') ? 1 : 0,
                'keyboard_shortcuts' => $request->has('keyboard_shortcuts') ? 1 : 0,
                'pos_settings' => json_encode([
                    'amount_rounding_method' => 'none',
                    'disable_pay_checkout' => 0,
                    'disable_draft' => 0,
                    'disable_express_checkout' => 0,
                    'hide_product_suggestion' => 0,
                    'hide_recent_trans' => 0,
                    'disable_discount' => 0,
                    'disable_order_tax' => 0,
                    'is_pos_subtotal_editable' => 0,
                    'print_on_suspend' => 0,
                    'show_pricing_on_product_sugesstion' => 1,
                    'enable_payment_link' => 0,
                    'inline_service_staff' => 0
                ]),
                'enabled_modules' => json_encode([
                    'purchases', 'add_sale', 'pos', 'stock_transfers', 'stock_adjustment',
                    'expenses', 'account', 'tables', 'modifiers', 'service_staff',
                    'kitchen', 'communication', 'booking', 'crm_module'
                ]),
                'ref_no_prefixes' => json_encode([
                    'purchase' => 'PO',
                    'stock_transfer' => 'ST',
                    'stock_adjustment' => 'SA',
                    'sell_return' => 'CN',
                    'expense' => 'EP',
                    'contacts' => 'CO',
                    'purchase_payment' => 'PP',
                    'sell_payment' => 'SP',
                    'expense_payment' => 'EP',
                    'business_location' => 'BL',
                    'username' => '',
                    'subscription' => 'SU',
                    'draft' => 'DF',
                    'quotation' => 'QU'
                ])
            ]);

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

            // Assign admin role to user for this business (handle foreign key constraint)
            try {
                $adminRole = \Spatie\Permission\Models\Role::where('name', 'Admin#' . $business->id)->first();
                if (!$adminRole) {
                    // Check if roles table has business_id column
                    $rolesColumns = DB::select("SHOW COLUMNS FROM roles");
                    $hasBusinessId = false;
                    
                    foreach ($rolesColumns as $column) {
                        if ($column->Field === 'business_id') {
                            $hasBusinessId = true;
                            break;
                        }
                    }
                    
                    if ($hasBusinessId) {
                        // Create role with business_id using raw SQL to avoid constraint issues
                        DB::table('roles')->insert([
                            'name' => 'Admin#' . $business->id,
                            'guard_name' => 'web',
                            'business_id' => $business->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        
                        $adminRole = \Spatie\Permission\Models\Role::where('name', 'Admin#' . $business->id)->first();
                    } else {
                        // Create role without business_id
                        $adminRole = \Spatie\Permission\Models\Role::create([
                            'name' => 'Admin#' . $business->id,
                            'guard_name' => 'web'
                        ]);
                    }
                    
                    // Give all permissions to admin role
                    $permissions = \Spatie\Permission\Models\Permission::all();
                    if ($permissions->count() > 0) {
                        $adminRole->syncPermissions($permissions);
                    }
                }
                
                $user->assignRole($adminRole);
                
            } catch (\Exception $roleException) {
                // If role creation fails, give user direct permissions
                Log::warning('Role creation failed, using direct permissions: ' . $roleException->getMessage());
                
                // Create essential permissions if they don't exist
                $essentialPermissions = ['sell.create', 'superadmin'];
                foreach ($essentialPermissions as $permName) {
                    $permission = \Spatie\Permission\Models\Permission::firstOrCreate([
                        'name' => $permName,
                        'guard_name' => 'web'
                    ]);
                    
                    $user->givePermissionTo($permission);
                }
            }

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