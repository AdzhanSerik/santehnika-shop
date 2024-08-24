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

// Обработка удаления подкатегории
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $subcategory_id = intval($_GET['id']);

    // Проверяем наличие товаров в подкатегории перед удалением
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE subcategory_id = :subcategory_id");
    $stmt->execute(['subcategory_id' => $subcategory_id]);
    $product_count = $stmt->fetchColumn();

    if ($product_count > 0) {
        // Если есть товары, не удаляем подкатегорию и показываем предупреждение
        $_SESSION['error'] = "Подкатегория не может быть удалена, так как в ней есть товары.";
    } else {
        // Если товаров нет, удаляем подкатегорию
        $stmt = $pdo->prepare("DELETE FROM subcategories WHERE id = :id");
        $stmt->execute(['id' => $subcategory_id]);

        $_SESSION['success'] = "Подкатегория успешно удалена.";
    }

    // Перенаправляем обратно на страницу управления подкатегориями
    header('Location: admin_subcategories.php');
    exit();
}

// Получаем список всех подкатегорий и соответствующих категорий, с учетом поиска
$subcategory_query = "
    SELECT subcategories.*, categories.name as category_name 
    FROM subcategories 
    JOIN categories ON subcategories.category_id = categories.id
";

if ($search_query !== '') {
    $subcategory_query .= " WHERE subcategories.name LIKE :search_query";
}

$stmt = $pdo->prepare($subcategory_query);

if ($search_query !== '') {
    $stmt->bindValue(':search_query', '%' . $search_query . '%', PDO::PARAM_STR);
}

$stmt->execute();
$subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление подкатегориями - Магазин сантехники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h1>Управление подкатегориями</h1>

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

        <form method="GET" action="admin_subcategories.php" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Поиск подкатегорий..." name="search" value="<?= htmlspecialchars($search_query) ?>">
                <button class="btn btn-primary" type="submit">Искать</button>
            </div>
        </form>

        <a href="admin_add_subcategory.php" class="btn btn-success mb-3">Добавить подкатегорию</a>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название подкатегории</th>
                    <th>Категория</th>
                    <th>Описание</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($subcategories) > 0): ?>
                    <?php foreach ($subcategories as $subcategory): ?>
                        <tr>
                            <td><?= $subcategory['id'] ?></td>
                            <td><?= htmlspecialchars($subcategory['name']) ?></td>
                            <td><?= htmlspecialchars($subcategory['category_name']) ?></td>
                            <td><?= htmlspecialchars($subcategory['description']) ?></td>
                            <td>
                                <a href="admin_edit_subcategory.php?id=<?= $subcategory['id'] ?>" class="btn btn-primary btn-sm">Редактировать</a>
                                <a href="admin_subcategories.php?action=delete&id=<?= $subcategory['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Вы уверены, что хотите удалить эту подкатегорию?');">Удалить</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Подкатегории не найдены.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>