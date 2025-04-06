<?php

$conn = "";

try {
    $servername = "MySQL-8.2";
    $dbname = "Admin";
    $username = "root";
    $password = "";

    $conn = new PDO(
        "mysql:host=$servername; dbname=Admin",
        $username,
        $password
    );

    $conn->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>