<?php
/**
 * Fix TinyMCE 404 Errors and Checkbox Issues
 */

echo "🔧 Fixing TinyMCE 404 errors and checkbox issues...\n\n";

// The issue is that TinyMCE is still loading from vendor.js with hardcoded paths
// We need to override the TinyMCE configuration more aggressively

// Create a TinyMCE override script
$tinymceOverride = "
// TinyMCE Path Override - Fix 404 errors
if (typeof tinymce !== 'undefined') {
    // Override the default paths before any initialization
    tinymce.baseURL = (typeof base_path !== 'undefined' ? base_path : '') + '/js';
    tinymce.suffix = '.min';
    
    // Set global defaults
    tinymce.overrideDefaults({
        base_url: (typeof base_path !== 'undefined' ? base_path : '') + '/js',
        skin_url: (typeof base_path !== 'undefined' ? base_path : '') + '/js/skins/ui/oxide',
        content_css: (typeof base_path !== 'undefined' ? base_path : '') + '/js/skins/content/default/content.min.css',
        theme: 'silver',
        height: 300
    });
}

// Checkbox fix for business settings
$(document).ready(function() {
    // Force checkbox visibility and functionality
    $('.icheckbox_square-blue, .iradio_square-blue').css({
        'display': 'inline-block !important',
        'visibility': 'visible !important',
        'opacity': '1 !important'
    });
    
    // Re-initialize iCheck if it exists
    if (typeof $.fn.iCheck !== 'undefined') {
        $('input[type=\"checkbox\"], input[type=\"radio\"]').iCheck('destroy').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue'
        });
    }
});
";

// Write the override script
file_put_contents('public/js/tinymce-checkbox-fix.js', $tinymceOverride);
echo "✅ Created TinyMCE and checkbox override script\n";

echo "\n🎉 Fix completed!\n";
echo "📝 Next steps:\n";
echo "1. Add the script to your layout: <script src=\"/js/tinymce-checkbox-fix.js\"></script>\n";
echo "2. Clear browser cache\n";
echo "3. Test TinyMCE editors and checkboxes\n";
?>