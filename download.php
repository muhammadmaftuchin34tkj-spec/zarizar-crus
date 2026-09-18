<?php
require 'config.php';

$id = $_GET['id'] ?? '';

if (!is_numeric($id)) {
    die('ID tidak valid.');
}

$stmt = $pdo->prepare("SELECT foto FROM photos WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    die('Data tidak ditemukan.');
}

$file = __DIR__ . '/uploads/' . basename($data['foto']);

if (!file_exists($file)) {
    die('File tidak ditemukan.');
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($file) . '"');
header('Content-Length: ' . filesize($file));
header('Cache-Control: no-cache');

readfile($file);
exit;
