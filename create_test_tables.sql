-- =============================================
-- Test Ortamı Tablo Kopyalama Scripti
-- =============================================
-- Bu script tüm tabloların test kopyalarını (atest_ prefix ile) oluşturur
-- Canlı tablolara hiç dokunmaz, sadece yeni tablolar oluşturur
--
-- Kullanım:
-- 1. Bu scripti MSSQL Management Studio'da açın
-- 2. LKS veritabanını seçin
-- 3. Tüm scripti çalıştırın
-- =============================================

USE LKS;
GO

PRINT 'Test tabloları oluşturma başlıyor...';
PRINT '';

-- =============================================
-- aa_erp_kt_* Tabloları (38 adet)
-- =============================================
PRINT 'aa_erp_kt_* tabloları kopyalanıyor...';

-- 1. aa_erp_kt_firsatlar
IF OBJECT_ID('atest_aa_erp_kt_firsatlar', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_firsatlar;
SELECT * INTO atest_aa_erp_kt_firsatlar FROM aa_erp_kt_firsatlar;
PRINT '✓ atest_aa_erp_kt_firsatlar oluşturuldu';

-- 2. aa_erp_kt_teklifler
IF OBJECT_ID('atest_aa_erp_kt_teklifler', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_teklifler;
SELECT * INTO atest_aa_erp_kt_teklifler FROM aa_erp_kt_teklifler;
PRINT '✓ atest_aa_erp_kt_teklifler oluşturuldu';

-- 3. aa_erp_kt_teklifler_urunler
IF OBJECT_ID('atest_aa_erp_kt_teklifler_urunler', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_teklifler_urunler;
SELECT * INTO atest_aa_erp_kt_teklifler_urunler FROM aa_erp_kt_teklifler_urunler;
PRINT '✓ atest_aa_erp_kt_teklifler_urunler oluşturuldu';

-- 4. aa_erp_kt_fiyat_listesi
IF OBJECT_ID('atest_aa_erp_kt_fiyat_listesi', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_fiyat_listesi;
SELECT * INTO atest_aa_erp_kt_fiyat_listesi FROM aa_erp_kt_fiyat_listesi;
PRINT '✓ atest_aa_erp_kt_fiyat_listesi oluşturuldu';

-- 5. aa_erp_kt_log
IF OBJECT_ID('atest_aa_erp_kt_log', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_log;
SELECT * INTO atest_aa_erp_kt_log FROM aa_erp_kt_log;
PRINT '✓ atest_aa_erp_kt_log oluşturuldu';

-- 6. aa_erp_kt_siparisler
IF OBJECT_ID('atest_aa_erp_kt_siparisler', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_siparisler;
SELECT * INTO atest_aa_erp_kt_siparisler FROM aa_erp_kt_siparisler;
PRINT '✓ atest_aa_erp_kt_siparisler oluşturuldu';

-- 7. aa_erp_kt_siparisler_urunler
IF OBJECT_ID('atest_aa_erp_kt_siparisler_urunler', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_siparisler_urunler;
SELECT * INTO atest_aa_erp_kt_siparisler_urunler FROM aa_erp_kt_siparisler_urunler;
PRINT '✓ atest_aa_erp_kt_siparisler_urunler oluşturuldu';

-- 8. aa_erp_kt_bayiler_yetkililer
IF OBJECT_ID('atest_aa_erp_kt_bayiler_yetkililer', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_bayiler_yetkililer;
SELECT * INTO atest_aa_erp_kt_bayiler_yetkililer FROM aa_erp_kt_bayiler_yetkililer;
PRINT '✓ atest_aa_erp_kt_bayiler_yetkililer oluşturuldu';

-- 9. aa_erp_kt_bayiler_kara_liste
IF OBJECT_ID('atest_aa_erp_kt_bayiler_kara_liste', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_bayiler_kara_liste;
SELECT * INTO atest_aa_erp_kt_bayiler_kara_liste FROM aa_erp_kt_bayiler_kara_liste;
PRINT '✓ atest_aa_erp_kt_bayiler_kara_liste oluşturuldu';

-- 10. aa_erp_kt_bayiler_markaseviyeleri
IF OBJECT_ID('atest_aa_erp_kt_bayiler_markaseviyeleri', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_bayiler_markaseviyeleri;
SELECT * INTO atest_aa_erp_kt_bayiler_markaseviyeleri FROM aa_erp_kt_bayiler_markaseviyeleri;
PRINT '✓ atest_aa_erp_kt_bayiler_markaseviyeleri oluşturuldu';

-- 11. aa_erp_kt_bayiler_eskiseviye
IF OBJECT_ID('atest_aa_erp_kt_bayiler_eskiseviye', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_bayiler_eskiseviye;
SELECT * INTO atest_aa_erp_kt_bayiler_eskiseviye FROM aa_erp_kt_bayiler_eskiseviye;
PRINT '✓ atest_aa_erp_kt_bayiler_eskiseviye oluşturuldu';

-- 12. aa_erp_kt_musteriler
IF OBJECT_ID('atest_aa_erp_kt_musteriler', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_musteriler;
SELECT * INTO atest_aa_erp_kt_musteriler FROM aa_erp_kt_musteriler;
PRINT '✓ atest_aa_erp_kt_musteriler oluşturuldu';

-- 13. aa_erp_kt_musteriler_yetkililer
IF OBJECT_ID('atest_aa_erp_kt_musteriler_yetkililer', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_musteriler_yetkililer;
SELECT * INTO atest_aa_erp_kt_musteriler_yetkililer FROM aa_erp_kt_musteriler_yetkililer;
PRINT '✓ atest_aa_erp_kt_musteriler_yetkililer oluşturuldu';

-- 14. aa_erp_kt_markalar
IF OBJECT_ID('atest_aa_erp_kt_markalar', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_markalar;
SELECT * INTO atest_aa_erp_kt_markalar FROM aa_erp_kt_markalar;
PRINT '✓ atest_aa_erp_kt_markalar oluşturuldu';

-- 15. aa_erp_kt_markalar_managers
IF OBJECT_ID('atest_aa_erp_kt_markalar_managers', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_markalar_managers;
SELECT * INTO atest_aa_erp_kt_markalar_managers FROM aa_erp_kt_markalar_managers;
PRINT '✓ atest_aa_erp_kt_markalar_managers oluşturuldu';

-- 16. aa_erp_kt_fatura_i
IF OBJECT_ID('atest_aa_erp_kt_fatura_i', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_fatura_i;
SELECT * INTO atest_aa_erp_kt_fatura_i FROM aa_erp_kt_fatura_i;
PRINT '✓ atest_aa_erp_kt_fatura_i oluşturuldu';

-- 17. aa_erp_kt_fatura_urunler_i
IF OBJECT_ID('atest_aa_erp_kt_fatura_urunler_i', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_fatura_urunler_i;
SELECT * INTO atest_aa_erp_kt_fatura_urunler_i FROM aa_erp_kt_fatura_urunler_i;
PRINT '✓ atest_aa_erp_kt_fatura_urunler_i oluşturuldu';

-- 18. aa_erp_kt_teklif_dosyalar
IF OBJECT_ID('atest_aa_erp_kt_teklif_dosyalar', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_teklif_dosyalar;
SELECT * INTO atest_aa_erp_kt_teklif_dosyalar FROM aa_erp_kt_teklif_dosyalar;
PRINT '✓ atest_aa_erp_kt_teklif_dosyalar oluşturuldu';

-- 19. aa_erp_kt_teklif_lisans_dosyalar
IF OBJECT_ID('atest_aa_erp_kt_teklif_lisans_dosyalar', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_teklif_lisans_dosyalar;
SELECT * INTO atest_aa_erp_kt_teklif_lisans_dosyalar FROM aa_erp_kt_teklif_lisans_dosyalar;
PRINT '✓ atest_aa_erp_kt_teklif_lisans_dosyalar oluşturuldu';

-- 20. aa_erp_kt_aktiviteler
IF OBJECT_ID('atest_aa_erp_kt_aktiviteler', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_aktiviteler;
SELECT * INTO atest_aa_erp_kt_aktiviteler FROM aa_erp_kt_aktiviteler;
PRINT '✓ atest_aa_erp_kt_aktiviteler oluşturuldu';

-- 21. aa_erp_kt_etkinlikler
IF OBJECT_ID('atest_aa_erp_kt_etkinlikler', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_etkinlikler;
SELECT * INTO atest_aa_erp_kt_etkinlikler FROM aa_erp_kt_etkinlikler;
PRINT '✓ atest_aa_erp_kt_etkinlikler oluşturuldu';

-- 22. aa_erp_kt_demolar
IF OBJECT_ID('atest_aa_erp_kt_demolar', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_demolar;
SELECT * INTO atest_aa_erp_kt_demolar FROM aa_erp_kt_demolar;
PRINT '✓ atest_aa_erp_kt_demolar oluşturuldu';

-- 23. aa_erp_kt_demolar_skular
IF OBJECT_ID('atest_aa_erp_kt_demolar_skular', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_demolar_skular;
SELECT * INTO atest_aa_erp_kt_demolar_skular FROM aa_erp_kt_demolar_skular;
PRINT '✓ atest_aa_erp_kt_demolar_skular oluşturuldu';

-- 24. aa_erp_kt_poc
IF OBJECT_ID('atest_aa_erp_kt_poc', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_poc;
SELECT * INTO atest_aa_erp_kt_poc FROM aa_erp_kt_poc;
PRINT '✓ atest_aa_erp_kt_poc oluşturuldu';

-- 25. aa_erp_kt_poc_emek
IF OBJECT_ID('atest_aa_erp_kt_poc_emek', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_poc_emek;
SELECT * INTO atest_aa_erp_kt_poc_emek FROM aa_erp_kt_poc_emek;
PRINT '✓ atest_aa_erp_kt_poc_emek oluşturuldu';

-- 26. aa_erp_kt_onay_bekleyenler
IF OBJECT_ID('atest_aa_erp_kt_onay_bekleyenler', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_onay_bekleyenler;
SELECT * INTO atest_aa_erp_kt_onay_bekleyenler FROM aa_erp_kt_onay_bekleyenler;
PRINT '✓ atest_aa_erp_kt_onay_bekleyenler oluşturuldu';

-- 27. aa_erp_kt_teklifler_onay
IF OBJECT_ID('atest_aa_erp_kt_teklifler_onay', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_teklifler_onay;
SELECT * INTO atest_aa_erp_kt_teklifler_onay FROM aa_erp_kt_teklifler_onay;
PRINT '✓ atest_aa_erp_kt_teklifler_onay oluşturuldu';

-- 28. aa_erp_kt_ayarlar_onaylar
IF OBJECT_ID('atest_aa_erp_kt_ayarlar_onaylar', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_ayarlar_onaylar;
SELECT * INTO atest_aa_erp_kt_ayarlar_onaylar FROM aa_erp_kt_ayarlar_onaylar;
PRINT '✓ atest_aa_erp_kt_ayarlar_onaylar oluşturuldu';

-- 29. aa_erp_kt_ayarlar_onaylar_kar
IF OBJECT_ID('atest_aa_erp_kt_ayarlar_onaylar_kar', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_ayarlar_onaylar_kar;
SELECT * INTO atest_aa_erp_kt_ayarlar_onaylar_kar FROM aa_erp_kt_ayarlar_onaylar_kar;
PRINT '✓ atest_aa_erp_kt_ayarlar_onaylar_kar oluşturuldu';

-- 30. aa_erp_kt_mcafee_kotasyon
IF OBJECT_ID('atest_aa_erp_kt_mcafee_kotasyon', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_mcafee_kotasyon;
SELECT * INTO atest_aa_erp_kt_mcafee_kotasyon FROM aa_erp_kt_mcafee_kotasyon;
PRINT '✓ atest_aa_erp_kt_mcafee_kotasyon oluşturuldu';

-- 31. aa_erp_kt_mcafee_sku_sure
IF OBJECT_ID('atest_aa_erp_kt_mcafee_sku_sure', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_mcafee_sku_sure;
SELECT * INTO atest_aa_erp_kt_mcafee_sku_sure FROM aa_erp_kt_mcafee_sku_sure;
PRINT '✓ atest_aa_erp_kt_mcafee_sku_sure oluşturuldu';

-- 32. aa_erp_kt_kampanyalar
IF OBJECT_ID('atest_aa_erp_kt_kampanyalar', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_kampanyalar;
SELECT * INTO atest_aa_erp_kt_kampanyalar FROM aa_erp_kt_kampanyalar;
PRINT '✓ atest_aa_erp_kt_kampanyalar oluşturuldu';

-- 33. aa_erp_kt_is_atama
IF OBJECT_ID('atest_aa_erp_kt_is_atama', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_is_atama;
SELECT * INTO atest_aa_erp_kt_is_atama FROM aa_erp_kt_is_atama;
PRINT '✓ atest_aa_erp_kt_is_atama oluşturuldu';

-- 34. aa_erp_kt_edi
IF OBJECT_ID('atest_aa_erp_kt_edi', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_edi;
SELECT * INTO atest_aa_erp_kt_edi FROM aa_erp_kt_edi;
PRINT '✓ atest_aa_erp_kt_edi oluşturuldu';

-- 35. aa_erp_kt_Acronis_CH_Eslesme
IF OBJECT_ID('atest_aa_erp_kt_Acronis_CH_Eslesme', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_Acronis_CH_Eslesme;
SELECT * INTO atest_aa_erp_kt_Acronis_CH_Eslesme FROM aa_erp_kt_Acronis_CH_Eslesme;
PRINT '✓ atest_aa_erp_kt_Acronis_CH_Eslesme oluşturuldu';

-- 36. aa_erp_kt_acronis_fatura_kes
IF OBJECT_ID('atest_aa_erp_kt_acronis_fatura_kes', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_acronis_fatura_kes;
SELECT * INTO atest_aa_erp_kt_acronis_fatura_kes FROM aa_erp_kt_acronis_fatura_kes;
PRINT '✓ atest_aa_erp_kt_acronis_fatura_kes oluşturuldu';

-- 37. aa_erp_kt_fatura_kes_sophos
IF OBJECT_ID('atest_aa_erp_kt_fatura_kes_sophos', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_fatura_kes_sophos;
SELECT * INTO atest_aa_erp_kt_fatura_kes_sophos FROM aa_erp_kt_fatura_kes_sophos;
PRINT '✓ atest_aa_erp_kt_fatura_kes_sophos oluşturuldu';

-- 38. aa_erp_kt_sophos_edi_cari
IF OBJECT_ID('atest_aa_erp_kt_sophos_edi_cari', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_sophos_edi_cari;
SELECT * INTO atest_aa_erp_kt_sophos_edi_cari FROM aa_erp_kt_sophos_edi_cari;
PRINT '✓ atest_aa_erp_kt_sophos_edi_cari oluşturuldu';

-- 39. aa_erp_kt_mediamarkt_faturalama
IF OBJECT_ID('atest_aa_erp_kt_mediamarkt_faturalama', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_mediamarkt_faturalama;
SELECT * INTO atest_aa_erp_kt_mediamarkt_faturalama FROM aa_erp_kt_mediamarkt_faturalama;
PRINT '✓ atest_aa_erp_kt_mediamarkt_faturalama oluşturuldu';

-- 40. aa_erp_kt_vatan_faturalama
IF OBJECT_ID('atest_aa_erp_kt_vatan_faturalama', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_vatan_faturalama;
SELECT * INTO atest_aa_erp_kt_vatan_faturalama FROM aa_erp_kt_vatan_faturalama;
PRINT '✓ atest_aa_erp_kt_vatan_faturalama oluşturuldu';

-- 41. aa_erp_kt_tl_fatura_marka
IF OBJECT_ID('atest_aa_erp_kt_tl_fatura_marka', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_tl_fatura_marka;
SELECT * INTO atest_aa_erp_kt_tl_fatura_marka FROM aa_erp_kt_tl_fatura_marka;
PRINT '✓ atest_aa_erp_kt_tl_fatura_marka oluşturuldu';

-- 42. aa_erp_kt_mt_hedefler
IF OBJECT_ID('atest_aa_erp_kt_mt_hedefler', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_mt_hedefler;
SELECT * INTO atest_aa_erp_kt_mt_hedefler FROM aa_erp_kt_mt_hedefler;
PRINT '✓ atest_aa_erp_kt_mt_hedefler oluşturuldu';

-- 43. aa_erp_kt_bankalar
IF OBJECT_ID('atest_aa_erp_kt_bankalar', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_bankalar;
SELECT * INTO atest_aa_erp_kt_bankalar FROM aa_erp_kt_bankalar;
PRINT '✓ atest_aa_erp_kt_bankalar oluşturuldu';

-- 44. aa_erp_kt_users
IF OBJECT_ID('atest_aa_erp_kt_users', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_users;
SELECT * INTO atest_aa_erp_kt_users FROM aa_erp_kt_users;
PRINT '✓ atest_aa_erp_kt_users oluşturuldu';

-- 45. aa_erp_kt_pref
IF OBJECT_ID('atest_aa_erp_kt_pref', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_pref;
SELECT * INTO atest_aa_erp_kt_pref FROM aa_erp_kt_pref;
PRINT '✓ atest_aa_erp_kt_pref oluşturuldu';

-- 46. aa_erp_kt_firsatlar_backup
IF OBJECT_ID('atest_aa_erp_kt_firsatlar_backup', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_firsatlar_backup;
SELECT * INTO atest_aa_erp_kt_firsatlar_backup FROM aa_erp_kt_firsatlar_backup;
PRINT '✓ atest_aa_erp_kt_firsatlar_backup oluşturuldu';

-- 47. aa_erp_kt_teklifler_backup
IF OBJECT_ID('atest_aa_erp_kt_teklifler_backup', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kt_teklifler_backup;
SELECT * INTO atest_aa_erp_kt_teklifler_backup FROM aa_erp_kt_teklifler_backup;
PRINT '✓ atest_aa_erp_kt_teklifler_backup oluşturuldu';

PRINT '';
PRINT 'aa_erp_kt_* tabloları tamamlandı (47 adet)';
PRINT '';

-- =============================================
-- aa_erp_* Tabloları (7 adet)
-- =============================================
PRINT 'aa_erp_* tabloları kopyalanıyor...';

-- 48. aa_erp_borsa
IF OBJECT_ID('atest_aa_erp_borsa', 'U') IS NOT NULL DROP TABLE atest_aa_erp_borsa;
SELECT * INTO atest_aa_erp_borsa FROM aa_erp_borsa;
PRINT '✓ atest_aa_erp_borsa oluşturuldu';

-- 49. aa_erp_il_ilce
IF OBJECT_ID('atest_aa_erp_il_ilce', 'U') IS NOT NULL DROP TABLE atest_aa_erp_il_ilce;
SELECT * INTO atest_aa_erp_il_ilce FROM aa_erp_il_ilce;
PRINT '✓ atest_aa_erp_il_ilce oluşturuldu';

-- 50. aa_erp_kur
IF OBJECT_ID('atest_aa_erp_kur', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kur;
SELECT * INTO atest_aa_erp_kur FROM aa_erp_kur;
PRINT '✓ atest_aa_erp_kur oluşturuldu';

-- 51. aa_erp_kur_yk
IF OBJECT_ID('atest_aa_erp_kur_yk', 'U') IS NOT NULL DROP TABLE atest_aa_erp_kur_yk;
SELECT * INTO atest_aa_erp_kur_yk FROM aa_erp_kur_yk;
PRINT '✓ atest_aa_erp_kur_yk oluşturuldu';

-- 52. aa_erp_log
IF OBJECT_ID('atest_aa_erp_log', 'U') IS NOT NULL DROP TABLE atest_aa_erp_log;
SELECT * INTO atest_aa_erp_log FROM aa_erp_log;
PRINT '✓ atest_aa_erp_log oluşturuldu';

-- 53. aa_erp_release_note
IF OBJECT_ID('atest_aa_erp_release_note', 'U') IS NOT NULL DROP TABLE atest_aa_erp_release_note;
SELECT * INTO atest_aa_erp_release_note FROM aa_erp_release_note;
PRINT '✓ atest_aa_erp_release_note oluşturuldu';

-- 54. aa_erp_tickets
IF OBJECT_ID('atest_aa_erp_tickets', 'U') IS NOT NULL DROP TABLE atest_aa_erp_tickets;
SELECT * INTO atest_aa_erp_tickets FROM aa_erp_tickets;
PRINT '✓ atest_aa_erp_tickets oluşturuldu';

PRINT '';
PRINT 'aa_erp_* tabloları tamamlandı (7 adet)';
PRINT '';

-- =============================================
-- aa_kt_* Tabloları (2 adet)
-- =============================================
PRINT 'aa_kt_* tabloları kopyalanıyor...';

-- 55. aa_kt_logo_aktarim
IF OBJECT_ID('atest_aa_kt_logo_aktarim', 'U') IS NOT NULL DROP TABLE atest_aa_kt_logo_aktarim;
SELECT * INTO atest_aa_kt_logo_aktarim FROM aa_kt_logo_aktarim;
PRINT '✓ atest_aa_kt_logo_aktarim oluşturuldu';

-- 56. aa_kt_pref_list
IF OBJECT_ID('atest_aa_kt_pref_list', 'U') IS NOT NULL DROP TABLE atest_aa_kt_pref_list;
SELECT * INTO atest_aa_kt_pref_list FROM aa_kt_pref_list;
PRINT '✓ atest_aa_kt_pref_list oluşturuldu';

PRINT '';
PRINT 'aa_kt_* tabloları tamamlandı (2 adet)';
PRINT '';

-- =============================================
-- aaa_erp_kt_* Tabloları (4 adet)
-- =============================================
PRINT 'aaa_erp_kt_* tabloları kopyalanıyor...';

-- 57. aaa_erp_kt_bayiler
IF OBJECT_ID('atest_aaa_erp_kt_bayiler', 'U') IS NOT NULL DROP TABLE atest_aaa_erp_kt_bayiler;
SELECT * INTO atest_aaa_erp_kt_bayiler FROM aaa_erp_kt_bayiler;
PRINT '✓ atest_aaa_erp_kt_bayiler oluşturuldu';

-- 58. aaa_erp_kt_sechard_list
IF OBJECT_ID('atest_aaa_erp_kt_sechard_list', 'U') IS NOT NULL DROP TABLE atest_aaa_erp_kt_sechard_list;
SELECT * INTO atest_aaa_erp_kt_sechard_list FROM aaa_erp_kt_sechard_list;
PRINT '✓ atest_aaa_erp_kt_sechard_list oluşturuldu';

-- 59. aaa_erp_kt_stoklar_satis
IF OBJECT_ID('atest_aaa_erp_kt_stoklar_satis', 'U') IS NOT NULL DROP TABLE atest_aaa_erp_kt_stoklar_satis;
SELECT * INTO atest_aaa_erp_kt_stoklar_satis FROM aaa_erp_kt_stoklar_satis;
PRINT '✓ atest_aaa_erp_kt_stoklar_satis oluşturuldu';

-- 60. aaa_erp_kt_serial_no
IF OBJECT_ID('atest_aaa_erp_kt_serial_no', 'U') IS NOT NULL DROP TABLE atest_aaa_erp_kt_serial_no;
SELECT * INTO atest_aaa_erp_kt_serial_no FROM aaa_erp_kt_serial_no;
PRINT '✓ atest_aaa_erp_kt_serial_no oluşturuldu';

PRINT '';
PRINT 'aaa_erp_kt_* tabloları tamamlandı (4 adet)';
PRINT '';

-- =============================================
-- aaaa_erp_kt_* Tabloları (1 adet)
-- =============================================
PRINT 'aaaa_erp_kt_* tabloları kopyalanıyor...';

-- 61. aaaa_erp_kt_komisyon_raporu_ham
IF OBJECT_ID('atest_aaaa_erp_kt_komisyon_raporu_ham', 'U') IS NOT NULL DROP TABLE atest_aaaa_erp_kt_komisyon_raporu_ham;
SELECT * INTO atest_aaaa_erp_kt_komisyon_raporu_ham FROM aaaa_erp_kt_komisyon_raporu_ham;
PRINT '✓ atest_aaaa_erp_kt_komisyon_raporu_ham oluşturuldu';

PRINT '';
PRINT 'aaaa_erp_kt_* tabloları tamamlandı (1 adet)';
PRINT '';

-- =============================================
-- LOGO Entegrasyon Tablosu (1 adet)
-- =============================================
PRINT 'LOGO entegrasyon tablosu kopyalanıyor...';

-- 62. ARYD_FIS_AKTARIM
IF OBJECT_ID('atest_ARYD_FIS_AKTARIM', 'U') IS NOT NULL DROP TABLE atest_ARYD_FIS_AKTARIM;
SELECT * INTO atest_ARYD_FIS_AKTARIM FROM ARYD_FIS_AKTARIM;
PRINT '✓ atest_ARYD_FIS_AKTARIM oluşturuldu';

PRINT '';
PRINT 'LOGO entegrasyon tablosu tamamlandı (1 adet)';
PRINT '';

-- =============================================
-- Özet
-- =============================================
PRINT '==========================================';
PRINT 'TÜM TABLOLAR BAŞARIYLA KOPYALANDI!';
PRINT '';
PRINT 'Toplam: 62 tablo oluşturuldu';
PRINT '  - aa_erp_kt_* : 47 tablo';
PRINT '  - aa_erp_*    : 7 tablo';
PRINT '  - aa_kt_*     : 2 tablo';
PRINT '  - aaa_erp_kt_*: 4 tablo';
PRINT '  - aaaa_erp_kt*: 1 tablo';
PRINT '  - ARYD_*      : 1 tablo';
PRINT '';
PRINT 'Test ortamı hazır!';
PRINT 'erptest.komtera.com domain''i otomatik olarak';
PRINT 'atest_ prefix''li tabloları kullanacak.';
PRINT '==========================================';
GO
