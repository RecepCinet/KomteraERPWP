<?php
// Bu dosya WordPress context'inde include ediliyor, authentication ve wp-load gerekmez

// Database connection
include dirname(__DIR__) . '/_conn.php';

// Get firsat_no parameter
$firsat_no = $_GET['firsat_no'] ?? '';

if (empty($firsat_no)) {
    echo '<div style="padding: 40px; text-align: center; color: #d32f2f;">Fırsat numarası belirtilmemiş.</div>';
    exit;
}

// Fetch firsat details
$firsat_data = null;
try {
    $sql = "SELECT TOP 1 * FROM aa_erp_kt_firsatlar
            WHERE FIRSAT_NO = :firsat_no AND (SIL IS NULL OR SIL <> '1')";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':firsat_no', $firsat_no);
    $stmt->execute();
    $firsat_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$firsat_data) {
        echo '<div style="padding: 40px; text-align: center; color: #d32f2f;">Fırsat bulunamadı: ' . htmlspecialchars($firsat_no) . '</div>';
        exit;
    }
} catch (Exception $e) {
    echo '<div style="padding: 40px; text-align: center; color: #d32f2f;">Veri çekme hatası: ' . htmlspecialchars($e->getMessage()) . '</div>';
    exit;
}

// Fetch related teklifler
$teklifler = [];
$teklif_error = '';
try {
    // Tablo yapısına göre düzeltilmiş sorgu
    $teklif_sql = "SELECT t.TEKLIF_NO,
                          t.YARATILIS_TARIHI,
                          t.YARATILIS_SAATI,
                          t.KILIT,
                          t.TEKLIF_TIPI,
                          t.SATIS_TIPI
                   FROM aa_erp_kt_teklifler t
                   WHERE t.X_FIRSAT_NO = :firsat_no AND (t.SIL IS NULL OR t.SIL <> '1')
                   ORDER BY t.YARATILIS_TARIHI DESC, t.YARATILIS_SAATI DESC";

    $teklif_stmt = $conn->prepare($teklif_sql);
    $teklif_stmt->bindParam(':firsat_no', $firsat_no);
    $teklif_stmt->execute();
    $teklifler_temp = $teklif_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Her teklif için ürün sayısını ayrı ayrı al
    foreach ($teklifler_temp as $teklif) {
        $urun_sql = "SELECT COUNT(*) as URUN_SAYISI
                     FROM aa_erp_kt_teklifler_urunler
                     WHERE X_TEKLIF_NO = :teklif_no";
        $urun_stmt = $conn->prepare($urun_sql);
        $urun_stmt->bindParam(':teklif_no', $teklif['TEKLIF_NO']);
        $urun_stmt->execute();
        $urun_result = $urun_stmt->fetch(PDO::FETCH_ASSOC);

        $teklif['URUN_SAYISI'] = $urun_result['URUN_SAYISI'] ?? 0;
        $teklifler[] = $teklif;
    }

} catch (Exception $e) {
    $teklif_error = $e->getMessage();
}

