<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}


include './dataBase/db.php'; // Подключаемся к базе данных



// Инициализация корзины, если она еще не существует
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


// Подсчет общей суммы
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина - Магазин сантехники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h1>Корзина</h1>
        <?php if (empty($_SESSION['cart'])): ?>
            <p>Ваша корзина пуста.</p>
            <a href="index.php" class="btn btn-primary">Вернуться к покупкам</a>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Товар</th>
                        <th>Цена</th>
                        <th>Количество</th>
                        <th>Итого</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= htmlspecialchars($item['price']) ?> $</td>
                            <td><?= htmlspecialchars($item['quantity']) ?></td>
                            <td><?= htmlspecialchars($item['price'] * $item['quantity']) ?> $</td>
                            <td>
                                <a href="cart.php?action=remove&id=<?= $id ?>" class="btn btn-danger btn-sm">Удалить</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p><strong>Общая сумма: <?= $total ?> $</strong></p>
            <a href="checkout.php" class="btn btn-success">Оформить заказ</a>
            <a href="index.php" class="btn btn-warning">Продолжить покупки</a>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>