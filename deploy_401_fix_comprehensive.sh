#!/bin/bash

echo "🚀 Deploying comprehensive 401 Unauthorized fix..."

# Step 1: Add missing notification methods to HomeController
echo "📝 Step 1: Adding missing notification methods..."

# Create a backup of HomeController
cp app/Http/Controllers/HomeController.php app/Http/Controllers/HomeController.php.backup

# Add the missing methods to HomeController
cat >> app/Http/Controllers/HomeController.php << 'EOF'

    /**
     * Get total unread notifications for the authenticated user
     */
    public function getTotalUnreadNotifications()
    {
        try {
            if (!auth()->check()) {
                return response()->json(['total_unread' => 0, 'error' => 'Not authenticated'], 401);
            }
            
            $total_unread = auth()->user()->unreadNotifications()->count();
            
            return response()->json([
                'total_unread' => $total_unread
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'total_unread' => 0,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Load more notifications for the authenticated user
     */
    public function loadMoreNotifications(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response('<li class="no-notification">Please login to view notifications</li>', 401);
            }
            
            $page = $request->get('page', 1);
            $per_page = 10;
            
            $notifications = auth()->user()
                ->notifications()
                ->paginate($per_page, ['*'], 'page', $page);
            
            $html = '';
            foreach ($notifications as $notification) {
                $html .= '<li class="notification-li">';
                $html .= '<a href="#" class="show-notification-in-popup" data-href="/show-notification/' . $notification->id . '">';
                $html .= '<div class="notification-content">';
                $html .= '<span class="notification-text">' . ($notification->data['message'] ?? 'New notification') . '</span>';
                $html .= '<span class="notification-time">' . $notification->created_at->diffForHumans() . '</span>';
                $html .= '</div>';
                $html .= '</a>';
                $html .= '</li>';
            }
            
            if ($notifications->isEmpty()) {
                $html = '<li class="no-notification">No more notifications</li>';
            }
            
            return response($html);
        } catch (\Exception $e) {
            return response('<li class="no-notification">Error loading notifications</li>', 500);
        }
    }

    /**
     * Show a specific notification
     */
    public function showNotification($id)
    {
        try {
            if (!auth()->check()) {
                return response()->json(['success' => false, 'error' => 'Not authenticated'], 401);
            }
            
            $notification = auth()->user()->notifications()->findOrFail($id);
            
            // Mark as read
            $notification->markAsRead();
            
            return response()->json([
                'success' => true,
                'notification' => $notification
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
EOF

echo "✅ Added notification methods to HomeController"

# Step 2: Clear all caches
echo "📦 Step 2: Clearing all caches..."
rm -rf storage/framework/views/*.php 2>/dev/null || true
rm -rf storage/framework/sessions/* 2>/dev/null || true
rm -f bootstrap/cache/config.php 2>/dev/null || true
rm -f bootstrap/cache/routes.php 2>/dev/null || true
rm -f bootstrap/cache/services.php 2>/dev/null || true
echo "✅ Caches cleared"

# Step 3: Set proper permissions
echo "🔐 Step 3: Setting permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
echo "✅ Permissions set"

# Step 4: Check authentication middleware
echo "🔍 Step 4: Checking authentication setup..."
if grep -q "middleware('auth')" routes/web.php; then
    echo "✅ Auth middleware found in routes"
else
    echo "⚠️  Auth middleware might be missing"
fi

# Step 5: Create a test endpoint for debugging
echo "🧪 Step 5: Creating debug endpoint..."
cat > public/auth_test.php << 'EOF'
<?php
session_start();

echo "<h2>Authentication Debug</h2>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Session Status:</strong> " . (session_status() === PHP_SESSION_ACTIVE ? "Active" : "Inactive") . "</p>";
echo "<p><strong>Current Time:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>Server:</strong> " . $_SERVER['HTTP_HOST'] . "</p>";
echo "<p><strong>Protocol:</strong> " . (isset($_SERVER['HTTPS']) ? 'HTTPS' : 'HTTP') . "</p>";

echo "<h3>Session Data:</h3>";
echo "<pre>" . print_r($_SESSION, true) . "</pre>";

echo "<h3>Cookies:</h3>";
echo "<pre>" . print_r($_COOKIE, true) . "</pre>";

echo "<h3>Instructions:</h3>";
echo "<ul>";
echo "<li>If you see session data, authentication should work</li>";
echo "<li>If session is empty, there might be a login issue</li>";
echo "<li>Check that cookies are being set properly</li>";
echo "</ul>";
?>
EOF
echo "✅ Created auth test at /auth_test.php"

echo ""
echo "🎉 401 fix deployment completed!"
echo ""
echo "Summary of changes:"
echo "- Added missing notification methods to HomeController"
echo "- Cleared all Laravel caches"
echo "- Set proper storage permissions"
echo "- Created authentication debug endpoint"
echo ""
echo "Next steps:"
echo "1. Try accessing the application again"
echo "2. If still getting 401 errors, visit https://pos.digitrot.com/auth_test.php"
echo "3. Check browser developer tools for authentication cookies"
echo "4. Verify user is properly logged in"