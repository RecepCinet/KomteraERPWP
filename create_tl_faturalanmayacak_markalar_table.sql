-- TL Faturalanmayacak Markalar tablosu
-- Test için: atest_aa_erp_kt_ayarlar_tl_faturalanmayacak_markalar
-- Production için: aa_erp_kt_ayarlar_tl_faturalanmayacak_markalar

CREATE TABLE IF NOT EXISTS `atest_aa_erp_kt_ayarlar_tl_faturalanmayacak_markalar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `marka` varchar(100) NOT NULL COMMENT 'Marka adı',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `marka` (`marka`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Örnek kayıtlar
INSERT INTO `atest_aa_erp_kt_ayarlar_tl_faturalanmayacak_markalar` (`marka`) VALUES
('SECHARD'),
('KOMSPOT');
