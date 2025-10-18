<?php
/**
 * NextCode Group - Monitoring Dashboard
 * Uptime monitoring verilerini görselleştirir
 */

// Güvenlik kontrolü
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/uptime-monitor.php';

// Monitoring tablosunu oluştur
$monitor = new UptimeMonitor();
$monitor->createMonitoringTable();

// İstatistikleri al
$stats24h = $monitor->getLast24HoursStats();
$recentDowntime = $monitor->getRecentDowntime();

$page_title = 'Monitoring Dashboard - NextCode Group';
$current_page = 'monitoring';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .status-online { color: #28a745; }
        .status-offline { color: #dc3545; }
        .metric-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .metric-value {
            font-size: 2.5rem;
            font-weight: bold;
        }
        .metric-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .refresh-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">
                    <i class="fas fa-chart-line text-primary me-2"></i>
                    Monitoring Dashboard
                </h1>
            </div>
        </div>
        
        <?php if ($stats24h['success']): ?>
        <div class="row">
            <!-- Uptime Percentage -->
            <div class="col-md-3">
                <div class="metric-card text-center">
                    <div class="metric-value">
                        <?php echo $stats24h['stats']['uptime_percentage']; ?>%
                    </div>
                    <div class="metric-label">
                        <i class="fas fa-heartbeat me-1"></i>
                        Uptime (24h)
                    </div>
                </div>
            </div>
            
            <!-- Total Checks -->
            <div class="col-md-3">
                <div class="metric-card text-center">
                    <div class="metric-value">
                        <?php echo $stats24h['stats']['total_checks']; ?>
                    </div>
                    <div class="metric-label">
                        <i class="fas fa-check-circle me-1"></i>
                        Total Checks
                    </div>
                </div>
            </div>
            
            <!-- Average Response Time -->
            <div class="col-md-3">
                <div class="metric-card text-center">
                    <div class="metric-value">
                        <?php echo round($stats24h['stats']['avg_response_time'], 0); ?>ms
                    </div>
                    <div class="metric-label">
                        <i class="fas fa-tachometer-alt me-1"></i>
                        Avg Response
                    </div>
                </div>
            </div>
            
            <!-- Offline Checks -->
            <div class="col-md-3">
                <div class="metric-card text-center">
                    <div class="metric-value">
                        <?php echo $stats24h['stats']['offline_checks']; ?>
                    </div>
                    <div class="metric-label">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Downtime Events
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="row">
            <!-- Response Time Chart -->
            <div class="col-md-8">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="fas fa-chart-area text-primary me-2"></i>
                        Response Time Trend (Last 24 Hours)
                    </h5>
                    <canvas id="responseTimeChart" height="100"></canvas>
                </div>
            </div>
            
            <!-- Status Overview -->
            <div class="col-md-4">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="fas fa-pie-chart text-primary me-2"></i>
                        Status Overview
                    </h5>
                    <canvas id="statusChart" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Recent Downtime Events -->
        <div class="row">
            <div class="col-12">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="fas fa-history text-primary me-2"></i>
                        Recent Downtime Events
                    </h5>
                    <?php if ($recentDowntime['success'] && !empty($recentDowntime['downtime'])): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>URL</th>
                                    <th>Response Time</th>
                                    <th>Status Code</th>
                                    <th>Error Message</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentDowntime['downtime'] as $event): ?>
                                <tr>
                                    <td><?php echo date('Y-m-d H:i:s', strtotime($event['check_time'])); ?></td>
                                    <td><?php echo htmlspecialchars($event['site_url']); ?></td>
                                    <td><?php echo $event['response_time']; ?>ms</td>
                                    <td>
                                        <span class="badge bg-danger"><?php echo $event['status_code']; ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($event['error_message'] ?? 'N/A'); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <p>No downtime events in the last 24 hours!</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="row">
            <div class="col-12">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="fas fa-tools text-primary me-2"></i>
                        Quick Actions
                    </h5>
                    <div class="row">
                        <div class="col-md-3">
                            <a href="?check=1" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-sync me-1"></i>
                                Run Check Now
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="dashboard.php" class="btn btn-success w-100 mb-2">
                                <i class="fas fa-refresh me-1"></i>
                                Refresh Dashboard
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="../index.php" class="btn btn-info w-100 mb-2">
                                <i class="fas fa-home me-1"></i>
                                Back to Site
                            </a>
                        </div>
                        <div class="col-md-3">
                            <button onclick="exportData()" class="btn btn-warning w-100 mb-2">
                                <i class="fas fa-download me-1"></i>
                                Export Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Refresh Button -->
    <button class="btn btn-primary refresh-btn" onclick="location.reload()">
        <i class="fas fa-sync"></i>
    </button>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Response Time Chart
        const ctx1 = document.getElementById('responseTimeChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00'],
                datasets: [{
                    label: 'Response Time (ms)',
                    data: [120, 150, 180, 200, 160, 140],
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Status Chart
        const ctx2 = document.getElementById('statusChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Online', 'Offline'],
                datasets: [{
                    data: [
                        <?php echo $stats24h['success'] ? $stats24h['stats']['online_checks'] : 0; ?>,
                        <?php echo $stats24h['success'] ? $stats24h['stats']['offline_checks'] : 0; ?>
                    ],
                    backgroundColor: ['#28a745', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
        
        // Auto refresh every 5 minutes
        setTimeout(() => {
            location.reload();
        }, 300000);
        
        function exportData() {
            alert('Export functionality will be implemented in the next version.');
        }
    </script>
</body>
</html>
