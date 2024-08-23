<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include './dataBase/db.php';

// Получаем ID пользователя из сессии
$user_id = $_SESSION['user_id'];

// Получаем информацию о пользователе
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Получаем историю заказов пользователя
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->execute(['user_id' => $user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Перевод статусов заказа на русский язык
$status_translation = [
    'processing' => 'В обработке',
    'shipped' => 'В процессе отправки',
    'delivered' => 'Доставлено',
    'canceled' => 'Отменено'
];
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет - Магазин сантехники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h1>Личный кабинет</h1>

        <div class="card mb-4">
            <div class="card-header">
                Личная информация
            </div>
            <div class="card-body">
                <p><strong>Имя пользователя:</strong> <?= htmlspecialchars($user['username']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                <p><strong>Дата регистрации:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
                <a href="edit_account.php" class="btn btn-primary">Редактировать профиль</a>
            </div>
        </div>

        <h3>История заказов</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID заказа</th>
                    <th>Общая сумма</th>
                    <th>Статус</th>
                    <th>Дата создания</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['id']) ?></td>
                        <td><?= htmlspecialchars($order['total_amount']) ?> $</td>
                        <td><?= htmlspecialchars($status_translation[$order['status']]) ?></td>
                        <td><?= htmlspecialchars($order['created_at']) ?></td>
                        <td>
                            <a href="view_order.php?id=<?= $order['id'] ?>" class="btn btn-info btn-sm">Просмотр</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>