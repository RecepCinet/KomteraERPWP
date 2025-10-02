<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// JavaScript Enhanced Grid for Teklif Ürünleri
include dirname(dirname(__DIR__)) . '/_conn.php';

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
                tu.id,
                tu.X_TEKLIF_NO,
                tu.SKU,
                tu.ACIKLAMA,
                tu.TIP,
                tu.SURE,
                tu.ADET,
                tu.B_LISTE_FIYATI,
                tu.MEVCUT_LISANS,
                tu.B_MALIYET,
                tu.O_MALIYET,
                tu.ISKONTO,
                tu.B_SATIS_FIYATI,
                CASE
                    WHEN tu.B_MALIYET>0 THEN tu.B_MALIYET*tu.ADET
                    ELSE tu.O_MALIYET*tu.ADET
                END AS T_MALIYET,
                tu.B_SATIS_FIYATI*tu.ADET AS T_SATIS_FIYATI,
                CASE
                    WHEN tu.B_MALIYET>0 THEN ( ( tu.B_SATIS_FIYATI - tu.B_MALIYET ) / NULLIF(tu.B_SATIS_FIYATI,0) ) * 100
                    ELSE ( ( tu.B_SATIS_FIYATI - tu.O_MALIYET ) / NULLIF(tu.B_SATIS_FIYATI,0) ) * 100
                END AS KARLILIK,
                (SELECT TOP 1 1 FROM " . getTableName('aa_erp_kt_mcafee_sku_sure') . " s WHERE s.sku=tu.SKU) AS MCSURE,
                tu.SATIS_TIPI
            FROM " . getTableName('aa_erp_kt_teklifler_urunler') . " tu
            WHERE tu.X_TEKLIF_NO = :teklif_no";

    if ($page_size !== 'all' && is_numeric($page_size)) {
        $sql .= " ORDER BY tu.id DESC
                  OFFSET 0 ROWS FETCH NEXT " . intval($page_size) . " ROWS ONLY";
    } else {
        $sql .= " ORDER BY tu.id DESC";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':teklif_no', $teklif_no);
    $stmt->execute();
    $teklif_urunler = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = 'SQL Error: ' . $e->getMessage() . ' | Code: ' . $e->getCode();
    http_response_code(500);
    die($error);
} catch (Exception $e) {
    $error = 'Error: ' . $e->getMessage();
    http_response_code(500);
    die($error);
}

// Calculate totals
$totals = [
    'toplam_liste_fiyat' => 0,
    'toplam_maliyet' => 0,
    'toplam_satis_fiyat' => 0,
    'toplam_karlilik' => 0
];

foreach ($teklif_urunler as $urun) {
    $totals['toplam_liste_fiyat'] += floatval($urun['B_LISTE_FIYATI'] ?? 0) * floatval($urun['ADET'] ?? 0);
    $totals['toplam_maliyet'] += floatval($urun['T_MALIYET'] ?? 0);
    $totals['toplam_satis_fiyat'] += floatval($urun['T_SATIS_FIYATI'] ?? 0);
}

// Calculate overall profit margin
if ($totals['toplam_satis_fiyat'] > 0) {
    $totals['toplam_karlilik'] = (($totals['toplam_satis_fiyat'] - $totals['toplam_maliyet']) / $totals['toplam_satis_fiyat']) * 100;
}

