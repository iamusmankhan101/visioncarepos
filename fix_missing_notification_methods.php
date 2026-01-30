<?php
/**
 * Add missing notification methods to HomeController
 */

echo "🔧 Adding missing notification methods to HomeController...\n\n";

$controller_path = 'app/Http/Controllers/HomeController.php';

if (!file_exists($controller_path)) {
    echo "❌ HomeController not found at $controller_path\n";
    exit(1);
}

$controller_content = file_get_contents($controller_path);

// Check if getTotalUnreadNotifications method exists
if (strpos($controller_content, 'getTotalUnreadNotifications') !== false) {
    echo "✅ getTotalUnreadNotifications method already exists\n";
} else {
    echo "🔧 Adding getTotalUnreadNotifications method...\n";
    
    // Find the end of the class (before the last closing brace)
    $last_brace_pos = strrpos($controller_content, '}');
    
    if ($last_brace_pos === false) {
        echo "❌ Could not find class closing brace\n";
        exit(1);
    }
    
    $method_code = '
    /**
     * Get total unread notifications for the authenticated user
     */
    public function getTotalUnreadNotifications()
    {
        try {
            $total_unread = auth()->user()->unreadNotifications()->count();
            
            return response()->json([
                \'total_unread\' => $total_unread
            ]);
        } catch (\Exception $e) {
            return response()->json([
                \'total_unread\' => 0,
                \'error\' => $e->getMessage()
            ]);
        }
    }

    /**
     * Load more notifications for the authenticated user
     */
    public function loadMoreNotifications(Request $request)
    {
        try {
            $page = $request->get(\'page\', 1);
            $per_page = 10;
            
            $notifications = auth()->user()
                ->notifications()
                ->paginate($per_page, [\'*\'], \'page\', $page);
            
            $html = \'\';
            foreach ($notifications as $notification) {
                $html .= \'<li class="notification-li">\';
                $html .= \'<a href="#" class="show-notification-in-popup" data-href="/show-notification/\' . $notification->id . \'">\';
                $html .= \'<div class="notification-content">\';
                $html .= \'<span class="notification-text">\' . ($notification->data[\'message\'] ?? \'New notification\') . \'</span>\';
                $html .= \'<span class="notification-time">\' . $notification->created_at->diffForHumans() . \'</span>\';
                $html .= \'</div>\';
                $html .= \'</a>\';
                $html .= \'</li>\';
            }
            
            if ($notifications->isEmpty()) {
                $html = \'<li class="no-notification">No more notifications</li>\';
            }
            
            return response($html);
        } catch (\Exception $e) {
            return response(\'<li class="no-notification">Error loading notifications</li>\');
        }
    }

    /**
     * Show a specific notification
     */
    public function showNotification($id)
    {
        try {
            $notification = auth()->user()->notifications()->findOrFail($id);
            
            // Mark as read
            $notification->markAsRead();
            
            return response()->json([
                \'success\' => true,
                \'notification\' => $notification
            ]);
        } catch (\Exception $e) {
            return response()->json([
                \'success\' => false,
                \'error\' => $e->getMessage()
            ]);
        }
    }

';
    
    // Insert the method before the last closing brace
    $new_content = substr($controller_content, 0, $last_brace_pos) . $method_code . substr($controller_content, $last_brace_pos);
    
    // Write the updated content
    if (file_put_contents($controller_path, $new_content)) {
        echo "✅ Successfully added notification methods to HomeController\n";
    } else {
        echo "❌ Failed to write to HomeController\n";
        exit(1);
    }
}

// Check if loadMoreNotifications method exists
if (strpos($controller_content, 'loadMoreNotifications') !== false) {
    echo "✅ loadMoreNotifications method already exists\n";
} else {
    echo "ℹ️  loadMoreNotifications method was added with getTotalUnreadNotifications\n";
}

// Check if showNotification method exists
if (strpos($controller_content, 'showNotification') !== false) {
    echo "✅ showNotification method already exists\n";
} else {
    echo "ℹ️  showNotification method was added with getTotalUnreadNotifications\n";
}

echo "\n🎉 Notification methods fix completed!\n";
echo "\nAdded methods:\n";
echo "- getTotalUnreadNotifications() - Returns count of unread notifications\n";
echo "- loadMoreNotifications() - Loads paginated notifications\n";
echo "- showNotification() - Shows and marks notification as read\n";
?>