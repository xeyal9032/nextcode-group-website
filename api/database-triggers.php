<?php
// Database Trigger Sistemi
require_once 'config/database.php';

class DatabaseTriggerManager {
    private $pdo;
    
    public function __construct() {
        try {
            $database = new Database();
            $this->pdo = $database->getConnection();
            $this->createTriggers();
        } catch (Exception $e) {
            error_log('Database trigger connection failed: ' . $e->getMessage());
            $this->pdo = null;
        }
    }
    
    private function createTriggers() {
        if (!$this->pdo) return;
        
        // Contact info triggers
        $this->createContactInfoTriggers();
        
        // Site settings triggers
        $this->createSiteSettingsTriggers();
        
        // Site content triggers
        $this->createSiteContentTriggers();
    }
    
    private function createContactInfoTriggers() {
        // After INSERT trigger
        $this->pdo->exec("
            DROP TRIGGER IF EXISTS contact_info_after_insert
        ");
        
        $this->pdo->exec("
            CREATE TRIGGER contact_info_after_insert
            AFTER INSERT ON contact_info
            FOR EACH ROW
            BEGIN
                INSERT INTO sync_log (table_name, record_id, action, new_data, sync_status)
                VALUES ('contact_info', NEW.id, 'INSERT', JSON_OBJECT(
                    'company_name', NEW.company_name,
                    'address', NEW.address,
                    'phone', NEW.phone,
                    'email', NEW.email,
                    'website', NEW.website,
                    'working_hours', NEW.working_hours
                ), 'pending');
                
                INSERT INTO cache_invalidation (cache_key, cache_type, reason)
                VALUES ('contact_info', 'api', 'Contact info inserted');
            END
        ");
        
        // After UPDATE trigger
        $this->pdo->exec("
            DROP TRIGGER IF EXISTS contact_info_after_update
        ");
        
        $this->pdo->exec("
            CREATE TRIGGER contact_info_after_update
            AFTER UPDATE ON contact_info
            FOR EACH ROW
            BEGIN
                INSERT INTO sync_log (table_name, record_id, action, old_data, new_data, sync_status)
                VALUES ('contact_info', NEW.id, 'UPDATE', JSON_OBJECT(
                    'company_name', OLD.company_name,
                    'address', OLD.address,
                    'phone', OLD.phone,
                    'email', OLD.email,
                    'website', OLD.website,
                    'working_hours', OLD.working_hours
                ), JSON_OBJECT(
                    'company_name', NEW.company_name,
                    'address', NEW.address,
                    'phone', NEW.phone,
                    'email', NEW.email,
                    'website', NEW.website,
                    'working_hours', NEW.working_hours
                ), 'pending');
                
                INSERT INTO cache_invalidation (cache_key, cache_type, reason)
                VALUES ('contact_info', 'api', 'Contact info updated');
            END
        ");
        
        // After DELETE trigger
        $this->pdo->exec("
            DROP TRIGGER IF EXISTS contact_info_after_delete
        ");
        
        $this->pdo->exec("
            CREATE TRIGGER contact_info_after_delete
            AFTER DELETE ON contact_info
            FOR EACH ROW
            BEGIN
                INSERT INTO sync_log (table_name, record_id, action, old_data, sync_status)
                VALUES ('contact_info', OLD.id, 'DELETE', JSON_OBJECT(
                    'company_name', OLD.company_name,
                    'address', OLD.address,
                    'phone', OLD.phone,
                    'email', OLD.email,
                    'website', OLD.website,
                    'working_hours', OLD.working_hours
                ), 'pending');
                
                INSERT INTO cache_invalidation (cache_key, cache_type, reason)
                VALUES ('contact_info', 'api', 'Contact info deleted');
            END
        ");
    }
    
    private function createSiteSettingsTriggers() {
        // After UPDATE trigger for site_settings
        $this->pdo->exec("
            DROP TRIGGER IF EXISTS site_settings_after_update
        ");
        
        $this->pdo->exec("
            CREATE TRIGGER site_settings_after_update
            AFTER UPDATE ON site_settings
            FOR EACH ROW
            BEGIN
                INSERT INTO sync_log (table_name, record_id, action, old_data, new_data, sync_status)
                VALUES ('site_settings', NEW.id, 'UPDATE', JSON_OBJECT(
                    'setting_key', OLD.setting_key,
                    'setting_value', OLD.setting_value
                ), JSON_OBJECT(
                    'setting_key', NEW.setting_key,
                    'setting_value', NEW.setting_value
                ), 'pending');
                
                INSERT INTO cache_invalidation (cache_key, cache_type, reason)
                VALUES (CONCAT('site_settings_', NEW.setting_key), 'api', 'Site setting updated');
            END
        ");
    }
    
    private function createSiteContentTriggers() {
        // After UPDATE trigger for site_content
        $this->pdo->exec("
            DROP TRIGGER IF EXISTS site_content_after_update
        ");
        
        $this->pdo->exec("
            CREATE TRIGGER site_content_after_update
            AFTER UPDATE ON site_content
            FOR EACH ROW
            BEGIN
                INSERT INTO sync_log (table_name, record_id, action, old_data, new_data, sync_status)
                VALUES ('site_content', NEW.id, 'UPDATE', JSON_OBJECT(
                    'content_key', OLD.content_key,
                    'content_value', OLD.content_value,
                    'content_type', OLD.content_type
                ), JSON_OBJECT(
                    'content_key', NEW.content_key,
                    'content_value', NEW.content_value,
                    'content_type', NEW.content_type
                ), 'pending');
                
                INSERT INTO cache_invalidation (cache_key, cache_type, reason)
                VALUES (CONCAT('site_content_', NEW.content_key), 'api', 'Site content updated');
            END
        ");
    }
    
    public function enableTriggers() {
        if (!$this->pdo) return false;
        
        try {
            $this->createTriggers();
            return true;
        } catch (Exception $e) {
            error_log('Enable triggers error: ' . $e->getMessage());
            return false;
        }
    }
    
    public function disableTriggers() {
        if (!$this->pdo) return false;
        
        try {
            $triggers = [
                'contact_info_after_insert',
                'contact_info_after_update',
                'contact_info_after_delete',
                'site_settings_after_update',
                'site_content_after_update'
            ];
            
            foreach ($triggers as $trigger) {
                $this->pdo->exec("DROP TRIGGER IF EXISTS {$trigger}");
            }
            
            return true;
        } catch (Exception $e) {
            error_log('Disable triggers error: ' . $e->getMessage());
            return false;
        }
    }
    
    public function getTriggerStatus() {
        if (!$this->pdo) return null;
        
        try {
            $stmt = $this->pdo->query("
                SELECT 
                    TRIGGER_NAME,
                    EVENT_MANIPULATION,
                    EVENT_OBJECT_TABLE,
                    ACTION_TIMING
                FROM INFORMATION_SCHEMA.TRIGGERS 
                WHERE TRIGGER_SCHEMA = DATABASE()
                AND TRIGGER_NAME LIKE '%contact_info%' 
                OR TRIGGER_NAME LIKE '%site_settings%'
                OR TRIGGER_NAME LIKE '%site_content%'
            ");
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Get trigger status error: ' . $e->getMessage());
            return null;
        }
    }
}

// API endpoint for trigger management
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    
    $triggerManager = new DatabaseTriggerManager();
    
    switch ($action) {
        case 'enable':
            $result = $triggerManager->enableTriggers();
            echo json_encode(['success' => $result]);
            break;
            
        case 'disable':
            $result = $triggerManager->disableTriggers();
            echo json_encode(['success' => $result]);
            break;
            
        case 'status':
            $status = $triggerManager->getTriggerStatus();
            echo json_encode(['success' => true, 'data' => $status]);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Unknown action']);
    }
} else {
    // GET request - return trigger status
    $triggerManager = new DatabaseTriggerManager();
    $status = $triggerManager->getTriggerStatus();
    
    echo json_encode([
        'success' => true,
        'data' => $status,
        'message' => 'Database Trigger Manager'
    ]);
}
?>
