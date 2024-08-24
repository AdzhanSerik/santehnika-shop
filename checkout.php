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
$username = isset($_SESSION['username']) ? $_SESSION['username'] : ''; // Получаем имя пользователя из сессии
$email = isset($_SESSION['email']) ? $_SESSION['email'] : ''; // Получаем email пользователя из сессии

// Ваш API-ключ от Exchangerate API
$api_key = 'ec8ab46cf58b7b9696710f7d'; // Замените на ваш реальный API-ключ

// URL для получения курса валют
$api_url = "https://v6.exchangerate-api.com/v6/{$api_key}/latest/USD";

// Инициализация cURL для получения курса валют
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);

// Парсим ответ и проверяем наличие курса
$response_data = json_decode($response, true);
if (isset($response_data['conversion_rates']['KZT'])) {
    $exchange_rate = $response_data['conversion_rates']['KZT'] + 3;
} else {
    $exchange_rate = 480; // Запасной курс на случай ошибки API
}

// Обработка формы заказа
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $address = htmlspecialchars($_POST['address']);
    $city = htmlspecialchars($_POST['city']); // Получаем значение города
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $payment_method = htmlspecialchars($_POST['payment_method']); // Получаем метод оплаты
    $total = 0;

    // Подсчет общей суммы заказа в долларах
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    // Подсчет общей суммы заказа в тенге
    $total_in_kzt = $total * $exchange_rate;

    // Создание нового заказа в базе данных с учетом времени Алматы
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, name, address, city, email, phone, total_amount, total_amount_kzt, status, payment_method, created_at) VALUES (:user_id, :name, :address, :city, :email, :phone, :total, :total_kzt, 'processing', :payment_method, :created_at)");
    $stmt->execute([
        'user_id' => $user_id,
        'name' => $name,
        'address' => $address,
        'city' => $city,
        'email' => $email,
        'phone' => $phone,
        'total' => $total,
        'total_kzt' => $total_in_kzt, // Сохранение суммы в тенге
        'payment_method' => $payment_method,
        'created_at' => $formatted_time // Используем время Алматы
    ]);
    $order_id = $pdo->lastInsertId();

    // Добавление товаров в таблицу order_items с учетом времени Алматы
    foreach ($_SESSION['cart'] as $id => $item) {
        $price_kzt = $item['price'] * $exchange_rate; // Вычисляем цену в тенге
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, price_kzt, created_at) VALUES (:order_id, :product_id, :quantity, :price, :price_kzt, :created_at)");
        $stmt->execute([
            'order_id' => $order_id,
            'product_id' => $id,
            'quantity' => $item['quantity'],
            'price' => $item['price'],
            'price_kzt' => $price_kzt, // Сохранение цены в тенге
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
    <link rel="icon" href="/logo.jpg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .container {
            flex: 1;
        }
        .btn {
            border-radius: 50px;
        }
        .form-label {
            font-weight: bold;
        }
        .form-control, .form-select {
            border-radius: 50px;
        }
        .form-control::placeholder, .form-select::placeholder {
            font-style: italic;
        }
        .input-group-text {
            border-radius: 50px 0 0 50px;
        }
        .form-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 10px;
        }
        .input-group {
            position: relative;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h1 class="mb-4">Оформление заказа</h1>
        <form method="POST">
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Имя" required>
                </div>
            </div>
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-home"></i></span>
                    <input type="text" class="form-control" id="address" name="address" placeholder="Адрес доставки" required>
                </div>
            </div>
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-city"></i></span>
                    <input type="text" class="form-control" id="city" name="city" placeholder="Город" required>
                </div>
            </div>
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="Электронная почта" required>
                </div>
            </div>
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="Телефон" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="payment_method" class="form-label">Метод оплаты</label>
                <select class="form-control" id="payment_method" name="payment_method" required>
                    <option value="cash">Наличными при получении (г. Алматы)</option>
                    <option value="qr">Оплата через QR-код</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Подтвердить заказ</button>
        </form>
    </div>
    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
