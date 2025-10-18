<?php
/**
 * Admin Database Helper Functions
 * NextCode Group
 */

// Prevent direct access
if (!defined('SECURE_ACCESS')) {
    die('Direct access not allowed');
}

/**
 * Get database connection for admin
 */
function getAdminDatabase() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            require_once __DIR__ . '/../../config/database.php';
            $db = new Database();
            $pdo = $db->getConnection();
        } catch (Exception $e) {
            error_log("Admin database connection failed: " . $e->getMessage());
            return null;
        }
    }
    
    return $pdo;
}

/**
 * Execute query with error handling
 */
function executeQuery($sql, $params = []) {
    $pdo = getAdminDatabase();
    
    if (!$pdo) {
        return ['success' => false, 'error' => 'Database connection failed'];
    }
    
    try {
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($params);
        
        return [
            'success' => true,
            'result' => $result,
            'stmt' => $stmt
        ];
    } catch (Exception $e) {
        error_log("Query execution failed: " . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Fetch single row
 */
function fetchRow($sql, $params = []) {
    $result = executeQuery($sql, $params);
    
    if ($result['success']) {
        return $result['stmt']->fetch(PDO::FETCH_ASSOC);
    }
    
    return null;
}

/**
 * Fetch all rows
 */
function fetchAll($sql, $params = []) {
    $result = executeQuery($sql, $params);
    
    if ($result['success']) {
        return $result['stmt']->fetchAll(PDO::FETCH_ASSOC);
    }
    
    return [];
}

/**
 * Get count
 */
function getCount($table, $where = '', $params = []) {
    $sql = "SELECT COUNT(*) FROM $table";
    
    if ($where) {
        $sql .= " WHERE $where";
    }
    
    $result = executeQuery($sql, $params);
    
    if ($result['success']) {
        return $result['stmt']->fetchColumn();
    }
    
    return 0;
}

/**
 * Insert data
 */
function insertData($table, $data) {
    $columns = array_keys($data);
    $values = array_values($data);
    $placeholders = array_fill(0, count($columns), '?');
    
    $sql = "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
    
    $result = executeQuery($sql, $values);
    
    if ($result['success']) {
        return [
            'success' => true,
            'id' => getAdminDatabase()->lastInsertId()
        ];
    }
    
    return $result;
}

/**
 * Update data
 */
function updateData($table, $data, $where, $whereParams = []) {
    $columns = array_keys($data);
    $values = array_values($data);
    $setClause = implode(' = ?, ', $columns) . ' = ?';
    
    $sql = "UPDATE $table SET $setClause WHERE $where";
    
    $params = array_merge($values, $whereParams);
    
    return executeQuery($sql, $params);
}

/**
 * Delete data
 */
function deleteData($table, $where, $params = []) {
    $sql = "DELETE FROM $table WHERE $where";
    return executeQuery($sql, $params);
}

/**
 * Check if table exists
 */
function tableExists($tableName) {
    $sql = "SHOW TABLES LIKE ?";
    $result = executeQuery($sql, [$tableName]);
    
    if ($result['success']) {
        return $result['stmt']->rowCount() > 0;
    }
    
    return false;
}

/**
 * Get table structure
 */
function getTableStructure($tableName) {
    $sql = "DESCRIBE $tableName";
    return fetchAll($sql);
}

/**
 * Create table if not exists
 */
function createTableIfNotExists($tableName, $structure) {
    if (tableExists($tableName)) {
        return ['success' => true, 'message' => 'Table already exists'];
    }
    
    $sql = "CREATE TABLE $tableName ($structure)";
    return executeQuery($sql);
}

/**
 * Add column if not exists
 */
function addColumnIfNotExists($tableName, $columnName, $definition) {
    $structure = getTableStructure($tableName);
    
    foreach ($structure as $column) {
        if ($column['Field'] === $columnName) {
            return ['success' => true, 'message' => 'Column already exists'];
        }
    }
    
    $sql = "ALTER TABLE $tableName ADD COLUMN $columnName $definition";
    return executeQuery($sql);
}

/**
 * Get database info
 */
function getDatabaseInfo() {
    $pdo = getAdminDatabase();
    
    if (!$pdo) {
        return null;
    }
    
    $info = [];
    
    try {
        // Database version
        $stmt = $pdo->query("SELECT VERSION()");
        $info['version'] = $stmt->fetchColumn();
        
        // Database size
        $stmt = $pdo->query("
            SELECT 
                ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'DB Size in MB'
            FROM information_schema.tables 
            WHERE table_schema = DATABASE()
        ");
        $info['size'] = $stmt->fetchColumn();
        
        // Table count
        $stmt = $pdo->query("
            SELECT COUNT(*) 
            FROM information_schema.tables 
            WHERE table_schema = DATABASE()
        ");
        $info['table_count'] = $stmt->fetchColumn();
        
        // Connection info
        $info['connection_id'] = $pdo->query("SELECT CONNECTION_ID()")->fetchColumn();
        
    } catch (Exception $e) {
        error_log("Database info error: " . $e->getMessage());
    }
    
    return $info;
}

/**
 * Backup table data
 */
function backupTableData($tableName, $limit = 1000) {
    $pdo = getAdminDatabase();
    
    if (!$pdo) {
        return null;
    }
    
    try {
        $sql = "SELECT * FROM $tableName LIMIT $limit";
        $stmt = $pdo->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'success' => true,
            'data' => $data,
            'count' => count($data)
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Optimize table
 */
function optimizeTable($tableName) {
    $sql = "OPTIMIZE TABLE $tableName";
    return executeQuery($sql);
}

/**
 * Repair table
 */
function repairTable($tableName) {
    $sql = "REPAIR TABLE $tableName";
    return executeQuery($sql);
}

/**
 * Get table status
 */
function getTableStatus($tableName) {
    $sql = "SHOW TABLE STATUS LIKE ?";
    return fetchRow($sql, [$tableName]);
}

/**
 * Check database health
 */
function checkDatabaseHealth() {
    $pdo = getAdminDatabase();
    
    if (!$pdo) {
        return [
            'status' => 'error',
            'message' => 'Database connection failed'
        ];
    }
    
    $health = [
        'status' => 'healthy',
        'checks' => []
    ];
    
    try {
        // Test basic query
        $stmt = $pdo->query("SELECT 1");
        $health['checks']['basic_query'] = $stmt ? 'pass' : 'fail';
        
        // Check admin_users table
        $health['checks']['admin_users'] = tableExists('admin_users') ? 'pass' : 'fail';
        
        // Check admin_logs table
        $health['checks']['admin_logs'] = tableExists('admin_logs') ? 'pass' : 'fail';
        
        // Check for errors in error log
        $stmt = $pdo->query("SHOW WARNINGS");
        $warnings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $health['checks']['warnings'] = empty($warnings) ? 'pass' : 'warning';
        $health['warnings'] = $warnings;
        
        // Check connection count
        $stmt = $pdo->query("SHOW STATUS LIKE 'Threads_connected'");
        $connections = $stmt->fetch(PDO::FETCH_ASSOC);
        $health['connections'] = $connections['Value'] ?? 0;
        
    } catch (Exception $e) {
        $health['status'] = 'error';
        $health['message'] = $e->getMessage();
    }
    
    return $health;
}

/**
 * Get slow queries (if enabled)
 */
function getSlowQueries($limit = 10) {
    $pdo = getAdminDatabase();
    
    if (!$pdo) {
        return [];
    }
    
    try {
        $sql = "
            SELECT 
                query_time,
                lock_time,
                rows_sent,
                rows_examined,
                sql_text
            FROM mysql.slow_log 
            ORDER BY start_time DESC 
            LIMIT $limit
        ";
        
        return fetchAll($sql);
    } catch (Exception $e) {
        // Slow log might not be enabled
        return [];
    }
}

/**
 * Get process list
 */
function getProcessList() {
    $sql = "SHOW PROCESSLIST";
    return fetchAll($sql);
}

/**
 * Kill process
 */
function killProcess($processId) {
    $sql = "KILL ?";
    return executeQuery($sql, [$processId]);
}

/**
 * Get table sizes
 */
function getTableSizes() {
    $sql = "
        SELECT 
            table_name,
            ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size in MB',
            table_rows
        FROM information_schema.tables 
        WHERE table_schema = DATABASE()
        ORDER BY (data_length + index_length) DESC
    ";
    
    return fetchAll($sql);
}

/**
 * Get index usage
 */
function getIndexUsage() {
    $sql = "
        SELECT 
            table_name,
            index_name,
            column_name,
            cardinality
        FROM information_schema.statistics 
        WHERE table_schema = DATABASE()
        ORDER BY table_name, index_name
    ";
    
    return fetchAll($sql);
}

/**
 * Transaction helpers
 */
function beginTransaction() {
    $pdo = getAdminDatabase();
    return $pdo ? $pdo->beginTransaction() : false;
}

function commitTransaction() {
    $pdo = getAdminDatabase();
    return $pdo ? $pdo->commit() : false;
}

function rollbackTransaction() {
    $pdo = getAdminDatabase();
    return $pdo ? $pdo->rollBack() : false;
}

/**
 * Escape string for LIKE queries
 */
function escapeLikeString($string) {
    return str_replace(['%', '_'], ['\\%', '\\_'], $string);
}

/**
 * Build WHERE clause from array
 */
function buildWhereClause($conditions) {
    if (empty($conditions)) {
        return ['', []];
    }
    
    $whereParts = [];
    $params = [];
    
    foreach ($conditions as $column => $value) {
        if (is_array($value)) {
            // IN clause
            $placeholders = str_repeat('?,', count($value) - 1) . '?';
            $whereParts[] = "$column IN ($placeholders)";
            $params = array_merge($params, $value);
        } else {
            // Equality
            $whereParts[] = "$column = ?";
            $params[] = $value;
        }
    }
    
    return ['WHERE ' . implode(' AND ', $whereParts), $params];
}

/**
 * Paginate query results
 */
function paginateQuery($sql, $params, $page, $perPage = 20) {
    // Get total count
    $countSql = "SELECT COUNT(*) FROM ($sql) AS count_query";
    $total = fetchRow($countSql, $params)['COUNT(*)'] ?? 0;
    
    // Calculate offset
    $offset = ($page - 1) * $perPage;
    
    // Add LIMIT and OFFSET
    $paginatedSql = "$sql LIMIT $perPage OFFSET $offset";
    
    // Get data
    $data = fetchAll($paginatedSql, $params);
    
    // Calculate pagination info
    $totalPages = ceil($total / $perPage);
    
    return [
        'data' => $data,
        'total' => $total,
        'page' => $page,
        'per_page' => $perPage,
        'total_pages' => $totalPages,
        'has_next' => $page < $totalPages,
        'has_prev' => $page > 1
    ];
}
?>
