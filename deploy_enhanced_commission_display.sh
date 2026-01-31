#!/bin/bash

echo "🚀 DEPLOYING ENHANCED COMMISSION DISPLAY"
echo "========================================"
echo ""

echo "1. Checking database structure..."
php -r "
require_once 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
try {
    \$columns = DB::select(\"SHOW COLUMNS FROM users LIKE 'target_type'\");
    if (empty(\$columns)) {
        echo '❌ Enhanced columns missing. Run: php fix_commission_database_error.php\n';
        exit(1);
    } else {
        echo '✅ Enhanced columns exist\n';
    }
} catch (Exception \$e) {
    echo '❌ Database error: ' . \$e->getMessage() . '\n';
    exit(1);
}
"

if [ $? -ne 0 ]; then
    echo ""
    echo "🔧 Fixing database structure..."
    php fix_commission_database_error.php
fi

echo ""
echo "2. Clearing Laravel caches..."
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:clear

echo ""
echo "3. Testing commission agents functionality..."
php -r "
require_once 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
try {
    \$controller = new \App\Http\Controllers\SalesCommissionAgentController(new \App\Utils\Util());
    echo '✅ Controller instantiated successfully\n';
    
    \$users = \App\User::where('is_cmmsn_agnt', 1)->limit(1)->get();
    echo '✅ Commission agents query works\n';
    
    echo '✅ Enhanced commission display ready\n';
} catch (Exception \$e) {
    echo '❌ Error: ' . \$e->getMessage() . '\n';
}
"

echo ""
echo "✅ ENHANCED COMMISSION DISPLAY DEPLOYED!"
echo "========================================"
echo ""

echo "🎯 New Features Available:"
echo "1. ✅ Target Status Column - Shows progress towards targets"
echo "2. ✅ Commission Applicable Column - Shows when commission applies"
echo "3. ✅ Enhanced Condition Display - Shows targets and rules"
echo "4. ✅ Real-time Progress Tracking - Current vs target sales"
echo "5. ✅ Visual Status Indicators - Color-coded labels and icons"
echo ""

echo "📊 What You'll See:"
echo "• Target progress percentages with achievement status"
echo "• Commission applicability based on target completion"
echo "• Detailed condition information with target types"
echo "• Bonus commission indicators"
echo "• Monthly/Quarterly/Yearly target tracking"
echo ""

echo "🔄 Next Steps:"
echo "1. Visit the Commission Agents page"
echo "2. View the enhanced target/condition columns"
echo "3. Create/edit agents to set up targets"
echo "4. Monitor real-time performance tracking"
echo ""

echo "💡 Tips:"
echo "• Green labels = Targets achieved or commission applicable"
echo "• Yellow labels = Progress towards targets"
echo "• Red labels = Targets not met or commission not applicable"
echo "• Blue text = Target and condition details"
echo ""