<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Подключение к БД
        $db = new PDO('mysql:host=MySQL-8.2;dbname=user;charset=utf8', 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Получение данных
        $new_username = htmlspecialchars(trim($_POST['new_username']));
        $user_id = $_SESSION['user_id'];

        // Проверка на пустое имя
        if (empty($new_username)) {
            die("Имя не может быть пустым.");
        }

        // Проверка уникальности имени (опционально)
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->execute([$new_username, $user_id]);
        if ($stmt->fetch()) {
            die("Это имя уже занято.");
        }

        // Обновление имени в БД
        $stmt = $db->prepare("UPDATE users SET username = ? WHERE id = ?");
        $stmt->execute([$new_username, $user_id]);

        // Обновление имени в сессии
        $_SESSION['username'] = $new_username;

        // Перенаправление с сообщением об успехе
        header('Location: cabinet.php?success=username_updated');
        exit();

    } catch (PDOException $e) {
        die("Ошибка базы данных: " . $e->getMessage());
    }
}