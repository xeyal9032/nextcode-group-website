/**
 * Modern AJAX Handler
 * Güvenli, kolay kullanımlı AJAX sistemi
 */

class AjaxHandler {
    constructor(options = {}) {
        this.baseUrl = options.baseUrl || '/api';
        this.timeout = options.timeout || 30000;
        this.defaultHeaders = {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };
        
        // CSRF token'ı otomatik ekle
        const csrfToken = this.getCSRFToken();
        if (csrfToken) {
            this.defaultHeaders['X-CSRF-Token'] = csrfToken;
        }
        
        // Global error handler
        this.onError = options.onError || this.defaultErrorHandler;
        
        // Global success handler
        this.onSuccess = options.onSuccess || null;
        
        // Loading state
        this.isLoading = false;
        this.activeRequests = 0;
    }
    
    /**
     * GET request
     */
    async get(url, options = {}) {
        return this.request(url, { ...options, method: 'GET' });
    }
    
    /**
     * POST request
     */
    async post(url, data = {}, options = {}) {
        return this.request(url, { 
            ...options, 
            method: 'POST',
            body: JSON.stringify(data)
        });
    }
    
    /**
     * PUT request
     */
    async put(url, data = {}, options = {}) {
        return this.request(url, { 
            ...options, 
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }
    
    /**
     * DELETE request
     */
    async delete(url, options = {}) {
        return this.request(url, { ...options, method: 'DELETE' });
    }
    
    /**
     * Main request method
     */
    async request(url, options = {}) {
        // Full URL oluştur
        const fullUrl = url.startsWith('http') ? url : `${this.baseUrl}/${url.replace(/^\//, '')}`;
        
        // Headers birleştir
        const headers = {
            ...this.defaultHeaders,
            ...options.headers
        };
        
        // Loading state başlat
        this.startLoading();
        
        try {
            // Timeout promise
            const timeoutPromise = new Promise((_, reject) => {
                setTimeout(() => reject(new Error('Request timeout')), this.timeout);
            });
            
            // Fetch promise
            const fetchPromise = fetch(fullUrl, {
                method: options.method || 'GET',
                headers: headers,
                body: options.body,
                credentials: 'same-origin'
            });
            
            // Race between fetch and timeout
            const response = await Promise.race([fetchPromise, timeoutPromise]);
            
            // Response'u parse et
            const data = await this.parseResponse(response);
            
            // Loading state bitir
            this.stopLoading();
            
            // Success handler
            if (this.onSuccess) {
                this.onSuccess(data);
            }
            
            return {
                success: true,
                data: data,
                status: response.status
            };
            
        } catch (error) {
            this.stopLoading();
            return this.handleError(error, url);
        }
    }
    
    /**
     * Form submit handler
     */
    async submitForm(form, options = {}) {
        if (typeof form === 'string') {
            form = document.querySelector(form);
        }
        
        if (!form) {
            throw new Error('Form not found');
        }
        
        // Form validation
        if (!form.checkValidity()) {
            form.reportValidity();
            return { success: false, error: 'Form validation failed' };
        }
        
        // Form data topla
        const formData = new FormData(form);
        const data = {};
        
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        
        // Submit button'u disable et
        const submitBtn = form.querySelector('[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.dataset.originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Gönderiliyor...';
        }
        
        try {
            // AJAX request
            const result = await this.post(
                options.url || form.action,
                data,
                options
            );
            
            // Success
            if (result.success) {
                this.showNotification('success', options.successMessage || 'İşlem başarılı!');
                
                if (options.resetForm !== false) {
                    form.reset();
                }
                
                if (options.onSuccess) {
                    options.onSuccess(result.data);
                }
            }
            
            return result;
            
        } finally {
            // Submit button'u aktif et
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = submitBtn.dataset.originalText;
            }
        }
    }
    
    /**
     * File upload
     */
    async uploadFile(url, file, options = {}) {
        const formData = new FormData();
        formData.append(options.fieldName || 'file', file);
        
        // Ek alanlar
        if (options.data) {
            for (let key in options.data) {
                formData.append(key, options.data[key]);
            }
        }
        
        this.startLoading();
        
        try {
            const response = await fetch(`${this.baseUrl}/${url}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-Token': this.getCSRFToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData,
                credentials: 'same-origin'
            });
            
            const data = await this.parseResponse(response);
            
            this.stopLoading();
            
            // Progress callback
            if (options.onProgress) {
                options.onProgress(100);
            }
            
            return {
                success: true,
                data: data
            };
            
        } catch (error) {
            this.stopLoading();
            return this.handleError(error, url);
        }
    }
    
    /**
     * Response parse
     */
    async parseResponse(response) {
        const contentType = response.headers.get('content-type');
        
        if (contentType && contentType.includes('application/json')) {
            return await response.json();
        }
        
        return await response.text();
    }
    
    /**
     * Error handler
     */
    handleError(error, url) {
        console.error('AJAX Error:', error);
        
        const errorObj = {
            success: false,
            error: error.message,
            url: url
        };
        
        if (this.onError) {
            this.onError(errorObj);
        }
        
        this.showNotification('error', error.message);
        
        return errorObj;
    }
    
    /**
     * Default error handler
     */
    defaultErrorHandler(error) {
        console.error('Request failed:', error);
    }
    
    /**
     * CSRF token al
     */
    getCSRFToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : null;
    }
    
    /**
     * Loading state başlat
     */
    startLoading() {
        this.activeRequests++;
        this.isLoading = true;
        
        // Global loading indicator göster
        this.showLoadingIndicator();
    }
    
    /**
     * Loading state bitir
     */
    stopLoading() {
        this.activeRequests--;
        
        if (this.activeRequests === 0) {
            this.isLoading = false;
            this.hideLoadingIndicator();
        }
    }
    
    /**
     * Loading indicator göster
     */
    showLoadingIndicator() {
        let indicator = document.getElementById('ajax-loading-indicator');
        
        if (!indicator) {
            indicator = document.createElement('div');
            indicator.id = 'ajax-loading-indicator';
            indicator.className = 'ajax-loading-indicator';
            indicator.innerHTML = '<div class="ajax-spinner"></div>';
            document.body.appendChild(indicator);
        }
        
        indicator.classList.add('active');
    }
    
    /**
     * Loading indicator gizle
     */
    hideLoadingIndicator() {
        const indicator = document.getElementById('ajax-loading-indicator');
        if (indicator) {
            indicator.classList.remove('active');
        }
    }
    
    /**
     * Notification göster
     */
    showNotification(type, message) {
        const notification = document.createElement('div');
        notification.className = `ajax-notification ajax-notification--${type}`;
        
        const icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
        
        notification.innerHTML = `
            <div class="ajax-notification__content">
                <i class="fas fa-${icon}"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animasyon için delay
        setTimeout(() => notification.classList.add('show'), 10);
        
        // Otomatik kapat
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    
    /**
     * Retry mekanizması
     */
    async retry(fn, retries = 3, delay = 1000) {
        for (let i = 0; i < retries; i++) {
            try {
                return await fn();
            } catch (error) {
                if (i === retries - 1) throw error;
                await new Promise(resolve => setTimeout(resolve, delay));
            }
        }
    }
}

// Global instance
window.ajax = new AjaxHandler({
    baseUrl: '/api',
    timeout: 30000
});

// Inject CSS
const style = document.createElement('style');
style.textContent = `
    .ajax-loading-indicator {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: transparent;
        z-index: 99999;
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .ajax-loading-indicator.active {
        opacity: 1;
    }
    
    .ajax-spinner {
        height: 3px;
        background: linear-gradient(90deg, #667eea, #764ba2);
        animation: loading 1s infinite;
    }
    
    @keyframes loading {
        0% { width: 0%; margin-left: 0%; }
        50% { width: 50%; margin-left: 25%; }
        100% { width: 0%; margin-left: 100%; }
    }
    
    .ajax-notification {
        position: fixed;
        top: -100px;
        left: 50%;
        transform: translateX(-50%);
        background: white;
        padding: 1rem 2rem;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        z-index: 100000;
        transition: top 0.3s;
        min-width: 300px;
    }
    
    .ajax-notification.show {
        top: 2rem;
    }
    
    .ajax-notification__content {
        display: flex;
        align-items: center;
        gap: 1rem;
        font-size: 1rem;
    }
    
    .ajax-notification--success {
        border-left: 4px solid #10b981;
    }
    
    .ajax-notification--success i {
        color: #10b981;
    }
    
    .ajax-notification--error {
        border-left: 4px solid #ef4444;
    }
    
    .ajax-notification--error i {
        color: #ef4444;
    }
    
    .ajax-notification--info {
        border-left: 4px solid #3b82f6;
    }
    
    .ajax-notification--info i {
        color: #3b82f6;
    }
`;
document.head.appendChild(style);

console.log('🚀 AJAX Handler initialized');


