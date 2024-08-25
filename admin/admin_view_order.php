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

// Получаем информацию о заказе (до обработки формы)
$stmt = $pdo->prepare("SELECT orders.*, users.username FROM orders LEFT JOIN users ON orders.user_id = users.id WHERE orders.id = :order_id");
$stmt->execute(['order_id' => $order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
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
        'updated_at' => $formatted_time,
        'id' => $order_id
    ]);

    // Устанавливаем сообщение об успешном обновлении статуса
    $_SESSION['status_updated'] = "Статус заказа #$order_id был успешно обновлен.";

    $client_email = htmlspecialchars($order['email']); // Используем email клиента

    $status_translation = [
        'processing' => 'В обработке',
        'shipped' => 'Отправлен',
        'delivered' => 'Доставлен',
        'canceled' => 'Отменен'
    ];

    $new_status_rus = isset($status_translation[$new_status]) ? $status_translation[$new_status] : $new_status;

    require_once('../phpmailer/PHPMailerAutoload.php');
    $mail = new PHPMailer;
    $mail->CharSet = 'utf-8';
    $mail->isSMTP();
    $mail->Host = 'smtp.mail.ru';
    $mail->SMTPAuth = true;
    $mail->Username = 'opto.marketkz@mail.ru';
    $mail->Password = 'ym3LGUwevvY4Yq2hGYnQ';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
    $mail->setFrom('opto.marketkz@mail.ru');
    $mail->addAddress($client_email); // Кому будет уходить письмо (email клиента)
    $mail->isHTML(true);
    $mail->Subject = 'Опто-Маркет';
    $mail->Body = "Заказ №" . $order_id . " был обновлен. Новый статус: " . $new_status_rus;
    $mail->AltBody = '';

    if (!$mail->send()) {
        echo 'Error';
    } else {
        echo "Рахмет!";
    }

    // Перенаправляем обратно на страницу просмотра заказа
    header("Location: admin_view_order.php?id=$order_id");
    exit();
}

// Получаем товары из заказа
$stmt = $pdo->prepare("SELECT order_items.*, products.name FROM order_items JOIN products ON order_items.product_id = products.id WHERE order_items.order_id = :order_id");
$stmt->execute(['order_id' => $order_id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5 mb-5">
        <h1>Просмотр заказа #<?= $order['id'] ?></h1>

        <!-- Уведомление об успешном обновлении статуса -->
        <?php if (isset($_SESSION['status_updated'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['status_updated'] ?>
            </div>
            <?php unset($_SESSION['status_updated']); ?>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header">
                Информация о заказе
            </div>
            <div class="card-body">
                <p><strong>Пользователь:</strong> <?= $order['username'] ? htmlspecialchars($order['username']) : 'Гость' ?></p>
                <p><strong>Имя клиента:</strong> <?= htmlspecialchars($order['name']) ?></p>
                <p><strong>Адрес доставки:</strong> <?= htmlspecialchars($order['address']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                <p><strong>Город:</strong> <?= htmlspecialchars($order['city']) ?></p>
                <p><strong>Телефон:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                <p><strong>Общая сумма на момент покупки:</strong> <?= htmlspecialchars($order['total_amount']) ?> $ / <?= htmlspecialchars(number_format($order['total_amount_kzt'], 2, ',', ' ')) ?> ₸</p>
                <p><strong>Стоимость доставки:</strong> <?= htmlspecialchars(number_format($order['delivery_fee_kzt'], 2, ',', ' ')) ?> ₸</p>
                <p><strong>Итоговая сумма:</strong> <?= htmlspecialchars(number_format($order['grand_total_kzt'], 2, ',', ' ')) ?> ₸</p>
                <p><strong>Дата создания:</strong> <?= htmlspecialchars($order['created_at']) ?></p>
                <p><strong>Способ оплаты:</strong> <?= htmlspecialchars($payment_method) ?></p>
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
                        <td><?= htmlspecialchars($item['price']) ?> $ / <?= htmlspecialchars(number_format($item['price_kzt'], 2, ',', ' ')) ?> ₸</td>
                        <td><?= htmlspecialchars($item['quantity'] * $item['price']) ?> $ / <?= htmlspecialchars(number_format($item['quantity'] * $item['price_kzt'], 2, ',', ' ')) ?> ₸</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="admin_orders.php" class="btn btn-secondary mt-4">Вернуться к списку заказов</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>