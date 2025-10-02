-- =============================================
-- Test Tablolarına Index Kopyalama Scripti
-- =============================================
-- Orijinal tabloların tüm index'lerini test tablolarına kopyalar
-- =============================================

USE LKS;
GO

PRINT 'Index''ler kopyalanıyor...';
PRINT '';

-- Tüm index'leri dinamik olarak kopyalayan script
DECLARE @sql NVARCHAR(MAX);
DECLARE @originalTable NVARCHAR(128);
DECLARE @testTable NVARCHAR(128);
DECLARE @indexName NVARCHAR(128);
DECLARE @newIndexName NVARCHAR(128);
DECLARE @indexType VARCHAR(20);
DECLARE @isUnique BIT;
DECLARE @columns NVARCHAR(MAX);
DECLARE @includedColumns NVARCHAR(MAX);

-- Cursor ile tüm orijinal tabloları ve index'lerini tara
DECLARE index_cursor CURSOR FOR
SELECT
    t.name AS TableName,
    i.name AS IndexName,
    i.type_desc AS IndexType,
    i.is_unique AS IsUnique
FROM sys.tables t
INNER JOIN sys.indexes i ON t.object_id = i.object_id
WHERE t.name LIKE 'aa_erp_kt_%'
   OR t.name LIKE 'aaa_erp_kt_%'
   OR t.name LIKE 'aaaa_erp_kt_%'
   OR t.name = 'ARYD_FIS_AKTARIM'
AND i.name IS NOT NULL  -- PK ve clustered index'leri atla
AND i.is_primary_key = 0  -- Primary key'leri atla
AND i.type > 0  -- Heap'leri atla
ORDER BY t.name, i.name;

OPEN index_cursor;
FETCH NEXT FROM index_cursor INTO @originalTable, @indexName, @indexType, @isUnique;

WHILE @@FETCH_STATUS = 0
BEGIN
    -- Test tablosu adını oluştur
    SET @testTable = 'atest_' + @originalTable;
    SET @newIndexName = REPLACE(@indexName, @originalTable, @testTable);

    -- Test tablosu var mı kontrol et
    IF EXISTS (SELECT 1 FROM sys.tables WHERE name = @testTable)
    BEGIN
        -- Index kolonlarını al
        SELECT @columns = STRING_AGG(c.name, ', ') WITHIN GROUP (ORDER BY ic.key_ordinal)
        FROM sys.index_columns ic
        INNER JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id
        WHERE ic.object_id = OBJECT_ID(@originalTable)
        AND ic.index_id = (SELECT index_id FROM sys.indexes WHERE object_id = OBJECT_ID(@originalTable) AND name = @indexName)
        AND ic.is_included_column = 0;

        -- Included kolonları al (varsa)
        SELECT @includedColumns = STRING_AGG(c.name, ', ') WITHIN GROUP (ORDER BY ic.index_column_id)
        FROM sys.index_columns ic
        INNER JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id
        WHERE ic.object_id = OBJECT_ID(@originalTable)
        AND ic.index_id = (SELECT index_id FROM sys.indexes WHERE object_id = OBJECT_ID(@originalTable) AND name = @indexName)
        AND ic.is_included_column = 1;

        -- Index'i oluştur (varsa önce sil)
        IF EXISTS (SELECT 1 FROM sys.indexes WHERE object_id = OBJECT_ID(@testTable) AND name = @newIndexName)
        BEGIN
            SET @sql = 'DROP INDEX ' + QUOTENAME(@newIndexName) + ' ON ' + QUOTENAME(@testTable);
            EXEC sp_executesql @sql;
        END

        -- Yeni index'i oluştur
        SET @sql = 'CREATE ';

        IF @isUnique = 1
            SET @sql = @sql + 'UNIQUE ';

        SET @sql = @sql + 'NONCLUSTERED INDEX ' + QUOTENAME(@newIndexName) +
                   ' ON ' + QUOTENAME(@testTable) + ' (' + @columns + ')';

        -- Include kolonları varsa ekle
        IF @includedColumns IS NOT NULL AND LEN(@includedColumns) > 0
            SET @sql = @sql + ' INCLUDE (' + @includedColumns + ')';

        -- Index'i oluştur
        BEGIN TRY
            EXEC sp_executesql @sql;
            PRINT '✓ ' + @testTable + ' - ' + @newIndexName + ' oluşturuldu';
        END TRY
        BEGIN CATCH
            PRINT '✗ HATA: ' + @testTable + ' - ' + @newIndexName + ': ' + ERROR_MESSAGE();
        END CATCH
    END

    FETCH NEXT FROM index_cursor INTO @originalTable, @indexName, @indexType, @isUnique;
END

CLOSE index_cursor;
DEALLOCATE index_cursor;

PRINT '';
PRINT '==========================================';
PRINT 'Index kopyalama tamamlandı!';
PRINT '==========================================';
GO

-- İstatistikleri güncelle
PRINT '';
PRINT 'İstatistikler güncelleniyor...';

DECLARE @updateStats NVARCHAR(MAX);
DECLARE @testTableName NVARCHAR(128);

DECLARE stats_cursor CURSOR FOR
SELECT name
FROM sys.tables
WHERE name LIKE 'atest_%';

OPEN stats_cursor;
FETCH NEXT FROM stats_cursor INTO @testTableName;

WHILE @@FETCH_STATUS = 0
BEGIN
    SET @updateStats = 'UPDATE STATISTICS ' + QUOTENAME(@testTableName) + ' WITH FULLSCAN';
    EXEC sp_executesql @updateStats;
    PRINT '✓ ' + @testTableName + ' istatistikleri güncellendi';

    FETCH NEXT FROM stats_cursor INTO @testTableName;
END

CLOSE stats_cursor;
DEALLOCATE stats_cursor;

PRINT '';
PRINT '==========================================';
PRINT 'Tüm işlemler tamamlandı!';
PRINT 'Test tabloları artık orijinal tablolarla';
PRINT 'aynı index yapısına sahip.';
PRINT '==========================================';
GO
