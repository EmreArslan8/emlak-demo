# Vista Gayrimenkul — Kurumsal Emlak Sitesi (MVP)

Saf PHP 8 + SQLite ile yazılmış, framework gerektirmeyen, açık kaynak ve geliştirilebilir emlak ofisi web sitesi.
Tüm veriler örnek (mock) veridir; görseller Unsplash stok fotoğraflarıdır.

## Hızlı başlangıç

```bash
php -S localhost:8000 -t public public/router.php
```

- Site: http://localhost:8000
- Yönetim paneli: http://localhost:8000/yonetim — **admin / admin123**

İlk istekte `storage/emlak.sqlite` otomatik oluşturulur ve örnek verilerle doldurulur.
Sıfırlamak için dosyayı silmeniz yeterli.

Apache'de `public/` klasörünü web kökü (DocumentRoot) yapın; `.htaccess` SEO uyumlu adresleri yönlendirir.
Gereksinimler: PHP 8.1+, `pdo_sqlite`, `mbstring`, `gd` (yükleme doğrulaması için).

## Özellikler

**İlan yönetimi** — Satılık/kiralık portföy; konum, fiyat, oda sayısı, m², ilan ve emlak tipi, anahtar kelime filtreleri;
sıralama, sayfalama, liste/harita görünümü (fiyat etiketli işaretçiler); galeri + tam ekran görüntüleyici, ilan bilgileri,
özellikler, yaklaşık konum haritası, benzer ilanlar, ilana özel bilgi/randevu formu ve WhatsApp mesajı.

**Danışmanlar** — Profil sayfaları ve her danışmanın kendi ilan listesi.

**Kurumsal sayfalar** — Hakkımızda, Hizmetlerimiz, Blog/Haberler (kategori filtreli), İletişim (form, WhatsApp hızlı erişim,
konum haritası). Sabit WhatsApp butonu, mobilde ilan sayfasında Ara/WhatsApp çubuğu.

**SEO & performans** — Temiz URL'ler (`/ilan/modada-deniz-manzarali-...`, `/satilik`, `/danisman/elif-aydin`), sayfa başına
title/description/canonical/Open Graph, schema.org JSON-LD (`RealEstateAgent`, `RealEstateListing`), `sitemap.xml`, `robots.txt`,
lazy-load görseller, boyutlandırılmış görsel adresleri, harici JS framework yok (yalnızca projeye dahil Leaflet).

**Türkçe yönetim paneli** — Genel bakış, ilan / danışman / blog ekle-düzenle-sil, çoklu fotoğraf yükleme ve
kapak seçimi, haritadan konum seçme, gelen talepler (okundu işareti, tek tık arama/WhatsApp), site ayarları
(iletişim bilgileri, ana sayfa metinleri, SEO açıklaması, şifre değiştirme).

**Güvenlik** — PDO hazır sorgular, çıktı kaçışlama, CSRF koruması, `password_hash`, yükleme dosya tipi doğrulaması,
`uploads/` içinde PHP çalıştırma engeli, form bal küpü (spam).

## Klasör yapısı

```
app/
  bootstrap.php, db.php, helpers.php   çekirdek
  schema.sql, seed.php                 veritabanı şeması ve örnek veri
  controllers/site.php, admin.php      sayfa mantığı
  views/site/*, views/admin/*          şablonlar
public/
  index.php (yönlendirici), router.php, .htaccess
  assets/css, assets/js, assets/img, assets/vendor/leaflet
  uploads/                             yüklenen görseller
storage/                               SQLite veritabanı
```

## Sonraki adımlar (MVP sonrası)

MySQL'e geçiş (PDO DSN değişikliği), e-posta bildirimleri (SMTP), çoklu kullanıcı/rol, çoklu dil (TR/EN),
görsel optimizasyonu (WebP dönüştürme), sahibinden/hepsiemlak XML ilan aktarımı.

## Vercel'e dağıtım

Depo `vercel.json` ile hazır gelir (`vercel-php` runtime'ı, giriş noktası `api/index.php`).
Vercel → **Add New → Project → GitHub deposunu içe aktar** → Framework Preset: **Other** → Deploy.
Build komutu veya ortam değişkeni gerekmez.

Vercel'in dosya sistemi salt-okunur olduğundan veritabanı ve oturumlar `/tmp`'de tutulur:
panelden yapılan değişiklikler geçicidir (sunucu yeniden başladığında demo verisine döner) ve dosya yükleme kapalıdır
(görsel bağlantısı kullanılabilir). Kalıcı kullanım için PHP destekli klasik bir hosting (cPanel/Apache) önerilir.
