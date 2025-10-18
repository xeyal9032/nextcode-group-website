<?php
// Update Portfolio Database
$pdo = new PDO('mysql:host=gtorg.mysql.tools;dbname=gtorg_nextcode;charset=utf8mb4', 'gtorg_nextcode', ';849#dVEyg', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

echo "Adding category_name column...\n";
$pdo->exec('ALTER TABLE portfolio_projects ADD COLUMN category_name VARCHAR(255) AFTER category_id');

echo "Updating category names...\n";
$pdo->exec('UPDATE portfolio_projects p JOIN portfolio_categories c ON p.category_id = c.id SET p.category_name = c.name');

echo "Portfolio database updated successfully!\n";
?>
