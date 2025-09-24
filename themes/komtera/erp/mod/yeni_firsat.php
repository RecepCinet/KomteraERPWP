<?php
// WordPress integration
$dir = __DIR__;
$found = false;
for ($i = 0; $i < 10; $i++) {
    if (file_exists($dir . '/wp-load.php')) {
        require_once $dir . '/wp-load.php';
        $found = true;
        break;
    }
    $dir = dirname($dir);
}

if (!$found) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "wp-load.php bulunamadı.\n";
    echo "Başlangıç dizini: " . __DIR__ . "\n";
    exit;
}

// User authentication check
if (!is_user_logged_in()) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttrequest') {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Oturum süreniz dolmuş. Lütfen tekrar giriş yapın.']);
        exit;
    } else {
        wp_redirect(wp_login_url());
        exit;
    }
}

// Database connection
include dirname(__DIR__) . '/_conn.php';

// Get current user info
$current_user = wp_get_current_user();
$user = $current_user->user_login;

// Form submit işlemi
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'save_firsat') {
    header('Content-Type: application/json');
    try {
        $marka = $_POST['marka'] ?? '';
        $gelis_kanali = $_POST['gelis_kanali'] ?? '';
        $olasilik = $_POST['olasilik'] ?? '';
        $proje_adi = $_POST['proje_adi'] ?? '';
        $firsat_aciklama = $_POST['firsat_aciklama'] ?? '';
        $bayi = $_POST['bayi'] ?? '';
        $bayi_kodu = $_POST['bayi_kodu'] ?? '';
        $bayi_yetkili = $_POST['bayi_yetkili'] ?? '';
        $musteri = $_POST['musteri'] ?? '';
        $musteri_yetkili = $_POST['musteri_yetkili'] ?? '';
        $musteri_temsilcisi = $_POST['musteri_temsilcisi'] ?? '';
        $accman = $_POST['accman'] ?? '';
        $etkinlik = $_POST['etkinlik'] ?? '';
        $register = isset($_POST['register']) ? '1' : '0';
        $register_dr_no = $_POST['register_dr_no'] ?? '';

        // Fırsat numarası oluşturma - son Fırsat No'ya random ekleyerek
        try {
            // Son fırsat numarasını al
            $lastFirsatSql = "SELECT TOP 1 FIRSAT_NO FROM aa_erp_kt_firsatlar ORDER BY BASLANGIC_TARIHI DESC";
            $lastFirsatStmt = $conn->query($lastFirsatSql);
            $lastFirsat = $lastFirsatStmt->fetch(PDO::FETCH_ASSOC);

            if ($lastFirsat && $lastFirsat['FIRSAT_NO']) {
                // Son fırsat no'dan tarih kısmını çıkar
                // Örnek: F20250924-123 -> 20250924
                preg_match('/F(\d{8})/', $lastFirsat['FIRSAT_NO'], $matches);
                if (!empty($matches[1])) {
                    $lastDate = intval($matches[1]);
                    // 2 ile 12 arası random sayı ekle
                    $randomAdd = rand(2, 12);
                    $newDate = $lastDate + $randomAdd;
                    $firsat_no = 'F' . $newDate;
                } else {
                    // Eğer parse edilemezse varsayılan yöntem
                    $firsat_no = 'F' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
                }
            } else {
                // Hiç kayıt yoksa varsayılan yöntem
                $firsat_no = 'F' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            }
        } catch (Exception $e) {
            // Hata durumunda varsayılan yöntem
            $firsat_no = 'F' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        }

        $sql = "INSERT INTO aa_erp_kt_firsatlar (
            FIRSAT_NO,
            MARKA,
            GELIS_KANALI,
            OLASILIK,
            MUSTERI_TEMSILCISI,
            PROJE_ADI,
            FIRSAT_ACIKLAMA,
            BAYI_ADI,
            BAYI_CHKODU,
            BAYI_YETKILI_TEL,
            MUSTERI_ADI,
            MUSTERI_YETKILI_TEL,
            ETKINLIK,
            REGISTER,
            KAYIDI_ACAN,
            BASLANGIC_TARIHI,
            REVIZE_TARIHI,
            DURUM
        ) VALUES (
            :firsat_no,
            :marka,
            :gelis_kanali,
            :olasilik,
            :musteri_temsilcisi,
            :proje_adi,
            :firsat_aciklama,
            :bayi,
            :bayi_kodu,
            :bayi_yetkili,
            :musteri,
            :musteri_yetkili,
            :etkinlik,
            :register,
            :kayidi_acan,
            GETDATE(),
            DATEADD(day, 30, GETDATE()),
            '0'
        )";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':firsat_no', $firsat_no);
        $stmt->bindParam(':marka', $marka);
        $stmt->bindParam(':gelis_kanali', $gelis_kanali);
        $stmt->bindParam(':olasilik', $olasilik);
        $stmt->bindParam(':musteri_temsilcisi', $musteri_temsilcisi);
        $stmt->bindParam(':proje_adi', $proje_adi);
        $stmt->bindParam(':firsat_aciklama', $firsat_aciklama);
        $stmt->bindParam(':bayi', $bayi);
        $stmt->bindParam(':bayi_kodu', $bayi_kodu);
        $stmt->bindParam(':bayi_yetkili', $bayi_yetkili);
        $stmt->bindParam(':musteri', $musteri);
        $stmt->bindParam(':musteri_yetkili', $musteri_yetkili);
        $stmt->bindParam(':etkinlik', $etkinlik);
        $stmt->bindParam(':register', $register);
        $stmt->bindParam(':kayidi_acan', $user);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Fırsat başarıyla oluşturuldu.', 'firsat_no' => $firsat_no]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Kayıt sırasında bir hata oluştu.']);
        }
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
        exit;
    }
}

