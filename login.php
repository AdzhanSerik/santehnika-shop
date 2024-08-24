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
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-size: cover;
            font-family: 'Nunito', sans-serif;
        }
        .login-container {
            max-width: 400px;
            margin: 5% auto;
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .login-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .login-header h1 {
            font-size: 2rem;
            color: #333;
        }
        .btn-custom {
            background-color: #007bff;
            color: white;
            border: none;
        }
        .btn-custom:hover {
            background-color: #0056b3;
        }
        .alert-custom {
            border-radius: 5px;
    background-color: #f8d7da; /* Светло-красный фон для ошибки */
    color: #721c24; /* Темно-красный текст */
    padding: 10px;
    margin-bottom: 20px;
    text-align: center;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="login-container">
        <div class="login-header">
            <h1>Вход</h1>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert-custom"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-custom w-100" style='background-color:#007bff; color: white'>Войти</button>
        </form>

        <p class="mt-3 text-center">Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
