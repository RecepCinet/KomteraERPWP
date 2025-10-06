-- Tüm tablolar için kombine script (identity olan/olmayan)
DECLARE @sql NVARCHAR(MAX) = '';

SELECT @sql = @sql +
              CASE
                  WHEN EXISTS (
                      SELECT 1
                      FROM sys.columns c
                      WHERE c.object_id = t.object_id
                        AND c.is_identity = 1
                        AND c.name = 'ID'
                  ) THEN
                      -- Identity olan tablolar için
                      '-- ' + t.name + ' (identity)' + CHAR(13) +
    'IF EXISTS (SELECT 1 FROM sys.tables WHERE name = ''atest_' + t.name + ''')' + CHAR(13) +
    'BEGIN' + CHAR(13) +
    '    DELETE FROM atest_' + t.name + ';' + CHAR(13) +
    '    SET IDENTITY_INSERT atest_' + t.name + ' ON;' + CHAR(13) +
    '    INSERT INTO atest_' + t.name + ' SELECT * FROM ' + t.name + ';' + CHAR(13) +
    '    SET IDENTITY_INSERT atest_' + t.name + ' OFF;' + CHAR(13) +
    '    PRINT ''' + t.name + ' tablosundan '' + CAST(@@ROWCOUNT AS NVARCHAR(10)) + '' kayıt kopyalandı (identity).'';' + CHAR(13) +
    'END' + CHAR(13)
    ELSE
       -- Identity olmayan tablolar için
    '-- ' + t.name + ' (no identity)' + CHAR(13) +
    'IF EXISTS (SELECT 1 FROM sys.tables WHERE name = ''atest_' + t.name + ''')' + CHAR(13) +
    'BEGIN' + CHAR(13) +
    '    DELETE FROM atest_' + t.name + ';' + CHAR(13) +
    '    INSERT INTO atest_' + t.name + ' SELECT * FROM ' + t.name + ';' + CHAR(13) +
    '    PRINT ''' + t.name + ' tablosundan '' + CAST(@@ROWCOUNT AS NVARCHAR(10)) + '' kayıt kopyalandı.'';' + CHAR(13) +
    'END' + CHAR(13)
END + CHAR(13)
FROM sys.tables t
WHERE t.name LIKE 'aa_erp_%'
AND EXISTS (SELECT 1 FROM sys.tables WHERE name = 'atest_' + t.name)
ORDER BY t.name;

PRINT 'Tüm tablolar kopyalanıyor...';
EXEC sp_executesql @sql;
PRINT 'Kopyalama işlemi tamamlandı.';