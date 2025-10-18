// Admin Panel JavaScript Functions
document.addEventListener('DOMContentLoaded', function() {
    // Initialize admin panel
    initializeAdminPanel();
    
    // Initialize tooltips
    initializeTooltips();
    
    // Initialize search functionality
    initializeSearch();
    
    // Initialize bulk operations
    initializeBulkOperations();
    
    // Initialize notifications
    initializeNotifications();
    
    // Initialize charts if present
    initializeCharts();
});

// Initialize admin panel
function initializeAdminPanel() {
    console.log('Admin panel initialized');
    
    // Add loading states to forms
    const forms = document.querySelectorAll('form[data-ajax-submit]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            handleAjaxFormSubmit(this);
        });
    });
    
    // Add AJAX functionality to buttons
    const ajaxButtons = document.querySelectorAll('[data-ajax-action]');
    ajaxButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            handleAjaxAction(this);
        });
    });
}

// Initialize tooltips
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Initialize search functionality
function initializeSearch() {
    const searchInputs = document.querySelectorAll('.search-input');
    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const targetTable = document.querySelector(this.getAttribute('data-target'));
            
            if (targetTable) {
                const rows = targetTable.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        });
    });
}

// Initialize bulk operations
function initializeBulkOperations() {
    const selectAllCheckbox = document.querySelector('#selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const bulkActions = document.querySelector('.bulk-actions');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActionsVisibility();
        });
    }
    
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkActionsVisibility();
        });
    });
    
    function updateBulkActionsVisibility() {
        const checkedItems = document.querySelectorAll('.item-checkbox:checked');
        if (checkedItems.length > 0) {
            bulkActions.style.display = 'block';
        } else {
            bulkActions.style.display = 'none';
        }
    }
}

