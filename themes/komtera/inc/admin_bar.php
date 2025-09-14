<?php

add_action('admin_bar_menu', function ($wp_admin_bar) {
    $icon_url = 'https://erp.komtera.com/wp-content/uploads/2025/08/komtera_logo.png';
    $wp_admin_bar->add_node([
        'id' => 'my-mini-icon',
        'title' => '<img src="' . esc_url($icon_url) . '" style="height:22px; width:auto; vertical-align:middle;" alt=""/>',
        'href' => admin_url(),
    ]);
    
    // Arama kutusu ekle
    $wp_admin_bar->add_node([
        'id' => 'search-box',
        'title' => '<input type="text" id="admin-search" placeholder="Ara..." style="padding: 1px 5px; border: 1px solid #ccc; border-radius: 3px; font-size: 12px; width: 350px; height: 12px; line-height: 12px;" onkeydown="handleSearchKeydown(event, this.value)">',
        'href' => false,
    ]);
    
    $wp_admin_bar->remove_node('wp-logo');
    $wp_admin_bar->remove_node('site-name');
    $wp_admin_bar->remove_node('help');
}, 999);

// Sağ üstteki yardım tab'ını kaldır
add_action('admin_head', function () {
    echo '<style>
        #contextual-help-link-wrap {
        display: none !important;
        }
        #wpadminbar {
        height: calc(32px + 6px) !important;
        }
        #wpcontent, #wpfooter {
        margin-left: 160px;
        }
        html.wp-toolbar {
        padding-top: calc(32px + 16px) !important;
        }
        /* Admin bar arama kutusunu ortala */
        #wp-admin-bar-search-box {
            position: absolute !important;
            left: 50% !important;
            top: calc(50% + 21px) !important;
            transform: translate(-50%, -50%) !important;
        }
        #wp-admin-bar-search-box .ab-item {
            padding: 0 !important;
            height: auto !important;
        }
        
        /* Arama kutusunun text boşluklarını minimize et */
        #admin-search {
            padding: 0px 5px !important;
            line-height: 1 !important;
            height: 10px !important;
            box-sizing: border-box !important;
        }
        
        @media screen and (max-width: 782px) {
        html.wp-toolbar {
            padding-top: calc(46px + 16px) !important;
        }
        #wpadminbar {
            height: calc(46px + 6px) !important;
        }
        #wp-admin-bar-search-box {
            display: none !important;
        }
        }
    </style>';
    
    echo '<script>
        let isSearchModalOpen = false;
        
        function handleSearchKeydown(event, query) {
            if (event.key === "Enter") {
                event.preventDefault();
                event.stopPropagation();
                if (query.trim()) {
                    adminSearch(query);
                }
                return false;
            }
        }
        
        function adminSearch(query) {
            if(query.trim() && !isSearchModalOpen) {
                // Modal pencere oluştur ve AJAX ile arama yap
                showSearchResults(query);
            }
        }
        
        function showSearchResults(query) {
            // Önceki modal varsa kapat
            closeSearchModal();
            
            // Modal açık olarak işaretle
            isSearchModalOpen = true;
            
            // Modal HTML oluştur
            const modalHtml = `
                <div id="search-modal" style="
                    position: fixed; 
                    top: 0; 
                    left: 0; 
                    width: 100%; 
                    height: 100%; 
                    background: rgba(0,0,0,0.5); 
                    z-index: 999999;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                ">
                    <div style="
                        background: white; 
                        padding: 20px; 
                        border-radius: 8px; 
                        width: 900px; 
                        height: 500px; 
                        overflow-y: auto;
                        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
                    ">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <h3 style="margin: 0;">Arama Sonuçları: "${query}"</h3>
                            <button onclick="event.stopPropagation(); closeSearchModal(); return false;" style="
                                background: #dc3545; 
                                color: white; 
                                border: none; 
                                padding: 5px 10px; 
                                border-radius: 3px; 
                                cursor: pointer;
                            ">Kapat</button>
                        </div>
                        <div id="search-results-content">
                            <p>Aranıyor...</p>
                        </div>
                    </div>
                </div>
            `;
            
            // Modal\'ı sayfaya ekle
            document.body.insertAdjacentHTML("beforeend", modalHtml);
            
            // Modal dışına tıklanırsa kapat
            document.getElementById("search-modal").onclick = function(e) {
                if (e.target.id === "search-modal") {
                    closeSearchModal();
                }
            };
            
            // AJAX arama yap
            fetch("' . admin_url('admin-ajax.php') . '", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: "action=admin_search&query=" + encodeURIComponent(query)
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById("search-results-content").innerHTML = data;
            })
            .catch(error => {
                document.getElementById("search-results-content").innerHTML = "<p>Arama sırasında hata oluştu.</p>";
            });
        }
        
        function closeSearchModal() {
            // Modal kapalı olarak işaretle
            isSearchModalOpen = false;
            
            // Tüm modalları kapat (eğer birden fazla varsa)
            const modals = document.querySelectorAll("[id^=\'search-modal\']");
            modals.forEach(modal => {
                if (modal && modal.parentNode) {
                    modal.parentNode.removeChild(modal);
                }
            });
            
            // Specific modalı da kontrol et
            const specificModal = document.getElementById("search-modal");
            if (specificModal && specificModal.parentNode) {
                specificModal.parentNode.removeChild(specificModal);
            }
        }
        
        // Sadece ESC tuşu ile modal\'ı kapat (Enter çift tetiklemeyi önlemek için)
        document.addEventListener("keydown", function(e) {
            if (e.key === "Escape") {
                if (document.getElementById("search-modal")) {
                    closeSearchModal();
                }
            }
        });
    </script>';
});

