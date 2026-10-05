<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
?>
<?php
require 'config.php';

$message = '';

function getRarity($aircraftType)
{
    $type = strtoupper($aircraftType);

    if (
        strpos($type, 'A380') !== false ||
        strpos($type, '747') !== false ||
        strpos($type, 'MD-11') !== false
    ) {
        return 5;
    }

    if (
        strpos($type, 'A340') !== false ||
        strpos($type, 'A300') !== false ||
        strpos($type, 'A310') !== false
    ) {
        return 4;
    }

    if (
        strpos($type, 'A330') !== false ||
        strpos($type, 'A350') !== false ||
        strpos($type, 'B777') !== false ||
        strpos($type, 'B787') !== false
    ) {
        return 3;
    }

    if (
        strpos($type, 'A320') !== false ||
        strpos($type, 'A321') !== false ||
        strpos($type, 'B737') !== false
    ) {
        return 2;
    }

    return 2;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $registration = trim($_POST['registration_number'] ?? '');
    $aircraftType = trim($_POST['aircraft_type'] ?? '');
    $airport = trim($_POST['airport'] ?? '');

    if (
        $registration === '' ||
        $aircraftType === '' ||
        $airport === ''
    ) {
        $message = 'Please complete all information.';
    } elseif (
        !isset($_FILES['foto']) ||
        $_FILES['foto']['error'] !== UPLOAD_ERR_OK
    ) {
        $message = 'Please select an aircraft photo.';
    } else {

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $extension = strtolower(
            pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION)
        );

        if (!in_array($extension, $allowed, true)) {

            $message = 'Only JPG, JPEG, PNG, and WEBP files are allowed.';

        } else {

            $filename = uniqid('aircraft_', true) . '.' . $extension;
            $destination = __DIR__ . '/uploads/' . $filename;

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $destination)) {

                $stmt = $pdo->prepare(
                    "INSERT INTO photos
                    (registration_number, aircraft_type, airport, foto)
                    VALUES (?, ?, ?, ?)"
                );

                $stmt->execute([
                    $registration,
                    $aircraftType,
                    $airport,
                    $filename
                ]);

                header('Location: index.php');
                exit;

            } else {

                $message = 'Failed to upload the aircraft photo.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Add Aircraft | Planespotting</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --color-1: #2AA27E;
            --color-2: #2A8AA2;
            --color-3: #2A4EA2;
        }

        body {
            font-family: Arial, sans-serif;
            min-height: 100vh;
            background:
                radial-gradient(
                    circle at 15% 10%,
                    var(--color-3) 0%,
                    transparent 35%
                ),
                radial-gradient(
                    circle at 85% 90%,
                    var(--color-1) 0%,
                    transparent 40%
                ),
                #1A196C;
            color: white;
        }

        .page {
            max-width: 1100px;
            margin: 0 auto;
            padding: 55px 20px;
        }

        .page-title {
            text-align: center;
            margin-bottom: 35px;
        }

        .page-title h1 {
            font-size: 40px;
            letter-spacing: 5px;
            margin-bottom: 10px;
        }

        .page-title p {
            color: rgba(255,255,255,0.8);
        }

        .form-card {
            padding: 35px;
            border-radius: 22px;

            background: rgba(255,255,255,0.10);

            border: 1px solid rgba(255,255,255,0.25);

            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);

            box-shadow:
                0 10px 40px rgba(0,0,0,0.20);

            transition: all 0.35s ease;
        }

        .form-card:hover {
            box-shadow:
                0 15px 45px rgba(255,255,255,0.12),
                0 10px 40px rgba(0,0,0,0.25);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .photo-section {
            grid-row: span 2;
        }

        label {
            display: block;
            margin-bottom: 10px;

            color: white;

            font-size: 13px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        input[type="text"],
        input[type="file"] {
            width: 100%;

            padding: 14px 16px;

            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 12px;

            background: rgba(255,255,255,0.08);

            color: white;

            outline: none;

            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);

            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="file"]:focus {
            border-color: rgba(255,255,255,0.65);

            box-shadow:
                0 0 18px rgba(255,255,255,0.15);
        }

        input::placeholder {
            color: rgba(255,255,255,0.55);
        }

        input[type="file"]::file-selector-button {
            margin-right: 12px;

            padding: 9px 14px;

            border: none;
            border-radius: 8px;

            background: rgba(255,255,255,0.15);
            color: white;

            cursor: pointer;
        }

        .photo-box {
            min-height: 250px;

            display: flex;
            align-items: center;
            justify-content: center;

            border: 1px dashed rgba(255,255,255,0.4);
            border-radius: 16px;

            background: rgba(255,255,255,0.05);

            transition: all 0.35s ease;
        }

        .photo-box:hover {
            background: rgba(255,255,255,0.10);

            box-shadow:
                0 0 30px rgba(255,255,255,0.12);
        }

        .photo-box-content {
            width: 100%;
            padding: 20px;
            text-align: center;
        }

        .photo-box-content p {
            margin-bottom: 15px;
            color: rgba(255,255,255,0.75);
        }

        .bottom-section {
            margin-top: 30px;
        }

        .rarity {
            margin-top: 25px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 16px 18px;

            border-radius: 12px;

            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.15);
        }

        .rarity-label {
            font-size: 14px;
            color: rgba(255,255,255,0.8);
        }

        .stars {
            letter-spacing: 4px;
            font-size: 20px;
            color: white;
        }

        .actions {
            margin-top: 30px;

            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .button {
            padding: 12px 20px;

            border-radius: 10px;

            color: white;
            text-decoration: none;

            border: 1px solid rgba(255,255,255,0.25);

            background: rgba(255,255,255,0.08);

            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);

            cursor: pointer;

            transition: all 0.3s ease;
        }

        .button:hover {
            background: rgba(255,255,255,0.15);

            box-shadow:
                0 0 25px rgba(255,255,255,0.15);

            transform: translateY(-2px);
        }

        .message {
            margin-bottom: 20px;

            padding: 13px 16px;

            border-radius: 10px;

            background: rgba(255,80,80,0.15);
            border: 1px solid rgba(255,120,120,0.35);

            color: white;
        }

        @media (max-width: 700px) {

            .form-grid {
                grid-template-columns: 1fr;
            }

            .photo-section {
                grid-row: auto;
            }

            .form-card {
                padding: 22px;
            }

            .page-title h1 {
                font-size: 30px;
            }

            .actions {
                flex-direction: column;
            }

            .button {
                text-align: center;
            }
        }

    </style>

