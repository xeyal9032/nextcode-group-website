/**
 * Admin Panel AJAX Functions - NextCode Group
 * Modern AJAX implementation for admin panel
 */

class AdminAJAX {
    constructor() {
        this.csrfToken = this.getCSRFToken();
        this.baseUrl = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '') + '/ajax-handler.php';
    }

    getCSRFToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    async makeRequest(action, data = {}, method = 'POST') {
        try {
            const requestData = {
                action: action,
                csrf_token: this.csrfToken,
                ...data
            };

            const options = {
                method: method,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                }
            };

            if (method === 'POST') {
                options.body = new URLSearchParams(requestData);
            } else {
                options.body = new URLSearchParams(requestData);
            }

            const response = await fetch(this.baseUrl, options);
            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message || 'Request failed');
            }

            return result;
        } catch (error) {
            console.error('AJAX Error:', error);
            this.showNotification('Hata: ' + error.message, 'error');
            return { success: false, message: error.message };
        }
    }

    // Message Management
    async updateMessageStatus(messageId, status) {
        const result = await this.makeRequest('update_message_status', {
            message_id: messageId,
            status: status
        });

        if (result.success) {
            this.showNotification('Mesaj durumu güncellendi', 'success');
            this.updateMessageUI(messageId, status);
        }

        return result;
    }

    async deleteMessage(messageId) {
        if (!confirm('Bu mesajı silmek istediğinizden emin misiniz?')) {
            return { success: false, message: 'Cancelled' };
        }

        const result = await this.makeRequest('delete_message', {
            message_id: messageId
        });

        if (result.success) {
            this.showNotification('Mesaj silindi', 'success');
            this.removeMessageElement(messageId);
        }

        return result;
    }

    // Portfolio Management
    async deletePortfolio(projectId) {
        if (!confirm('Bu projeyi silmek istediğinizden emin misiniz?')) {
            return { success: false, message: 'Cancelled' };
        }

        const result = await this.makeRequest('delete_portfolio', {
            project_id: projectId
        });

        if (result.success) {
            this.showNotification('Proje silindi', 'success');
            this.removePortfolioElement(projectId);
        }

        return result;
    }

    // Blog Management
    async deleteBlogPost(postId) {
        if (!confirm('Bu blog yazısını silmek istediğinizden emin misiniz?')) {
            return { success: false, message: 'Cancelled' };
        }

        const result = await this.makeRequest('delete_blog_post', {
            post_id: postId
        });

        if (result.success) {
            this.showNotification('Blog yazısı silindi', 'success');
            this.removeBlogElement(postId);
        }

        return result;
    }

    // User Management
    async deleteUser(userId) {
        if (!confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')) {
            return { success: false, message: 'Cancelled' };
        }

        const result = await this.makeRequest('delete_user', {
            user_id: userId
        });

        if (result.success) {
            this.showNotification('Kullanıcı silindi', 'success');
            this.removeUserElement(userId);
        }

        return result;
    }

    async unlockUser(userId) {
        const result = await this.makeRequest('unlock_user', {
            user_id: userId
        });

        if (result.success) {
            this.showNotification('Kullanıcı kilidi açıldı', 'success');
            this.updateUserStatus(userId, 'unlocked');
        }

        return result;
    }

    // Content Management
    async addContent(pageName, sectionName, contentKey, contentValue, contentType = 'text') {
        const result = await this.makeRequest('add_content', {
            page_name: pageName,
            section_name: sectionName,
            content_key: contentKey,
            content_value: contentValue,
            content_type: contentType
        });

        if (result.success) {
            this.showNotification('İçerik eklendi', 'success');
            this.addContentElement(result.data);
        }

        return result;
    }

    async updateContent(contentId, contentValue) {
        const result = await this.makeRequest('update_content', {
            content_id: contentId,
            content_value: contentValue
        });

        if (result.success) {
            this.showNotification('İçerik güncellendi', 'success');
            this.updateContentElement(contentId, contentValue);
        }

        return result;
    }

    async deleteContent(contentId) {
        if (!confirm('Bu içeriği silmek istediğinizden emin misiniz?')) {
            return { success: false, message: 'Cancelled' };
        }

        const result = await this.makeRequest('delete_content', {
            content_id: contentId
        });

        if (result.success) {
            this.showNotification('İçerik silindi', 'success');
            this.removeContentElement(contentId);
        }

        return result;
    }

    // Dashboard Stats
    async getStats() {
        return await this.makeRequest('get_stats');
    }

    async getRecentActivities() {
        return await this.makeRequest('get_recent_activities');
    }

    // UI Updates
    updateMessageUI(messageId, status) {
        const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
        if (messageElement) {
            const statusElement = messageElement.querySelector('.message-status');
            if (statusElement) {
                statusElement.textContent = status === 'read' ? 'Okundu' : 'Okunmadı';
                statusElement.className = `message-status ${status}`;
            }

            const actionButtons = messageElement.querySelector('.message-actions');
            if (actionButtons) {
                const statusBtn = actionButtons.querySelector('.btn-status');
                if (statusBtn) {
                    if (status === 'read') {
                        statusBtn.innerHTML = '<i class="fas fa-eye-slash"></i> Okunmadı İşaretle';
                        statusBtn.onclick = () => this.updateMessageStatus(messageId, 'unread');
                    } else {
                        statusBtn.innerHTML = '<i class="fas fa-eye"></i> Okundu İşaretle';
                        statusBtn.onclick = () => this.updateMessageStatus(messageId, 'read');
                    }
                }
            }
        }
    }

    removeMessageElement(messageId) {
        const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
        if (messageElement) {
            messageElement.style.opacity = '0';
            messageElement.style.transform = 'translateX(-100%)';
            setTimeout(() => {
                messageElement.remove();
            }, 300);
        }
    }

    removePortfolioElement(projectId) {
        const projectElement = document.querySelector(`[data-project-id="${projectId}"]`);
        if (projectElement) {
            projectElement.style.opacity = '0';
            projectElement.style.transform = 'scale(0.8)';
            setTimeout(() => {
                projectElement.remove();
            }, 300);
        }
    }

    removeBlogElement(postId) {
        const postElement = document.querySelector(`[data-post-id="${postId}"]`);
        if (postElement) {
            postElement.style.opacity = '0';
            postElement.style.transform = 'translateY(-20px)';
            postElement.style.transition = 'all 0.3s ease';
            setTimeout(() => {
                postElement.remove();
                // Update stats if exists
                this.updateBlogStats();
            }, 300);
        }
    }

    removeUserElement(userId) {
        const userElement = document.querySelector(`[data-user-id="${userId}"]`);
        if (userElement) {
            userElement.style.opacity = '0';
            userElement.style.transform = 'translateX(-100%)';
            userElement.style.transition = 'all 0.3s ease';
            setTimeout(() => {
                userElement.remove();
                // Update stats if exists
                this.updateUserStats();
            }, 300);
        }
    }

    updateUserStatus(userId, status) {
        const userElement = document.querySelector(`[data-user-id="${userId}"]`);
        if (userElement) {
            const statusElement = userElement.querySelector('.status-badge');
            if (statusElement) {
                if (status === 'unlocked') {
                    statusElement.textContent = 'Aktif';
                    statusElement.className = 'status-badge status-active';
                } else {
                    statusElement.textContent = 'Kilitli';
                    statusElement.className = 'status-badge status-locked';
                }
            }
            
            // Remove unlock button if user is unlocked
            const unlockBtn = userElement.querySelector('[data-ajax-action="unlock_user"]');
            if (unlockBtn && status === 'unlocked') {
                unlockBtn.remove();
            }
        }
    }

    // Helper methods for stats updates
    updateBlogStats() {
        // Count remaining posts and update stats if stat elements exist
        const remainingPosts = document.querySelectorAll('[data-post-id]').length;
        const totalPostsElement = document.querySelector('.stat-number');
        if (totalPostsElement) {
            totalPostsElement.textContent = remainingPosts;
        }
    }

    updateUserStats() {
        // Count remaining users and update stats if stat elements exist
        const remainingUsers = document.querySelectorAll('[data-user-id]').length;
        const totalUsersElement = document.querySelector('.stat-number');
        if (totalUsersElement) {
            totalUsersElement.textContent = remainingUsers;
        }
    }

    // Content Management UI Updates
    addContentElement(contentData) {
        // Add new content element to the page
        const contentContainer = document.querySelector('.content-list');
        if (contentContainer) {
            const contentElement = document.createElement('div');
            contentElement.className = 'content-item';
            contentElement.setAttribute('data-content-id', contentData.id);
            contentElement.innerHTML = `
                <div class="content-header">
                    <div class="content-info">
                        <div class="content-key">${contentData.content_key}</div>
                        <div class="content-section">${contentData.section_name}</div>
                        <span class="content-type">${contentData.content_type}</span>
                    </div>
                    <div class="content-actions">
                        <button class="btn btn-danger btn-sm" data-ajax-action="delete_content" data-target-id="${contentData.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="content-body">
                    <textarea class="form-control" data-content-id="${contentData.id}" rows="3">${contentData.content_value}</textarea>
                    <button class="btn btn-primary btn-sm" onclick="adminAJAX.updateContent(${contentData.id}, this.previousElementSibling.value)">
                        <i class="fas fa-save"></i> Kaydet
                    </button>
                </div>
            `;
            
            contentContainer.appendChild(contentElement);
            
            // Add smooth animation
            contentElement.style.opacity = '0';
            contentElement.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                contentElement.style.transition = 'all 0.3s ease';
                contentElement.style.opacity = '1';
                contentElement.style.transform = 'translateY(0)';
            }, 100);
        }
    }

    // Search Functionality
    searchContent(query) {
        const searchableElements = document.querySelectorAll('[data-searchable]');
        const searchResults = [];
        
        searchableElements.forEach(element => {
            const text = element.textContent.toLowerCase();
            if (text.includes(query.toLowerCase())) {
                searchResults.push(element);
                element.style.backgroundColor = '#fff3cd';
                element.style.border = '2px solid #ffc107';
            } else {
                element.style.backgroundColor = '';
                element.style.border = '';
            }
        });
        
        return searchResults;
    }

    // Bulk Operations
    selectAll() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"][data-selectable]');
        const selectAllCheckbox = document.querySelector('#select-all');
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
            checkbox.closest('tr').style.backgroundColor = checkbox.checked ? '#e3f2fd' : '';
        });
        
        this.updateBulkActionsVisibility();
    }

    updateBulkActionsVisibility() {
        const selectedItems = document.querySelectorAll('input[type="checkbox"][data-selectable]:checked');
        const bulkActions = document.querySelector('.bulk-actions');
        
        if (bulkActions) {
            bulkActions.style.display = selectedItems.length > 0 ? 'block' : 'none';
            bulkActions.querySelector('.selected-count').textContent = selectedItems.length;
        }
    }

    bulkDelete(type) {
        const selectedItems = document.querySelectorAll('input[type="checkbox"][data-selectable]:checked');
        const ids = Array.from(selectedItems).map(item => item.value);
        
        if (ids.length === 0) {
            this.showNotification('Lütfen silinecek öğeleri seçin', 'warning');
            return;
        }
        
        if (!confirm(`${ids.length} öğeyi silmek istediğinizden emin misiniz?`)) {
            return;
        }
        
        const promises = ids.map(id => {
            return this.makeRequest(`delete_${type}`, { [`${type}_id`]: id });
        });
        
        Promise.all(promises).then(results => {
            const successCount = results.filter(r => r.success).length;
            this.showNotification(`${successCount} öğe başarıyla silindi`, 'success');
            
            // Remove deleted items from UI
            selectedItems.forEach(item => {
                const row = item.closest('tr');
                if (row) {
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(-100%)';
                    row.style.transition = 'all 0.3s ease';
                    setTimeout(() => row.remove(), 300);
                }
            });
            
            this.updateBulkActionsVisibility();
        });
    }

    updateContentElement(contentId, contentValue) {
        const contentElement = document.querySelector(`[data-content-id="${contentId}"]`);
        if (contentElement) {
            const textarea = contentElement.querySelector('textarea');
            if (textarea) {
                textarea.value = contentValue;
                textarea.style.borderColor = '#28a745';
                setTimeout(() => {
                    textarea.style.borderColor = '#e1e5e9';
                }, 2000);
            }
        }
    }

    removeContentElement(contentId) {
        const contentElement = document.querySelector(`[data-content-id="${contentId}"]`);
        if (contentElement) {
            contentElement.style.opacity = '0';
            contentElement.style.transform = 'translateX(-100%)';
            contentElement.style.transition = 'all 0.3s ease';
            setTimeout(() => {
                contentElement.remove();
            }, 300);
        }
    }

    // Notification System
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${this.getNotificationIcon(type)}"></i>
                <span>${message}</span>
            </div>
        `;

        // Add styles if not exists
        if (!document.querySelector('#notification-styles')) {
            const styles = document.createElement('style');
            styles.id = 'notification-styles';
            styles.textContent = `
                .notification {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    padding: 15px 20px;
                    border-radius: 8px;
                    color: white;
                    font-weight: 500;
                    z-index: 10000;
                    transform: translateX(400px);
                    transition: transform 0.3s ease;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                }
                .notification.show {
                    transform: translateX(0);
                }
                .notification-success { background: #28a745; }
                .notification-error { background: #dc3545; }
                .notification-warning { background: #ffc107; color: #333; }
                .notification-info { background: #17a2b8; }
                .notification-content {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }
            `;
            document.head.appendChild(styles);
        }

        document.body.appendChild(notification);

        // Show notification
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);

        // Hide notification
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }

    getNotificationIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    // Loading States
    showLoading(element) {
        if (element) {
            element.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Yükleniyor...';
            element.disabled = true;
        }
    }

    hideLoading(element, originalText) {
        if (element) {
            element.innerHTML = originalText;
            element.disabled = false;
        }
    }
}

// Initialize AJAX system
document.addEventListener('DOMContentLoaded', function() {
    window.adminAJAX = new AdminAJAX();
    
    // Add click handlers for AJAX actions
    document.addEventListener('click', function(e) {
        if (e.target.matches('[data-ajax-action]')) {
            e.preventDefault();
            const action = e.target.getAttribute('data-ajax-action');
            const targetId = e.target.getAttribute('data-target-id');
            
            switch (action) {
                case 'update_message_status':
                    const status = e.target.getAttribute('data-status');
                    adminAJAX.updateMessageStatus(targetId, status);
                    break;
                    
                case 'delete_message':
                    adminAJAX.deleteMessage(targetId);
                    break;
                    
                case 'delete_portfolio':
                    adminAJAX.deletePortfolio(targetId);
                    break;
                    
                case 'delete_blog_post':
                    adminAJAX.deleteBlogPost(targetId);
                    break;
                    
                case 'delete_user':
                    adminAJAX.deleteUser(targetId);
                    break;
                    
                case 'unlock_user':
                    adminAJAX.unlockUser(targetId);
                    break;
                    
                case 'add_content':
                    const pageName = e.target.getAttribute('data-page-name');
                    const sectionName = e.target.getAttribute('data-section-name');
                    const contentKey = e.target.getAttribute('data-content-key');
                    const contentValue = e.target.getAttribute('data-content-value');
                    const contentType = e.target.getAttribute('data-content-type') || 'text';
                    adminAJAX.addContent(pageName, sectionName, contentKey, contentValue, contentType);
                    break;
                    
                case 'update_content':
                    const contentValue = e.target.getAttribute('data-content-value');
                    adminAJAX.updateContent(targetId, contentValue);
                    break;
                    
                case 'delete_content':
                    adminAJAX.deleteContent(targetId);
                    break;
            }
        }
    });
});

// Auto-refresh dashboard stats every 30 seconds
setInterval(async () => {
    if (window.adminAJAX && document.querySelector('.stats-grid')) {
        const stats = await adminAJAX.getStats();
        if (stats.success) {
            // Update stats display
            const statElements = {
                'total_projects': document.querySelector('[data-stat="projects"]'),
                'total_posts': document.querySelector('[data-stat="posts"]'),
                'total_messages': document.querySelector('[data-stat="messages"]'),
                'total_users': document.querySelector('[data-stat="users"]')
            };
            
            Object.keys(stats.data).forEach(key => {
                if (statElements[key]) {
                    statElements[key].textContent = stats.data[key];
                }
            });
        }
    }
}, 30000);
