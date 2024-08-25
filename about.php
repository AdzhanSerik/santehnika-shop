<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>О нас - Опто Маркет</title>
    <link rel="icon" href="/logo.jpg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .about-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 5% auto;
        }

        .about-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .about-header i {
            font-size: 4rem;
            color: #007bff;
        }

        .about-header h1 {
            font-size: 2.5rem;
            color: #333;
        }

        .about-content {
            font-size: 1.1rem;
            color: #555;
            line-height: 1.6;
        }

        .about-content p {
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }

        /* Медиазапросы для мобильных устройств */
        @media (max-width: 576px) {
            .about-container {
                padding: 20px;
                margin: 10% auto;
            }

            .about-header i {
                font-size: 3rem;
            }

            .about-header h1 {
                font-size: 1.8rem;
            }

            .about-content {
                font-size: 1rem;
            }

            .about-content p {
                margin-bottom: 15px;
            }
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <div class="about-container">
            <div class="about-header">
                <i class="fas fa-truck"></i>
                <h1>О компании Опто Маркет</h1>
            </div>
            <div class="about-content">
                <p>Добро пожаловать в «Опто Маркет»! Мы — ваш надежный партнер в области оптовой продажи сантехнического оборудования. Наша компания специализируется на предоставлении высококачественной сантехники и аксессуаров по выгодным оптовым ценам.</p>
                <p>Мы гордимся тем, что предлагаем нашим клиентам не только широкий ассортимент продукции, но и исключительный сервис. Мы стремимся обеспечить быструю и надежную доставку, чтобы вы могли своевременно получить необходимые товары.</p>
                <p>Наша команда профессионалов тщательно отбирает каждый товар, чтобы гарантировать его соответствие высоким стандартам качества. Мы понимаем важность оперативности и надежности в бизнесе, и поэтому делаем все возможное, чтобы обеспечить вам наилучший опыт покупок.</p>
                <p>Благодарим вас за выбор «Опто Маркет». Мы рады стать частью вашего бизнеса и готовы предложить лучшие решения для ваших потребностей в сантехнике. Если у вас возникли вопросы или предложения, не стесняйтесь обращаться к нам.</p>
                <a href="index.php" class="btn btn-primary mt-3">Вернуться на главную</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
