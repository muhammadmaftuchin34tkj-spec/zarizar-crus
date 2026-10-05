<?php
require 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');

    if ($nama === '') {
        $message = 'Nama wajib diisi.';
    } elseif (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        $message = 'Foto wajib diupload.';
    } else {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowed, true)) {
            $message = 'Format foto tidak didukung.';
        } else {
            $filename = uniqid('foto_', true) . '.' . $extension;
            $destination = __DIR__ . '/uploads/' . $filename;

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $destination)) {
                $stmt = $pdo->prepare(
                    "INSERT INTO photos (nama, deskripsi, foto)
                     VALUES (?, ?, ?)"
                );
                $stmt->execute([$nama, $deskripsi, $filename]);

                $message = 'Data berhasil ditambahkan.';
            } else {
                $message = 'Gagal menyimpan foto.';
            }
        }
    }
}

$stmt = $pdo->query("SELECT * FROM photos ORDER BY id DESC");
$data = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarizar CRUD</title>
</head>
<body>

<h1>Data Foto Zarizar</h1>

<?php if ($message): ?>
    <p><strong><?= htmlspecialchars($message) ?></strong></p>
<?php endif; ?>

<h2>Tambah Foto</h2>

<form method="POST" enctype="multipart/form-data">
    <p>
        <label>Nama:</label><br>
        <input type="text" name="nama" required>
    </p>

    <p>
        <label>Deskripsi:</label><br>
        <textarea name="deskripsi" rows="4"></textarea>
    </p>

    <p>
        <label>Foto:</label><br>
        <input type="file" name="foto" accept="image/*" required>
    </p>

    <button type="submit">Tambah Data</button>
</form>

<hr>

<h2>Data Foto</h2>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>Deskripsi</th>
        <th>Foto</th>
        <th>Aksi</th>
    </tr>

    <?php foreach ($data as $row): ?>
    <tr>
        <td><?= htmlspecialchars($row['id']) ?></td>
        <td><?= htmlspecialchars($row['nama']) ?></td>
        <td><?= htmlspecialchars($row['deskripsi']) ?></td>
        <td>
            <img
                src="uploads/<?= htmlspecialchars($row['foto']) ?>"
                width="150"
                alt="Foto"
            >
        </td>
        <td>
            <a href="edit.php?id=<?= $row['id'] ?>">Edit</a>
            |
            <a
                href="delete.php?id=<?= $row['id'] ?>"
                onclick="return confirm('Yakin ingin menghapus data ini?')"
            >Hapus</a>
            |
            <a href="download.php?id=<?= $row['id'] ?>">Download</a>
        </td>
    </tr>
    <?php endforeach; ?>

</table>

</body>
</html>
