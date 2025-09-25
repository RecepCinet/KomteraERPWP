<?php
// MS SQL için: TOP 1 + ORDER BY
function aa_get_latest_kur(){
    global $conn;
    $stmt = $conn->prepare("
        SELECT TOP 1 usd, eur, tarih
        FROM aa_erp_kur
        ORDER BY tarih DESC
    ");
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['usd'=>null,'eur'=>null,'tarih'=>null];
}

function kur_dashboard_widget() {
    $data  = aa_get_latest_kur();
    $usd   = $data['usd'];
    $eur   = $data['eur'];
    $tarih = $data['tarih'];

    // Tarih formatla
    $formatted_date = '';
    if ($tarih) {
        $t = is_numeric($tarih) ? (int)$tarih : strtotime($tarih);
        if ($t) $formatted_date = date('d.m.Y H:i', $t);
    }
    ?>
    <div class="kur-widget-content">
        <div class="kur-rates">
            <div class="kur-item">
                <span class="kur-symbol">$</span>
                <span class="kur-value"><?php echo number_format($usd, 4, ',', '.'); ?></span>
                <span class="kur-label">USD/TRY</span>
            </div>
            <div class="kur-item">
                <span class="kur-symbol">€</span>
                <span class="kur-value"><?php echo number_format($eur, 4, ',', '.'); ?></span>
                <span class="kur-label">EUR/TRY</span>
            </div>
            <div class="kur-item">
                <span class="kur-symbol">Parity</span>
                <span class="kur-value"><?php echo number_format(($eur && $usd) ? $eur / $usd : 0, 4, ',', '.'); ?></span>
                <span class="kur-label">EUR/USD</span>
            </div>
        </div>

        <!-- Hızlı Çevirme Butonu -->
        <div class="kur-converter-section">
            <button type="button" id="openConverter" class="kur-converter-btn">
                💱 Hızlı Çevirme Aracı
            </button>
        </div>
    </div>

    <!-- Modal Popup -->
    <div id="kurConverterModal" class="kur-modal">
        <div class="kur-modal-content">
            <div class="kur-modal-header">
                <h3>Hızlı Para Çevirme</h3>
                <span class="kur-modal-close">&times;</span>
            </div>
            <div class="kur-modal-body">
                <div class="kur-form-group">
                    <label>Kaynak Para Birimi:</label>
                    <select id="sourceCurrency">
                        <option value="TRY">TRY (Türk Lirası)</option>
                        <option value="USD">USD (Amerikan Doları)</option>
                        <option value="EUR">EUR (Euro)</option>
                    </select>
                </div>

                <div class="kur-form-group">
                    <label>Hedef Para Birimi:</label>
                    <select id="targetCurrency">
                        <option value="USD">USD (Amerikan Doları)</option>
                        <option value="EUR">EUR (Euro)</option>
                        <option value="TRY">TRY (Türk Lirası)</option>
                    </select>
                </div>

                <div class="kur-form-group">
                    <label>Miktar:</label>
                    <input type="number" id="amount" placeholder="0.00" step="1" min="0">
                    <span id="sourceLabel">TRY</span>
                </div>

                <div class="kur-result">
                    <div id="conversionResult">Sonuç burada görünecek</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Kur verilerini JavaScript'e aktar
        const kurData = {
            USD: <?php echo json_encode($usd); ?>,
            EUR: <?php echo json_encode($eur); ?>,
            date: <?php echo json_encode($formatted_date); ?>
        };

        // DOM yüklendikten sonra event listener'ları ekle
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('kurConverterModal');
            const openBtn = document.getElementById('openConverter');
            const closeBtn = document.querySelector('.kur-modal-close');
            const sourceCurrency = document.getElementById('sourceCurrency');
            const targetCurrency = document.getElementById('targetCurrency');
            const amountInput = document.getElementById('amount');
            const sourceLabel = document.getElementById('sourceLabel');
            const resultDiv = document.getElementById('conversionResult');

            // Modal açma
            openBtn.addEventListener('click', function() {
                modal.style.display = 'block';
                amountInput.focus();
            });

            // Modal kapatma - X butonu
            closeBtn.addEventListener('click', function() {
                modal.style.display = 'none';
            });

            // Modal kapatma - backdrop
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });

            // ESC tuşu ile kapatma
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.style.display === 'block') {
                    modal.style.display = 'none';
                }
            });

            // Kaynak para birimi değiştiğinde label güncelle
            sourceCurrency.addEventListener('change', function() {
                sourceLabel.textContent = this.value;
                calculate();
            });

            // Hedef para birimi değiştiğinde hesapla
            targetCurrency.addEventListener('change', calculate);

            // Miktar değiştiğinde hesapla
            amountInput.addEventListener('input', calculate);

            // Çevirme hesaplama fonksiyonu
            function calculate() {
                const amount = parseFloat(amountInput.value) || 0;
                const source = sourceCurrency.value;
                const target = targetCurrency.value;

                if (amount === 0) {
                    resultDiv.innerHTML = 'Miktar girin';
                    return;
                }

                if (source === target) {
                    resultDiv.innerHTML = `${formatNumber(amount)} ${target}`;
                    return;
                }

                let result = 0;
                let rate = 0;

                // Çevirme mantığı
                if (source === 'TRY' && target === 'USD') {
                    result = amount / kurData.USD;
                    rate = kurData.USD;
                } else if (source === 'TRY' && target === 'EUR') {
                    result = amount / kurData.EUR;
                    rate = kurData.EUR;
                } else if (source === 'USD' && target === 'TRY') {
                    result = amount * kurData.USD;
                    rate = kurData.USD;
                } else if (source === 'EUR' && target === 'TRY') {
                    result = amount * kurData.EUR;
                    rate = kurData.EUR;
                } else if (source === 'USD' && target === 'EUR') {
                    result = (amount * kurData.USD) / kurData.EUR;
                    rate = kurData.EUR / kurData.USD;
                } else if (source === 'EUR' && target === 'USD') {
                    result = (amount * kurData.EUR) / kurData.USD;
                    rate = kurData.USD / kurData.EUR;
                }

                resultDiv.innerHTML = `
                    <div style="font-size: 20px; margin-bottom: 8px;">
                        ${formatNumber(result, 4)} ${target}
                    </div>
                    <div style="font-size: 12px; color: #646970;">
                        Kur: 1 ${source} = ${formatNumber(rate, 4)} ${target}
                    </div>
                `;
            }

            // Sayı formatlama fonksiyonu
            function formatNumber(num, decimals = 2) {
                return new Intl.NumberFormat('tr-TR', {
                    minimumFractionDigits: decimals,
                    maximumFractionDigits: decimals
                }).format(num);
            }

            // İlk yükleme için hesapla
            calculate();
        });
    </script>
    <?php
}

