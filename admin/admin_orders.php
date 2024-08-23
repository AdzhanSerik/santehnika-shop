<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../dataBase/db.php';

// Получаем все заказы
$stmt = $pdo->query("SELECT orders.id, orders.total_amount, orders.status, orders.created_at, users.username FROM orders LEFT JOIN users ON orders.user_id = users.id ORDER BY orders.created_at DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление заказами - Магазин сантехники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h1>Заказы</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID заказа</th>
                    <th>Пользователь</th>
                    <th>Общая сумма</th>
                    <th>Статус</th>
                    <th>Дата создания</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $order['id'] ?></td>
                        <td><?= $order['username'] ? htmlspecialchars($order['username']) : 'Гость' ?></td>
                        <td><?= htmlspecialchars($order['total_amount']) ?> $</td>
                        <td><?= htmlspecialchars($order['status']) ?></td>
                        <td><?= htmlspecialchars($order['created_at']) ?></td>
                        <td>
                            <a href="admin_view_order.php?id=<?= $order['id'] ?>" class="btn btn-info btn-sm">Просмотр</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>