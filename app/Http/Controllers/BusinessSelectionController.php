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

            // Redirect to POS or dashboard
            if ($user->can('sell.create')) {
                return redirect('/pos/create')->with('success', 'Switched to ' . $business->name . ' successfully!');
            }

            return redirect('/home')->with('success', 'Switched to ' . $business->name . ' successfully!');
            
        } catch (\Exception $e) {
            Log::error('Business switch error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Unable to switch business. Please try again.']);
        }
    }

    /**
     * Store new business (simplified)
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $user = Auth::user();
            
            // Create basic business
            $business = Business::create([
                'name' => $request->name,
                'owner_id' => $user->id,
                'created_by' => $user->id,
                'is_active' => 1,
                'currency_id' => 1, // Default currency
                'start_date' => now(),
                'fy_start_month' => 1,
                'accounting_method' => 'fifo',
                'transaction_edit_days' => 30,
                'stock_expiry_alert_days' => 30,
                'date_format' => 'd-m-Y',
                'time_format' => '12',
                'currency_symbol_placement' => 'before',
                'sales_cmsn_agnt' => 'logged_in_user',
                'item_addition_method' => 1,
            ]);

            // Update user's business_id
            $user->business_id = $business->id;
            $user->save();

            // Set session
            session(['selected_business_id' => $business->id]);

            return redirect('/home')->with('success', 'Business registered successfully!');
            
        } catch (\Exception $e) {
            Log::error('Business store error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Unable to register business: ' . $e->getMessage()])->withInput();
        }
    }
}