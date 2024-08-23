<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../dataBase/db.php';

// Обработка удаления категории
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $category_id = intval($_GET['id']);
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
    $stmt->execute(['id' => $category_id]);

    // Перенаправляем обратно на страницу управления категориями
    header('Location: admin_categories.php');
    exit();
}

// Получаем список всех категорий
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
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
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>