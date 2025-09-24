-- aa_erp_kt_log tablosunu oluştur
CREATE TABLE aa_erp_kt_log (
    id INT IDENTITY(1,1) PRIMARY KEY,
    tarih DATETIME NOT NULL DEFAULT GETDATE(),
    modul NVARCHAR(100) NOT NULL,
    kullanici NVARCHAR(100) NOT NULL,
    yapilan_islem NVARCHAR(500) NOT NULL,
    detay NVARCHAR(MAX) NULL,
    ip_adres NVARCHAR(45) NULL,
    olusturma_tarihi DATETIME NOT NULL DEFAULT GETDATE()
);

-- Index'ler ekle (performans için)
CREATE INDEX IX_aa_erp_kt_log_tarih ON aa_erp_kt_log (tarih);
CREATE INDEX IX_aa_erp_kt_log_modul ON aa_erp_kt_log (modul);
CREATE INDEX IX_aa_erp_kt_log_kullanici ON aa_erp_kt_log (kullanici);

-- Örnek kayıtlar
INSERT INTO aa_erp_kt_log (modul, kullanici, yapilan_islem, detay, ip_adres) VALUES
('FIRSAT', 'admin', 'Yeni fırsat oluşturuldu', 'Fırsat No: F20250924-001, Marka: SOPHOS', '127.0.0.1'),
('BAYI', 'admin', 'Bayi yetkili eklendi', 'Bayi: KOMTERA, Yetkili: Ahmet Yılmaz', '127.0.0.1'),
('MUSTERI', 'admin', 'Müşteri yetkili güncellendi', 'Müşteri ID: 123, Yetkili: Mehmet Özkan', '127.0.0.1');