</head>

<body>

<div class="page">

    <div class="page-title">

        <h1>ADD AIRCRAFT</h1>

        <p>
            Add a new aircraft to your planespotting collection
        </p>

    </div>

    <?php if ($message): ?>

        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>

    <form
        class="form-card"
        method="POST"
        enctype="multipart/form-data"
    >

        <div class="form-grid">

            <div class="photo-section">

                <label>Aircraft Photo</label>

                <div class="photo-box">

                    <div class="photo-box-content">

                        <p>
                            Upload an aircraft photo
                        </p>

                        <input
                            type="file"
                            name="foto"
                            accept=".jpg,.jpeg,.png,.webp"
                            required
                        >

                    </div>

                </div>

            </div>

            <div>

                <label for="registration_number">
                    Registration Number
                </label>

                <input
                    type="text"
                    id="registration_number"
                    name="registration_number"
                    placeholder="Example: PK-GIF"
                    required
                >

            </div>

            <div>

                <label for="airport">
                    Airport
                </label>

                <input
                    type="text"
                    id="airport"
                    name="airport"
                    placeholder="Example: Juanda International Airport"
                    required
                >

            </div>

        </div>

        <div class="bottom-section">

            <label for="aircraft_type">
                Aircraft Type
            </label>

            <input
                type="text"
                id="aircraft_type"
                name="aircraft_type"
                placeholder="Example: A330-300 NEO"
                required
            >

            <div class="rarity">

                <span class="rarity-label">
                    Automatic Aircraft Rarity
                </span>

                <span class="stars">
                    ★★★★★
                </span>

            </div>

        </div>

        <div class="actions">

            <a
                href="index.php"
                class="button"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="button"
            >
                Add Aircraft
            </button>

        </div>

    </form>

</div>

</body>

</html>
