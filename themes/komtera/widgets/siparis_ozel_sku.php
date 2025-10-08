<?php
/**
 * Sipariş için Gelen Özel SKUlar Widget
 * G-106'dan dönüştürülmüştür
 */

function siparis_ozel_sku_widget_content() {
    global $wpdb;
    include get_stylesheet_directory() . '/erp/_conn.php';
    require_once get_stylesheet_directory() . '/inc/table_helper.php';

    $sql = "SELECT TOP 25 n.SIPARIS_NO
            FROM " . getTableName('aaa_erp_kt_siparis_icin_gelen_skular') . " n
            LEFT JOIN " . getTableName('aa_erp_kt_fatura_i') . " fi ON n.SIPARIS_NO = fi.siparisNo
            WHERE fi.siparisNo IS NULL
            AND n.SIPARIS_NO NOT IN ('T77434-1', 'T85942-1', 'T89330-1', 'T117692-1', 'T129014-1')
            GROUP BY n.SIPARIS_NO
            ORDER BY n.SIPARIS_NO DESC";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo '<div class="error"><p>' . esc_html($e->getMessage()) . '</p></div>';
        return;
    }
    ?>

    <style>
        .siparis-sku-widget {
            padding: 12px;
        }
        .siparis-sku-widget h4 {
            margin: 0 0 12px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #007cba;
            color: #007cba;
        }
        .siparis-sku-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 8px;
        }
        .siparis-sku-item {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            background: #007cba;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.2s;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .siparis-sku-item:hover {
            background: #005a87;
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .siparis-sku-empty {
            color: #999;
            font-style: italic;
            text-align: center;
            padding: 20px;
        }
    </style>

    <div class="siparis-sku-widget">
        <h4>Sipariş için Gelen Özel SKUlar</h4>
        <?php if (!empty($data)): ?>
            <div class="siparis-sku-list">
                <?php foreach ($data as $satir): ?>
                    <a href="<?php echo admin_url('admin.php?page=siparisler_detay&siparis_no=' . urlencode($satir['SIPARIS_NO'])); ?>"
                       class="siparis-sku-item">
                        <?php echo esc_html($satir['SIPARIS_NO']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="siparis-sku-empty">
                Bekleyen özel SKU bulunmuyor
            </div>
        <?php endif; ?>
    </div>
    <?php
}

function add_siparis_ozel_sku_widget() {
    wp_add_dashboard_widget(
        'siparis_ozel_sku_widget',
        'Sipariş Özel SKUlar',
        'siparis_ozel_sku_widget_content'
    );
}
add_action('wp_dashboard_setup', 'add_siparis_ozel_sku_widget');
