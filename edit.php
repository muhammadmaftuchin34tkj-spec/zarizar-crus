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

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');

    if ($nama === '') {
        $message = 'Nama wajib diisi.';
    } else {
        $stmt = $pdo->prepare(
            "UPDATE photos SET nama = ?, deskripsi = ? WHERE id = ?"
        );
        $stmt->execute([$nama, $deskripsi, $id]);

        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data - Zarizar CRUD</title>
</head>
<body>

<h1>Edit Data Foto</h1>

<?php if ($message): ?>
    <p><strong><?= htmlspecialchars($message) ?></strong></p>
<?php endif; ?>

<form method="POST">

    <p>
        <label>Nama:</label><br>
        <input
            type="text"
            name="nama"
            value="<?= htmlspecialchars($data['nama']) ?>"
            required
        >
    </p>

    <p>
        <label>Deskripsi:</label><br>
        <textarea
            name="deskripsi"
            rows="4"
        ><?= htmlspecialchars($data['deskripsi']) ?></textarea>
    </p>

    <button type="submit">Simpan Perubahan</button>
    <a href="index.php">Batal</a>

</form>

</body>
</html>
