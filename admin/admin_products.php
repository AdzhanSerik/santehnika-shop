<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../dataBase/db.php';

// Проверка прав администратора (опционально, если есть система авторизации)

// Обработка удаления товара
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute(['id' => $product_id]);

    // Перенаправляем обратно на страницу управления товарами
    header('Location: admin_products.php');
    exit();
}

// Получаем список всех товаров
$products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление товарами - Магазин сантехники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h1>Управление товарами</h1>
        <a href="admin_add_product.php" class="btn btn-success mb-3">Добавить товар</a>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Описание</th>
                    <th>Цена</th>
                    <th>Категория</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= $product['id'] ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['description']) ?></td>
                        <td><?= htmlspecialchars($product['price']) ?> $</td>
                        <td><?= htmlspecialchars($product['category_id']) ?></td>
                        <td>
                            <a href="admin_edit_product.php?id=<?= $product['id'] ?>" class="btn btn-primary btn-sm">Редактировать</a>
                            <a href="admin_products.php?action=delete&id=<?= $product['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Вы уверены, что хотите удалить этот товар?');">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>