<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказ оформлен - Опто Маркет</title>
    <link rel="icon" href="/logo.jpg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa; /* Светлый фон для страницы */
            font-family: Arial, sans-serif;
        }
        .confirmation-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 5% auto;
        }
        .confirmation-header {
            margin-bottom: 20px;
        }
        .confirmation-header i {
            font-size: 4rem;
            color: #28a745; /* Зеленый цвет для иконки успеха */
        }
        .confirmation-header h1 {
            font-size: 2rem;
            color: #333;
        }
        .confirmation-message {
            font-size: 1.25rem;
            color: #555;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="confirmation-container text-center">
        <div class="confirmation-header">
            <i class="fas fa-check-circle"></i>
            <h1>Спасибо за ваш заказ!</h1>
        </div>
        <p class="confirmation-message">Ваш заказ был успешно оформлен. Мы свяжемся с вами для подтверждения и отправки.</p>
        <a href="index.php" class="btn btn-primary mt-3">Вернуться на главную</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
