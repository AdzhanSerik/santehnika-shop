<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../dataBase/db.php';

// Получаем ID товара из URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Если ID товара не указан, перенаправляем на страницу управления товарами
if ($product_id === 0) {
    header('Location: admin_products.php');
    exit();
}

// Получаем информацию о товаре из базы данных
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute(['id' => $product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: admin_products.php');
    exit();
}

// Обработка формы редактирования товара
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $description = htmlspecialchars($_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $subcategory_id = intval($_POST['subcategory_id']);
    $stock_quantity = intval($_POST['stock_quantity']);

    // Проверяем, было ли загружено новое изображение
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_path = '../images/' . $image;

        // Перемещаем загруженное изображение в папку
        if (move_uploaded_file($image_tmp, $image_path)) {
            // Удаляем старое изображение, если оно есть
            if (!empty($product['image'])) {
                unlink('../images/' . $product['image']);
            }
        } else {
            $error = "Ошибка загрузки изображения.";
        }
    } else {
        $image = $product['image']; // Оставляем старое изображение
    }

    // Обновляем данные товара в базе данных
    if (!isset($error)) {
        $stmt = $pdo->prepare("UPDATE products SET name = :name, description = :description, price = :price, category_id = :category_id, subcategory_id = :subcategory_id, stock_quantity = :stock_quantity, image = :image, updated_at = NOW() WHERE id = :id");
        $stmt->execute([
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'category_id' => $category_id,
            'subcategory_id' => $subcategory_id,
            'stock_quantity' => $stock_quantity,
            'image' => $image,
            'id' => $product_id
        ]);

        // Перенаправляем на страницу управления товарами
        header('Location: admin_products.php');
        exit();
    }
}

// Получаем список категорий для выбора
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// Получаем список подкатегорий для выбранной категории
$subcategories = [];
if ($product['category_id']) {
    $stmt = $pdo->prepare("SELECT * FROM subcategories WHERE category_id = :category_id");
    $stmt->execute(['category_id' => $product['category_id']]);
    $subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование товара - Магазин сантехники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function loadSubcategories(categoryId) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_edit_sub.php?category_id=' + categoryId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById('subcategory_id').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        // Автоматически загружаем подкатегории при загрузке страницы, если выбрана категория
        document.addEventListener("DOMContentLoaded", function() {
            const categorySelect = document.getElementById('category_id');
            if (categorySelect.value) {
                loadSubcategories(categorySelect.value);
            }
        });
    </script>

</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h1>Редактирование товара</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Название товара</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Описание товара</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?= htmlspecialchars($product['description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Цена</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" value="<?= htmlspecialchars($product['price']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Категория</label>
                <select class="form-control" id="category_id" name="category_id" required onchange="loadSubcategories(this.value)">
                    <option value="">Выберите категорию</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= $category['id'] == $product['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="subcategory_id" class="form-label">Подкатегория</label>
                <select class="form-control" id="subcategory_id" name="subcategory_id" required>
                    <option value="">Выберите подкатегорию</option>
                    <?php foreach ($subcategories as $subcategory): ?>
                        <option value="<?= $subcategory['id'] ?>" <?= $subcategory['id'] == $product['subcategory_id'] ? 'selected' : '' ?>><?= htmlspecialchars($subcategory['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="stock_quantity" class="form-label">Количество на складе</label>
                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" value="<?= htmlspecialchars($product['stock_quantity']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Изображение товара</label>
                <input type="file" class="form-control" id="image" name="image">
                <?php if (!empty($product['image'])): ?>
                    <img src="../images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="img-thumbnail mt-2" width="200">
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>