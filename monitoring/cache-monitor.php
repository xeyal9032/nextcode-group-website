<?php
/**
 * Cache Performance Monitor
 * Cache performansını izler ve raporlar
 */

define('SECURE_ACCESS', true);
require_once __DIR__ . '/../config/cache-manager.php';

class CacheMonitor
{
    private $logFile;
    
    public function __construct()
    {
        $this->logFile = __DIR__ . '/../logs/cache-performance.log';
        
        // Log dizini oluştur
        $dir = dirname($this->logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
    
    /**
     * Cache performansını logla
     */
    public function logPerformance()
    {
        $stats = cache_stats();
        
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'stats' => $stats
        ];
        
        file_put_contents(
            $this->logFile, 
            json_encode($logEntry) . "\n", 
            FILE_APPEND
        );
    }
    
    /**
     * Cache performans raporu oluştur
     */
    public function generateReport()
    {
        if (!file_exists($this->logFile)) {
            return ['error' => 'No log data available'];
        }
        
        $lines = file($this->logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $totalHits = 0;
        $totalMisses = 0;
        $totalWrites = 0;
        $count = 0;
        
        foreach (array_slice($lines, -100) as $line) { // Son 100 kayıt
            $data = json_decode($line, true);
            if ($data && isset($data['stats'])) {
                $totalHits += $data['stats']['hits'] ?? 0;
                $totalMisses += $data['stats']['misses'] ?? 0;
                $totalWrites += $data['stats']['writes'] ?? 0;
                $count++;
            }
        }
        
        $hitRatio = $totalHits + $totalMisses > 0 
            ? round($totalHits / ($totalHits + $totalMisses) * 100, 2) 
            : 0;
        
        return [
            'period' => 'Last 100 records',
            'total_hits' => $totalHits,
            'total_misses' => $totalMisses,
            'total_writes' => $totalWrites,
            'hit_ratio' => $hitRatio . '%',
            'average_hits_per_request' => $count > 0 ? round($totalHits / $count, 2) : 0,
            'recommendations' => $this->getRecommendations($hitRatio)
        ];
    }
    
    /**
     * Performans önerileri
     */
    private function getRecommendations($hitRatio)
    {
        $recommendations = [];
        
        if ($hitRatio < 50) {
            $recommendations[] = 'Cache hit ratio düşük. TTL değerlerini artırın.';
            $recommendations[] = 'Daha fazla veri cache\'lenebilir.';
        } elseif ($hitRatio < 70) {
            $recommendations[] = 'Cache performansı orta seviyede. Kritik endpoint\'leri cache\'leyin.';
        } else {
            $recommendations[] = 'Cache performansı iyi durumda!';
        }
        
        return $recommendations;
    }
    
    /**
     * HTML rapor oluştur
     */
    public function renderReport()
    {
        $report = $this->generateReport();
        $currentStats = cache_stats();
        
        ?>
        <!DOCTYPE html>
        <html lang="tr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Cache Performance Monitor</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                
                body {
                    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    padding: 2rem;
                    min-height: 100vh;
                }
                
                .container {
                    max-width: 1200px;
                    margin: 0 auto;
                }
                
                .header {
                    background: white;
                    padding: 2rem;
                    border-radius: 12px;
                    margin-bottom: 2rem;
                    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
                }
                
                .header h1 {
                    color: #667eea;
                    margin-bottom: 0.5rem;
                }
                
                .header p {
                    color: #6b7280;
                }
                
                .grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                    gap: 1.5rem;
                    margin-bottom: 2rem;
                }
                
                .card {
                    background: white;
                    padding: 1.5rem;
                    border-radius: 12px;
                    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
                }
                
                .card h3 {
                    color: #667eea;
                    font-size: 0.9rem;
                    margin-bottom: 1rem;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
                
                .metric {
                    font-size: 2.5rem;
                    font-weight: 700;
                    color: #1f2937;
                }
                
                .metric-label {
                    color: #6b7280;
                    font-size: 0.875rem;
                    margin-top: 0.5rem;
                }
                
                .recommendations {
                    background: white;
                    padding: 2rem;
                    border-radius: 12px;
                    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
                }
                
                .recommendations h2 {
                    color: #667eea;
                    margin-bottom: 1rem;
                }
                
                .recommendations ul {
                    list-style: none;
                    padding: 0;
                }
                
                .recommendations li {
                    padding: 0.75rem;
                    margin-bottom: 0.5rem;
                    background: #f3f4f6;
                    border-radius: 8px;
                    border-left: 4px solid #667eea;
                }
                
                .chart-container {
                    background: white;
                    padding: 2rem;
                    border-radius: 12px;
                    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
                    margin-top: 2rem;
                }
                
                .hit-ratio {
                    font-size: 3rem;
                    font-weight: 700;
                    background: linear-gradient(135deg, #667eea, #764ba2);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    background-clip: text;
                }
                
                .badge {
                    display: inline-block;
                    padding: 0.25rem 0.75rem;
                    border-radius: 20px;
                    font-size: 0.75rem;
                    font-weight: 600;
                    margin-top: 0.5rem;
                }
                
                .badge-success {
                    background: #d1fae5;
                    color: #065f46;
                }
                
                .badge-warning {
                    background: #fef3c7;
                    color: #92400e;
                }
                
                .badge-danger {
                    background: #fee2e2;
                    color: #991b1b;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>📊 Cache Performance Monitor</h1>
                    <p>Real-time cache performance tracking and analytics</p>
                </div>
                
                <div class="grid">
                    <div class="card">
                        <h3>Cache Hits</h3>
                        <div class="metric"><?php echo number_format($report['total_hits']); ?></div>
                        <div class="metric-label">Successful cache retrievals</div>
                    </div>
                    
                    <div class="card">
                        <h3>Cache Misses</h3>
                        <div class="metric"><?php echo number_format($report['total_misses']); ?></div>
                        <div class="metric-label">Cache not found</div>
                    </div>
                    
                    <div class="card">
                        <h3>Total Writes</h3>
                        <div class="metric"><?php echo number_format($report['total_writes']); ?></div>
                        <div class="metric-label">Data written to cache</div>
                    </div>
                    
                    <div class="card">
                        <h3>Hit Ratio</h3>
                        <div class="hit-ratio"><?php echo $report['hit_ratio']; ?></div>
                        <?php
                        $ratio = floatval($report['hit_ratio']);
                        $badgeClass = $ratio >= 70 ? 'badge-success' : ($ratio >= 50 ? 'badge-warning' : 'badge-danger');
                        $badgeText = $ratio >= 70 ? 'Excellent' : ($ratio >= 50 ? 'Good' : 'Needs Improvement');
                        ?>
                        <span class="badge <?php echo $badgeClass; ?>"><?php echo $badgeText; ?></span>
                    </div>
                </div>
                
                <div class="chart-container">
                    <h2 style="color: #667eea; margin-bottom: 1rem;">Current Session Stats</h2>
                    <div class="grid">
                        <div>
                            <strong>Hits:</strong> <?php echo $currentStats['hits']; ?>
                        </div>
                        <div>
                            <strong>Misses:</strong> <?php echo $currentStats['misses']; ?>
                        </div>
                        <div>
                            <strong>Writes:</strong> <?php echo $currentStats['writes']; ?>
                        </div>
                        <div>
                            <strong>Hit Ratio:</strong> <?php echo $currentStats['hit_ratio']; ?>%
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($report['recommendations'])): ?>
                <div class="recommendations">
                    <h2>💡 Recommendations</h2>
                    <ul>
                        <?php foreach ($report['recommendations'] as $recommendation): ?>
                            <li><?php echo htmlspecialchars($recommendation); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <div class="chart-container" style="margin-top: 2rem;">
                    <h2 style="color: #667eea; margin-bottom: 1rem;">Cache Layers</h2>
                    <?php if (isset($currentStats['layers'])): ?>
                        <div class="grid">
                            <?php foreach ($currentStats['layers'] as $name => $layerStats): ?>
                                <div class="card">
                                    <h3><?php echo ucfirst($name); ?> Cache</h3>
                                    <?php if (isset($layerStats['connected'])): ?>
                                        <span class="badge <?php echo $layerStats['connected'] ? 'badge-success' : 'badge-danger'; ?>">
                                            <?php echo $layerStats['connected'] ? 'Connected' : 'Disconnected'; ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (isset($layerStats['keys'])): ?>
                                        <div class="metric-label">Keys: <?php echo $layerStats['keys']; ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <script>
                // Auto refresh every 30 seconds
                setTimeout(() => {
                    location.reload();
                }, 30000);
            </script>
        </body>
        </html>
        <?php
    }
}

// Display report if accessed directly
if (basename($_SERVER['PHP_SELF']) === 'cache-monitor.php') {
    $monitor = new CacheMonitor();
    $monitor->renderReport();
}


