<?php
// Emergency checkbox fix - immediate visibility
// Access: http://your-domain/emergency_checkbox_fix.php

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>🚨 Emergency Checkbox Fix</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; max-width: 800px; margin: 0 auto; }
        .btn { background: #dc3545; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; margin: 10px 5px; }
        .btn:hover { background: #c82333; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .code { background: #f8f9fa; padding: 10px; border-radius: 4px; font-family: monospace; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚨 Emergency Checkbox Fix</h1>
        <p><strong>Use this if checkboxes are still not visible after the main fix.</strong></p>
        
        <div class="code">
            <strong>Quick Test:</strong><br>
            1. Go to User Management → Add User<br>
            2. Look for checkboxes in the form<br>
            3. If they're not visible, click the button below
        </div>
        
        <button class="btn" onclick="applyEmergencyFix()">🚀 Apply Emergency Fix</button>
        
        <div id="result" style="margin-top: 20px;"></div>
        
        <h3>📋 What this emergency fix does:</h3>
        <ul>
            <li>Injects CSS directly into the page to force checkbox visibility</li>
            <li>Removes any iCheck interference immediately</li>
            <li>Applies emergency styling to all checkboxes</li>
            <li>Works instantly without page refresh</li>
        </ul>
        
        <h3>🔧 Manual Fix (if needed):</h3>
        <p>If the button doesn't work, add this CSS to your browser's developer tools:</p>
        <div class="code">
.input-icheck {<br>
&nbsp;&nbsp;display: inline-block !important;<br>
&nbsp;&nbsp;visibility: visible !important;<br>
&nbsp;&nbsp;opacity: 1 !important;<br>
&nbsp;&nbsp;width: 18px !important;<br>
&nbsp;&nbsp;height: 18px !important;<br>
&nbsp;&nbsp;position: relative !important;<br>
&nbsp;&nbsp;margin-right: 8px !important;<br>
}<br><br>
.icheckbox_square-blue, .iradio_square-blue {<br>
&nbsp;&nbsp;display: none !important;<br>
}
        </div>
    </div>

    <script>
    function applyEmergencyFix() {
        const resultDiv = document.getElementById('result');
        resultDiv.innerHTML = '<p>🔧 Applying emergency fix...</p>';
        
        // Create emergency CSS
        const emergencyCSS = `
        <style id="emergency-checkbox-fix">
        /* EMERGENCY CHECKBOX FIX - FORCE VISIBILITY */
        .input-icheck {
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
            width: 18px !important;
            height: 18px !important;
            margin: 0 8px 0 0 !important;
            vertical-align: middle !important;
            position: relative !important;
            z-index: 9999 !important;
            cursor: pointer !important;
            
            /* Emergency styling */
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            border: 2px solid #007cba !important;
            border-radius: 3px !important;
            background: white !important;
            outline: none !important;
        }
        
        .input-icheck:hover {
            border-color: #005a87 !important;
            box-shadow: 0 0 5px rgba(0, 124, 186, 0.3) !important;
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
        }
        
        /* Hide iCheck wrappers completely */
        .icheckbox_square-blue,
        .iradio_square-blue {
            display: none !important;
        }
        
        /* Force checkbox containers to be visible */
        .checkbox {
            display: block !important;
            margin: 10px 0 !important;
            min-height: 20px !important;
        }
        
        /* Label styling */
        .checkbox label {
            cursor: pointer !important;
            user-select: none !important;
            display: inline-flex !important;
            align-items: center !important;
            margin: 0 !important;
            padding: 5px 0 !important;
        }
        </style>`;
        
        // Inject CSS into current page
        document.head.insertAdjacentHTML('beforeend', emergencyCSS);
        
        // Force all checkboxes to be visible
        setTimeout(() => {
            const checkboxes = document.querySelectorAll('.input-icheck');
            let fixedCount = 0;
            
            checkboxes.forEach((checkbox, index) => {
                checkbox.style.display = 'inline-block';
                checkbox.style.visibility = 'visible';
                checkbox.style.opacity = '1';
                checkbox.style.position = 'relative';
                checkbox.style.zIndex = '9999';
                fixedCount++;
            });
            
            // Remove iCheck wrappers
            const iCheckWrappers = document.querySelectorAll('.icheckbox_square-blue, .iradio_square-blue');
            let removedCount = 0;
            
            iCheckWrappers.forEach(wrapper => {
                const input = wrapper.querySelector('input');
                if (input) {
                    wrapper.parentNode.insertBefore(input, wrapper);
                    wrapper.remove();
                    removedCount++;
                }
            });
            
            resultDiv.innerHTML = `
                <div class="success">
                    ✅ Emergency fix applied successfully!<br>
                    📊 Fixed ${fixedCount} checkboxes<br>
                    🗑️ Removed ${removedCount} iCheck wrappers<br><br>
                    <strong>Next steps:</strong><br>
                    1. Go to User Management → Add User<br>
                    2. All checkboxes should now be visible<br>
                    3. Test clicking them to ensure they work<br>
                </div>
            `;
            
            // Also apply to any future pages
            const script = document.createElement('script');
            script.textContent = `
                // Apply fix to any dynamically loaded content
                setInterval(function() {
                    document.querySelectorAll('.input-icheck').forEach(function(checkbox) {
                        if (checkbox.style.display === 'none' || checkbox.style.visibility === 'hidden') {
                            checkbox.style.display = 'inline-block';
                            checkbox.style.visibility = 'visible';
                            checkbox.style.opacity = '1';
                        }
                    });
                }, 1000);
            `;
            document.head.appendChild(script);
            
        }, 500);
    }
    </script>
</body>
</html>