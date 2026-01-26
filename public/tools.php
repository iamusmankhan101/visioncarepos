<?php
// Tools Index - List of all available tools
?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Tools</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; max-width: 800px; margin: 0 auto; }
        .tool { background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 4px; border-left: 4px solid #007bff; }
        .tool h3 { margin: 0 0 10px 0; color: #007bff; }
        .tool p { margin: 5px 0; color: #666; }
        .btn { background: #007bff; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 5px 5px 0 0; }
        .btn:hover { background: #0056b3; }
        .btn.success { background: #28a745; }
        .btn.warning { background: #ffc107; color: #000; }
        .status { float: right; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
        .available { background: #d4edda; color: #155724; }
        .unavailable { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛠️ Database Tools</h1>
        <p>Collection of tools for database management and table creation</p>
        
        <?php
        // Check which tools are available
        $tools = [
            'test_file_access.php' => 'File Access Test',
            'db_test.php' => 'Database Connection Test', 
            'sql_executor.php' => 'SQL Executor',
            'create_tables_from_sql.php' => 'Advanced Table Creator',
            'fix_datatable_complete.php' => 'DataTable Fix Tool',
            'test_sales_commission.php' => 'Sales Commission Test',
            'fix_condition_column.php' => 'Condition Column Fix'
        ];
        
        foreach ($tools as $file => $name) {
            $exists = file_exists(__DIR__ . '/' . $file);
            $status = $exists ? 'available' : 'unavailable';
            $statusText = $exists ? 'Available' : 'Not Found';
            
            echo '<div class="tool">';
            echo '<h3>' . $name . ' <span class="status ' . $status . '">' . $statusText . '</span></h3>';
            
            if ($file === 'test_file_access.php') {
                echo '<p>Test if PHP files are accessible through the web server</p>';
                if ($exists) echo '<a href="' . $file . '" class="btn">Test Access</a>';
            } elseif ($file === 'db_test.php') {
                echo '<p>Test database connection and show existing tables</p>';
                if ($exists) echo '<a href="' . $file . '" class="btn">Test Database</a>';
            } elseif ($file === 'sql_executor.php') {
                echo '<p>Simple tool to execute CREATE TABLE statements</p>';
                if ($exists) echo '<a href="' . $file . '" class="btn success">Execute SQL</a>';
            } elseif ($file === 'create_tables_from_sql.php') {
                echo '<p>Advanced table creation tool with templates and management features</p>';
                if ($exists) echo '<a href="' . $file . '" class="btn">Advanced Tool</a>';
            } elseif ($file === 'fix_datatable_complete.php') {
                echo '<p>Fix DataTables errors and create test commission agents</p>';
                if ($exists) echo '<a href="' . $file . '" class="btn warning">Fix DataTables</a>';
            } elseif ($file === 'test_sales_commission.php') {
                echo '<p>Test sales commission agents functionality</p>';
                if ($exists) echo '<a href="' . $file . '" class="btn">Test Commission</a>';
            } elseif ($file === 'fix_condition_column.php') {
                echo '<p>Add missing condition column to users table</p>';
                if ($exists) echo '<a href="' . $file . '" class="btn warning">Fix Column</a>';
            }
            
            echo '</div>';
        }
        ?>
        
        <div class="tool">
            <h3>📋 Quick SQL Templates</h3>
            <p>Copy and paste these into the SQL Executor:</p>
            
            <h4>Basic User Table:</h4>
            <pre style="background: #f1f1f1; padding: 10px; border-radius: 4px; overflow-x: auto;">CREATE TABLE users_example (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;</pre>
            
            <h4>Products Table:</h4>
            <pre style="background: #f1f1f1; padding: 10px; border-radius: 4px; overflow-x: auto;">CREATE TABLE products_example (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  stock_quantity INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;</pre>
        </div>
        
        <div class="tool">
            <h3>🔧 Troubleshooting</h3>
            <p>If tools are not working:</p>
            <ul>
                <li><strong>404 Errors:</strong> Check if files exist in the public directory</li>
                <li><strong>500 Errors:</strong> Check PHP error logs and Laravel configuration</li>
                <li><strong>Database Errors:</strong> Verify .env database settings</li>
                <li><strong>Permission Errors:</strong> Ensure web server can read PHP files</li>
            </ul>
        </div>
    </div>
</body>
</html>