?><!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fırsat Detay - <?php echo htmlspecialchars($firsat_no); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f8f9fa;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: #fff;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid #007cba;
        }

        .header h1 {
            color: #007cba;
            font-size: 28px;
            margin-bottom: 8px;
        }

        .header .subtitle {
            color: #666;
            font-size: 16px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-left: 12px;
        }

        .status-acik { background: #e3f2fd; color: #1976d2; }
        .status-kazanildi { background: #e8f5e8; color: #2e7d32; }
        .status-kaybedildi { background: #ffebee; color: #c62828; }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: 1px solid #e0e0e0;
        }

        .card h2 {
            color: #007cba;
            font-size: 18px;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #007cba;
        }

        .field-group {
            margin-bottom: 16px;
        }

        .field-group:last-child {
            margin-bottom: 0;
        }

        .field-label {
            font-weight: 300;
            color: #888;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .field-value {
            font-size: 15px;
            color: #000;
            font-weight: bold;
            min-height: 20px;
        }

        .field-value.empty {
            color: #999;
            font-style: italic;
        }

        .wide-card {
            grid-column: 1 / -1;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        .table th {
            background: #f5f5f5;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .table td {
            font-size: 14px;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
        }

        .teklif-link {
            color: #007cba;
            text-decoration: none;
            font-weight: 500;
        }

        .teklif-link:hover {
            text-decoration: underline;
        }

        .action-icons {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
        }

        .icon-btn:hover {
            transform: scale(1.1);
        }

        .icon-cogalt { background: #e3f2fd; color: #1976d2; }
        .icon-cogalt:hover { background: #bbdefb; }

        .icon-pdf { background: #fff3e0; color: #f57c00; }
        .icon-pdf:hover { background: #ffe0b2; }

        .icon-siparis { background: #e8f5e8; color: #2e7d32; }
        .icon-siparis:hover { background: #c8e6c9; }

        .icon-ana-teklif { background: #fff8e1; color: #f9a825; }
        .icon-ana-teklif:hover { background: #ffecb3; }

        .icon-kilit { background: #ffebee; color: #c62828; }
        .icon-kilit:hover { background: #ffcdd2; }

        .status-icon {
            width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 12px;
            margin-left: 8px;
        }

        .empty-state {
            text-align: center;
            color: #999;
            font-style: italic;
            padding: 40px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 12px;
            }

            .grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .card {
                padding: 16px;
            }

            .header {
                padding: 16px;
            }

            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>
                Fırsat Detay: <?php echo htmlspecialchars($firsat_data['FIRSAT_NO']); ?>
                <?php
                $durum = $firsat_data['DURUM'] ?? '0';
                $status_class = 'status-acik';
                $status_text = 'Açık';

                if ($durum == '1') {
                    $status_class = 'status-kazanildi';
                    $status_text = 'Kazanıldı';
                } elseif ($durum == '-1') {
                    $status_class = 'status-kaybedildi';
                    $status_text = 'Kaybedildi';
                }
                ?>
                <span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
            </h1>
            <div class="subtitle"><?php echo htmlspecialchars($firsat_data['PROJE_ADI'] ?? 'Proje adı belirtilmemiş'); ?></div>

            <!-- Fırsat Açıklaması -->
            <div style="margin-top: 16px; padding: 16px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid #007cba;">
                <div style="font-weight: bold; color: #000; font-size: 15px; line-height: 1.5;">
                    <?php echo nl2br(htmlspecialchars($firsat_data['FIRSAT_ACIKLAMA'] ?? 'Açıklama girilmemiş')); ?>
                </div>
            </div>
        </div>

        <!-- İlişkili Teklifler - Cardların üstünde -->
        <div style="background: #fff; border-radius: 8px; padding: 24px; margin: 24px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border: 1px solid #e0e0e0; border-left: 4px solid #007cba;">
            <h2 style="color: #007cba; font-size: 18px; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid #007cba;">İlişkili Teklifler</h2>
                <?php if (!empty($teklif_error)): ?>
                    <div style="background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 12px; border-radius: 4px; margin-bottom: 16px;">
                        <strong>Hata:</strong> <?php echo htmlspecialchars($teklif_error); ?>
                    </div>
                <?php endif; ?>

                <!-- Debug bilgisi -->
                <div style="background: #f8f9fa; border: 1px solid #dee2e6; color: #6c757d; padding: 8px; border-radius: 4px; margin-bottom: 16px; font-size: 12px;">
                    <strong>Debug:</strong> Fırsat No: <?php echo htmlspecialchars($firsat_no); ?> |
                    Bulunan Teklif Sayısı: <?php echo count($teklifler); ?>
                </div>

                <?php if (count($teklifler) > 0): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Teklif No</th>
                                <th>Açma Tarihi</th>
                                <th>Ürün Sayısı</th>
                                <th>Teklif Tipi</th>
                                <th>İşlemler</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($teklifler as $teklif): ?>
                                <tr>
                                    <td>
                                        <a href="#" class="teklif-link" onclick="TeklifAc('<?php echo htmlspecialchars($teklif['TEKLIF_NO']); ?>')">
                                            <?php echo htmlspecialchars($teklif['TEKLIF_NO']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php
                                        if ($teklif['YARATILIS_TARIHI']) {
                                            try {
                                                $tarih_str = $teklif['YARATILIS_TARIHI'];
                                                if (!empty($teklif['YARATILIS_SAATI'])) {
                                                    $tarih_str .= ' ' . $teklif['YARATILIS_SAATI'];
                                                }
                                                $date = new DateTime($tarih_str);
                                                echo $date->format('d.m.Y H:i');
                                            } catch (Exception $e) {
                                                echo htmlspecialchars($teklif['YARATILIS_TARIHI']);
                                            }
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo $teklif['URUN_SAYISI'] ?? 0; ?> ürün</td>
                                    <td><?php echo htmlspecialchars($teklif['TEKLIF_TIPI'] ?? '-'); ?></td>
                                    <td>
                                        <div class="action-icons">
                                            <button class="icon-btn icon-cogalt" title="Çoğalt" onclick="teklifCogalt('<?php echo htmlspecialchars($teklif['TEKLIF_NO']); ?>')">
                                                📋
                                            </button>
                                            <button class="icon-btn icon-pdf" title="PDF İndir" onclick="teklifPDF('<?php echo htmlspecialchars($teklif['TEKLIF_NO']); ?>')">
                                                📄
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center;">
                                            <?php
                                            // Satış tipi gösterimi
                                            $satis_tipi = $teklif['SATIS_TIPI'] ?? '';
                                            if ($satis_tipi == '0') {
                                                echo '<span class="status-icon" style="background: #e3f2fd; color: #1976d2;" title="İlk Satış">1️⃣</span>';
                                            } elseif ($satis_tipi == '1') {
                                                echo '<span class="status-icon" style="background: #fff8e1; color: #f9a825;" title="Yenileme">🔄</span>';
                                            }
                                            ?>

                                            <?php if ($teklif['KILIT'] == '1'): ?>
                                                <span class="status-icon icon-kilit" title="Kilitli">🔒</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        Bu fırsatla ilişkili herhangi bir teklif bulunamadı.
                    </div>
                <?php endif; ?>
        </div>

        <div class="grid">
            <!-- Temel Bilgiler -->
            <div class="card">
                <h2>Temel Bilgiler</h2>
                <div class="field-group">
                    <div class="field-label">Marka</div>
                    <div class="field-value <?php echo empty($firsat_data['MARKA']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['MARKA'] ?? 'Belirtilmemiş'); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label">Olasılık</div>
                    <div class="field-value <?php echo empty($firsat_data['OLASILIK']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['OLASILIK'] ?? 'Belirtilmemiş'); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label">Geliş Kanalı</div>
                    <div class="field-value <?php echo empty($firsat_data['GELIS_KANALI']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['GELIS_KANALI'] ?? 'Belirtilmemiş'); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label">Para Birimi</div>
                    <div class="field-value <?php echo empty($firsat_data['PARA_BIRIMI']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['PARA_BIRIMI'] ?? 'Belirtilmemiş'); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label">Register</div>
                    <div class="field-value">
                        <?php echo ($firsat_data['REGISTER'] == '1') ? '✓ Evet' : '✗ Hayır'; ?>
                    </div>
                </div>
            </div>

            <!-- Bayi Bilgileri -->
            <div class="card">
                <h2>Bayi Bilgileri</h2>
                <div class="field-group">
                    <div class="field-label">Bayi Adı</div>
                    <div class="field-value <?php echo empty($firsat_data['BAYI_ADI']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['BAYI_ADI'] ?? 'Belirtilmemiş'); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label">Bayi Kodu</div>
                    <div class="field-value <?php echo empty($firsat_data['BAYI_CHKODU']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['BAYI_CHKODU'] ?? 'Belirtilmemiş'); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label">Bayi Yetkili</div>
                    <div class="field-value <?php echo empty($firsat_data['BAYI_YETKILI_ISIM']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['BAYI_YETKILI_ISIM'] ?? 'Belirtilmemiş'); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label">Telefon</div>
                    <div class="field-value <?php echo empty($firsat_data['BAYI_YETKILI_TEL']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['BAYI_YETKILI_TEL'] ?? 'Belirtilmemiş'); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label">E-posta</div>
                    <div class="field-value <?php echo empty($firsat_data['BAYI_YETKILI_EPOSTA']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['BAYI_YETKILI_EPOSTA'] ?? 'Belirtilmemiş'); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label">Adres</div>
                    <div class="field-value <?php echo empty($firsat_data['BAYI_ADRES']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['BAYI_ADRES'] ?? 'Belirtilmemiş'); ?>
                    </div>
                </div>
            </div>

            <!-- Müşteri Bilgileri -->
            <div class="card">
                <h2>Müşteri Bilgileri</h2>
                <div class="field-group">
                    <div class="field-label">Müşteri Adı</div>
                    <div class="field-value <?php echo empty($firsat_data['MUSTERI_ADI']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['MUSTERI_ADI'] ?? 'Belirtilmemiş'); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label">Müşteri Yetkili</div>
                    <div class="field-value <?php echo empty($firsat_data['MUSTERI_YETKILI_ISIM']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['MUSTERI_YETKILI_ISIM'] ?? 'Belirtilmemiş'); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label">Telefon</div>
                    <div class="field-value <?php echo empty($firsat_data['MUSTERI_YETKILI_TEL']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['MUSTERI_YETKILI_TEL'] ?? 'Belirtilmemiş'); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label">E-posta</div>
                    <div class="field-value <?php echo empty($firsat_data['MUSTERI_YETKILI_EPOSTA']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['MUSTERI_YETKILI_EPOSTA'] ?? 'Belirtilmemiş'); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label">Müşteri Temsilcisi</div>
                    <div class="field-value <?php echo empty($firsat_data['MUSTERI_TEMSILCISI']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['MUSTERI_TEMSILCISI'] ?? 'Belirtilmemiş'); ?>
                    </div>
                </div>
            </div>

            <!-- Tarihler ve Yönetim -->
            <div class="card">
                <h2>Tarihler & Yönetim</h2>
                <div class="field-group">
                    <div class="field-label">Başlangıç Tarihi</div>
                    <div class="field-value <?php echo empty($firsat_data['BASLANGIC_TARIHI']) ? 'empty' : ''; ?>">
                        <?php
                        if ($firsat_data['BASLANGIC_TARIHI']) {
                            $date = new DateTime($firsat_data['BASLANGIC_TARIHI']);
                            echo $date->format('d.m.Y');
                        } else {
                            echo 'Belirtilmemiş';
                        }
                        ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label">Bitiş Tarihi</div>
                    <div class="field-value <?php echo empty($firsat_data['BITIS_TARIHI']) ? 'empty' : ''; ?>">
                        <?php
                        if ($firsat_data['BITIS_TARIHI']) {
                            $date = new DateTime($firsat_data['BITIS_TARIHI']);
                            echo $date->format('d.m.Y');
                        } else {
                            echo 'Belirtilmemiş';
                        }
                        ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label">Kaydı Açan</div>
                    <div class="field-value <?php echo empty($firsat_data['KAYIDI_ACAN']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['KAYIDI_ACAN'] ?? 'Belirtilmemiş'); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label">Marka Manager</div>
                    <div class="field-value <?php echo empty($firsat_data['MARKA_MANAGER']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['MARKA_MANAGER'] ?? 'Belirtilmemiş'); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label">Etkinlik</div>
                    <div class="field-value <?php echo empty($firsat_data['ETKINLIK']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['ETKINLIK'] ?? 'Belirtilmemiş'); ?>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Teklif açma fonksiyonu - şimdilik alert göster
        function TeklifAc(teklifNo) {
            alert('Teklif detayı: ' + teklifNo);
            // TODO: Teklif detay sayfasını aç
        }

        // Teklif çoğaltma fonksiyonu
        function teklifCogalt(teklifNo) {
            if (confirm('Teklifi çoğaltmak istediğinizden emin misiniz?\n\nTeklif No: ' + teklifNo)) {
                alert('Çoğaltma işlemi başlatıldı: ' + teklifNo);
                // TODO: Teklif çoğaltma işlemi
            }
        }

        // PDF indirme fonksiyonu
        function teklifPDF(teklifNo) {
            alert('PDF indiriliyor: ' + teklifNo);
            // TODO: PDF indirme işlemi
            // window.open('pdf_endpoint.php?teklif_no=' + teklifNo, '_blank');
        }

        // Sayfa yüklendiğinde focus için
        document.addEventListener('DOMContentLoaded', function() {
            document.body.style.opacity = '1';
        });
    </script>
</body>
</html>