<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include './dataBase/db.php'; // Подключаемся к базе данных

// Получаем ID заказа из URL
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($order_id === 0) {
    header('Location: account.php');
    exit();
}

// Получаем информацию о заказе
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :order_id AND user_id = :user_id");
$stmt->execute([
    'order_id' => $order_id,
    'user_id' => $_SESSION['user_id']
]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: account.php');
    exit();
}

// Получаем товары из заказа
$stmt = $pdo->prepare("SELECT order_items.*, products.name, products.image, (order_items.price * order_items.quantity) AS total_price, (order_items.price_kzt * order_items.quantity) AS total_price_kzt FROM order_items JOIN products ON order_items.product_id = products.id WHERE order_items.order_id = :order_id");
$stmt->execute(['order_id' => $order_id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Перевод статусов заказа на русский язык
$status_translation = [
    'processing' => 'В обработке',
    'shipped' => 'В процессе отправки',
    'delivered' => 'Доставлено',
    'canceled' => 'Отменено'
];

$order_status = isset($status_translation[$order['status']]) ? $status_translation[$order['status']] : 'Неизвестный статус';

// Перевод методов оплаты на русский язык
$payment_translation = [
    'cash' => 'Наличными при получении',
    'qr' => 'Оплата через QR-код'
];

$payment_method = isset($payment_translation[$order['payment_method']]) ? $payment_translation[$order['payment_method']] : 'Неизвестный метод оплаты';
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Просмотр заказа - Магазин сантехники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-image {
            max-width: 100px;
            height: auto;
        }
    </style>
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
                <p><strong>Имя клиента:</strong> <?= htmlspecialchars($order['name']) ?></p>
                <p><strong>Адрес доставки:</strong> <?= htmlspecialchars($order['address']) ?></p>
                <p><strong>Город:</strong> <?= htmlspecialchars($order['city']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                <p><strong>Телефон:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                <p><strong>Общая сумма на момент покупки:</strong> <?= htmlspecialchars($order['total_amount']) ?> $ / <?= htmlspecialchars(number_format($order['total_amount_kzt'], 2, ',', ' ')) ?> ₸</p>
                <p><strong>Статус:</strong> <?= htmlspecialchars($order_status) ?></p>
                <p><strong>Дата создания:</strong> <?= htmlspecialchars($order['created_at']) ?></p>
                <p><strong>Способ оплаты:</strong> <?= htmlspecialchars($payment_method) ?></p>
            </div>
        </div>

        <h3>Товары в заказе</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Изображение</th>
                    <th>Название товара</th>
                    <th>Количество</th>
                    <th>Цена за единицу</th>
                    <th>Общая стоимость</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td><img src="images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="product-image"></td>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                        <td><?= htmlspecialchars($item['price']) ?> $ / <?= htmlspecialchars(number_format($item['price_kzt'], 2, ',', ' ')) ?> ₸</td>
                        <td><?= htmlspecialchars($item['total_price']) ?> $ / <?= htmlspecialchars(number_format($item['total_price_kzt'], 2, ',', ' ')) ?> ₸</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="account.php" class="btn btn-secondary mt-4">Вернуться в личный кабинет</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>