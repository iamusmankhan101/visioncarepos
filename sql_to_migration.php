<?php

// Convert SQL CREATE TABLE statements to Laravel migrations
// Usage: php sql_to_migration.php [sql_file.sql]

require_once 'vendor/autoload.php';

echo "=== SQL TO LARAVEL MIGRATION CONVERTER ===\n\n";

$sqlFile = $argv[1] ?? null;

if (!$sqlFile) {
    echo "Usage: php sql_to_migration.php [sql_file.sql]\n";
    echo "Example: php sql_to_migration.php sample_tables.sql\n\n";
    exit(1);
}

if (!file_exists($sqlFile)) {
    echo "Error: SQL file '{$sqlFile}' not found\n";
    exit(1);
}

try {
    $sqlContent = file_get_contents($sqlFile);
    $statements = array_filter(array_map('trim', explode(';', $sqlContent)));
    
    echo "Processing {$sqlFile}...\n";
    echo "Found " . count($statements) . " SQL statements\n\n";
    
    foreach ($statements as $statement) {
        if (stripos($statement, 'CREATE TABLE') === false) {
            continue;
        }
        
        // Extract table name
        preg_match('/CREATE TABLE\s+(?:IF NOT EXISTS\s+)?(?:`?(\w+)`?)/i', $statement, $matches);
        if (!isset($matches[1])) {
            continue;
        }
        
        $tableName = $matches[1];
        $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $tableName)));
        $migrationName = "Create{$className}Table";
        $timestamp = date('Y_m_d_His', time() + rand(1, 999));
        $fileName = "database/migrations/{$timestamp}_create_{$tableName}_table.php";
        
        echo "Creating migration for table: {$tableName}\n";
        echo "File: {$fileName}\n";
        
        // Parse columns from SQL
        $columns = parseColumns($statement);
        $indexes = parseIndexes($statement);
        $foreignKeys = parseForeignKeys($statement);
        
        // Generate migration content
        $migrationContent = generateMigration($migrationName, $tableName, $columns, $indexes, $foreignKeys);
        
        // Create migration file
        if (!is_dir('database/migrations')) {
            mkdir('database/migrations', 0755, true);
        }
        
        file_put_contents($fileName, $migrationContent);
        echo "✓ Migration created successfully\n\n";
    }
    
    echo "All migrations created successfully!\n";
    echo "Run 'php artisan migrate' to execute the migrations.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

function parseColumns($sql) {
    $columns = [];
    
    // Extract the part between parentheses
    preg_match('/CREATE TABLE[^(]+\((.*)\)/is', $sql, $matches);
    if (!isset($matches[1])) {
        return $columns;
    }
    
    $columnsPart = $matches[1];
    
    // Split by commas, but be careful with nested parentheses
    $lines = explode("\n", $columnsPart);
    
    foreach ($lines as $line) {
        $line = trim($line, " \t\n\r\0\x0B,");
        
        if (empty($line) || 
            stripos($line, 'PRIMARY KEY') !== false ||
            stripos($line, 'UNIQUE KEY') !== false ||
            stripos($line, 'KEY ') !== false ||
            stripos($line, 'FOREIGN KEY') !== false ||
            stripos($line, 'CONSTRAINT') !== false) {
            continue;
        }
        
        // Parse column definition
        if (preg_match('/`?(\w+)`?\s+(.+)/', $line, $colMatches)) {
            $columnName = $colMatches[1];
            $definition = $colMatches[2];
            
            $columns[$columnName] = parseColumnDefinition($definition);
        }
    }
    
    return $columns;
}

function parseColumnDefinition($definition) {
    $column = [
        'type' => 'string',
        'nullable' => false,
        'default' => null,
        'length' => null,
        'precision' => null,
        'scale' => null,
        'autoIncrement' => false,
        'unsigned' => false
    ];
    
    $definition = strtolower($definition);
    
    // Auto increment
    if (strpos($definition, 'auto_increment') !== false) {
        $column['autoIncrement'] = true;
        $column['type'] = 'id';
    }
    
    // Unsigned
    if (strpos($definition, 'unsigned') !== false) {
        $column['unsigned'] = true;
    }
    
    // Nullable
    if (strpos($definition, 'null') !== false && strpos($definition, 'not null') === false) {
        $column['nullable'] = true;
    }
    
    // Default value
    if (preg_match('/default\s+([^\s]+)/i', $definition, $matches)) {
        $default = trim($matches[1], "'\"");
        if ($default !== 'null') {
            $column['default'] = $default;
        }
    }
    
    // Data types
    if (preg_match('/^(bigint|int|tinyint|smallint|mediumint)(\((\d+)\))?/i', $definition, $matches)) {
        $type = strtolower($matches[1]);
        if ($type === 'bigint') {
            $column['type'] = $column['unsigned'] ? 'unsignedBigInteger' : 'bigInteger';
        } elseif ($type === 'tinyint') {
            if (isset($matches[3]) && $matches[3] == '1') {
                $column['type'] = 'boolean';
            } else {
                $column['type'] = $column['unsigned'] ? 'unsignedTinyInteger' : 'tinyInteger';
            }
        } else {
            $column['type'] = $column['unsigned'] ? 'unsignedInteger' : 'integer';
        }
    } elseif (preg_match('/^varchar\((\d+)\)/i', $definition, $matches)) {
        $column['type'] = 'string';
        $column['length'] = (int)$matches[1];
    } elseif (preg_match('/^char\((\d+)\)/i', $definition, $matches)) {
        $column['type'] = 'char';
        $column['length'] = (int)$matches[1];
    } elseif (preg_match('/^decimal\((\d+),(\d+)\)/i', $definition, $matches)) {
        $column['type'] = 'decimal';
        $column['precision'] = (int)$matches[1];
        $column['scale'] = (int)$matches[2];
    } elseif (strpos($definition, 'text') !== false) {
        $column['type'] = 'text';
    } elseif (strpos($definition, 'longtext') !== false) {
        $column['type'] = 'longText';
    } elseif (strpos($definition, 'mediumtext') !== false) {
        $column['type'] = 'mediumText';
    } elseif (strpos($definition, 'json') !== false) {
        $column['type'] = 'json';
    } elseif (strpos($definition, 'timestamp') !== false) {
        $column['type'] = 'timestamp';
    } elseif (strpos($definition, 'datetime') !== false) {
        $column['type'] = 'dateTime';
    } elseif (strpos($definition, 'date') !== false) {
        $column['type'] = 'date';
    } elseif (strpos($definition, 'time') !== false) {
        $column['type'] = 'time';
    } elseif (preg_match('/^enum\((.+)\)/i', $definition, $matches)) {
        $column['type'] = 'enum';
        $column['values'] = array_map(function($v) {
            return trim($v, "'\"");
        }, explode(',', $matches[1]));
    }
    
    return $column;
}

