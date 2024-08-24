<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include './dataBase/db.php'; // Подключаемся к базе данных

// Ваш API-ключ от Exchangerate API
$api_key = 'ec8ab46cf58b7b9696710f7d'; // Замените 'YOUR_API_KEY' на ваш реальный API-ключ

// URL для получения курса валют
$api_url = "https://v6.exchangerate-api.com/v6/{$api_key}/latest/USD";

// Инициализация cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);

// Парсим ответ и получаем курс USD к KZT
$response_data = json_decode($response, true);
$exchange_rate = $response_data['conversion_rates']['KZT'] + 3; // Если API не сработал, используем запасной курс

// Инициализация корзины, если она еще не существует
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Обработка действий с корзиной
if (isset($_GET['action']) && $_GET['action'] == 'remove') {
    $product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

// Подсчет общей суммы
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Конвертация общей суммы в тенге
$total_in_kzt = $total * $exchange_rate;
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина - Опто Маркет</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
        }

        .container {
            flex: 1;
        }

        .table-container {
            overflow-x: auto;
        }

        .table {
            margin-bottom: 0;
        }

        .btn {
            border-radius: 50px;
        }

        .table th, .table td {
            text-align: center;
        }

        .table th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        .table td {
            vertical-align: middle;
        }

        .table .btn-danger {
            background-color: #dc3545;
            border: none;
        }

        .table .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-success {
            background-color: #28a745;
            border: none;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .btn-warning {
            background-color: #ffc107;
            border: none;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        .footer {
            text-align: center;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h1 class="mb-4">Корзина</h1>
        <?php if (empty($_SESSION['cart'])): ?>
            <p>Ваша корзина пуста.</p>
            <a href="index.php" class="btn btn-primary">Вернуться к покупкам</a>
        <?php else: ?>
            <div class="table-container">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Товар</th>
                            <th>Цена</th>
                            <th>Количество</th>
                            <th>Итого</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                            <?php
                            // Конвертация суммы для каждого товара в тенге
                            $item_total_in_kzt = $item['price'] * $item['quantity'] * $exchange_rate;
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td><?= htmlspecialchars($item['price']) ?> $ / <?= number_format($item['price'] * $exchange_rate, 2, ',', ' ') ?> ₸</td>
                                <td><?= htmlspecialchars($item['quantity']) ?></td>
                                <td><?= htmlspecialchars($item['price'] * $item['quantity']) ?> $ / <?= number_format($item_total_in_kzt, 2, ',', ' ') ?> ₸</td>
                                <td>
                                    <a href="cart.php?action=remove&id=<?= $id ?>" class="btn btn-danger btn-sm">Удалить</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="mt-4"><strong>Общая сумма: <?= number_format($total, 2, ',', ' ') ?> $ / <?= number_format($total_in_kzt, 2, ',', ' ') ?> ₸</strong></p>
            <a href="checkout.php" class="btn btn-success btn-lg">Оформить заказ</a>
            <a href="index.php" class="btn btn-warning btn-lg">Продолжить покупки</a>
        <?php endif; ?>
    </div>
    <footer class="footer">
        <?php include 'footer.php'; ?>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
