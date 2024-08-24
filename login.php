<?php
session_start();
include './dataBase/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    // Поиск пользователя в базе данных по email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        // Если пароль совпадает, сохраняем данные пользователя в сессии
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Перенаправляем на главную страницу или страницу администратора в зависимости от роли
        if ($user['role'] == 'admin') {
            header('Location: admin/admin.php');
        } else {
            header('Location: index.php');
        }
        exit();
    } else {
        $error = "Неверный email или пароль.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - Опто Маркет</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            padding-bottom: 80px; /* Оставляем место для футера */
        }
        .container-login {
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
    <div class="container-login">
        <h1 class="text-center mb-4">Вход</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group mb-3">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text icon"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Введите ваш email" required>
                </div>
            </div>
            <div class="form-group mb-4">
                <label for="password" class="form-label">Пароль</label>
                <div class="input-group">
                    <span class="input-group-text icon"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Введите ваш пароль" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Войти</button>
        </form>

        <p class="text-center mt-3">Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
