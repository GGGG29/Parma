<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Подключение к БД (параметры должны совпадать с register.php)
        $db = new PDO(dsn: 'mysql:host=MySQL-8.2;dbname=user;charset=utf8', username: 'root', password: '');
        $db->setAttribute(attribute: PDO::ATTR_ERRMODE, value: PDO::ERRMODE_EXCEPTION);

        // Получение данных из формы
        $email = htmlspecialchars(string: $_POST['email']);
        $password = $_POST['password'];

        // Поиск пользователя по email
        $stmt = $db->prepare(query: "SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            die("Пользователь с таким email не найден. <a href='/login.html'>Попробовать снова</a>");
        }

        // Проверка пароля
        if (password_verify(password: $password, hash: $user['password'])) {
            // Авторизация
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header(header: 'Location: cabinet.php');
            exit();
        } else {
            die("Неверный пароль. <a href='/login.html'>Попробовать снова</a>");
        }

    } catch (PDOException $e) {
        die("Ошибка базы данных: " . $e->getMessage());
    }
}
?>