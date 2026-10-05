<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'config.php';

$userId = $_SESSION['user_id'];
$message = '';
$error = '';

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($name === '') {
        $error = 'Name cannot be empty.';
    } else {

        $profilePhoto = $user['profile_photo'];

        if (
            isset($_FILES['profile_photo']) &&
            $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK
        ) {

            $allowedTypes = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp'
            ];

            $mime = mime_content_type($_FILES['profile_photo']['tmp_name']);

            if (!isset($allowedTypes[$mime])) {
                $error = 'Only JPG, PNG, and WEBP images are allowed.';
            } else {

                $uploadDir = '/var/www/html/uploads/profile';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $extension = $allowedTypes[$mime];
                $filename = 'profile_' . $userId . '_' . time() . '.' . $extension;

                $target = $uploadDir . '/' . $filename;

                if (move_uploaded_file(
                    $_FILES['profile_photo']['tmp_name'],
                    $target
                )) {
                    $profilePhoto = 'uploads/profile/' . $filename;
                } else {
                    $error = 'Failed to upload profile photo.';
                }
            }
        }

        if ($error === '') {

            $stmt = $pdo->prepare("
                UPDATE users
                SET name = ?, profile_photo = ?, description = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $name,
                $profilePhoto,
                $description,
                $userId
            ]);

            $_SESSION['user_name'] = $name;

            header("Location: profile.php?saved=1");
            exit;
        }
    }
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (isset($_GET['saved'])) {
    $message = 'Profile updated successfully.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - zarizar.net</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            color: white;

            background:
                radial-gradient(circle at 15% 20%, rgba(42, 162, 126, 0.25), transparent 35%),
                radial-gradient(circle at 85% 25%, rgba(42, 138, 162, 0.25), transparent 35%),
                radial-gradient(circle at 50% 90%, rgba(42, 78, 162, 0.30), transparent 40%),
                #1A196C;
        }

        .page {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .profile-card {
            width: 100%;
            max-width: 620px;
            padding: 35px;
            border-radius: 28px;

            background: rgba(255, 255, 255, 0.10);
            border: 1px solid rgba(255, 255, 255, 0.18);

            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);

            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.25);
        }

        .top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .top h1 {
            margin: 0;
            font-size: 28px;
        }

        .back {
            color: white;
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 14px;

            background: rgba(255, 255, 255, 0.10);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .avatar-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .avatar {
            width: 120px;
            height: 120px;
            margin: 0 auto 15px;

            border-radius: 50%;
            overflow: hidden;

            display: flex;
            align-items: center;
            justify-content: center;

            background: rgba(255, 255, 255, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.25);

            font-size: 42px;
            font-weight: bold;
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .role {
            opacity: 0.65;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .message {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 14px;
            background: rgba(42, 162, 126, 0.20);
            border: 1px solid rgba(42, 162, 126, 0.35);
        }

        .error {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 14px;
            background: rgba(255, 80, 80, 0.15);
            border: 1px solid rgba(255, 120, 120, 0.3);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            opacity: 0.8;
        }

        input,
        textarea {
            width: 100%;
            padding: 14px 16px;
            margin-bottom: 20px;

            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 14px;

            background: rgba(255, 255, 255, 0.08);
            color: white;

            outline: none;
            font-size: 15px;
        }

        input::placeholder,
        textarea::placeholder {
            color: rgba(255, 255, 255, 0.45);
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        input[type="file"] {
            padding: 12px;
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 10px;
        }

        button,
        .cancel {
            flex: 1;
            padding: 14px;
            border-radius: 14px;
            font-size: 15px;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
        }

        button {
            border: 1px solid rgba(255, 255, 255, 0.25);
            background: rgba(255, 255, 255, 0.16);
            color: white;
        }

        .cancel {
            border: 1px solid rgba(255, 255, 255, 0.15);
            background: rgba(255, 255, 255, 0.06);
        }

        .admin-box {
            margin-top: 30px;
            padding: 20px;

            border-radius: 20px;

            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.14);
        }

        .admin-box h2 {
            margin-top: 0;
            font-size: 19px;
        }

        .admin-box p {
            opacity: 0.7;
            font-size: 14px;
            line-height: 1.5;
        }
    </style>
</head>

<body>

<div class="page">

    <div class="profile-card">

        <div class="top">
            <h1>Profile</h1>
            <a href="index.php" class="back">← Back</a>
        </div>

        <div class="avatar-section">

            <div class="avatar">
                <?php if (!empty($user['profile_photo'])): ?>
                    <img src="<?= htmlspecialchars($user['profile_photo']) ?>" alt="Profile">
                <?php else: ?>
                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                <?php endif; ?>
            </div>

            <div>
                <?= htmlspecialchars($user['name']) ?>
            </div>

            <div class="role">
                <?= htmlspecialchars($user['role']) ?>
            </div>

        </div>

        <?php if ($message): ?>
            <div class="message">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <label>Name</label>
            <input
                type="text"
                name="name"
                value="<?= htmlspecialchars($user['name']) ?>"
                maxlength="100"
                required
            >

            <label>Profile Photo</label>
            <input
                type="file"
                name="profile_photo"
                accept=".jpg,.jpeg,.png,.webp"
            >

            <label>Description</label>
            <textarea
                name="description"
                maxlength="1000"
                placeholder="Tell something about yourself..."
            ><?= htmlspecialchars($user['description'] ?? '') ?></textarea>

            <div class="actions">
                <a href="index.php" class="cancel">Cancel</a>
                <button type="submit">Save Changes</button>
            </div>

        </form>

        <?php if ($user['role'] === 'admin'): ?>

            <div class="admin-box">
                <h2>Admin Control</h2>
                <p>
                    You are logged in as an administrator.
                    Aircraft management controls will appear here.
                </p>
            </div>

        <?php endif; ?>

    </div>

</div>

</body>
</html>
