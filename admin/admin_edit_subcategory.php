<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../dataBase/db.php';

// Получаем ID подкатегории из URL
$subcategory_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($subcategory_id === 0) {
    header('Location: admin_subcategories.php');
    exit();
}

// Обработка формы редактирования подкатегории
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $category_id = intval($_POST['category_id']);

    $stmt = $pdo->prepare("UPDATE subcategories SET name = :name, category_id = :category_id WHERE id = :id");
    $stmt->execute([
        'name' => $name,
        'category_id' => $category_id,
        'id' => $subcategory_id
    ]);

    $_SESSION['success'] = "Подкатегория успешно обновлена.";
    header('Location: admin_subcategories.php');
    exit();
}

// Получаем информацию о редактируемой подкатегории
$stmt = $pdo->prepare("SELECT * FROM subcategories WHERE id = :id");
$stmt->execute(['id' => $subcategory_id]);
$subcategory = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$subcategory) {
    header('Location: admin_subcategories.php');
    exit();
}

// Получаем список всех категорий для выпадающего списка
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование подкатегории - Магазин сантехники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h1>Редактирование подкатегории</h1>

        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Название подкатегории</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($subcategory['name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Категория</label>
                <select class="form-control" id="category_id" name="category_id" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= $category['id'] == $subcategory['category_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            <a href="admin_subcategories.php" class="btn btn-secondary">Отмена</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>