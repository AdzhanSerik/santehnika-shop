<?php
$host = 'localhost'; // Адрес сервера базы данных
$db = 'santehnika'; // Название базы данных
$user = 'root'; // Имя пользователя базы данных
$pass = ''; // Пароль пользователя базы данных

try {
    // Создаем объект PDO для подключения к базе данных
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    // Устанавливаем режим обработки ошибок в виде исключений
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Выводим сообщение об ошибке в случае неудачи подключения
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}
