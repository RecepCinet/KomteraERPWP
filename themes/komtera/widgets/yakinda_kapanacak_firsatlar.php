<?php
/**
 * Yakında Kapanacak Fırsatlar Widget
 * G-104'den dönüştürülmüştür
 */

function yakinda_kapanacak_firsatlar_widget_content() {
    global $wpdb;
    include get_stylesheet_directory() . '/erp/_conn.php';

    $current_user = wp_get_current_user();
    $cryp = $current_user->user_login;

    $ek = "";
    if ($cryp === "recep.cinet" || $cryp === "gokhan.ilgit") {
        $ek = "";
    } else {
        $ek = "AND MUSTERI_TEMSILCISI='$cryp'";
    }

    $kactane = 15;

    $sql = "SELECT TOP $kactane
            FIRSAT_NO,
            BITIS_TARIHI,
            DATEDIFF(day, GETDATE(), BITIS_TARIHI) as KALAN_GUN,
            BAYI_ADI,
            MUSTERI_ADI
            FROM " . getTableName('aa_erp_kt_firsatlar') . "
            WHERE DATEADD(day, 1, BITIS_TARIHI) >= GETDATE()
            AND FIRSAT_NO IS NOT NULL
            AND (SIL IS NULL OR SIL <> 1)
            AND DURUM = 0
            $ek
            ORDER BY BITIS_TARIHI";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $datafull = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo '<div class="error"><p>' . esc_html($e->getMessage()) . '</p></div>';
        return;
    }

    // Otomatik kaybedildi durumuna getir
    try {
        $update_sql = "UPDATE " . getTableName('aa_erp_kt_firsatlar') . "
                       SET KAYBEDILME_NEDENI = 'Yetersiz Takip', DURUM = '-1'
                       WHERE DATEADD(day, 1, BITIS_TARIHI) <= GETDATE()
                       AND (SIL IS NULL OR SIL <> 1)
                       AND DURUM = 0";
        $stmt = $conn->prepare($update_sql);
        $stmt->execute();
    } catch (Exception $e) {
        // Hata durumunda sessizce geç
    }
    ?>

    <style>
        .yakinda-kapanacak-container {
            font-size: 12px;
        }
        .yakinda-kapanacak-header {
            background-color: #f2f2f2;
            padding: 10px 12px;
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 8px;
            border-radius: 4px;
        }
        .yakinda-firsat-item {
            background-color: #fafafa;
            margin-bottom: 6px;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .yakinda-firsat-item:nth-child(even) {
            background-color: #f5f5f5;
        }
        .yakinda-firsat-item:hover {
            background-color: #e8f4f8;
            transform: translateX(2px);
        }
        .yakinda-firsat-item.renk-0 {
            background-color: #ffebee;
            border-left: 4px solid #f44336;
        }
        .yakinda-firsat-item.renk-1 {
            background-color: #fff3e0;
            border-left: 4px solid #ff9800;
        }
        .yakinda-firsat-item.renk-2 {
            background-color: #fff9c4;
            border-left: 4px solid #ffc107;
        }
        .yakinda-row-1 {
            padding: 8px 12px 4px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .yakinda-row-2 {
            padding: 4px 12px 8px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            color: #666;
        }
        .yakinda-firsat-no {
            font-weight: 600;
            color: #007cba;
        }
        .yakinda-firsat-no a {
            color: #007cba;
            text-decoration: none;
        }
        .yakinda-firsat-no a:hover {
            text-decoration: underline;
        }
        .yakinda-tarih-badge {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .yakinda-gun-badge {
            background: #d32f2f;
            color: white;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        .yakinda-gun-badge.gun-1 {
            background: #f57c00;
        }
        .yakinda-gun-badge.gun-2 {
            background: #ffa000;
        }
        .yakinda-gun-badge.gun-3plus {
            background: #388e3c;
        }
        .yakinda-bayi {
            font-weight: 500;
            color: #333;
        }
        .yakinda-musteri {
            color: #666;
        }
        .yakinda-empty {
            text-align: center;
            color: #999;
            padding: 20px;
            font-style: italic;
        }
    </style>

    <div class="yakinda-kapanacak-container">
        <div class="yakinda-kapanacak-header">
            Yakında Kapanacak Fırsatlar (Yetersiz Takip Yapılacak)
        </div>
        <?php
        foreach ($datafull as $satir) {
            $renk_class = "";
            $gun_class = "gun-3plus";
            $kalan_gun = (int)$satir['KALAN_GUN'];

            if ($kalan_gun == 0) {
                $renk_class = "renk-0";
                $gun_class = "";
            } elseif ($kalan_gun == 1) {
                $renk_class = "renk-1";
                $gun_class = "gun-1";
            } elseif ($kalan_gun == 2) {
                $renk_class = "renk-2";
                $gun_class = "gun-2";
            }

            $bitis_tarihi = '';
            if ($satir['BITIS_TARIHI']) {
                try {
                    $date = new DateTime($satir['BITIS_TARIHI']);
                    $bitis_tarihi = $date->format('d.m.Y');
                } catch (Exception $e) {
                    $bitis_tarihi = $satir['BITIS_TARIHI'];
                }
            }
            ?>
            <div class="yakinda-firsat-item <?php echo $renk_class; ?>">
                <div class="yakinda-row-1">
                    <div class="yakinda-firsat-no">
                        <a href="<?php echo admin_url('admin.php?page=firsatlar_detay&firsat_no=' . urlencode($satir['FIRSAT_NO'])); ?>">
                            <?php echo esc_html($satir['FIRSAT_NO']); ?>
                        </a>
                    </div>
                    <div class="yakinda-tarih-badge">
                        <span>📅 <?php echo esc_html($bitis_tarihi); ?></span>
                        <span class="yakinda-gun-badge <?php echo $gun_class; ?>">
                            <?php echo $kalan_gun == 0 ? 'BUGÜN!' : ($kalan_gun . ' gün'); ?>
                        </span>
                    </div>
                </div>
                <div class="yakinda-row-2">
                    <div class="yakinda-bayi">
                        🏢 <?php echo esc_html(mb_substr($satir['BAYI_ADI'], 0, 35)); ?>
                    </div>
                    <div class="yakinda-musteri">
                        👤 <?php echo esc_html(mb_substr($satir['MUSTERI_ADI'], 0, 35)); ?>
                    </div>
                </div>
            </div>
            <?php
        }
        if (empty($datafull)) {
            echo '<div class="yakinda-empty">✓ Yakında kapanacak fırsat bulunmuyor</div>';
        }
        ?>
    </div>
    <?php
}

function add_yakinda_kapanacak_firsatlar_widget() {
    wp_add_dashboard_widget(
        'yakinda_kapanacak_firsatlar_widget',
        'Yakında Kapanacak Fırsatlar',
        'yakinda_kapanacak_firsatlar_widget_content'
    );
}
add_action('wp_dashboard_setup', 'add_yakinda_kapanacak_firsatlar_widget');
