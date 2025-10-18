                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (for AJAX compatibility) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom Admin JS -->
    <script src="../assets/js/admin.js"></script>
    
    <!-- Notification System -->
    <div id="notification-container"></div>
    
    <script>
        // Admin AJAX Helper
        const adminAJAX = {
            showNotification: function(message, type = 'info', duration = 5000) {
                const notification = document.createElement('div');
                notification.className = `notification ${type}`;
                notification.innerHTML = `
                    <i class="fas fa-${this.getIcon(type)}"></i>
                    ${message}
                `;
                
                document.getElementById('notification-container').appendChild(notification);
                
                // Show notification
                setTimeout(() => {
                    notification.classList.add('show');
                }, 100);
                
                // Hide notification
                setTimeout(() => {
                    notification.classList.remove('show');
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }, duration);
            },
            
            getIcon: function(type) {
                const icons = {
                    'success': 'check-circle',
                    'error': 'exclamation-circle',
                    'warning': 'exclamation-triangle',
                    'info': 'info-circle'
                };
                return icons[type] || 'info-circle';
            },
            
            showLoading: function(element) {
                if (element) {
                    element.disabled = true;
                    const originalText = element.innerHTML;
                    element.setAttribute('data-original-text', originalText);
                    element.innerHTML = '<span class="loading"></span> Yükleniyor...';
                }
            },
            
            hideLoading: function(element) {
                if (element) {
                    element.disabled = false;
                    const originalText = element.getAttribute('data-original-text');
                    if (originalText) {
                        element.innerHTML = originalText;
                        element.removeAttribute('data-original-text');
                    }
                }
            },
            
            handleAjaxResponse: function(response, element = null) {
                this.hideLoading(element);
                
                if (response.success) {
                    this.showNotification(response.message || 'İşlem başarılı', 'success');
                    return true;
                } else {
                    this.showNotification(response.message || 'Bir hata oluştu', 'error');
                    return false;
                }
            }
        };
        
        // Global AJAX Error Handler
        $(document).ajaxError(function(event, xhr, settings, thrownError) {
            adminAJAX.showNotification('AJAX hatası: ' + thrownError, 'error');
        });
        
        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-dismiss alerts
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
            
            // Confirm delete actions
            const deleteButtons = document.querySelectorAll('[data-confirm-delete]');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const message = this.getAttribute('data-confirm-message') || 'Bu işlemi geri alamazsınız. Devam etmek istediğinizden emin misiniz?';
                    if (!confirm(message)) {
                        e.preventDefault();
                    }
                });
            });
            
            // Auto-submit forms with AJAX
            const ajaxForms = document.querySelectorAll('form[data-ajax-submit]');
            ajaxForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const submitButton = this.querySelector('button[type="submit"]');
                    adminAJAX.showLoading(submitButton);
                    
                    const formData = new FormData(this);
                    
                    fetch(this.action || 'ajax-handler.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (adminAJAX.handleAjaxResponse(data, submitButton)) {
                            // Reload page or update UI based on form data
                            const action = formData.get('action');
                            if (action && ['add_role', 'update_role', 'delete_role'].includes(action)) {
                                setTimeout(() => location.reload(), 1000);
                            }
                        }
                    })
                    .catch(error => {
                        adminAJAX.showNotification('Form gönderim hatası: ' + error.message, 'error');
                        adminAJAX.hideLoading(submitButton);
                    });
                });
            });
        });
        
        // Utility functions
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('tr-TR') + ' ' + date.toLocaleTimeString('tr-TR', {hour: '2-digit', minute: '2-digit'});
        }
        
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        
        // Search functionality
        function searchTable(inputId, tableId) {
            const input = document.getElementById(inputId);
            const table = document.getElementById(tableId);
            
            if (input && table) {
                input.addEventListener('keyup', function() {
                    const filter = this.value.toLowerCase();
                    const rows = table.querySelectorAll('tbody tr');
                    
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(filter) ? '' : 'none';
                    });
                });
            }
        }
        
        // Bulk actions
        function selectAll(checkboxId, itemCheckboxes) {
            const selectAllCheckbox = document.getElementById(checkboxId);
            const itemCheckboxesElements = document.querySelectorAll(itemCheckboxes);
            
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    itemCheckboxesElements.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateBulkActionsVisibility();
                });
            }
            
            itemCheckboxesElements.forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkActionsVisibility);
            });
        }
        
        function updateBulkActionsVisibility() {
            const checkedBoxes = document.querySelectorAll('input[name="selected_items[]"]:checked');
            const bulkActions = document.querySelectorAll('.bulk-actions');
            
            bulkActions.forEach(action => {
                action.style.display = checkedBoxes.length > 0 ? 'block' : 'none';
            });
        }
        
        function bulkDelete(actionUrl, confirmMessage = 'Seçili öğeleri silmek istediğinizden emin misiniz?') {
            const checkedBoxes = document.querySelectorAll('input[name="selected_items[]"]:checked');
            
            if (checkedBoxes.length === 0) {
                adminAJAX.showNotification('Lütfen silinecek öğeleri seçin', 'warning');
                return;
            }
            
            if (!confirm(confirmMessage)) {
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'bulk_delete');
            
            checkedBoxes.forEach(checkbox => {
                formData.append('selected_items[]', checkbox.value);
            });
            
            fetch(actionUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (adminAJAX.handleAjaxResponse(data)) {
                    setTimeout(() => location.reload(), 1000);
                }
            })
            .catch(error => {
                adminAJAX.showNotification('Toplu silme hatası: ' + error.message, 'error');
            });
        }
        
        // Theme toggle (if needed)
        function toggleTheme() {
            document.body.classList.toggle('dark-theme');
            localStorage.setItem('darkTheme', document.body.classList.contains('dark-theme'));
        }
        
        // Load saved theme
        if (localStorage.getItem('darkTheme') === 'true') {
            document.body.classList.add('dark-theme');
        }
        
        // Auto-save form data
        function autoSaveForm(formId, interval = 30000) {
            const form = document.getElementById(formId);
            if (!form) return;
            
            setInterval(() => {
                const formData = new FormData(form);
                const data = {};
                for (let [key, value] of formData.entries()) {
                    data[key] = value;
                }
                localStorage.setItem(`autosave_${formId}`, JSON.stringify(data));
            }, interval);
        }
        
        // Load auto-saved data
        function loadAutoSavedData(formId) {
            const form = document.getElementById(formId);
            if (!form) return;
            
            const savedData = localStorage.getItem(`autosave_${formId}`);
            if (savedData) {
                try {
                    const data = JSON.parse(savedData);
                    Object.keys(data).forEach(key => {
                        const input = form.querySelector(`[name="${key}"]`);
                        if (input && input.type !== 'password') {
                            input.value = data[key];
                        }
                    });
                } catch (e) {
                    console.error('Auto-save data parse error:', e);
                }
            }
        }
        
        // Clear auto-saved data
        function clearAutoSavedData(formId) {
            localStorage.removeItem(`autosave_${formId}`);
        }
        
        // Page visibility API for auto-refresh
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden && window.location.pathname.includes('admin/')) {
                // Page became visible, check for updates
                console.log('Page visible - checking for updates...');
            }
        });
        
        // Performance monitoring
        window.addEventListener('load', function() {
            if (window.performance && window.performance.timing) {
                const loadTime = window.performance.timing.loadEventEnd - window.performance.timing.navigationStart;
                console.log(`Page load time: ${loadTime}ms`);
            }
        });
    </script>
</body>
</html>
