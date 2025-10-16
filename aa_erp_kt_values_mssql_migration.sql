-- MSSQL Migration: aa_erp_kt_values table
-- Bu dosyayı MSSQL'de çalıştırın

-- TEST ORTAMI için:
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'atest_aa_erp_kt_values')
BEGIN
    CREATE TABLE [atest_aa_erp_kt_values] (
        [id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        [key] NVARCHAR(255) NOT NULL UNIQUE,
        [value] NVARCHAR(MAX) NULL
    );

    -- İlk kayıt - teklif_notu
    INSERT INTO [atest_aa_erp_kt_values] ([key], [value])
    VALUES ('teklif_notu', '');
END
GO

-- CANLI ORTAM için:
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'aa_erp_kt_values')
BEGIN
    CREATE TABLE [aa_erp_kt_values] (
        [id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        [key] NVARCHAR(255) NOT NULL UNIQUE,
        [value] NVARCHAR(MAX) NULL
    );

    -- İlk kayıt - teklif_notu
    INSERT INTO [aa_erp_kt_values] ([key], [value])
    VALUES ('teklif_notu', '');
END
GO
