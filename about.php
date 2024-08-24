<?php
session_start();
include './dataBase/db.php';

// Проверяем, авторизован ли пользователь
$is_logged_in = isset($_SESSION['user_id']);
$username = $is_logged_in ? $_SESSION['username'] : '';
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>О нас - Магазин сантехники</title>
    <link rel="icon" href="logo.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body, .navbar-nav .nav-link, .navbar-brand span {
            font-family: "Nunito", sans-serif;
            font-style: normal;
        }
    </style>
</head>

<body>
    <!-- Навигация -->
    <?php include 'header.php'; ?>

    <!-- Основной контент -->
    <div class="container mt-5">
        <h1 class="mb-4">О нас</h1>
        <p>
            Добро пожаловать в наш магазин сантехники! Мы специализируемся на предоставлении качественных
            товаров для вашего дома и офиса. Наш ассортимент включает в себя разнообразные виды сантехники,
            от современных душевых систем до классических смесителей.
        </p>
        <p>
            Мы гордимся тем, что предлагаем своим клиентам только проверенные и надежные продукты от известных
            производителей. Наша команда профессионалов всегда готова помочь вам в выборе необходимых товаров
            и ответить на все ваши вопросы.
        </p>
        <p>
            Мы стремимся обеспечить высокий уровень обслуживания и удовлетворение потребностей наших клиентов.
            Наша цель — стать вашим надежным партнером в сфере сантехники и сделать вашу жизнь более комфортной.
        </p>
        <p>
            Спасибо, что выбрали наш магазин. Мы ценим ваше доверие и готовы предложить вам лучшие товары и
            услуги.
        </p>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
