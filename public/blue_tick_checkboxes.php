<?php
// Blue Tick Checkboxes - Apply blue checkmark styling
// Access: http://your-domain/blue_tick_checkboxes.php

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>💙 Blue Tick Checkboxes</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; max-width: 800px; margin: 0 auto; }
        .btn { background: #007cba; color: white; padding: 15px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 18px; margin: 10px 5px; }
        .btn:hover { background: #005a87; }
        .success { color: #28a745; font-weight: bold; }
        .preview { border: 1px solid #ddd; padding: 20px; margin: 20px 0; border-radius: 4px; background: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container">
        <h1>💙 Blue Tick Checkboxes</h1>
        <p>This will apply blue checkmark styling to your checkboxes instead of white ticks.</p>
        
        <div class="preview">
            <h3>📋 Preview of Blue Tick Checkboxes:</h3>
            <div style="margin: 10px 0;">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input type="checkbox" class="blue-tick-checkbox" checked style="margin-right: 8px;"> Status for user (Blue Tick)
                </label>
            </div>
            <div style="margin: 10px 0;">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input type="checkbox" class="blue-tick-checkbox" style="margin-right: 8px;"> Allow Login (Blue Tick)
                </label>
            </div>
        </div>
        
        <button class="btn" onclick="applyBlueTicks()">💙 Apply Blue Tick Styling</button>
        
        <div id="result" style="margin-top: 20px;"></div>
        
        <h3>🎨 Blue Tick Features:</h3>
        <ul>
            <li>💙 <strong>Blue checkmarks</strong> instead of white</li>
            <li>⚪ <strong>White background</strong> when checked</li>
            <li>🔵 <strong>Blue border</strong> for consistency</li>
            <li>✨ <strong>Blue shadow</strong> for better visibility</li>
            <li>🎯 <strong>Professional appearance</strong> that matches your theme</li>
        </ul>
    </div>

    <style>
    /* Preview styling for blue tick checkboxes */
    .blue-tick-checkbox {
        width: 20px !important;
        height: 20px !important;
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        border: 2px solid #007cba !important;
        border-radius: 4px !important;
        background: white !important;
        cursor: pointer !important;
        position: relative !important;
        transition: all 0.2s ease !important;
    }
    
    .blue-tick-checkbox:hover {
        border-color: #005a87 !important;
        box-shadow: 0 0 8px rgba(0, 124, 186, 0.3) !important;
        transform: scale(1.05) !important;
    }
    
    .blue-tick-checkbox:checked {
        background: white !important;
        border-color: #007cba !important;
    }
    
    .blue-tick-checkbox:checked::after {
        content: '✓' !important;
        position: absolute !important;
        top: -1px !important;
        left: 3px !important;
        color: #007cba !important;
        font-size: 16px !important;
        font-weight: bold !important;
        line-height: 1 !important;
        text-shadow: 0 0 2px rgba(0, 124, 186, 0.3) !important;
    }
    </style>

    <script>
    function applyBlueTicks() {
        const resultDiv = document.getElementById('result');
        resultDiv.innerHTML = '<p style="color: #007cba; font-weight: bold;">💙 Applying blue tick styling...</p>';
        
        // Create blue tick CSS
        const blueTickCSS = document.createElement('style');
        blueTickCSS.id = 'blue-tick-checkboxes';
        blueTickCSS.innerHTML = `
        /* BLUE TICK CHECKBOXES - PROFESSIONAL STYLING */
        
        .input-icheck,
        input[type="checkbox"].input-icheck,
        input.input-icheck,
        .icheckbox_square-blue input[type="checkbox"] {
            /* Size and positioning */
            width: 20px !important;
            height: 20px !important;
            min-width: 20px !important;
            min-height: 20px !important;
            margin: 0 10px 0 0 !important;
            padding: 0 !important;
            
            /* Visibility */
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
            position: relative !important;
            z-index: 9999 !important;
            
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
            
            /* Transitions */
            transition: all 0.2s ease !important;
            
            /* Positioning */
            left: auto !important;
            top: auto !important;
            clip: auto !important;
            overflow: visible !important;
        }
        
        /* Hover effects */
        .input-icheck:hover,
        .icheckbox_square-blue input[type="checkbox"]:hover {
            border-color: #005a87 !important;
            box-shadow: 0 0 8px rgba(0, 124, 186, 0.3) !important;
            transform: scale(1.05) !important;
        }
        
        /* Checked state - WHITE BACKGROUND */
        .input-icheck:checked,
        .icheckbox_square-blue input[type="checkbox"]:checked {
            background: white !important;
            border-color: #007cba !important;
        }
        
        /* BLUE CHECKMARK */
        .input-icheck:checked::after,
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
        }
        
        /* Hide iCheck wrappers */
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
        
        // Remove existing styling
        const existing = document.getElementById('blue-tick-checkboxes');
        if (existing) existing.remove();
        
        // Inject CSS
        document.head.appendChild(blueTickCSS);
        
        // Apply to all checkboxes
        setTimeout(() => {
            let styledCount = 0;
            
            // Find and style all checkboxes
            const checkboxes = document.querySelectorAll('.input-icheck, input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                if (checkbox.type === 'checkbox') {
                    checkbox.classList.add('input-icheck');
                    checkbox.style.setProperty('display', 'inline-block', 'important');
                    checkbox.style.setProperty('visibility', 'visible', 'important');
                    checkbox.style.setProperty('opacity', '1', 'important');
                    styledCount++;
                }
            });
            
            // Remove iCheck wrappers
            const wrappers = document.querySelectorAll('.icheckbox_square-blue, .iradio_square-blue');
            let removedWrappers = 0;
            wrappers.forEach(wrapper => {
                const input = wrapper.querySelector('input');
                if (input) {
                    wrapper.parentNode.insertBefore(input, wrapper);
                    wrapper.remove();
                    removedWrappers++;
                }
            });
            
            resultDiv.innerHTML = `
                <div style="color: #007cba; font-weight: bold; font-size: 18px;">
                    💙 BLUE TICK STYLING APPLIED!<br><br>
                    
                    📊 <strong>${styledCount}</strong> checkboxes now have blue ticks<br>
                    🗑️ <strong>${removedWrappers}</strong> iCheck wrappers removed<br>
                    ⚪ White background when checked<br>
                    💙 Blue checkmarks for better visibility<br><br>
                    
                    <div style="background: #e3f2fd; border: 1px solid #90caf9; padding: 15px; border-radius: 4px; margin: 10px 0;">
                        <strong>✅ SUCCESS!</strong><br>
                        Your checkboxes now have:<br>
                        • 💙 Blue checkmarks instead of white<br>
                        • ⚪ Clean white background when checked<br>
                        • 🔵 Blue border for consistency<br>
                        • ✨ Professional appearance<br>
                    </div>
                </div>
            `;
            
        }, 1000);
    }
    </script>
</body>
</html>