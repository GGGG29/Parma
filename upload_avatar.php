<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["avatar"])) {
    try {
        // Подключение к БД
        $db = new PDO('mysql:host=MySQL-8.2;dbname=user;charset=utf8', 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Настройки загрузки
        $uploadDir = __DIR__ . '/uploads/avatars/';
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxFileSize = 2 * 1024 * 1024; // 2 МБ

        // Проверка файла
        $file = $_FILES["avatar"];
        if ($file["error"] !== UPLOAD_ERR_OK) {
            die("Ошибка загрузки файла.");
        }

        // Проверка типа и размера
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file["tmp_name"]);
        if (!in_array($mime, $allowedTypes)) {
            die("Допустимы только изображения JPEG, PNG или GIF.");
        }
        if ($file["size"] > $maxFileSize) {
            die("Максимальный размер файла — 2 МБ.");
        }

        // Генерация уникального имени
        $extension = pathinfo($file["name"], PATHINFO_EXTENSION);
        $newFileName = uniqid() . '.' . $extension;
        $destination = $uploadDir . $newFileName;

        // Перемещение файла
        if (!move_uploaded_file($file["tmp_name"], $destination)) {
            die("Не удалось сохранить файл.");
        }

        // Сохранение пути в БД
        $avatarUrl = 'uploads/avatars/' . $newFileName;
        $stmt = $db->prepare("UPDATE users SET avatar_url = ? WHERE id = ?");
        $stmt->execute([$avatarUrl, $_SESSION['user_id']]);

        // Перенаправление с сообщением
        header('Location: cabinet.php?success=avatar_updated');
        exit();

    } catch (PDOException $e) {
        die("Ошибка базы данных: " . $e->getMessage());
    }
}