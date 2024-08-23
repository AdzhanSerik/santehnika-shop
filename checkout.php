<?php
session_start();
include './dataBase/db.php'; // Подключаемся к базе данных

// Устанавливаем временную зону для Алматы без учета летнего времени
$timezone = new DateTimeZone('Asia/Atyrau');
$current_time = new DateTime('now', $timezone);
$formatted_time = $current_time->format('Y-m-d H:i:s');

// Проверяем, есть ли товары в корзине
if (empty($_SESSION['cart'])) {
    header('Location: index.php');
    exit();
}

// Проверка авторизации пользователя
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Обработка формы заказа
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $address = htmlspecialchars($_POST['address']);
    $city = htmlspecialchars($_POST['city']); // Получаем значение города
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $payment_method = htmlspecialchars($_POST['payment_method']); // Получаем метод оплаты
    $total = 0;

    // Подсчет общей суммы заказа
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    // Создание нового заказа в базе данных с учетом времени Алматы
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, name, address, city, email, phone, total_amount, status, payment_method, created_at) VALUES (:user_id, :name, :address, :city, :email, :phone, :total, 'processing', :payment_method, :created_at)");
    $stmt->execute([
        'user_id' => $user_id,
        'name' => $name,
        'address' => $address,
        'city' => $city,
        'email' => $email,
        'phone' => $phone,
        'total' => $total,
        'payment_method' => $payment_method,
        'created_at' => $formatted_time // Используем время Алматы
    ]);
    $order_id = $pdo->lastInsertId();

    // Добавление товаров в таблицу order_items с учетом времени Алматы
    foreach ($_SESSION['cart'] as $id => $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, created_at) VALUES (:order_id, :product_id, :quantity, :price, :created_at)");
        $stmt->execute([
            'order_id' => $order_id,
            'product_id' => $id,
            'quantity' => $item['quantity'],
            'price' => $item['price'],
            'created_at' => $formatted_time // Используем время Алматы
        ]);
    }

    // Очищаем корзину после оформления заказа
    unset($_SESSION['cart']);

    // Переход на страницу оплаты или завершения заказа
    if ($payment_method === 'qr') {
        header('Location: payment_qr.php?order_id=' . $order_id);
    } else {
        header('Location: success.php');
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление заказа - Магазин сантехники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h1>Оформление заказа</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Имя</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Адрес доставки</label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            <div class="mb-3">
                <label for="city" class="form-label">Город</label>
                <input type="text" class="form-control" id="city" name="city" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Электронная почта</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Телефон</label>
                <input type="tel" class="form-control" id="phone" name="phone" required>
            </div>
            <div class="mb-3">
                <label for="payment_method" class="form-label">Метод оплаты</label>
                <select class="form-control" id="payment_method" name="payment_method" required>
                    <option value="cash">Наличными при получении</option>
                    <option value="qr">Оплата через QR-код</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Подтвердить заказ</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>