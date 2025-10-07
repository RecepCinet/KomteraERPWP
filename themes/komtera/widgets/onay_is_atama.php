<?php
/**
 * Onay/İş Atama/Bilgilendirmeler Widget
 * G-102'den dönüştürülmüştür
 */

function onay_is_atama_widget_content() {
    global $wpdb;
    include get_stylesheet_directory() . '/erp/_conn.php';

    $current_user = wp_get_current_user();
    $cryp = $current_user->user_login;

    // Silme işlemi
    if (isset($_GET['sil_is_atama'])) {
        $sil = intval($_GET['sil_is_atama']);
        $sql = "DELETE FROM " . getTableName('aa_erp_kt_is_atama') . " WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $sil]);
        wp_die('OK');
    }

    $stan = "kime='$cryp'";
    $kactane = 15;

    $sql = "SELECT TOP 15 ia.*,
            (SELECT ISNULL(t.ONAY1,0)+ISNULL(t.ONAY2,0)
             FROM " . getTableName('aa_erp_kt_teklifler') . " t
             WHERE t.TEKLIF_NO=ia.mid) as ONAYY
            FROM " . getTableName('aa_erp_kt_is_atama') . " ia
            WHERE $stan
            ORDER BY cd DESC, ct DESC";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $datafull = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo '<div class="error"><p>' . esc_html($e->getMessage()) . '</p></div>';
        return;
    }
    ?>

    <style>
        .onay-is-atama-container {
            font-size: 12px;
        }
        .onay-is-atama-header {
            background-color: #f2f2f2;
            padding: 10px 12px;
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 8px;
            border-radius: 4px;
        }
        .onay-is-atama-item {
            background-color: #fafafa;
            margin-bottom: 6px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        .onay-is-atama-item:nth-child(even) {
            background-color: #f5f5f5;
        }
        .onay-is-atama-item:hover {
            background-color: #e8f4f8;
        }
        .onay-is-row-1 {
            padding: 8px 12px 4px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .onay-is-row-2 {
            padding: 4px 12px 8px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            color: #666;
        }
        .onay-modul-no {
            font-weight: 600;
            color: #007cba;
        }
        .onay-modul-no a {
            color: #007cba;
            text-decoration: none;
        }
        .onay-modul-no a:hover {
            text-decoration: underline;
        }
        .onay-beklenen {
            background: #fff3cd;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 11px;
            color: #856404;
        }
        .onay-check {
            color: #28a745;
            font-size: 16px;
            margin-left: 8px;
        }
        .onay-kisi {
            display: flex;
            gap: 12px;
        }
        .onay-sil-btn {
            color: #d32f2f;
            cursor: pointer;
            text-decoration: none;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 3px;
        }
        .onay-sil-btn:hover {
            background: #ffebee;
            text-decoration: none;
        }
        .onay-more {
            text-align: center;
            color: #999;
            padding: 8px;
            font-size: 11px;
        }
    </style>

    <div class="onay-is-atama-container">
        <div class="onay-is-atama-header">
            Onay/İş Atama/Bilgilendirmeler (Son <?php echo $kactane; ?> Kayıt)
        </div>
        <?php
        $sayy = 0;
        foreach ($datafull as $data) {
            $sayy++;
            $dd = $data['cd'];
            $tt = substr($data['ct'], 0, 8);
            $onay = $data['ONAYY'];
            ?>
            <div class="onay-is-atama-item" id="row-<?php echo $data['id']; ?>">
                <div class="onay-is-row-1">
                    <div class="onay-modul-no">
                        <?php echo esc_html($data['modul']); ?>:
                        <a href="<?php echo admin_url('admin.php?page=' . strtolower($data['modul']) . 'lar_detay&' . strtolower($data['modul']) . '_no=' . $data['mid']); ?>">
                            <?php echo esc_html($data['mid']); ?>
                        </a>
                        <?php if ($onay == 1 || $onay == 2): ?>
                            <span class="onay-check">✓</span>
                        <?php endif; ?>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="onay-beklenen"><?php echo esc_html($data['beklenen']); ?></span>
                        <a href="#" class="onay-sil-btn" onclick="satirSil(<?php echo $data['id']; ?>); return false;">Sil</a>
                    </div>
                </div>
                <div class="onay-is-row-2">
                    <div>📅 <?php echo esc_html($dd . ' ' . $tt); ?></div>
                    <div class="onay-kisi">
                        <span>👤 <?php echo esc_html($data['kimden']); ?></span>
                        <span>→</span>
                        <span>👤 <?php echo esc_html($data['kime']); ?></span>
                    </div>
                </div>
            </div>
            <?php
        }
        if ($sayy >= 15) {
            echo '<div class="onay-more">...</div>';
        }
        if ($sayy == 0) {
            echo '<div class="onay-more">Henüz kayıt bulunmuyor</div>';
        }
        ?>
    </div>

    <script>
        function satirSil(id) {
            var row = document.getElementById('row-' + id);
            row.style.background = '#FFEEEE';

            var xhr = new XMLHttpRequest();
            xhr.open('GET', '<?php echo admin_url('admin.php'); ?>?sil_is_atama=' + id, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    row.style.display = 'none';
                }
            };
            xhr.send();
        }
    </script>
    <?php
}

function add_onay_is_atama_widget() {
    wp_add_dashboard_widget(
        'onay_is_atama_widget',
        'Onay/İş Atama/Bilgilendirmeler',
        'onay_is_atama_widget_content'
    );
}
add_action('wp_dashboard_setup', 'add_onay_is_atama_widget');