// Initialize notifications
function initializeNotifications() {
    // Create notification container if it doesn't exist
    if (!document.getElementById('notification-container')) {
        const container = document.createElement('div');
        container.id = 'notification-container';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        `;
        document.body.appendChild(container);
    }
}

// Initialize charts
function initializeCharts() {
    // Chart.js initialization if charts are present
    const chartElements = document.querySelectorAll('.chart');
    if (chartElements.length > 0 && typeof Chart !== 'undefined') {
        chartElements.forEach(element => {
            const ctx = element.getContext('2d');
            const chartType = element.dataset.chartType || 'line';
            const chartData = JSON.parse(element.dataset.chartData || '{}');
            
            new Chart(ctx, {
                type: chartType,
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });
    }
}

// Handle AJAX form submission
function handleAjaxFormSubmit(form) {
    const formData = new FormData(form);
    const action = formData.get('action');
    
    if (!action) {
        showNotification('Form action is missing', 'error');
        return;
    }
    
    const submitButton = form.querySelector('button[type="submit"]');
    showLoading(submitButton);
    
    fetch('ajax-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading(submitButton);
        
        if (data.success) {
            showNotification(data.message || 'İşlem başarılı', 'success');
            
            // Reset form if specified
            if (form.hasAttribute('data-reset-on-success')) {
                form.reset();
            }
            
            // Redirect if specified
            if (data.redirect) {
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1000);
            }
            
            // Reload page if specified
            if (data.reload) {
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } else {
            showNotification(data.message || 'Bir hata oluştu', 'error');
        }
    })
    .catch(error => {
        hideLoading(submitButton);
        console.error('AJAX Error:', error);
        showNotification('Bir hata oluştu: ' + error.message, 'error');
    });
}

// Handle AJAX action buttons
function handleAjaxAction(button) {
    const action = button.getAttribute('data-ajax-action');
    const itemId = button.getAttribute('data-item-id');
    
    if (!action) {
        showNotification('Action is missing', 'error');
        return;
    }
    
    if (!itemId) {
        showNotification('Item ID is missing', 'error');
        return;
    }
    
    // Confirm deletion actions
    if (action.includes('delete')) {
        if (!confirm('Bu işlemi gerçekleştirmek istediğinizden emin misiniz?')) {
            return;
        }
    }
    
    showLoading(button);
    
    const formData = new FormData();
    formData.append('action', action);
    formData.append('item_id', itemId);
    
    // Add specific ID field based on action
    if (action.includes('blog')) {
        formData.append('post_id', itemId);
    } else if (action.includes('portfolio')) {
        formData.append('project_id', itemId);
    } else if (action.includes('user')) {
        formData.append('user_id', itemId);
    } else if (action.includes('message')) {
        formData.append('message_id', itemId);
    } else if (action.includes('content')) {
        formData.append('content_id', itemId);
    } else if (action.includes('media')) {
        formData.append('media_id', itemId);
    } else if (action.includes('web_content')) {
        formData.append('content_id', itemId);
    }
    
    fetch('ajax-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading(button);
        
        if (data.success) {
            showNotification(data.message || 'İşlem başarılı', 'success');
            
            // Remove element if it's a delete action
            if (action.includes('delete')) {
                const row = button.closest('tr');
                if (row) {
                    row.remove();
                }
                
                const card = button.closest('.card');
                if (card) {
                    card.remove();
                }
            }
            
            // Update counters if present
            updateCounters();
        } else {
            showNotification(data.message || 'Bir hata oluştu', 'error');
        }
    })
    .catch(error => {
        hideLoading(button);
        console.error('AJAX Error:', error);
        showNotification('Bir hata oluştu: ' + error.message, 'error');
    });
}

// Show notification
function showNotification(message, type = 'info', duration = 5000) {
    const container = document.getElementById('notification-container');
    if (!container) return;
    
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    notification.style.cssText = `
        margin-bottom: 10px;
        animation: slideInRight 0.3s ease-out;
    `;
    
    const icon = getNotificationIcon(type);
    notification.innerHTML = `
        <i class="fas fa-${icon}"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    container.appendChild(notification);
    
    // Auto remove after duration
    setTimeout(() => {
        if (notification.parentNode) {
            notification.classList.remove('show');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }
    }, duration);
}

// Get notification icon
function getNotificationIcon(type) {
    const icons = {
        'success': 'check-circle',
        'error': 'exclamation-circle',
        'warning': 'exclamation-triangle',
        'info': 'info-circle'
    };
    return icons[type] || 'info-circle';
}

// Show loading state
function showLoading(element) {
    if (element) {
        element.disabled = true;
        const originalText = element.innerHTML;
        element.setAttribute('data-original-text', originalText);
        element.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Yükleniyor...';
    }
}

// Hide loading state
function hideLoading(element) {
    if (element) {
        element.disabled = false;
        const originalText = element.getAttribute('data-original-text');
        if (originalText) {
            element.innerHTML = originalText;
            element.removeAttribute('data-original-text');
        }
    }
}

// Update counters
function updateCounters() {
    // Update blog posts counter
    const blogRows = document.querySelectorAll('#blogTable tbody tr');
    const blogCounter = document.querySelector('#blogCounter');
    if (blogCounter) {
        blogCounter.textContent = blogRows.length;
    }
    
    // Update portfolio counter
    const portfolioRows = document.querySelectorAll('#portfolioTable tbody tr');
    const portfolioCounter = document.querySelector('#portfolioCounter');
    if (portfolioCounter) {
        portfolioCounter.textContent = portfolioRows.length;
    }
    
    // Update users counter
    const userRows = document.querySelectorAll('#usersTable tbody tr');
    const userCounter = document.querySelector('#userCounter');
    if (userCounter) {
        userCounter.textContent = userRows.length;
    }
    
    // Update messages counter
    const messageRows = document.querySelectorAll('#messagesTable tbody tr');
    const messageCounter = document.querySelector('#messageCounter');
    if (messageCounter) {
        messageCounter.textContent = messageRows.length;
    }
}

// Utility functions
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('tr-TR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Export functions for global use
window.adminPanel = {
    showNotification,
    showLoading,
    hideLoading,
    handleAjaxFormSubmit,
    handleAjaxAction,
    formatDate,
    formatFileSize,
    updateCounters
};






