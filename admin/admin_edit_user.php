<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../dataBase/db.php';

// Получаем ID пользователя из URL
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Если ID пользователя не указан или пользователь не найден, перенаправляем на страницу управления пользователями
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: admin_users.php');
    exit();
}

// Обработка формы редактирования пользователя
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $role = htmlspecialchars($_POST['role']);

    // Обновляем данные пользователя в базе данных
    $stmt = $pdo->prepare("UPDATE users SET username = :username, email = :email, role = :role WHERE id = :id");
    $stmt->execute([
        'username' => $username,
        'email' => $email,
        'role' => $role,
        'id' => $user_id
    ]);

    // Перенаправляем на страницу управления пользователями после успешного обновления
    header('Location: admin_users.php');
    exit();
}

// Возможные роли пользователя
$roles = ['admin', 'customer'];
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование пользователя - Магазин сантехники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h1>Редактирование пользователя #<?= $user['id'] ?></h1>

        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Имя пользователя</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Роль</label>
                <select class="form-control" id="role" name="role" required>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role ?>" <?= $role == $user['role'] ? 'selected' : '' ?>><?= ucfirst($role) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
        </form>

        <a href="admin_users.php" class="btn btn-secondary mt-3">Назад к пользователям</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>