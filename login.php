<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $password === '') {
        $error = 'Please enter your name and password.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE name = ? LIMIT 1");
        $stmt->execute([$name]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            header('Location: index.php?login=1');
            exit;
        } else {
            $error = 'Invalid name or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Zarizar Planespotting</title>

    <style>
        :root {
            --color-1: #2AA27E;
            --color-2: #2A8AA2;
            --color-3: #2A4EA2;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            background:
                radial-gradient(circle at 20% 20%, rgba(42,162,126,.18), transparent 35%),
                radial-gradient(circle at 80% 80%, rgba(42,78,162,.22), transparent 35%),
                #1A196C;
        }

        .login-card {
            width: min(420px, calc(100% - 40px));
            padding: 36px;
            border: 1px solid rgba(255,255,255,.18);
            border-radius: 24px;
            background: rgba(255,255,255,.10);
            backdrop-filter: blur(22px);
            -webkit-backdrop-filter: blur(22px);
            box-shadow: 0 20px 60px rgba(0,0,0,.25);
        }

        h1 {
            margin: 0 0 8px;
            font-size: 30px;
        }

        .subtitle {
            margin: 0 0 28px;
            color: rgba(255,255,255,.7);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: rgba(255,255,255,.8);
        }

        input {
            width: 100%;
            padding: 14px 16px;
            margin-bottom: 18px;
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 12px;
            outline: none;
            color: white;
            background: rgba(255,255,255,.08);
        }

        input::placeholder {
            color: rgba(255,255,255,.4);
        }

        button,
        .google-button {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,.2);
            color: white;
            background: rgba(255,255,255,.10);
            cursor: pointer;
            text-decoration: none;
            display: block;
            text-align: center;
            font-size: 15px;
        }

        button:hover,
        .google-button:hover {
            background: rgba(255,255,255,.18);
        }

        .error {
            margin-bottom: 18px;
            padding: 12px;
            border-radius: 10px;
            background: rgba(255,70,70,.15);
            border: 1px solid rgba(255,100,100,.25);
            color: #ffdede;
            font-size: 14px;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0;
            color: rgba(255,255,255,.45);
            font-size: 13px;
        }

        .divider::before,
        .divider::after {
            content: "";
            height: 1px;
            flex: 1;
            background: rgba(255,255,255,.15);
        }

        .guest-info {
            margin-top: 18px;
            text-align: center;
            font-size: 12px;
            color: rgba(255,255,255,.5);
            line-height: 1.6;
        }
    </style>
</head>

<body>

<div class="login-card">

    <h1>Welcome</h1>
    <p class="subtitle">Login to Zarizar Planespotting</p>

    <?php if ($error): ?>
        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label for="name">Name</label>
        <input
            type="text"
            id="name"
            name="name"
            placeholder="Enter your name"
            required
        >

        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            placeholder="Enter your password"
            required
        >

        <button type="submit">
            Continue
        </button>

    </form>

    <div class="divider">OR</div>

    <a href="#" class="google-button">
        Continue with Google
    </a>

    <div class="guest-info">
        Your login session ends when the browser session is closed.
    </div>

</div>

</body>
</html>
