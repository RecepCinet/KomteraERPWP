<?php
// Bu dosya WordPress context'inde include ediliyor, authentication ve wp-load gerekmez

// Database connection
include dirname(__DIR__) . '/_conn.php';

// Get teklif_no parameter
$teklif_no = $_GET['teklif_no'] ?? '';

if (empty($teklif_no)) {
    echo '<div style="padding: 40px; text-align: center; color: #d32f2f;">' . __('Teklif numarası belirtilmemiş.', 'komtera') . '</div>';
    exit;
}

// Fetch teklif details with related firsat data
$teklif_data = null;
$firsat_data = null;
try {
    // First get teklif data
    $sql = "SELECT TOP 1 * FROM " . getTableName('aa_erp_kt_teklifler') . "
            WHERE TEKLIF_NO = :teklif_no AND (SIL IS NULL OR SIL <> '1')";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':teklif_no', $teklif_no);
    $stmt->execute();
    $teklif_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$teklif_data) {
        echo '<div style="padding: 40px; text-align: center; color: #d32f2f;">' . __('Teklif bulunamadı', 'komtera') . ': ' . htmlspecialchars($teklif_no) . '</div>';
        exit;
    }

    // Get related firsat data if X_FIRSAT_NO exists
    if (!empty($teklif_data['X_FIRSAT_NO'])) {
        $firsat_sql = "SELECT TOP 1 * FROM " . getTableName('aa_erp_kt_firsatlar') . "
                       WHERE FIRSAT_NO = :firsat_no AND (SIL IS NULL OR SIL <> '1')";

        $firsat_stmt = $conn->prepare($firsat_sql);
        $firsat_stmt->bindParam(':firsat_no', $teklif_data['X_FIRSAT_NO']);
        $firsat_stmt->execute();
        $firsat_data = $firsat_stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get customer risk/credit information from LOGO
    $risk_data = null;
    $bayi_chkodu = $firsat_data['BAYI_CHKODU'] ?? $teklif_data['BAYI_CHKODU'] ?? null;

    if (!empty($bayi_chkodu)) {
        try {
            // Query LOGO database for customer credit information
            // LOGO typically stores this in LG_XXX_CLCARD view where XXX is company number
            // Trying common company numbers: 001, 002, 003
            $risk_sql = "SELECT TOP 1
                            CODE as CARI_KOD,
                            DEFINITION_ as CARI_ADI,
                            CREDITLIMIT as RISK_LIMITI,
                            (SELECT SUM(DEBIT - CREDIT)
                             FROM LKS.dbo.LG_001_01_CLFLINE
                             WHERE CLIENTREF = CLCARD.LOGICALREF) as BAKIYE
                        FROM LKS.dbo.LG_001_CLCARD CLCARD
                        WHERE CODE = :bayi_chkodu";

            $risk_stmt = $conn->prepare($risk_sql);
            $risk_stmt->bindParam(':bayi_chkodu', $bayi_chkodu);
            $risk_stmt->execute();
            $risk_data = $risk_stmt->fetch(PDO::FETCH_ASSOC);

            // If not found in 001, try 002
            if (!$risk_data) {
                $risk_sql = "SELECT TOP 1
                                CODE as CARI_KOD,
                                DEFINITION_ as CARI_ADI,
                                CREDITLIMIT as RISK_LIMITI,
                                (SELECT SUM(DEBIT - CREDIT)
                                 FROM LKS.dbo.LG_002_01_CLFLINE
                                 WHERE CLIENTREF = CLCARD.LOGICALREF) as BAKIYE
                            FROM LKS.dbo.LG_002_CLCARD CLCARD
                            WHERE CODE = :bayi_chkodu";

                $risk_stmt = $conn->prepare($risk_sql);
                $risk_stmt->bindParam(':bayi_chkodu', $bayi_chkodu);
                $risk_stmt->execute();
                $risk_data = $risk_stmt->fetch(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            // Log error but don't break the page
            error_log("Risk limit query error: " . $e->getMessage());
            $risk_data = null;
        }
    }
} catch (Exception $e) {
    echo '<div style="padding: 40px; text-align: center; color: #d32f2f;">' . __('Veri çekme hatası', 'komtera') . ': ' . htmlspecialchars($e->getMessage()) . '</div>';
    exit;
}

// Fetch teklif ürünleri
$teklif_urunler = [];
$urun_error = '';
try {
    $urun_sql = "SELECT * FROM " . getTableName('aa_erp_kt_teklifler_urunler') . "
                 WHERE X_TEKLIF_NO = :teklif_no
                 ORDER BY id DESC";

    $urun_stmt = $conn->prepare($urun_sql);
    $urun_stmt->bindParam(':teklif_no', $teklif_no);
    $urun_stmt->execute();
    $teklif_urunler = $urun_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $urun_error = $e->getMessage();
}

?><!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('Teklif Detay', 'komtera'); ?> - <?php echo htmlspecialchars($teklif_no); ?></title>
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
            width: 100%;
            margin: 0;
            padding: 20px;
        }

        /* Header with Toolbar */
        .header {
            background: #fff;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid #007cba;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .header-info {
            flex: 1;
            min-width: 300px;
        }

        .header h1 {
            color: #007cba;
            font-size: 28px;
            margin-bottom: 8px;
        }

        .header .subtitle {
            color: #666;
            font-size: 16px;
            margin-bottom: 4px;
        }

        .header .company-info {
            color: #333;
            font-size: 18px;
            font-weight: 600;
            margin-top: 8px;
        }

        /* Toolbar Buttons */
        .toolbar {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
            align-items: flex-start;
        }

        .toolbar-group {
            display: flex;
            gap: 4px;
            background: #f8f9fa;
            padding: 4px;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
        }

        .toolbar-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            background: #fff;
            color: #333;
            text-decoration: none;
            font-size: 10px;
            font-weight: 500;
            line-height: 1.2;
            padding: 6px 4px;
            text-align: center;
        }

        .toolbar-btn:hover {
            background: #e3f2fd;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 124, 186, 0.2);
        }

        /* File Count Badge Styles */
        .file-count-btn, .license-count-btn {
            position: relative;
        }

        .file-count-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #d63384;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 11px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            z-index: 10;
            min-width: 20px;
            padding: 0 2px;
        }

        .file-count-badge.has-files {
            display: flex !important;
        }

        .file-count-badge.many-files {
            background: #dc3545;
            animation: pulse-badge 2s infinite;
        }

        @keyframes pulse-badge {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .toolbar-btn.active {
            background: #007cba;
            color: white;
        }

        .toolbar-btn .icon {
            font-size: 20px;
            margin-bottom: 4px;
            display: block;
            width: 20px;
            height: 20px;
        }

        .toolbar-btn .icon.dashicons {
            font-family: 'dashicons';
            line-height: 1;
        }

        /* Specific button colors */
        .btn-security { background: #2d2d2d; color: white; }
        .btn-security:hover { background: #1a1a1a; }

        .btn-light { background: #ffc107; color: #333; }
        .btn-light:hover { background: #ffb300; }

        .btn-pdf { background: #dc3545; color: white; }
        .btn-pdf:hover { background: #c82333; }

        .btn-barcode { background: #6c757d; color: white; }
        .btn-barcode:hover { background: #5a6268; }

        .btn-handshake { background: #28a745; color: white; }
        .btn-handshake:hover { background: #218838; }

        .btn-add { background: #17a2b8; color: white; }
        .btn-add:hover { background: #138496; }

        /* Status badges */
        .status-info {
            display: flex;
            gap: 12px;
            margin-top: 12px;
            flex-wrap: wrap;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            gap: 4px;
        }

        .status-platinum { background: #e3f2fd; color: #1976d2; }
        .risk-limit { background: #fff3e0; color: #f57c00; }

        /* Risk Limit Box */
        .risk-box {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            border: 2px solid #007cba;
            border-radius: 8px;
            padding: 12px;
            min-width: 200px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 124, 186, 0.1);
            transition: all 0.3s ease;
        }

        .risk-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 124, 186, 0.15);
        }

        .risk-title {
            font-size: 12px;
            font-weight: 600;
            color: #007cba;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .risk-limit-value {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 2px;
        }

        .risk-current-value {
            font-size: 14px;
            color: #d32f2f;
            font-weight: 600;
        }

        /* Grid Container */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }

        /* Product Grid Container */
        .product-section {
            background: #fff;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: 1px solid #e0e0e0;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #007cba;
        }

        .section-title {
            color: #007cba;
            font-size: 20px;
            font-weight: 600;
        }

        /* Iframe for product grid */
        .product-iframe {
            width: 100%;
            height: 600px;
            min-height: 400px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            background: #fff;
        }

        /* Company info card */
        .company-card {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 16px;
            margin-top: 16px;
            border-left: 4px solid #007cba;
        }

        .company-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin-top: 12px;
        }

        .company-field {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .field-label {
            font-size: 12px;
            font-weight: 500;
            color: #666;
            text-transform: uppercase;
        }

        .field-value {
            font-size: 14px;
            color: #333;
            font-weight: 500;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .container {
                padding: 12px;
            }

            .header-top {
                flex-direction: column;
                align-items: stretch;
            }

            .toolbar {
                justify-content: center;
                gap: 6px;
            }

            .toolbar-group {
                flex-wrap: wrap;
                justify-content: center;
            }

            .toolbar-btn {
                width: 50px;
                height: 50px;
                font-size: 9px;
            }

            .toolbar-btn .icon {
                font-size: 16px;
                margin-bottom: 2px;
                width: 16px;
                height: 16px;
            }

            .header h1 {
                font-size: 24px;
            }

            .company-details {
                grid-template-columns: 1fr;
            }

            .risk-box {
                min-width: unset;
            }

            .product-iframe {
                height: 250px;
            }
        }

        @media (max-width: 480px) {
            .toolbar-btn {
                width: 45px;
                height: 45px;
                font-size: 8px;
                padding: 4px 2px;
            }

            .toolbar-btn .icon {
                font-size: 14px;
                width: 14px;
                height: 14px;
            }
        }
        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 10000;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.show {
            display: flex !important;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            transform: scale(0.9);
            transition: transform 0.3s ease;
        }

        .modal-overlay.show .modal-content {
            transform: scale(1);
        }

        .modal-header {
            background: linear-gradient(135deg, #007cba 0%, #005a87 100%);
            color: white;
            padding: 20px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background 0.2s;
        }

        .modal-close:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .modal-body {
            padding: 24px;
            max-height: calc(90vh - 140px);
            overflow-y: auto;
        }

        /* File Upload Area */
        .upload-area {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 40px 20px;
            text-align: center;
            background: #fafafa;
            transition: all 0.3s ease;
            margin-bottom: 24px;
            cursor: pointer;
        }

        .upload-area:hover,
        .upload-area.dragover {
            border-color: #007cba;
            background: #f0f8ff;
        }

        .upload-icon {
            font-size: 48px;
            color: #ccc;
            margin-bottom: 16px;
        }

        .upload-area:hover .upload-icon,
        .upload-area.dragover .upload-icon {
            color: #007cba;
        }

        .upload-text {
            font-size: 16px;
            color: #666;
            margin-bottom: 8px;
        }

        .upload-subtitle {
            font-size: 14px;
            color: #999;
        }

        .hidden-input {
            display: none;
        }

        /* File List */
        .file-list {
            margin-top: 24px;
        }

        .file-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: all 0.2s ease;
        }

        .file-item:hover {
            background: #e3f2fd;
            border-color: #007cba;
        }

        .file-icon {
            width: 40px;
            height: 40px;
            background: #007cba;
            color: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 16px;
        }

        .file-info {
            flex: 1;
            min-width: 0;
        }

        .file-name {
            font-weight: 500;
            color: #333;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .file-details {
            font-size: 12px;
            color: #666;
        }

        .file-actions {
            display: flex;
            gap: 8px;
        }

        .file-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .file-btn.view {
            background: #007cba;
            color: white;
        }

        .file-btn.view:hover {
            background: #005a87;
        }

        .file-btn.delete {
            background: #dc3545;
            color: white;
        }

        .file-btn.delete:hover {
            background: #c82333;
        }

        /* Progress Bar */
        .upload-progress {
            width: 100%;
            height: 8px;
            background: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 8px;
            display: none;
        }

        .upload-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #007cba 0%, #00a2d8 100%);
            width: 0%;
            transition: width 0.3s ease;
        }

        /* Empty State */
        .empty-files {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }

        .empty-files .dashicons {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header with Toolbar -->
        <div class="header">
            <div class="header-top">
                <div class="header-info">
                    <h1>
                        <?php echo htmlspecialchars($teklif_data['TEKLIF_NO']); ?>
                        <?php if ($teklif_data['TEKLIF_TIPI'] == '1'): ?>
                            <span class="status-badge" style="background: #f0ad4e; color: white; margin-left: 8px;">
                                ⭐ ANA TEKLİF
                            </span>
                        <?php endif; ?>
                        <?php if ($teklif_data['KILIT'] == '1'): ?>
                            <span class="status-badge" style="background: #d9534f; color: white; margin-left: 8px;">
                                🔒 KİLİTLİ
                            </span>
                        <?php endif; ?>
                    </h1>

                    <div class="subtitle">
                        <?php echo __('Satış Tipi', 'komtera'); ?>:
                        <?php
                        $satis_tipi_text = '';
                        switch($teklif_data['SATIS_TIPI']) {
                            case '0': $satis_tipi_text = __('İlk Satış', 'komtera'); break;
                            case '1': $satis_tipi_text = __('Yenileme', 'komtera'); break;
                            default: $satis_tipi_text = __('Belirtilmemiş', 'komtera');
                        }
                        echo $satis_tipi_text;
                        ?>
                        | <?php echo __('Marka', 'komtera'); ?>: <?php echo htmlspecialchars($firsat_data['MARKA'] ?? $teklif_data['MARKA'] ?? __('Belirtilmemiş', 'komtera')); ?>
                        | <?php echo __('Oluşturma', 'komtera'); ?>:
                        <?php
                        if ($teklif_data['YARATILIS_TARIHI']) {
                            $tarih = new DateTime($teklif_data['YARATILIS_TARIHI']);
                            echo $tarih->format('d.m.Y');
                            if ($teklif_data['YARATILIS_SAATI']) {
                                // Saat formatını HH:MM olarak göster (saniyesiz)
                                $saat = $teklif_data['YARATILIS_SAATI'];
                                // Eğer saat 09:17:29.2033333 formatındaysa, sadece HH:MM al
                                if (strlen($saat) > 5) {
                                    $saat = substr($saat, 0, 5); // İlk 5 karakter: HH:MM
                                }
                                echo ' ' . $saat;
                            }
                        }
                        ?>
                    </div>

                    <div class="company-info">
                        <?php
                        $bayi_adi = $firsat_data['BAYI_ADI'] ?? $teklif_data['BAYI_ADI'] ?? __('Bayi Adı Belirtilmemiş', 'komtera');
                        echo htmlspecialchars($bayi_adi);
                        ?>
                    </div>

                    <div class="status-info">
                        <?php
                        // Bayi seviyesi göster (fırsattan al)
                        $bayi_seviye = $firsat_data['MARKA_BAYI_SEVIYE'] ?? '';
                        if (!empty($bayi_seviye)) {
                            echo '<span class="status-badge status-platinum">';
                            echo '<span>🏆</span> ' . htmlspecialchars(strtoupper($bayi_seviye));
                            echo '</span>';
                        }
                        ?>
                        <?php
                        // Risk limit bilgisi göster - LOGO'dan çekilen verilerle
                        if ($risk_data && isset($risk_data['RISK_LIMITI'])):
                            $risk_limit = floatval($risk_data['RISK_LIMITI'] ?? 0);
                            $bakiye = floatval($risk_data['BAKIYE'] ?? 0);
                            $kalan = $risk_limit - $bakiye;

                            // Renk belirle
                            $color_class = '';
                            if ($kalan < 0) {
                                $color_class = 'style="border-color: #d32f2f;"'; // Kırmızı - limit aşıldı
                            } else if ($kalan < $risk_limit * 0.2) {
                                $color_class = 'style="border-color: #f57c00;"'; // Turuncu - %80'i doldu
                            }
                        ?>
                        <div class="risk-box" <?php echo $color_class; ?>>
                            <div class="risk-title"><?php echo __('Risk Limit', 'komtera'); ?></div>
                            <div class="risk-limit-value">
                                <?php echo number_format($risk_limit, 2, ',', '.'); ?> ₺
                            </div>
                            <div class="risk-current-value">
                                <?php echo __('Bakiye', 'komtera'); ?>: <?php echo number_format($bakiye, 2, ',', '.'); ?> ₺
                            </div>
                            <div style="font-size: 12px; color: <?php echo $kalan < 0 ? '#d32f2f' : '#666'; ?>; margin-top: 4px;">
                                <?php echo __('Kalan', 'komtera'); ?>: <?php echo number_format($kalan, 2, ',', '.'); ?> ₺
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Toolbar Buttons -->
                <div class="toolbar">
                    <!-- Kilit Grubu - EN BAŞTA -->
                    <div class="toolbar-group">
                        <?php if ($teklif_data['KILIT'] == '1'): ?>
                            <!-- Teklif kilitli - Kilidi aç butonu göster -->
                            <button class="toolbar-btn btn-handshake" title="<?php echo __('Teklif Kilidini Aç', 'komtera'); ?>" onclick="teklifKilitle(0)">
                                <span class="icon dashicons dashicons-unlock"></span>
                                <span><?php echo __('Kilidi', 'komtera'); ?><br><?php echo __('Aç', 'komtera'); ?></span>
                            </button>
                        <?php else: ?>
                            <!-- Teklif açık - Kilitle butonu göster -->
                            <button class="toolbar-btn" style="background: #dc3545; color: white;" title="<?php echo __('Teklifi Kilitle', 'komtera'); ?>" onclick="teklifKilitle(1)">
                                <span class="icon dashicons dashicons-lock"></span>
                                <span><?php echo __('Teklifi', 'komtera'); ?><br><?php echo __('Kilitle', 'komtera'); ?></span>
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- Dosya Grubu -->
                    <div class="toolbar-group">
                        <button class="toolbar-btn btn-light file-count-btn" title="<?php echo __('Özel Fiyat Dosyası', 'komtera'); ?>" onclick="ozelFiyatDosyasi()">
                            <span class="icon dashicons dashicons-lightbulb"></span>
                            <span><?php echo __('Özel Fiyat', 'komtera'); ?><br><?php echo __('Dosyası', 'komtera'); ?></span>
                            <span id="fileCountBadge" class="file-count-badge" style="display: none;">0</span>
                        </button>

                        <button class="toolbar-btn btn-barcode license-count-btn" title="<?php echo __('Lisans Dosyası', 'komtera'); ?>" onclick="lisansDosyasi()">
                            <span class="icon dashicons dashicons-id-alt"></span>
                            <span><?php echo __('Lisans', 'komtera'); ?><br><?php echo __('Dosyası', 'komtera'); ?></span>
                            <span id="licenseCountBadge" class="file-count-badge" style="display: none;">0</span>
                        </button>
                    </div>

                    <!-- PDF Grubu -->
                    <div class="toolbar-group">
                        <button class="toolbar-btn btn-pdf" title="<?php echo __('PDF Teklif', 'komtera'); ?>" onclick="pdfTeklif()">
                            <span class="icon dashicons dashicons-media-document"></span>
                            <span><?php echo __('PDF Teklif', 'komtera'); ?></span>
                        </button>
                    </div>

                    <!-- İşlem Grubu -->
                    <div class="toolbar-group">
                        <button class="toolbar-btn btn-handshake" title="<?php echo __('Sipariş Getir', 'komtera'); ?>" onclick="siparisGetir()">
                            <span class="icon dashicons dashicons-cart"></span>
                            <span><?php echo __('Sipariş', 'komtera'); ?><br><?php echo __('Getir', 'komtera'); ?></span>
                        </button>

                        <button class="toolbar-btn btn-add" title="<?php echo __('Yeni Ekle', 'komtera'); ?>" onclick="yeniEkle()">
                            <span class="icon dashicons dashicons-plus-alt"></span>
                            <span><?php echo __('Yeni Ekle', 'komtera'); ?></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Company Details Card -->
            <div class="company-card">
                <div class="company-details">
                    <div class="company-field">
                        <div class="field-label"><?php echo __('Bayi', 'komtera'); ?></div>
                        <div class="field-value"><?php echo htmlspecialchars($firsat_data['BAYI_ADI'] ?? $teklif_data['BAYI_ADI'] ?? '-'); ?></div>
                    </div>
                    <div class="company-field">
                        <div class="field-label"><?php echo __('CH Kodu', 'komtera'); ?></div>
                        <div class="field-value"><?php echo htmlspecialchars($firsat_data['BAYI_CHKODU'] ?? $teklif_data['BAYI_CHKODU'] ?? '-'); ?></div>
                    </div>
                    <div class="company-field">
                        <div class="field-label"><?php echo __('Bayi Yetkili', 'komtera'); ?></div>
                        <div class="field-value"><?php echo htmlspecialchars($firsat_data['BAYI_YETKILI_ISIM'] ?? $teklif_data['BAYI_YETKILI_ISIM'] ?? '-'); ?></div>
                    </div>
                    <div class="company-field">
                        <div class="field-label"><?php echo __('Müşteri Kurulumu', 'komtera'); ?></div>
                        <div class="field-value"><?php echo htmlspecialchars($firsat_data['MUSTERI_ADI'] ?? $teklif_data['MUSTERI_ADI'] ?? '-'); ?></div>
                    </div>
                    <div class="company-field">
                        <div class="field-label"><?php echo __('Müşteri Yetkili', 'komtera'); ?></div>
                        <div class="field-value"><?php echo htmlspecialchars($firsat_data['MUSTERI_YETKILI_ISIM'] ?? $teklif_data['MUSTERI_YETKILI_ISIM'] ?? '-'); ?></div>
                    </div>
                    <div class="company-field">
                        <div class="field-label"><?php echo __('Partner Kurulumu', 'komtera'); ?></div>
                        <div class="field-value"><?php echo htmlspecialchars($firsat_data['PARTNER_ADI'] ?? $teklif_data['PARTNER_ADI'] ?? '-'); ?></div>
                    </div>
                    <?php if ($firsat_data): ?>
                    <div class="company-field">
                        <div class="field-label"><?php echo __('İlgili Fırsat', 'komtera'); ?></div>
                        <div class="field-value">
                            <a href="admin.php?page=firsatlar_detay&firsat_no=<?php echo urlencode($teklif_data['X_FIRSAT_NO']); ?>"
                               style="color: #007cba; text-decoration: none; font-weight: 500;">
                                <?php echo htmlspecialchars($teklif_data['X_FIRSAT_NO']); ?>
                            </a>
                        </div>
                    </div>
                    <div class="company-field">
                        <div class="field-label"><?php echo __('Proje Adı', 'komtera'); ?></div>
                        <div class="field-value"><?php echo htmlspecialchars($firsat_data['PROJE_ADI'] ?? '-'); ?></div>
                    </div>
                    <div class="company-field">
                        <div class="field-label"><?php echo __('Müşteri Tel', 'komtera'); ?></div>
                        <div class="field-value"><?php echo htmlspecialchars($firsat_data['MUSTERI_YETKILI_TEL'] ?? '-'); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="content-grid">
            <!-- Product Section with Grid -->
            <div class="product-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <?php echo __('Teklif Ürünleri', 'komtera'); ?>
                        <span style="font-size: 16px; color: #666; font-weight: normal; margin-left: 8px;">
                            (<?php echo count($teklif_urunler); ?> <?php echo __('ürün', 'komtera'); ?>)
                        </span>
                    </h2>

                    <!-- Grid Controls -->
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <select id="gridPageSize" onchange="updateGridPageSize()" style="padding: 6px 12px; border: 1px solid #ccc; border-radius: 4px;">
                            <option value="25">25</option>
                            <option value="50" selected>50</option>
                            <option value="100">100</option>
                            <option value="all"><?php echo __('Tümü', 'komtera'); ?></option>
                        </select>

                        <button onclick="refreshGrid()" style="padding: 6px 12px; background: #007cba; color: white; border: none; border-radius: 4px; cursor: pointer;">
                            <?php echo __('Yenile', 'komtera'); ?>
                        </button>
                    </div>
                </div>

                <?php if (!empty($urun_error)): ?>
                    <div style="background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 12px; border-radius: 4px; margin-bottom: 16px;">
                        <strong><?php echo __('Hata', 'komtera'); ?>:</strong> <?php echo htmlspecialchars($urun_error); ?>
                    </div>
                <?php endif; ?>

                <!-- Grid iFrame -->
                <iframe
                    id="productGrid"
                    class="product-iframe"
                    src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/erp/tablo_render.php?t=teklif_urunler&teklif_no=<?php echo urlencode($teklif_no); ?>"
                    frameborder="0"
                    title="<?php echo __('Teklif Ürünleri Grid', 'komtera'); ?>">
                </iframe>
            </div>

            <!-- Summary Section -->
            <div class="product-section">
                <div class="section-header">
                    <h2 class="section-title"><?php echo __('Teklif Özeti', 'komtera'); ?></h2>
                    <button onclick="location.reload()" style="padding: 8px 16px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 6px;" title="<?php echo __('Hesaplamaları Yenile', 'komtera'); ?>">
                        <span class="dashicons dashicons-update" style="font-size: 16px; width: 16px; height: 16px;"></span>
                        <?php echo __('Yenile', 'komtera'); ?>
                    </button>
                </div>

                <?php
                // Calculate teklif summary from database
                try {
                    $summary_sql = "SELECT
                        COUNT(*) as URUN_SAYISI,
                        SUM(B_LISTE_FIYATI * ADET) as TOPLAM_LISTE_FIYATI,
                        SUM(CASE WHEN B_MALIYET > 0 THEN B_MALIYET * ADET ELSE O_MALIYET * ADET END) as TOPLAM_MALIYET,
                        SUM(B_SATIS_FIYATI * ADET) as TOPLAM_SATIS_FIYATI,
                        SUM(CASE WHEN B_MALIYET > 0 THEN
                            ((B_SATIS_FIYATI - B_MALIYET) / NULLIF(B_SATIS_FIYATI, 0)) * 100 * (B_SATIS_FIYATI * ADET)
                        ELSE
                            ((B_SATIS_FIYATI - O_MALIYET) / NULLIF(B_SATIS_FIYATI, 0)) * 100 * (B_SATIS_FIYATI * ADET)
                        END) / NULLIF(SUM(B_SATIS_FIYATI * ADET), 0) as ORTALAMA_KARLILIK
                    FROM " . getTableName('aa_erp_kt_teklifler_urunler') . "
                    WHERE X_TEKLIF_NO = :teklif_no";

                    $summary_stmt = $conn->prepare($summary_sql);
                    $summary_stmt->bindParam(':teklif_no', $teklif_no);
                    $summary_stmt->execute();
                    $summary = $summary_stmt->fetch(PDO::FETCH_ASSOC);

                    $toplam_iskonto = 0;
                    if ($summary['TOPLAM_LISTE_FIYATI'] > 0) {
                        $toplam_iskonto = (($summary['TOPLAM_LISTE_FIYATI'] - $summary['TOPLAM_SATIS_FIYATI']) / $summary['TOPLAM_LISTE_FIYATI']) * 100;
                    }
                } catch (Exception $e) {
                    $summary = ['URUN_SAYISI' => 0, 'TOPLAM_LISTE_FIYATI' => 0, 'TOPLAM_MALIYET' => 0, 'TOPLAM_SATIS_FIYATI' => 0, 'ORTALAMA_KARLILIK' => 0];
                    $toplam_iskonto = 0;
                }
                ?>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                    <div style="text-align: right;">
                        <div style="font-size: 14px; color: #666; margin-bottom: 4px;"><?php echo __('Toplam Liste Fiyatı', 'komtera'); ?></div>
                        <div style="font-size: 18px; font-weight: bold; color: #666;">
                            <?php echo number_format($summary['TOPLAM_LISTE_FIYATI'] ?? 0, 2, ',', '.') . ' ₺'; ?>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 14px; color: #666; margin-bottom: 4px;"><?php echo __('Toplam İskonto', 'komtera'); ?></div>
                        <div style="font-size: 18px; font-weight: bold; color: #f57c00;">
                            %<?php echo number_format($toplam_iskonto, 2, ',', '.'); ?>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 14px; color: #666; margin-bottom: 4px;"><?php echo __('Net Satış Fiyatı', 'komtera'); ?></div>
                        <div style="font-size: 20px; font-weight: bold; color: #2e7d32;">
                            <?php echo number_format($summary['TOPLAM_SATIS_FIYATI'] ?? 0, 2, ',', '.') . ' ₺'; ?>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 14px; color: #666; margin-bottom: 4px;"><?php echo __('Ortalama Karlılık', 'komtera'); ?></div>
                        <div style="font-size: 20px; font-weight: bold; color: <?php echo ($summary['ORTALAMA_KARLILIK'] ?? 0) < 0 ? '#d32f2f' : '#007cba'; ?>;">
                            %<?php echo number_format($summary['ORTALAMA_KARLILIK'] ?? 0, 2, ',', '.'); ?>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #e0e0e0;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; text-align: center;">
                        <div>
                            <div style="font-size: 12px; color: #666; margin-bottom: 4px;"><?php echo __('Ürün Sayısı', 'komtera'); ?></div>
                            <div style="font-size: 16px; font-weight: bold;"><?php echo number_format($summary['URUN_SAYISI'] ?? 0); ?></div>
                        </div>
                        <div>
                            <div style="font-size: 12px; color: #666; margin-bottom: 4px;"><?php echo __('Toplam Maliyet', 'komtera'); ?></div>
                            <div style="font-size: 16px; font-weight: bold;"><?php echo number_format($summary['TOPLAM_MALIYET'] ?? 0, 2, ',', '.') . ' ₺'; ?></div>
                        </div>
                        <div>
                            <div style="font-size: 12px; color: #666; margin-bottom: 4px;"><?php echo __('Komisyon', 'komtera'); ?></div>
                            <div style="font-size: 16px; font-weight: bold;">
                                <?php
                                $komisyon = ($teklif_data['KOMISYON_F1'] ?? 0) + ($teklif_data['KOMISYON_F2'] ?? 0) + ($teklif_data['KOMISYON_F3'] ?? 0);
                                echo number_format($komisyon, 2, ',', '.') . ' ₺';
                                ?>
                            </div>
                        </div>
                        <div>
                            <div style="font-size: 12px; color: #666; margin-bottom: 4px;"><?php echo __('Geçerlilik', 'komtera'); ?></div>
                            <div style="font-size: 16px; font-weight: bold;">
                                <?php echo $teklif_data['TEKLIF_SURE'] ?? 30; ?> <?php echo __('gün', 'komtera'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Özel Fiyat Dosyası Modal -->
    <div id="ozelFiyatModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">
                    <span class="dashicons dashicons-media-document" style="margin-right: 8px;"></span>
                    <?php echo __('Özel Fiyat Dosyaları', 'komtera'); ?>
                    <span style="font-size: 14px; font-weight: normal; opacity: 0.8; margin-left: 8px;">
                        (<?php echo htmlspecialchars($teklif_no); ?>)
                    </span>
                </h2>
                <button class="modal-close" onclick="closeOzelFiyatModal()">×</button>
            </div>
            <div class="modal-body">
                <!-- Upload Area -->
                <div class="upload-area" onclick="triggerFileUpload()" ondrop="handleDrop(event)" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)">
                    <div class="upload-icon">📎</div>
                    <div class="upload-text"><?php echo __('Dosya yüklemek için tıklayın veya sürükleyip bırakın', 'komtera'); ?></div>
                    <div class="upload-subtitle"><?php echo __('PDF, PNG, JPEG formatları desteklenir (Max: 10MB)', 'komtera'); ?></div>
                    <div class="upload-progress">
                        <div class="upload-progress-bar"></div>
                    </div>
                </div>

                <input type="file" id="fileInput" class="hidden-input" multiple accept=".pdf,.png,.jpg,.jpeg" onchange="handleFileSelect(event)">

                <!-- File List -->
                <div class="file-list">
                    <div id="fileListContainer">
                        <!-- Files will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lisans Dosyası Modal -->
    <div id="lisansModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">
                    <span class="dashicons dashicons-id-alt" style="margin-right: 8px;"></span>
                    <?php echo __('Lisans Dosyaları', 'komtera'); ?>
                    <span style="font-size: 14px; font-weight: normal; opacity: 0.8; margin-left: 8px;">
                        (<?php echo htmlspecialchars($teklif_no); ?>)
                    </span>
                </h2>
                <button class="modal-close" onclick="closeLisansModal()">×</button>
            </div>
            <div class="modal-body">
                <!-- Upload Area -->
                <div class="upload-area" onclick="triggerLisansFileUpload()" ondrop="handleLisansDrop(event)" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)">
                    <div class="upload-icon">🔐</div>
                    <div class="upload-text"><?php echo __('Lisans dosyası yüklemek için tıklayın veya sürükleyip bırakın', 'komtera'); ?></div>
                    <div class="upload-subtitle"><?php echo __('PDF, PNG, JPEG formatları desteklenir (Max: 10MB)', 'komtera'); ?></div>
                    <div class="upload-progress" id="lisansUploadProgress">
                        <div class="upload-progress-bar" id="lisansProgressBar"></div>
                    </div>
                </div>

                <input type="file" id="lisansFileInput" class="hidden-input" multiple accept=".pdf,.png,.jpg,.jpeg" onchange="handleLisansFileSelect(event)">

                <!-- File List -->
                <div class="file-list">
                    <div id="lisansFileListContainer">
                        <!-- Files will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toolbar button functions
        function teklifGeriAl() {
            if (!confirm('<?php echo __('Teklifi geri almak istediğinizden emin misiniz?', 'komtera'); ?>')) {
                return;
            }

            // Show loading state
            const btn = event.target.closest('.toolbar-btn');
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<span class="icon dashicons dashicons-update"></span><span><?php echo __('İşlem', 'komtera'); ?><br><?php echo __('Yapılıyor', 'komtera'); ?></span>';
            btn.disabled = true;

            // Simulate API call
            setTimeout(() => {
                btn.innerHTML = originalContent;
                btn.disabled = false;
                alert('<?php echo __('Teklif başarıyla geri alındı.', 'komtera'); ?>');
                location.reload();
            }, 2000);
        }

        function ozelFiyatDosyasi() {
            // Show modal instead of opening new window
            showOzelFiyatModal();
        }

        function pdfTeklif() {
            // Show loading state
            const btn = event.target.closest('.toolbar-btn');
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<span class="icon dashicons dashicons-pdf"></span><span><?php echo __('PDF', 'komtera'); ?><br><?php echo __('Oluşturuluyor', 'komtera'); ?></span>';
            btn.disabled = true;

            // Generate PDF
            const pdfUrl = '<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/_service/teklif_pdf.php?teklif_no=<?php echo urlencode($teklif_no); ?>';

            fetch(pdfUrl)
                .then(response => {
                    if (response.ok) {
                        return response.blob();
                    }
                    throw new Error('PDF oluşturma hatası');
                })
                .then(blob => {
                    // Download PDF
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = '<?php echo htmlspecialchars($teklif_no); ?>_teklif.pdf';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                })
                .catch(error => {
                    alert('<?php echo __('PDF oluşturma hatası:', 'komtera'); ?> ' + error.message);
                })
                .finally(() => {
                    btn.innerHTML = originalContent;
                    btn.disabled = false;
                });
        }

        function lisansDosyasi() {
            console.log('Lisans Dosyası button clicked');
            // Show modal instead of opening new window
            showLisansModal();
        }

        /**
         * Unified function to lock/unlock teklif
         * @param {number} lockStatus - 0 = unlock, 1 = lock
         */
        function teklifKilitle(lockStatus) {
            // Determine action text
            const isLocking = lockStatus === 1;
            const actionText = isLocking ? '<?php echo __('kilitlemek', 'komtera'); ?>' : '<?php echo __('kilidini açmak', 'komtera'); ?>';
            const confirmMsg = isLocking
                ? '<?php echo __('Teklifi kilitlemek istediğinizden emin misiniz?', 'komtera'); ?>'
                : '<?php echo __('Teklif kilidini açmak istediğinizden emin misiniz?', 'komtera'); ?>';

            if (!confirm(confirmMsg)) {
                return;
            }

            // Show loading state
            const btn = event.target.closest('.toolbar-btn');
            const originalContent = btn.innerHTML;
            const loadingText = isLocking
                ? '<span class="icon dashicons dashicons-lock"></span><span><?php echo __('Kilitleniyor', 'komtera'); ?></span>'
                : '<span class="icon dashicons dashicons-unlock"></span><span><?php echo __('Açılıyor', 'komtera'); ?></span>';
            btn.innerHTML = loadingText;
            btn.disabled = true;

            // Determine service URL
            const serviceUrl = isLocking
                ? '<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/_service/teklif_lock.php'
                : '<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/_service/teklif_unlock.php';

            // AJAX call to lock/unlock teklif
            const xhr = new XMLHttpRequest();
            xhr.open('POST', serviceUrl, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        try {
                            const data = JSON.parse(xhr.responseText);
                            if (data.success) {
                                // First reload the iframe to refresh grid with new editable state
                                const iframe = document.getElementById('productGrid');
                                const iframeSrc = iframe.src;
                                iframe.src = iframeSrc.split('?')[0] + '?teklif_no=<?php echo urlencode($teklif_no); ?>&refresh=' + new Date().getTime();

                                // Then reload the page after a short delay to show the new lock state
                                setTimeout(function() {
                                    location.reload();
                                }, 500);
                            } else {
                                alert('<?php echo __('Hata:', 'komtera'); ?> ' + data.error);
                                btn.innerHTML = originalContent;
                                btn.disabled = false;
                            }
                        } catch (e) {
                            alert('<?php echo __('İşlem sırasında bir hata oluştu.', 'komtera'); ?>');
                            btn.innerHTML = originalContent;
                            btn.disabled = false;
                        }
                    } else {
                        alert('<?php echo __('Bağlantı hatası oluştu.', 'komtera'); ?>');
                        btn.innerHTML = originalContent;
                        btn.disabled = false;
                    }
                }
            };
            xhr.send('teklif_no=<?php echo urlencode($teklif_no); ?>');
        }

        function siparisGetir() {
            const url = 'admin.php?page=siparisler&action=create_from_teklif&teklif_no=<?php echo urlencode($teklif_no); ?>';
            if (confirm('<?php echo __('Bu tekliften sipariş oluşturmak istediğinizden emin misiniz?', 'komtera'); ?>')) {
                window.location.href = url;
            }
        }

        function yeniEkle() {
            // Show dropdown menu for new items
            showNewItemMenu(event);
        }

        function showNewItemMenu(event) {
            const rect = event.target.closest('.toolbar-btn').getBoundingClientRect();

            // Create dropdown menu
            const menu = document.createElement('div');
            menu.className = 'new-item-menu';
            menu.style.cssText = `
                position: fixed;
                top: ${rect.bottom + 5}px;
                left: ${rect.left}px;
                background: white;
                border: 1px solid #ccc;
                border-radius: 4px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 1000;
                min-width: 150px;
            `;

            menu.innerHTML = `
                <div style="padding: 8px 0;">
                    <a href="#" onclick="yeniUrunEkle(); hideNewItemMenu();" style="display: block; padding: 8px 16px; text-decoration: none; color: #333; font-size: 14px;"><?php echo __('Yeni Ürün', 'komtera'); ?></a>
                    <a href="#" onclick="kategoriEkle(); hideNewItemMenu();" style="display: block; padding: 8px 16px; text-decoration: none; color: #333; font-size: 14px;"><?php echo __('Kategori', 'komtera'); ?></a>
                    <a href="#" onclick="notEkle(); hideNewItemMenu();" style="display: block; padding: 8px 16px; text-decoration: none; color: #333; font-size: 14px;"><?php echo __('Not Ekle', 'komtera'); ?></a>
                </div>
            `;

            document.body.appendChild(menu);

            // Hide menu when clicking outside
            setTimeout(() => {
                document.addEventListener('click', function hideMenu(e) {
                    if (!menu.contains(e.target)) {
                        document.body.removeChild(menu);
                        document.removeEventListener('click', hideMenu);
                    }
                });
            }, 100);
        }

        function hideNewItemMenu() {
            const menu = document.querySelector('.new-item-menu');
            if (menu) {
                document.body.removeChild(menu);
            }
        }

        function yeniUrunEkle() {
            const url = 'admin.php?page=urun_ekle&teklif_no=<?php echo urlencode($teklif_no); ?>';
            window.location.href = url;
        }

        function kategoriEkle() {
            const kategori = prompt('<?php echo __('Kategori adı:', 'komtera'); ?>');
            if (kategori) {
                alert('<?php echo __('Kategori eklendi:', 'komtera'); ?> ' + kategori);
                // TODO: Add category to teklif
            }
        }

        function notEkle() {
            const not = prompt('<?php echo __('Not:', 'komtera'); ?>');
            if (not) {
                alert('<?php echo __('Not eklendi:', 'komtera'); ?> ' + not);
                // TODO: Add note to teklif
            }
        }

        // Modal Functions
        function showOzelFiyatModal() {
            const modal = document.getElementById('ozelFiyatModal');
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.classList.add('show');
            }, 10);

            // Load existing files
            loadExistingFiles();
        }

        function closeOzelFiyatModal() {
            const modal = document.getElementById('ozelFiyatModal');
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }

        // File Upload Functions
        function triggerFileUpload() {
            document.getElementById('fileInput').click();
        }

        function handleFileSelect(event) {
            const files = event.target.files;
            uploadFiles(files);
        }

        function handleDragOver(event) {
            event.preventDefault();
            event.currentTarget.classList.add('dragover');
        }

        function handleDragLeave(event) {
            event.currentTarget.classList.remove('dragover');
        }

        function handleDrop(event) {
            event.preventDefault();
            event.currentTarget.classList.remove('dragover');
            const files = event.dataTransfer.files;
            uploadFiles(files);
        }

        function uploadFiles(files) {
            if (!files || files.length === 0) return;

            // Validate files
            for (let file of files) {
                if (!isValidFile(file)) {
                    alert(`<?php echo __('Geçersiz dosya:', 'komtera'); ?> ${file.name}`);
                    return;
                }
            }

            // Show progress bar
            const progressContainer = document.querySelector('.upload-progress');
            const progressBar = document.querySelector('.upload-progress-bar');
            progressContainer.style.display = 'block';
            progressBar.style.width = '0%';

            // Upload each file
            Array.from(files).forEach((file, index) => {
                uploadSingleFile(file, index, files.length);
            });
        }

        function isValidFile(file) {
            const allowedTypes = ['application/pdf', 'image/png', 'image/jpeg', 'image/jpg'];
            const maxSize = 10 * 1024 * 1024; // 10MB

            if (!allowedTypes.includes(file.type)) {
                return false;
            }

            if (file.size > maxSize) {
                alert(`<?php echo __('Dosya çok büyük:', 'komtera'); ?> ${file.name} (${(file.size / 1024 / 1024).toFixed(2)}MB)`);
                return false;
            }

            return true;
        }

        function uploadSingleFile(file, index, totalFiles) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('teklif_no', '<?php echo htmlspecialchars($teklif_no); ?>');

            fetch('<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/_service/upload_ozel_fiyat.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Upload response status:', response.status);
                // Always try to parse JSON, even for error responses
                return response.json().then(data => {
                    if (!response.ok) {
                        throw new Error(data.error || `HTTP ${response.status}: ${response.statusText}`);
                    }
                    return data;
                });
            })
            .then(data => {
                if (data.success) {
                    console.log(`File uploaded: ${file.name}`);

                    // Update progress
                    const progress = ((index + 1) / totalFiles) * 100;
                    document.querySelector('.upload-progress-bar').style.width = progress + '%';

                    // If last file, hide progress and reload file list
                    if (index === totalFiles - 1) {
                        setTimeout(() => {
                            document.querySelector('.upload-progress').style.display = 'none';
                            loadExistingFiles();

                            // Reset file input
                            document.getElementById('fileInput').value = '';

                            // Show success message
                            showUploadMessage('<?php echo __('Dosyalar başarıyla yüklendi!', 'komtera'); ?>', 'success');
                        }, 500);
                    }
                } else {
                    showUploadMessage(`<?php echo __('Upload hatası:', 'komtera'); ?> ${file.name} - ${data.error}`, 'error');
                }
            })
            .catch(error => {
                console.error('Upload error:', error);

                let errorMessage = `<?php echo __('Upload hatası:', 'komtera'); ?> ${file.name}`;

                if (error.message.includes('413')) {
                    errorMessage += ' - <?php echo __('Dosya çok büyük (Server limiti)', 'komtera'); ?>';
                } else if (error.message.includes('408')) {
                    errorMessage += ' - <?php echo __('Upload timeout (Yavaş bağlantı)', 'komtera'); ?>';
                } else if (error.message.includes('502') || error.message.includes('503')) {
                    errorMessage += ' - <?php echo __('Server hatası (Geçici)', 'komtera'); ?>';
                } else {
                    errorMessage += ` - ${error.message}`;
                }

                showUploadMessage(errorMessage, 'error');
            });
        }

        function loadExistingFiles() {
            fetch(`<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/_service/get_ozel_fiyat_files.php?teklif_no=<?php echo urlencode($teklif_no); ?>`)
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Debug response:', data);
                    if (data.success) {
                        displayFileList(data.files || []);
                        updateFileCountBadge(data.files || []);
                    } else {
                        console.error('Server error:', data.error);
                        console.error('Debug info:', data.debug);
                        alert('Debug Error: ' + data.error + '\n\nCheck console for details');
                        displayFileList([]);
                        updateFileCountBadge([]);
                    }
                })
                .catch(error => {
                    console.error('Error loading files:', error);
                    alert('Network Error: ' + error.message);
                    displayFileList([]);
                    updateFileCountBadge([]);
                });
        }

        function updateFileCountBadge(files) {
            const badge = document.getElementById('fileCountBadge');
            const count = files.length;

            if (count > 0) {
                badge.textContent = count;
                badge.classList.add('has-files');

                // Add special styling for many files
                if (count >= 5) {
                    badge.classList.add('many-files');
                } else {
                    badge.classList.remove('many-files');
                }
            } else {
                badge.classList.remove('has-files', 'many-files');
            }
        }

        function displayFileList(files) {
            const container = document.getElementById('fileListContainer');

            if (!files || files.length === 0) {
                container.innerHTML = `
                    <div class="empty-files">
                        <div class="dashicons dashicons-media-document"></div>
                        <div><?php echo __('Henüz dosya yüklenmemiş', 'komtera'); ?></div>
                    </div>
                `;
                return;
            }

            container.innerHTML = files.map(file => `
                <div class="file-item" data-file-id="${file.id}">
                    <div class="file-icon">
                        ${getFileIcon(file.extension)}
                    </div>
                    <div class="file-info">
                        <div class="file-name" title="${file.original_name}">${file.original_name}</div>
                        <div class="file-details">
                            ${formatFileSize(file.file_size)} • ${file.upload_date}
                        </div>
                    </div>
                    <div class="file-actions">
                        <button class="file-btn view" onclick="viewFile('${file.file_path}')" title="<?php echo __('Görüntüle', 'komtera'); ?>">
                            <span class="dashicons dashicons-visibility"></span> <?php echo __('Görüntüle', 'komtera'); ?>
                        </button>
                        <button class="file-btn delete" onclick="deleteFile(${file.id})" title="<?php echo __('Sil', 'komtera'); ?>">
                            <span class="dashicons dashicons-trash"></span> <?php echo __('Sil', 'komtera'); ?>
                        </button>
                    </div>
                </div>
            `).join('');
        }

        function getFileIcon(extension) {
            switch(extension.toLowerCase()) {
                case 'pdf':
                    return '📄';
                case 'png':
                case 'jpg':
                case 'jpeg':
                    return '🖼️';
                default:
                    return '📎';
            }
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function viewFile(filePath) {
            const fullUrl = '<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/uploads/ozel_fiyat/' + filePath;
            window.open(fullUrl, '_blank');
        }

        function deleteFile(fileId) {
            if (!confirm('<?php echo __('Bu dosyayı silmek istediğinizden emin misiniz?', 'komtera'); ?>')) {
                return;
            }

            fetch('<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/_service/delete_ozel_fiyat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `file_id=${fileId}&teklif_no=<?php echo urlencode($teklif_no); ?>`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadExistingFiles(); // Refresh file list
                } else {
                    alert('<?php echo __('Silme hatası:', 'komtera'); ?> ' + data.error);
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                alert('<?php echo __('Silme hatası oluştu.', 'komtera'); ?>');
            });
        }

        // Message display function
        function showUploadMessage(message, type = 'info') {
            // Remove existing messages
            const existingMessages = document.querySelectorAll('.upload-message');
            existingMessages.forEach(msg => msg.remove());

            // Create message element
            const messageEl = document.createElement('div');
            messageEl.className = `upload-message upload-message-${type}`;
            messageEl.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 12px 20px;
                border-radius: 6px;
                color: white;
                font-weight: 500;
                z-index: 10001;
                max-width: 400px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                animation: slideInRight 0.3s ease;
            `;

            // Set background color based on type
            const colors = {
                success: '#28a745',
                error: '#dc3545',
                warning: '#ffc107',
                info: '#007cba'
            };
            messageEl.style.backgroundColor = colors[type] || colors.info;

            messageEl.textContent = message;

            // Add close button
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

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (messageEl.parentNode) {
                    messageEl.style.animation = 'slideOutRight 0.3s ease';
                    setTimeout(() => messageEl.remove(), 300);
                }
            }, 5000);
        }

        // Add CSS animations
        if (!document.getElementById('upload-animations')) {
            const style = document.createElement('style');
            style.id = 'upload-animations';
            style.textContent = `
                @keyframes slideInRight {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOutRight {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
        }

        // Close modal when clicking outside
        document.getElementById('ozelFiyatModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeOzelFiyatModal();
            }
        });

        // Grid functions
        function updateGridPageSize() {
            const pageSize = document.getElementById('gridPageSize').value;
            const iframe = document.getElementById('productGrid');
            const currentSrc = iframe.src;

            // Update iframe src with new page size
            const url = new URL(currentSrc);
            url.searchParams.set('page_size', pageSize);
            iframe.src = url.toString();
        }

        function refreshGrid() {
            const iframe = document.getElementById('productGrid');
            const currentSrc = iframe.src;

            // Force reload iframe
            iframe.src = currentSrc + (currentSrc.includes('?') ? '&' : '?') + 'refresh=' + new Date().getTime();
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Set focus and initialize any required elements
            document.body.style.opacity = '1';

            // Update iframe height based on content if needed
            const iframe = document.getElementById('productGrid');
            iframe.onload = function() {
                try {
                    // Adjust iframe height if accessible
                    const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                    if (iframeDoc && iframeDoc.body) {
                        const height = iframeDoc.body.scrollHeight;
                        if (height > 400) {
                            iframe.style.height = Math.min(height + 50, 800) + 'px';
                        }
                    }
                } catch (e) {
                    // Cross-origin restriction, keep default height
                }
            };
        });

        // Initialize file count badge on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadFileCountForBadge();
            loadLicenseCountForBadge();
        });

        function loadFileCountForBadge() {
            fetch(`<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/_service/get_ozel_fiyat_files.php?teklif_no=<?php echo urlencode($teklif_no); ?>`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateFileCountBadge(data.files || []);
                    }
                })
                .catch(error => {
                    console.error('Error loading file count:', error);
                });
        }

        // === LISANS DOSYASI MODAL FUNCTIONS ===

        function showLisansModal() {
            console.log('showLisansModal called');
            const modal = document.getElementById('lisansModal');
            console.log('Modal element:', modal);
            modal.classList.add('show');
            console.log('Modal classes after add:', modal.className);

            // Add escape key listener
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeLisansModal();
                }
            });

            // Add click outside to close
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeLisansModal();
                }
            });

            // Auto-hide modal after 10 seconds
            setTimeout(() => {
                modal.classList.add('show');
            }, 10);

            // Load existing files
            loadExistingLisansFiles();
        }

        function closeLisansModal() {
            const modal = document.getElementById('lisansModal');
            modal.classList.remove('show');
        }

        function triggerLisansFileUpload() {
            document.getElementById('lisansFileInput').click();
        }

        function handleLisansDrop(event) {
            event.preventDefault();
            event.stopPropagation();

            const files = event.dataTransfer.files;
            handleLisansFileSelect({target: {files: files}});
        }

        function handleLisansFileSelect(event) {
            const files = event.target.files;

            if (files.length === 0) return;

            // Validate files
            for (let file of files) {
                if (!isValidFile(file)) {
                    return;
                }
            }

            uploadLisansFiles(files);
        }

        function uploadLisansFiles(files) {
            const progressContainer = document.getElementById('lisansUploadProgress');
            const progressBar = document.getElementById('lisansProgressBar');

            progressContainer.style.display = 'block';
            progressBar.style.width = '0%';

            // Upload each file
            Array.from(files).forEach((file, index) => {
                uploadSingleLisansFile(file, index, files.length);
            });
        }

        function uploadSingleLisansFile(file, index, totalFiles) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('teklif_no', '<?php echo htmlspecialchars($teklif_no); ?>');

            fetch('<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/_service/upload_lisans_dosya.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Lisans upload response status:', response.status);
                // Always try to parse JSON, even for error responses
                return response.json().then(data => {
                    if (!response.ok) {
                        throw new Error(data.error || `HTTP ${response.status}: ${response.statusText}`);
                    }
                    return data;
                });
            })
            .then(data => {
                if (data.success) {
                    console.log(`Lisans file uploaded: ${file.name}`);

                    // Update progress
                    const progress = ((index + 1) / totalFiles) * 100;
                    document.getElementById('lisansProgressBar').style.width = progress + '%';

                    // If last file, hide progress and reload file list
                    if (index === totalFiles - 1) {
                        setTimeout(() => {
                            document.getElementById('lisansUploadProgress').style.display = 'none';
                            loadExistingLisansFiles();

                            // Reset file input
                            document.getElementById('lisansFileInput').value = '';

                            // Show success message
                            showUploadMessage('<?php echo __('Lisans dosyaları başarıyla yüklendi!', 'komtera'); ?>', 'success');
                        }, 500);
                    }
                } else {
                    showUploadMessage(`<?php echo __('Upload hatası:', 'komtera'); ?> ${file.name} - ${data.error}`, 'error');
                }
            })
            .catch(error => {
                console.error('Lisans upload error:', error);

                let errorMessage = `<?php echo __('Upload hatası:', 'komtera'); ?> ${file.name}`;

                if (error.message.includes('413')) {
                    errorMessage += ' - <?php echo __('Dosya çok büyük (Server limiti)', 'komtera'); ?>';
                } else if (error.message.includes('408')) {
                    errorMessage += ' - <?php echo __('Upload timeout (Yavaş bağlantı)', 'komtera'); ?>';
                } else if (error.message.includes('502') || error.message.includes('503')) {
                    errorMessage += ' - <?php echo __('Server hatası (Geçici)', 'komtera'); ?>';
                } else {
                    errorMessage += ` - ${error.message}`;
                }

                showUploadMessage(errorMessage, 'error');
            });
        }

        function loadExistingLisansFiles() {
            fetch(`<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/_service/get_lisans_files.php?teklif_no=<?php echo urlencode($teklif_no); ?>`)
                .then(response => {
                    console.log('Lisans files response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Lisans files response:', data);
                    if (data.success) {
                        displayLisansFileList(data.files || []);
                        updateLicenseCountBadge(data.files || []);
                    } else {
                        console.error('Server error:', data.error);
                        console.error('Debug info:', data.debug);
                        alert('Debug Error: ' + data.error + '\n\nCheck console for details');
                        displayLisansFileList([]);
                        updateLicenseCountBadge([]);
                    }
                })
                .catch(error => {
                    console.error('Error loading lisans files:', error);
                    alert('Network Error: ' + error.message);
                    displayLisansFileList([]);
                    updateLicenseCountBadge([]);
                });
        }

        function updateLicenseCountBadge(files) {
            const badge = document.getElementById('licenseCountBadge');
            const count = files.length;

            if (count > 0) {
                badge.textContent = count;
                badge.classList.add('has-files');

                // Add special styling for many files
                if (count >= 5) {
                    badge.classList.add('many-files');
                } else {
                    badge.classList.remove('many-files');
                }
            } else {
                badge.classList.remove('has-files', 'many-files');
            }
        }

        function displayLisansFileList(files) {
            const container = document.getElementById('lisansFileListContainer');

            if (files.length === 0) {
                container.innerHTML = '<div class="no-files"><?php echo __('Henüz lisans dosyası yüklenmemiş', 'komtera'); ?></div>';
                return;
            }

            container.innerHTML = files.map(file => `
                <div class="file-item">
                    <div class="file-icon">
                        ${file.file_type === 'application/pdf' ? '📄' : '🖼️'}
                    </div>
                    <div class="file-info">
                        <div class="file-name">${file.original_name}</div>
                        <div class="file-meta">
                            ${formatFileSize(file.file_size)} • ${file.upload_date}
                            ${file.uploaded_by ? ` • ${file.uploaded_by}` : ''}
                        </div>
                    </div>
                    <div class="file-actions">
                        <button class="btn-download" onclick="downloadLisansFile(${file.id})" title="<?php echo __('İndir', 'komtera'); ?>">
                            <span class="dashicons dashicons-download"></span>
                        </button>
                        <button class="btn-view" onclick="viewLisansFile('${file.file_name}')" title="<?php echo __('Görüntüle', 'komtera'); ?>">
                            <span class="dashicons dashicons-visibility"></span>
                        </button>
                        <button class="btn-delete" onclick="deleteLisansFile(${file.id})" title="<?php echo __('Sil', 'komtera'); ?>">
                            <span class="dashicons dashicons-trash"></span>
                        </button>
                    </div>
                </div>
            `).join('');
        }

        function deleteLisansFile(fileId) {
            if (!confirm('<?php echo __('Bu lisans dosyasını silmek istediğinizden emin misiniz?', 'komtera'); ?>')) {
                return;
            }

            fetch('<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/_service/delete_lisans_dosya.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `file_id=${fileId}&teklif_no=<?php echo urlencode($teklif_no); ?>`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadExistingLisansFiles(); // Refresh file list
                } else {
                    alert('<?php echo __('Silme hatası:', 'komtera'); ?> ' + data.error);
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                alert('<?php echo __('Silme hatası:', 'komtera'); ?> ' + error.message);
            });
        }

        function downloadLisansFile(fileId) {
            window.open(`<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/_service/download_lisans_dosya.php?file_id=${fileId}&teklif_no=<?php echo urlencode($teklif_no); ?>`, '_blank');
        }

        function viewLisansFile(fileName) {
            window.open(`<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/uploads/lisans_dosyalar/${fileName}`, '_blank');
        }

        function loadLicenseCountForBadge() {
            fetch(`<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/_service/get_lisans_files.php?teklif_no=<?php echo urlencode($teklif_no); ?>`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateLicenseCountBadge(data.files || []);
                    }
                })
                .catch(error => {
                    console.error('Error loading license file count:', error);
                });
        }
    </script>
</body>
</html>