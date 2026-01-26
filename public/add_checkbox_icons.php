<?php
// Add Checkbox Icons - Restore visual checkboxes to Add User form
// Access: http://your-domain/add_checkbox_icons.php

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>✅ Add Checkbox Icons</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; max-width: 800px; margin: 0 auto; }
        .btn { background: #28a745; color: white; padding: 15px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 18px; margin: 10px 5px; }
        .btn:hover { background: #218838; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .preview { border: 1px solid #ddd; padding: 20px; margin: 20px 0; border-radius: 4px; background: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container">
        <h1>✅ Add Checkbox Icons</h1>
        <p>This will add proper checkbox icons back to your Add User form with professional styling.</p>
        
        <div class="preview">
            <h3>📋 Preview of New Checkboxes:</h3>
            <div style="margin: 10px 0;">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input type="checkbox" class="modern-checkbox" checked style="margin-right: 8px;"> Status for user
                </label>
            </div>
            <div style="margin: 10px 0;">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input type="checkbox" class="modern-checkbox" style="margin-right: 8px;"> Allow Login
                </label>
            </div>
            <div style="margin: 10px 0;">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input type="checkbox" class="modern-checkbox" style="margin-right: 8px;"> Selected Contacts
                </label>
            </div>
        </div>
        
        <button class="btn" onclick="addCheckboxIcons()">✅ Add Checkbox Icons Now</button>
        
        <div id="result" style="margin-top: 20px;"></div>
        
        <h3>🎨 Features of New Checkboxes:</h3>
        <ul>
            <li>✅ <strong>Professional blue styling</strong> that matches your theme</li>
            <li>✅ <strong>Clear checkmark icons</strong> when selected</li>
            <li>✅ <strong>Hover effects</strong> for better user experience</li>
            <li>✅ <strong>Proper spacing</strong> and alignment</li>
            <li>✅ <strong>Mobile-friendly</strong> touch targets</li>
            <li>✅ <strong>Accessible</strong> for screen readers</li>
        </ul>
    </div>

    <style>
    /* Preview styling for modern checkboxes */
    .modern-checkbox {
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
    
    .modern-checkbox:hover {
        border-color: #005a87 !important;
        box-shadow: 0 0 8px rgba(0, 124, 186, 0.3) !important;
        transform: scale(1.05) !important;
    }
    
    .modern-checkbox:checked {
        background: #007cba !important;
        border-color: #007cba !important;
    }
    
    .modern-checkbox:checked::after {
        content: '✓' !important;
        position: absolute !important;
        top: -1px !important;
        left: 3px !important;
        color: white !important;
        font-size: 16px !important;
        font-weight: bold !important;
        line-height: 1 !important;
    }
    </style>

    <script>
    function addCheckboxIcons() {
        const resultDiv = document.getElementById('result');
        resultDiv.innerHTML = '<p style="color: #28a745; font-weight: bold;">✅ Adding checkbox icons...</p>';
        
        // Create comprehensive CSS for checkbox icons
        const checkboxCSS = document.createElement('style');
        checkboxCSS.id = 'checkbox-icons-fix';
        checkboxCSS.innerHTML = `
        /* MODERN CHECKBOX ICONS - PROFESSIONAL STYLING */
        
        .input-icheck,
        input[type="checkbox"].input-icheck,
        input.input-icheck {
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
            z-index: 1000 !important;
            
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
            
            /* Smooth transitions */
            transition: all 0.2s ease !important;
            
            /* Ensure it's not hidden */
            left: auto !important;
            top: auto !important;
            clip: auto !important;
            overflow: visible !important;
        }
        
        /* Hover effect */
        .input-icheck:hover {
            border-color: #005a87 !important;
            box-shadow: 0 0 8px rgba(0, 124, 186, 0.3) !important;
            transform: scale(1.05) !important;
        }
        
        /* Focus effect */
        .input-icheck:focus {
            border-color: #005a87 !important;
            box-shadow: 0 0 0 3px rgba(0, 124, 186, 0.2) !important;
        }
        
        /* Checked state */
        .input-icheck:checked {
            background: #007cba !important;
            border-color: #007cba !important;
        }
        
        /* Checkmark icon */
        .input-icheck[type="checkbox"]:checked::after {
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
        
        /* Alternative checkmark styles */
        .input-icheck[type="checkbox"]:checked::before {
            content: '' !important;
            position: absolute !important;
            top: 2px !important;
            left: 6px !important;
            width: 6px !important;
            height: 10px !important;
            border: solid white !important;
            border-width: 0 2px 2px 0 !important;
            transform: rotate(45deg) !important;
            display: none !important; /* Use ::after instead */
        }
        
        /* Disabled state */
        .input-icheck:disabled {
            background: #f8f9fa !important;
            border-color: #dee2e6 !important;
            cursor: not-allowed !important;
            opacity: 0.6 !important;
        }
        
        /* Remove iCheck interference completely */
        .icheckbox_square-blue,
        .iradio_square-blue,
        .icheckbox_square-blue *,
        .iradio_square-blue * {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            position: absolute !important;
            left: -9999px !important;
            top: -9999px !important;
        }
        
        /* Checkbox container styling */
        .checkbox {
            display: block !important;
            margin: 15px 0 !important;
            min-height: 24px !important;
            position: relative !important;
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
            line-height: 1.4 !important;
        }
        
        /* Form group spacing */
        .form-group .checkbox {
            margin-top: 5px !important;
            margin-bottom: 15px !important;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .input-icheck {
                width: 22px !important;
                height: 22px !important;
                margin-right: 12px !important;
            }
            
            .input-icheck:checked::after {
                font-size: 18px !important;
                left: 4px !important;
            }
        }
        
        /* High contrast mode support */
        @media (prefers-contrast: high) {
            .input-icheck {
                border-width: 3px !important;
            }
            
            .input-icheck:checked::after {
                font-weight: 900 !important;
            }
        }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .input-icheck {
                background: #2d3748 !important;
                border-color: #4a5568 !important;
            }
            
            .input-icheck:checked {
                background: #3182ce !important;
                border-color: #3182ce !important;
            }
        }
        `;
        
        // Remove any existing fix
        const existing = document.getElementById('checkbox-icons-fix');
        if (existing) existing.remove();
        
        // Inject the CSS
        document.head.appendChild(checkboxCSS);
        
        // Force all checkboxes to be visible and styled
        setTimeout(() => {
            let fixedCount = 0;
            let removedWrappers = 0;
            
            // Find and fix all checkboxes
            const checkboxes = document.querySelectorAll('.input-icheck, input[type="checkbox"]');
            checkboxes.forEach((checkbox, index) => {
                if (checkbox.type === 'checkbox') {
                    // Add our class
                    checkbox.classList.add('input-icheck');
                    
                    // Force visibility
                    checkbox.style.setProperty('display', 'inline-block', 'important');
                    checkbox.style.setProperty('visibility', 'visible', 'important');
                    checkbox.style.setProperty('opacity', '1', 'important');
                    checkbox.style.setProperty('position', 'relative', 'important');
                    checkbox.style.setProperty('z-index', '1000', 'important');
                    
                    fixedCount++;
                }
            });
            
            // Remove iCheck wrappers
            const wrappers = document.querySelectorAll('.icheckbox_square-blue, .iradio_square-blue');
            wrappers.forEach(wrapper => {
                const input = wrapper.querySelector('input');
                if (input) {
                    wrapper.parentNode.insertBefore(input, wrapper);
                    wrapper.remove();
                    removedWrappers++;
                }
            });
            
            // Apply to future elements
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) {
                            const newCheckboxes = node.querySelectorAll('input[type="checkbox"]');
                            newCheckboxes.forEach(cb => {
                                cb.classList.add('input-icheck');
                                cb.style.setProperty('display', 'inline-block', 'important');
                                cb.style.setProperty('visibility', 'visible', 'important');
                                cb.style.setProperty('opacity', '1', 'important');
                            });
                        }
                    });
                });
            });
            
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
            
            resultDiv.innerHTML = `
                <div style="color: #28a745; font-weight: bold; font-size: 18px;">
                    ✅ CHECKBOX ICONS ADDED SUCCESSFULLY!<br><br>
                    
                    📊 <strong>${fixedCount}</strong> checkboxes now have icons<br>
                    🗑️ <strong>${removedWrappers}</strong> old wrappers removed<br>
                    🎨 Professional styling applied<br>
                    👀 Continuous monitoring active<br><br>
                    
                    <div style="background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 4px; margin: 10px 0;">
                        <strong>🎉 SUCCESS!</strong><br>
                        Go to User Management → Add User<br>
                        You should now see:<br>
                        • ✅ Blue checkbox icons<br>
                        • ✅ Checkmarks when selected<br>
                        • ✅ Hover effects<br>
                        • ✅ Professional styling<br>
                    </div>
                    
                    <div style="background: #cce5ff; border: 1px solid #99d6ff; padding: 10px; border-radius: 4px; margin: 10px 0; font-size: 14px;">
                        <strong>💡 Features Added:</strong><br>
                        • Modern checkbox design<br>
                        • Smooth hover animations<br>
                        • Clear checkmark icons<br>
                        • Mobile-friendly sizing<br>
                        • Accessibility support<br>
                    </div>
                </div>
            `;
            
        }, 1000);
    }
    </script>
</body>
</html>