?><!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teklif Ürünleri Grid - JS Enhanced</title>
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

        /* Grid Header Controls */
        .grid-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-bottom: none;
            font-size: 11px;
        }

        .grid-info {
            color: #666;
            font-weight: 500;
        }

        .grid-actions {
            display: flex;
            gap: 8px;
        }

        .grid-btn {
            padding: 4px 8px;
            border: 1px solid #ccc;
            background: #fff;
            color: #333;
            cursor: pointer;
            border-radius: 3px;
            font-size: 10px;
            transition: all 0.2s;
        }

        .grid-btn:hover {
            background: #007cba;
            color: white;
            border-color: #007cba;
        }

        .grid-btn.active {
            background: #007cba;
            color: white;
            border-color: #007cba;
        }

        /* Main Grid Table */
        .grid-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1200px;
            background: #fff;
            border: 1px solid #ddd;
        }

        .grid-table th,
        .grid-table td {
            padding: 8px 10px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 11px;
            vertical-align: middle;
        }

        .grid-table th {
            background: linear-gradient(to bottom, #f8f9fa 0%, #e9ecef 100%);
            font-weight: 600;
            color: #333;
            position: sticky;
            top: 0;
            z-index: 10;
            white-space: nowrap;
            cursor: pointer;
            user-select: none;
        }

        .grid-table th:hover {
            background: linear-gradient(to bottom, #e9ecef 0%, #dee2e6 100%);
        }

        .grid-table th.sortable::after {
            content: ' ⇅';
            opacity: 0.5;
            font-size: 10px;
        }

        .grid-table th.sort-asc::after {
            content: ' ↑';
            opacity: 1;
            color: #007cba;
        }

        .grid-table th.sort-desc::after {
            content: ' ↓';
            opacity: 1;
            color: #007cba;
        }

        .grid-table tbody tr {
            background: #fff;
            transition: background-color 0.15s;
        }

        .grid-table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .grid-table tbody tr:hover {
            background: #e3f2fd !important;
            cursor: pointer;
        }

        .grid-table tbody tr.selected {
            background: #bbdefb !important;
        }

        /* Column specific styling */
        .col-no {
            width: 40px;
            text-align: center;
            font-weight: 600;
        }

        .col-sku {
            width: 140px;
            font-family: 'Courier New', monospace;
            font-weight: 500;
            font-size: 10px;
        }

        .col-aciklama {
            min-width: 200px;
            max-width: 300px;
            word-wrap: break-word;
            line-height: 1.3;
        }

        .col-tip {
            width: 80px;
            text-align: center;
        }

        .col-mevcut, .col-sure {
            width: 60px;
            text-align: center;
        }

        .col-number {
            width: 90px;
            text-align: right;
            font-family: 'Courier New', monospace;
            font-weight: 500;
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

        /* Editable cells */
        .editable {
            position: relative;
            cursor: text;
        }

        .editable:hover {
            background: rgba(0, 124, 186, 0.1) !important;
        }

        .editable.editing {
            padding: 0;
        }

        .editable input {
            width: 100%;
            height: 100%;
            border: none;
            padding: 8px 10px;
            font-size: 11px;
            font-family: inherit;
            background: #fff;
            box-shadow: 0 0 0 2px #007cba;
        }

        .editable input:focus {
            outline: none;
        }

        /* Type badges */
        .type-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .type-hardware { background: #e8f5e8; color: #2e7d32; }
        .type-license { background: #fff3e0; color: #f57c00; }
        .type-service { background: #e3f2fd; color: #1976d2; }

        /* Summary row */
        .summary-row {
            background: linear-gradient(to bottom, #f0f8ff 0%, #e0f0ff 100%) !important;
            font-weight: 600;
            border-top: 2px solid #007cba !important;
        }

        .summary-row td {
            border-top: 2px solid #007cba;
            padding: 10px 8px;
        }

        .summary-row:hover {
            background: linear-gradient(to bottom, #e0f0ff 0%, #d0e8ff 100%) !important;
        }

        /* Number formatting */
        .currency {
            color: #2e7d32;
            font-weight: 500;
        }

        .currency.negative {
            color: #d32f2f;
        }

        /* Loading and error states */
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

        /* Action buttons in cells */
        .cell-actions {
            display: none;
            position: absolute;
            right: 2px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.9);
            padding: 2px;
            border-radius: 2px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        .grid-table tbody tr:hover .cell-actions {
            display: block;
        }

        .action-btn {
            padding: 2px 4px;
            border: none;
            background: #007cba;
            color: white;
            font-size: 9px;
            cursor: pointer;
            border-radius: 2px;
            margin-left: 2px;
        }

        .action-btn:hover {
            background: #005a87;
        }

        /* Context menu */
        .context-menu {
            position: fixed;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 4px 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            display: none;
            min-width: 120px;
        }

        .context-menu-item {
            padding: 6px 12px;
            cursor: pointer;
            font-size: 11px;
            transition: background-color 0.15s;
        }

        .context-menu-item:hover {
            background: #e3f2fd;
        }

        .context-menu-separator {
            height: 1px;
            background: #e0e0e0;
            margin: 4px 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .grid-table {
                min-width: 900px;
                font-size: 10px;
            }

            .grid-table th,
            .grid-table td {
                padding: 6px 8px;
            }

            .col-aciklama {
                min-width: 150px;
                max-width: 200px;
            }

            .col-number {
                width: 70px;
            }
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
            <!-- Grid Controls -->
            <div class="grid-controls">
                <div class="grid-info">
                    <strong><?php echo count($teklif_urunler); ?></strong> ürün listeleniyor
                    <?php if ($page_size !== 'all'): ?>
                        (ilk <?php echo $page_size; ?> kayıt)
                    <?php endif; ?>
                </div>
                <div class="grid-actions">
                    <button class="grid-btn" onclick="exportToExcel()" title="Excel'e Aktar">
                        📊 Excel
                    </button>
                    <button class="grid-btn" onclick="refreshGrid()" title="Yenile">
                        🔄 Yenile
                    </button>
                    <button class="grid-btn" onclick="toggleEditMode()" id="editModeBtn" title="Düzenleme Modu">
                        ✏️ Düzenle
                    </button>
                </div>
            </div>

            <table class="grid-table" id="mainGrid">
                <thead>
                    <tr>
                        <th class="col-no">S</th>
                        <th class="col-sku sortable" data-column="SKU">SKU</th>
                        <th class="col-aciklama sortable" data-column="ACIKLAMA">Açıklama</th>
                        <th class="col-tip sortable" data-column="TIP">Tip</th>
                        <th class="col-mevcut sortable" data-column="MEVCUT_LISANS">Mevcut<br>Lisans</th>
                        <th class="col-sure sortable" data-column="SURE">Süre</th>
                        <th class="col-adet sortable" data-column="ADET">Adet</th>
                        <th class="col-number sortable" data-column="B_LISTE_FIYATI">Liste Fiyatı<br>(Birim)</th>
                        <th class="col-number sortable" data-column="B_MALIYET">Maliyet<br>(Birim)</th>
                        <th class="col-iskonto sortable" data-column="ISKONTO">İskonto<br>Oranı (%)</th>
                        <th class="col-number sortable" data-column="B_SATIS_FIYATI">Satış Fiyatı<br>(Birim)</th>
                        <th class="col-number sortable" data-column="T_SATIS_FIYATI">Toplam<br>Satış</th>
                        <th class="col-number sortable" data-column="KARLILIK">Karlılık<br>(%)</th>
                    </tr>
                </thead>
                <tbody id="gridBody">
                    <?php foreach ($teklif_urunler as $index => $urun): ?>
                        <tr data-row="<?php echo $index; ?>" data-id="<?php echo htmlspecialchars($urun['id'] ?? ''); ?>" data-sku="<?php echo htmlspecialchars($urun['SKU'] ?? ''); ?>">
                            <td class="col-no"><?php echo ($index + 1); ?></td>
                            <td class="col-sku"><?php echo htmlspecialchars($urun['SKU'] ?? ''); ?></td>
                            <td class="col-aciklama editable" data-field="ACIKLAMA">
                                <?php echo htmlspecialchars($urun['ACIKLAMA'] ?? ''); ?>
                            </td>
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
                            <td class="col-mevcut editable" data-field="MEVCUT_LISANS">
                                <?php echo htmlspecialchars($urun['MEVCUT_LISANS'] ?? '0'); ?>
                            </td>
                            <td class="col-sure editable" data-field="SURE">
                                <?php echo htmlspecialchars($urun['SURE'] ?? ''); ?>
                            </td>
                            <td class="col-adet editable" data-field="ADET">
                                <?php
                                $adet = intval($urun['ADET'] ?? 0);
                                echo number_format($adet);
                                ?>
                            </td>
                            <td class="col-number currency">
                                <?php
                                $liste_fiyat = floatval($urun['B_LISTE_FIYATI'] ?? 0);
                                echo number_format($liste_fiyat, 2, ',', '.');
                                ?>
                            </td>
                            <td class="col-number currency">
                                <?php
                                $maliyet = floatval($urun['B_MALIYET'] ?? $urun['O_MALIYET'] ?? 0);
                                echo number_format($maliyet, 2, ',', '.');
                                ?>
                            </td>
                            <td class="col-iskonto editable" data-field="ISKONTO">
                                <?php
                                $iskonto = floatval($urun['ISKONTO'] ?? 0);
                                echo number_format($iskonto, 2, ',', '.') . '%';
                                ?>
                            </td>
                            <td class="col-number currency editable" data-field="B_SATIS_FIYATI">
                                <?php
                                $satis_fiyat = floatval($urun['B_SATIS_FIYATI'] ?? 0);
                                echo number_format($satis_fiyat, 2, ',', '.');
                                ?>
                            </td>
                            <td class="col-number currency">
                                <?php
                                $toplam_satis = floatval($urun['T_SATIS_FIYATI'] ?? 0);
                                echo number_format($toplam_satis, 2, ',', '.');
                                ?>
                            </td>
                            <td class="col-number <?php echo floatval($urun['KARLILIK'] ?? 0) < 0 ? 'currency negative' : 'currency'; ?>">
                                <?php
                                $karlilik = floatval($urun['KARLILIK'] ?? 0);
                                echo number_format($karlilik, 2, ',', '.') . '%';
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
                        <td class="col-adet">
                            <strong><?php echo count($teklif_urunler); ?></strong>
                        </td>
                        <td class="col-number currency">
                            <strong><?php echo number_format($totals['toplam_liste_fiyat'], 2, ',', '.'); ?></strong>
                        </td>
                        <td class="col-number currency">
                            <strong><?php echo number_format($totals['toplam_maliyet'], 2, ',', '.'); ?></strong>
                        </td>
                        <td class="col-iskonto"></td>
                        <td class="col-number"></td>
                        <td class="col-number currency">
                            <strong><?php echo number_format($totals['toplam_satis_fiyat'], 2, ',', '.'); ?></strong>
                        </td>
                        <td class="col-number <?php echo $totals['toplam_karlilik'] < 0 ? 'currency negative' : 'currency'; ?>">
                            <strong><?php echo number_format($totals['toplam_karlilik'], 2, ',', '.'); ?>%</strong>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Context Menu -->
    <div class="context-menu" id="contextMenu">
        <div class="context-menu-item" onclick="copyCell()">Kopyala</div>
        <div class="context-menu-item" onclick="editCell()">Düzenle</div>
        <div class="context-menu-separator"></div>
        <div class="context-menu-item" onclick="deleteRow()">Satırı Sil</div>
        <div class="context-menu-item" onclick="duplicateRow()">Satırı Çoğalt</div>
    </div>

    <script>
        // Global variables
        let editMode = false;
        let currentEditCell = null;
        let selectedRows = new Set();
        let sortDirection = {};

        // Grid data
        const gridData = <?php echo json_encode($teklif_urunler); ?>;
        const teklifNo = '<?php echo htmlspecialchars($teklif_no); ?>';

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            initializeGrid();
            setupEventListeners();
            adjustIframeHeight();
        });

        function initializeGrid() {
            // Add sorting functionality
            document.querySelectorAll('.sortable').forEach(header => {
                header.addEventListener('click', function() {
                    sortTable(this.dataset.column);
                });
            });

            // Add row selection
            document.querySelectorAll('#gridBody tr:not(.summary-row)').forEach(row => {
                row.addEventListener('click', function(e) {
                    if (!e.ctrlKey && !e.metaKey) {
                        selectedRows.clear();
                        document.querySelectorAll('.selected').forEach(r => r.classList.remove('selected'));
                    }

                    const rowIndex = this.dataset.row;
                    if (selectedRows.has(rowIndex)) {
                        selectedRows.delete(rowIndex);
                        this.classList.remove('selected');
                    } else {
                        selectedRows.add(rowIndex);
                        this.classList.add('selected');
                    }
                });
            });
        }

        function setupEventListeners() {
            // Right click context menu
            document.addEventListener('contextmenu', function(e) {
                if (e.target.closest('.grid-table')) {
                    e.preventDefault();
                    showContextMenu(e.pageX, e.pageY);
                }
            });

            // Hide context menu
            document.addEventListener('click', function() {
                hideContextMenu();
            });

            // Editable cells
            document.querySelectorAll('.editable').forEach(cell => {
                cell.addEventListener('dblclick', function() {
                    if (editMode) {
                        editCell(this);
                    }
                });
            });
        }

        // Sorting functionality
        function sortTable(column) {
            const direction = sortDirection[column] === 'asc' ? 'desc' : 'asc';
            sortDirection = {}; // Reset other sorts
            sortDirection[column] = direction;

            // Update header indicators
            document.querySelectorAll('.sortable').forEach(header => {
                header.classList.remove('sort-asc', 'sort-desc');
            });

            const header = document.querySelector(`[data-column="${column}"]`);
            header.classList.add(`sort-${direction}`);

            // Sort rows
            const tbody = document.getElementById('gridBody');
            const rows = Array.from(tbody.querySelectorAll('tr:not(.summary-row)'));

            rows.sort((a, b) => {
                const aValue = a.querySelector(`[data-field="${column}"]`)?.textContent.trim() ||
                              a.children[getColumnIndex(column)]?.textContent.trim() || '';
                const bValue = b.querySelector(`[data-field="${column}"]`)?.textContent.trim() ||
                              b.children[getColumnIndex(column)]?.textContent.trim() || '';

                // Try numeric comparison first
                const aNum = parseFloat(aValue.replace(/[^\d.-]/g, ''));
                const bNum = parseFloat(bValue.replace(/[^\d.-]/g, ''));

                if (!isNaN(aNum) && !isNaN(bNum)) {
                    return direction === 'asc' ? aNum - bNum : bNum - aNum;
                }

                // Fallback to string comparison
                return direction === 'asc' ?
                    aValue.localeCompare(bValue, 'tr', { numeric: true }) :
                    bValue.localeCompare(aValue, 'tr', { numeric: true });
            });

            // Remove existing rows
            rows.forEach(row => row.remove());

            // Re-append sorted rows
            const summaryRow = tbody.querySelector('.summary-row');
            rows.forEach(row => tbody.insertBefore(row, summaryRow));

            // Update row numbers
            updateRowNumbers();
        }

        function getColumnIndex(column) {
            const headers = document.querySelectorAll('.grid-table th');
            return Array.from(headers).findIndex(header => header.dataset.column === column);
        }

        function updateRowNumbers() {
            document.querySelectorAll('#gridBody tr:not(.summary-row)').forEach((row, index) => {
                const numberCell = row.querySelector('.col-no');
                if (numberCell) {
                    numberCell.textContent = index + 1;
                }
            });
        }

        // Edit functionality
        function toggleEditMode() {
            editMode = !editMode;
            const btn = document.getElementById('editModeBtn');

            if (editMode) {
                btn.textContent = '💾 Kaydet';
                btn.classList.add('active');
                document.querySelectorAll('.editable').forEach(cell => {
                    cell.style.backgroundColor = 'rgba(0, 124, 186, 0.05)';
                });
            } else {
                btn.textContent = '✏️ Düzenle';
                btn.classList.remove('active');
                document.querySelectorAll('.editable').forEach(cell => {
                    cell.style.backgroundColor = '';
                });
                saveChanges();
            }
        }

        function editCell(cell = null) {
            if (currentEditCell) return; // Already editing

            if (!cell) {
                // Find selected cell or use context
                cell = document.querySelector('.selected .editable') || currentContextCell;
            }

            if (!cell) return;

            currentEditCell = cell;
            const originalValue = cell.textContent.trim();

            // Create input
            const input = document.createElement('input');
            input.type = 'text';
            input.value = originalValue;

            cell.classList.add('editing');
            cell.innerHTML = '';
            cell.appendChild(input);

            input.focus();
            input.select();

            // Handle save/cancel
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    saveEdit(cell, input.value);
                } else if (e.key === 'Escape') {
                    cancelEdit(cell, originalValue);
                }
            });

            input.addEventListener('blur', function() {
                saveEdit(cell, input.value);
            });
        }

        function saveEdit(cell, newValue) {
            if (!currentEditCell) return;

            const field = cell.dataset.field;
            const row = cell.closest('tr');
            const rowIndex = row.dataset.row;

            // Update cell content
            cell.classList.remove('editing');
            cell.textContent = newValue;

            // Mark as changed
            cell.classList.add('changed');

            // Update grid data
            if (gridData[rowIndex]) {
                gridData[rowIndex][field] = newValue;
            }

            currentEditCell = null;

            // Recalculate totals if needed
            if (['ADET', 'SATIS_FIYAT_BIRIM', 'TOPLAM_MALIYET'].includes(field)) {
                recalculateTotals();
            }
        }

        function cancelEdit(cell, originalValue) {
            if (!currentEditCell) return;

            cell.classList.remove('editing');
            cell.textContent = originalValue;
            currentEditCell = null;
        }

        // Context menu
        let currentContextCell = null;

        function showContextMenu(x, y) {
            const menu = document.getElementById('contextMenu');
            menu.style.left = x + 'px';
            menu.style.top = y + 'px';
            menu.style.display = 'block';

            // Store context
            currentContextCell = document.elementFromPoint(x, y);
        }

        function hideContextMenu() {
            document.getElementById('contextMenu').style.display = 'none';
        }

        function copyCell() {
            if (currentContextCell) {
                const text = currentContextCell.textContent.trim();
                navigator.clipboard.writeText(text).then(() => {
                    console.log('Kopyalandı:', text);
                });
            }
            hideContextMenu();
        }

        function deleteRow() {
            if (selectedRows.size > 0) {
                if (confirm(`${selectedRows.size} satırı silmek istediğinizden emin misiniz?`)) {
                    selectedRows.forEach(rowIndex => {
                        const row = document.querySelector(`[data-row="${rowIndex}"]`);
                        if (row) row.remove();
                    });
                    selectedRows.clear();
                    updateRowNumbers();
                    recalculateTotals();
                }
            }
            hideContextMenu();
        }

        function duplicateRow() {
            if (selectedRows.size === 1) {
                const rowIndex = Array.from(selectedRows)[0];
                const originalRow = document.querySelector(`[data-row="${rowIndex}"]`);
                if (originalRow) {
                    const newRow = originalRow.cloneNode(true);
                    const newIndex = document.querySelectorAll('#gridBody tr:not(.summary-row)').length;
                    newRow.dataset.row = newIndex;

                    // Insert before summary row
                    const summaryRow = document.querySelector('.summary-row');
                    summaryRow.parentNode.insertBefore(newRow, summaryRow);

                    updateRowNumbers();
                    recalculateTotals();
                }
            }
            hideContextMenu();
        }

        // Utility functions
        function recalculateTotals() {
            // This would recalculate summary row totals
            console.log('Recalculating totals...');
        }

        function exportToExcel() {
            console.log('Excel export triggered');
            alert('Excel export özelliği geliştirilecek.');
        }

        function refreshGrid() {
            window.location.reload();
        }

        function saveChanges() {
            const changes = [];
            document.querySelectorAll('.changed').forEach(cell => {
                const field = cell.dataset.field;
                const row = cell.closest('tr');
                const sku = row.dataset.sku;
                const newValue = cell.textContent.trim();

                changes.push({
                    sku: sku,
                    field: field,
                    value: newValue
                });
            });

            if (changes.length > 0) {
                console.log('Saving changes:', changes);
                // Here you would send changes to server
                // fetch('/save-changes.php', { method: 'POST', body: JSON.stringify(changes) })
                alert(`${changes.length} değişiklik kaydedilecek (henüz aktif değil)`);
            }
        }

        function adjustIframeHeight() {
            if (window.parent !== window) {
                function adjustHeight() {
                    const height = Math.max(document.body.scrollHeight, 600);
                    try {
                        window.parent.postMessage({
                            type: 'resize',
                            height: height
                        }, '*');
                    } catch (e) {
                        try {
                            const iframe = window.parent.document.getElementById('productGrid');
                            if (iframe) {
                                iframe.style.height = (height + 20) + 'px';
                            }
                        } catch (e) {
                            // Cross-origin restriction
                        }
                    }
                }

                adjustHeight();
                window.addEventListener('resize', adjustHeight);
                setTimeout(adjustHeight, 500);
            }
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                switch(e.key) {
                    case 's':
                        e.preventDefault();
                        if (editMode) saveChanges();
                        break;
                    case 'a':
                        e.preventDefault();
                        // Select all rows
                        document.querySelectorAll('#gridBody tr:not(.summary-row)').forEach(row => {
                            selectedRows.add(row.dataset.row);
                            row.classList.add('selected');
                        });
                        break;
                }
            }

            if (e.key === 'Escape') {
                selectedRows.clear();
                document.querySelectorAll('.selected').forEach(row => row.classList.remove('selected'));
            }
        });
    </script>
</body>
</html>