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
    <title><?= htmlspecialchars($product['name']) ?> - Опто Маркет</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: "Nunito", sans-serif;
            background-color: #f5f5f5;
        }
        .carousel-item img {
            width: 100%;
            height: 500px;
            object-fit: cover;
            border-radius: 8px;
        }
        .product-details {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .product-details h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: #333;
        }
        .product-details p {
            margin-bottom: 1rem;
            color: #666;
            overflow: visible; /* Позволяет отображать весь текст */
            white-space: normal; /* Позволяет тексту переноситься */
            word-break: break-word; /* Прерывание длинных слов */
        }
        .product-details p strong {
            color: #333;
        }
        .btn-primary {
            background-color: #0056b3;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 1rem;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #004494;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 1rem;
            transition: background-color 0.3s;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .icon {
            font-size: 1rem; /* Уменьшение размера иконок */
            margin-right: 5px;
        }
        .btn-icon {
            display: flex;
            align-items: center;
        }
        .btn-icon .icon {
            font-size: 1.2rem; /* Размер иконки в кнопках */
        }
        .form-control {
            border-radius: 5px;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
        }
        .mt-5-footer {
            margin-top: 60px; /* Добавление отступа между контентом и футером */
        }
        @media (max-width: 767.98px) {
            .carousel-item img {
                height: 300px;
            }
            .product-details {
                padding: 15px;
            }
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <div id="productCarousel" class="carousel slide">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        </div>
                        <?php foreach ($additional_images as $image): ?>
                            <div class="carousel-item">
                                <img src="images/<?= htmlspecialchars($image['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
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
                    <h1><?= htmlspecialchars($product['name']) ?></h1>
                    <p><?= htmlspecialchars($product['description']) ?></p>
                    <p><strong>Цена: <?= htmlspecialchars($product['price']) ?> $</strong></p>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST" class="mt-4">
                        <div class="mb-3">
                            <label for="quantity" class="form-label"><i class="icon fas fa-cube"></i> Количество</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-icon">
                            <i class="icon fas fa-cart-plus"></i> Добавить в корзину
                        </button>
                    </form>

                    <a href="index.php" class="btn btn-secondary btn-icon mt-3">
                        <i class="icon fas fa-arrow-left"></i> Вернуться к покупкам
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-5-footer">
        <?php include 'footer.php'; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
