<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../dataBase/db.php';

// Обработка формы добавления товара
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $description = htmlspecialchars($_POST['description']);
    $price = floatval($_POST['price']);
    $stock_quantity = intval($_POST['stock_quantity']);
    $category_id = intval($_POST['category_id']);

    // Обработка основного изображения
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_path = '../images/' . $image;

    if (move_uploaded_file($image_tmp, $image_path)) {
        // Вставляем данные о товаре в базу данных с основным изображением
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock_quantity, category_id, image, created_at) VALUES (:name, :description, :price, :stock_quantity, :category_id, :image, NOW())");
        $stmt->execute([
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'stock_quantity' => $stock_quantity,
            'category_id' => $category_id,
            'image' => $image
        ]);

        $product_id = $pdo->lastInsertId();

        // Обработка загрузки дополнительных изображений
        $images = $_FILES['images'];
        $total_images = count($images['name']);

        for ($i = 0; $i < $total_images; $i++) {
            if ($images['error'][$i] == 0) {
                $image_name = $images['name'][$i];
                $image_tmp_name = $images['tmp_name'][$i];
                $image_path = '../images/' . $image_name;

                if (move_uploaded_file($image_tmp_name, $image_path)) {
                    // Вставляем информацию об изображении в таблицу product_images
                    $stmt = $pdo->prepare("INSERT INTO product_images (product_id, image_url) VALUES (:product_id, :image_url)");
                    $stmt->execute([
                        'product_id' => $product_id,
                        'image_url' => $image_name
                    ]);
                }
            }
        }

        // Перенаправляем на страницу управления товарами
        header('Location: admin_products.php');
        exit();
    } else {
        $error = "Ошибка загрузки изображения.";
    }
}

// Получаем список категорий для выбора
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавление товара - Магазин сантехники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h1>Добавление нового товара</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Название товара</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Описание товара</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Цена</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="stock_quantity" class="form-label">Количество на складе</label>
                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" required>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Категория</label>
                <select class="form-control" id="category_id" name="category_id" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Основное изображение товара</label>
                <input type="file" class="form-control" id="image" name="image" required>
            </div>
            <div class="mb-3">
                <label for="images" class="form-label">Дополнительные изображения товара</label>
                <input type="file" class="form-control" id="images" name="images[]" multiple>
            </div>
            <button type="submit" class="btn btn-success">Добавить товар</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>