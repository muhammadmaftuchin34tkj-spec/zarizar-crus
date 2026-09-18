<?php
require 'config.php';

$id = $_GET['id'] ?? '';

if (!is_numeric($id)) {
    die('ID tidak valid.');
}

$stmt = $pdo->prepare("SELECT * FROM photos WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    die('Data tidak ditemukan.');
}

if (!empty($data['foto'])) {
    $file = __DIR__ . '/uploads/' . $data['foto'];

    if (file_exists($file)) {
        unlink($file);
    }
}

$stmt = $pdo->prepare("DELETE FROM photos WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php");
exit;
