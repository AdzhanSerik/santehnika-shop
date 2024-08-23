<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../dataBase/db.php';

// Здесь вы можете добавить проверку прав доступа администратора
// Например, проверка роли пользователя, если у вас есть система авторизации

// Получаем общее количество товаров, категорий, заказов и пользователей
$product_count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$category_count = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$order_count = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$user_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель администратора - Магазин сантехники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h1 class="text-center">Панель администратора</h1>

        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Товары</h5>
                        <p class="card-text">Количество товаров: <?= $product_count ?></p>
                        <a href="admin_products.php" class="btn btn-light">Управление товарами</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-secondary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Категории</h5>
                        <p class="card-text">Количество категорий: <?= $category_count ?></p>
                        <a href="admin_categories.php" class="btn btn-light">Управление категориями</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Заказы</h5>
                        <p class="card-text">Количество заказов: <?= $order_count ?></p>
                        <a href="admin_orders.php" class="btn btn-light">Управление заказами</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Пользователи</h5>
                        <p class="card-text">Количество пользователей: <?= $user_count ?></p>
                        <a href="admin_users.php" class="btn btn-light">Управление пользователями</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>