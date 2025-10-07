<?php
/**
 * Satış Hedefleri Widget
 * G-107'den dönüştürülmüştür
 */

function satis_hedefleri_widget_content() {
    global $wpdb;
    include get_stylesheet_directory() . '/erp/_conn.php';

    $current_user = wp_get_current_user();

    // Kullanıcı markalarını WordPress user meta'dan al
    $user_brands = get_user_meta($current_user->ID, 'my_brands', true);

    // Eğer marka yoksa veya boşsa, tüm markaları göster
    if (empty($user_brands) || !is_array($user_brands)) {
        $user_brands = ['KOMTERA'];
    } else {
        // KOMTERA'yı ekle
        $user_brands[] = 'KOMTERA';
    }

    // SQL IN clause için formatla
    $markalar = "'" . implode("','", array_unique($user_brands)) . "'";

    // Hedefleri al
    try {
        $sql = "SELECT SUM(q1) as Q1, SUM(q2) as Q2, SUM(q3) as Q3, SUM(q4) as Q4
                FROM " . getTableName('aa_erp_kt_mt_hedefler') . "
                WHERE marka IN ($markalar)";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $hedefler = $stmt->fetch(PDO::FETCH_ASSOC);

        $q1_hedef = $hedefler['Q1'] ?? 0;
        $q2_hedef = $hedefler['Q2'] ?? 0;
        $q3_hedef = $hedefler['Q3'] ?? 0;
        $q4_hedef = $hedefler['Q4'] ?? 0;
    } catch (Exception $e) {
        $q1_hedef = $q2_hedef = $q3_hedef = $q4_hedef = 0;
    }

    // Satışları al
    try {
        $sql = "SELECT
                SUM(Ocak+Subat+Mart) AS Q1,
                SUM(Nisan+Mayis+Haziran) AS Q2,
                SUM(Temmuz+Agustos+Eylul) AS Q3,
                SUM(Ekim+Kasim+Aralik) AS Q4
                FROM " . getTableName('aaaa_erp_kt_komisyon_raporu_ham') . "
                WHERE MARKA IN ($markalar)";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $satislar = $stmt->fetch(PDO::FETCH_ASSOC);

        $q1_satis = $satislar['Q1'] ?? 0;
        $q2_satis = $q1_satis + ($satislar['Q2'] ?? 0);
        $q3_satis = $q2_satis + ($satislar['Q3'] ?? 0);
        $q4_satis = $q3_satis + ($satislar['Q4'] ?? 0);
    } catch (Exception $e) {
        $q1_satis = $q2_satis = $q3_satis = $q4_satis = 0;
    }

    // Kalanları hesapla
    $q1_kalan = $q1_hedef - $q1_satis;
    $q2_kalan = $q2_hedef - $q2_satis;
    $q3_kalan = $q3_hedef - $q3_satis;
    $q4_kalan = $q4_hedef - $q4_satis;

    // Açık siparişleri al
    try {
        $sql = "SELECT
                CASE
                    WHEN MONTH(s.CD)>=1 AND MONTH(s.CD)<=3 THEN 1
                    WHEN MONTH(s.CD)>=4 AND MONTH(s.CD)<=6 THEN 2
                    WHEN MONTH(s.CD)>=7 AND MONTH(s.CD)<=9 THEN 3
                    WHEN MONTH(s.CD)>=10 AND MONTH(s.CD)<=12 THEN 4
                    ELSE 0
                END AS Q,
                CASE
                    WHEN f.PARA_BIRIMI = 'USD' THEN (su.ADET * su.BIRIM_FIYAT)
                    WHEN f.PARA_BIRIMI = 'TRY' THEN (su.ADET * su.BIRIM_FIYAT) / (SELECT TOP 1 USD FROM aa_erp_kur k ORDER BY tarih DESC)
                    WHEN f.PARA_BIRIMI = 'EUR' THEN (su.ADET * su.BIRIM_FIYAT) / (SELECT TOP 1 USD/EUR FROM aa_erp_kur k ORDER BY tarih DESC)
                    ELSE 0
                END AS DLR_TUTAR
                FROM " . getTableName('aa_erp_kt_siparisler_urunler') . " AS su
                LEFT JOIN " . getTableName('aa_erp_kt_siparisler') . " AS s ON s.SIPARIS_NO = su.X_SIPARIS_NO
                LEFT JOIN " . getTableName('aa_erp_kt_firsatlar') . " AS f ON f.FIRSAT_NO = s.X_FIRSAT_NO
                WHERE (s.SIPARIS_DURUM = 0 OR s.SIPARIS_DURUM = 1) AND f.MARKA IN ($markalar)";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $acsip = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $q1_toplam = $q2_toplam = $q3_toplam = $q4_toplam = 0;
        foreach ($acsip as $satir) {
            $q = $satir['Q'];
            $tutar = (int)($satir['DLR_TUTAR'] ?? 0);

            if ($q == 1) $q1_toplam += $tutar;
            if ($q == 2) $q2_toplam += $tutar;
            if ($q == 3) $q3_toplam += $tutar;
            if ($q == 4) $q4_toplam += $tutar;
        }
    } catch (Exception $e) {
        $q1_toplam = $q2_toplam = $q3_toplam = $q4_toplam = 0;
    }

    // Mevcut çeyreği belirle
    $ay = (int)date("m");
    $aktif_q = 1;
    if ($ay >= 4 && $ay <= 6) $aktif_q = 2;
    elseif ($ay >= 7 && $ay <= 9) $aktif_q = 3;
    elseif ($ay >= 10 && $ay <= 12) $aktif_q = 4;
    ?>

    <style>
        .satis-hedefleri-table {
            width: 100%;
            border-collapse: collapse;
        }
        .satis-hedefleri-table th,
        .satis-hedefleri-table td {
            padding: 8px;
            border: 1px solid #e0e0e0;
            text-align: right;
            font-size: 12px;
        }
        .satis-hedefleri-table thead th {
            background-color: #f2f2f2;
            font-weight: 600;
        }
        .satis-hedefleri-table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .satis-hedefleri-table .aktif-q {
            background-color: #e3f2fd !important;
            font-weight: bold;
        }
        .satis-hedefleri-table .satir-label {
            text-align: left;
            font-weight: 600;
        }
    </style>

    <table class="satis-hedefleri-table">
        <thead>
            <tr>
                <th class="satir-label">🎯 Satış Hedefleri</th>
                <th class="<?php echo $aktif_q == 1 ? 'aktif-q' : ''; ?>">Q1</th>
                <th class="<?php echo $aktif_q == 2 ? 'aktif-q' : ''; ?>">Q2</th>
                <th class="<?php echo $aktif_q == 3 ? 'aktif-q' : ''; ?>">Q3</th>
                <th class="<?php echo $aktif_q == 4 ? 'aktif-q' : ''; ?>">Q4</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="satir-label">📊 Hedefler</td>
                <td class="<?php echo $aktif_q == 1 ? 'aktif-q' : ''; ?>"><?php echo number_format($q1_hedef, 0, ",", "."); ?></td>
                <td class="<?php echo $aktif_q == 2 ? 'aktif-q' : ''; ?>"><?php echo number_format($q2_hedef, 0, ",", "."); ?></td>
                <td class="<?php echo $aktif_q == 3 ? 'aktif-q' : ''; ?>"><?php echo number_format($q3_hedef, 0, ",", "."); ?></td>
                <td class="<?php echo $aktif_q == 4 ? 'aktif-q' : ''; ?>"><?php echo number_format($q4_hedef, 0, ",", "."); ?></td>
            </tr>
            <tr>
                <td class="satir-label">✅ Satış</td>
                <td class="<?php echo $aktif_q == 1 ? 'aktif-q' : ''; ?>"><?php echo number_format($q1_satis, 0, ",", "."); ?></td>
                <td class="<?php echo $aktif_q == 2 ? 'aktif-q' : ''; ?>"><?php echo number_format($q2_satis, 0, ",", "."); ?></td>
                <td class="<?php echo $aktif_q == 3 ? 'aktif-q' : ''; ?>"><?php echo number_format($q3_satis, 0, ",", "."); ?></td>
                <td class="<?php echo $aktif_q == 4 ? 'aktif-q' : ''; ?>"><?php echo number_format($q4_satis, 0, ",", "."); ?></td>
            </tr>
            <tr>
                <td class="satir-label">⏳ Kalan</td>
                <td class="<?php echo $aktif_q == 1 ? 'aktif-q' : ''; ?>"><?php echo $aktif_q == 1 ? number_format($q1_kalan, 0, ",", ".") : ''; ?></td>
                <td class="<?php echo $aktif_q == 2 ? 'aktif-q' : ''; ?>"><?php echo $aktif_q == 2 ? number_format($q2_kalan, 0, ",", ".") : ''; ?></td>
                <td class="<?php echo $aktif_q == 3 ? 'aktif-q' : ''; ?>"><?php echo $aktif_q == 3 ? number_format($q3_kalan, 0, ",", ".") : ''; ?></td>
                <td class="<?php echo $aktif_q == 4 ? 'aktif-q' : ''; ?>"><?php echo $aktif_q == 4 ? number_format($q4_kalan, 0, ",", ".") : ''; ?></td>
            </tr>
            <tr>
                <td class="satir-label">📦 Açık Sipariş</td>
                <td class="<?php echo $aktif_q == 1 ? 'aktif-q' : ''; ?>"><?php echo number_format($q1_toplam, 0, ",", "."); ?></td>
                <td class="<?php echo $aktif_q == 2 ? 'aktif-q' : ''; ?>"><?php echo number_format($q2_toplam, 0, ",", "."); ?></td>
                <td class="<?php echo $aktif_q == 3 ? 'aktif-q' : ''; ?>"><?php echo number_format($q3_toplam, 0, ",", "."); ?></td>
                <td class="<?php echo $aktif_q == 4 ? 'aktif-q' : ''; ?>"><?php echo number_format($q4_toplam, 0, ",", "."); ?></td>
            </tr>
        </tbody>
    </table>
    <?php
}

function add_satis_hedefleri_widget() {
    wp_add_dashboard_widget(
        'satis_hedefleri_widget',
        'Satış Hedefleri',
        'satis_hedefleri_widget_content'
    );
}
add_action('wp_dashboard_setup', 'add_satis_hedefleri_widget');
