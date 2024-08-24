<?php
session_start();
include './dataBase/db.php'; // Подключаемся к базе данных

// Получаем ID товара из URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id === 0) {
    header('Location: index.php');
    exit();
}

// Получаем информацию о товаре, включая его категорию и подкатегорию, из базы данных
$stmt = $pdo->prepare("
    SELECT products.*, categories.name AS category_name, subcategories.name AS subcategory_name 
    FROM products 
    LEFT JOIN categories ON products.category_id = categories.id 
    LEFT JOIN subcategories ON products.subcategory_id = subcategories.id 
    WHERE products.id = :id
");
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
    <title><?= htmlspecialchars($product['name']) ?> - Опто Маркет</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .carousel-inner img {
            width: 100%;
            height: auto;
        }

        .product-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .product-description {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .quantity-icon {
            font-size: 1.5rem;
            vertical-align: middle;
        }

        .btn-icon {
            font-size: 1.5rem;
            vertical-align: middle;
        }
        
        .btn-primary {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        
        .btn-primary:hover {
            background-color: #004494;
            border-color: #003d7a;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <!-- Карусель изображений -->
                <div id="productCarousel" class="carousel slide">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="images/<?= htmlspecialchars($product['image']) ?>" class="d-block w-100" alt="<?= htmlspecialchars($product['name']) ?>">
                        </div>
                        <?php foreach ($additional_images as $image): ?>
                            <div class="carousel-item">
                                <img src="images/<?= htmlspecialchars($image['image_url']) ?>" class="d-block w-100" alt="<?= htmlspecialchars($product['name']) ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Предыдущий</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Следующий</span>
                    </button>
                </div>
            </div>
            <div class="col-md-6">
                <div class="product-details">
                    <h1 class="display-4"><?= htmlspecialchars($product['name']) ?></h1>
                    <p class="lead product-description"><?= htmlspecialchars($product['description']) ?></p>
                    <p class="h4"><strong>Цена: <?= htmlspecialchars($product['price']) ?> $</strong></p>
                    <p class="h5"><strong>Категория:</strong> <?= htmlspecialchars($product['category_name']) ?></p>
                    <p class="h5"><strong>Подкатегория:</strong> <?= htmlspecialchars($product['subcategory_name']) ?></p>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST" class="mt-4">
                        <div class="mb-3">
                            <label for="quantity" class="form-label">
                                <i class="bi bi-box"></i> Количество
                            </label>
                            <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-cart-plus"></i> Добавить в корзину
                        </button>
                    </form>

                    <a href="index.php" class="btn btn-secondary btn-lg mt-3">
                        <i class="bi bi-arrow-left"></i> Вернуться к покупкам
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div>
    <?php include 'footer.php'; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>