/**
 * Heartbeat Monitor
 *
 * Monitors server connectivity and displays modal on connection loss
 * Checks every 10 seconds, shows modal after 2 consecutive failures
 */

(function() {
    'use strict';

    const HeartbeatMonitor = {
        config: {
            checkInterval: 10000,        // 10 seconds
            timeout: 5000,               // 5 second timeout
            failureThreshold: 2,         // Show modal after 2 failures
            heartbeatUrl: '/themes/komtera/heartbeat.php'
        },

        state: {
            consecutiveFailures: 0,
            isModalShown: false,
            checkIntervalId: null,
            lastSuccessTime: Date.now()
        },

        init: function() {
            console.log('[Heartbeat] Monitor başlatıldı');
            this.startMonitoring();
            this.createModal();
        },

        startMonitoring: function() {
            // İlk check'i hemen yap
            this.checkHeartbeat();

            // Periyodik check'leri başlat
            this.state.checkIntervalId = setInterval(() => {
                this.checkHeartbeat();
            }, this.config.checkInterval);
        },

        checkHeartbeat: function() {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), this.config.timeout);

            fetch(this.config.heartbeatUrl, {
                method: 'GET',
                cache: 'no-cache',
                signal: controller.signal
            })
            .then(response => {
                clearTimeout(timeoutId);

                if (response.ok) {
                    return response.json();
                } else {
                    throw new Error('Server returned ' + response.status);
                }
            })
            .then(data => {
                if (data.status === 'ok') {
                    this.onSuccess();
                } else {
                    this.onFailure('Invalid response');
                }
            })
            .catch(error => {
                clearTimeout(timeoutId);
                this.onFailure(error.message);
            });
        },

        onSuccess: function() {
            this.state.consecutiveFailures = 0;
            this.state.lastSuccessTime = Date.now();

            // Modal gösterilmişse kapat
            if (this.state.isModalShown) {
                console.log('[Heartbeat] Bağlantı yeniden kuruldu');
                this.hideModal();
            }
        },

        onFailure: function(reason) {
            this.state.consecutiveFailures++;

            console.warn(
                `[Heartbeat] Bağlantı hatası (${this.state.consecutiveFailures}/${this.config.failureThreshold}):`,
                reason
            );

            // Threshold aşıldıysa modal göster
            if (this.state.consecutiveFailures >= this.config.failureThreshold && !this.state.isModalShown) {
                this.showModal();
            }
        },

        createModal: function() {
            const modalHTML = `
                <div id="heartbeat-modal" class="heartbeat-modal" style="display: none;">
                    <div class="heartbeat-modal-backdrop"></div>
                    <div class="heartbeat-modal-content">
                        <div class="heartbeat-modal-icon">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                        </div>
                        <h2 class="heartbeat-modal-title">Bağlantı Kesildi</h2>
                        <p class="heartbeat-modal-message">
                            Sunucu ile bağlantınız sonlanmıştır.<br>
                            Lütfen VPN bağlantınızı kontrol edin ve tekrar giriş yapmayı deneyin.
                        </p>
                        <div class="heartbeat-modal-actions">
                            <button id="heartbeat-reconnect-btn" class="heartbeat-btn heartbeat-btn-primary">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="23 4 23 10 17 10"></polyline>
                                    <polyline points="1 20 1 14 7 14"></polyline>
                                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                                </svg>
                                Yeniden Dene
                            </button>
                            <button id="heartbeat-reload-btn" class="heartbeat-btn heartbeat-btn-secondary">
                                Sayfayı Yenile
                            </button>
                        </div>
                        <div class="heartbeat-modal-footer">
                            Son başarılı bağlantı: <span id="heartbeat-last-success">-</span>
                        </div>
                    </div>
                </div>
            `;

            // Modal'ı DOM'a ekle
            const div = document.createElement('div');
            div.innerHTML = modalHTML;
            document.body.appendChild(div.firstElementChild);

            // Event listeners
            document.getElementById('heartbeat-reconnect-btn').addEventListener('click', () => {
                this.checkHeartbeat();
            });

            document.getElementById('heartbeat-reload-btn').addEventListener('click', () => {
                window.location.reload();
            });

            // CSS ekle
            this.injectStyles();
        },

        showModal: function() {
            if (this.state.isModalShown) return;

            console.error('[Heartbeat] Bağlantı kesildi - Modal gösteriliyor');

            const modal = document.getElementById('heartbeat-modal');
            const lastSuccessEl = document.getElementById('heartbeat-last-success');

            // Son başarılı bağlantı zamanını göster
            const lastSuccessDate = new Date(this.state.lastSuccessTime);
            lastSuccessEl.textContent = lastSuccessDate.toLocaleTimeString('tr-TR');

            modal.style.display = 'block';
            this.state.isModalShown = true;

            // Body scroll'u kilitle
            document.body.style.overflow = 'hidden';
        },

        hideModal: function() {
            if (!this.state.isModalShown) return;

            const modal = document.getElementById('heartbeat-modal');
            modal.style.display = 'none';
            this.state.isModalShown = false;

            // Body scroll'u aç
            document.body.style.overflow = '';
        },

        injectStyles: function() {
            const styles = `
                .heartbeat-modal {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    z-index: 999999;
                }

                .heartbeat-modal-backdrop {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.75);
                    backdrop-filter: blur(4px);
                }

                .heartbeat-modal-content {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    background: white;
                    border-radius: 12px;
                    padding: 32px;
                    max-width: 480px;
                    width: 90%;
                    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                    animation: heartbeat-modal-in 0.3s ease-out;
                }

                @keyframes heartbeat-modal-in {
                    from {
                        opacity: 0;
                        transform: translate(-50%, -45%);
                    }
                    to {
                        opacity: 1;
                        transform: translate(-50%, -50%);
                    }
                }

                .heartbeat-modal-icon {
                    text-align: center;
                    color: #ef4444;
                    margin-bottom: 16px;
                }

                .heartbeat-modal-title {
                    text-align: center;
                    font-size: 24px;
                    font-weight: 600;
                    color: #1f2937;
                    margin: 0 0 12px 0;
                }

                .heartbeat-modal-message {
                    text-align: center;
                    color: #6b7280;
                    line-height: 1.6;
                    margin: 0 0 24px 0;
                }

                .heartbeat-modal-actions {
                    display: flex;
                    gap: 12px;
                    margin-bottom: 16px;
                }

                .heartbeat-btn {
                    flex: 1;
                    padding: 12px 24px;
                    border: none;
                    border-radius: 8px;
                    font-size: 14px;
                    font-weight: 500;
                    cursor: pointer;
                    transition: all 0.2s;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 8px;
                }

                .heartbeat-btn-primary {
                    background: #3b82f6;
                    color: white;
                }

                .heartbeat-btn-primary:hover {
                    background: #2563eb;
                    transform: translateY(-1px);
                    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
                }

                .heartbeat-btn-secondary {
                    background: #f3f4f6;
                    color: #374151;
                }

                .heartbeat-btn-secondary:hover {
                    background: #e5e7eb;
                }

                .heartbeat-modal-footer {
                    text-align: center;
                    font-size: 12px;
                    color: #9ca3af;
                    padding-top: 16px;
                    border-top: 1px solid #e5e7eb;
                }

                #heartbeat-last-success {
                    font-weight: 600;
                    color: #6b7280;
                }
            `;

            const styleSheet = document.createElement('style');
            styleSheet.textContent = styles;
            document.head.appendChild(styleSheet);
        },

        destroy: function() {
            if (this.state.checkIntervalId) {
                clearInterval(this.state.checkIntervalId);
            }
            console.log('[Heartbeat] Monitor durduruldu');
        }
    };

    // Sayfa yüklendiğinde başlat
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => HeartbeatMonitor.init());
    } else {
        HeartbeatMonitor.init();
    }

    // Global erişim için
    window.HeartbeatMonitor = HeartbeatMonitor;

})();
