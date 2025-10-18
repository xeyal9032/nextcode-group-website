<?php
// NextCode Group - Setup Page
// This page helps initialize the database for first-time setup

// Include API router for handling API requests
require_once 'api-router.php';

// Include database configuration
require_once 'config/database.php';

$setup_complete = false;
$error_message = '';
$success_message = '';

// Check if tables already exist
try {
    $tables_exist = true;
    $required_tables = ['pages', 'site_settings', 'site_statistics'];
    
    foreach ($required_tables as $table) {
        $result = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($result->rowCount() == 0) {
            $tables_exist = false;
            break;
        }
    }
    
    if ($tables_exist) {
        $setup_complete = true;
        $success_message = 'Database is already set up and ready to use!';
    }
    
} catch (Exception $e) {
    $error_message = 'Database connection error: ' . $e->getMessage();
}

// Handle setup request
if (isset($_POST['setup_database']) && !$setup_complete) {
    try {
        // Read and execute SQL file
        $sql_file = __DIR__ . '/database/create_basic_tables.sql';
        
        if (!file_exists($sql_file)) {
            throw new Exception('SQL file not found. Please ensure create_basic_tables.sql exists in the database folder.');
        }
        
        $sql_content = file_get_contents($sql_file);
        $statements = array_filter(
            array_map('trim', explode(';', $sql_content)),
            function($stmt) {
                return !empty($stmt) && !preg_match('/^\s*--/', $stmt);
            }
        );
        
        foreach ($statements as $statement) {
            if (trim($statement)) {
                $pdo->exec($statement);
            }
        }
        
        $setup_complete = true;
        $success_message = 'Database has been successfully initialized!';
        
    } catch (Exception $e) {
        $error_message = 'Setup error: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NextCode Group - Database Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .setup-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .setup-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .setup-body {
            padding: 2rem;
        }
        .btn-setup {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-setup:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
        }
        .status-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .success-icon {
            color: #28a745;
        }
        .error-icon {
            color: #dc3545;
        }
        .warning-icon {
            color: #ffc107;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="setup-card">
                    <div class="setup-header">
                        <i class="fas fa-cog fa-3x mb-3"></i>
                        <h2>NextCode Group</h2>
                        <p class="mb-0">Database Setup</p>
                    </div>
                    
                    <div class="setup-body">
                        <?php if ($setup_complete): ?>
                            <div class="text-center">
                                <i class="fas fa-check-circle status-icon success-icon"></i>
                                <h4 class="text-success">Setup Complete!</h4>
                                <div class="alert alert-success">
                                    <?php echo htmlspecialchars($success_message); ?>
                                </div>
                                <p class="text-muted mb-4">
                                    Your database is ready and the website is now functional.
                                </p>
                                <a href="index.php" class="btn btn-setup">
                                    <i class="fas fa-home me-2"></i>Go to Website
                                </a>
                            </div>
                        <?php elseif ($error_message): ?>
                            <div class="text-center">
                                <i class="fas fa-exclamation-triangle status-icon error-icon"></i>
                                <h4 class="text-danger">Setup Error</h4>
                                <div class="alert alert-danger">
                                    <?php echo htmlspecialchars($error_message); ?>
                                </div>
                                <p class="text-muted mb-4">
                                    Please check your database configuration and try again.
                                </p>
                                <form method="post">
                                    <button type="submit" name="setup_database" class="btn btn-setup">
                                        <i class="fas fa-redo me-2"></i>Retry Setup
                                    </button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="text-center">
                                <i class="fas fa-database status-icon warning-icon"></i>
                                <h4>Database Setup Required</h4>
                                <p class="text-muted mb-4">
                                    Welcome to NextCode Group! To get started, we need to set up your database.
                                    This will create the necessary tables and insert default content.
                                </p>
                                
                                <div class="alert alert-info text-start">
                                    <h6><i class="fas fa-info-circle me-2"></i>What will be created:</h6>
                                    <ul class="mb-0">
                                        <li>Pages table (for content management)</li>
                                        <li>Site settings table (for configuration)</li>
                                        <li>Site statistics table (for analytics)</li>
                                        <li>Default homepage content</li>
                                        <li>Basic site configuration</li>
                                    </ul>
                                </div>
                                
                                <form method="post">
                                    <button type="submit" name="setup_database" class="btn btn-setup">
                                        <i class="fas fa-play me-2"></i>Initialize Database
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <p class="text-white-50">
                        <i class="fas fa-shield-alt me-2"></i>
                        Secure setup process - Your data is protected
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>