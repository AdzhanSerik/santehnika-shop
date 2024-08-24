<?php
$is_logged_in = isset($_SESSION['user_id']);
$username = $is_logged_in ? $_SESSION['username'] : '';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Опто Маркет</title>
    <link rel="icon" href="logo.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: "Nunito", sans-serif;
        }
        .navbar {
            background-color: #0056b3; /* Убедитесь, что здесь установлен нужный цвет */
        }
        .navbar-brand span {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .navbar-nav .nav-link {
            color: #ffffff;
            transition: background-color 0.3s ease, color 0.3s ease;
            padding: 10px 15px;
            border-radius: 5px;
        }
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            background-color: #003d80;
            color: #e9ecef;
        }
        .navbar-toggler {
            border: none;
            background-color: #003d80;
            border-radius: 5px;
        }
        .navbar-toggler:focus {
            box-shadow: none;
        }
        .navbar-toggler-icon {
            background-color: #ffffff;
            border-radius: 2px;
        }
        @media (max-width: 767.98px) {
            .navbar-nav {
                text-align: center;
            }
            .navbar-nav .nav-link {
                padding: 0.75rem;
                display: block;
            }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <span>Опто Маркет</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link text-light" href="index.php">Главная</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light" href="about.php">О нас</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light" href="contact.php">Контакты</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php if ($is_logged_in): ?>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="cart.php">
                            <i class="fas fa-shopping-cart"></i> Корзина
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="account.php">
                            <i class="fas fa-user"></i> Личный кабинет
                        </a>
                    </li>
                    <li class="nav-item">
                        <span class="nav-link text-light">
                            Привет, <strong><?= htmlspecialchars($username) ?></strong>!
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Выход
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="login.php">
                            <i class="fas fa-sign-in-alt"></i> Вход
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="register.php">
                            <i class="fas fa-user-plus"></i> Регистрация
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="cart.php">
                            <i class="fas fa-shopping-cart"></i> Корзина
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
