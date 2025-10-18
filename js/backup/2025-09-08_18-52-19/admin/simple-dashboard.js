/**
 * Simple Dashboard JavaScript
 * NextCode Group - Dashboard Functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Simple Dashboard Loading...');
    
    // Load dashboard stats
    loadDashboardStats();
    
    // Refresh stats every 30 seconds
    setInterval(loadDashboardStats, 30000);
    
    // Initialize quick actions
    initQuickActions();
    
    console.log('Simple Dashboard Loaded Successfully');
});

function loadDashboardStats() {
    console.log('Loading dashboard stats...');
    
    fetch('simple-dashboard-stats.php')
        .then(response => response.json())
        .then(data => {
            console.log('Dashboard stats loaded:', data);
            
            if (data.success) {
                updateStatsCards(data.stats);
                updateQuickActions(data.quick_actions);
                updateRecentActivities(data.recent_activities);
            } else {
                console.error('Failed to load stats:', data.error);
                // Use fallback stats
                if (data.fallback_stats) {
                    updateStatsCards(data.fallback_stats);
                }
            }
        })
        .catch(error => {
            console.error('Error loading dashboard stats:', error);
            // Load fallback stats
            loadFallbackStats();
        });
}

function updateStatsCards(stats) {
    const statsContainer = document.querySelector('.dashboard-stats');
    if (!statsContainer) {
        console.log('Stats container not found');
        return;
    }
    
    statsContainer.innerHTML = '';
    
    stats.forEach(stat => {
        const statCard = document.createElement('div');
        statCard.className = 'stat-card';
        statCard.innerHTML = `
            <div class="stat-icon" style="background-color: ${stat.color || '#667eea'}20; color: ${stat.color || '#667eea'}">
                <i class="fas fa-${stat.icon}"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">${stat.value}</div>
                <div class="stat-label">${stat.label}</div>
            </div>
        `;
        statsContainer.appendChild(statCard);
    });
}

function updateQuickActions(actions) {
    const actionsContainer = document.querySelector('.quick-actions-grid');
    if (!actionsContainer || !actions) {
        console.log('Quick actions container not found or no actions');
        return;
    }
    
    actionsContainer.innerHTML = '';
    
    actions.forEach(action => {
        const actionCard = document.createElement('div');
        actionCard.className = 'quick-action-card';
        actionCard.innerHTML = `
            <div class="action-icon" style="background-color: ${action.color}20; color: ${action.color}">
                <i class="fas fa-${action.icon}"></i>
            </div>
            <div class="action-content">
                <h4>${action.title}</h4>
                <p>${action.description}</p>
            </div>
        `;
        
        if (action.url && action.url !== '#') {
            actionCard.style.cursor = 'pointer';
            actionCard.addEventListener('click', () => {
                window.location.href = action.url;
            });
        }
        
        actionsContainer.appendChild(actionCard);
    });
}

function updateRecentActivities(activities) {
    const activitiesContainer = document.querySelector('.recent-activities-list');
    if (!activitiesContainer || !activities) {
        console.log('Recent activities container not found or no activities');
        return;
    }
    
    activitiesContainer.innerHTML = '';
    
    activities.forEach(activity => {
        const activityItem = document.createElement('div');
        activityItem.className = 'activity-item';
        activityItem.innerHTML = `
            <div class="activity-icon" style="background-color: ${activity.color}20; color: ${activity.color}">
                <i class="fas fa-${activity.icon}"></i>
            </div>
            <div class="activity-content">
                <div class="activity-action">${activity.action}</div>
                <div class="activity-user">by ${activity.user}</div>
            </div>
            <div class="activity-time">${activity.time}</div>
        `;
        activitiesContainer.appendChild(activityItem);
    });
}

function loadFallbackStats() {
    const fallbackStats = [
        {label: 'Toplam Dosya', value: '500+', icon: 'file-alt', color: '#667eea'},
        {label: 'Son Aktivite', value: '1', icon: 'clock', color: '#28a745'},
        {label: 'Aktif Oturum', value: '1', icon: 'user-check', color: '#17a2b8'},
        {label: 'Sistem Durumu', value: 'Online', icon: 'server', color: '#ffc107'}
    ];
    
    updateStatsCards(fallbackStats);
    
    // Fallback quick actions
    const fallbackActions = [
        {title: 'Dosya Yükle', description: 'Yeni dosya yükle', icon: 'upload', url: 'pages/file-manager.php', color: '#667eea'},
        {title: 'Kod Düzenle', description: 'Dosyaları düzenle', icon: 'code', url: 'pages/code-editor.php', color: '#28a745'},
        {title: 'Veritabanı', description: 'DB yönetimi', icon: 'database', url: 'pages/database.php', color: '#17a2b8'},
        {title: 'Yedek Al', description: 'Backup oluştur', icon: 'download', url: '#', color: '#ffc107'}
    ];
    
    updateQuickActions(fallbackActions);
    
    // Fallback activities
    const fallbackActivities = [
        {action: 'Admin Login', user: 'Administrator', time: new Date().toLocaleTimeString(), icon: 'sign-in-alt', color: '#28a745'},
        {action: 'Dashboard Viewed', user: 'Administrator', time: new Date().toLocaleTimeString(), icon: 'eye', color: '#17a2b8'},
        {action: 'System Online', user: 'System', time: new Date().toLocaleTimeString(), icon: 'server', color: '#ffc107'}
    ];
    
    updateRecentActivities(fallbackActivities);
}

function initQuickActions() {
    // Add click handlers for menu items
    const menuItems = document.querySelectorAll('.sidebar-menu a');
    menuItems.forEach(item => {
        item.addEventListener('click', function(e) {
            // Remove active class from all items
            menuItems.forEach(i => i.parentElement.classList.remove('active'));
            // Add active class to clicked item
            this.parentElement.classList.add('active');
        });
    });
    
    // Highlight current page in menu
    const currentPage = window.location.pathname.split('/').pop();
    menuItems.forEach(item => {
        if (item.getAttribute('href') === currentPage) {
            item.parentElement.classList.add('active');
        }
    });
}

// Utility functions
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}-circle"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('tr-TR', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}
