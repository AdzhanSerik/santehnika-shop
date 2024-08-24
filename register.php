<?php
session_start();
include './dataBase/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Проверка на совпадение паролей
    if ($password !== $confirm_password) {
        $error = "Пароли не совпадают.";
    } else {
        // Проверка на уникальность email
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Пользователь с таким email уже зарегистрирован.";
        } else {
            // Хеширование пароля
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Вставка нового пользователя в базу данных
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role, created_at) VALUES (:username, :email, :password_hash, 'customer', NOW())");
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password_hash' => $password_hash
            ]);

            // Перенаправление на страницу авторизации
            header('Location: login.php');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - Опто Маркет</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            padding-bottom: 80px; /* Оставляем место для футера */
        }
        .container-register {
            max-width: 400px;
            margin: 50px auto;
        }
        .form-control {
            border-radius: 50px;
            padding-left: 45px;
        }
        .form-label {
            font-weight: bold;
        }
        .form-group {
            position: relative;
        }
        .input-group-text {
            border-radius: 50px;
        }
        .form-group .icon {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: #6c757d;
        }
        .btn {
            border-radius: 50px;
        }
        .alert {
            margin-top: 20px;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container-register">
        <h1 class="text-center mb-4">Регистрация</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group mb-3">
                <label for="username" class="form-label">Имя пользователя</label>
                <div class="input-group">
                    <span class="input-group-text icon"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Введите ваше имя" required>
                </div>
            </div>
            <div class="form-group mb-3">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text icon"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Введите ваш email" required>
                </div>
            </div>
            <div class="form-group mb-3">
                <label for="password" class="form-label">Пароль</label>
                <div class="input-group">
                    <span class="input-group-text icon"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Введите ваш пароль" required>
                </div>
            </div>
            <div class="form-group mb-4">
                <label for="confirm_password" class="form-label">Подтвердите пароль</label>
                <div class="input-group">
                    <span class="input-group-text icon"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Повторите ваш пароль" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Зарегистрироваться</button>
        </form>

        <p class="text-center mt-3">Уже есть аккаунт? <a href="login.php">Войти</a></p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
