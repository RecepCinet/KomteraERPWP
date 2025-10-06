-- aa_erp_kt_* tablolarındaki dataları atest_aa_erp_kt_* tablolarına kopyala
-- ID kolonları hariç (yeni ID'ler otomatik oluşacak)

DECLARE @tableName NVARCHAR(200);
DECLARE @atestTableName NVARCHAR(200);
DECLARE @columns NVARCHAR(MAX);
DECLARE @sql NVARCHAR(MAX);

DECLARE table_cursor CURSOR FOR
SELECT name
FROM sys.tables
WHERE name LIKE 'aa_erp_kt_%'
AND EXISTS (SELECT 1 FROM sys.tables WHERE name = 'atest_' + sys.tables.name)
ORDER BY name;

OPEN table_cursor;
FETCH NEXT FROM table_cursor INTO @tableName;

WHILE @@FETCH_STATUS = 0
BEGIN
    SET @atestTableName = 'atest_' + @tableName;

    -- ID kolonları hariç tüm kolonları al
    SELECT @columns = STRING_AGG(QUOTENAME(c.name), ', ')
    FROM sys.columns c
    WHERE c.object_id = OBJECT_ID(@tableName)
    AND c.name NOT IN ('id', 'ID', 'Id')
    AND c.is_computed = 0;

    -- INSERT script'i oluştur
    SET @sql = 'INSERT INTO ' + QUOTENAME(@atestTableName) + ' (' + @columns + ') ' +
               'SELECT ' + @columns + ' FROM ' + QUOTENAME(@tableName) + ';';

    BEGIN TRY
        EXEC sp_executesql @sql;
        PRINT 'Copied data: ' + @tableName + ' -> ' + @atestTableName + ' (' + CAST(@@ROWCOUNT AS VARCHAR) + ' rows)';
    END TRY
    BEGIN CATCH
        PRINT 'ERROR copying ' + @tableName + ': ' + ERROR_MESSAGE();
    END CATCH

    FETCH NEXT FROM table_cursor INTO @tableName;
END;

CLOSE table_cursor;
DEALLOCATE table_cursor;

PRINT 'Data copy completed!';
