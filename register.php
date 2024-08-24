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
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa; /* Светлый фон для всего сайта */
            font-family: 'Nunito', sans-serif;
        }
        .registration-container {
            max-width: 450px;
            margin: 5% auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .registration-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .registration-header h1 {
            font-size: 2.5rem;
            color: #343a40;
        }
        .btn-custom {
            background-color: #007bff; /* Синий цвет для кнопки */
            border-color: #007bff;
        }
        .btn-custom:hover {
            background-color: #0056b3; /* Темный синий для наведения */
            border-color: #004085;
        }
        .btn-custom:focus, .btn-custom.focus {
            box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.5); /* Цвет тени при фокусе */
        }
        .alert-custom {
            border-radius: 5px;
            background-color: #f8d7da; /* Светло-красный фон для ошибки */
            color: #721c24; /* Темно-красный текст */
            padding: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 1rem;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="registration-container">
        <div class="registration-header">
            <h1>Регистрация</h1>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-custom"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Имя пользователя</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Подтвердите пароль</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-custom w-100" style='background-color:#007bff; color: white'>Зарегистрироваться</button>
        </form>

        <p class="mt-3 text-center">Уже есть аккаунт? <a href="login.php">Войти</a></p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
