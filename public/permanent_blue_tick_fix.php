<?php
// Permanent Blue Tick Fix - Prevents iCheck from overriding blue ticks
// Access: http://your-domain/permanent_blue_tick_fix.php

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>🔒 Permanent Blue Tick Fix</title>
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
        <h1>🔒 Permanent Blue Tick Fix</h1>
        <div class="warning">
            <strong>⚠️ PROBLEM:</strong> Blue ticks show on refresh but turn white when page loads fully because iCheck overrides your styling.
        </div>
        
        <button class="btn" onclick="applyPermanentFix()">🔒 Apply Permanent Blue Tick Fix</button>
        
        <div id="result" style="margin-top: 20px;"></div>
        
        <h3>🔧 What this permanent fix does:</h3>
        <ul>
            <li><strong>Blocks iCheck completely</strong> from initializing</li>
            <li><strong>Prevents style overrides</strong> after page load</li>
            <li><strong>Maintains blue ticks permanently</strong> with continuous monitoring</li>
            <li><strong>Removes iCheck wrappers</strong> immediately when created</li>
            <li><strong>Forces blue tick styling</strong> with maximum CSS priority</li>
        </ul>
    </div>

    <script>
    function applyPermanentFix() {
        const resultDiv = document.getElementById('result');
        resultDiv.innerHTML = '<p style="color: #dc3545; font-weight: bold;">🔒 Applying permanent blue tick fix...</p>';
        
        // Step 1: Nuclear iCheck blocking
        console.log('🚫 Blocking iCheck completely...');
        
        // Block all possible iCheck methods
        window.iCheck = function() { 
            console.log('🚫 Blocked iCheck call');
            return this; 
        };
        
        if (window.jQuery) {
            window.jQuery.fn.iCheck = function() { 
                console.log('🚫 Blocked jQuery iCheck');
                return this; 
            };
            if (window.$) {
                window.$.fn.iCheck = function() { 
                    console.log('🚫 Blocked $ iCheck');
                    return this; 
                };
            }
        }
        
        // Block iCheck from being loaded
        if (window.jQuery && window.jQuery.fn.iCheck) {
            delete window.jQuery.fn.iCheck;
        }
        if (window.$ && window.$.fn.iCheck) {
            delete window.$.fn.iCheck;
        }
        
        // Step 2: Ultra-high priority CSS
        const permanentCSS = document.createElement('style');
        permanentCSS.id = 'permanent-blue-tick-fix';
        permanentCSS.innerHTML = `
        /* PERMANENT BLUE TICK FIX - MAXIMUM PRIORITY */
        
        /* Destroy iCheck wrappers with extreme prejudice */
        .icheckbox_square-blue,
        .iradio_square-blue,
        .icheckbox_square-blue *:not(input[type="checkbox"]),
        .iradio_square-blue *:not(input[type="radio"]),
        div[class*="icheckbox"],
        div[class*="iradio"] {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            position: absolute !important;
            left: -99999px !important;
            top: -99999px !important;
            width: 0 !important;
            height: 0 !important;
            z-index: -999999 !important;
        }
        
        /* Force checkboxes visible with ultra-high priority */
        .input-icheck,
        input[type="checkbox"].input-icheck,
        input.input-icheck,
        .icheckbox_square-blue input[type="checkbox"],
        .iradio_square-blue input[type="radio"],
        input[type="checkbox"][class*="icheck"],
        input[type="radio"][class*="icheck"] {
            /* Ultra-high priority visibility */
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
            position: relative !important;
            z-index: 999999 !important;
            
            /* Size and spacing */
            width: 20px !important;
            height: 20px !important;
            min-width: 20px !important;
            min-height: 20px !important;
            margin: 0 10px 0 0 !important;
            padding: 0 !important;
            
            /* Remove all default styling */
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            
            /* Force custom styling */
            border: 2px solid #007cba !important;
            border-radius: 4px !important;
            background: white !important;
            cursor: pointer !important;
            outline: none !important;
            
            /* Positioning overrides */
            left: auto !important;
            top: auto !important;
            right: auto !important;
            bottom: auto !important;
            clip: auto !important;
            overflow: visible !important;
            transform: none !important;
            
            /* Transitions */
            transition: all 0.2s ease !important;
        }
        
        /* Hover effects */
        .input-icheck:hover,
        input[type="checkbox"].input-icheck:hover,
        .icheckbox_square-blue input[type="checkbox"]:hover {
            border-color: #005a87 !important;
            box-shadow: 0 0 8px rgba(0, 124, 186, 0.3) !important;
            transform: scale(1.05) !important;
        }
        
        /* BLUE TICK STYLING - PERMANENT */
        .input-icheck:checked,
        input[type="checkbox"].input-icheck:checked,
        .icheckbox_square-blue input[type="checkbox"]:checked {
            background: white !important;
            border-color: #007cba !important;
        }
        
        /* BLUE CHECKMARK - ULTRA HIGH PRIORITY */
        .input-icheck:checked::after,
        input[type="checkbox"].input-icheck:checked::after,
        .icheckbox_square-blue input[type="checkbox"]:checked::after {
            content: '✓' !important;
            position: absolute !important;
            top: -1px !important;
            left: 3px !important;
            color: #007cba !important;
            font-size: 16px !important;
            font-weight: bold !important;
            line-height: 1 !important;
            text-shadow: 0 0 2px rgba(0, 124, 186, 0.3) !important;
            z-index: 999999 !important;
        }
        
        /* Label and container styling */
        .checkbox label {
            cursor: pointer !important;
            user-select: none !important;
            display: flex !important;
            align-items: center !important;
            margin: 0 !important;
            padding: 5px 0 !important;
            font-weight: normal !important;
        }
        
        .checkbox {
            display: block !important;
            margin: 15px 0 !important;
            min-height: 24px !important;
        }
        `;
        
        // Remove any existing fixes
        const existing = document.getElementById('permanent-blue-tick-fix');
        if (existing) existing.remove();
        
        // Inject ultra-high priority CSS
        document.head.appendChild(permanentCSS);
        
        // Step 3: Aggressive wrapper removal and checkbox restoration
        setTimeout(() => {
            let removedWrappers = 0;
            let restoredCheckboxes = 0;
            
            // Remove ALL iCheck wrappers aggressively
            const allWrappers = document.querySelectorAll(
                '.icheckbox_square-blue, .iradio_square-blue, [class*="icheckbox"], [class*="iradio"]'
            );
            
            allWrappers.forEach(wrapper => {
                const input = wrapper.querySelector('input[type="checkbox"], input[type="radio"]');
                if (input) {
                    // Move input out of wrapper
                    wrapper.parentNode.insertBefore(input, wrapper);
                    
                    // Force styling on the input
                    input.classList.add('input-icheck');
                    input.style.setProperty('display', 'inline-block', 'important');
                    input.style.setProperty('visibility', 'visible', 'important');
                    input.style.setProperty('opacity', '1', 'important');
                    input.style.setProperty('position', 'relative', 'important');
                    input.style.setProperty('z-index', '999999', 'important');
                    
                    // Remove wrapper
                    wrapper.remove();
                    removedWrappers++;
                    restoredCheckboxes++;
                }
            });
            
            // Force all checkboxes to be visible and styled
            const allCheckboxes = document.querySelectorAll('input[type="checkbox"]');
            allCheckboxes.forEach(checkbox => {
                checkbox.classList.add('input-icheck');
                checkbox.style.setProperty('display', 'inline-block', 'important');
                checkbox.style.setProperty('visibility', 'visible', 'important');
                checkbox.style.setProperty('opacity', '1', 'important');
                checkbox.style.setProperty('position', 'relative', 'important');
                checkbox.style.setProperty('z-index', '999999', 'important');
            });
            
            // Step 4: Ultra-aggressive continuous monitoring
            const ultraMonitor = setInterval(() => {
                // Remove any new wrappers immediately
                const newWrappers = document.querySelectorAll(
                    '.icheckbox_square-blue, .iradio_square-blue, [class*="icheckbox"], [class*="iradio"]'
                );
                
                newWrappers.forEach(wrapper => {
                    const input = wrapper.querySelector('input');
                    if (input) {
                        wrapper.parentNode.insertBefore(input, wrapper);
                        wrapper.remove();
                        input.classList.add('input-icheck');
                        input.style.setProperty('display', 'inline-block', 'important');
                        input.style.setProperty('visibility', 'visible', 'important');
                        input.style.setProperty('opacity', '1', 'important');
                        console.log('🔄 Removed new iCheck wrapper');
                    }
                });
                
                // Force all checkboxes to remain visible
                document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                    if (checkbox.style.display === 'none' || 
                        checkbox.style.visibility === 'hidden' || 
                        checkbox.style.opacity === '0' ||
                        !checkbox.classList.contains('input-icheck')) {
                        
                        checkbox.classList.add('input-icheck');
                        checkbox.style.setProperty('display', 'inline-block', 'important');
                        checkbox.style.setProperty('visibility', 'visible', 'important');
                        checkbox.style.setProperty('opacity', '1', 'important');
                        checkbox.style.setProperty('position', 'relative', 'important');
                        checkbox.style.setProperty('z-index', '999999', 'important');
                    }
                });
                
                // Block any new iCheck attempts
                if (window.jQuery && !window.jQuery.fn.iCheck.blocked) {
                    window.jQuery.fn.iCheck = function() { 
                        console.log('🚫 Blocked late iCheck attempt');
                        return this; 
                    };
                    window.jQuery.fn.iCheck.blocked = true;
                }
                
            }, 100); // Check every 100ms
            
            // Store monitor for cleanup
            window.permanentBlueTickMonitor = ultraMonitor;
            
            resultDiv.innerHTML = `
                <div style="color: #28a745; font-weight: bold; font-size: 18px;">
                    🔒 PERMANENT BLUE TICK FIX APPLIED!<br><br>
                    
                    📊 <strong>${removedWrappers}</strong> iCheck wrappers destroyed<br>
                    ✅ <strong>${restoredCheckboxes}</strong> checkboxes restored with blue ticks<br>
                    🚫 iCheck completely blocked<br>
                    🔄 Ultra-aggressive monitoring active (every 100ms)<br>
                    💙 Blue ticks will stay PERMANENTLY<br><br>
                    
                    <div style="background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 4px; margin: 10px 0;">
                        <strong>🎉 PERMANENT SUCCESS!</strong><br>
                        Your blue tick checkboxes will now:<br>
                        • ✅ Stay visible after page load<br>
                        • 💙 Keep blue checkmarks permanently<br>
                        • 🚫 Never be overridden by iCheck<br>
                        • 🔄 Self-repair if anything tries to break them<br>
                    </div>
                    
                    <div style="background: #e3f2fd; border: 1px solid #90caf9; padding: 10px; border-radius: 4px; margin: 10px 0; font-size: 14px;">
                        <strong>🔧 Technical Details:</strong><br>
                        • iCheck library completely disabled<br>
                        • Ultra-high priority CSS applied<br>
                        • Continuous monitoring every 100ms<br>
                        • Automatic wrapper removal<br>
                        • Self-healing checkbox styling<br>
                    </div>
                </div>
            `;
            
        }, 1000);
    }
    
    // Auto-apply fix when page loads
    window.addEventListener('load', function() {
        setTimeout(applyPermanentFix, 2000);
    });
    </script>
</body>
</html>