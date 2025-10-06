-- aa_ ile başlayan tüm tabloları bul
SELECT
    t.name AS kaynak_tablo,
    CASE
        WHEN EXISTS (SELECT 1 FROM sys.tables WHERE name = 'atest_' + t.name)
        THEN 'MEVCUT'
        ELSE 'KOPYALANAMADI - TABLO YOK'
    END AS durum,
    'atest_' + t.name AS hedef_tablo
FROM sys.tables t
WHERE t.name LIKE 'aa_%'
ORDER BY t.name;

-- Özet
SELECT
    COUNT(*) AS toplam_aa_tablo,
    SUM(CASE WHEN EXISTS (SELECT 1 FROM sys.tables t2 WHERE t2.name = 'atest_' + t.name) THEN 1 ELSE 0 END) AS kopyalanan,
    SUM(CASE WHEN NOT EXISTS (SELECT 1 FROM sys.tables t2 WHERE t2.name = 'atest_' + t.name) THEN 1 ELSE 0 END) AS kopyalanamayan
FROM sys.tables t
WHERE t.name LIKE 'aa_%';

-- Detaylı kolon karşılaştırması (uyumsuz kolonları bul)
SELECT DISTINCT
    t1.name AS kaynak_tablo,
    'atest_' + t1.name AS hedef_tablo,
    'KOLON UYUŞMAZLIĞI' AS hata_tipi,
    c1.name AS kaynak_kolon,
    c2.name AS hedef_kolon
FROM sys.tables t1
INNER JOIN sys.tables t2 ON t2.name = 'atest_' + t1.name
LEFT JOIN sys.columns c1 ON c1.object_id = t1.object_id
LEFT JOIN sys.columns c2 ON c2.object_id = t2.object_id AND c1.name = c2.name
WHERE t1.name LIKE 'aa_%'
AND (c2.name IS NULL OR c1.name IS NULL)
ORDER BY t1.name;
