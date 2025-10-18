// Site İçeriği Otomatik Yükleyici - Merkezi İçerik Yönetimi
class SiteContentLoader {
    constructor() {
        this.cache = new Map();
        this.lastUpdate = null;
        this.autoRefreshInterval = null;
        this.isInitialized = false;
        
        // Otomatik yenileme süresi (5 dakika)
        this.refreshInterval = 5 * 60 * 1000;
        
        this.init();
    }

    async init() {
        try {
            console.log('🚀 Site Content Loader başlatılıyor...');
            
            // İlk yükleme
            await this.loadAllContent();
            
            // Otomatik yenileme başlat
            this.startAutoRefresh();
            
            // Admin paneli değişikliklerini dinle
            this.listenForAdminChanges();
            
            this.isInitialized = true;
            console.log('✅ Site Content Loader başarıyla başlatıldı');
            
        } catch (error) {
            console.error('❌ Site Content Loader başlatma hatası:', error);
        }
    }

    // Tüm içeriği yükle
    async loadAllContent() {
        try {
            console.log('📥 Tüm site içeriği yükleniyor...');
            
            const response = await fetch('/api/site-content.php');
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            
            if (data.success && data.data) {
                // Cache'i temizle
                this.clearCache();
                
                // Yeni verileri cache'e ekle
                data.data.forEach(item => {
                    this.cache.set(item.content_key, {
                        value: item.content_value,
                        type: item.content_type,
                        section: item.page_section,
                        updated_at: item.updated_at,
                        is_active: item.is_active
                    });
                });
                
                // Sayfa içeriğini güncelle
                this.updateAllContent();
                
                this.lastUpdate = new Date();
                console.log(`✅ ${this.cache.size} içerik yüklendi ve güncellendi`);
                
            } else {
                throw new Error(data.error || 'Veri yüklenemedi');
            }
            
        } catch (error) {
            console.error('❌ İçerik yükleme hatası:', error);
            // Hata durumunda cache'den yükle
            this.loadFromCache();
        }
    }

    // Belirli bir bölümü yükle
    async loadSection(section) {
        try {
            const response = await fetch(`/api/site-content.php?section=${section}`);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            
            if (data.success && data.data) {
                // Sadece bu bölümü güncelle
                data.data.forEach(item => {
                    this.cache.set(item.content_key, {
                        value: item.content_value,
                        type: item.content_type,
                        section: item.page_section,
                        updated_at: item.updated_at,
                        is_active: item.is_active
                    });
                });
                
                // Bu bölümü güncelle
                this.updateSectionContent(section);
                
                console.log(`✅ ${section} bölümü güncellendi`);
            }
            
        } catch (error) {
            console.error(`❌ ${section} bölümü yükleme hatası:`, error);
        }
    }

    // Belirli bir içeriği yükle
    async loadSpecificContent(key) {
        try {
            const response = await fetch(`/api/site-content.php?key=${key}`);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            
            if (data.success && data.data) {
                const item = data.data;
                this.cache.set(item.content_key, {
                    value: item.content_value,
                    type: item.content_type,
                    section: item.page_section,
                    updated_at: item.updated_at,
                    is_active: item.is_active
                });
                
                // Bu içeriği güncelle
                this.updateSpecificContent(key);
                
                console.log(`✅ ${key} içeriği güncellendi`);
            }
            
        } catch (error) {
            console.error(`❌ ${key} içeriği yükleme hatası:`, error);
        }
    }

    // Cache'den yükle
    loadFromCache() {
        if (this.cache.size > 0) {
            console.log('📋 Cache\'den içerik yükleniyor...');
            this.updateAllContent();
        }
    }

    // Tüm içeriği güncelle
    updateAllContent() {
        this.cache.forEach((content, key) => {
            if (content.is_active !== false) {
                this.updateSpecificContent(key);
            }
        });
    }

    // Belirli bir bölümü güncelle
    updateSectionContent(section) {
        this.cache.forEach((content, key) => {
            if (content.section === section && content.is_active !== false) {
                this.updateSpecificContent(key);
            }
        });
    }

