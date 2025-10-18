<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Create tables if they don't exist
    $conn->exec('
        CREATE TABLE IF NOT EXISTS team_members (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            position TEXT NOT NULL,
            bio TEXT,
            image_url TEXT,
            email TEXT,
            linkedin_url TEXT,
            twitter_url TEXT,
            github_url TEXT,
            skills TEXT,
            experience_years INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
            sort_order INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ');
    
    $conn->exec('
        CREATE TABLE IF NOT EXISTS company_stats (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            stat_name TEXT NOT NULL,
            stat_value TEXT NOT NULL,
            stat_label TEXT NOT NULL,
            icon_class TEXT,
            description TEXT,
            is_active INTEGER DEFAULT 1,
            sort_order INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ');
    
    $conn->exec('
        CREATE TABLE IF NOT EXISTS company_timeline (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            year INTEGER NOT NULL,
            title TEXT NOT NULL,
            description TEXT,
            achievement TEXT,
            icon_class TEXT,
            is_milestone INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
            sort_order INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ');
    
    // Insert sample data if tables are empty
    $stmt = $conn->query('SELECT COUNT(*) as count FROM team_members');
    $count = $stmt->fetch()['count'];
    
    if ($count == 0) {
        // Insert sample team members
        $conn->exec("INSERT INTO team_members (name, position, bio, image_url, email, linkedin_url, skills, experience_years, sort_order) VALUES 
            ('Xəyal Məmmədov', 'CEO & Founder', 'Texnologiya sahəsində 10+ il təcrübəsi olan rəhbər. AI və blockchain texnologiyalarında ekspert.', '/images/team/xeyal.jpg', 'xeyal@nextcode.az', 'https://linkedin.com/in/xeyal', 'Leadership,AI,Blockchain,Strategy', 10, 1),
            ('Nigar Əliyeva', 'CTO', 'Full-stack developer və sistem arxitekti. Cloud texnologiyaları və DevOps sahəsində mütəxəssis.', '/images/team/nigar.jpg', 'nigar@nextcode.az', 'https://linkedin.com/in/nigar', 'Full-Stack,Cloud,DevOps,Architecture', 8, 2),
            ('Rəşad Həsənov', 'Lead Frontend Developer', 'Modern frontend texnologiyaları və UI/UX dizaynında ekspert. React və Vue.js mütəxəssisi.', '/images/team/rashad.jpg', 'rashad@nextcode.az', 'https://linkedin.com/in/rashad', 'React,Vue.js,UI/UX,TypeScript', 6, 3),
            ('Leyla Quliyeva', 'Backend Developer', 'API inkişafı və veritabanı dizaynında mütəxəssis. Node.js və Python eksperti.', '/images/team/leyla.jpg', 'leyla@nextcode.az', 'https://linkedin.com/in/leyla', 'Node.js,Python,API,Database', 5, 4),
            ('Elvin Məhərrəmov', 'Mobile Developer', 'iOS və Android tətbiq inkişafında mütəxəssis. React Native və Flutter eksperti.', '/images/team/elvin.jpg', 'elvin@nextcode.az', 'https://linkedin.com/in/elvin', 'React Native,Flutter,iOS,Android', 4, 5),
            ('Səbinə Əhmədova', 'UI/UX Designer', 'İstifadəçi təcrübəsi və interfeys dizaynında yaradıcı mütəxəssis. Figma və Adobe Creative Suite eksperti.', '/images/team/sabina.jpg', 'sabina@nextcode.az', 'https://linkedin.com/in/sabina', 'UI/UX,Figma,Adobe,Design Thinking', 3, 6)
        ");
        
        // Insert sample company stats
        $conn->exec("INSERT INTO company_stats (stat_name, stat_value, stat_label, icon_class, description, sort_order) VALUES 
            ('projects_completed', '150+', 'Tamamlanmış Layihə', 'fas fa-project-diagram', 'Müxtəlif sahələrdə uğurla tamamladığımız layihələr', 1),
            ('happy_clients', '120+', 'Məmnun Müştəri', 'fas fa-users', 'Bizə etibar edən və məmnun qalan müştərilərimiz', 2),
            ('team_members', '25+', 'Komanda Üzvü', 'fas fa-user-tie', 'Peşəkar və təcrübəli komanda üzvlərimiz', 3),
            ('years_experience', '8+', 'İl Təcrübə', 'fas fa-calendar-alt', 'Texnologiya sahəsində qazandığımız təcrübə', 4),
            ('technologies', '50+', 'Texnologiya', 'fas fa-code', 'İstifadə etdiyimiz müasir texnologiyalar', 5),
            ('awards', '15+', 'Mükafat', 'fas fa-trophy', 'Qazandığımız sənaye mükafatları', 6)
        ");
        
        // Insert sample timeline
        $conn->exec("INSERT INTO company_timeline (year, title, description, achievement, icon_class, is_milestone, sort_order) VALUES 
            (2016, 'Şirkətin Təsisi', 'NextCode Group kiçik bir komanda ilə fəaliyyətə başladı', 'İlk müştəri layihəsi', 'fas fa-rocket', 1, 1),
            (2017, 'İlk Böyük Layihə', 'E-commerce platforması inkişaf etdirdik', '10+ müştəri', 'fas fa-shopping-cart', 0, 2),
            (2018, 'Komandanın Genişlənməsi', 'Komandamızı 15 nəfərə çatdırdıq', 'Yeni ofis', 'fas fa-building', 1, 3),
            (2019, 'AI Texnologiyalarına Keçid', 'Süni intellekt həlləri inkişaf etdirməyə başladıq', '50+ layihə', 'fas fa-brain', 1, 4),
            (2020, 'Beynəlxalq Genişlənmə', 'Xarici müştərilərlə işləməyə başladıq', 'Avropa bazarı', 'fas fa-globe', 0, 5),
            (2021, 'Blockchain Həlləri', 'DeFi və NFT layihələri inkişaf etdirdik', '100+ layihə', 'fas fa-link', 1, 6),
            (2022, 'Sənaye Liderliyi', 'Azərbaycanda aparıcı texnologiya şirkəti olduk', 'İl şirkəti mükafatı', 'fas fa-crown', 1, 7),
            (2023, 'İnnovasiya Mərkəzi', 'R&D departamenti yaratdıq', 'Patent müraciətləri', 'fas fa-lightbulb', 0, 8),
            (2024, 'Gələcəyə Doğru', 'Quantum computing və Web3 texnologiyalarında tədqiqat', 'Yeni texnologiyalar', 'fas fa-atom', 1, 9)
        ");
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = $_GET['action'] ?? 'all';
        
        switch ($action) {
            case 'team':
                $stmt = $conn->prepare("SELECT * FROM team_members WHERE is_active = 1 ORDER BY sort_order ASC");
                $stmt->execute();
                $team = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Parse skills for each team member
                foreach ($team as &$member) {
                    $member['skills'] = array_filter(array_map('trim', explode(',', $member['skills'] ?? '')));
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => $team
                ]);
                break;
                
            case 'stats':
                $stmt = $conn->prepare("SELECT * FROM company_stats WHERE is_active = 1 ORDER BY sort_order ASC");
                $stmt->execute();
                $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'data' => $stats
                ]);
                break;
                
            case 'timeline':
                $stmt = $conn->prepare("SELECT * FROM company_timeline WHERE is_active = 1 ORDER BY sort_order ASC");
                $stmt->execute();
                $timeline = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'data' => $timeline
                ]);
                break;
                
            default:
                // Return all data
                $stmt = $conn->prepare("SELECT * FROM team_members WHERE is_active = 1 ORDER BY sort_order ASC");
                $stmt->execute();
                $team = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $stmt = $conn->prepare("SELECT * FROM company_stats WHERE is_active = 1 ORDER BY sort_order ASC");
                $stmt->execute();
                $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $stmt = $conn->prepare("SELECT * FROM company_timeline WHERE is_active = 1 ORDER BY sort_order ASC");
                $stmt->execute();
                $timeline = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Parse skills for each team member
                foreach ($team as &$member) {
                    $member['skills'] = array_filter(array_map('trim', explode(',', $member['skills'] ?? '')));
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'team' => $team,
                        'stats' => $stats,
                        'timeline' => $timeline
                    ]
                ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Yalnız GET metodu dəstəklənir.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Sistem xətası: ' . $e->getMessage()
    ]);
}
?>