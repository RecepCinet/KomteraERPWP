<?php
// Bu dosya WordPress context'inde include ediliyor, authentication ve wp-load gerekmez

// Database connection
include dirname(__DIR__) . '/_conn.php';

// Get firsat_no parameter
$firsat_no = $_GET['firsat_no'] ?? '';

if (empty($firsat_no)) {
    echo '<div style="padding: 40px; text-align: center; color: #d32f2f;">' . __('Fırsat numarası belirtilmemiş.', 'komtera') . '</div>';
    exit;
}

// Fetch firsat details
$firsat_data = null;
try {
    $sql = "SELECT TOP 1 * FROM " . getTableName('aa_erp_kt_firsatlar') . "
            WHERE FIRSAT_NO = :firsat_no AND (SIL IS NULL OR SIL <> '1')";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':firsat_no', $firsat_no);
    $stmt->execute();
    $firsat_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$firsat_data) {
        echo '<div style="padding: 40px; text-align: center; color: #d32f2f;">' . __('Fırsat bulunamadı', 'komtera') . ': ' . htmlspecialchars($firsat_no) . '</div>';
        exit;
    }
} catch (Exception $e) {
    echo '<div style="padding: 40px; text-align: center; color: #d32f2f;">' . __('Veri çekme hatası', 'komtera') . ': ' . htmlspecialchars($e->getMessage()) . '</div>';
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
                   FROM " . getTableName('aa_erp_kt_teklifler') . " t
                   WHERE t.X_FIRSAT_NO = :firsat_no AND (t.SIL IS NULL OR t.SIL <> '1')
                   ORDER BY t.YARATILIS_TARIHI DESC, t.YARATILIS_SAATI DESC";

    $teklif_stmt = $conn->prepare($teklif_sql);
    $teklif_stmt->bindParam(':firsat_no', $firsat_no);
    $teklif_stmt->execute();
    $teklifler_temp = $teklif_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Her teklif için ürün sayısını ayrı ayrı al
    foreach ($teklifler_temp as $teklif) {
        $urun_sql = "SELECT COUNT(*) as URUN_SAYISI
                     FROM " . getTableName('aa_erp_kt_teklifler_urunler') . "
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

// Debug: Teklif sayısını ekrana bas
if (empty($teklifler) && empty($teklif_error)) {
    $teklif_error = "Fırsat NO: $firsat_no - Teklif bulunamadı (getTableName: " . getTableName('aa_erp_kt_teklifler') . ")";
}

?><!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('Fırsat Detay', 'komtera'); ?> - <?php echo htmlspecialchars($firsat_no); ?></title>
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
            color: #d32f2f;
            font-style: italic;
            font-weight: normal;
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

        .table tbody tr.ana-teklif {
            background: #d4f0d4 !important;
        }

        .table tbody tr.ana-teklif:hover {
            background: #c8e6c9 !important;
        }

        .status-icon.kilitli {
            background: #333 !important;
            color: #fff !important;
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
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 18px;
        }

        .dashicons {
            font-family: 'dashicons';
            font-size: 18px;
            line-height: 1;
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
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 16px;
            margin-left: 8px;
            background: #f5f5f5 !important;
            color: #999 !important;
        }

        .status-icon .dashicons {
            font-size: 16px;
        }

        .checkbox-column {
            width: 40px;
            text-align: center;
        }

        .teklif-checkbox {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .alternatif-teklif-btn {
            background: #007cba;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            opacity: 0.5;
            pointer-events: none;
            transition: all 0.3s;
        }

        .alternatif-teklif-btn.active {
            opacity: 1;
            pointer-events: auto;
        }

        .alternatif-teklif-btn.active:hover {
            background: #005a87;
        }

        /* Yeni Teklif Butonu */
        .yeni-teklif-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            padding: 14px 28px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 200px;
        }

        .yeni-teklif-btn:hover {
            background: linear-gradient(135deg, #218838 0%, #1abc9c 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }

        .yeni-teklif-btn:active {
            transform: translateY(0);
            box-shadow: 0 3px 10px rgba(40, 167, 69, 0.3);
        }

        .yeni-teklif-btn .dashicons {
            animation: rotate-icon 2s infinite linear;
        }

        @keyframes rotate-icon {
            0% { transform: rotate(0deg); }
            25% { transform: rotate(90deg); }
            50% { transform: rotate(90deg); }
            75% { transform: rotate(90deg); }
            100% { transform: rotate(90deg); }
        }

        .yeni-teklif-btn:hover .dashicons {
            animation: pulse-icon 0.6s ease-in-out;
        }

        @keyframes pulse-icon {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        .firsat-cogalt-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .firsat-cogalt-btn:hover {
            background: #218838;
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

            /* Mobil tablo responsive */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table {
                min-width: 800px;
                font-size: 13px;
            }

            .table th,
            .table td {
                padding: 8px 6px;
                white-space: nowrap;
            }

            .checkbox-column {
                width: 30px;
            }

            .teklif-checkbox {
                width: 14px;
                height: 14px;
            }

            .icon-btn {
                width: 32px;
                height: 32px;
                font-size: 14px;
            }

            .status-icon {
                width: 24px;
                height: 24px;
                font-size: 14px;
            }

            .action-icons {
                gap: 4px;
            }

            .alternatif-teklif-btn {
                padding: 6px 12px;
                font-size: 13px;
            }

            .yeni-teklif-btn {
                padding: 10px 16px;
                font-size: 14px;
                min-width: 150px;
            }

            /* Mobilde bazı sütunları daha kompakt yap */
            .table th:nth-child(3), /* Açma Tarihi */
            .table td:nth-child(3) {
                font-size: 12px;
            }

            .table th:nth-child(4), /* Ürün Sayısı */
            .table td:nth-child(4) {
                font-size: 12px;
            }

            /* Teklif no linkini daha belirgin yap */
            .teklif-link {
                font-size: 14px;
                font-weight: 600;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>
                <?php echo __('Fırsat Detay', 'komtera'); ?>: <?php echo htmlspecialchars($firsat_data['FIRSAT_NO']); ?>
                <?php
                $durum = $firsat_data['DURUM'] ?? '0';
                $status_class = 'status-acik';
                $status_text = __('Açık', 'komtera');

                if ($durum == '1') {
                    $status_class = 'status-kazanildi';
                    $status_text = __('Kazanıldı', 'komtera');
                } elseif ($durum == '-1') {
                    $status_class = 'status-kaybedildi';
                    $status_text = __('Kaybedildi', 'komtera');
                }
                ?>
                <span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
            </h1>
            <div class="subtitle"><?php echo htmlspecialchars($firsat_data['PROJE_ADI'] ?? __('Proje adı belirtilmemiş', 'komtera')); ?></div>

            <!-- Fırsat Açıklaması -->
            <div style="margin-top: 16px; padding: 16px; background: #f8f9fa; border-radius: 1px; border-left: 1px solid #007cba;">
                <div style="font-weight: bold; color: #000; font-size: 15px; line-height: 1.5;">
                    <?php echo nl2br(htmlspecialchars($firsat_data['FIRSAT_ACIKLAMA'] ?? __('Açıklama girilmemiş', 'komtera'))); ?>
                </div>
            </div>

            <!-- Yeni Teklif Butonu -->
            <div style="margin-top: 20px; text-align: right;">
                <button class="yeni-teklif-btn" onclick="yeniTeklifOlustur()" title="<?php echo __('Bu fırsat için yeni teklif oluştur', 'komtera'); ?>">
                    <span class="dashicons dashicons-plus-alt" style="margin-right: 8px; font-size: 18px; line-height: 1;"></span>
                    <?php echo __('Yeni Teklif Oluştur', 'komtera'); ?>
                </button>
            </div>
        </div>

        <!-- İlişkili Teklifler - Cardların üstünde -->
        <div style="background: #fff; border-radius: 8px; padding: 24px; margin: 24px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border: 1px solid #e0e0e0; border-left: 4px solid #007cba;">
                <?php if (!empty($teklif_error)): ?>
                    <div style="background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 12px; border-radius: 4px; margin-bottom: 16px;">
                        <strong><?php echo __('Hata', 'komtera'); ?>:</strong> <?php echo htmlspecialchars($teklif_error); ?>
                    </div>
                <?php endif; ?>


                <?php if (count($teklifler) > 0): ?>
                    <!-- Seçili tekliflerle işlem yapma butonları -->
                    <div style="margin-bottom: 16px; display: flex; gap: 10px;">
                        <button id="alternatifTeklifBtn" class="alternatif-teklif-btn" onclick="alternatifTeklifYap()">
                            <span class="dashicons dashicons-admin-tools" style="margin-right: 5px; font-size: 16px; line-height: 1;"></span>
                            <?php echo __('Seçilileri Alternatifli Teklif Yap', 'komtera'); ?>
                        </button>
                        <button class="firsat-cogalt-btn" onclick="firsatCogalt()">
                            <span class="dashicons dashicons-admin-page" style="margin-right: 5px; font-size: 16px; line-height: 1;"></span>
                            <?php echo __('Fırsat Çoğalt', 'komtera'); ?>
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                        <thead>
                            <tr>
                                <th class="checkbox-column">
                                    <input type="checkbox" id="selectAll" onchange="toggleAllCheckboxes(this)" title="<?php echo __('Tümünü Seç', 'komtera'); ?>">
                                </th>
                                <th><?php echo __('Teklif No', 'komtera'); ?></th>
                                <th><?php echo __('Açma Tarihi', 'komtera'); ?></th>
                                <th><?php echo __('Ürün Sayısı', 'komtera'); ?></th>
                                <th><?php echo __('Teklif Tipi', 'komtera'); ?></th>
                                <th><?php echo __('İşlemler', 'komtera'); ?></th>
                                <th><?php echo __('Durum', 'komtera'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($teklifler as $index => $teklif): ?>
                                <?php
                                $anaTeklifClass = ($teklif['TEKLIF_TIPI'] == '1') ? 'ana-teklif' : '';
                                ?>
                                <tr class="<?php echo $anaTeklifClass; ?>">
                                    <td class="checkbox-column">
                                        <input type="checkbox" class="teklif-checkbox" value="<?php echo htmlspecialchars($teklif['TEKLIF_NO']); ?>" onchange="updateAlternatifButton()">
                                    </td>
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
                                    <td><?php echo $teklif['URUN_SAYISI'] ?? 0; ?> <?php echo __('ürün', 'komtera'); ?></td>
                                    <td><?php echo htmlspecialchars($teklif['TEKLIF_TIPI'] ?? '-'); ?></td>
                                    <td>
                                        <div class="action-icons">
                                            <button class="icon-btn icon-cogalt" title="<?php echo __('Çoğalt', 'komtera'); ?>" onclick="teklifCogalt('<?php echo htmlspecialchars($teklif['TEKLIF_NO']); ?>')">
                                                <span class="dashicons dashicons-admin-page"></span>
                                            </button>
                                            <?php if ($teklif['TEKLIF_TIPI'] != '1'): ?>
                                            <button class="icon-btn icon-ana-teklif" title="<?php echo __('Ana Teklif Yap', 'komtera'); ?>" onclick="anaTeklifYap('<?php echo htmlspecialchars($teklif['TEKLIF_NO']); ?>')">
                                                <span class="dashicons dashicons-star-filled"></span>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 4px;">
                                            <?php
                                            // Satış tipi gösterimi
                                            $satis_tipi = $teklif['SATIS_TIPI'] ?? '';
                                            if ($satis_tipi == '0') {
                                                echo '<span class="status-icon" title="' . __('İlk Satış', 'komtera') . '"><span class="dashicons dashicons-yes-alt"></span></span>';
                                            } elseif ($satis_tipi == '1') {
                                                echo '<span class="status-icon" title="' . __('Yenileme', 'komtera') . '"><span class="dashicons dashicons-update"></span></span>';
                                            }
                                            ?>

                                            <?php if ($teklif['KILIT'] == '1') { ?>
                                                <span class="status-icon kilitli" title="<?php echo __('Kilitli', 'komtera'); ?>"><span class="dashicons dashicons-lock"></span></span>
                                            <?php } else {?>
                                                <span class="status-icon" title="<?php echo __('Kilitli', 'komtera'); ?>"><span class="dashicons dashicons-unlock"></span></span>
                                            <?php } ?>

                                            <!-- PDF Durumu - şimdilik gri, sonra dinamik olacak -->
                                            <span class="status-icon" title="<?php echo __('PDF Hazır Değil', 'komtera'); ?>">
                                                <span class="dashicons dashicons-media-text"></span>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <?php echo __('Bu fırsatla ilişkili herhangi bir teklif bulunamadı.', 'komtera'); ?>
                    </div>
                <?php endif; ?>
        </div>

        <div class="grid">
            <!-- Temel Bilgiler -->
            <div class="card">
                <h2><?php echo __('Temel Bilgiler', 'komtera'); ?></h2>
                <div class="field-group">
                    <div class="field-label"><?php echo __('Marka', 'komtera'); ?></div>
                    <div class="field-value <?php echo empty($firsat_data['MARKA']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['MARKA'] ?? __('Belirtilmemiş', 'komtera')); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label"><?php echo __('Olasılık', 'komtera'); ?></div>
                    <div class="field-value <?php echo empty($firsat_data['OLASILIK']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['OLASILIK'] ?? __('Belirtilmemiş', 'komtera')); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label"><?php echo __('Geliş Kanalı', 'komtera'); ?></div>
                    <div class="field-value <?php echo empty($firsat_data['GELIS_KANALI']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['GELIS_KANALI'] ?? __('Belirtilmemiş', 'komtera')); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label"><?php echo __('Para Birimi', 'komtera'); ?></div>
                    <div class="field-value <?php echo empty($firsat_data['PARA_BIRIMI']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['PARA_BIRIMI'] ?? __('Belirtilmemiş', 'komtera')); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label">Register</div>
                    <div class="field-value">
                        <?php echo ($firsat_data['REGISTER'] == '1') ? '✓ ' . __('Evet', 'komtera') : '✗ ' . __('Hayır', 'komtera'); ?>
                    </div>
                </div>
            </div>

            <!-- Bayi Bilgileri -->
            <div class="card">
                <h2><?php echo __('Bayi Bilgileri', 'komtera'); ?></h2>
                <div class="field-group">
                    <div class="field-label"><?php echo __('Bayi Adı', 'komtera'); ?></div>
                    <div class="field-value <?php echo empty($firsat_data['BAYI_ADI']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['BAYI_ADI'] ?? __('Belirtilmemiş', 'komtera')); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label"><?php echo __('Bayi Kodu', 'komtera'); ?></div>
                    <div class="field-value <?php echo empty($firsat_data['BAYI_CHKODU']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['BAYI_CHKODU'] ?? __('Belirtilmemiş', 'komtera')); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label"><?php echo __('Bayi Yetkili', 'komtera'); ?></div>
                    <div class="field-value <?php echo empty($firsat_data['BAYI_YETKILI_ISIM']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['BAYI_YETKILI_ISIM'] ?? __('Belirtilmemiş', 'komtera')); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label"><?php echo __('Telefon', 'komtera'); ?></div>
                    <div class="field-value <?php echo empty($firsat_data['BAYI_YETKILI_TEL']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['BAYI_YETKILI_TEL'] ?? __('Belirtilmemiş', 'komtera')); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label"><?php echo __('E-posta', 'komtera'); ?></div>
                    <div class="field-value <?php echo empty($firsat_data['BAYI_YETKILI_EPOSTA']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['BAYI_YETKILI_EPOSTA'] ?? __('Belirtilmemiş', 'komtera')); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label"><?php echo __('Adres', 'komtera'); ?></div>
                    <div class="field-value <?php echo empty($firsat_data['BAYI_ADRES']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['BAYI_ADRES'] ?? __('Belirtilmemiş', 'komtera')); ?>
                    </div>
                </div>
            </div>

            <!-- Müşteri Bilgileri -->
            <div class="card">
                <h2><?php echo __('Müşteri Bilgileri', 'komtera'); ?></h2>
                <div class="field-group">
                    <div class="field-label"><?php echo __('Müşteri Adı', 'komtera'); ?></div>
                    <div class="field-value <?php echo empty($firsat_data['MUSTERI_ADI']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['MUSTERI_ADI'] ?? __('Belirtilmemiş', 'komtera')); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label"><?php echo __('Müşteri Yetkili', 'komtera'); ?></div>
                    <div class="field-value <?php echo empty($firsat_data['MUSTERI_YETKILI_ISIM']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['MUSTERI_YETKILI_ISIM'] ?? __('Belirtilmemiş', 'komtera')); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label"><?php echo __('Telefon', 'komtera'); ?></div>
                    <div class="field-value <?php echo empty($firsat_data['MUSTERI_YETKILI_TEL']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['MUSTERI_YETKILI_TEL'] ?? __('Belirtilmemiş', 'komtera')); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label"><?php echo __('E-posta', 'komtera'); ?></div>
                    <div class="field-value <?php echo empty($firsat_data['MUSTERI_YETKILI_EPOSTA']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['MUSTERI_YETKILI_EPOSTA'] ?? __('Belirtilmemiş', 'komtera')); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label"><?php echo __('Müşteri Temsilcisi', 'komtera'); ?></div>
                    <div class="field-value <?php echo empty($firsat_data['MUSTERI_TEMSILCISI']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['MUSTERI_TEMSILCISI'] ?? __('Belirtilmemiş', 'komtera')); ?>
                    </div>
                </div>
            </div>

            <!-- Tarihler ve Yönetim -->
            <div class="card">
                <h2><?php echo __('Tarihler & Yönetim', 'komtera'); ?></h2>
                <div class="field-group">
                    <div class="field-label"><?php echo __('Başlangıç Tarihi', 'komtera'); ?></div>
                    <div class="field-value <?php echo empty($firsat_data['BASLANGIC_TARIHI']) ? 'empty' : ''; ?>">
                        <?php
                        if ($firsat_data['BASLANGIC_TARIHI']) {
                            $date = new DateTime($firsat_data['BASLANGIC_TARIHI']);
                            echo $date->format('d.m.Y');
                        } else {
                            echo __('Belirtilmemiş', 'komtera');
                        }
                        ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label"><?php echo __('Bitiş Tarihi', 'komtera'); ?></div>
                    <div class="field-value <?php echo empty($firsat_data['BITIS_TARIHI']) ? 'empty' : ''; ?>">
                        <?php
                        if ($firsat_data['BITIS_TARIHI']) {
                            $date = new DateTime($firsat_data['BITIS_TARIHI']);
                            echo $date->format('d.m.Y');
                        } else {
                            echo __('Belirtilmemiş', 'komtera');
                        }
                        ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label"><?php echo __('Kaydı Açan', 'komtera'); ?></div>
                    <div class="field-value <?php echo empty($firsat_data['KAYIDI_ACAN']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['KAYIDI_ACAN'] ?? __('Belirtilmemiş', 'komtera')); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label"><?php echo __('Marka Manager', 'komtera'); ?></div>
                    <div class="field-value <?php echo empty($firsat_data['MARKA_MANAGER']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['MARKA_MANAGER'] ?? __('Belirtilmemiş', 'komtera')); ?>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-label"><?php echo __('Etkinlik', 'komtera'); ?></div>
                    <div class="field-value <?php echo empty($firsat_data['ETKINLIK']) ? 'empty' : ''; ?>">
                        <?php echo htmlspecialchars($firsat_data['ETKINLIK'] ?? __('Belirtilmemiş', 'komtera')); ?>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Teklif açma fonksiyonu - teklif detay sayfasını aç
        function TeklifAc(teklifNo) {
            window.location.href = 'admin.php?page=teklifler_detay&teklif_no=' + encodeURIComponent(teklifNo);
        }

        // Teklif çoğaltma fonksiyonu (daha sonra yapılacak)
        function teklifCogalt(teklifNo) {
            alert('<?php echo __('Teklif çoğaltma işlemi daha sonra eklenecek', 'komtera'); ?>: ' + teklifNo);
            // TODO: Teklif çoğaltma işlemi
        }

        // Fırsat çoğaltma fonksiyonu
        function firsatCogalt() {
            if (confirm('<?php echo __('Fırsatı çoğaltmak istediğinizden emin misiniz?', 'komtera'); ?>\n\n<?php echo __('Fırsat No', 'komtera'); ?>: <?php echo htmlspecialchars($firsat_no); ?>')) {
                // XMLHttpRequest kullan
                var xhr = new XMLHttpRequest();
                xhr.open('GET', '<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/_service/firsat_cogalt.php?firsat_no=<?php echo urlencode($firsat_no); ?>', true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            try {
                                var data = JSON.parse(xhr.responseText);
                                if (data.success) {
                                    alert('<?php echo __('İşlem başarılı', 'komtera'); ?>: ' + data.yeni_firsat_no + ' <?php echo __('numaralı fırsat oluşturuldu', 'komtera'); ?>');
                                    // Yeni fırsata yönlendir
                                    window.location.href = 'admin.php?page=firsatlar_detay&firsat_no=' + data.yeni_firsat_no;
                                } else {
                                    alert('<?php echo __('Hata', 'komtera'); ?>: ' + data.error);
                                }
                            } catch (e) {
                                alert('<?php echo __('JSON parse hatası', 'komtera'); ?>');
                            }
                        } else {
                            alert('<?php echo __('Bağlantı hatası oluştu', 'komtera'); ?>');
                        }
                    }
                };
                xhr.send();
            }
        }

        // Ana teklif yapma fonksiyonu
        function anaTeklifYap(teklifNo) {
            if (confirm('<?php echo __('Bu teklifi ana teklif olarak ayarlamak istediğinizden emin misiniz?', 'komtera'); ?>\n\n<?php echo __('Teklif No', 'komtera'); ?>: ' + teklifNo)) {
                // XMLHttpRequest kullan (jQuery yerine)
                var xhr = new XMLHttpRequest();
                xhr.open('GET', '<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/_service/ana_teklif_yap.php?teklif_no=' + teklifNo, true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            try {
                                var data = JSON.parse(xhr.responseText);
                                if (data.success) {
                                    alert('<?php echo __('İşlem başarılı', 'komtera'); ?>: ' + teklifNo + ' <?php echo __('ana teklif olarak ayarlandı', 'komtera'); ?>');
                                    location.reload();
                                } else {
                                    alert('<?php echo __('Hata', 'komtera'); ?>: ' + data.error);
                                }
                            } catch (e) {
                                alert('<?php echo __('JSON parse hatası', 'komtera'); ?>');
                            }
                        } else {
                            alert('<?php echo __('Bağlantı hatası oluştu', 'komtera'); ?>');
                        }
                    }
                };
                xhr.send();
            }
        }

        // PDF indirme fonksiyonu
        function teklifPDF(teklifNo) {
            alert('<?php echo __('PDF indiriliyor', 'komtera'); ?>: ' + teklifNo);
            // TODO: PDF indirme işlemi
            // window.open('pdf_endpoint.php?teklif_no=' + teklifNo, '_blank');
        }

        // Tüm checkbox'ları seç/kaldır
        function toggleAllCheckboxes(selectAllCheckbox) {
            const checkboxes = document.querySelectorAll('.teklif-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateAlternatifButton();
        }

        // Alternatif teklif butonunun durumunu güncelle
        function updateAlternatifButton() {
            const checkedBoxes = document.querySelectorAll('.teklif-checkbox:checked');
            const alternatifBtn = document.getElementById('alternatifTeklifBtn');

            // Eğer buton yoksa (henüz DOM'a eklenmemişse) çık
            if (!alternatifBtn) {
                return;
            }

            if (checkedBoxes.length > 1) {  // Birden fazla seçim yapıldığında aktif
                alternatifBtn.classList.add('active');
                alternatifBtn.innerHTML = '<span class="dashicons dashicons-admin-tools" style="margin-right: 5px; font-size: 16px; line-height: 1;"></span>' +
                                        '<?php echo __('Seçilileri Alternatifli Teklif Yap', 'komtera'); ?> (' + checkedBoxes.length + ')';
            } else {
                alternatifBtn.classList.remove('active');
                alternatifBtn.innerHTML = '<span class="dashicons dashicons-admin-tools" style="margin-right: 5px; font-size: 16px; line-height: 1;"></span>' +
                                        '<?php echo __('Seçilileri Alternatifli Teklif Yap', 'komtera'); ?>';
            }

            // Select all checkbox'ın durumunu güncelle
            const selectAllCheckbox = document.getElementById('selectAll');

            // Eğer checkbox yoksa çık
            if (!selectAllCheckbox) {
                return;
            }

            const allCheckboxes = document.querySelectorAll('.teklif-checkbox');
            const allChecked = allCheckboxes.length > 0 && Array.from(allCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(allCheckboxes).some(cb => cb.checked);

            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked && !allChecked;
        }

        // Alternatifli teklif yapma fonksiyonu
        function alternatifTeklifYap() {
            const checkedBoxes = document.querySelectorAll('.teklif-checkbox:checked');
            if (checkedBoxes.length < 2) {
                alert('<?php echo __('Alternatifli teklif için en az 2 teklif seçmeniz gerekir.', 'komtera'); ?>');
                return;
            }

            const teklifNolar = Array.from(checkedBoxes).map(cb => cb.value);

            if (confirm('<?php echo __('Seçili tekliflerle alternatifli teklif yapmak istediğinizden emin misiniz?', 'komtera'); ?>\n\n' +
                       '<?php echo __('Seçili Teklifler', 'komtera'); ?>: ' + teklifNolar.join(', '))) {
                alert('<?php echo __('Alternatifli teklif işlemi başlatıldı', 'komtera'); ?>: ' + teklifNolar.join(', '));
                // TODO: Alternatifli teklif yapma işlemi
            }
        }

        // Sayfa yüklendiğinde focus için
        document.addEventListener('DOMContentLoaded', function() {
            document.body.style.opacity = '1';
            updateAlternatifButton();
        });

        // Yeni Teklif Oluştur Fonksiyonu
        function yeniTeklifOlustur() {
            const firsat_no = '<?php echo htmlspecialchars($firsat_no); ?>';

            // Butonu loading state'e al
            const btn = document.querySelector('.yeni-teklif-btn');
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<span class="dashicons dashicons-update" style="margin-right: 8px; animation: spin 1s linear infinite;"></span><?php echo __('Teklif Oluşturuluyor...', 'komtera'); ?>';
            btn.disabled = true;

            // AJAX ile yeni teklif oluştur
            fetch('<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/_service/yeni_teklif_olustur.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `firsat_no=${encodeURIComponent(firsat_no)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Başarı mesajı göster
                    showMessage('<?php echo __('Yeni teklif başarıyla oluşturuldu!', 'komtera'); ?>', 'success');

                    // Teklif detay sayfasına yönlendir
                    setTimeout(() => {
                        window.open(`<?php echo admin_url('admin.php'); ?>?page=teklifler_detay&teklif_no=${data.teklif_no}`, '_blank');
                        // Mevcut sayfayı yenile (yeni teklif listede görünsün)
                        location.reload();
                    }, 1000);
                } else {
                    showMessage('<?php echo __('Teklif oluşturma hatası:', 'komtera'); ?> ' + (data.error || '<?php echo __('Bilinmeyen hata', 'komtera'); ?>'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('<?php echo __('Bağlantı hatası:', 'komtera'); ?> ' + error.message, 'error');
            })
            .finally(() => {
                // Butonu eski haline döndür
                btn.innerHTML = originalContent;
                btn.disabled = false;
            });
        }

        // Spin animasyonu için CSS
        const style = document.createElement('style');
        style.textContent = `
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);

        // Mesaj gösterme fonksiyonu
        function showMessage(message, type) {
            const messageEl = document.createElement('div');
            messageEl.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 16px 24px;
                background: ${type === 'success' ? '#d4edda' : '#f8d7da'};
                color: ${type === 'success' ? '#155724' : '#721c24'};
                border: 1px solid ${type === 'success' ? '#c3e6cb' : '#f5c6cb'};
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 10000;
                font-size: 14px;
                font-weight: 500;
                max-width: 400px;
                word-wrap: break-word;
            `;
            messageEl.textContent = message;

            // Close button ekle
            const closeBtn = document.createElement('span');
            closeBtn.innerHTML = '×';
            closeBtn.style.cssText = `
                float: right;
                margin-left: 15px;
                cursor: pointer;
                font-size: 18px;
                opacity: 0.8;
            `;
            closeBtn.onclick = () => messageEl.remove();
            messageEl.appendChild(closeBtn);

            document.body.appendChild(messageEl);

            // 5 saniye sonra otomatik kapat
            setTimeout(() => {
                if (messageEl.parentNode) {
                    messageEl.remove();
                }
            }, 5000);
        }
    </script>
</body>
</html>