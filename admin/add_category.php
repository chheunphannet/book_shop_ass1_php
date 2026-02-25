<?php
require_once __DIR__ . '/../service.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Method not allowed']);
    exit;
}

$name = trim($_POST['name'] ?? '');

if ($name === '') {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => 'Category name is required']);
    exit;
}

if (strlen($name) > 25) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => 'Category name is too long']);
    exit;
}

try {
    $service = new DatabaseService();
    $categoryId = $service->createCategory($name);

    if ($categoryId === null) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'message' => 'Failed to add category']);
        exit;
    }

    echo json_encode([
        'ok' => true,
        'message' => 'Category created successfully',
        'category_id' => $categoryId,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Server error']);
}
