====================================================
NextCode - FTP'ye Yüklenecek 11 Kritik Dosya
====================================================

FTP: gtorg.ftp.tools
User: gtorg_nextcode

====================================================
MANUEL YÜKLEME LİSTESİ (Tek Tek Yükle)
====================================================

1. contact.php
   Local: C:\Users\xeyal\Desktop\nextcode\contact.php
   Remote: /contact.php
   Eylem: GÜNCELLE (üzerine yaz)

2. index.php
   Local: C:\Users\xeyal\Desktop\nextcode\index.php
   Remote: /index.php
   Eylem: GÜNCELLE (üzerine yaz)

3. api/contact.php
   Local: C:\Users\xeyal\Desktop\nextcode\api\contact.php
   Remote: /api/contact.php
   Eylem: GÜNCELLE (üzerine yaz)

4. admin/login.php
   Local: C:\Users\xeyal\Desktop\nextcode\admin\login.php
   Remote: /admin/login.php
   Eylem: GÜNCELLE (üzerine yaz)

5. admin/database-migration.php
   Local: C:\Users\xeyal\Desktop\nextcode\admin\database-migration.php
   Remote: /admin/database-migration.php
   Eylem: YENİ DOSYA (yükle)

6. includes/header.php
   Local: C:\Users\xeyal\Desktop\nextcode\includes\header.php
   Remote: /includes/header.php
   Eylem: GÜNCELLE (üzerine yaz)

7. includes/content_helper.php
   Local: C:\Users\xeyal\Desktop\nextcode\includes\content_helper.php
   Remote: /includes/content_helper.php
   Eylem: GÜNCELLE (üzerine yaz)

8. config/database.php
   Local: C:\Users\xeyal\Desktop\nextcode\config\database.php
   Remote: /config/database.php
   Eylem: GÜNCELLE (üzerine yaz)

9. config/auto-migration.php
   Local: C:\Users\xeyal\Desktop\nextcode\config\auto-migration.php
   Remote: /config/auto-migration.php
   Eylem: YENİ DOSYA (yükle)

10. js/console-guard.js
    Local: C:\Users\xeyal\Desktop\nextcode\js\console-guard.js
    Remote: /js/console-guard.js
    Eylem: YENİ DOSYA (yükle)

11. cron/auto-migration.php
    Local: C:\Users\xeyal\Desktop\nextcode\cron\auto-migration.php
    Remote: /cron/auto-migration.php
    Eylem: YENİ DOSYA (önce /cron/ klasörünü oluştur)

====================================================
YÜKLEME SIRASI
====================================================

Öncelik 1 (Kritik):
✓ config/database.php
✓ config/auto-migration.php

Öncelik 2 (Güvenlik):
✓ includes/header.php
✓ includes/content_helper.php
✓ js/console-guard.js

Öncelik 3 (Fonksiyonellik):
✓ contact.php
✓ api/contact.php
✓ admin/login.php
✓ admin/database-migration.php

Öncelik 4 (Ana Sayfa):
✓ index.php

Öncelik 5 (Opsiyonel):
✓ cron/auto-migration.php

====================================================
YÜKLEME SONRASI KONTROL
====================================================

1. https://nextcode.az
   ✅ Ana sayfa açılıyor mu?

2. https://nextcode.az/contact.php
   ✅ Contact form çalışıyor mu?

3. https://nextcode.az/admin/database-migration.php
   ✅ Admin panel açılıyor mu?

4. F12 → Console
   ✅ "Console logs disabled" mesajı var mı?

5. FTP'de cache/ klasörü
   ✅ migration_cache.json oluştu mu?

====================================================
SORUN OLURSA
====================================================

1. Site açılmıyor:
   → Yedek dosyaları geri yükle
   → contact.php.backup → contact.php

2. Migration çalışmıyor:
   → cache/ klasörü yazılabilir mi? (755)
   → config/auto-migration.php yüklendi mi?

3. Console guard çalışmıyor:
   → js/console-guard.js yüklendi mi?
   → includes/header.php yüklendi mi?

====================================================
TAMAMLANDI
====================================================

Tarih: _______________
Yüklenen: 11 dosya
Süre: _______________
Sorun: [ ] Yok  [ ] Var: _______________

====================================================