    // Belirli bir içeriği güncelle
    updateSpecificContent(key) {
        const content = this.cache.get(key);
        if (!content || content.is_active === false) return;

        // data-content-key attribute'u olan elementleri bul ve güncelle
        const elements = document.querySelectorAll(`[data-content-key="${key}"]`);
        
        if (elements.length > 0) {
            elements.forEach(element => {
                if (content.type === 'html') {
                    element.innerHTML = content.value;
                } else if (content.type === 'image') {
                    if (element.tagName === 'IMG') {
                        element.src = content.value;
                    } else {
                        element.style.backgroundImage = `url(${content.value})`;
                    }
                } else if (content.type === 'link') {
                    if (element.tagName === 'A') {
                        element.href = content.value;
                    }
                } else {
                    element.textContent = content.value;
                }
                
                // Güncelleme animasyonu
                element.style.transition = 'all 0.3s ease';
                element.style.transform = 'scale(1.02)';
                setTimeout(() => {
                    element.style.transform = 'scale(1)';
                }, 300);
            });
            
            console.log(`🔄 ${key} içeriği ${elements.length} elementte güncellendi`);
        }
    }

    // Cache temizle
    clearCache() {
        this.cache.clear();
        this.lastUpdate = null;
        console.log('🗑️ Cache temizlendi');
    }

    // Manuel yenileme
    async refresh() {
        console.log('🔄 İçerik manuel olarak yenileniyor...');
        this.clearCache();
        await this.loadAllContent();
    }

    // Belirli bir bölümü yenile
    async refreshSection(section) {
        console.log(`🔄 ${section} bölümü yenileniyor...`);
        const data = await this.loadSection(section);
        if (data) {
            this.updateSectionContent(section);
        }
    }

    // Otomatik yenileme başlat
    startAutoRefresh() {
        if (this.autoRefreshInterval) {
            clearInterval(this.autoRefreshInterval);
        }
        
        this.autoRefreshInterval = setInterval(() => {
            if (!document.hidden && this.isInitialized) {
                console.log('⏰ Otomatik yenileme başlatılıyor...');
                this.refresh();
            }
        }, this.refreshInterval);
        
        console.log(`⏰ Otomatik yenileme başlatıldı (${this.refreshInterval / 1000} saniye)`);
    }

    // Otomatik yenilemeyi durdur
    stopAutoRefresh() {
        if (this.autoRefreshInterval) {
            clearInterval(this.autoRefreshInterval);
            this.autoRefreshInterval = null;
            console.log('⏹️ Otomatik yenileme durduruldu');
        }
    }

    // Admin paneli değişikliklerini dinle
    listenForAdminChanges() {
        // Admin paneli açıksa ve değişiklik yapılıyorsa
        if (window.opener && !window.opener.closed) {
            // Admin panelinden gelen mesajları dinle
            window.addEventListener('message', (event) => {
                if (event.data.type === 'content_updated') {
                    console.log('📢 Admin panelinden içerik güncelleme mesajı alındı');
                    this.refresh();
                }
            });
        }
        
        // Sayfa görünürlük değişikliklerini dinle
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                console.log('👁️ Sayfa görünür hale geldi, içerik yenileniyor...');
                this.refresh();
            }
        });
    }

    // Cache durumunu getir
    getCacheStatus() {
        return {
            size: this.cache.size,
            lastUpdate: this.lastUpdate,
            isInitialized: this.isInitialized,
            autoRefreshActive: !!this.autoRefreshInterval
        };
    }

    // Debug bilgileri
    debug() {
        console.log('🔍 Site Content Loader Debug Bilgileri:');
        console.log('- Cache boyutu:', this.cache.size);
        console.log('- Son güncelleme:', this.lastUpdate);
        console.log('- Başlatıldı:', this.isInitialized);
        console.log('- Otomatik yenileme:', !!this.autoRefreshInterval);
        console.log('- Cache içeriği:', Array.from(this.cache.entries()));
    }
}

// Global instance oluştur
window.siteContentLoader = new SiteContentLoader();

// Global fonksiyonlar
window.refreshSiteContent = () => window.siteContentLoader.refresh();
window.refreshSection = (section) => window.siteContentLoader.refreshSection(section);
window.loadSection = (section) => window.siteContentLoader.loadSection(section);
window.loadContent = (key) => window.siteContentLoader.loadSpecificContent(key);
window.clearSiteCache = () => window.siteContentLoader.clearCache();
window.getCacheStatus = () => window.siteContentLoader.getCacheStatus();
window.debugSiteContent = () => window.siteContentLoader.debug();

// Sayfa yüklendiğinde otomatik başlat
document.addEventListener('DOMContentLoaded', () => {
    // Site content loader zaten başlatıldı, sadece durumu kontrol et
    if (window.siteContentLoader) {
        console.log('📋 Site Content Loader durumu:', window.siteContentLoader.getCacheStatus());
    }
});

// Sayfa kapatılırken temizlik yap
window.addEventListener('beforeunload', () => {
    if (window.siteContentLoader) {
        window.siteContentLoader.stopAutoRefresh();
    }
});

console.log('🚀 Site Content Loader yüklendi!');
