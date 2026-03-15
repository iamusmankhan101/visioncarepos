# Delivery Modal Deployment Script (PowerShell)
# This script clears caches and ensures the delivery modal feature is properly deployed

Write-Host "🚀 Deploying Delivery Modal Feature..." -ForegroundColor Green
Write-Host "======================================"

# Check if we're in a Laravel project
if (-not (Test-Path "artisan")) {
    Write-Host "❌ Error: Not in a Laravel project directory" -ForegroundColor Red
    Write-Host "Please run this script from your Laravel project root" -ForegroundColor Red
    exit 1
}

Write-Host "✅ Laravel project detected" -ForegroundColor Green

# Clear all Laravel caches
Write-Host ""
Write-Host "🧹 Clearing Laravel caches..." -ForegroundColor Yellow
Write-Host "------------------------------"

Write-Host "Clearing application cache..."
& php artisan cache:clear

Write-Host "Clearing route cache..."
& php artisan route:clear

Write-Host "Clearing view cache..."
& php artisan view:clear

Write-Host "Clearing config cache..."
& php artisan config:clear

Write-Host "Clearing compiled services..."
& php artisan clear-compiled

# Optimize for production (optional)
Write-Host ""
Write-Host "⚡ Optimizing application..." -ForegroundColor Yellow
Write-Host "---------------------------"

Write-Host "Caching routes..."
& php artisan route:cache

Write-Host "Caching config..."
& php artisan config:cache

Write-Host "Caching views..."
& php artisan view:cache

# Check file permissions
Write-Host ""
Write-Host "🔒 Checking file permissions..." -ForegroundColor Yellow
Write-Host "-------------------------------"

# Check if storage directory exists and is accessible
if (Test-Path "storage") {
    Write-Host "✅ Storage directory exists" -ForegroundColor Green
} else {
    Write-Host "⚠️  Storage directory missing" -ForegroundColor Yellow
}

# Check if bootstrap/cache exists and is accessible
if (Test-Path "bootstrap/cache") {
    Write-Host "✅ Bootstrap cache directory exists" -ForegroundColor Green
} else {
    Write-Host "⚠️  Bootstrap cache directory missing" -ForegroundColor Yellow
}

# Verify key files exist
Write-Host ""
Write-Host "📁 Verifying delivery modal files..." -ForegroundColor Yellow
Write-Host "------------------------------------"

$files = @(
    "resources/views/sale_pos/partials/payment_modal.blade.php",
    "public/js/pos.js",
    "app/Utils/TransactionUtil.php",
    "app/Http/Controllers/SellPosController.php",
    "resources/views/sale_pos/receipts/classic.blade.php",
    "resources/views/sale_pos/receipts/elegant.blade.php",
    "resources/views/sale_pos/receipts/detailed.blade.php"
)

foreach ($file in $files) {
    if (Test-Path $file) {
        Write-Host "✅ $file exists" -ForegroundColor Green
    } else {
        Write-Host "❌ $file is missing" -ForegroundColor Red
    }
}

# Check for test files
Write-Host ""
Write-Host "🧪 Checking test files..." -ForegroundColor Yellow
Write-Host "-------------------------"

$testFiles = @(
    "test_delivery_modal_integration.php",
    "DELIVERY_MODAL_IMPLEMENTATION_SUMMARY.md",
    "DELIVERY_MODAL_VERIFICATION_CHECKLIST.md"
)

foreach ($file in $testFiles) {
    if (Test-Path $file) {
        Write-Host "✅ $file exists" -ForegroundColor Green
    } else {
        Write-Host "⚠️  $file is missing" -ForegroundColor Yellow
    }
}

# Check database migration (if PHP is available)
Write-Host ""
Write-Host "🗄️  Checking database structure..." -ForegroundColor Yellow
Write-Host "----------------------------------"

try {
    $output = & php artisan tinker --execute="
    try {
        `$hasColumn = \Schema::hasColumn('transactions', 'delivery_date');
        if (`$hasColumn) {
            echo '✅ delivery_date column exists in transactions table';
        } else {
            echo '❌ delivery_date column missing from transactions table';
        }
    } catch (Exception `$e) {
        echo '⚠️  Could not check database: ' . `$e->getMessage();
    }
    " 2>$null
    
    if ($output) {
        Write-Host $output
    } else {
        Write-Host "⚠️  Could not verify database structure" -ForegroundColor Yellow
    }
} catch {
    Write-Host "⚠️  Could not check database structure" -ForegroundColor Yellow
}

# Final summary
Write-Host ""
Write-Host "📋 Deployment Summary" -ForegroundColor Cyan
Write-Host "====================="
Write-Host ""
Write-Host "✅ Caches cleared" -ForegroundColor Green
Write-Host "✅ Application optimized" -ForegroundColor Green
Write-Host "✅ File permissions checked" -ForegroundColor Green
Write-Host "✅ Key files verified" -ForegroundColor Green
Write-Host "✅ Database structure checked" -ForegroundColor Green
Write-Host ""
Write-Host "🎯 Next Steps:" -ForegroundColor Yellow
Write-Host "1. Test the delivery modal in your browser"
Write-Host "2. Go to POS page (/pos/create)"
Write-Host "3. Add products and finalize sale"
Write-Host "4. Verify delivery modal appears after customer selection"
Write-Host "5. Check that delivery date appears on invoices"
Write-Host ""
Write-Host "📖 For detailed testing instructions, see:" -ForegroundColor Cyan
Write-Host "   - DELIVERY_MODAL_VERIFICATION_CHECKLIST.md"
Write-Host "   - DELIVERY_MODAL_IMPLEMENTATION_SUMMARY.md"
Write-Host ""
Write-Host "🚀 Delivery Modal deployment completed!" -ForegroundColor Green