<?php
session_start();
include './dataBase/db.php'; // Подключаемся к базе данных

// Получаем ID товара из URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id === 0) {
    header('Location: index.php');
    exit();
}

// Получаем информацию о товаре из базы данных
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute(['id' => $product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: index.php');
    exit();
}

// Получаем дополнительные изображения товара
$stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = :product_id");
$stmt->execute(['product_id' => $product_id]);
$additional_images = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Обработка добавления товара в корзину
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['quantity'])) {
    $quantity = intval($_POST['quantity']);
    if ($quantity > 0) {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity
            ];
        }

        // Перенаправляем обратно на страницу корзины
        header('Location: cart.php');
        exit();
    } else {
        $error = "Количество товара должно быть больше нуля.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - Магазин сантехники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-image-main {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .product-image-thumbnail {
            max-width: 100px;
            height: auto;
            margin: 10px;
            cursor: pointer;
            border: 2px solid #ddd;
            transition: border-color 0.3s;
        }

        .product-image-thumbnail:hover {
            border-color: #007bff;
        }

        .product-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <img src="images/<?= htmlspecialchars($product['image']) ?>" class="product-image-main img-fluid" id="mainImage" alt="<?= htmlspecialchars($product['name']) ?>">

                <div>
                    <!-- Основное изображение как миниатюра -->
                    <img src="images/<?= htmlspecialchars($product['image']) ?>" class="product-image-thumbnail img-fluid" onclick="document.getElementById('mainImage').src=this.src" alt="<?= htmlspecialchars($product['name']) ?>">

                    <!-- Дополнительные изображения как миниатюры -->
                    <?php foreach ($additional_images as $image): ?>
                        <img src="images/<?= htmlspecialchars($image['image_url']) ?>" class="product-image-thumbnail img-fluid" onclick="document.getElementById('mainImage').src=this.src" alt="<?= htmlspecialchars($product['name']) ?>">
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="product-details">
                    <h1 class="display-4"><?= htmlspecialchars($product['name']) ?></h1>
                    <p class="lead"><?= htmlspecialchars($product['description']) ?></p>
                    <p class="h4"><strong>Цена: <?= htmlspecialchars($product['price']) ?> $</strong></p>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST" class="mt-4">
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Количество</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg">Добавить в корзину</button>
                    </form>

                    <a href="index.php" class="btn btn-secondary btn-lg mt-3">Вернуться к покупкам</a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>