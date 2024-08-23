<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../dataBase/db.php';

// Получаем ID заказа из URL
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Если ID заказа не указан или заказ не найден, перенаправляем на страницу управления заказами
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id");
$stmt->execute(['id' => $order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: admin_orders.php');
    exit();
}

// Обработка формы изменения статуса заказа
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = htmlspecialchars($_POST['status']);

    // Обновляем статус заказа в базе данных
    $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
    $stmt->execute([
        'status' => $status,
        'id' => $order_id
    ]);

    // Перенаправляем на страницу управления заказами после успешного обновления
    header('Location: admin_orders.php');
    exit();
}

// Возможные статусы заказа
$statuses = ['В обработке', 'Отправлен', 'Доставлен'];
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Изменение статуса заказа - Магазин сантехники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h1>Изменение статуса заказа #<?= $order['id'] ?></h1>
        <p><strong>Текущий статус:</strong> <?= htmlspecialchars($order['status']) ?></p>

        <form method="POST">
            <div class="mb-3">
                <label for="status" class="form-label">Новый статус</label>
                <select class="form-control" id="status" name="status" required>
                    <?php foreach ($statuses as $status): ?>
                        <option value="<?= $status ?>" <?= $status == $order['status'] ? 'selected' : '' ?>><?= $status ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
        </form>

        <a href="admin_orders.php" class="btn btn-secondary mt-3">Назад к заказам</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>