function parseIndexes($sql) {
    $indexes = [];
    
    // Parse KEY definitions
    preg_match_all('/(?:UNIQUE\s+)?KEY\s+`?(\w+)`?\s*\(([^)]+)\)/i', $sql, $matches, PREG_SET_ORDER);
    
    foreach ($matches as $match) {
        $indexName = $match[1];
        $columns = array_map(function($col) {
            return trim($col, " `'\"");
        }, explode(',', $match[2]));
        
        $indexes[] = [
            'name' => $indexName,
            'columns' => $columns,
            'unique' => stripos($match[0], 'UNIQUE') !== false
        ];
    }
    
    return $indexes;
}

function parseForeignKeys($sql) {
    $foreignKeys = [];
    
    preg_match_all('/FOREIGN KEY\s*\(`?(\w+)`?\)\s*REFERENCES\s*`?(\w+)`?\s*\(`?(\w+)`?\)(?:\s*ON\s+DELETE\s+(\w+(?:\s+\w+)?))?(?:\s*ON\s+UPDATE\s+(\w+(?:\s+\w+)?))?/i', $sql, $matches, PREG_SET_ORDER);
    
    foreach ($matches as $match) {
        $foreignKeys[] = [
            'column' => $match[1],
            'references' => $match[3],
            'on' => $match[2],
            'onDelete' => isset($match[4]) ? strtolower(str_replace(' ', '', $match[4])) : null,
            'onUpdate' => isset($match[5]) ? strtolower(str_replace(' ', '', $match[5])) : null
        ];
    }
    
    return $foreignKeys;
}

function generateMigration($className, $tableName, $columns, $indexes, $foreignKeys) {
    $timestamp = date('Y-m-d H:i:s');
    
    $content = "<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {\n";
    
    // Generate column definitions
    foreach ($columns as $columnName => $column) {
        $line = "            ";
        
        if ($column['type'] === 'id') {
            $line .= "\$table->id('{$columnName}')";
        } else {
            $line .= "\$table->{$column['type']}('{$columnName}'";
            
            if ($column['length']) {
                $line .= ", {$column['length']}";
            } elseif ($column['precision'] && $column['scale']) {
                $line .= ", {$column['precision']}, {$column['scale']}";
            } elseif (isset($column['values'])) {
                $values = "['" . implode("', '", $column['values']) . "']";
                $line .= ", {$values}";
            }
            
            $line .= ")";
        }
        
        if ($column['nullable']) {
            $line .= "->nullable()";
        }
        
        if ($column['default'] !== null) {
            if (is_numeric($column['default'])) {
                $line .= "->default({$column['default']})";
            } else {
                $line .= "->default('{$column['default']}')";
            }
        }
        
        if ($column['unsigned'] && $column['type'] !== 'id') {
            $line .= "->unsigned()";
        }
        
        $line .= ";\n";
        $content .= $line;
    }
    
    // Add timestamps if not present
    if (!isset($columns['created_at']) && !isset($columns['updated_at'])) {
        $content .= "            \$table->timestamps();\n";
    }
    
    // Generate indexes
    foreach ($indexes as $index) {
        if ($index['unique']) {
            if (count($index['columns']) === 1) {
                $content .= "            \$table->unique('{$index['columns'][0]}');\n";
            } else {
                $columns_str = "['" . implode("', '", $index['columns']) . "']";
                $content .= "            \$table->unique({$columns_str});\n";
            }
        } else {
            if (count($index['columns']) === 1) {
                $content .= "            \$table->index('{$index['columns'][0]}');\n";
            } else {
                $columns_str = "['" . implode("', '", $index['columns']) . "']";
                $content .= "            \$table->index({$columns_str});\n";
            }
        }
    }
    
    // Generate foreign keys
    foreach ($foreignKeys as $fk) {
        $line = "            \$table->foreign('{$fk['column']}')->references('{$fk['references']}')->on('{$fk['on']}')";
        
        if ($fk['onDelete']) {
            $onDelete = str_replace('setnull', 'set null', $fk['onDelete']);
            $line .= "->onDelete('{$onDelete}')";
        }
        
        if ($fk['onUpdate']) {
            $onUpdate = str_replace('setnull', 'set null', $fk['onUpdate']);
            $line .= "->onUpdate('{$onUpdate}')";
        }
        
        $line .= ";\n";
        $content .= $line;
    }
    
    $content .= "        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('{$tableName}');
    }
};
";
    
    return $content;
}
?>