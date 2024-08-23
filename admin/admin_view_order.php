<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../dataBase/db.php';

// Устанавливаем временную зону для Атырау (UTC+5)
$timezone = new DateTimeZone('Asia/Atyrau');

// Получаем ID заказа из URL
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($order_id === 0) {
    header('Location: admin_orders.php');
    exit();
}

// Обработка формы изменения статуса
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_status = htmlspecialchars($_POST['status']);
    $current_time = new DateTime('now', $timezone);
    $formatted_time = $current_time->format('Y-m-d H:i:s');

    $stmt = $pdo->prepare("UPDATE orders SET status = :status, updated_at = :updated_at WHERE id = :id");
    $stmt->execute([
        'status' => $new_status,
        'updated_at' => $formatted_time, // Время обновления статуса с учетом часового пояса
        'id' => $order_id
    ]);

    // Обновляем информацию о заказе после изменения статуса
    header("Location: admin_view_order.php?id=$order_id");
    exit();
}

// Получаем информацию о заказе
$stmt = $pdo->prepare("SELECT orders.*, users.username FROM orders LEFT JOIN users ON orders.user_id = users.id WHERE orders.id = :order_id");
$stmt->execute(['order_id' => $order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: admin_orders.php');
    exit();
}

// Получаем товары из заказа
$stmt = $pdo->prepare("SELECT order_items.*, products.name FROM order_items JOIN products ON order_items.product_id = products.id WHERE order_items.order_id = :order_id");
$stmt->execute(['order_id' => $order_id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Просмотр заказа - Магазин сантехники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h1>Просмотр заказа #<?= $order['id'] ?></h1>
        <div class="card mb-4">
            <div class="card-header">
                Информация о заказе
            </div>
            <div class="card-body">
                <p><strong>Пользователь:</strong> <?= $order['username'] ? htmlspecialchars($order['username']) : 'Гость' ?></p>
                <p><strong>Имя клиента:</strong> <?= htmlspecialchars($order['name']) ?></p>
                <p><strong>Адрес доставки:</strong> <?= htmlspecialchars($order['address']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                <p><strong>Телефон:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                <p><strong>Общая сумма:</strong> <?= htmlspecialchars($order['total_amount']) ?> $</p>
                <p><strong>Дата создания:</strong> <?= htmlspecialchars($order['created_at']) ?></p>
                <form method="POST">
                    <div class="mb-3">
                        <label for="status" class="form-label">Статус заказа</label>
                        <select class="form-control" id="status" name="status">
                            <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>В обработке</option>
                            <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Отправлен</option>
                            <option value="delivered" <?= $order['status'] == 'delivered' ? 'selected' : '' ?>>Доставлен</option>
                            <option value="canceled" <?= $order['status'] == 'canceled' ? 'selected' : '' ?>>Отменен</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Обновить статус</button>
                </form>
            </div>
        </div>

        <h3>Товары в заказе</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Название товара</th>
                    <th>Количество</th>
                    <th>Цена за единицу</th>
                    <th>Общая стоимость</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                        <td><?= htmlspecialchars($item['price']) ?> $</td>
                        <td><?= htmlspecialchars($item['quantity'] * $item['price']) ?> $</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="admin_orders.php" class="btn btn-secondary mt-4">Вернуться к списку заказов</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>