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
    <title>Оформление заказа - Опто Маркет</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .container-checkout {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            margin-bottom: 20px;
            font-size: 28px;
            color: #333;
        }

        .form-label {
            font-weight: 600;
        }

        .btn {
            border-radius: 50px;
            font-size: 16px;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004bb5;
        }

        .form-control {
            border-radius: 8px;
            box-shadow: none;
            border: 1px solid #ced4da;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: none;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            background: #fff;
            border-top: 1px solid #dee2e6;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 24px;
            }

            .btn {
                font-size: 14px;
            }
        }

        @media (max-width: 576px) {
            h1 {
                font-size: 20px;
            }

            .btn {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container-checkout">
        <h1>Оформление заказа</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label"><i class="fas fa-user"></i> Имя</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label"><i class="fas fa-map-marker-alt"></i> Адрес доставки</label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            <div class="mb-3">
                <label for="city" class="form-label"><i class="fas fa-city"></i> Город</label>
                <input type="text" class="form-control" id="city" name="city" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label"><i class="fas fa-envelope"></i> Электронная почта</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label"><i class="fas fa-phone"></i> Телефон</label>
                <input type="tel" class="form-control" id="phone" name="phone" required>
            </div>
            <div class="mb-3">
                <label for="payment_method" class="form-label"><i class="fas fa-credit-card"></i> Метод оплаты</label>
                <select class="form-control" id="payment_method" name="payment_method" required>
                    <option value="cash">Наличными при получении</option>
                    <option value="qr">Оплата через QR-код</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Подтвердить заказ</button>
        </form>
    </div>
    <footer class="footer">
        <?php include 'footer.php'; ?>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
