<?php

// Direct test of business selection without middleware
require_once 'vendor/autoload.php';

// Create a simple test route
$testRoute = "
// Test route for business selection (add to routes/web.php temporarily)
Route::get('/test-business-select', function() {
    try {
        \$user = auth()->user();
        if (!\$user) {
            return 'User not authenticated';
        }
        
        \$businesses = \App\Business::where('is_active', 1)
            ->where('owner_id', \$user->id)
            ->get();
            
        return view('business.select', ['available_businesses' => \$businesses]);
    } catch (\Exception \$e) {
        return 'Error: ' . \$e->getMessage() . '<br>Stack: ' . \$e->getTraceAsString();
    }
})->middleware('auth');
";

echo "Business Selection Direct Test Route\n";
echo "===================================\n\n";
echo "Add this test route to routes/web.php:\n\n";
echo $testRoute;
echo "\n\nThen visit: https://pos.digitrot.com/test-business-select\n";
echo "This will bypass the middleware and test the controller directly.\n\n";

// Also create a minimal controller test
$minimalController = '<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestBusinessController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }

    public function test()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response("User not authenticated", 401);
            }
            
            return response("Business selection test successful. User: " . $user->username);
            
        } catch (\Exception $e) {
            return response("Error: " . $e->getMessage(), 500);
        }
    }
}';

file_put_contents('app/Http/Controllers/TestBusinessController.php', $minimalController);
echo "Created TestBusinessController for debugging.\n";
echo "Add this route to test: Route::get('/test-business', [TestBusinessController::class, 'test'])->middleware('auth');\n";