<?php
session_start();
include './dataBase/db.php';

// Получаем ID товара из URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id === 0) {
    header('Location: index.php');
    exit();
}

// Получаем информацию о товаре из базы данных
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

// Получаем курс валют и вычисляем цену в тенге
$api_key = 'ec8ab46cf58b7b9696710f7d'; // Замените на ваш реальный API-ключ
$api_url = "https://v6.exchangerate-api.com/v6/{$api_key}/latest/USD";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);

$response_data = json_decode($response, true);
$exchange_rate = $response_data['conversion_rates']['KZT'] + 3; // Если API не сработал, используем запасной курс

$price_in_kzt = $product['price'] * $exchange_rate;

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
    <link rel="icon" href="/logo.jpg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .card-text.price {
    font-size: 1.1rem; /* Чуть больший размер шрифта */
    font-weight: 600; /* Полужирный шрифт */
    color: #333; /* Основной цвет текста */
    margin-bottom: 12px; /* Отступ снизу */
}

/* Стили для цены в долларах */
.card-text .price-usd {
    color: #28a745; /* Зеленый цвет для USD */
    margin-right: 5px; /* Отступ справа */
}

/* Стили для цены в тенге */
.card-text .price-kzt {
    color: #007bff; /* Синий цвет для KZT */
}

.carousel-inner img {
    width: 100%;
    height: 600px; /* Размер для больших экранов */
    object-fit: cover;
}

/* Медиазапрос для уменьшения размера изображения на мобильных устройствах */
@media (max-width: 768px) {
    .carousel-inner img {
        height: 300px; /* Меньший размер для мобильных устройств */
    }
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
        /* Основные стили для деталей продукта */
.product-details {
    padding: 20px;
    border: 1px solid #e0e0e0; /* Легкая граница для выделения блока */
    border-radius: 8px; /* Скругленные углы */
    background-color: #fff; /* Белый фон для четкости */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Легкая тень для глубины */
}

/* Стили заголовка продукта */
.product-title {
    font-size: 2.5rem; /* Увеличенный размер шрифта */
    font-weight: bold;
    margin-bottom: 15px; /* Отступ снизу */
    color: #333; /* Темный цвет текста */
}

/* Стили описания продукта */
.product-description {
    font-size: 1.25rem; /* Увеличенный размер шрифта */
    color: #555; /* Более светлый серый для текста */
    margin-bottom: 20px; /* Отступ снизу */
}

/* Стили цены продукта */
.product-price {
    font-size: 1.5rem; /* Увеличенный размер шрифта */
    font-weight: 700; /* Более жирный шрифт для акцента */
    color: #007bff; /* Синий цвет для цены */
    margin-bottom: 20px; /* Отступ снизу */
}

/* Стили для категории и подкатегории */
.product-category,
.product-subcategory {
    font-size: 1.1rem; /* Чуть больший размер шрифта */
    color: #333; /* Темный цвет текста */
    margin-bottom: 10px; /* Отступ снизу */
}

/* Стили для кнопок */
.btn-primary {
    background-color: #28a745; /* Зеленый цвет для кнопки */
    border-color: #28a745;
    transition: background-color 0.3s ease; /* Плавный переход цвета */
}

.btn-primary:hover {
    background-color: #218838; /* Более темный зеленый при наведении */
    border-color: #1e7e34;
}

.btn-secondary {
    background-color: #6c757d; /* Серый цвет для кнопки */
    border-color: #6c757d;
    transition: background-color 0.3s ease; /* Плавный переход цвета */
}

.btn-secondary:hover {
    background-color: #5a6268; /* Более темный серый при наведении */
    border-color: #545b62;
}

/* Стили для инпутов */
.form-control {
    border-radius: 4px; /* Скругленные углы */
    border-color: #ced4da; /* Цвет границы */
    padding: 10px; /* Внутренний отступ */
    font-size: 1rem; /* Размер шрифта */
}

/* Стили для иконок */
.bi {
    margin-right: 5px; /* Отступ справа от иконки */
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
        <h1 class="display-4 product-title"><?= htmlspecialchars($product['name']) ?></h1>
        <p class="lead product-description"><?= htmlspecialchars($product['description']) ?></p>
        <p class="card-text price">
    <strong class="price-usd"><?= htmlspecialchars($product['price']) ?> $</strong> / 
    <strong class="price-kzt"><?= number_format($price_in_kzt, 2, ',', ' ') ?> ₸</strong>
</p>
        <p class="product-category"><strong>Категория:</strong> <?= htmlspecialchars($product['category_name']) ?></p>
        <p class="product-subcategory"><strong>Подкатегория:</strong> <?= htmlspecialchars($product['subcategory_name']) ?></p>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label for="quantity" class="form-label">
                    <i class="bi bi-box"></i> Количество:
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
    </div>
    <div>
    <?php include 'footer.php'; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>