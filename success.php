<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказ оформлен - Магазин сантехники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5 text-center">
        <h1>Спасибо за ваш заказ!</h1>
        <p>Ваш заказ был успешно оформлен. Мы свяжемся с вами для подтверждения и отправки.</p>
        <a href="index.php" class="btn btn-primary mt-3">Вернуться на главную</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>