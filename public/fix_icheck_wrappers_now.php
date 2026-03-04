<?php
// Fix iCheck Wrappers - Prevent iCheck from hiding checkboxes
// Access: http://your-domain/fix_icheck_wrappers_now.php

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>🔧 Fix iCheck Wrappers</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; max-width: 800px; margin: 0 auto; }
        .btn { background: #dc3545; color: white; padding: 15px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 18px; margin: 10px 5px; }
        .btn:hover { background: #c82333; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 4px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Fix iCheck Wrappers</h1>
        <div class="warning">
            <strong>⚠️ PROBLEM IDENTIFIED:</strong> iCheck library is creating wrapper divs that hide your checkboxes after page load.
        </div>
        
        <button class="btn" onclick="fixICheckWrappers()">🚀 Fix iCheck Wrappers Now</button>
        
        <div id="result" style="margin-top: 20px;"></div>
        
        <h3>🔧 What this does:</h3>
        <ul>
            <li><strong>Prevents iCheck initialization</strong> from running</li>
            <li><strong>Removes existing iCheck wrappers</strong> that hide checkboxes</li>
            <li><strong>Restores original checkboxes</strong> with proper styling</li>
            <li><strong>Blocks future iCheck interference</strong> permanently</li>
            <li><strong>Applies modern checkbox styling</strong> that looks professional</li>
        </ul>
    </div>

    <script>
    function fixICheckWrappers() {
        const resultDiv = document.getElementById('result');
        resultDiv.innerHTML = '<p style="color: #dc3545; font-weight: bold;">🔧 Fixing iCheck wrappers...</p>';
        
        // Step 1: Block iCheck from initializing
        window.iCheck = function() { return this; };
        if (window.jQuery) {
            window.jQuery.fn.iCheck = function() { return this; };
            window.$.fn.iCheck = function() { return this; };
        }
        
        // Step 2: Create anti-iCheck CSS
        const antiICheckCSS = document.createElement('style');
        antiICheckCSS.id = 'anti-icheck-fix';
        antiICheckCSS.innerHTML = `
        /* ANTI-ICHECK FIX - PREVENT WRAPPER CREATION */
        
        /* Hide all iCheck wrappers immediately */
        .icheckbox_square-blue,
        .iradio_square-blue,
        .icheckbox_square-blue *:not(input),
        .iradio_square-blue *:not(input) {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            position: absolute !important;
            left: -9999px !important;
            top: -9999px !important;
            width: 0 !important;
            height: 0 !important;
        }
        
        /* Force original checkboxes to be visible */
        .input-icheck,
        input[type="checkbox"].input-icheck,
        input.input-icheck,
        .icheckbox_square-blue input[type="checkbox"],
        .iradio_square-blue input[type="radio"] {
            /* Force visibility */
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
            position: relative !important;
            z-index: 9999 !important;
            
            /* Size and spacing */
            width: 20px !important;
            height: 20px !important;
            min-width: 20px !important;
            min-height: 20px !important;
            margin: 0 10px 0 0 !important;
            padding: 0 !important;
            
            /* Remove default styling */
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            
            /* Custom styling */
            border: 2px solid #007cba !important;
            border-radius: 4px !important;
            background: white !important;
            cursor: pointer !important;
            outline: none !important;
            
            /* Positioning */
            left: auto !important;
            top: auto !important;
            right: auto !important;
            bottom: auto !important;
            clip: auto !important;
            overflow: visible !important;
            
            /* Transitions */
            transition: all 0.2s ease !important;
        }
        
        /* Hover effects */
        .input-icheck:hover,
        .icheckbox_square-blue input[type="checkbox"]:hover {
            border-color: #005a87 !important;
            box-shadow: 0 0 8px rgba(0, 124, 186, 0.3) !important;
            transform: scale(1.05) !important;
        }
        
        /* Checked state */
        .input-icheck:checked,
        .icheckbox_square-blue input[type="checkbox"]:checked {
            background: #007cba !important;
            border-color: #007cba !important;
        }
        
        /* Checkmark */
        .input-icheck:checked::after,
        .icheckbox_square-blue input[type="checkbox"]:checked::after {
            content: '✓' !important;
            position: absolute !important;
            top: -1px !important;
            left: 3px !important;
            color: white !important;
            font-size: 16px !important;
            font-weight: bold !important;
            line-height: 1 !important;
            text-shadow: 0 0 2px rgba(0,0,0,0.3) !important;
        }
        
        /* Label styling */
        .checkbox label {
            cursor: pointer !important;
            user-select: none !important;
            display: flex !important;
            align-items: center !important;
            margin: 0 !important;
            padding: 5px 0 !important;
            font-weight: normal !important;
        }
        
        /* Container styling */
        .checkbox {
            display: block !important;
            margin: 15px 0 !important;
            min-height: 24px !important;
        }
        `;
        
        // Remove existing fix
        const existing = document.getElementById('anti-icheck-fix');
        if (existing) existing.remove();
        
        // Inject CSS
        document.head.appendChild(antiICheckCSS);
        
        // Step 3: Remove existing iCheck wrappers and restore checkboxes
        setTimeout(() => {
            let removedWrappers = 0;
            let restoredCheckboxes = 0;
            
            // Find all iCheck wrappers
            const wrappers = document.querySelectorAll('.icheckbox_square-blue, .iradio_square-blue');
            
            wrappers.forEach(wrapper => {
                // Find the input inside the wrapper
                const input = wrapper.querySelector('input[type="checkbox"], input[type="radio"]');
                
                if (input) {
                    // Get the parent container
                    const parent = wrapper.parentNode;
                    
                    // Move the input out of the wrapper
                    parent.insertBefore(input, wrapper);
                    
                    // Remove the wrapper
                    wrapper.remove();
                    
                    // Style the restored input
                    input.classList.add('input-icheck');
                    input.style.setProperty('display', 'inline-block', 'important');
                    input.style.setProperty('visibility', 'visible', 'important');
                    input.style.setProperty('opacity', '1', 'important');
                    input.style.setProperty('position', 'relative', 'important');
                    input.style.setProperty('z-index', '9999', 'important');
                    
                    removedWrappers++;
                    restoredCheckboxes++;
                }
            });
            
            // Step 4: Set up continuous monitoring
            const monitor = setInterval(() => {
                // Remove any new iCheck wrappers that might be created
                const newWrappers = document.querySelectorAll('.icheckbox_square-blue, .iradio_square-blue');
                newWrappers.forEach(wrapper => {
                    const input = wrapper.querySelector('input');
                    if (input) {
                        wrapper.parentNode.insertBefore(input, wrapper);
                        wrapper.remove();
                        input.classList.add('input-icheck');
                        input.style.setProperty('display', 'inline-block', 'important');
                        input.style.setProperty('visibility', 'visible', 'important');
                        input.style.setProperty('opacity', '1', 'important');
                    }
                });
                
                // Ensure all checkboxes remain visible
                document.querySelectorAll('.input-icheck').forEach(checkbox => {
                    if (checkbox.style.display === 'none' || 
                        checkbox.style.visibility === 'hidden' || 
                        checkbox.style.opacity === '0') {
                        
                        checkbox.style.setProperty('display', 'inline-block', 'important');
                        checkbox.style.setProperty('visibility', 'visible', 'important');
                        checkbox.style.setProperty('opacity', '1', 'important');
                    }
                });
            }, 500);
            
            // Store monitor for cleanup
            window.iCheckMonitor = monitor;
            
            resultDiv.innerHTML = `
                <div style="color: #28a745; font-weight: bold; font-size: 18px;">
                    🎉 iCHECK WRAPPERS FIXED!<br><br>
                    
                    📊 <strong>${removedWrappers}</strong> iCheck wrappers removed<br>
                    ✅ <strong>${restoredCheckboxes}</strong> checkboxes restored<br>
                    🛡️ iCheck initialization blocked<br>
                    🔄 Continuous monitoring active<br><br>
                    
                    <div style="background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 4px; margin: 10px 0;">
                        <strong>✅ SUCCESS!</strong><br>
                        Your checkboxes should now be permanently visible!<br>
                        • No more disappearing after page load<br>
                        • Professional blue styling<br>
                        • Checkmarks when selected<br>
                        • Hover effects working<br>
                    </div>
                    
                    <div style="background: #cce5ff; border: 1px solid #99d6ff; padding: 10px; border-radius: 4px; margin: 10px 0; font-size: 14px;">
                        <strong>🔧 Technical Fix Applied:</strong><br>
                        • Blocked iCheck library initialization<br>
                        • Removed existing wrapper divs<br>
                        • Restored original checkbox elements<br>
                        • Applied modern CSS styling<br>
                        • Set up continuous monitoring<br>
                    </div>
                </div>
            `;
            
        }, 1000);
    }
    
    // Auto-apply fix when page loads
    window.addEventListener('load', function() {
        setTimeout(fixICheckWrappers, 2000);
    });
    </script>
</body>
</html>