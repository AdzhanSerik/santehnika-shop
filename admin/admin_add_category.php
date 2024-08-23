<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../dataBase/db.php';



// Обработка формы добавления категории
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $description = htmlspecialchars($_POST['description']);

    // Вставляем данные о категории в базу данных
    $stmt = $pdo->prepare("INSERT INTO categories (name, description, created_at) VALUES (:name, :description, NOW())");
    $stmt->execute([
        'name' => $name,
        'description' => $description
    ]);

    // Перенаправляем на страницу управления категориями
    header('Location: admin_categories.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавление категории - Магазин сантехники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h1>Добавление новой категории</h1>

        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Название категории</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Описание категории</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-success">Добавить категорию</button>
        </form>
        </di