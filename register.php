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
    <title>Регистрация - Магазин сантехники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h1>Регистрация</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
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
            <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
        </form>

        <p class="mt-3">Уже есть аккаунт? <a href="login.php">Войти</a></p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>