function add_kur_dashboard_widget() {
    wp_add_dashboard_widget(
        'kur_widget',
        'Döviz Kurları',
        'kur_dashboard_widget'
    );
}
add_action('wp_dashboard_setup', 'add_kur_dashboard_widget');

add_action('admin_head', function() {
    echo '<style>
        .kur-widget-content {
            padding: 10px 0;
        }

        .kur-rates {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 15px;
        }

        .kur-item {
            text-align: center;
            background: #f8f9fa;
            border: 1px solid #e0e6ed;
            border-radius: 8px;
            padding: 12px 8px;
            flex: 1;
            min-width: 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
        }

        .kur-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }

        .kur-symbol {
            display: block;
            font-size: 24px;
            font-weight: bold;
            color: #0073aa;
            margin-bottom: 5px;
        }

        .kur-value {
            display: block;
            font-size: 18px;
            font-weight: 600;
            color: #1d2327;
            margin-bottom: 3px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        .kur-label {
            display: block;
            font-size: 12px;
            color: #646970;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kur-date {
            text-align: center;
            padding: 8px 0;
            border-top: 1px solid #f0f0f1;
            margin-top: 10px;
        }

        .kur-date small {
            color: #646970;
            font-size: 11px;
        }

        /* Responsive */
        @media screen and (max-width: 950px) {
            .kur-rates {
                flex-direction: column;
                gap: 10px;
            }

            .kur-item {
                min-width: auto;
                width: 100%;
                flex: none;
            }
        }

        @media screen and (min-width: 951px) and (max-width: 1200px) {
            .kur-symbol {
                font-size: 20px;
            }

            .kur-value {
                font-size: 16px;
            }
        }

        /* Çevirme Butonu */
        .kur-converter-section {
            text-align: center;
            margin: 15px 0 10px 0;
            padding-top: 15px;
            border-top: 1px solid #e0e6ed;
        }

        .kur-converter-btn {
            background: linear-gradient(135deg, #0073aa, #005177);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,115,170,0.2);
        }

        .kur-converter-btn:hover {
            background: linear-gradient(135deg, #005177, #003d5c);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,115,170,0.3);
        }

        /* Modal Styles */
        .kur-modal {
            display: none;
            position: fixed;
            z-index: 999999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(2px);
        }

        .kur-modal-content {
            position: relative;
            background: white;
            margin: 10% auto;
            width: 90%;
            max-width: 400px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from { opacity: 0; transform: translateY(-50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .kur-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: #0073aa;
            color: white;
            border-radius: 12px 12px 0 0;
        }

        .kur-modal-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .kur-modal-close {
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.2s;
        }

        .kur-modal-close:hover {
            opacity: 1;
        }

        .kur-modal-body {
            padding: 24px;
        }

        .kur-form-group {
            margin-bottom: 18px;
        }

        .kur-form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #1d2327;
            font-size: 14px;
        }

        .kur-form-group select,
        .kur-form-group input {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e0e6ed;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.2s;
            font-family: inherit;
        }

        .kur-form-group select:focus,
        .kur-form-group input:focus {
            outline: none;
            border-color: #0073aa;
            box-shadow: 0 0 0 2px rgba(0,115,170,0.1);
        }

        .kur-form-group:nth-child(3) {
            position: relative;
        }

        #sourceLabel {
            position: absolute;
            right: 32px;
            top: 32px;
            color: #646970;
            font-size: 14px;
            font-weight: 600;
            pointer-events: none;
        }

        .kur-result {
            background: #f8f9fa;
            border: 2px solid #e0e6ed;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
            margin-top: 20px;
        }

        #conversionResult {
            font-size: 18px;
            font-weight: 600;
            color: #0073aa;
            min-height: 24px;
        }

        /* Mobile Responsive */
        @media screen and (max-width: 480px) {
            .kur-modal-content {
                margin: 5% auto;
                width: 95%;
            }

            .kur-modal-header,
            .kur-modal-body {
                padding: 16px;
            }
        }
    </style>';
});
