<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Подключение к базе данных
try {
    $db = new PDO('mysql:host=MySQL-8.2;dbname=user;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Получение данных текущего пользователя
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("Пользователь не найден!");
    }

} catch (PDOException $e) {
    die("Ошибка базы данных: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Parma Technologies Group</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700;900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <title>Мой профиль</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Montserrat", sans-serif;
            background-color: #293240;
            color: #e9ecee;
            line-height: 1.6;
        }

        .profile-card {
            max-width: 600px;
            margin: 50px auto;
            padding: 25px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }

        .info-item {
            margin: 15px 0;
            font-size: 18px;
        }

        .header {
            background: #0f2232;
            color: #e9ecee;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
            border-bottom: 3px solid #ff4346;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .promotions-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .promotions-header h2 {
            font-size: 30px;
            color: #ff4346;
            text-align: left;
            font-weight: 900;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
            }

            .nav {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }
        }

        .logo h1 {
            font-size: 32px;
            font-weight: 900;
            margin: 0;
            line-height: 1;
            display: flex;
            flex-direction: column;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .logo h1 .parma {
            color: #ff0000;
            font-size: 1.5em;
        }

        .logo h1 .tech {
            color: #e9ecee;
            font-size: 0.7em;
            margin-top: -5px;
        }

        .nav {
            display: flex;
            gap: 25px;
        }

        .nav-link {
            color: #e9ecee;
            text-decoration: none;
            font-size: 18px;
            font-weight: 500;
            transition: color 0.3s, transform 0.2s;
        }

        .nav-link:hover {
            color: #ff0000;
            transform: scale(1.1);
        }
    </style>
</head>

<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <h1>
                    <span class="parma">Parma</span><span class="tech">Technologies Group</span>
                </h1>
            </div>
            <nav class="nav">
                <a href="profile.php" class="nav-link">Сотрудник</a>
                <a href="#wishlist" class="nav-link">Список желаний</a>
                <a href="#promotions" class="nav-link">Акции</a>
                <a href="login.html" class="nav-link">Выход</a>

            </nav>
        </header>
        <div class="profile-card">
            <?php if (!empty($user['avatar_url'])): ?>
                <img src="<?= htmlspecialchars($user['avatar_url']) ?>" class="avatar" alt="Аватар">
            <?php endif; ?>

            <div class="info-item">
                <strong>👤 Имя:</strong> <?= htmlspecialchars($user['username']) ?>
            </div>

            <div class="info-item">
                <strong>💼 Должность:</strong> <?= htmlspecialchars($user['post']) ?>
            </div>

            <div class="info-item">
                <strong>📅 Стаж:</strong> <?= $user['experience'] ?> лет
            </div>

            <div class="info-item">
                <strong>📧 Email:</strong> <?= htmlspecialchars($user['email']) ?>
            </div>

            <div class="info-item">
                <strong>💰 Баланс:</strong> <?= $user['balance'] ?> баллов
            </div>

            <a href="edit_profile.php" style="display: inline-block; margin-top: 20px;">
                Редактировать профиль
            </a>
        </div>
    </div>
</body>

</html>