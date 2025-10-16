-- Migration: aa_erp_kt_values table
-- Bu dosyayı hem CANLI hem TEST ortamlarında çalıştırın
-- Test ortamında tablo ismi: atest_aa_erp_kt_values
-- Canlı ortamında tablo ismi: aa_erp_kt_values

-- TEST ORTAMI için:
CREATE TABLE IF NOT EXISTS `atest_aa_erp_kt_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- İlk kayıt - teklif_notu
INSERT INTO `atest_aa_erp_kt_values` (`key`, `value`)
VALUES ('teklif_notu', '')
ON DUPLICATE KEY UPDATE `key`='teklif_notu';

-- CANLI ORTAM için:
CREATE TABLE IF NOT EXISTS `aa_erp_kt_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- İlk kayıt - teklif_notu
INSERT INTO `aa_erp_kt_values` (`key`, `value`)
VALUES ('teklif_notu', '')
ON DUPLICATE KEY UPDATE `key`='teklif_notu';
