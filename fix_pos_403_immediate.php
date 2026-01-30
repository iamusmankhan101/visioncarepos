<?php
require_once 'vendor/autoload.php';

// Start Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "<h2>Fixing POS 403 Error</h2>";

try {
    // Check if user is authenticated
    if (!auth()->check()) {
        echo "❌ User not authenticated. Please login first.<br>";
        exit;
    }

    $user = auth()->user();
    echo "✅ User authenticated: " . $user->email . "<br>";

    // Fix 1: Ensure user has a business assigned
    if (!$user->business_id) {
        // Get the first active business
        $business = \App\Business::where('is_active', 1)->first();
        if ($business) {
            $user->business_id = $business->id;
            $user->save();
            echo "✅ Assigned business ID {$business->id} to user<br>";
        } else {
            echo "❌ No active business found. Creating default business...<br>";
            
            // Create a default business
            $business = new \App\Business();
            $business->name = 'Default Business';
            $business->currency_id = 1; // Assuming USD
            $business->start_date = date('Y-m-d');
            $business->is_active = 1;
            $business->created_by = $user->id;
            $business->save();
            
            $user->business_id = $business->id;
            $user->save();
            echo "✅ Created and assigned default business<br>";
        }
    } else {
        echo "✅ User has business ID: " . $user->business_id . "<br>";
    }

    // Fix 2: Set selected_business_id in session
    session(['selected_business_id' => $user->business_id]);
    echo "✅ Set selected_business_id in session: " . $user->business_id . "<br>";

    // Fix 3: Ensure business is active
    $business = \App\Business::find($user->business_id);
    if ($business && !$business->is_active) {
        $business->is_active = 1;
        $business->save();
        echo "✅ Activated business: " . $business->name . "<br>";
    }

    // Fix 4: Check SellPosController create method
    $controller = new \App\Http\Controllers\SellPosController();
    if (!method_exists($controller, 'create')) {
        echo "❌ create method missing in SellPosController<br>";
        echo "Adding emergency create method...<br>";
        
        // Read current controller
        $controllerPath = 'app/Http/Controllers/SellPosController.php';
        $content = file_get_contents($controllerPath);
        
        // Check if create method already exists
        if (strpos($content, 'public function create') === false) {
            // Add create method before the last closing brace
            $createMethod = '
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can("sell.create")) {
            abort(403, "Unauthorized.");
        }

        $business_id = request()->session()->get("user.business_id");
        $walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);
        $business_details = $this->businessUtil->getDetails($business_id);
        $taxes = TaxRate::forBusinessDropdown($business_id, true, true);
        $payment_lines = $this->getPaymentMethods($business_id);
        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $bl_attributes = $business_locations["attributes"];
        $business_locations = $business_locations["locations"];

        $default_location = null;
        if (count($business_locations) == 1) {
            foreach ($business_locations as $id => $name) {
                $default_location = $id;
            }
        }

        return view("sale_pos.create")
            ->with(compact(
                "walk_in_customer",
                "business_details", 
                "taxes",
                "payment_lines",
                "business_locations",
                "bl_attributes",
                "default_location"
            ));
    }
';
            
            // Insert before the last closing brace
            $lastBrace = strrpos($content, '}');
            $newContent = substr($content, 0, $lastBrace) . $createMethod . "\n" . substr($content, $lastBrace);
            
            file_put_contents($controllerPath, $newContent);
            echo "✅ Added create method to SellPosController<br>";
        } else {
            echo "✅ create method already exists in SellPosController<br>";
        }
    } else {
        echo "✅ create method exists in SellPosController<br>";
    }

    // Clear cache
    \Artisan::call('cache:clear');
    \Artisan::call('config:clear');
    \Artisan::call('route:clear');
    echo "✅ Cleared Laravel cache<br>";

    echo "<br><h3>✅ All fixes applied successfully!</h3>";
    echo "<a href='/pos/create' target='_blank'>Test POS Create</a><br>";
    echo "<a href='/pos' target='_blank'>Go to POS Index</a><br>";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString() . "<br>";
}
?>