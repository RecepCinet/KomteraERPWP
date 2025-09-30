<?php
// Grid için standalone sayfa
include dirname(__DIR__) . '/_conn.php';

// Get parameters
$teklif_no = $_GET['teklif_no'] ?? '';
$page_size = $_GET['page_size'] ?? 50;
$standalone = $_GET['standalone'] ?? false;

if (empty($teklif_no)) {
    die('Teklif numarası belirtilmemiş.');
}

// Fetch teklif ürünleri with detailed information
$teklif_urunler = [];
$error = '';
try {
    $sql = "SELECT
                tu.SATIR_NO,
                tu.SKU,
                tu.ACIKLAMA,
                tu.TIP,
                tu.MEVCUT_LISANS,
                tu.SURE,
                tu.OZEL_SATIS_MALIYET,
                tu.LISTE_FIYAT,
                tu.STANDART_MALIYET,
                tu.ISKONTO_ORANI,
                tu.ADET,
                tu.FIYAT_BIRIM,
                tu.TOPLAM_MALIYET,
                tu.SATIS_FIYAT_BIRIM
            FROM aa_erp_kt_teklifler_urunler tu
            WHERE tu.X_TEKLIF_NO = :teklif_no";

    if ($page_size !== 'all' && is_numeric($page_size)) {
        $sql .= " ORDER BY tu.SATIR_NO
                  OFFSET 0 ROWS FETCH NEXT " . intval($page_size) . " ROWS ONLY";
    } else {
        $sql .= " ORDER BY tu.SATIR_NO";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':teklif_no', $teklif_no);
    $stmt->execute();
    $teklif_urunler = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $error = $e->getMessage();
}

// Calculate totals
$totals = [
    'toplam_liste_fiyat' => 0,
    'toplam_standart_maliyet' => 0,
    'toplam_satis_fiyat' => 0,
    'toplam_maliyet' => 0
];

foreach ($teklif_urunler as $urun) {
    $adet = floatval($urun['ADET'] ?? 0);
    $totals['toplam_liste_fiyat'] += floatval($urun['LISTE_FIYAT'] ?? 0) * $adet;
    $totals['toplam_standart_maliyet'] += floatval($urun['STANDART_MALIYET'] ?? 0) * $adet;
    $totals['toplam_satis_fiyat'] += floatval($urun['SATIS_FIYAT_BIRIM'] ?? 0) * $adet;
    $totals['toplam_maliyet'] += floatval($urun['TOPLAM_MALIYET'] ?? 0);
}

