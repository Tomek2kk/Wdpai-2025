<?php

define('ROOT_PATH', dirname(__DIR__));

header('Content-Type: application/json');

if (!isset($_FILES['upload'])) {
    echo json_encode([
        'error' => [
            'message' => 'Brak pliku'
        ]
    ]);
    exit;
}

$file = $_FILES['upload'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode([
        'error' => [
            'message' => 'Błąd uploadu'
        ]
    ]);
    exit;
}

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

$allowed = ['jpg','jpeg','png','webp','gif'];

if (!in_array($ext, $allowed)) {
    echo json_encode([
        'error' => [
            'message' => 'Zły format pliku'
        ]
    ]);
    exit;
}

$name = uniqid() . "." . $ext;

$path = ROOT_PATH . '/public/uploads/' . $name;

if (!move_uploaded_file($file['tmp_name'], $path)) {
    echo json_encode([
        'error' => [
            'message' => 'Nie można zapisać pliku'
        ]
    ]);
    exit;
}

echo json_encode([
    'url' => '/uploads/' . $name
]);
