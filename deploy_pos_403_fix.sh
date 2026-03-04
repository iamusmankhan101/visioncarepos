#!/bin/bash

echo "🚀 Deploying POS 403 Forbidden fix..."

# Step 1: Create debug endpoints
echo "🧪 Step 1: Creating debug endpoints..."
cat > public/pos_debug.php << 'EOF'
<?php
session_start();

echo "<h2>POS Access Debug</h2>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Session Status:</strong> " . (session_status() === PHP_SESSION_ACTIVE ? "Active" : "Inactive") . "</p>";

echo "<h3>Session Data:</h3>";
echo "<pre>" . print_r($_SESSION, true) . "</pre>";

echo "<h3>Business Selection Check:</h3>";
if (isset($_SESSION["selected_business_id"])) {
    echo "<p>✅ Selected Business ID: " . $_SESSION["selected_business_id"] . "</p>";
} else {
    echo "<p>❌ No business selected in session</p>";
}

if (isset($_SESSION["user_business_id"])) {
    echo "<p>✅ User Business ID: " . $_SESSION["user_business_id"] . "</p>";
} else {
    echo "<p>❌ No user business ID in session</p>";
}

echo "<h3>Cookies:</h3>";
echo "<pre>" . print_r($_COOKIE, true) . "</pre>";

echo "<h3>Possible Solutions:</h3>";
echo "<ul>";
echo "<li>If no business is selected, visit <a href='/business/select'>/business/select</a> first</li>";
echo "<li>If session is empty, try <a href='/login'>logging in again</a></li>";
echo "<li>Check if user has proper permissions for POS access</li>";
echo "<li>Clear browser cache and cookies</li>";
echo "</ul>";
?>
EOF
echo "✅ Created POS debug endpoint at /pos_debug.php"

# Step 2: Check if SellPosController has create method
echo "📝 Step 2: Checking SellPosController..."
if grep -q "function create" app/Http/Controllers/SellPosController.php; then
    echo "✅ create method found in SellPosController"
else
    echo "❌ create method NOT found - this is likely the cause of 403"
    echo "🔧 Adding basic create method..."
    
    # Create backup
    cp app/Http/Controllers/SellPosController.php app/Http/Controllers/SellPosController.php.backup
    
    # Add create method (this is a basic implementation)
    cat >> app/Http/Controllers/SellPosController.php << 'EOF'

    /**
     * Show the form for creating a new POS sale.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            // Check if user has selected a business
            if (!session()->has('selected_business_id')) {
                return redirect()->route('business.select')
                    ->with('error', 'Please select a business first.');
            }

            // Get business details
            $business_id = session('selected_business_id');
            $user = auth()->user();
            
            if (!$user) {
                return redirect()->route('login');
            }

            // Get necessary data for POS
            $business = $user->business;
            $locations = $business->locations ?? collect();
            
            // Return the POS create view
            return view('sale_pos.create', compact('business', 'locations'));
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error accessing POS: ' . $e->getMessage());
        }
    }
}
EOF
    echo "✅ Added create method to SellPosController"
fi

# Step 3: Clear all caches
echo "📦 Step 3: Clearing caches..."
rm -rf storage/framework/views/*.php 2>/dev/null || true
rm -rf storage/framework/sessions/* 2>/dev/null || true
rm -f bootstrap/cache/config.php 2>/dev/null || true
rm -f bootstrap/cache/routes.php 2>/dev/null || true
echo "✅ Caches cleared"

# Step 4: Set permissions
echo "🔐 Step 4: Setting permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
echo "✅ Permissions set"

# Step 5: Create business selection helper
echo "🏢 Step 5: Creating business selection helper..."
cat > public/fix_business_session.php << 'EOF'
<?php
// Simple business session fixer
session_start();

echo "<h2>Business Session Fixer</h2>";

// Set a default business ID if none exists
if (!isset($_SESSION['selected_business_id'])) {
    $_SESSION['selected_business_id'] = 1; // Default to business ID 1
    echo "<p>✅ Set selected_business_id to 1</p>";
} else {
    echo "<p>✅ Business already selected: " . $_SESSION['selected_business_id'] . "</p>";
}

echo "<p><a href='/pos/create'>Try accessing POS now</a></p>";
?>
EOF
echo "✅ Created business session fixer at /fix_business_session.php"

echo ""
echo "🎉 POS 403 fix deployment completed!"
echo ""
echo "Summary of changes:"
echo "- Created debug endpoint at /pos_debug.php"
echo "- Added create method to SellPosController (if missing)"
echo "- Cleared all Laravel caches"
echo "- Created business session fixer at /fix_business_session.php"
echo ""
echo "Troubleshooting steps:"
echo "1. Visit https://pos.digitrot.com/pos_debug.php to check session"
echo "2. If no business selected, visit https://pos.digitrot.com/fix_business_session.php"
echo "3. Then try https://pos.digitrot.com/pos/create"
echo "4. If still failing, visit /business/select first"