?><!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teklif Ürünleri Grid</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.4;
            color: #333;
            background: #fff;
            font-size: 12px;
        }

        .grid-container {
            width: 100%;
            overflow-x: auto;
            padding: 4px;
        }

        .grid-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1200px;
            background: #fff;
            border: 1px solid #ddd;
        }

        .grid-table th,
        .grid-table td {
            padding: 6px 8px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 11px;
            vertical-align: top;
        }

        .grid-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
            position: sticky;
            top: 0;
            z-index: 10;
            white-space: nowrap;
        }

        .grid-table td {
            background: #fff;
        }

        .grid-table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .grid-table tbody tr:hover {
            background: #e3f2fd;
        }

        /* Column specific styling */
        .col-no {
            width: 40px;
            text-align: center;
            font-weight: 600;
        }

        .col-sku {
            width: 120px;
            font-family: monospace;
            font-weight: 500;
        }

        .col-aciklama {
            min-width: 200px;
            max-width: 300px;
            word-wrap: break-word;
        }

        .col-tip {
            width: 80px;
            text-align: center;
        }

        .col-mevcut {
            width: 60px;
            text-align: center;
        }

        .col-sure {
            width: 60px;
            text-align: center;
        }

        .col-number {
            width: 90px;
            text-align: right;
            font-family: monospace;
        }

        .col-adet {
            width: 50px;
            text-align: center;
            font-weight: 600;
        }

        .col-iskonto {
            width: 70px;
            text-align: right;
        }

        /* Type badges */
        .type-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .type-hardware { background: #e8f5e8; color: #2e7d32; }
        .type-license { background: #fff3e0; color: #f57c00; }
        .type-service { background: #e3f2fd; color: #1976d2; }

        /* Summary row */
        .summary-row {
            background: #f0f8ff !important;
            font-weight: 600;
        }

        .summary-row td {
            border-top: 2px solid #007cba;
            padding: 8px;
        }

        /* Number formatting */
        .currency {
            color: #2e7d32;
            font-weight: 500;
        }

        .currency.negative {
            color: #d32f2f;
        }

        /* Status indicators */
        .status-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 4px;
        }

        .status-active { background: #4caf50; }
        .status-inactive { background: #f44336; }
        .status-pending { background: #ff9800; }

        /* Responsive */
        @media (max-width: 768px) {
            .grid-table {
                min-width: 800px;
                font-size: 10px;
            }

            .grid-table th,
            .grid-table td {
                padding: 4px 6px;
            }

            .col-aciklama {
                min-width: 150px;
                max-width: 200px;
            }

            .col-number {
                width: 70px;
            }
        }

        /* Loading state */
        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .error {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 12px;
            margin: 10px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="grid-container">
        <?php if (!empty($error)): ?>
            <div class="error">
                <strong>Hata:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($teklif_urunler) && empty($error)): ?>
            <div class="loading">
                Bu teklifte henüz ürün bulunmuyor.
            </div>
        <?php else: ?>
            <table class="grid-table">
                <thead>
                    <tr>
                        <th class="col-no">S</th>
                        <th class="col-sku">SKU</th>
                        <th class="col-aciklama">Açıklama</th>
                        <th class="col-tip">Tip</th>
                        <th class="col-mevcut">Mevcut<br>Lisans</th>
                        <th class="col-sure">Süre</th>
                        <th class="col-number">Özel Satış<br>Maliyet (%)</th>
                        <th class="col-number">Liste Fiyatı<br>Maliyet</th>
                        <th class="col-number">Standart<br>Maliyet (%)</th>
                        <th class="col-iskonto">İskonto<br>Oranı</th>
                        <th class="col-adet">Adet</th>
                        <th class="col-number">Satış Fiyatı<br>Birim</th>
                        <th class="col-number">Toplam<br>Maliyet</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teklif_urunler as $index => $urun): ?>
                        <tr>
                            <td class="col-no"><?php echo ($index + 1); ?></td>
                            <td class="col-sku"><?php echo htmlspecialchars($urun['SKU'] ?? ''); ?></td>
                            <td class="col-aciklama"><?php echo htmlspecialchars($urun['ACIKLAMA'] ?? ''); ?></td>
                            <td class="col-tip">
                                <?php
                                $tip = strtolower($urun['TIP'] ?? '');
                                $tip_class = 'type-service';
                                $tip_text = $urun['TIP'] ?? '';

                                if (strpos($tip, 'hardware') !== false) {
                                    $tip_class = 'type-hardware';
                                    $tip_text = 'Hardware';
                                } elseif (strpos($tip, 'license') !== false) {
                                    $tip_class = 'type-license';
                                    $tip_text = 'License';
                                }
                                ?>
                                <span class="type-badge <?php echo $tip_class; ?>">
                                    <?php echo htmlspecialchars($tip_text); ?>
                                </span>
                            </td>
                            <td class="col-mevcut">
                                <?php
                                $mevcut = $urun['MEVCUT_LISANS'] ?? '0';
                                echo htmlspecialchars($mevcut);
                                ?>
                            </td>
                            <td class="col-sure">
                                <?php
                                $sure = $urun['SURE'] ?? '';
                                echo htmlspecialchars($sure);
                                ?>
                            </td>
                            <td class="col-number">
                                <?php
                                $ozel_maliyet = floatval($urun['OZEL_SATIS_MALIYET'] ?? 0);
                                echo number_format($ozel_maliyet, 2, ',', '.');
                                ?>
                            </td>
                            <td class="col-number currency">
                                <?php
                                $liste_fiyat = floatval($urun['LISTE_FIYAT'] ?? 0);
                                echo number_format($liste_fiyat, 2, ',', '.');
                                ?>
                            </td>
                            <td class="col-number">
                                <?php
                                $standart_maliyet = floatval($urun['STANDART_MALIYET'] ?? 0);
                                echo number_format($standart_maliyet, 2, ',', '.');
                                ?>
                            </td>
                            <td class="col-iskonto">
                                <?php
                                $iskonto = floatval($urun['ISKONTO_ORANI'] ?? 0);
                                echo number_format($iskonto, 2, ',', '.') . '%';
                                ?>
                            </td>
                            <td class="col-adet">
                                <?php
                                $adet = intval($urun['ADET'] ?? 0);
                                echo number_format($adet);
                                ?>
                            </td>
                            <td class="col-number currency">
                                <?php
                                $satis_fiyat = floatval($urun['SATIS_FIYAT_BIRIM'] ?? 0);
                                echo number_format($satis_fiyat, 2, ',', '.');
                                ?>
                            </td>
                            <td class="col-number currency">
                                <?php
                                $toplam_maliyet = floatval($urun['TOPLAM_MALIYET'] ?? 0);
                                echo number_format($toplam_maliyet, 2, ',', '.');
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <!-- Summary Row -->
                    <tr class="summary-row">
                        <td class="col-no"></td>
                        <td class="col-sku"></td>
                        <td class="col-aciklama"><strong>TOPLAM</strong></td>
                        <td class="col-tip"></td>
                        <td class="col-mevcut"></td>
                        <td class="col-sure"></td>
                        <td class="col-number"></td>
                        <td class="col-number currency">
                            <strong><?php echo number_format($totals['toplam_liste_fiyat'], 2, ',', '.'); ?></strong>
                        </td>
                        <td class="col-number currency">
                            <strong><?php echo number_format($totals['toplam_standart_maliyet'], 2, ',', '.'); ?></strong>
                        </td>
                        <td class="col-iskonto"></td>
                        <td class="col-adet">
                            <strong><?php echo count($teklif_urunler); ?></strong>
                        </td>
                        <td class="col-number currency">
                            <strong><?php echo number_format($totals['toplam_satis_fiyat'], 2, ',', '.'); ?></strong>
                        </td>
                        <td class="col-number currency">
                            <strong><?php echo number_format($totals['toplam_maliyet'], 2, ',', '.'); ?></strong>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <script>
        // Grid functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-adjust iframe height if we're in an iframe
            if (window.parent !== window) {
                function adjustHeight() {
                    const height = document.body.scrollHeight;
                    try {
                        window.parent.postMessage({
                            type: 'resize',
                            height: height
                        }, '*');
                    } catch (e) {
                        // Fallback - try to access parent directly
                        try {
                            const iframe = window.parent.document.getElementById('productGrid');
                            if (iframe) {
                                iframe.style.height = (height + 20) + 'px';
                            }
                        } catch (e) {
                            // Cross-origin restriction, can't adjust
                        }
                    }
                }

                // Adjust height after load and on window resize
                adjustHeight();
                window.addEventListener('resize', adjustHeight);

                // Also try after a short delay in case content is still loading
                setTimeout(adjustHeight, 500);
            }

            // Add row click handlers for future functionality
            const rows = document.querySelectorAll('.grid-table tbody tr:not(.summary-row)');
            rows.forEach(row => {
                row.addEventListener('click', function() {
                    // Remove previous selection
                    rows.forEach(r => r.classList.remove('selected'));
                    // Add selection to current row
                    this.classList.add('selected');
                });
            });
        });
    </script>
</body>
</html>