<?php
session_start();
include './dataBase/db.php'; // Подключаемся к базе данных

// Получаем ID заказа из URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id === 0) {
    header('Location: index.php');
    exit();
}

// Получаем информацию о заказе
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id");
$stmt->execute(['id' => $order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: index.php');
    exit();
}

// Генерация QR-кода (это пример, нужно использовать библиотеку для генерации QR-кода, например, PHP QR Code)
$qr_code_url = "https://example.com/generate_qr.php?amount=" . $order['total_amount'] . "&order_id=" . $order_id;

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оплата заказа - Опто Маркет</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 600px;
            margin: 0 auto;
        }
        .btn {
            border-radius: 50px;
            font-size: 14px;
            padding: 10px 20px; /* Добавляем отступы для улучшения внешнего вида */
            margin: 10px 0;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .img-fluid {
            max-width: 100%;
            height: auto;
        }
        .header-margin {
            margin-bottom: 20px;
        }
        @media (max-width: 576px) {
            .btn {
                font-size: 16px;
                padding: 12px 24px; /* Увеличиваем отступы на мобильных устройствах */
            }
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5 text-center">
        <h1 class="header-margin">Оплата заказа #<?= $order_id ?></h1>
        <p class="mb-4">Сумма к оплате: <strong><?= $order['total_amount'] ?> $</strong></p>
        <p class="mb-4">Сканируйте QR-код для оплаты:</p>
        <img src="/qr.png" alt="QR-код для оплаты" class="img-fluid mb-4">
        <a href="account.php" class="btn btn-primary">Я оплатил</a>
        <a href="index.php" class="btn btn-secondary">Я передумал</a>
    </div>
    <footer>
        <?php include 'footer.php'; ?>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
