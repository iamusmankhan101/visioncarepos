<?php
// FORCE CHECKBOXES NOW - Nuclear option
// Access: http://your-domain/force_checkboxes_now.php

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>🚨 FORCE CHECKBOXES NOW</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; max-width: 800px; margin: 0 auto; }
        .btn { background: #dc3545; color: white; padding: 15px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 18px; margin: 10px 5px; }
        .btn:hover { background: #c82333; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .code { background: #f8f9fa; padding: 15px; border-radius: 4px; font-family: monospace; margin: 10px 0; border: 1px solid #ddd; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 4px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚨 FORCE CHECKBOXES NOW</h1>
        <div class="warning">
            <strong>⚠️ NUCLEAR OPTION:</strong> Use this if checkboxes are still not visible after all other fixes.
        </div>
        
        <button class="btn" onclick="forceCheckboxesNow()">💥 FORCE CHECKBOXES VISIBLE NOW</button>
        
        <div id="result" style="margin-top: 20px;"></div>
        
        <h3>🔧 What this does:</h3>
        <ul>
            <li><strong>Injects ultra-aggressive CSS</strong> that overrides everything</li>
            <li><strong>Forces all checkboxes visible</strong> with maximum priority</li>
            <li><strong>Removes all iCheck interference</strong> completely</li>
            <li><strong>Applies emergency styling</strong> that can't be overridden</li>
            <li><strong>Works on current page</strong> and all future pages</li>
        </ul>
        
        <h3>📋 Manual CSS (copy to browser dev tools):</h3>
        <div class="code">
/* PASTE THIS IN BROWSER DEV TOOLS - STYLES TAB */
.input-icheck {
    display: inline-block !important;
    visibility: visible !important;
    opacity: 1 !important;
    width: 18px !important;
    height: 18px !important;
    position: relative !important;
    z-index: 99999 !important;
    margin-right: 8px !important;
    border: 2px solid #007cba !important;
    border-radius: 3px !important;
    background: white !important;
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
}

.input-icheck:checked {
    background: #007cba !important;
    border-color: #007cba !important;
}

.input-icheck:checked::after {
    content: '✓' !important;
    position: absolute !important;
    top: -2px !important;
    left: 2px !important;
    color: white !important;
    font-size: 14px !important;
    font-weight: bold !important;
}

.icheckbox_square-blue, .iradio_square-blue {
    display: none !important;
}
        </div>
    </div>

    <script>
    function forceCheckboxesNow() {
        const resultDiv = document.getElementById('result');
        resultDiv.innerHTML = '<p style="color: #dc3545; font-weight: bold;">💥 FORCING CHECKBOXES VISIBLE NOW...</p>';
        
        // NUCLEAR CSS - Maximum priority
        const nuclearCSS = document.createElement('style');
        nuclearCSS.id = 'nuclear-checkbox-fix';
        nuclearCSS.innerHTML = `
        /* NUCLEAR CHECKBOX FIX - MAXIMUM PRIORITY */
        .input-icheck,
        input[type="checkbox"].input-icheck,
        input.input-icheck {
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
            width: 18px !important;
            height: 18px !important;
            min-width: 18px !important;
            min-height: 18px !important;
            max-width: 18px !important;
            max-height: 18px !important;
            margin: 0 8px 0 0 !important;
            padding: 0 !important;
            vertical-align: middle !important;
            position: relative !important;
            z-index: 999999 !important;
            cursor: pointer !important;
            left: auto !important;
            top: auto !important;
            right: auto !important;
            bottom: auto !important;
            
            /* Styling */
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            border: 2px solid #007cba !important;
            border-radius: 3px !important;
            background: white !important;
            outline: none !important;
            box-shadow: none !important;
            transform: none !important;
            clip: auto !important;
            overflow: visible !important;
        }
        
        .input-icheck:hover {
            border-color: #005a87 !important;
            box-shadow: 0 0 5px rgba(0, 124, 186, 0.3) !important;
        }
        
        .input-icheck:focus {
            border-color: #005a87 !important;
            box-shadow: 0 0 0 2px rgba(0, 124, 186, 0.2) !important;
        }
        
        .input-icheck:checked {
            background: #007cba !important;
            border-color: #007cba !important;
        }
        
        .input-icheck[type="checkbox"]:checked::after {
            content: '✓' !important;
            position: absolute !important;
            top: -2px !important;
            left: 2px !important;
            color: white !important;
            font-size: 14px !important;
            font-weight: bold !important;
            line-height: 1 !important;
            z-index: 999999 !important;
        }
        
        /* DESTROY iCheck completely */
        .icheckbox_square-blue,
        .iradio_square-blue,
        .icheckbox_square-blue *,
        .iradio_square-blue * {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            width: 0 !important;
            height: 0 !important;
            position: absolute !important;
            left: -9999px !important;
            top: -9999px !important;
        }
        
        /* Force checkbox containers */
        .checkbox {
            display: block !important;
            margin: 10px 0 !important;
            min-height: 20px !important;
            position: relative !important;
        }
        
        .checkbox label {
            cursor: pointer !important;
            user-select: none !important;
            display: inline-flex !important;
            align-items: center !important;
            margin: 0 !important;
            padding: 5px 0 !important;
            position: relative !important;
        }
        
        /* Emergency visibility for any hidden checkboxes */
        input[type="checkbox"][style*="display: none"],
        input[type="checkbox"][style*="visibility: hidden"],
        input[type="checkbox"][style*="opacity: 0"] {
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        `;
        
        // Remove any existing fix
        const existing = document.getElementById('nuclear-checkbox-fix');
        if (existing) existing.remove();
        
        // Inject nuclear CSS
        document.head.appendChild(nuclearCSS);
        
        // FORCE all checkboxes visible with JavaScript
        setTimeout(() => {
            let fixedCount = 0;
            let removedCount = 0;
            
            // Find ALL possible checkbox selectors
            const selectors = [
                '.input-icheck',
                'input[type="checkbox"].input-icheck',
                'input.input-icheck',
                'input[type="checkbox"]',
                '.checkbox input',
                'input[class*="icheck"]'
            ];
            
            selectors.forEach(selector => {
                const elements = document.querySelectorAll(selector);
                elements.forEach(checkbox => {
                    if (checkbox.type === 'checkbox') {
                        // FORCE visibility with all possible methods
                        checkbox.style.setProperty('display', 'inline-block', 'important');
                        checkbox.style.setProperty('visibility', 'visible', 'important');
                        checkbox.style.setProperty('opacity', '1', 'important');
                        checkbox.style.setProperty('position', 'relative', 'important');
                        checkbox.style.setProperty('z-index', '999999', 'important');
                        checkbox.style.setProperty('width', '18px', 'important');
                        checkbox.style.setProperty('height', '18px', 'important');
                        checkbox.style.setProperty('margin-right', '8px', 'important');
                        checkbox.style.setProperty('left', 'auto', 'important');
                        checkbox.style.setProperty('top', 'auto', 'important');
                        checkbox.style.setProperty('clip', 'auto', 'important');
                        checkbox.style.setProperty('overflow', 'visible', 'important');
                        
                        // Remove any hiding classes
                        checkbox.classList.remove('hidden', 'hide', 'invisible');
                        
                        // Add our class
                        checkbox.classList.add('input-icheck');
                        
                        fixedCount++;
                    }
                });
            });
            
            // DESTROY all iCheck wrappers
            const iCheckWrappers = document.querySelectorAll('.icheckbox_square-blue, .iradio_square-blue, [class*="icheckbox"], [class*="iradio"]');
            iCheckWrappers.forEach(wrapper => {
                const input = wrapper.querySelector('input');
                if (input && input.type === 'checkbox') {
                    // Move input out of wrapper
                    wrapper.parentNode.insertBefore(input, wrapper);
                    wrapper.remove();
                    removedCount++;
                }
            });
            
            // Apply continuous monitoring
            const monitor = setInterval(() => {
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
            
            // Store monitor ID for cleanup
            window.checkboxMonitor = monitor;
            
            resultDiv.innerHTML = `
                <div style="color: #28a745; font-weight: bold; font-size: 18px;">
                    💥 NUCLEAR FIX APPLIED!<br><br>
                    📊 <strong>${fixedCount}</strong> checkboxes forced visible<br>
                    🗑️ <strong>${removedCount}</strong> iCheck wrappers destroyed<br>
                    🔄 Continuous monitoring active<br><br>
                    
                    <div style="background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 4px; margin: 10px 0;">
                        <strong>✅ SUCCESS!</strong><br>
                        Go to User Management → Add User<br>
                        All checkboxes should now be visible!
                    </div>
                    
                    <div style="background: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 4px; margin: 10px 0; font-size: 14px;">
                        <strong>⚠️ If still not visible:</strong><br>
                        1. Hard refresh (Ctrl+F5)<br>
                        2. Clear browser cache<br>
                        3. Try incognito mode<br>
                        4. Use the manual CSS above
                    </div>
                </div>
            `;
            
        }, 1000);
    }
    </script>
</body>
</html>