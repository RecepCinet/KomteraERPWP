-- Onaylar Diğer tablosu
-- Test için: atest_aa_erp_kt_ayarlar_onaylar_diger
-- Production için: aa_erp_kt_ayarlar_onaylar_diger

CREATE TABLE IF NOT EXISTS `atest_aa_erp_kt_ayarlar_onaylar_diger` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key_name` varchar(100) NOT NULL COMMENT 'Anahtar kelime/key',
  `aciklama` text DEFAULT NULL COMMENT 'Açıklama',
  `kullanici` varchar(255) DEFAULT NULL COMMENT 'WordPress kullanıcı display name',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_name` (`key_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Örnek kayıt
INSERT INTO `atest_aa_erp_kt_ayarlar_onaylar_diger` (`key_name`, `aciklama`, `kullanici`) VALUES
('teklif_onay', 'Teklif onayı için mail gönderilecek kullanıcı', 'Admin User');
