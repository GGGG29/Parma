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
} catch (PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

// Получение текущих данных пользователя
$user = [];
$errors = [];
$success = '';

try {
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("Пользователь не найден");
    }
} catch (Exception $e) {
    die($e->getMessage());
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF защита
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Недействительный CSRF-токен");
    }

    // Валидация данных
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $experience = (int) $_POST['experience'];
    $post = trim($_POST['post']);
    $avatar_url = $user['avatar_url'];

    // Проверка имени
    if (empty($username)) {
        $errors['username'] = 'Имя обязательно для заполнения';
    }

    // Проверка email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Некорректный email';
    }

    // Обработка аватарки
    if (!empty($_FILES['avatar']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB

        if ($_FILES['avatar']['size'] > $max_size) {
            $errors['avatar'] = 'Максимальный размер файла - 2MB';
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['avatar']['tmp_name']);

        if (!in_array($mime, $allowed_types)) {
            $errors['avatar'] = 'Допустимые форматы: JPEG, PNG, GIF';
        }

        // Сохранение файла
        if (empty($errors)) {
            $upload_dir = 'uploads/avatars/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
            $destination = $upload_dir . $filename;

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destination)) {
                // Удаление старого аватара
                if ($user['avatar_url'] && file_exists($user['avatar_url'])) {
                    unlink($user['avatar_url']);
                }
                $avatar_url = $destination;
            } else {
                $errors['avatar'] = 'Ошибка загрузки файла';
            }
        }
    }

    // Обновление данных
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("
                UPDATE users SET 
                    username = ?, 
                    email = ?, 
                    post = ?, 
                    experience = ?, 
                    avatar_url = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $username,
                $email,
                $post,
                $experience,
                $avatar_url,
                $_SESSION['user_id']
            ]);

            $success = 'Профиль успешно обновлен!';
            // Обновляем данные в сессии
            $_SESSION['username'] = $username;

        } catch (PDOException $e) {
            $errors['database'] = 'Ошибка обновления: ' . $e->getMessage();
        }
    }
}

// Генерация CSRF-токена
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html>

<head>
    <title>Редактирование профиля</title>
    <style>
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .error {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
        }

        .success {
            color: #28a745;
            margin-bottom: 20px;
        }

        .avatar-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }

        .btn {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Редактирование профиля</h1>

        <?php if ($success): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="form-group">
                <label>Аватар:</label>
                <?php if ($user['avatar_url']): ?>
                    <img src="<?= htmlspecialchars($user['avatar_url']) ?>" class="avatar-preview" alt="Текущий аватар">
                <?php endif; ?>
                <input type="file" name="avatar">
                <?php if (isset($errors['avatar'])): ?>
                    <div class="error"><?= $errors['avatar'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Имя пользователя:</label>
                <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>">
                <?php if (isset($errors['username'])): ?>
                    <div class="error"><?= $errors['username'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">
                <?php if (isset($errors['email'])): ?>
                    <div class="error"><?= $errors['email'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Должность:</label>
                <input type="text" name="post" value="<?= htmlspecialchars($user['post']) ?>">
            </div>

            <div class="form-group">
                <label>Стаж (лет):</label>
                <input type="number" name="experience" min="0" value="<?= htmlspecialchars($user['experience']) ?>">
            </div>

            <button type="submit" class="btn">Сохранить изменения</button>

            <?php if (isset($errors['database'])): ?>
                <div class="error"><?= $errors['database'] ?></div>
            <?php endif; ?>
        </form>

        <div style="margin-top: 20px;">
            <a href="profile.php">← Вернуться в профиль</a>
        </div>
    </div>
</body>

</html>