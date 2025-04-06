<?php
include_once('connection.php');

// Получаем всех пользователей с открытыми паролями
$stmt = $conn->query("SELECT id, password FROM admins");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $user) {
    // Хешируем пароль
    $hash = password_hash($user['password'], PASSWORD_DEFAULT);

    // Обновляем запись
    $update = $conn->prepare("UPDATE admins SET password_hash = ? WHERE id = ?");
    $update->execute([$hash, $user['id']]);
}

echo "Миграция завершена! Удалите этот файл после выполнения.";
?>