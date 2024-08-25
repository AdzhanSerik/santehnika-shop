<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../dataBase/db.php';

// Инициализация переменных для пагинации и поиска
$search_query = '';
$items_per_page = 10; // Количество заказов на странице
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($current_page - 1) * $items_per_page;

// Проверка на наличие поискового запроса
if (isset($_GET['search'])) {
    $search_query = htmlspecialchars($_GET['search']);
}

// Базовый SQL-запрос
$sql = "SELECT orders.id, orders.total_amount, orders.total_amount_kzt, orders.delivery_fee_kzt, orders.grand_total_kzt, orders.status, orders.created_at, users.username 
        FROM orders 
        LEFT JOIN users ON orders.user_id = users.id";

// Добавляем условия поиска
if ($search_query !== '') {
    $sql .= " WHERE orders.id LIKE :search_query OR users.username LIKE :search_query";
}

// Добавляем сортировку и лимит для пагинации
$sql .= " ORDER BY orders.created_at DESC LIMIT :offset, :items_per_page";

$stmt = $pdo->prepare($sql);
if ($search_query !== '') {
    $stmt->bindValue(':search_query', '%' . $search_query . '%', PDO::PARAM_STR);
}
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':items_per_page', $items_per_page, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем общее количество заказов для пагинации
$count_sql = "SELECT COUNT(*) FROM orders";
if ($search_query !== '') {
    $count_sql .= " LEFT JOIN users ON orders.user_id = users.id WHERE orders.id LIKE :search_query OR users.username LIKE :search_query";
}

$count_stmt = $pdo->prepare($count_sql);
if ($search_query !== '') {
    $count_stmt->bindValue(':search_query', '%' . $search_query . '%', PDO::PARAM_STR);
}
$count_stmt->execute();
$total_orders = $count_stmt->fetchColumn();
$total_pages = ceil($total_orders / $items_per_page);

// Перевод статусов заказа на русский язык
$status_translation = [
    'processing' => 'В обработке',
    'shipped' => 'Отправлено',
    'delivered' => 'Доставлено',
    'canceled' => 'Отменено'
];
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление заказами - Магазин сантехники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h1>Заказы</h1>

        <!-- Форма поиска -->
        <form method="GET" action="admin_orders.php" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Поиск по ID заказа или пользователю..." name="search" value="<?= htmlspecialchars($search_query) ?>">
                <button class="btn btn-primary" type="submit">Искать</button>
            </div>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID заказа</th>
                    <th>Пользователь</th>
                    <th>Общая сумма (USD)</th>
                    <th>Общая сумма (KZT)</th>
                    <th>Стоимость доставки (KZT)</th>
                    <th>Итоговая сумма (KZT)</th>
                    <th>Статус</th>
                    <th>Дата создания</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($orders) > 0): ?>
                    <?php foreach ($orders as $order): ?>
                        <?php
                        $status = isset($status_translation[$order['status']]) ? $status_translation[$order['status']] : $order['status'];
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($order['id']) ?></td>
                            <td><?= $order['username'] ? htmlspecialchars($order['username']) : 'Гость' ?></td>
                            <td><?= htmlspecialchars($order['total_amount']) ?> $</td>
                            <td><?= htmlspecialchars(number_format($order['total_amount_kzt'], 2, ',', ' ')) ?> ₸</td>
                            <td><?= htmlspecialchars(number_format($order['delivery_fee_kzt'], 2, ',', ' ')) ?> ₸</td>
                            <td><?= htmlspecialchars(number_format($order['grand_total_kzt'], 2, ',', ' ')) ?> ₸</td>
                            <td><?= htmlspecialchars($status) ?></td>
                            <td><?= htmlspecialchars($order['created_at']) ?></td>
                            <td>
                                <a href="admin_view_order.php?id=<?= $order['id'] ?>" class="btn btn-info btn-sm">Просмотр</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">Заказы не найдены</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Пагинация -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $current_page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $current_page - 1 ?>&search=<?= htmlspecialchars($search_query) ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $current_page == $i ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&search=<?= htmlspecialchars($search_query) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $current_page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $current_page + 1 ?>&search=<?= htmlspecialchars($search_query) ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>