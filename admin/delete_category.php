<?php
require_once __DIR__ . '/../service.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Method not allowed']);
    exit;
}

$categoryId = isset($_POST['category_id']) ? (int) $_POST['category_id'] : 0;

if ($categoryId <= 0) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => 'Invalid category id']);
    exit;
}

try {
    $service = new DatabaseService();

    if ($service->countBooksInCategory($categoryId) > 0) {
        http_response_code(409);
        echo json_encode([
            'ok' => false,
            'message' => 'Cannot delete category that already has books',
        ]);
        exit;
    }

    $ok = $service->deleteCategory($categoryId);

    if (!$ok) {
        http_response_code(404);
        echo json_encode(['ok' => false, 'message' => 'Category not found']);
        exit;
    }

    echo json_encode(['ok' => true, 'message' => 'Category deleted successfully']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Server error']);
}
