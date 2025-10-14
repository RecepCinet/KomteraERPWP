<?PHP

error_reporting(E_ALL);
ini_set('display_erros', true);

session_start();

include '../../_conn.php';
require_once '../../inc/table_helper.php';

// Tek bir etkinlik getir (ID parametresi varsa)
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "SELECT *,
        CASE
            WHEN e.tarih_bit < GETDATE() THEN 'Bitti'
            ELSE 'Devam'
        END AS BITTI
    FROM " . getTableName('aa_erp_kt_etkinlikler') . " e
    WHERE e.id = :id";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
} else {
    // Tüm etkinlikleri getir
    $sql = "SELECT *,
        CASE
            WHEN e.tarih_bit < GETDATE() THEN 'Bitti'
            ELSE 'Devam'
        END AS BITTI
    FROM " . getTableName('aa_erp_kt_etkinlikler') . " e
    ORDER BY e.tarih_bit DESC";

    try {
        $stmt = $conn->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Hata: " . $e->getMessage();
        exit;
    }
}

$response = [
    'data' => $data
];

if (isset($_GET['callback'])) {
    echo $_GET['callback'] . '(' . json_encode($response) . ')';
} else {
    echo json_encode($response);
}
?>
