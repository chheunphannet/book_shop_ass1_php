<?php
require_once __DIR__ . '/../service.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Method not allowed']);
    exit;
}

$bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
$title = trim($_POST['title'] ?? '');
$categoryName = trim($_POST['category'] ?? '');
$pageNumberRaw = $_POST['page_number'] ?? '';
$unitPriceRaw = $_POST['unit_price'] ?? null;
$stockQtyRaw = $_POST['stock_quantity'] ?? null;

if (
    $bookId <= 0 ||
    $title === '' ||
    $categoryName === '' ||
    $unitPriceRaw === null ||
    $stockQtyRaw === null ||
    !is_numeric($unitPriceRaw) ||
    !is_numeric($stockQtyRaw)
) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => 'Invalid input']);
    exit;
}

$pageNumber = 0;
if ($pageNumberRaw !== '') {
    if (!is_numeric($pageNumberRaw) || (int)$pageNumberRaw < 0) {
        http_response_code(422);
        echo json_encode(['ok' => false, 'message' => 'Pages must be >= 0']);
        exit;
    }
    $pageNumber = (int)$pageNumberRaw;
}

$unitPrice = (float)$unitPriceRaw;
$stockQty = (int)$stockQtyRaw;

if ($unitPrice < 0 || $stockQty < 0) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => 'Price and stock must be >= 0']);
    exit;
}

$bookCoverBinary = null;
if (isset($_FILES['book_cover']) && $_FILES['book_cover']['error'] === UPLOAD_ERR_OK) {
    $tmpFile = $_FILES['book_cover']['tmp_name'];
    if (is_uploaded_file($tmpFile)) {
        $bookCoverBinary = file_get_contents($tmpFile);
        if ($bookCoverBinary === false) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'message' => 'Invalid uploaded file']);
            exit;
        }
    }
}

try {
    $service = new DatabaseService();
    $ok = $service->updateBookByCategoryName(
        $bookId,
        $title,
        $categoryName,
        $pageNumber,
        $unitPrice,
        $stockQty,
        $bookCoverBinary
    );

    if (!$ok) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'message' => 'Failed to update book']);
        exit;
    }

    echo json_encode(['ok' => true, 'message' => 'Book updated successfully']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Server error']);
}
