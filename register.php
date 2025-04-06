<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $db = new PDO(dsn: 'mysql:host=MySQL-8.2;dbname=user;charset=utf8', username: 'root', password: '');
        $db->setAttribute(attribute: PDO::ATTR_ERRMODE, value: PDO::ERRMODE_EXCEPTION); // Включаем вывод ошибок PDO

        $username = htmlspecialchars(string: $_POST['username']);
        $email = htmlspecialchars(string: $_POST['email']);
        $password = password_hash(password: $_POST['password'], algo: PASSWORD_DEFAULT);
        $experience = (int) $_POST['experience'];


        // Проверка уникальности email
        $stmt = $db->prepare(query: "SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            die("Email уже зарегистрирован. <a href='/register.html'>Попробовать снова</a>");
        }

        // Сохранение пользователя
        $stmt = $db->prepare(query: "INSERT INTO users (username, email, password, experience ) VALUES ( ?, ?, ?, ?)");
        $stmt->execute([$username, $email, $password, $experience]);
        $user_id = $db->lastInsertId();

        // Авторизация
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;

        // Перенаправление
        header(header: 'Location: cabinet.php');
        exit();
    } catch (PDOException $e) {
        die("Ошибка базы данных: " . $e->getMessage()); // Вывод ошибки PDO
    }
}
?>