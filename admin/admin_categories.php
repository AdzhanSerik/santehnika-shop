<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../dataBase/db.php';

// Обработка поиска
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = htmlspecialchars($_GET['search']);
}

// Обработка удаления категории
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $category_id = intval($_GET['id']);

    // Проверяем наличие подкатегорий и товаров в категории перед удалением
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM subcategories WHERE category_id = :category_id");
    $stmt->execute(['category_id' => $category_id]);
    $subcategory_count = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = :category_id");
    $stmt->execute(['category_id' => $category_id]);
    $product_count = $stmt->fetchColumn();

    if ($subcategory_count > 0 || $product_count > 0) {
        // Если есть подкатегории или товары, не удаляем категорию и показываем предупреждение
        $_SESSION['error'] = "Категория не может быть удалена, так как в ней есть подкатегории или товары.";
    } else {
        // Если подкатегорий и товаров нет, удаляем категорию
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->execute(['id' => $category_id]);

        $_SESSION['success'] = "Категория успешно удалена.";
    }

    // Перенаправляем обратно на страницу управления категориями
    header('Location: admin_categories.php');
    exit();
}

// Получаем список всех категорий с учетом поиска
$category_query = "SELECT * FROM categories";
if ($search_query !== '') {
    $category_query .= " WHERE name LIKE :search_query";
}

$stmt = $pdo->prepare($category_query);

if ($search_query !== '') {
    $stmt->bindValue(':search_query', '%' . $search_query . '%', PDO::PARAM_STR);
}

$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление категориями - Магазин сантехники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h1>Управление категориями</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error'] ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success'] ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form method="GET" action="admin_categories.php" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Поиск категорий..." name="search" value="<?= htmlspecialchars($search_query) ?>">
                <button class="btn btn-primary" type="submit">Искать</button>
            </div>
        </form>

        <a href="admin_add_category.php" class="btn btn-success mb-3">Добавить категорию</a>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Описание</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($categories) > 0): ?>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?= $category['id'] ?></td>
                            <td><?= htmlspecialchars($category['name']) ?></td>
                            <td><?= htmlspecialchars($category['description']) ?></td>
                            <td>
                                <a href="admin_edit_category.php?id=<?= $category['id'] ?>" class="btn btn-primary btn-sm">Редактировать</a>
                                <a href="admin_categories.php?action=delete&id=<?= $category['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Вы уверены, что хотите удалить эту категорию?');">Удалить</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Категории не найдены.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>