<?php
require_once __DIR__ . '/../service.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Method not allowed']);
    exit;
}

$bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
if ($bookId <= 0) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => 'Invalid book id']);
    exit;
}

try {
    $service = new DatabaseService();
    $ok = $service->softDeleteBook($bookId);

    if (!$ok) {
        http_response_code(404);
        echo json_encode(['ok' => false, 'message' => 'Book not found or already deleted']);
        exit;
    }

    echo json_encode(['ok' => true, 'message' => 'Book deleted']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Server error']);
}
