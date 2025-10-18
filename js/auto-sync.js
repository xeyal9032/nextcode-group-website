// Otomatik Senkronizasyon JavaScript Sistemi
class AutoSyncManager {
    constructor() {
        this.syncInterval = 30000; // 30 saniye
        this.lastSyncTime = localStorage.getItem('lastSyncTime') || 0;
        this.isOnline = navigator.onLine;
        this.syncQueue = JSON.parse(localStorage.getItem('syncQueue') || '[]');
        
        this.init();
    }
    
    init() {
        // Online/offline event listeners
        window.addEventListener('online', () => {
            this.isOnline = true;
            this.processSyncQueue();
        });
        
        window.addEventListener('offline', () => {
            this.isOnline = false;
        });
        
        // Page visibility change
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden && this.isOnline) {
                this.checkForUpdates();
            }
        });
        
        // Start periodic sync
        this.startPeriodicSync();
        
        // Listen for custom sync events
        document.addEventListener('dataChanged', (e) => {
            this.handleDataChange(e.detail);
        });
        
        // Listen for API responses
        this.interceptAPIResponses();
    }
    
    startPeriodicSync() {
        setInterval(() => {
            if (this.isOnline && !document.hidden) {
                this.checkForUpdates();
            }
        }, this.syncInterval);
    }
    
    async checkForUpdates() {
        try {
            const response = await fetch('/api/auto-sync.php', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.data) {
                    this.handleSyncStatus(data.data);
                }
            }
        } catch (error) {
            console.log('Sync check failed:', error);
        }
    }
    
    handleSyncStatus(syncData) {
        const pendingCount = syncData.find(item => item.sync_status === 'pending')?.count || 0;
        
        if (pendingCount > 0) {
            this.showSyncNotification(`${pendingCount} güncelleme bekliyor`);
            this.processSyncQueue();
        }
    }
    
    async processSyncQueue() {
        if (!this.isOnline || this.syncQueue.length === 0) return;
        
        const queue = [...this.syncQueue];
        this.syncQueue = [];
        localStorage.setItem('syncQueue', JSON.stringify(this.syncQueue));
        
        for (const item of queue) {
            try {
                await this.syncItem(item);
            } catch (error) {
                console.error('Sync item failed:', error);
                // Re-add to queue if failed
                this.syncQueue.push(item);
            }
        }
        
        localStorage.setItem('syncQueue', JSON.stringify(this.syncQueue));
    }
    
    async syncItem(item) {
        const response = await fetch('/api/auto-sync.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'log_change',
                table_name: item.table,
                record_id: item.recordId,
                action: item.action,
                old_data: item.oldData,
                new_data: item.newData
            })
        });
        
        if (response.ok) {
            const result = await response.json();
            if (result.success) {
                this.showSyncNotification('Veri senkronize edildi');
                this.invalidateCache(item.table);
            }
        }
    }
    
    handleDataChange(detail) {
        const syncItem = {
            table: detail.table,
            recordId: detail.recordId,
            action: detail.action,
            oldData: detail.oldData,
            newData: detail.newData,
            timestamp: Date.now()
        };
        
        if (this.isOnline) {
            this.syncItem(syncItem).catch(() => {
                // If sync fails, add to queue
                this.syncQueue.push(syncItem);
                localStorage.setItem('syncQueue', JSON.stringify(this.syncQueue));
            });
        } else {
            // Add to queue for later sync
            this.syncQueue.push(syncItem);
            localStorage.setItem('syncQueue', JSON.stringify(this.syncQueue));
        }
        
        // Invalidate local cache
        this.invalidateCache(detail.table);
    }
    
    async invalidateCache(tableName) {
        try {
            await fetch('/api/auto-sync.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'invalidate_cache',
                    cache_key: tableName,
                    cache_type: 'api',
                    reason: 'Auto sync invalidation'
                })
            });
            
            // Clear local storage cache
            this.clearLocalCache(tableName);
            
            // Reload affected components
            this.reloadAffectedComponents(tableName);
            
        } catch (error) {
            console.error('Cache invalidation failed:', error);
        }
    }
    
    clearLocalCache(tableName) {
        const cacheKeys = Object.keys(localStorage).filter(key => 
            key.includes(tableName) || key.includes('cache')
        );
        
        cacheKeys.forEach(key => {
            localStorage.removeItem(key);
        });
    }
    
    reloadAffectedComponents(tableName) {
        const components = {
            'contact_info': ['contact-info-loader', 'contact-cards'],
            'site_settings': ['site-settings', 'theme-settings'],
            'site_content': ['dynamic-content', 'page-content']
        };
        
        if (components[tableName]) {
            components[tableName].forEach(component => {
                const elements = document.querySelectorAll(`[data-component="${component}"]`);
                elements.forEach(element => {
                    this.reloadComponent(element);
                });
            });
        }
    }
    
    reloadComponent(element) {
        const componentType = element.dataset.component;
        
        switch (componentType) {
            case 'contact-info-loader':
                if (window.loadContactInfo) {
                    window.loadContactInfo();
                }
                break;
                
            case 'contact-cards':
                this.reloadContactCards(element);
                break;
                
            case 'dynamic-content':
                this.reloadDynamicContent(element);
                break;
                
            default:
                // Generic reload
                element.innerHTML = '<div class="loading">Yükleniyor...</div>';
                setTimeout(() => {
                    location.reload();
                }, 1000);
        }
    }
    
    async reloadContactCards(container) {
        try {
            const response = await fetch('/api/contact-info.php');
            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    this.updateContactCards(container, data.data);
                }
            }
        } catch (error) {
            console.error('Contact cards reload failed:', error);
        }
    }
    
    updateContactCards(container, data) {
        const cards = container.querySelectorAll('.contact-card');
        
        cards.forEach(card => {
            const type = card.classList.contains('contact-card--address') ? 'address' :
                        card.classList.contains('contact-card--phone') ? 'phone' :
                        card.classList.contains('contact-card--email') ? 'email' :
                        card.classList.contains('contact-card--hours') ? 'hours' : null;
            
            if (type && data[type]) {
                const textElement = card.querySelector('.card-text');
                if (textElement) {
                    textElement.textContent = data[type];
                }
            }
        });
    }
    
    async reloadDynamicContent(container) {
        try {
            const response = await fetch('/api/content.php');
            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    container.innerHTML = data.html;
                }
            }
        } catch (error) {
            console.error('Dynamic content reload failed:', error);
        }
    }
    
    interceptAPIResponses() {
        const originalFetch = window.fetch;
        
        window.fetch = async (...args) => {
            const response = await originalFetch(...args);
            
            // Check if this is a data modification request
            if (args[1] && args[1].method && ['POST', 'PUT', 'DELETE', 'PATCH'].includes(args[1].method)) {
                const url = args[0];
                
                // Extract table name from URL
                const tableMatch = url.match(/\/api\/([^\/]+)\.php/);
                if (tableMatch) {
                    const tableName = tableMatch[1];
                    
                    // Trigger sync event
                    document.dispatchEvent(new CustomEvent('dataChanged', {
                        detail: {
                            table: tableName,
                            action: args[1].method.toLowerCase(),
                            timestamp: Date.now()
                        }
                    }));
                }
            }
            
            return response;
        };
    }
    
    showSyncNotification(message) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'sync-notification';
        notification.innerHTML = `
            <div class="sync-notification-content">
                <i class="fas fa-sync-alt"></i>
                <span>${message}</span>
            </div>
        `;
        
        // Add styles
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10000;
            animation: slideIn 0.3s ease;
        `;
        
        // Add animation styles
        if (!document.querySelector('#sync-notification-styles')) {
            const style = document.createElement('style');
            style.id = 'sync-notification-styles';
            style.textContent = `
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOut {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
        }
        
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
    
    // Public methods for manual sync
    async manualSync() {
        this.showSyncNotification('Manuel senkronizasyon başlatılıyor...');
        await this.processSyncQueue();
        await this.checkForUpdates();
    }
    
    getSyncStatus() {
        return {
            isOnline: this.isOnline,
            queueLength: this.syncQueue.length,
            lastSyncTime: this.lastSyncTime
        };
    }
}

// Initialize auto sync when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.autoSyncManager = new AutoSyncManager();
    
    // Add manual sync button to admin areas
    if (document.querySelector('.admin-panel') || document.querySelector('[data-admin="true"]')) {
        const syncButton = document.createElement('button');
        syncButton.innerHTML = '<i class="fas fa-sync-alt"></i> Senkronize Et';
        syncButton.className = 'btn btn-primary sync-button';
        syncButton.onclick = () => window.autoSyncManager.manualSync();
        
        const adminPanel = document.querySelector('.admin-panel') || document.querySelector('[data-admin="true"]');
        if (adminPanel) {
            adminPanel.appendChild(syncButton);
        }
    }
});

// Export for use in other scripts
window.AutoSyncManager = AutoSyncManager;
