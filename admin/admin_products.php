<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../dataBase/db.php';

// Инициализация переменных для пагинации и поиска
$search_query = '';
$items_per_page = 10; // Количество товаров на странице
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($current_page - 1) * $items_per_page;

// Проверка на наличие поискового запроса
if (isset($_GET['search'])) {
    $search_query = htmlspecialchars($_GET['search']);
}

// Базовый SQL-запрос
$sql = "
    SELECT products.*, categories.name as category_name, subcategories.name as subcategory_name 
    FROM products 
    LEFT JOIN categories ON products.category_id = categories.id 
    LEFT JOIN subcategories ON products.subcategory_id = subcategories.id
";

// Добавляем условия поиска
if ($search_query !== '') {
    $sql .= " WHERE products.name LIKE :search_query OR products.description LIKE :search_query";
}

// Добавляем сортировку и лимит для пагинации
$sql .= " ORDER BY products.id DESC LIMIT :offset, :items_per_page";

$stmt = $pdo->prepare($sql);
if ($search_query !== '') {
    $stmt->bindValue(':search_query', '%' . $search_query . '%', PDO::PARAM_STR);
}
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':items_per_page', $items_per_page, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем общее количество товаров для пагинации
$count_sql = "SELECT COUNT(*) FROM products";
if ($search_query !== '') {
    $count_sql .= " WHERE name LIKE :search_query OR description LIKE :search_query";
}
$count_stmt = $pdo->prepare($count_sql);
if ($search_query !== '') {
    $count_stmt->bindValue(':search_query', '%' . $search_query . '%', PDO::PARAM_STR);
}
$count_stmt->execute();
$total_products = $count_stmt->fetchColumn();
$total_pages = ceil($total_products / $items_per_page);

// Обработка удаления товара
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute(['id' => $product_id]);

    // Перенаправляем обратно на страницу управления товарами
    header('Location: admin_products.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление товарами - Магазин сантехники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h1>Управление товарами</h1>
        <a href="admin_add_product.php" class="btn btn-success mb-3">Добавить товар</a>

        <!-- Форма поиска -->
        <form method="GET" action="admin_products.php" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Поиск по названию или описанию..." name="search" value="<?= htmlspecialchars($search_query) ?>">
                <button class="btn btn-primary" type="submit">Искать</button>
            </div>
        </form>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Описание</th>
                    <th>Цена</th>
                    <th>Категория</th>
                    <th>Подкатегория</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= $product['id'] ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['description']) ?></td>
                        <td><?= htmlspecialchars($product['price']) ?> $</td>
                        <td><?= htmlspecialchars($product['category_name']) ?></td>
                        <td><?= htmlspecialchars($product['subcategory_name']) ?></td>
                        <td>
                            <a href="admin_edit_product.php?id=<?= $product['id'] ?>" class="btn btn-primary btn-sm">Редактировать</a>
                            <a href="admin_products.php?action=delete&id=<?= $product['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Вы уверены, что хотите удалить этот товар?');">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
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

</html>2