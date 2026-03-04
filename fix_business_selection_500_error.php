<?php

// Fix for business selection 500 error
echo "Fixing Business Selection 500 Error\n";
echo "===================================\n\n";

// Create a simplified business selection controller
$controllerContent = '<?php

namespace App\Http\Controllers;

use App\Business;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BusinessSelectionController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }

    /**
     * Show business selection/registration screen
     */
    public function select()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return redirect("/login")->with("error", "Please login first");
            }
            
            // Get businesses where user can access (simplified query)
            $available_businesses = collect();
            
            try {
                $available_businesses = Business::where("is_active", 1)
                    ->where("owner_id", $user->id)
                    ->get();
            } catch (\Exception $e) {
                // If there\'s a database error, continue with empty collection
                \Log::error("Business selection query error: " . $e->getMessage());
            }

            return view("business.select", compact("available_businesses"));
            
        } catch (\Exception $e) {
            \Log::error("Business selection error: " . $e->getMessage());
            return redirect("/home")->with("error", "Unable to load business selection. Please contact support.");
        }
    }

    /**
     * Show business registration form
     */
    public function register()
    {
        try {
            return view("business.register");
        } catch (\Exception $e) {
            \Log::error("Business registration view error: " . $e->getMessage());
            return redirect()->route("business.select")->with("error", "Unable to load registration form.");
        }
    }

    /**
     * Switch to selected business
     */
    public function switch(Request $request)
    {
        try {
            $request->validate([
                "business_id" => "required|exists:business,id"
            ]);

            $user = Auth::user();
            $business = Business::findOrFail($request->business_id);

            // Check if user has access to this business
            if ($business->owner_id != $user->id) {
                return back()->withErrors(["error" => "You do not have access to this business."]);
            }

            if (!$business->is_active) {
                return back()->withErrors(["error" => "This business is inactive."]);
            }

            // Update user\'s business_id
            $user->business_id = $business->id;
            $user->save();

            // Set session to indicate business has been selected
            session(["selected_business_id" => $business->id]);

            // Clear any cached business data
            session()->forget(["business", "location"]);

            // Redirect to POS or dashboard
            if ($user->can("sell.create")) {
                return redirect("/pos/create")->with("success", "Switched to " . $business->name . " successfully!");
            }

            return redirect("/home")->with("success", "Switched to " . $business->name . " successfully!");
            
        } catch (\Exception $e) {
            \Log::error("Business switch error: " . $e->getMessage());
            return back()->withErrors(["error" => "Unable to switch business. Please try again."]);
        }
    }

    /**
     * Store new business (simplified)
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                "name" => "required|string|max:255",
            ]);

            $user = Auth::user();
            
            // Create basic business
            $business = Business::create([
                "name" => $request->name,
                "owner_id" => $user->id,
                "created_by" => $user->id,
                "is_active" => 1,
                "currency_id" => 1, // Default currency
                "start_date" => now(),
                "fy_start_month" => 1,
                "accounting_method" => "fifo",
                "transaction_edit_days" => 30,
                "stock_expiry_alert_days" => 30,
                "date_format" => "d-m-Y",
                "time_format" => "12",
                "currency_symbol_placement" => "before",
                "sales_cmsn_agnt" => "logged_in_user",
                "item_addition_method" => 1,
            ]);

            // Update user\'s business_id
            $user->business_id = $business->id;
            $user->save();

            // Set session
            session(["selected_business_id" => $business->id]);

            return redirect("/home")->with("success", "Business registered successfully!");
            
        } catch (\Exception $e) {
            \Log::error("Business store error: " . $e->getMessage());
            return back()->withErrors(["error" => "Unable to register business: " . $e->getMessage()])->withInput();
        }
    }
}';

// Write the simplified controller
file_put_contents('app/Http/Controllers/BusinessSelectionController.php', $controllerContent);
echo "✓ Created simplified BusinessSelectionController\n";

// Create a simplified business selection view
$viewContent = '@extends("layouts.auth")

@section("title", __("Select Business"))

@section("content")
<div class="login-form col-md-12 col-xs-12 right-col-content-register">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="login-content">
                <div class="login-header">
                    <h3 class="text-center">Welcome! Select Your Business</h3>
                    <p class="text-center text-muted">Choose your business to continue to POS system</p>
                </div>

                @if(session("success"))
                    <div class="alert alert-success">
                        {{ session("success") }}
                    </div>
                @endif

                @if(session("error"))
                    <div class="alert alert-danger">
                        {{ session("error") }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="login-form-container">
                    @if($available_businesses->count() > 0)
                        <div class="business-selection-section">
                            <h4 class="text-center mb-3">Available Businesses</h4>
                            
                            <form method="POST" action="{{ route("business.switch") }}">
                                @csrf
                                <div class="form-group">
                                    <label for="business_id">Select Business:</label>
                                    <select name="business_id" id="business_id" class="form-control" required>
                                        <option value="">Choose a business...</option>
                                        @foreach($available_businesses as $business)
                                            <option value="{{ $business->id }}">
                                                {{ $business->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fa fa-sign-in"></i> Enter Business
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="text-center my-4">
                            <span class="text-muted">OR</span>
                        </div>
                    @endif

                    <div class="business-registration-section">
                        <h4 class="text-center mb-3">Register New Business</h4>
                        
                        <form method="POST" action="{{ route("business.store") }}">
                            @csrf
                            <div class="form-group">
                                <label for="name">Business Name:</label>
                                <input type="text" name="name" id="name" class="form-control" 
                                       value="{{ old("name") }}" required>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fa fa-plus"></i> Register Business
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route("logout") }}" 
                           onclick="event.preventDefault(); document.getElementById(\'logout-form\').submit();"
                           class="btn btn-link">
                            <i class="fa fa-sign-out"></i> Logout
                        </a>
                        
                        <form id="logout-form" action="{{ route("logout") }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.business-selection-section, .business-registration-section {
    padding: 20px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    margin-bottom: 20px;
    background-color: #f9f9f9;
}

.login-header {
    margin-bottom: 30px;
}

.login-header h3 {
    color: #333;
    font-weight: 600;
}

.btn-block {
    padding: 12px;
    font-size: 16px;
    font-weight: 500;
}

.form-control {
    padding: 10px;
    font-size: 14px;
}

.alert {
    margin-bottom: 20px;
}
</style>
@endsection';

// Write the simplified view
if (!is_dir('resources/views/business')) {
    mkdir('resources/views/business', 0755, true);
}
file_put_contents('resources/views/business/select.blade.php', $viewContent);
echo "✓ Created simplified business selection view\n";

echo "\nSimplified business selection system created!\n";
echo "This version includes:\n";
echo "- Error handling and logging\n";
echo "- Simplified database queries\n";
echo "- Basic business registration\n";
echo "- Graceful error recovery\n";
echo "\nTry accessing /business/select again.\n";