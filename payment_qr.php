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

        .img-fluid {
            max-width: 100%;
            height: auto;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #007bff;
            color: #fff;
            font-size: 1.5rem;
            border-radius: 10px 10px 0 0;
        }

        .card-body {
            padding: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 50px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
            border-radius: 50px;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <div class="card text-center">
            <div class="card-header">
                Оплата заказа #<?= $order_id ?>
            </div>
            <div class="card-body">
                <h5 class="card-title">Сумма к оплате</h5>
                <p class="card-text"><strong><?= $order['total_amount'] ?> $</strong></p>
                <p class="card-text">Сканируйте QR-код для оплаты:</p>
                <img src="/qr.png" alt="QR-код для оплаты" class="img-fluid mb-4">
                <a href="account.php" class="btn btn-primary btn-lg">Я оплатил</a>
                <a href="index.php" class="btn btn-secondary btn-lg ms-3">Я передумал</a>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
