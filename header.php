<?php
$is_logged_in = isset($_SESSION['user_id']);
$username = $is_logged_in ? $_SESSION['username'] : '';

?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Магазин сантехники</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Главная</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">О нас</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Контакты</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php if ($is_logged_in): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">Корзина</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="account.php">Личный кабинет</a>
                    </li>
                    <li class="nav-item">
                        <span class="nav-link">Привет, <?= htmlspecialchars($username) ?>!</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Выход</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Вход</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Регистрация</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">Корзина</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>