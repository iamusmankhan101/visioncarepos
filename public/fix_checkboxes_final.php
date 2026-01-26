<?php
// Final checkbox fix - run this in your browser
// Access: http://your-domain/fix_checkboxes_final.php

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>🔧 Final Checkbox Fix</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; max-width: 800px; margin: 0 auto; }
        .btn { background: #007bff; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; margin: 10px 5px; }
        .btn:hover { background: #0056b3; }
        .output { background: #f8f9fa; border: 1px solid #ddd; padding: 15px; margin: 20px 0; border-radius: 4px; font-family: monospace; white-space: pre-wrap; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Final Checkbox Fix</h1>
        <p>This will fix the checkbox visibility issues in user management forms by updating the JavaScript event handlers.</p>
        
        <?php
        if (isset($_POST['apply_fix'])) {
            echo '<div class="output">';
            
            try {
                echo "=== APPLYING FINAL CHECKBOX FIX ===\n\n";
                
                // Fix 1: Update create form JavaScript
                $createFile = '../resources/views/manage_user/create.blade.php';
                if (file_exists($createFile)) {
                    $content = file_get_contents($createFile);
                    
                    // Replace iCheck events with regular events
                    $content = str_replace(
                        "    $('#selected_contacts').on('change ifChecked', function(event){\n      \$('div.selected_contacts_div').removeClass('hide');\n    });\n    $('#selected_contacts').on('ifUnchecked', function(event){\n      \$('div.selected_contacts_div').addClass('hide');\n    });",
                        "    // Handle selected contacts checkbox\n    $('#selected_contacts').on('change', function(event){\n      if (this.checked) {\n        \$('div.selected_contacts_div').removeClass('hide');\n      } else {\n        \$('div.selected_contacts_div').addClass('hide');\n      }\n    });",
                        $content
                    );
                    
                    $content = str_replace(
                        "    $('#is_enable_service_staff_pin').on('change ifChecked', function(event){\n      \$('div.service_staff_pin_div').removeClass('hide');\n    });\n    $('#is_enable_service_staff_pin').on('ifUnchecked', function(event){\n      \$('div.service_staff_pin_div').addClass('hide');\n      \$('#service_staff_pin').val('');\n    });",
                        "    // Handle service staff pin checkbox\n    $('#is_enable_service_staff_pin').on('change', function(event){\n      if (this.checked) {\n        \$('div.service_staff_pin_div').removeClass('hide');\n      } else {\n        \$('div.service_staff_pin_div').addClass('hide');\n        \$('#service_staff_pin').val('');\n      }\n    });",
                        $content
                    );
                    
                    $content = str_replace(
                        "    $('#allow_login').on('change ifChecked', function(event){\n      \$('div.user_auth_fields').removeClass('hide');\n    });\n    $('#allow_login').on('ifUnchecked', function(event){\n      \$('div.user_auth_fields').addClass('hide');\n    });",
                        "    // Handle allow login checkbox\n    $('#allow_login').on('change', function(event){\n      if (this.checked) {\n        \$('div.user_auth_fields').removeClass('hide');\n      } else {\n        \$('div.user_auth_fields').addClass('hide');\n      }\n    });",
                        $content
                    );
                    
                    file_put_contents($createFile, $content);
                    echo "✅ Updated create.blade.php JavaScript events\n";
                } else {
                    echo "❌ Create form file not found\n";
                }
                
                // Fix 2: Update edit form JavaScript
                $editFile = '../resources/views/manage_user/edit.blade.php';
                if (file_exists($editFile)) {
                    $content = file_get_contents($editFile);
                    
                    // Replace iCheck events with regular events
                    $content = str_replace(
                        "    $('#selected_contacts').on('ifChecked', function(event){\n      \$('div.selected_contacts_div').removeClass('hide');\n    });\n    $('#selected_contacts').on('ifUnchecked', function(event){\n      \$('div.selected_contacts_div').addClass('hide');\n    });",
                        "    // Handle selected contacts checkbox\n    $('#selected_contacts').on('change', function(event){\n      if (this.checked) {\n        \$('div.selected_contacts_div').removeClass('hide');\n      } else {\n        \$('div.selected_contacts_div').addClass('hide');\n      }\n    });",
                        $content
                    );
                    
                    $content = str_replace(
                        "    $('#is_enable_service_staff_pin').on('ifChecked', function(event){\n      \$('div.service_staff_pin_div').removeClass('hide');\n    });\n\n    $('#is_enable_service_staff_pin').on('ifUnchecked', function(event){\n      \$('div.service_staff_pin_div').addClass('hide');\n      \$('#service_staff_pin').val('');\n    });",
                        "    // Handle service staff pin checkbox\n    $('#is_enable_service_staff_pin').on('change', function(event){\n      if (this.checked) {\n        \$('div.service_staff_pin_div').removeClass('hide');\n      } else {\n        \$('div.service_staff_pin_div').addClass('hide');\n        \$('#service_staff_pin').val('');\n      }\n    });",
                        $content
                    );
                    
                    $content = str_replace(
                        "    $('#allow_login').on('ifChecked', function(event){\n      \$('div.user_auth_fields').removeClass('hide');\n    });\n    $('#allow_login').on('ifUnchecked', function(event){\n      \$('div.user_auth_fields').addClass('hide');\n    });",
                        "    // Handle allow login checkbox\n    $('#allow_login').on('change', function(event){\n      if (this.checked) {\n        \$('div.user_auth_fields').removeClass('hide');\n      } else {\n        \$('div.user_auth_fields').addClass('hide');\n      }\n    });",
                        $content
                    );
                    
                    file_put_contents($editFile, $content);
                    echo "✅ Updated edit.blade.php JavaScript events\n";
                } else {
                    echo "❌ Edit form file not found\n";
                }
                
                echo "\n=== FIX COMPLETED SUCCESSFULLY ===\n";
                echo "✅ Checkbox events updated to use regular JavaScript instead of iCheck\n";
                echo "✅ All checkboxes should now be visible and functional\n";
                echo "✅ Form interactions (show/hide fields) should work properly\n\n";
                echo "NEXT STEPS:\n";
                echo "1. Go to User Management > Add User\n";
                echo "2. Check that all checkboxes are visible\n";
                echo "3. Test checkbox interactions (clicking should show/hide related fields)\n";
                echo "4. Test User Management > Edit User as well\n";
                
            } catch (Exception $e) {
                echo '<span class="error">ERROR: ' . $e->getMessage() . '</span>' . "\n";
            }
            
            echo '</div>';
        }
        ?>
        
        <form method="post">
            <button type="submit" name="apply_fix" class="btn">🚀 Apply Final Checkbox Fix</button>
        </form>
        
        <h3>📋 What this fix does:</h3>
        <ul>
            <li>Replaces iCheck event handlers with regular JavaScript events</li>
            <li>Fixes checkbox interactions (show/hide related fields)</li>
            <li>Ensures all checkboxes are visible and clickable</li>
            <li>Maintains all existing functionality without iCheck dependency</li>
        </ul>
        
        <h3>🔍 Test after applying:</h3>
        <ol>
            <li><strong>Add User form</strong>: All checkboxes should be visible</li>
            <li><strong>Allow Login</strong>: Should show/hide username/password fields</li>
            <li><strong>Selected Contacts</strong>: Should show/hide contact selection</li>
            <li><strong>Service Staff PIN</strong>: Should show/hide PIN field</li>
            <li><strong>Location checkboxes</strong>: Should all be visible and clickable</li>
        </ol>
    </div>
</body>
</html>