// AJAX veri çekme işlemleri
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    try {
        if ($_GET['action'] === 'get_markalar') {
            $sql = "SELECT MARKA FROM aa_erp_kt_fiyat_listesi WHERE MARKA IS NOT NULL GROUP BY MARKA ORDER BY MARKA";
            $stmt = $conn->query($sql);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data);
            exit;
        }

        if ($_GET['action'] === 'search_markalar') {
            $query = $_GET['query'] ?? '';
            $mode = $_GET['mode'] ?? 'startswith';

            // Arama moduna göre search term oluştur
            if ($mode === 'startswith') {
                $searchTerm = $query . '%';
            } else { // contains (default)
                $searchTerm = '%' . $query . '%';
            }

            $sql = "SELECT MARKA FROM aa_erp_kt_fiyat_listesi WHERE MARKA LIKE :query AND MARKA IS NOT NULL GROUP BY MARKA ORDER BY MARKA";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':query', $searchTerm);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data);
            exit;
        }

        if ($_GET['action'] === 'get_bayiler') {
            $sql = "SELECT b.CH_KODU, b.CH_UNVANI, k.dikkat_listesi, k.kara_liste FROM aaa_erp_kt_bayiler b LEFT JOIN aa_erp_kt_bayiler_kara_liste k ON b.CH_KODU = k.ch_kodu ORDER BY b.CH_UNVANI";
            $stmt = $conn->query($sql);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // KOMTERA bayisini debug için işaretle
            foreach ($data as &$item) {
                if (stripos($item['CH_UNVANI'], 'KOMTERA') !== false) {
                    $item['debug'] = 'KOMTERA FOUND - CH_KODU: ' . $item['CH_KODU'];
                }
            }

            echo json_encode($data);
            exit;
        }

        if ($_GET['action'] === 'search_bayiler') {
            $query = $_GET['query'] ?? '';
            $mode = $_GET['mode'] ?? 'startswith';

            // Arama moduna göre search term oluştur
            if ($mode === 'startswith') {
                $searchTerm = $query . '%';
            } else { // contains (default)
                $searchTerm = '%' . $query . '%';
            }

            $sql = "SELECT b.CH_KODU, b.CH_UNVANI, k.dikkat_listesi, k.kara_liste FROM aaa_erp_kt_bayiler b LEFT JOIN aa_erp_kt_bayiler_kara_liste k ON b.CH_KODU = k.ch_kodu WHERE b.CH_UNVANI LIKE :query ORDER BY b.CH_UNVANI";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':query', $searchTerm);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data);
            exit;
        }

        if ($_GET['action'] === 'get_musteriler') {
            $sql = "SELECT m.id, m.musteri FROM aa_erp_kt_musteriler m WHERE musteri IS NOT NULL ORDER BY m.musteri";
            $stmt = $conn->query($sql);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data);
            exit;
        }

        if ($_GET['action'] === 'search_musteriler') {
            $query = $_GET['query'] ?? '';
            $mode = $_GET['mode'] ?? 'startswith';

            // Arama moduna göre search term oluştur
            if ($mode === 'startswith') {
                $searchTerm = $query . '%';
            } else { // contains (default)
                $searchTerm = '%' . $query . '%';
            }

            $sql = "SELECT m.id, m.musteri FROM aa_erp_kt_musteriler m WHERE m.musteri LIKE :query AND m.musteri IS NOT NULL ORDER BY m.musteri";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':query', $searchTerm);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data);
            exit;
        }

        if ($_GET['action'] === 'get_bayi_yetkililer') {
            $bayi_kodu = $_GET['bayi_kodu'] ?? '';
            try {
                if ($bayi_kodu) {
                    // Önce tabloyu kontrol et ve tablo yapısını öğren
                    $checkSql = "SELECT COUNT(*) as count FROM aa_erp_kt_bayiler_yetkililer WHERE CH_KODU = :bayi_kodu";
                    $checkStmt = $conn->prepare($checkSql);
                    $checkStmt->bindParam(':bayi_kodu', $bayi_kodu);
                    $checkStmt->execute();
                    $count = $checkStmt->fetch(PDO::FETCH_ASSOC);

                    // Tablo yapısını görmek için sample data çek
                    $sampleSql = "SELECT TOP 3 * FROM aa_erp_kt_bayiler_yetkililer";
                    $sampleStmt = $conn->query($sampleSql);
                    $sampleData = $sampleStmt->fetchAll(PDO::FETCH_ASSOC);

                    $sql = "SELECT * FROM aa_erp_kt_bayiler_yetkililer WHERE CH_KODU = :bayi_kodu ORDER BY yetkili";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':bayi_kodu', $bayi_kodu);
                    $stmt->execute();
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Test amaçlı sample data ekle (eğer veri yoksa) - herhangi bir bayi için
                    if (empty($data)) {
                        $data = [
                            [
                                'id' => 1,
                                'CH_KODU' => $bayi_kodu,
                                'yetkili' => 'Ahmet Yılmaz',
                                'telefon' => '0532 123 45 67',
                                'eposta' => 'ahmet@test.com'
                            ],
                            [
                                'id' => 2,
                                'CH_KODU' => $bayi_kodu,
                                'yetkili' => 'Ayşe Demir',
                                'telefon' => '0533 987 65 43',
                                'eposta' => 'ayse@test.com'
                            ]
                        ];
                    }

                    // Debug bilgisi ekle
                    $response = [
                        'success' => true,
                        'count' => $count['count'],
                        'bayi_kodu' => $bayi_kodu,
                        'sample_data' => $sampleData,
                        'data' => $data
                    ];
                    echo json_encode($response);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Bayi kodu boş', 'data' => []]);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage(), 'data' => []]);
            }
            exit;
        }

        if ($_GET['action'] === 'get_musteri_yetkililer') {
            $musteri_id = $_GET['musteri_id'] ?? '';
            if ($musteri_id) {
                try {
                    // Önce sample data görmek için
                    $sampleSql = "SELECT TOP 3 * FROM aa_erp_kt_musteriler_yetkililer";
                    $sampleStmt = $conn->query($sampleSql);
                    $sampleData = $sampleStmt->fetchAll(PDO::FETCH_ASSOC);

                    $sql = "SELECT * FROM aa_erp_kt_musteriler_yetkililer WHERE musteri_id = :musteri_id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':musteri_id', $musteri_id);
                    $stmt->execute();
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Test amaçlı sample data (eğer veri yoksa)
                    if (empty($data)) {
                        $data = [
                            [
                                'id' => 1,
                                'musteri_id' => $musteri_id,
                                'yetkili' => 'Mehmet Özkan',
                                'telefon' => '0534 111 22 33',
                                'eposta' => 'mehmet@musteri.com'
                            ],
                            [
                                'id' => 2,
                                'musteri_id' => $musteri_id,
                                'yetkili' => 'Fatma Kaya',
                                'telefon' => '0535 444 55 66',
                                'eposta' => 'fatma@musteri.com'
                            ]
                        ];
                    }

                    $response = [
                        'success' => true,
                        'musteri_id' => $musteri_id,
                        'sample_data' => $sampleData,
                        'data' => $data
                    ];
                    echo json_encode($response);
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'error' => $e->getMessage(), 'data' => []]);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Müşteri ID boş', 'data' => []]);
            }
            exit;
        }

        if ($_GET['action'] === 'save_bayi_yetkili') {
            $bayi_kodu = $_POST['bayi_kodu'] ?? '';
            $yetkili = $_POST['yetkili'] ?? '';
            $telefon = $_POST['telefon'] ?? '';
            $eposta = $_POST['eposta'] ?? '';

            try {
                if ($bayi_kodu && $yetkili) {
                    $sql = "INSERT INTO aa_erp_kt_bayiler_yetkililer (CH_KODU, yetkili, telefon, eposta) VALUES (:bayi_kodu, :yetkili, :telefon, :eposta)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':bayi_kodu', $bayi_kodu);
                    $stmt->bindParam(':yetkili', $yetkili);
                    $stmt->bindParam(':telefon', $telefon);
                    $stmt->bindParam(':eposta', $eposta);

                    if ($stmt->execute()) {
                        echo json_encode(['success' => true, 'message' => 'Bayi yetkili başarıyla eklendi.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Kayıt sırasında hata oluştu.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Bayi kodu ve yetkili adı gerekli.']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
            }
            exit;
        }

        if ($_GET['action'] === 'save_musteri_yetkili') {
            $musteri_id = $_POST['musteri_id'] ?? '';
            $yetkili = $_POST['yetkili'] ?? '';
            $telefon = $_POST['telefon'] ?? '';
            $eposta = $_POST['eposta'] ?? '';

            try {
                if ($musteri_id && $yetkili) {
                    $sql = "INSERT INTO aa_erp_kt_musteriler_yetkililer (musteri_id, yetkili, telefon, eposta) VALUES (:musteri_id, :yetkili, :telefon, :eposta)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':musteri_id', $musteri_id);
                    $stmt->bindParam(':yetkili', $yetkili);
                    $stmt->bindParam(':telefon', $telefon);
                    $stmt->bindParam(':eposta', $eposta);

                    if ($stmt->execute()) {
                        echo json_encode(['success' => true, 'message' => 'Müşteri yetkili başarıyla eklendi.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Kayıt sırasında hata oluştu.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Müşteri ID ve yetkili adı gerekli.']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
            }
            exit;
        }

        if ($_GET['action'] === 'update_bayi_yetkili') {
            $id = $_POST['id'] ?? '';
            $yetkili = $_POST['yetkili'] ?? '';
            $telefon = $_POST['telefon'] ?? '';
            $eposta = $_POST['eposta'] ?? '';

            try {
                if ($id && $yetkili) {
                    $sql = "UPDATE aa_erp_kt_bayiler_yetkililer SET yetkili = :yetkili, telefon = :telefon, eposta = :eposta WHERE id = :id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':id', $id);
                    $stmt->bindParam(':yetkili', $yetkili);
                    $stmt->bindParam(':telefon', $telefon);
                    $stmt->bindParam(':eposta', $eposta);

                    if ($stmt->execute()) {
                        echo json_encode(['success' => true, 'message' => 'Bayi yetkili başarıyla güncellendi.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Güncelleme sırasında hata oluştu.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'ID ve yetkili adı gerekli.']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
            }
            exit;
        }

        if ($_GET['action'] === 'update_musteri_yetkili') {
            $id = $_POST['id'] ?? '';
            $yetkili = $_POST['yetkili'] ?? '';
            $telefon = $_POST['telefon'] ?? '';
            $eposta = $_POST['eposta'] ?? '';

            try {
                if ($id && $yetkili) {
                    $sql = "UPDATE aa_erp_kt_musteriler_yetkililer SET yetkili = :yetkili, telefon = :telefon, eposta = :eposta WHERE id = :id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':id', $id);
                    $stmt->bindParam(':yetkili', $yetkili);
                    $stmt->bindParam(':telefon', $telefon);
                    $stmt->bindParam(':eposta', $eposta);

                    if ($stmt->execute()) {
                        echo json_encode(['success' => true, 'message' => 'Müşteri yetkili başarıyla güncellendi.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Güncelleme sırasında hata oluştu.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'ID ve yetkili adı gerekli.']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
            }
            exit;
        }

        if ($_GET['action'] === 'delete_bayi_yetkili') {
            $id = $_POST['id'] ?? '';

            try {
                if ($id) {
                    $sql = "DELETE FROM aa_erp_kt_bayiler_yetkililer WHERE id = :id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':id', $id);

                    if ($stmt->execute()) {
                        echo json_encode(['success' => true, 'message' => 'Bayi yetkili başarıyla silindi.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Silme sırasında hata oluştu.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'ID gerekli.']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
            }
            exit;
        }

        if ($_GET['action'] === 'delete_musteri_yetkili') {
            $id = $_POST['id'] ?? '';

            try {
                if ($id) {
                    $sql = "DELETE FROM aa_erp_kt_musteriler_yetkililer WHERE id = :id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':id', $id);

                    if ($stmt->execute()) {
                        echo json_encode(['success' => true, 'message' => 'Müşteri yetkili başarıyla silindi.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Silme sırasında hata oluştu.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'ID gerekli.']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
            }
            exit;
        }

        if ($_GET['action'] === 'get_musteri_temsilcileri') {
            $selected_marka = $_GET['marka'] ?? '';

            try {
                // my_brands meta'sına sahip kullanıcıları al
                $users = get_users(array(
                    'meta_key' => 'my_brands',
                    'meta_compare' => 'EXISTS'
                ));

                $filtered_users = array();

                foreach ($users as $wp_user) {
                    $user_brands = get_user_meta($wp_user->ID, 'my_brands', true);

                    // Eğer marka seçildiyse, sadece o markaya yetkili kullanıcıları göster
                    if ($selected_marka) {
                        $has_brand = false;
                        if (is_array($user_brands)) {
                            $has_brand = in_array($selected_marka, $user_brands);
                        } elseif (is_string($user_brands)) {
                            // Serialized array olabilir
                            $unserialized = maybe_unserialize($user_brands);
                            if (is_array($unserialized)) {
                                $has_brand = in_array($selected_marka, $unserialized);
                                $user_brands = $unserialized;
                            } else {
                                $has_brand = ($user_brands === $selected_marka);
                            }
                        }

                        if ($has_brand) {
                            $filtered_users[] = array(
                                'user_login' => $wp_user->user_login,
                                'brands' => $user_brands
                            );
                        }
                    } else {
                        // Marka seçili değilse tüm yetkili kullanıcıları göster
                        $filtered_users[] = array(
                            'user_login' => $wp_user->user_login,
                            'brands' => $user_brands
                        );
                    }
                }

                echo json_encode(['success' => true, 'data' => $filtered_users]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
            }
            exit;
        }

        if ($_GET['action'] === 'get_accman') {
            $selected_marka = $_GET['marka'] ?? '';

            try {
                // Gerçek tablo yapısı: marka, yetkili, telefon, eposta
                if ($selected_marka) {
                    // Seçili markaya göre filtrele
                    $sql = "SELECT * FROM aa_erp_kt_markalar_managers WHERE marka = :marka ORDER BY yetkili";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':marka', $selected_marka);
                    $stmt->execute();
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    // Tüm AccMan'leri getir
                    $sql = "SELECT * FROM aa_erp_kt_markalar_managers ORDER BY marka, yetkili";
                    $stmt = $conn->query($sql);
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }

                echo json_encode([
                    'success' => true,
                    'data' => $data,
                    'debug' => [
                        'selected_marka' => $selected_marka,
                        'count' => count($data),
                        'sql_used' => $selected_marka ? 'filtered' : 'all'
                    ]
                ]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
            }
            exit;
        }

        if ($_GET['action'] === 'save_accman') {
            $marka = $_POST['marka'] ?? '';
            $yetkili = $_POST['yetkili'] ?? '';
            $telefon = $_POST['telefon'] ?? '';
            $eposta = $_POST['eposta'] ?? '';

            try {
                if ($marka && $yetkili) {
                    // Gerçek tablo yapısı: marka, yetkili, telefon, eposta
                    $sql = "INSERT INTO aa_erp_kt_markalar_managers (marka, yetkili, telefon, eposta) VALUES (:marka, :yetkili, :telefon, :eposta)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':marka', $marka);
                    $stmt->bindParam(':yetkili', $yetkili);
                    $stmt->bindParam(':telefon', $telefon);
                    $stmt->bindParam(':eposta', $eposta);

                    if ($stmt->execute()) {
                        echo json_encode(['success' => true, 'message' => 'AccMan başarıyla eklendi.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Kayıt sırasında hata oluştu.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Marka ve yetkili adı gerekli.']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
            }
            exit;
        }

        if ($_GET['action'] === 'update_accman') {
            $id = $_POST['id'] ?? '';
            $yetkili = $_POST['yetkili'] ?? '';
            $telefon = $_POST['telefon'] ?? '';
            $eposta = $_POST['eposta'] ?? '';

            try {
                if ($id && $yetkili) {
                    $sql = "UPDATE aa_erp_kt_markalar_managers SET yetkili = :yetkili, telefon = :telefon, eposta = :eposta WHERE id = :id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':id', $id);
                    $stmt->bindParam(':yetkili', $yetkili);
                    $stmt->bindParam(':telefon', $telefon);
                    $stmt->bindParam(':eposta', $eposta);

                    if ($stmt->execute()) {
                        echo json_encode(['success' => true, 'message' => 'AccMan başarıyla güncellendi.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Güncelleme sırasında hata oluştu.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'ID ve yetkili adı gerekli.']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
            }
            exit;
        }

        if ($_GET['action'] === 'delete_accman') {
            $id = $_POST['id'] ?? '';

            try {
                if ($id) {
                    $sql = "DELETE FROM aa_erp_kt_markalar_managers WHERE id = :id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':id', $id);

                    if ($stmt->execute()) {
                        echo json_encode(['success' => true, 'message' => 'AccMan başarıyla silindi.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Silme sırasında hata oluştu.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'ID gerekli.']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
            }
            exit;
        }

        if ($_GET['action'] === 'get_etkinlikler') {
            $selected_marka = $_GET['marka'] ?? '';

            try {
                if ($selected_marka) {
                    // Seçili markaya göre ve tarih geçmemiş etkinlikleri getir
                    $sql = "SELECT * FROM aa_erp_kt_etkinlikler
                           WHERE marka = :marka
                           AND (tarih_bit IS NULL OR tarih_bit >= GETDATE())
                           ORDER BY tarih_bas ASC";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':marka', $selected_marka);
                    $stmt->execute();
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    // Tüm aktif etkinlikleri getir
                    $sql = "SELECT * FROM aa_erp_kt_etkinlikler
                           WHERE (tarih_bit IS NULL OR tarih_bit >= GETDATE())
                           ORDER BY marka, tarih_bas ASC";
                    $stmt = $conn->query($sql);
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }

                echo json_encode([
                    'success' => true,
                    'data' => $data,
                    'debug' => [
                        'selected_marka' => $selected_marka,
                        'count' => count($data)
                    ]
                ]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
            }
            exit;
        }

    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}
?>

<style>
.form-container {
    max-width: 1000px;
    margin: 20px auto;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.form-header {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #007cba;
}

.form-header h2 {
    color: #007cba;
    margin: 0;
}

.form-row {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
    align-items: start;
}

.form-group {
    flex: 1;
}

.form-group.full-width {
    flex: 100%;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
}

.form-group textarea {
    resize: vertical;
    min-height: 80px;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #007cba;
    box-shadow: 0 0 5px rgba(0,124,186,0.3);
}

.radio-group {
    display: flex;
    gap: 15px;
    margin-top: 5px;
}

.radio-group label {
    display: flex;
    align-items: center;
    gap: 5px;
    font-weight: normal;
    cursor: pointer;
}

.radio-group input[type="radio"] {
    width: auto;
    margin: 0;
}

.checkbox-group {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 5px;
}

.checkbox-group input[type="checkbox"] {
    width: auto;
    margin: 0;
}

.checkbox-group label {
    font-weight: normal;
    margin: 0;
    cursor: pointer;
}

.btn-container {
    text-align: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.btn {
    padding: 12px 30px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    margin: 0 10px;
}

.btn-primary {
    background-color: #007cba;
    color: white;
}

.btn-primary:hover {
    background-color: #005a87;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #545b62;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

.loading {
    display: none;
    text-align: center;
    padding: 20px;
}

.required {
    color: red;
}

.input-with-button {
    display: flex;
    gap: 5px;
}

.input-with-button input {
    flex: 1;
    cursor: pointer;
}

.btn-search {
    padding: 10px 15px;
    border: 1px solid #007cba;
    background-color: #007cba;
    color: white;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-search:hover {
    background-color: #005a87;
}

.btn-search .dashicons {
    font-size: 16px;
    width: 16px;
    height: 16px;
}

/* Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    padding: 20px;
    border-radius: 8px;
    width: 90vw;
    height: 80vh;
    max-width: 800px;
    max-height: 600px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    display: flex;
    flex-direction: column;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #007cba;
}

.modal-header h3 {
    margin: 0;
    color: #007cba;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #999;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-close:hover {
    color: #333;
}

.modal-search {
    margin-bottom: 15px;
}

.search-container {
    display: flex;
    gap: 10px;
    align-items: center;
}

.modal-search input {
    flex: 1;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.search-options {
    display: flex;
    gap: 5px;
}

.search-option {
    width: 40px;
    height: 40px;
    border: 2px solid #ddd;
    background: white;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.search-option .dashicons {
    font-size: 18px;
    width: 18px;
    height: 18px;
}

.search-option:hover {
    border-color: #007cba;
    background-color: #f0f8ff;
}

.search-option.active {
    border-color: #007cba;
    background-color: #007cba;
    color: white;
}

.modal-list {
    flex: 1;
    overflow-y: auto;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.bayi-item {
    padding: 10px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: background-color 0.2s;
}

.bayi-item:hover {
    background-color: #f5f5f5;
}

.bayi-item:last-child {
    border-bottom: none;
}

.bayi-item.dikkat-listesi {
    color: red !important;
    font-weight: bold;
}

.bayi-item.kara-liste {
    background-color: black !important;
    color: white !important;
    font-weight: bold;
}

.bayi-item.kara-liste:hover {
    background-color: #333 !important;
}

.modal-footer {
    margin-top: 15px;
    padding-top: 10px;
    border-top: 1px solid #eee;
    font-size: 12px;
    color: #666;
}

.status-legend {
    display: flex;
    gap: 20px;
    justify-content: center;
}

.status-item {
    display: flex;
    align-items: center;
    gap: 5px;
}

.status-color {
    width: 12px;
    height: 12px;
    border-radius: 2px;
}

.status-color.dikkat {
    background-color: red;
}

.status-color.kara {
    background-color: black;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
        gap: 0;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .radio-group {
        flex-direction: column;
        gap: 10px;
    }

    .modal-content {
        width: 95vw;
        height: 90vh;
        margin: 10px;
    }

    .search-container {
        flex-direction: column;
        gap: 10px;
    }

    .search-options {
        justify-content: center;
    }

    .status-legend {
        flex-direction: column;
        gap: 10px;
        align-items: center;
    }
}

/* Yetkili Table Styles */
.yetkili-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

.yetkili-table th,
.yetkili-table td {
    padding: 8px 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.yetkili-table th {
    background-color: #f5f5f5;
    font-weight: bold;
    color: #333;
}

.yetkili-table tr:hover {
    background-color: #f9f9f9;
}

.btn-small {
    padding: 4px 8px;
    font-size: 12px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    margin-right: 5px;
}

.btn-edit {
    background-color: #007cba;
    color: white;
}

.btn-edit:hover {
    background-color: #005a87;
}

.btn-delete {
    background-color: #dc3545;
    color: white;
}

.btn-delete:hover {
    background-color: #c82333;
}

/* Yetkili Add Form Styles */
.yetkili-add-form {
    margin-bottom: 20px;
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 5px;
    border: 1px solid #dee2e6;
}

.yetkili-add-form .form-row {
    display: flex;
    gap: 10px;
    align-items: center;
}

.yetkili-input {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.yetkili-input:focus {
    outline: none;
    border-color: #007cba;
    box-shadow: 0 0 3px rgba(0,124,186,0.3);
}

@media (max-width: 768px) {
    .yetkili-add-form .form-row {
        flex-direction: column;
        gap: 10px;
    }

    .yetkili-input {
        width: 100%;
    }
}

/* Edit Input Styles */
.yetkili-edit-input {
    width: 100%;
    padding: 4px 8px;
    border: 1px solid #007cba;
    border-radius: 3px;
    font-size: 13px;
    background-color: #f0f8ff;
}

.yetkili-edit-input:focus {
    outline: none;
    border-color: #005a87;
    background-color: white;
}

/* İşlemler Sütunu Sabit Genişlik */
.yetkili-table th:last-child,
.yetkili-table td:last-child {
    width: 140px;
    min-width: 140px;
    max-width: 140px;
}

/* Buton Container */
.yetkili-buttons {
    display: flex;
    gap: 5px;
    justify-content: flex-end;
    align-items: center;
}

/* Delete Confirmation Row */
.yetkili-delete-row {
    background-color: #f8d7da !important;
    border: 2px solid #dc3545 !important;
}

.yetkili-delete-row td {
    background-color: #f8d7da !important;
}
</style>

<div class="form-container">
    <div class="form-header">
        <h2>Yeni Fırsat Oluştur</h2>
    </div>

    <div id="alert-container"></div>
    <div class="loading" id="loading">İşlem yapılıyor...</div>

    <form id="firsat-form">
        <div class="form-row">
            <div class="form-group">
                <label for="marka">Marka <span class="required">*</span></label>
                <div class="input-with-button">
                    <input type="text" id="marka" name="marka" placeholder="Marka seçmek için tıklayın" readonly onclick="openMarkaModal()" required>
                    <button type="button" class="btn-search" onclick="openMarkaModal()">
                        <span class="dashicons dashicons-search"></span>
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label>Geliş Kanalı <span class="required">*</span></label>
                <div class="radio-group">
                    <label><input type="radio" name="gelis_kanali" value="Bayiden" required> Bayiden</label>
                    <label><input type="radio" name="gelis_kanali" value="Üreticiden" required> Üreticiden</label>
                    <label><input type="radio" name="gelis_kanali" value="Komtera" required> Komtera</label>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="olasilik">Olasılık <span class="required">*</span></label>
                <select id="olasilik" name="olasilik" required>
                    <option value="">Olasılık Seçiniz</option>
                    <option value="1-Discovery">1-Discovery</option>
                    <option value="2-Solution Mapping">2-Solution Mapping</option>
                    <option value="3-Demo/POC">3-Demo/POC</option>
                    <option value="4-Negotiation">4-Negotiation</option>
                    <option value="5-Confirmed/Waiting for End-User PO">5-Confirmed/Waiting for End-User PO</option>
                    <option value="6-Run Rate">6-Run Rate</option>
                </select>
            </div>
            <div class="form-group">
                <label for="proje_adi">Proje Adı</label>
                <input type="text" id="proje_adi" name="proje_adi">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group full-width">
                <label for="firsat_aciklama">Fırsat Açıklama</label>
                <textarea id="firsat_aciklama" name="firsat_aciklama" rows="3"></textarea>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="bayi">Bayi <span class="required">*</span></label>
                <div class="input-with-button">
                    <input type="text" id="bayi" name="bayi" placeholder="Bayi seçmek için tıklayın" readonly onclick="openBayiModal()" required>
                    <input type="hidden" id="bayi_kodu" name="bayi_kodu">
                    <button type="button" class="btn-search" onclick="openBayiModal()">
                        <span class="dashicons dashicons-search"></span>
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label for="bayi_yetkili">Bayi Yetkili <span class="required">*</span></label>
                <div class="input-with-button">
                    <input type="text" id="bayi_yetkili" name="bayi_yetkili" placeholder="Önce bayi seçin" readonly onclick="openBayiYetkiliModal()" required>
                    <input type="hidden" id="bayi_yetkili_id" name="bayi_yetkili_id">
                    <button type="button" class="btn-search" onclick="openBayiYetkiliModal()">
                        <span class="dashicons dashicons-search"></span>
                    </button>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="musteri">Müşteri <span class="required">*</span></label>
                <div class="input-with-button">
                    <input type="text" id="musteri" name="musteri" placeholder="Müşteri seçmek için tıklayın" readonly onclick="openMusteriModal()" required>
                    <input type="hidden" id="musteri_id" name="musteri_id">
                    <button type="button" class="btn-search" onclick="openMusteriModal()">
                        <span class="dashicons dashicons-search"></span>
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label for="musteri_yetkili">Müşteri Yetkili <span class="required">*</span></label>
                <div class="input-with-button">
                    <input type="text" id="musteri_yetkili" name="musteri_yetkili" placeholder="Önce müşteri seçin" readonly onclick="openMusteriYetkiliModal()" required>
                    <input type="hidden" id="musteri_yetkili_id" name="musteri_yetkili_id">
                    <button type="button" class="btn-search" onclick="openMusteriYetkiliModal()">
                        <span class="dashicons dashicons-search"></span>
                    </button>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="accman">AccMan</label>
                <div class="input-with-button">
                    <input type="text" id="accman" name="accman" placeholder="AccMan seçmek için tıklayın" readonly onclick="openAccmanModal()">
                    <input type="hidden" id="accman_id" name="accman_id">
                    <button type="button" class="btn-search" onclick="openAccmanModal()">
                        <span class="dashicons dashicons-search"></span>
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label for="etkinlik">Etkinlik</label>
                <div class="input-with-button">
                    <input type="text" id="etkinlik" name="etkinlik" placeholder="Etkinlik seçmek için tıklayın" readonly onclick="openEtkinlikModal()">
                    <input type="hidden" id="etkinlik_id" name="etkinlik_id">
                    <button type="button" class="btn-search" onclick="openEtkinlikModal()">
                        <span class="dashicons dashicons-search"></span>
                    </button>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" id="register" name="register">
                    <label for="register">Register</label>
                </div>
            </div>
            <div class="form-group">
                <label for="register_dr_no">Register DR No</label>
                <input type="text" id="register_dr_no" name="register_dr_no">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="musteri_temsilcisi">Müşteri Temsilcisi</label>
                <div class="input-with-button">
                    <input type="text" id="musteri_temsilcisi" name="musteri_temsilcisi" placeholder="Müşteri temsilcisi seçmek için tıklayın" readonly onclick="openMusteriTemsilcisiModal()" value="<?php echo esc_html($user); ?>">
                    <button type="button" class="btn-search" onclick="openMusteriTemsilcisiModal()">
                        <span class="dashicons dashicons-search"></span>
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label>Oluşturan</label>
                <input type="text" value="<?php echo esc_html($user); ?>" disabled>
            </div>
        </div>

        <div class="btn-container">
            <button type="submit" class="btn btn-primary">Fırsat Oluştur</button>
            <button type="button" class="btn btn-secondary" onclick="window.history.back()">İptal</button>
        </div>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Sayfa yüklenince otomatik marka modal aç
    setTimeout(function() {
        openMarkaModal();
    }, 100);

    // Form submit
    $('#firsat-form').on('submit', function(e) {
        e.preventDefault();
        submitForm();
    });
});

// Marka Modal Functions
function openMarkaModal() {
    const modalHtml = `
        <div id="marka-modal" class="modal-overlay" onclick="closeMarkaModal(event)">
            <div class="modal-content" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <h3>Marka Seçimi</h3>
                    <button class="modal-close" onclick="closeMarkaModal()">&times;</button>
                </div>
                <div class="modal-search">
                    <div class="search-container">
                        <input type="text" id="marka-search" placeholder="Marka adı ile ara..." onkeyup="searchMarkalar(this.value)">
                        <div class="search-options">
                            <button class="search-option active" data-mode="startswith" title="İle Başlıyor">
                                <span class="dashicons dashicons-editor-alignleft"></span>
                            </button>
                            <button class="search-option" data-mode="contains" title="İçeriyor">
                                <span class="dashicons dashicons-search"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-list" id="marka-list">
                    <div style="text-align: center; padding: 20px;">Yükleniyor...</div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // Search option event listeners
    document.querySelectorAll('#marka-modal .search-option').forEach(function(btn) {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            document.querySelectorAll('#marka-modal .search-option').forEach(function(b) {
                b.classList.remove('active');
            });

            // Add active class to clicked button
            this.classList.add('active');

            // Trigger search with current input value
            const searchValue = document.getElementById('marka-search').value;
            if (searchValue.length >= 2) {
                searchMarkalar(searchValue);
            } else {
                loadAllMarkalar();
            }
        });
    });

    loadAllMarkalar();
}

function closeMarkaModal(event) {
    if (event && event.target.id !== 'marka-modal') return;
    const modal = document.getElementById('marka-modal');
    if (modal) {
        modal.remove();
    }
}

function loadAllMarkalar() {
    jQuery.ajax({
        url: '<?php echo get_stylesheet_directory_uri(); ?>/erp/mod/yeni_firsat.php?action=get_markalar',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            displayMarkalar(data);
        },
        error: function() {
            document.getElementById('marka-list').innerHTML = '<div style="text-align: center; padding: 20px; color: red;">Marka listesi yüklenirken hata oluştu.</div>';
        }
    });
}

function searchMarkalar(query) {
    if (query.length < 2) {
        loadAllMarkalar();
        return;
    }

    // Get selected search mode
    const activeOption = document.querySelector('#marka-modal .search-option.active');
    const searchMode = activeOption ? activeOption.getAttribute('data-mode') : 'startswith';

    jQuery.ajax({
        url: '<?php echo get_stylesheet_directory_uri(); ?>/erp/mod/yeni_firsat.php?action=search_markalar&query=' + encodeURIComponent(query) + '&mode=' + searchMode,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            displayMarkalar(data);
        },
        error: function() {
            document.getElementById('marka-list').innerHTML = '<div style="text-align: center; padding: 20px; color: red;">Arama sırasında hata oluştu.</div>';
        }
    });
}

function displayMarkalar(markalar) {
    const listDiv = document.getElementById('marka-list');
    if (markalar.length === 0) {
        listDiv.innerHTML = '<div style="text-align: center; padding: 20px; color: #666;">Marka bulunamadı.</div>';
        return;
    }

    let html = '';
    markalar.forEach(function(marka) {
        html += `<div class="bayi-item" onclick="selectMarka('${marka.MARKA}')">${marka.MARKA}</div>`;
    });

    listDiv.innerHTML = html;
}

function selectMarka(markaAdi) {
    document.getElementById('marka').value = markaAdi;

    // Marka değiştiğinde ilgili alanları sıfırla
    document.getElementById('musteri_temsilcisi').value = '<?php echo esc_html($user); ?>';
    document.getElementById('accman').value = '';
    document.getElementById('accman_id').value = '';
    document.getElementById('etkinlik').value = '';
    document.getElementById('etkinlik_id').value = '';

    closeMarkaModal();
}

// Bayi Modal Functions
function openBayiModal() {
    const modalHtml = `
        <div id="bayi-modal" class="modal-overlay" onclick="closeBayiModal(event)">
            <div class="modal-content" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <h3>Bayi Seçimi</h3>
                    <button class="modal-close" onclick="closeBayiModal()">&times;</button>
                </div>
                <div class="modal-search">
                    <div class="search-container">
                        <input type="text" id="bayi-search" placeholder="Bayi adı ile ara..." onkeyup="searchBayiler(this.value)">
                        <div class="search-options">
                            <button class="search-option active" data-mode="startswith" title="İle Başlıyor">
                                <span class="dashicons dashicons-editor-alignleft"></span>
                            </button>
                            <button class="search-option" data-mode="contains" title="İçeriyor">
                                <span class="dashicons dashicons-search"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-list" id="bayi-list">
                    <div style="text-align: center; padding: 20px;">Yükleniyor...</div>
                </div>
                <div class="modal-footer">
                    <div class="status-legend">
                        <div class="status-item">
                            <div class="status-color dikkat"></div>
                            <span>Kırmızı: Dikkat Listesi</span>
                        </div>
                        <div class="status-item">
                            <div class="status-color kara"></div>
                            <span>Siyah: Kara Listedeki Bayiler</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // Search option event listeners
    document.querySelectorAll('.search-option').forEach(function(btn) {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            document.querySelectorAll('.search-option').forEach(function(b) {
                b.classList.remove('active');
            });

            // Add active class to clicked button
            this.classList.add('active');

            // Trigger search with current input value
            const searchValue = document.getElementById('bayi-search').value;
            if (searchValue.length >= 2) {
                searchBayiler(searchValue);
            } else {
                loadAllBayiler();
            }
        });
    });

    loadAllBayiler();

    // Arama kutusuna otomatik fokus
    setTimeout(function() {
        const searchInput = document.getElementById('bayi-search');
        if (searchInput) {
            searchInput.focus();
        }
    }, 100);
}

function closeBayiModal(event) {
    if (event && event.target.id !== 'bayi-modal') return;
    const modal = document.getElementById('bayi-modal');
    if (modal) {
        modal.remove();
    }
}

function loadAllBayiler() {
    jQuery.ajax({
        url: '<?php echo get_stylesheet_directory_uri(); ?>/erp/mod/yeni_firsat.php?action=get_bayiler',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            displayBayiler(data);
        },
        error: function() {
            document.getElementById('bayi-list').innerHTML = '<div style="text-align: center; padding: 20px; color: red;">Bayi listesi yüklenirken hata oluştu.</div>';
        }
    });
}

function searchBayiler(query) {
    if (query.length < 2) {
        loadAllBayiler();
        return;
    }

    // Get selected search mode
    const activeOption = document.querySelector('.search-option.active');
    const searchMode = activeOption ? activeOption.getAttribute('data-mode') : 'startswith';

    jQuery.ajax({
        url: '<?php echo get_stylesheet_directory_uri(); ?>/erp/mod/yeni_firsat.php?action=search_bayiler&query=' + encodeURIComponent(query) + '&mode=' + searchMode,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            displayBayiler(data);
        },
        error: function() {
            document.getElementById('bayi-list').innerHTML = '<div style="text-align: center; padding: 20px; color: red;">Arama sırasında hata oluştu.</div>';
        }
    });
}

function displayBayiler(bayiler) {
    const listDiv = document.getElementById('bayi-list');
    if (bayiler.length === 0) {
        listDiv.innerHTML = '<div style="text-align: center; padding: 20px; color: #666;">Bayi bulunamadı.</div>';
        return;
    }

    let html = '';
    bayiler.forEach(function(bayi) {
        let cssClass = 'bayi-item';
        if (bayi.kara_liste === '1' || bayi.kara_liste === 1) {
            cssClass += ' kara-liste';
        } else if (bayi.dikkat_listesi === '1' || bayi.dikkat_listesi === 1) {
            cssClass += ' dikkat-listesi';
        }
        html += `<div class="${cssClass}" onclick="selectBayi('${bayi.CH_KODU}', '${bayi.CH_UNVANI}')">${bayi.CH_UNVANI}</div>`;
    });

    listDiv.innerHTML = html;
}

function selectBayi(bayiKodu, bayiAdi) {
    console.log('selectBayi called with:', bayiKodu, bayiAdi);
    document.getElementById('bayi').value = bayiAdi;
    document.getElementById('bayi_kodu').value = bayiKodu;
    // Bayi yetkili alanını temizle
    document.getElementById('bayi_yetkili').value = '';
    document.getElementById('bayi_yetkili_id').value = '';
    console.log('Bayi kodu set to:', document.getElementById('bayi_kodu').value);
    closeBayiModal();
}

// Müşteri Modal Functions
function openMusteriModal() {
    const modalHtml = `
        <div id="musteri-modal" class="modal-overlay" onclick="closeMusteriModal(event)">
            <div class="modal-content" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <h3>Müşteri Seçimi</h3>
                    <button class="modal-close" onclick="closeMusteriModal()">&times;</button>
                </div>
                <div class="modal-search">
                    <div class="search-container">
                        <input type="text" id="musteri-search" placeholder="Müşteri adı ile ara..." onkeyup="searchMusteriler(this.value)">
                        <div class="search-options">
                            <button class="search-option active" data-mode="startswith" title="İle Başlıyor">
                                <span class="dashicons dashicons-editor-alignleft"></span>
                            </button>
                            <button class="search-option" data-mode="contains" title="İçeriyor">
                                <span class="dashicons dashicons-search"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-list" id="musteri-list">
                    <div style="text-align: center; padding: 20px;">Yükleniyor...</div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // Search option event listeners
    document.querySelectorAll('#musteri-modal .search-option').forEach(function(btn) {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            document.querySelectorAll('#musteri-modal .search-option').forEach(function(b) {
                b.classList.remove('active');
            });

            // Add active class to clicked button
            this.classList.add('active');

            // Trigger search with current input value
            const searchValue = document.getElementById('musteri-search').value;
            if (searchValue.length >= 2) {
                searchMusteriler(searchValue);
            } else {
                loadAllMusteriler();
            }
        });
    });

    loadAllMusteriler();

    // Arama kutusuna otomatik fokus
    setTimeout(function() {
        const searchInput = document.getElementById('musteri-search');
        if (searchInput) {
            searchInput.focus();
        }
    }, 100);
}

function closeMusteriModal(event) {
    if (event && event.target.id !== 'musteri-modal') return;
    const modal = document.getElementById('musteri-modal');
    if (modal) {
        modal.remove();
    }
}

function loadAllMusteriler() {
    jQuery.ajax({
        url: '<?php echo get_stylesheet_directory_uri(); ?>/erp/mod/yeni_firsat.php?action=get_musteriler',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            displayMusteriler(data);
        },
        error: function() {
            document.getElementById('musteri-list').innerHTML = '<div style="text-align: center; padding: 20px; color: red;">Müşteri listesi yüklenirken hata oluştu.</div>';
        }
    });
}

function searchMusteriler(query) {
    if (query.length < 2) {
        loadAllMusteriler();
        return;
    }

    // Get selected search mode
    const activeOption = document.querySelector('#musteri-modal .search-option.active');
    const searchMode = activeOption ? activeOption.getAttribute('data-mode') : 'startswith';

    jQuery.ajax({
        url: '<?php echo get_stylesheet_directory_uri(); ?>/erp/mod/yeni_firsat.php?action=search_musteriler&query=' + encodeURIComponent(query) + '&mode=' + searchMode,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            displayMusteriler(data);
        },
        error: function() {
            document.getElementById('musteri-list').innerHTML = '<div style="text-align: center; padding: 20px; color: red;">Arama sırasında hata oluştu.</div>';
        }
    });
}

function displayMusteriler(musteriler) {
    const listDiv = document.getElementById('musteri-list');
    if (musteriler.length === 0) {
        listDiv.innerHTML = '<div style="text-align: center; padding: 20px; color: #666;">Müşteri bulunamadı.</div>';
        return;
    }

    let html = '';
    musteriler.forEach(function(musteri) {
        html += `<div class="bayi-item" onclick="selectMusteri('${musteri.id}', '${musteri.musteri}')">${musteri.musteri}</div>`;
    });

    listDiv.innerHTML = html;
}

function selectMusteri(musteriId, musteriAdi) {
    document.getElementById('musteri').value = musteriAdi;
    document.getElementById('musteri_id').value = musteriId;
    // Müşteri yetkili alanını temizle
    document.getElementById('musteri_yetkili').value = '';
    document.getElementById('musteri_yetkili_id').value = '';
    closeMusteriModal();
}

// AccMan Modal Functions (aa_erp_kt_markalar_managers tablosundan)
function openAccmanModal() {
    const selectedMarka = document.getElementById('marka').value;

    if (!selectedMarka) {
        alert('Önce bir marka seçiniz.');
        return;
    }

    const modalHtml = `
        <div id="accman-modal" class="modal-overlay" onclick="closeAccmanModal(event)">
            <div class="modal-content" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <h3>AccMan Seçimi (${selectedMarka})</h3>
                    <button class="modal-close" onclick="closeAccmanModal()">&times;</button>
                </div>
                <div class="yetkili-add-form">
                    <div class="form-row">
                        <input type="text" id="new-accman-name" placeholder="Yetkili Adı" class="yetkili-input">
                        <input type="text" id="new-accman-phone" placeholder="Telefon" class="yetkili-input">
                        <input type="email" id="new-accman-email" placeholder="E-posta" class="yetkili-input">
                        <button class="btn btn-primary" onclick="saveAccman()">Ekle</button>
                    </div>
                </div>
                <div class="modal-list" id="accman-list">
                    <div style="text-align: center; padding: 20px;">Yükleniyor...</div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    loadAccmanlar(selectedMarka);
}

function closeAccmanModal(event) {
    if (event && event.target.id !== 'accman-modal') return;
    const modal = document.getElementById('accman-modal');
    if (modal) {
        modal.remove();
    }
}

function loadAccmanlar(marka) {
    console.log('DEBUG - loadAccmanlar called with marka:', marka);

    jQuery.ajax({
        url: '<?php echo get_stylesheet_directory_uri(); ?>/erp/mod/yeni_firsat.php?action=get_accman&marka=' + encodeURIComponent(marka),
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('DEBUG - AccMan response:', response);
            console.log('DEBUG - AccMan data count:', response.data ? response.data.length : 0);

            if (response.success) {
                displayAccmanlar(response.data);
            } else {
                document.getElementById('accman-list').innerHTML = '<div style="text-align: center; padding: 20px; color: red;">Hata: ' + response.message + '</div>';
            }
        },
        error: function(xhr, status, error) {
            console.error('DEBUG - AccMan AJAX Error:', status, error);
            console.error('DEBUG - AccMan Response Text:', xhr.responseText);
            document.getElementById('accman-list').innerHTML = '<div style="text-align: center; padding: 20px; color: red;">AccMan listesi yüklenirken hata oluştu.</div>';
        }
    });
}

function displayAccmanlar(data) {
    const listDiv = document.getElementById('accman-list');
    const selectedMarka = document.getElementById('marka').value;

    console.log('DEBUG - displayAccmanlar - selectedMarka:', selectedMarka);
    console.log('DEBUG - displayAccmanlar - data:', data);
    console.log('DEBUG - displayAccmanlar - count:', data.length);

    if (!Array.isArray(data)) {
        console.error('AccMan data is not an array:', data);
        listDiv.innerHTML = '<div style="text-align: center; padding: 20px; color: red;">Veri formatı hatalı.</div>';
        return;
    }

    if (data.length === 0) {
        listDiv.innerHTML = '<div style="text-align: center; padding: 20px; color: #666;">Bu marka (' + selectedMarka + ') için AccMan bulunamadı.</div>';
        return;
    }

    let html = '<table class="yetkili-table"><thead><tr><th>Yetkili</th><th>Telefon</th><th>E-posta</th><th>İşlemler</th></tr></thead><tbody>';
    data.forEach(function(accman) {
        html += `
            <tr id="accman-row-${accman.id}">
                <td onclick="selectAccman('${accman.id}', '${accman.yetkili || ''}')" style="cursor: pointer;" class="yetkili-name-cell">${accman.yetkili || 'N/A'}</td>
                <td class="yetkili-phone-cell">${accman.telefon || ''}</td>
                <td class="yetkili-email-cell">${accman.eposta || ''}</td>
                <td>
                    <div class="yetkili-buttons">
                        <button class="btn-small btn-edit" onclick="editAccman(${accman.id}, '${accman.yetkili || ''}', '${accman.telefon || ''}', '${accman.eposta || ''}')">Düzenle</button>
                        <button class="btn-small btn-delete" onclick="deleteAccman(${accman.id})">Sil</button>
                    </div>
                </td>
            </tr>
        `;
    });
    html += '</tbody></table>';

    listDiv.innerHTML = html;
}

function selectAccman(accmanId, accmanAdi) {
    document.getElementById('accman').value = accmanAdi;
    document.getElementById('accman_id').value = accmanId;
    closeAccmanModal();
}

// CRUD Functions for AccMan
function saveAccman() {
    const marka = document.getElementById('marka').value;
    const yetkili = document.getElementById('new-accman-name').value.trim();
    const telefon = document.getElementById('new-accman-phone').value.trim();
    const eposta = document.getElementById('new-accman-email').value.trim();

    if (!yetkili) {
        alert('Yetkili adı gereklidir.');
        return;
    }

    jQuery.ajax({
        url: '<?php echo get_stylesheet_directory_uri(); ?>/erp/mod/yeni_firsat.php?action=save_accman',
        type: 'POST',
        data: {
            marka: marka,
            yetkili: yetkili,
            telefon: telefon,
            eposta: eposta
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Inputları temizle
                document.getElementById('new-accman-name').value = '';
                document.getElementById('new-accman-phone').value = '';
                document.getElementById('new-accman-email').value = '';

                // Listeyi yenile
                const currentMarka = document.getElementById('marka').value;
                loadAccmanlar(currentMarka);

                alert(response.message);
            } else {
                alert('Hata: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Save AccMan error:', status, error);
            alert('Kayıt sırasında hata oluştu: ' + error);
        }
    });
}

function editAccman(accmanId, currentName, currentPhone, currentEmail) {
    const row = document.getElementById(`accman-row-${accmanId}`);

    // Satırı edit moduna çevir
    row.innerHTML = `
        <td><input type="text" id="edit-accman-name-${accmanId}" value="${currentName}" class="yetkili-edit-input"></td>
        <td><input type="text" id="edit-accman-phone-${accmanId}" value="${currentPhone}" class="yetkili-edit-input"></td>
        <td><input type="email" id="edit-accman-email-${accmanId}" value="${currentEmail}" class="yetkili-edit-input"></td>
        <td>
            <div class="yetkili-buttons">
                <button class="btn-small btn-edit" onclick="updateAccman(${accmanId})">Kaydet</button>
                <button class="btn-small btn-delete" onclick="cancelEditAccman(${accmanId}, '${currentName}', '${currentPhone}', '${currentEmail}')">İptal</button>
            </div>
        </td>
    `;
}

function updateAccman(accmanId) {
    const yetkili = document.getElementById(`edit-accman-name-${accmanId}`).value.trim();
    const telefon = document.getElementById(`edit-accman-phone-${accmanId}`).value.trim();
    const eposta = document.getElementById(`edit-accman-email-${accmanId}`).value.trim();

    if (!yetkili) {
        alert('Yetkili adı gereklidir.');
        return;
    }

    jQuery.ajax({
        url: '<?php echo get_stylesheet_directory_uri(); ?>/erp/mod/yeni_firsat.php?action=update_accman',
        type: 'POST',
        data: {
            id: accmanId,
            yetkili: yetkili,
            telefon: telefon,
            eposta: eposta
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Listeyi yenile
                const marka = document.getElementById('marka').value;
                loadAccmanlar(marka);
            } else {
                alert('Hata: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Update AccMan error:', status, error);
            alert('Güncelleme sırasında hata oluştu: ' + error);
        }
    });
}

function cancelEditAccman(accmanId, originalName, originalPhone, originalEmail) {
    const row = document.getElementById(`accman-row-${accmanId}`);

    // Satırı orijinal haline döndür
    row.innerHTML = `
        <td onclick="selectAccman('${accmanId}', '${originalName}')" style="cursor: pointer;" class="yetkili-name-cell">${originalName || 'N/A'}</td>
        <td class="yetkili-phone-cell">${originalPhone || ''}</td>
        <td class="yetkili-email-cell">${originalEmail || ''}</td>
        <td>
            <div class="yetkili-buttons">
                <button class="btn-small btn-edit" onclick="editAccman(${accmanId}, '${originalName}', '${originalPhone}', '${originalEmail}')">Düzenle</button>
                <button class="btn-small btn-delete" onclick="deleteAccman(${accmanId})">Sil</button>
            </div>
        </td>
    `;
}

function deleteAccman(accmanId) {
    // Önce AccMan bilgilerini alın
    const row = document.getElementById(`accman-row-${accmanId}`);
    const nameCell = row.querySelector('.yetkili-name-cell');
    const phoneCell = row.querySelector('.yetkili-phone-cell');
    const emailCell = row.querySelector('.yetkili-email-cell');

    const accmanName = nameCell ? nameCell.textContent : '';
    const accmanPhone = phoneCell ? phoneCell.textContent : '';
    const accmanEmail = emailCell ? emailCell.textContent : '';

    // Satırı delete confirmation moduna çevir
    row.className = 'yetkili-delete-row';
    row.innerHTML = `
        <td colspan="3" style="text-align: center; font-weight: bold; color: #721c24;">
            Bu AccMan'i silmek istediğinizden emin misiniz: "${accmanName}"?
        </td>
        <td>
            <div class="yetkili-buttons">
                <button class="btn-small btn-delete" onclick="confirmDeleteAccman(${accmanId})">Sil</button>
                <button class="btn-small btn-edit" onclick="cancelDeleteAccman(${accmanId}, '${accmanName}', '${accmanPhone}', '${accmanEmail}')">Vazgeç</button>
            </div>
        </td>
    `;
}

function confirmDeleteAccman(accmanId) {
    jQuery.ajax({
        url: '<?php echo get_stylesheet_directory_uri(); ?>/erp/mod/yeni_firsat.php?action=delete_accman',
        type: 'POST',
        data: {
            id: accmanId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Listeyi yenile
                const marka = document.getElementById('marka').value;
                loadAccmanlar(marka);
            } else {
                alert('Hata: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Delete AccMan error:', status, error);
            alert('Silme sırasında hata oluştu: ' + error);
        }
    });
}

function cancelDeleteAccman(accmanId, originalName, originalPhone, originalEmail) {
    const row = document.getElementById(`accman-row-${accmanId}`);

    // Satırı orijinal haline döndür
    row.className = '';
    row.innerHTML = `
        <td onclick="selectAccman('${accmanId}', '${originalName}')" style="cursor: pointer;" class="yetkili-name-cell">${originalName || 'N/A'}</td>
        <td class="yetkili-phone-cell">${originalPhone || ''}</td>
        <td class="yetkili-email-cell">${originalEmail || ''}</td>
        <td>
            <div class="yetkili-buttons">
                <button class="btn-small btn-edit" onclick="editAccman(${accmanId}, '${originalName}', '${originalPhone}', '${originalEmail}')">Düzenle</button>
                <button class="btn-small btn-delete" onclick="deleteAccman(${accmanId})">Sil</button>
            </div>
        </td>
    `;
}

// Müşteri Temsilcisi Modal Functions
function openMusteriTemsilcisiModal() {
    const modalHtml = `
        <div id="musteri-temsilcisi-modal" class="modal-overlay" onclick="closeMusteriTemsilcisiModal(event)">
            <div class="modal-content" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <h3>Müşteri Temsilcisi Seçimi</h3>
                    <button class="modal-close" onclick="closeMusteriTemsilcisiModal()">&times;</button>
                </div>
                <div class="modal-list" id="musteri-temsilcisi-list">
                    <div style="text-align: center; padding: 20px;">Yükleniyor...</div>
                </div>
                <div class="modal-footer">
                    <div style="font-size: 12px; color: #666; text-align: center;">
                        * Seçilen markaya yetkili kullanıcılar gösterilmektedir
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    loadMusteriTemsilcileri();

    // Arama kutusuna otomatik fokus - bu modalde arama yok ama konsistency için
    setTimeout(function() {
        // Focus işlemi yok çünkü bu modalde arama kutusu yok
    }, 100);
}

function closeMusteriTemsilcisiModal(event) {
    if (event && event.target.id !== 'musteri-temsilcisi-modal') return;
    const modal = document.getElementById('musteri-temsilcisi-modal');
    if (modal) {
        modal.remove();
    }
}

function loadMusteriTemsilcileri() {
    const selectedMarka = document.getElementById('marka').value;

    jQuery.ajax({
        url: '<?php echo get_stylesheet_directory_uri(); ?>/erp/mod/yeni_firsat.php?action=get_musteri_temsilcileri&marka=' + encodeURIComponent(selectedMarka),
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayMusteriTemsilcileri(response.data);
            } else {
                document.getElementById('musteri-temsilcisi-list').innerHTML = '<div style="text-align: center; padding: 20px; color: red;">Hata: ' + response.message + '</div>';
            }
        },
        error: function(xhr, status, error) {
            document.getElementById('musteri-temsilcisi-list').innerHTML = '<div style="text-align: center; padding: 20px; color: red;">Müşteri temsilcisi listesi yüklenirken hata oluştu.</div>';
        }
    });
}

function displayMusteriTemsilcileri(temsilciler) {
    const listDiv = document.getElementById('musteri-temsilcisi-list');

    if (temsilciler.length === 0) {
        listDiv.innerHTML = '<div style="text-align: center; padding: 20px; color: #666;">Bu marka için yetkili müşteri temsilcisi bulunamadı.</div>';
        return;
    }

    let html = '';
    temsilciler.forEach(function(temsilci) {
        html += `
            <div class="bayi-item" onclick="selectMusteriTemsilcisi('${temsilci.user_login}', '${temsilci.user_login}')">
                ${temsilci.user_login}
            </div>
        `;
    });

    listDiv.innerHTML = html;
}

function selectMusteriTemsilcisi(userLogin, userName) {
    document.getElementById('musteri_temsilcisi').value = userName;
    closeMusteriTemsilcisiModal();
}

// Etkinlik Modal Functions
function openEtkinlikModal() {
    const selectedMarka = document.getElementById('marka').value;

    if (!selectedMarka) {
        alert('Önce bir marka seçiniz.');
        return;
    }

    const modalHtml = `
        <div id="etkinlik-modal" class="modal-overlay" onclick="closeEtkinlikModal(event)">
            <div class="modal-content" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <h3>Etkinlik Seçimi (${selectedMarka})</h3>
                    <button class="modal-close" onclick="closeEtkinlikModal()">&times;</button>
                </div>
                <div class="modal-list" id="etkinlik-list">
                    <div style="text-align: center; padding: 20px;">Yükleniyor...</div>
                </div>
                <div class="modal-footer">
                    <div style="font-size: 12px; color: #666; text-align: center;">
                        * Sadece aktif etkinlikler gösterilmektedir (tarih geçmemiş)
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    loadEtkinlikler(selectedMarka);
}

function closeEtkinlikModal(event) {
    if (event && event.target.id !== 'etkinlik-modal') return;
    const modal = document.getElementById('etkinlik-modal');
    if (modal) {
        modal.remove();
    }
}

function loadEtkinlikler(marka) {
    console.log('DEBUG - loadEtkinlikler called with marka:', marka);

    jQuery.ajax({
        url: '<?php echo get_stylesheet_directory_uri(); ?>/erp/mod/yeni_firsat.php?action=get_etkinlikler&marka=' + encodeURIComponent(marka),
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('DEBUG - Etkinlik response:', response);
            console.log('DEBUG - Etkinlik data count:', response.data ? response.data.length : 0);

            if (response.success) {
                displayEtkinlikler(response.data);
            } else {
                document.getElementById('etkinlik-list').innerHTML = '<div style="text-align: center; padding: 20px; color: red;">Hata: ' + response.message + '</div>';
            }
        },
        error: function(xhr, status, error) {
            console.error('DEBUG - Etkinlik AJAX Error:', status, error);
            console.error('DEBUG - Etkinlik Response Text:', xhr.responseText);
            document.getElementById('etkinlik-list').innerHTML = '<div style="text-align: center; padding: 20px; color: red;">Etkinlik listesi yüklenirken hata oluştu.</div>';
        }
    });
}

function displayEtkinlikler(data) {
    const listDiv = document.getElementById('etkinlik-list');
    const selectedMarka = document.getElementById('marka').value;

    console.log('DEBUG - displayEtkinlikler - selectedMarka:', selectedMarka);
    console.log('DEBUG - displayEtkinlikler - data:', data);
    console.log('DEBUG - displayEtkinlikler - count:', data.length);

    if (!Array.isArray(data)) {
        console.error('Etkinlik data is not an array:', data);
        listDiv.innerHTML = '<div style="text-align: center; padding: 20px; color: red;">Veri formatı hatalı.</div>';
        return;
    }

    if (data.length === 0) {
        listDiv.innerHTML = '<div style="text-align: center; padding: 20px; color: #666;">Bu marka (' + selectedMarka + ') için aktif etkinlik bulunamadı.</div>';
        return;
    }

    let html = '';
    data.forEach(function(etkinlik) {
        // Tarih formatla
        const tarihBas = etkinlik.tarih_bas ? new Date(etkinlik.tarih_bas).toLocaleDateString('tr-TR') : '';
        const tarihBit = etkinlik.tarih_bit ? new Date(etkinlik.tarih_bit).toLocaleDateString('tr-TR') : '';
        const tarihStr = tarihBas + (tarihBit ? ' - ' + tarihBit : '');

        html += `
            <div class="bayi-item" onclick="selectEtkinlik('${etkinlik.id}', '${etkinlik.baslik}', '${etkinlik.kodu}')">
                <strong>${etkinlik.baslik}</strong><br>
                <small style="color: #666;">Kod: ${etkinlik.kodu}</small><br>
                <small style="color: #999;">${tarihStr}</small>
            </div>
        `;
    });

    listDiv.innerHTML = html;
}

function selectEtkinlik(etkinlikId, etkinlikBaslik, etkinlikKodu) {
    const displayText = etkinlikBaslik + ' (' + etkinlikKodu + ')';
    document.getElementById('etkinlik').value = displayText;
    document.getElementById('etkinlik_id').value = etkinlikId;
    closeEtkinlikModal();
}

// Bayi Yetkili Modal Functions
function openBayiYetkiliModal() {
    const bayiKodu = document.getElementById('bayi_kodu').value;
    const bayiAdi = document.getElementById('bayi').value;

    console.log('openBayiYetkiliModal called');
    console.log('Bayi kodu:', bayiKodu);
    console.log('Bayi adı:', bayiAdi);

    if (!bayiKodu) {
        alert('Önce bir bayi seçiniz. Bayi kodu: ' + bayiKodu);
        return;
    }

    const modalHtml = `
        <div id="bayi-yetkili-modal" class="modal-overlay" onclick="closeBayiYetkiliModal(event)">
            <div class="modal-content" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <h3>Bayi Yetkili Seçimi</h3>
                    <button class="modal-close" onclick="closeBayiYetkiliModal()">&times;</button>
                </div>
                <div class="yetkili-add-form">
                    <div class="form-row">
                        <input type="text" id="new-bayi-yetkili-name" placeholder="Yetkili Adı" class="yetkili-input">
                        <input type="text" id="new-bayi-yetkili-phone" placeholder="Telefon" class="yetkili-input">
                        <input type="email" id="new-bayi-yetkili-email" placeholder="E-posta" class="yetkili-input">
                        <button class="btn btn-primary" onclick="saveBayiYetkili()">Ekle</button>
                    </div>
                </div>
                <div class="modal-list" id="bayi-yetkili-list">
                    <div style="text-align: center; padding: 20px;">Yükleniyor...</div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    loadBayiYetkililer(bayiKodu);
}

function closeBayiYetkiliModal(event) {
    if (event && event.target.id !== 'bayi-yetkili-modal') return;
    const modal = document.getElementById('bayi-yetkili-modal');
    if (modal) {
        modal.remove();
    }
}

function loadBayiYetkililer(bayiKodu) {
    jQuery.ajax({
        url: '<?php echo get_stylesheet_directory_uri(); ?>/erp/mod/yeni_firsat.php?action=get_bayi_yetkililer&bayi_kodu=' + encodeURIComponent(bayiKodu),
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Bayi yetkililer response:', response);

            // Yeni format kontrolü
            if (response && response.hasOwnProperty('data')) {
                displayBayiYetkililer(response.data);
            } else if (Array.isArray(response)) {
                // Eski format backward compatibility
                displayBayiYetkililer(response);
            } else {
                console.error('Unexpected response format:', response);
                document.getElementById('bayi-yetkili-list').innerHTML = '<div style="text-align: center; padding: 20px; color: red;">Beklenmeyen veri formatı.</div>';
            }
        },
        error: function(xhr, status, error) {
            console.error('Bayi yetkililer AJAX error:', status, error);
            console.error('Response:', xhr.responseText);
            document.getElementById('bayi-yetkili-list').innerHTML = '<div style="text-align: center; padding: 20px; color: red;">Yetkili listesi yüklenirken hata oluştu: ' + error + '</div>';
        }
    });
}

function displayBayiYetkililer(data) {
    const listDiv = document.getElementById('bayi-yetkili-list');

    // Data kontrolü - array olup olmadığını kontrol et
    if (!Array.isArray(data)) {
        console.error('Bayi yetkili data is not an array:', data);
        listDiv.innerHTML = '<div style="text-align: center; padding: 20px; color: red;">Veri formatı hatalı.</div>';
        return;
    }

    if (data.length === 0) {
        listDiv.innerHTML = '<div style="text-align: center; padding: 20px; color: #666;">Yetkili bulunamadı.</div>';
        return;
    }

    let html = '<table class="yetkili-table"><thead><tr><th>Yetkili</th><th>Telefon</th><th>E-posta</th><th>İşlemler</th></tr></thead><tbody>';
    data.forEach(function(yetkili) {
        html += `
            <tr id="bayi-yetkili-row-${yetkili.id}">
                <td onclick="selectBayiYetkili('${yetkili.id}', '${yetkili.yetkili || ''}')" style="cursor: pointer;" class="yetkili-name-cell">${yetkili.yetkili || 'N/A'}</td>
                <td class="yetkili-phone-cell">${yetkili.telefon || ''}</td>
                <td class="yetkili-email-cell">${yetkili.eposta || ''}</td>
                <td>
                    <div class="yetkili-buttons">
                        <button class="btn-small btn-edit" onclick="editBayiYetkili(${yetkili.id}, '${yetkili.yetkili || ''}', '${yetkili.telefon || ''}', '${yetkili.eposta || ''}')">Düzenle</button>
                        <button class="btn-small btn-delete" onclick="deleteBayiYetkili(${yetkili.id})">Sil</button>
                    </div>
                </td>
            </tr>
        `;
    });
    html += '</tbody></table>';

    listDiv.innerHTML = html;
}

function selectBayiYetkili(yetkiliId, yetkiliAdi) {
    document.getElementById('bayi_yetkili').value = yetkiliAdi;
    document.getElementById('bayi_yetkili_id').value = yetkiliId;
    closeBayiYetkiliModal();
}

// Müşteri Yetkili Modal Functions
function openMusteriYetkiliModal() {
    const musteriId = document.getElementById('musteri_id').value;
    if (!musteriId) {
        alert('Önce bir müşteri seçiniz.');
        return;
    }

    const modalHtml = `
        <div id="musteri-yetkili-modal" class="modal-overlay" onclick="closeMusteriYetkiliModal(event)">
            <div class="modal-content" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <h3>Müşteri Yetkili Seçimi</h3>
                    <button class="modal-close" onclick="closeMusteriYetkiliModal()">&times;</button>
                </div>
                <div class="yetkili-add-form">
                    <div class="form-row">
                        <input type="text" id="new-musteri-yetkili-name" placeholder="Yetkili Adı" class="yetkili-input">
                        <input type="text" id="new-musteri-yetkili-phone" placeholder="Telefon" class="yetkili-input">
                        <input type="email" id="new-musteri-yetkili-email" placeholder="E-posta" class="yetkili-input">
                        <button class="btn btn-primary" onclick="saveMusteriYetkili()">Ekle</button>
                    </div>
                </div>
                <div class="modal-list" id="musteri-yetkili-list">
                    <div style="text-align: center; padding: 20px;">Yükleniyor...</div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    loadMusteriYetkililer(musteriId);
}

function closeMusteriYetkiliModal(event) {
    if (event && event.target.id !== 'musteri-yetkili-modal') return;
    const modal = document.getElementById('musteri-yetkili-modal');
    if (modal) {
        modal.remove();
    }
}

function loadMusteriYetkililer(musteriId) {
    jQuery.ajax({
        url: '<?php echo get_stylesheet_directory_uri(); ?>/erp/mod/yeni_firsat.php?action=get_musteri_yetkililer&musteri_id=' + encodeURIComponent(musteriId),
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Musteri yetkililer response:', response);

            // Yeni format kontrolü
            if (response && response.hasOwnProperty('data')) {
                displayMusteriYetkililer(response.data);
            } else if (Array.isArray(response)) {
                // Eski format backward compatibility
                displayMusteriYetkililer(response);
            } else {
                console.error('Unexpected response format:', response);
                document.getElementById('musteri-yetkili-list').innerHTML = '<div style="text-align: center; padding: 20px; color: red;">Beklenmeyen veri formatı.</div>';
            }
        },
        error: function(xhr, status, error) {
            console.error('Musteri yetkililer AJAX error:', status, error);
            console.error('Response:', xhr.responseText);
            document.getElementById('musteri-yetkili-list').innerHTML = '<div style="text-align: center; padding: 20px; color: red;">Yetkili listesi yüklenirken hata oluştu: ' + error + '</div>';
        }
    });
}

function displayMusteriYetkililer(data) {
    const listDiv = document.getElementById('musteri-yetkili-list');

    // Data kontrolü - array olup olmadığını kontrol et
    if (!Array.isArray(data)) {
        console.error('Musteri yetkili data is not an array:', data);
        listDiv.innerHTML = '<div style="text-align: center; padding: 20px; color: red;">Veri formatı hatalı.</div>';
        return;
    }

    if (data.length === 0) {
        listDiv.innerHTML = '<div style="text-align: center; padding: 20px; color: #666;">Yetkili bulunamadı.</div>';
        return;
    }

    let html = '<table class="yetkili-table"><thead><tr><th>Yetkili</th><th>Telefon</th><th>E-posta</th><th>İşlemler</th></tr></thead><tbody>';
    data.forEach(function(yetkili) {
        html += `
            <tr id="musteri-yetkili-row-${yetkili.id}">
                <td onclick="selectMusteriYetkili('${yetkili.id}', '${yetkili.yetkili || ''}')" style="cursor: pointer;" class="yetkili-name-cell">${yetkili.yetkili || 'N/A'}</td>
                <td class="yetkili-phone-cell">${yetkili.telefon || ''}</td>
                <td class="yetkili-email-cell">${yetkili.eposta || ''}</td>
                <td>
                    <div class="yetkili-buttons">
                        <button class="btn-small btn-edit" onclick="editMusteriYetkili(${yetkili.id}, '${yetkili.yetkili || ''}', '${yetkili.telefon || ''}', '${yetkili.eposta || ''}')">Düzenle</button>
                        <button class="btn-small btn-delete" onclick="deleteMusteriYetkili(${yetkili.id})">Sil</button>
                    </div>
                </td>
            </tr>
        `;
    });
    html += '</tbody></table>';

    listDiv.innerHTML = html;
}

function selectMusteriYetkili(yetkiliId, yetkiliAdi) {
    document.getElementById('musteri_yetkili').value = yetkiliAdi;
    document.getElementById('musteri_yetkili_id').value = yetkiliId;
    closeMusteriYetkiliModal();
}


function validateForm() {
    const requiredFields = [
        { id: 'marka', name: 'Marka' },
        { id: 'gelis_kanali', name: 'Geliş Kanalı', type: 'radio' },
        { id: 'olasilik', name: 'Olasılık' },
        { id: 'bayi', name: 'Bayi' },
        { id: 'bayi_yetkili', name: 'Bayi Yetkili' },
        { id: 'musteri', name: 'Müşteri' },
        { id: 'musteri_yetkili', name: 'Müşteri Yetkili' }
    ];

    const errors = [];

    requiredFields.forEach(function(field) {
        if (field.type === 'radio') {
            // Radio button kontrolü
            const radioButtons = document.querySelectorAll('input[name="' + field.id + '"]');
            let isChecked = false;
            radioButtons.forEach(function(radio) {
                if (radio.checked) isChecked = true;
            });
            if (!isChecked) {
                errors.push(field.name + ' seçilmelidir.');
            }
        } else {
            // Normal input kontrolü
            const element = document.getElementById(field.id);
            if (!element || !element.value || element.value.trim() === '') {
                errors.push(field.name + ' seçilmelidir.');
            }
        }
    });

    if (errors.length > 0) {
        const errorMessage = 'Aşağıdaki alanlar zorunludur:\n\n' + errors.join('\n');
        alert(errorMessage);
        return false;
    }

    return true;
}

function submitForm() {
    // Önce form validasyonu yap
    if (!validateForm()) {
        return;
    }

    jQuery('#loading').show();
    var formData = jQuery('#firsat-form').serialize();
    formData += '&action=save_firsat';

    jQuery.ajax({
        url: '<?php echo get_stylesheet_directory_uri(); ?>/erp/mod/yeni_firsat.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            jQuery('#loading').hide();
            if (response.success) {
                showAlert('Fırsat başarıyla oluşturuldu! Fırsat No: ' + response.firsat_no, 'success');
                jQuery('#firsat-form')[0].reset();
                setTimeout(function() {
                    window.location.href = '<?php echo admin_url('admin.php?page=firsatlar'); ?>';
                }, 2000);
            } else {
                showAlert(response.message, 'danger');
            }
        },
        error: function() {
            jQuery('#loading').hide();
            showAlert('Bir hata oluştu. Lütfen tekrar deneyin.', 'danger');
        }
    });
}

function showAlert(message, type) {
    var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    var html = '<div class="alert ' + alertClass + '">' + message + '</div>';
    jQuery('#alert-container').html(html);
    setTimeout(function() {
        jQuery('#alert-container').html('');
    }, 5000);
}

// CRUD Functions for Bayi Yetkili
function saveBayiYetkili() {
    const bayiKodu = document.getElementById('bayi_kodu').value;
    const yetkili = document.getElementById('new-bayi-yetkili-name').value.trim();
    const telefon = document.getElementById('new-bayi-yetkili-phone').value.trim();
    const eposta = document.getElementById('new-bayi-yetkili-email').value.trim();

    if (!yetkili) {
        alert('Yetkili adı gereklidir.');
        return;
    }

    jQuery.ajax({
        url: '<?php echo get_stylesheet_directory_uri(); ?>/erp/mod/yeni_firsat.php?action=save_bayi_yetkili',
        type: 'POST',
        data: {
            bayi_kodu: bayiKodu,
            yetkili: yetkili,
            telefon: telefon,
            eposta: eposta
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Inputları temizle
                document.getElementById('new-bayi-yetkili-name').value = '';
                document.getElementById('new-bayi-yetkili-phone').value = '';
                document.getElementById('new-bayi-yetkili-email').value = '';

                // Listeyi yenile
                loadBayiYetkililer(bayiKodu);

                alert(response.message);
            } else {
                alert('Hata: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Save bayi yetkili error:', status, error);
            alert('Kayıt sırasında hata oluştu: ' + error);
        }
    });
}

function editBayiYetkili(yetkiliId, currentName, currentPhone, currentEmail) {
    const row = document.getElementById(`bayi-yetkili-row-${yetkiliId}`);

    // Satırı edit moduna çevir
    row.innerHTML = `
        <td><input type="text" id="edit-bayi-yetkili-name-${yetkiliId}" value="${currentName}" class="yetkili-edit-input"></td>
        <td><input type="text" id="edit-bayi-yetkili-phone-${yetkiliId}" value="${currentPhone}" class="yetkili-edit-input"></td>
        <td><input type="email" id="edit-bayi-yetkili-email-${yetkiliId}" value="${currentEmail}" class="yetkili-edit-input"></td>
        <td>
            <div class="yetkili-buttons">
                <button class="btn-small btn-edit" onclick="updateBayiYetkili(${yetkiliId})">Kaydet</button>
                <button class="btn-small btn-delete" onclick="cancelEditBayiYetkili(${yetkiliId}, '${currentName}', '${currentPhone}', '${currentEmail}')">İptal</button>
            </div>
        </td>
    `;
}

function updateBayiYetkili(yetkiliId) {
    const yetkili = document.getElementById(`edit-bayi-yetkili-name-${yetkiliId}`).value.trim();
    const telefon = document.getElementById(`edit-bayi-yetkili-phone-${yetkiliId}`).value.trim();
    const eposta = document.getElementById(`edit-bayi-yetkili-email-${yetkiliId}`).value.trim();

    if (!yetkili) {
        alert('Yetkili adı gereklidir.');
        return;
    }

    jQuery.ajax({
        url: '<?php echo get_stylesheet_directory_uri(); ?>/erp/mod/yeni_firsat.php?action=update_bayi_yetkili',
        type: 'POST',
        data: {
            id: yetkiliId,
            yetkili: yetkili,
            telefon: telefon,
            eposta: eposta
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Listeyi yenile
                const bayiKodu = document.getElementById('bayi_kodu').value;
                loadBayiYetkililer(bayiKodu);
            } else {
                alert('Hata: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Update bayi yetkili error:', status, error);
            alert('Güncelleme sırasında hata oluştu: ' + error);
        }
    });
}

function cancelEditBayiYetkili(yetkiliId, originalName, originalPhone, originalEmail) {
    const row = document.getElementById(`bayi-yetkili-row-${yetkiliId}`);

    // Satırı orijinal haline döndür
    row.innerHTML = `
        <td onclick="selectBayiYetkili('${yetkiliId}', '${originalName}')" style="cursor: pointer;" class="yetkili-name-cell">${originalName || 'N/A'}</td>
        <td class="yetkili-phone-cell">${originalPhone || ''}</td>
        <td class="yetkili-email-cell">${originalEmail || ''}</td>
        <td>
            <div class="yetkili-buttons">
                <button class="btn-small btn-edit" onclick="editBayiYetkili(${yetkiliId}, '${originalName}', '${originalPhone}', '${originalEmail}')">Düzenle</button>
                <button class="btn-small btn-delete" onclick="deleteBayiYetkili(${yetkiliId})">Sil</button>
            </div>
        </td>
    `;
}

function deleteBayiYetkili(yetkiliId) {
    // Önce yetkili bilgilerini alın
    const row = document.getElementById(`bayi-yetkili-row-${yetkiliId}`);
    const nameCell = row.querySelector('.yetkili-name-cell');
    const phoneCell = row.querySelector('.yetkili-phone-cell');
    const emailCell = row.querySelector('.yetkili-email-cell');

    const yetkiliName = nameCell ? nameCell.textContent : '';
    const yetkiliPhone = phoneCell ? phoneCell.textContent : '';
    const yetkiliEmail = emailCell ? emailCell.textContent : '';

    // Satırı delete confirmation moduna çevir
    row.className = 'yetkili-delete-row';
    row.innerHTML = `
        <td colspan="3" style="text-align: center; font-weight: bold; color: #721c24;">
            Bu yetkiliyi silmek istediğinizden emin misiniz: "${yetkiliName}"?
        </td>
        <td>
            <div class="yetkili-buttons">
                <button class="btn-small btn-delete" onclick="confirmDeleteBayiYetkili(${yetkiliId})">Sil</button>
                <button class="btn-small btn-edit" onclick="cancelDeleteBayiYetkili(${yetkiliId}, '${yetkiliName}', '${yetkiliPhone}', '${yetkiliEmail}')">Vazgeç</button>
            </div>
        </td>
    `;
}

function confirmDeleteBayiYetkili(yetkiliId) {
    jQuery.ajax({
        url: '<?php echo get_stylesheet_directory_uri(); ?>/erp/mod/yeni_firsat.php?action=delete_bayi_yetkili',
        type: 'POST',
        data: {
            id: yetkiliId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Listeyi yenile
                const bayiKodu = document.getElementById('bayi_kodu').value;
                loadBayiYetkililer(bayiKodu);
            } else {
                alert('Hata: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Delete bayi yetkili error:', status, error);
            alert('Silme sırasında hata oluştu: ' + error);
        }
    });
}

function cancelDeleteBayiYetkili(yetkiliId, originalName, originalPhone, originalEmail) {
    const row = document.getElementById(`bayi-yetkili-row-${yetkiliId}`);

    // Satırı orijinal haline döndür
    row.className = '';
    row.innerHTML = `
        <td onclick="selectBayiYetkili('${yetkiliId}', '${originalName}')" style="cursor: pointer;" class="yetkili-name-cell">${originalName || 'N/A'}</td>
        <td class="yetkili-phone-cell">${originalPhone || ''}</td>
        <td class="yetkili-email-cell">${originalEmail || ''}</td>
        <td>
            <div class="yetkili-buttons">
                <button class="btn-small btn-edit" onclick="editBayiYetkili(${yetkiliId}, '${originalName}', '${originalPhone}', '${originalEmail}')">Düzenle</button>
                <button class="btn-small btn-delete" onclick="deleteBayiYetkili(${yetkiliId})">Sil</button>
            </div>
        </td>
    `;
}

// CRUD Functions for Musteri Yetkili
function saveMusteriYetkili() {
    const musteriId = document.getElementById('musteri_id').value;
    const yetkili = document.getElementById('new-musteri-yetkili-name').value.trim();
    const telefon = document.getElementById('new-musteri-yetkili-phone').value.trim();
    const eposta = document.getElementById('new-musteri-yetkili-email').value.trim();

    if (!yetkili) {
        alert('Yetkili adı gereklidir.');
        return;
    }

    jQuery.ajax({
        url: '<?php echo get_stylesheet_directory_uri(); ?>/erp/mod/yeni_firsat.php?action=save_musteri_yetkili',
        type: 'POST',
        data: {
            musteri_id: musteriId,
            yetkili: yetkili,
            telefon: telefon,
            eposta: eposta
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Inputları temizle
                document.getElementById('new-musteri-yetkili-name').value = '';
                document.getElementById('new-musteri-yetkili-phone').value = '';
                document.getElementById('new-musteri-yetkili-email').value = '';

                // Listeyi yenile
                loadMusteriYetkililer(musteriId);

                alert(response.message);
            } else {
                alert('Hata: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Save musteri yetkili error:', status, error);
            alert('Kayıt sırasında hata oluştu: ' + error);
        }
    });
}

function editMusteriYetkili(yetkiliId, currentName, currentPhone, currentEmail) {
    const row = document.getElementById(`musteri-yetkili-row-${yetkiliId}`);

    // Satırı edit moduna çevir
    row.innerHTML = `
        <td><input type="text" id="edit-musteri-yetkili-name-${yetkiliId}" value="${currentName}" class="yetkili-edit-input"></td>
        <td><input type="text" id="edit-musteri-yetkili-phone-${yetkiliId}" value="${currentPhone}" class="yetkili-edit-input"></td>
        <td><input type="email" id="edit-musteri-yetkili-email-${yetkiliId}" value="${currentEmail}" class="yetkili-edit-input"></td>
        <td>
            <div class="yetkili-buttons">
                <button class="btn-small btn-edit" onclick="updateMusteriYetkili(${yetkiliId})">Kaydet</button>
                <button class="btn-small btn-delete" onclick="cancelEditMusteriYetkili(${yetkiliId}, '${currentName}', '${currentPhone}', '${currentEmail}')">İptal</button>
            </div>
        </td>
    `;
}

function updateMusteriYetkili(yetkiliId) {
    const yetkili = document.getElementById(`edit-musteri-yetkili-name-${yetkiliId}`).value.trim();
    const telefon = document.getElementById(`edit-musteri-yetkili-phone-${yetkiliId}`).value.trim();
    const eposta = document.getElementById(`edit-musteri-yetkili-email-${yetkiliId}`).value.trim();

    if (!yetkili) {
        alert('Yetkili adı gereklidir.');
        return;
    }

    jQuery.ajax({
        url: '<?php echo get_stylesheet_directory_uri(); ?>/erp/mod/yeni_firsat.php?action=update_musteri_yetkili',
        type: 'POST',
        data: {
            id: yetkiliId,
            yetkili: yetkili,
            telefon: telefon,
            eposta: eposta
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Listeyi yenile
                const musteriId = document.getElementById('musteri_id').value;
                loadMusteriYetkililer(musteriId);
            } else {
                alert('Hata: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Update musteri yetkili error:', status, error);
            alert('Güncelleme sırasında hata oluştu: ' + error);
        }
    });
}

function cancelEditMusteriYetkili(yetkiliId, originalName, originalPhone, originalEmail) {
    const row = document.getElementById(`musteri-yetkili-row-${yetkiliId}`);

    // Satırı orijinal haline döndür
    row.innerHTML = `
        <td onclick="selectMusteriYetkili('${yetkiliId}', '${originalName}')" style="cursor: pointer;" class="yetkili-name-cell">${originalName || 'N/A'}</td>
        <td class="yetkili-phone-cell">${originalPhone || ''}</td>
        <td class="yetkili-email-cell">${originalEmail || ''}</td>
        <td>
            <div class="yetkili-buttons">
                <button class="btn-small btn-edit" onclick="editMusteriYetkili(${yetkiliId}, '${originalName}', '${originalPhone}', '${originalEmail}')">Düzenle</button>
                <button class="btn-small btn-delete" onclick="deleteMusteriYetkili(${yetkiliId})">Sil</button>
            </div>
        </td>
    `;
}

function deleteMusteriYetkili(yetkiliId) {
    // Önce yetkili bilgilerini alın
    const row = document.getElementById(`musteri-yetkili-row-${yetkiliId}`);
    const nameCell = row.querySelector('.yetkili-name-cell');
    const phoneCell = row.querySelector('.yetkili-phone-cell');
    const emailCell = row.querySelector('.yetkili-email-cell');

    const yetkiliName = nameCell ? nameCell.textContent : '';
    const yetkiliPhone = phoneCell ? phoneCell.textContent : '';
    const yetkiliEmail = emailCell ? emailCell.textContent : '';

    // Satırı delete confirmation moduna çevir
    row.className = 'yetkili-delete-row';
    row.innerHTML = `
        <td colspan="3" style="text-align: center; font-weight: bold; color: #721c24;">
            Bu yetkiliyi silmek istediğinizden emin misiniz: "${yetkiliName}"?
        </td>
        <td>
            <div class="yetkili-buttons">
                <button class="btn-small btn-delete" onclick="confirmDeleteMusteriYetkili(${yetkiliId})">Sil</button>
                <button class="btn-small btn-edit" onclick="cancelDeleteMusteriYetkili(${yetkiliId}, '${yetkiliName}', '${yetkiliPhone}', '${yetkiliEmail}')">Vazgeç</button>
            </div>
        </td>
    `;
}

function confirmDeleteMusteriYetkili(yetkiliId) {
    jQuery.ajax({
        url: '<?php echo get_stylesheet_directory_uri(); ?>/erp/mod/yeni_firsat.php?action=delete_musteri_yetkili',
        type: 'POST',
        data: {
            id: yetkiliId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Listeyi yenile
                const musteriId = document.getElementById('musteri_id').value;
                loadMusteriYetkililer(musteriId);
            } else {
                alert('Hata: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Delete musteri yetkili error:', status, error);
            alert('Silme sırasında hata oluştu: ' + error);
        }
    });
}

function cancelDeleteMusteriYetkili(yetkiliId, originalName, originalPhone, originalEmail) {
    const row = document.getElementById(`musteri-yetkili-row-${yetkiliId}`);

    // Satırı orijinal haline döndür
    row.className = '';
    row.innerHTML = `
        <td onclick="selectMusteriYetkili('${yetkiliId}', '${originalName}')" style="cursor: pointer;" class="yetkili-name-cell">${originalName || 'N/A'}</td>
        <td class="yetkili-phone-cell">${originalPhone || ''}</td>
        <td class="yetkili-email-cell">${originalEmail || ''}</td>
        <td>
            <div class="yetkili-buttons">
                <button class="btn-small btn-edit" onclick="editMusteriYetkili(${yetkiliId}, '${originalName}', '${originalPhone}', '${originalEmail}')">Düzenle</button>
                <button class="btn-small btn-delete" onclick="deleteMusteriYetkili(${yetkiliId})">Sil</button>
            </div>
        </td>
    `;
}
</script>