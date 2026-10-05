<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
?>
<?php
require 'config.php';

$stmt = $pdo->query("SELECT * FROM photos ORDER BY created_at DESC");
$photos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<script>
(function() {
    const params = new URLSearchParams(window.location.search);

    if (params.get('login') === '1') {
        sessionStorage.setItem('zarizar_logged_in', '1');
        window.history.replaceState({}, document.title, 'index.php');
        return;
    }

    if (sessionStorage.getItem('zarizar_logged_in') !== '1') {
        window.location.href = 'logout.php';
    }
})();
</script>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>PLANESPOTTING</title>

    <style>
        :root {
            --color-1: #2AA27E;
            --color-2: #2A8AA2;
            --color-3: #2A4EA2;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: radial-gradient(circle at 15% 10%, var(--color-3) 0%, transparent 35%), radial-gradient(circle at 85% 90%, var(--color-2) 0%, transparent 40%), var(--color-1);
            color: white;
            min-height: 100vh;
        }

        /* HEADER */
        header {
            text-align: center;
            padding: 55px 20px 35px;
        }

        header h1 {
            color: white;
            font-size: 44px;
            letter-spacing: 6px;
            transition: 0.3s ease;
            cursor: default;
        }


        header p {
            margin-top: 12px;
            color: white;
            opacity: 0.85;
            transition: 0.3s ease;
        }


        /* CONTENT */
        .container {
            max-width: 1100px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .top-bar h2 {
            color: white;
            font-size: 22px;
            transition: 0.3s ease;
        }


        /* ADD BUTTON */
        .add-button {
            color: white;
            text-decoration: none;
            padding: 12px 18px;
            border: 1px solid rgba(255,255,255,0.5);
            border-radius: 10px;
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            transition: all 0.3s ease;
        }

        .add-button:hover {
            background: rgba(255,255,255,0.14);
            box-shadow:
                0 0 12px rgba(255,255,255,0.35),
                0 0 30px rgba(255,255,255,0.15);
            transform: translateY(-2px);
        }

        /* PHOTO GALLERY */
        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
        }

        .card {
            background: white;
            color: #1A196C;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            transition: all 0.35s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow:
                0 10px 30px rgba(255,255,255,0.25),
                0 0 35px rgba(68,25,108,0.35);
        }

        .card img {
            width: 100%;
            height: 210px;
            object-fit: cover;
            display: block;
        }

        .card-content {
            padding: 18px;
        }
	
	.download-btn {
    display: block;
    width: fit-content;
    margin: 0 16px 16px auto;
    padding: 9px 16px;

    color: #1A196C;
    text-decoration: none;
    font-size: 14px;
    font-weight: bold;

    border: 1px solid rgba(255,255,255,0.7);
    border-radius: 999px;
    background: rgba(255,255,255,0.45);

    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);

    box-shadow: 0 4px 15px rgba(0,0,0,0.12);

    transition: all 0.3s ease;
}

.download-btn:hover {
    background: rgba(255,255,255,0.7);
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);

    box-shadow:
        0 0 12px rgba(255,255,255,0.7),
        0 0 25px rgba(42,138,162,0.45);

    transform: translateY(-2px);
}

        .registration {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .info {
            color: #555;
            line-height: 1.8;
        }

        /* EMPTY BOX */
        .empty {
            text-align: center;
            background: rgba(255,255,255,0.10);
            color: white;
            padding: 60px 20px;
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,0.25);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            transition: all 0.35s ease;
        }

        .empty:hover {
            transform: translateY(-4px);
            box-shadow:
                0 10px 30px rgba(255,255,255,0.3),
                0 0 40px rgba(108,25,107,0.3);
        }

        .empty h3 {
            margin-bottom: 8px;
            color: white;
        }

        .empty p {
            color: rgba(255,255,255,0.85);
        }

        /* MOBILE */
        @media (max-width: 600px) {
            header h1 {
                font-size: 32px;
            }

            .top-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .add-button {
                width: 100%;
                text-align: center;
            }
        }
.profile-link {
    position: absolute;
    top: 25px;
    right: 30px;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.25);
    color: white;
    text-decoration: none;
    font-weight: bold;
    font-size: 18px;
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
}

.profile-link img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

    </style>
</head>

<body>

<header>
    <a href="profile.php" class="profile-link">
        <?php
        $profileStmt = $pdo->prepare("SELECT name, profile_photo FROM users WHERE id = ?");
        $profileStmt->execute([$_SESSION["user_id"]]);
        $profileUser = $profileStmt->fetch();
        ?>
        <?php if (!empty($profileUser["profile_photo"])): ?>
            <img src="<?= htmlspecialchars($profileUser["profile_photo"]) ?>" alt="Profile">
        <?php else: ?>
            <?= strtoupper(substr($profileUser["name"], 0, 1)) ?>
        <?php endif; ?>
    </a>
    <h1>PLANESPOTTING</h1>
    <p>Aircraft photography and spotting collection</p>
</header>

<div class="container">

    <div class="top-bar">
        <h2>Aircraft Photos</h2>

        <a href="add.php" class="add-button">
            + Add Aircraft Photo
        </a>
    </div>

    <?php if (count($photos) > 0): ?>

        <div class="gallery">

            <?php foreach ($photos as $photo): ?>

                <div class="card">

                    <img
                        src="uploads/<?php echo htmlspecialchars($photo['foto']); ?>"
                        alt="Aircraft photo"
                    >

                    <div class="card-content">

                        <div class="registration">
                            <?php echo htmlspecialchars($photo['registration_number']); ?>
                        </div>

                        <div class="info">
                            <strong>Type:</strong>
                            <?php echo htmlspecialchars($photo['aircraft_type']); ?>
                            <br>

                            <strong>Airport:</strong>
                            <?php echo htmlspecialchars($photo['airport']); ?>
                        </div>

                    </div>
		     
                     <a href="download.php?id=<?php echo $photo['id']; ?>" class="download-btn">
                        Download
                    </a>

                </div>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="empty">
            <h3>No aircraft photos yet</h3>
            <p>Add your first aircraft photo to start the collection.</p>
        </div>

    <?php endif; ?>

</div>

</body>
</html>
