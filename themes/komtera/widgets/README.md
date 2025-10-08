# Komtera ERP - WordPress Dashboard Widgets

Bu klasör, WordPress Dashboard'da gösterilen widget'ları içerir.

## Widget Listesi

### Ana Widgets
- **kur.php** - Günlük döviz kurları (USD & EUR)
- **release_notes.php** - Sürüm notları

### Eski Gadgets'lardan Dönüştürülen Widgets

Aşağıdaki widget'lar `_eski_kodlar/gadgets/` klasöründen WordPress sistemine dönüştürülmüştür:

#### 1. **onay_is_atama.php** (G-102)
- **Orijinal:** `kt_G-102.php`
- **İşlev:** Onay/İş Atama/Bilgilendirmeler listesi
- **Özellikler:**
  - Son 15 kaydı gösterir
  - Modül, NO, Zaman, Beklenen, Onay durumu
  - Satır silme özelliği
  - Onay durumu görsel gösterimi (✓)

#### 2. **yakinda_kapanacak_firsatlar.php** (G-104)
- **Orijinal:** `kt_G-104.php`
- **İşlev:** Yakında kapanacak fırsatları gösterir
- **Özellikler:**
  - Bitiş tarihine göre renklendirme
    - Kırmızı: Bugün kapanıyor
    - Açık Kırmızı: 1 gün kaldı
    - Turuncu: 2 gün kaldı
  - Otomatik "Yetersiz Takip" kaybedilme nedeni ataması
  - Fırsat detay linkli

#### 3. **siparis_ozel_sku.php** (G-106)
- **Orijinal:** `kt_G-106.php`
- **İşlev:** Sipariş için gelen özel SKU'ları listeler
- **Özellikler:**
  - Faturası kesilmemiş özel SKU'lar
  - Sipariş detay sayfasına link
  - Modern badge tasarımı

#### 4. **satis_hedefleri.php** (G-107)
- **Orijinal:** `kt_G-107.php`
- **İşlev:** Çeyreklik satış hedefleri ve gerçekleşmeleri
- **Özellikler:**
  - Q1, Q2, Q3, Q4 satış hedefleri
  - Gerçekleşen satışlar
  - Kalan hedefler (sadece aktif çeyrek için)
  - Açık siparişler
  - Aktif çeyrek vurgulama

## Teknik Detaylar

### Widget Yükleme
Widget'lar `inc/widgets.php` dosyasından otomatik yüklenir:

```php
// Eski gadgets'lardan dönüştürülen widgets
include( get_stylesheet_directory() . '/widgets/onay_is_atama.php' );
include( get_stylesheet_directory() . '/widgets/yakinda_kapanacak_firsatlar.php' );
include( get_stylesheet_directory() . '/widgets/siparis_ozel_sku.php' );
include( get_stylesheet_directory() . '/widgets/satis_hedefleri.php' );
```

### Widget Yapısı
Her widget aşağıdaki yapıya sahiptir:

```php
function widget_name_content() {
    // Widget içeriği
}

function add_widget_name() {
    wp_add_dashboard_widget(
        'widget_id',
        'Widget Başlığı',
        'widget_name_content'
    );
}
add_action('wp_dashboard_setup', 'add_widget_name');
```

### Veritabanı Bağlantısı
Widget'lar şu dosyaları kullanır:
- `erp/_conn.php` - Veritabanı bağlantısı
- `inc/table_helper.php` - Test/Prod ortam tablo ismi yönetimi

### Test Ortamı Desteği
`getTableName()` fonksiyonu ile test ortamında (`erptest.komtera.com`) otomatik olarak `atest_` prefix'li tablolar kullanılır:
- ✅ `aa_erp_kt_*` → `atest_aa_erp_kt_*`
- ✅ `aaa_erp_kt_*` → `aaa_erp_kt_*` (LOGO view'ları değişmez)

## Yeni Widget Ekleme

1. `widgets/` klasörüne yeni widget dosyası oluştur
2. Widget fonksiyonlarını yaz (yukarıdaki yapıya göre)
3. `inc/widgets.php` dosyasına include ekle
4. Dashboard'da otomatik görünecek

## Template

Yeni widget oluşturmak için `template.php` dosyasını kullanabilirsiniz.
