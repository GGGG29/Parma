<?php
include_once('connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Добавляем проверку на существование полей
    if (empty($_POST["username"]) || empty($_POST["password"])) {
        die("Оба поля должны быть заполнены!");
    }

    $username = test_input($_POST["username"]);
    $password = test_input($_POST["password"]);

    // Проверка минимальной длины пароля
    if (strlen($password) < 8) {
        die("Пароль должен содержать минимум 8 символов");
    }

    // Хеширование пароля
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $conn->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
        $stmt->execute([$username, $password_hash]);
        echo "Администратор успешно зарегистрирован!";
    } catch (PDOException $e) {
        // Уточняем сообщение об ошибке
        if ($e->getCode() == 23000) {
            die("Ошибка: Логин уже занят");
        } else {
            die("Ошибка базы данных: " . $e->getMessage());
        }
    }
}

function test_input($data)
{
    if (empty($data)) {
        return null; // Не допускаем пустых значений
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<form method="post">
    <input type="text" name="username" placeholder="Логин" required>
    <input type="password" name="password" placeholder="Пароль (минимум 8 символов)" required>
    <button type="submit">Зарегистрировать</button>
    <p>Если у вас уже есть аккаунт то <a href="login.php">войдите</a></p>
</form>