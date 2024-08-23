<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../dataBase/db.php';

// Получаем ID категории из URL
$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Если ID категории не указан или категория не найдена, перенаправляем на страницу управления категориями
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = :id");
$stmt->execute(['id' => $category_id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    header('Location: admin_categories.php');
    exit();
}

// Обработка формы редактирования категории
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $description = htmlspecialchars($_POST['description']);

    // Обновляем данные о категории в базе данных
    $stmt = $pdo->prepare("UPDATE categories SET name = :name, description = :description WHERE id = :id");
    $stmt->execute([
        'name' => $name,
        'description' => $description,
        'id' => $category_id
    ]);

    // Перенаправляем на страницу управления категориями после успешного обновления
    header('Location: admin_categories.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование категории - Магазин сантехники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h1>Редактирование категории</h1>

        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Название категории</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Описание категории</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?= htmlspecialchars($category['description']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>