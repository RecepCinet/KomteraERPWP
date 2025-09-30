-- aa_erp_kt_firsatlar tablosu için alan uzunluklarını düzeltme
-- Bu script'i SQL Server Management Studio'da çalıştırın

-- 1. Mevcut tablo yapısını kontrol et
SELECT
    COLUMN_NAME,
    DATA_TYPE,
    CHARACTER_MAXIMUM_LENGTH
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'aa_erp_kt_firsatlar'
    AND DATA_TYPE IN ('varchar', 'nvarchar', 'char')
ORDER BY ORDINAL_POSITION;

-- 2. Problem olabilecek alanları genişlet
-- BAYI_ADI - şu anda muhtemelen 30-40 karakter, 100'e çıkar
ALTER TABLE aa_erp_kt_firsatlar
ALTER COLUMN BAYI_ADI NVARCHAR(100);

-- MUSTERI_ADI - şu anda muhtemelen 30-40 karakter, 100'e çıkar
ALTER TABLE aa_erp_kt_firsatlar
ALTER COLUMN MUSTERI_ADI NVARCHAR(100);

-- FIRSAT_ACIKLAMA - şu anda muhtemelen 25-30 karakter, 500'e çıkar
ALTER TABLE aa_erp_kt_firsatlar
ALTER COLUMN FIRSAT_ACIKLAMA NVARCHAR(500);

-- BAYI_CHKODU - 15 karaktere çıkar
ALTER TABLE aa_erp_kt_firsatlar
ALTER COLUMN BAYI_CHKODU NVARCHAR(20);

-- BAYI_YETKILI_ISIM - 50 karaktere çıkar
ALTER TABLE aa_erp_kt_firsatlar
ALTER COLUMN BAYI_YETKILI_ISIM NVARCHAR(50);

-- MUSTERI_YETKILI_ISIM - 50 karaktere çıkar
ALTER TABLE aa_erp_kt_firsatlar
ALTER COLUMN MUSTERI_YETKILI_ISIM NVARCHAR(50);

-- OLASILIK - 20 karaktere çıkar
ALTER TABLE aa_erp_kt_firsatlar
ALTER COLUMN OLASILIK NVARCHAR(20);

-- GELIS_KANALI - 20 karaktere çıkar
ALTER TABLE aa_erp_kt_firsatlar
ALTER COLUMN GELIS_KANALI NVARCHAR(20);

-- MARKA - 30 karaktere çıkar
ALTER TABLE aa_erp_kt_firsatlar
ALTER COLUMN MARKA NVARCHAR(30);

-- PROJE_ADI - 100 karaktere çıkar
ALTER TABLE aa_erp_kt_firsatlar
ALTER COLUMN PROJE_ADI NVARCHAR(100);

-- Kontrolü tekrar yap
SELECT
    COLUMN_NAME,
    DATA_TYPE,
    CHARACTER_MAXIMUM_LENGTH
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'aa_erp_kt_firsatlar'
    AND DATA_TYPE IN ('varchar', 'nvarchar', 'char')
    AND CHARACTER_MAXIMUM_LENGTH < 50
ORDER BY CHARACTER_MAXIMUM_LENGTH;