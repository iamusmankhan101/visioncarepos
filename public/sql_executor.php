<?php
// Simple SQL Executor - Direct table creation tool
?>
<!DOCTYPE html>
<html>
<head>
    <title>SQL Executor</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { background: white; padding: 20px; border-radius: 8px; max-width: 800px; margin: 0 auto; }
        textarea { width: 100%; height: 200px; font-family: monospace; padding: 10px; }
        .btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .output { background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 4px; font-family: monospace; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗃️ SQL Executor</h1>
        <p>Execute SQL CREATE TABLE statements</p>
        
        <?php
        if (isset($_POST['execute'])) {
            echo '<div class="output">';
            
            try {
                // Load Laravel
                require_once '../vendor/autoload.php';
                $app = require_once '../bootstrap/app.php';
                $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
                $kernel->bootstrap();

                use Illuminate\Support\Facades\DB;

                $sql = trim($_POST['sql']);
                if (empty($sql)) {
                    echo '<span class="error">No SQL provided</span>';
                } else {
                    echo "Executing SQL...\n";
                    
                    // Split by semicolons
                    $statements = array_filter(array_map('trim', explode(';', $sql)));
                    
                    foreach ($statements as $i => $statement) {
                        if (empty($statement)) continue;
                        
                        echo "\nStatement " . ($i + 1) . ":\n";
                        try {
                            DB::statement($statement);
                            echo '<span class="success">✓ Success</span>';
                            
                            // If CREATE TABLE, show table info
                            if (stripos($statement, 'CREATE TABLE') !== false) {
                                preg_match('/CREATE TABLE\s+(?:`?(\w+)`?)/i', $statement, $matches);
                                if (isset($matches[1])) {
                                    $table = $matches[1];
                                    echo " - Created table: {$table}";
                                }
                            }
                        } catch (Exception $e) {
                            echo '<span class="error">✗ Error: ' . $e->getMessage() . '</span>';
                        }
                        echo "\n";
                    }
                }
            } catch (Exception $e) {
                echo '<span class="error">Critical Error: ' . $e->getMessage() . '</span>';
            }
            
            echo '</div>';
        }
        ?>
        
        <form method="post">
            <h3>Enter SQL:</h3>
            <textarea name="sql" placeholder="CREATE TABLE example (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);"><?php echo isset($_POST['sql']) ? htmlspecialchars($_POST['sql']) : ''; ?></textarea>
            <br><br>
            <button type="submit" name="execute" class="btn">Execute SQL</button>
        </form>
        
        <h3>Sample SQL:</h3>
        <button onclick="loadSample()" class="btn">Load Sample</button>
        
        <div style="margin-top: 20px;">
            <h4>Quick Actions:</h4>
            <form method="post" style="display: inline;">
                <input type="hidden" name="sql" value="SHOW TABLES;">
                <button type="submit" name="execute" class="btn">Show Tables</button>
            </form>
            
            <form method="post" style="display: inline; margin-left: 10px;">
                <input type="text" name="table_name" placeholder="Table name" style="padding: 5px;">
                <button type="submit" name="describe" class="btn">Describe</button>
                <?php if (isset($_POST['describe']) && !empty($_POST['table_name'])): ?>
                    <input type="hidden" name="sql" value="DESCRIBE <?php echo htmlspecialchars($_POST['table_name']); ?>;">
                <?php endif; ?>
            </form>
        </div>
    </div>

    <script>
        function loadSample() {
            document.querySelector('textarea[name="sql"]').value = `CREATE TABLE users_example (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE products_example (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  stock INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;`;
        }
    </script>
</body>
</html>