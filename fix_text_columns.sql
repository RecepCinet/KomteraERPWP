-- =============================================
-- TEXT Kolonlarını VARCHAR'a Çevirme Scripti
-- =============================================
-- TEXT veri tipi deprecated ve performans sorunlu
-- Modern MSSQL'de VARCHAR(MAX) veya NVARCHAR(MAX) kullanılmalı
-- =============================================

USE LKS;
GO

PRINT 'TEXT kolonları VARCHAR''a çevriliyor...';
PRINT '';

-- atest_aa_erp_kt_firsatlar tablosu için
PRINT 'atest_aa_erp_kt_firsatlar tablosu düzenleniyor...';

-- FIRSAT_NO TEXT ise NVARCHAR'a çevir
IF EXISTS (
    SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_NAME = 'atest_aa_erp_kt_firsatlar'
    AND COLUMN_NAME = 'FIRSAT_NO'
    AND DATA_TYPE = 'text'
)
BEGIN
    ALTER TABLE atest_aa_erp_kt_firsatlar
    ALTER COLUMN FIRSAT_NO NVARCHAR(50);
    PRINT '✓ FIRSAT_NO → NVARCHAR(50)';
END

-- FIRSAT_ACIKLAMA TEXT ise NVARCHAR(MAX)'e çevir
IF EXISTS (
    SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_NAME = 'atest_aa_erp_kt_firsatlar'
    AND COLUMN_NAME = 'FIRSAT_ACIKLAMA'
    AND DATA_TYPE = 'text'
)
BEGIN
    ALTER TABLE atest_aa_erp_kt_firsatlar
    ALTER COLUMN FIRSAT_ACIKLAMA NVARCHAR(MAX);
    PRINT '✓ FIRSAT_ACIKLAMA → NVARCHAR(MAX)';
END

-- Diğer TEXT kolonları da varsa
DECLARE @sql NVARCHAR(MAX);
DECLARE @tableName NVARCHAR(128) = 'atest_aa_erp_kt_firsatlar';
DECLARE @columnName NVARCHAR(128);

DECLARE text_cursor CURSOR FOR
SELECT COLUMN_NAME
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = @tableName
AND DATA_TYPE = 'text';

OPEN text_cursor;
FETCH NEXT FROM text_cursor INTO @columnName;

WHILE @@FETCH_STATUS = 0
BEGIN
    SET @sql = 'ALTER TABLE ' + @tableName + ' ALTER COLUMN ' + @columnName + ' NVARCHAR(MAX)';
    EXEC sp_executesql @sql;
    PRINT '✓ ' + @columnName + ' → NVARCHAR(MAX)';

    FETCH NEXT FROM text_cursor INTO @columnName;
END

CLOSE text_cursor;
DEALLOCATE text_cursor;

PRINT '';
PRINT '==========================================';
PRINT 'TEXT kolonları başarıyla VARCHAR''a çevrildi!';
PRINT '==========================================';
GO
