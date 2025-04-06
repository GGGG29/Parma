<?php
session_start();
include_once('connection.php'); // Убедитесь что путь правильный

// Переносим функцию сюда или в connection.php
function test_input($data)
{
    if (empty($data)) {
        return null;
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Проверяем существование полей
    if (empty($_POST["username"]) || empty($_POST["password"])) {
        $_SESSION['error'] = "Все поля обязательны для заполнения";
        header("Location: login.php");
        exit();
    }

    $username = test_input($_POST["username"]);
    $password = test_input($_POST["password"]);

    // Проверка подключения к БД
    if (!$conn) {
        $_SESSION['error'] = "Ошибка подключения к базе данных";
        header("Location: login.php");
        exit();
    }

    try {
        $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['admin'] = $user;
            header("Location: adminpage.php");
            exit();
        } else {
            $_SESSION['error'] = "Неверные учетные данные";
            header("Location: login.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Ошибка базы данных: " . $e->getMessage();
        header("Location: login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход администратора</title>
</head>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        user-select: none;
    }

    body {
        font-family: Arial, sans-serif;
        max-width: 400px;
        margin: 150px auto;
        background-color: #293240;
    }

    input {
        width: 100%;
        padding: 17px;
        margin: 12px 0;
        border: none;
        outline: none;

    }

    button {
        background: #FF4346;
        color: rgb(255, 255, 255);
        padding: 15px;
        margin: 16px 0;
        border: none;
        width: 100%;
    }

    button:hover {
        background-color: #ff0000;
        box-shadow: -1px 2px 13px 0px rgba(0, 0, 0, 0.45);

    }

    button[type="submit"] {
        font-size: 20px;
        letter-spacing: 5px;
    }

    .content {
        height: 400px;
        text-align: center;
        padding: 40px 25px;
        background: rgba(15, 34, 50, 0.4);
        box-shadow: -1px 4px 28px 0px rgba(0, 0, 0, 1);
        text-align: center;
    }

    h2 {
        font-size: 29px;
        color: #FF0000;
        padding: 10px;
        margin: 15px 0;
    }
</style>

<body>
    <div class="content">
        <h2>Вход Администратора</h2>
        <form method="post">
            <input type="text" name="username" placeholder="Логин" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit">Войти</button>
        </form>
    </div>
</body>

</html>