// AJAX arama handler'ı
add_action('wp_ajax_admin_search', 'handle_admin_search');
add_action('wp_ajax_nopriv_admin_search', 'handle_admin_search');

function handle_admin_search() {
    $query = sanitize_text_field($_POST['query']);
    
    if (empty($query)) {
        echo '<p>Arama terimi boş olamaz.</p>';
        wp_die();
    }
    
    $args = array(
        'post_type' => array('post', 'page'),
        's' => $query,
        'posts_per_page' => 10,
        'post_status' => 'publish'
    );
    
    $search_results = new WP_Query($args);
    
    if ($search_results->have_posts()) {
        echo '<table style="width: 100%; border-collapse: collapse;">';
        echo '<thead>';
        echo '<tr style="background: #dc3545; color: white;">';
        echo '<th style="padding: 10px; text-align: left;">Başlık</th>';
        echo '<th style="padding: 10px; text-align: left;">Tür</th>';
        echo '<th style="padding: 10px; text-align: left;">Tarih</th>';
        echo '<th style="padding: 10px; text-align: left;">İşlemler</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        while ($search_results->have_posts()) {
            $search_results->the_post();
            echo '<tr style="border-bottom: 1px solid #ddd;">';
            echo '<td style="padding: 8px;">' . get_the_title() . '</td>';
            echo '<td style="padding: 8px;">' . get_post_type() . '</td>';
            echo '<td style="padding: 8px;">' . get_the_date('d.m.Y') . '</td>';
            echo '<td style="padding: 8px;">';
            echo '<a href="' . get_permalink() . '" target="_blank" style="background: #dc3545; color: white; padding: 4px 8px; text-decoration: none; border-radius: 3px; font-size: 12px;">Görüntüle</a>';
            echo '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        wp_reset_postdata();
    } else {
        echo '<div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 5px;">';
        echo '<h4>Sonuç bulunamadı</h4>';
        echo '<p>Arama kriterlerinize uygun içerik bulunamadı.</p>';
        echo '</div>';
    }
    
    wp_die();
}