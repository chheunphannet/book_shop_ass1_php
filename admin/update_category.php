<?php
require_once __DIR__ . '/../service.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Method not allowed']);
    exit;
}

$categoryId = isset($_POST['category_id']) ? (int) $_POST['category_id'] : 0;
$name = trim($_POST['name'] ?? '');

if ($categoryId <= 0 || $name === '') {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => 'Invalid input']);
    exit;
}

if (strlen($name) > 25) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => 'Category name is too long']);
    exit;
}

try {
    $service = new DatabaseService();
    $ok = $service->updateCategoryName($categoryId, $name);

    if (!$ok) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'message' => 'Failed to update category']);
        exit;
    }

    echo json_encode(['ok' => true, 'message' => 'Category updated successfully']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Server error']);
}
