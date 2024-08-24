<?php
session_start();
include './dataBase/db.php';

// Ваш API-ключ от Exchangerate API
$api_key = 'ec8ab46cf58b7b9696710f7d'; // Замените на ваш реальный API-ключ

// URL для получения курса валют
$api_url = "https://v6.exchangerate-api.com/v6/{$api_key}/latest/USD";

// Инициализация cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);

// Парсим ответ и получаем курс USD к KZT
$response_data = json_decode($response, true);
$exchange_rate = $response_data['conversion_rates']['KZT'] + 3; // Если API не сработал, используем запасной курс

// Получаем список всех категорий
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// Инициализация переменных для поиска и фильтрации
$search_query = '';
$category_filter = '';
$subcategory_filter = '';

// Проверка на наличие поискового запроса
if (isset($_GET['search'])) {
    $search_query = htmlspecialchars($_GET['search']);
}

// Проверка на наличие фильтра по категории
if (isset($_GET['category_id'])) {
    $category_filter = intval($_GET['category_id']);
}

// Проверка на наличие фильтра по подкатегории
if (isset($_GET['subcategory_id'])) {
    $subcategory_filter = intval($_GET['subcategory_id']);
}

// Создаем базовый SQL-запрос для получения товаров
$query = "
    SELECT products.*, categories.name as category_name, subcategories.name as subcategory_name 
    FROM products 
    LEFT JOIN categories ON products.category_id = categories.id 
    LEFT JOIN subcategories ON products.subcategory_id = subcategories.id 
    WHERE 1=1
";

// Добавляем условия поиска
if ($search_query !== '') {
    $query .= " AND (products.name LIKE :search_query OR products.description LIKE :search_query OR categories.name LIKE :search_query OR subcategories.name LIKE :search_query)";
}

// Добавляем фильтр по категории
if ($category_filter !== '') {
    $query .= " AND products.category_id = :category_id";
}

// Добавляем фильтр по подкатегории
if ($subcategory_filter !== '') {
    $query .= " AND products.subcategory_id = :subcategory_id";
}

// Подготавливаем SQL-запрос
$stmt = $pdo->prepare($query);

// Привязываем параметры к запросу
if ($search_query !== '') {
    $stmt->bindValue(':search_query', '%' . $search_query . '%', PDO::PARAM_STR);
}
if ($category_filter !== '') {
    $stmt->bindValue(':category_id', $category_filter, PDO::PARAM_INT);
}
if ($subcategory_filter !== '') {
    $stmt->bindValue(':subcategory_id', $subcategory_filter, PDO::PARAM_INT);
}

// Выполняем запрос и получаем результат
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Проверяем, авторизован ли пользователь
$is_logged_in = isset($_SESSION['user_id']);
$username = $is_logged_in ? $_SESSION['username'] : '';
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Опто Маркет</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="icon" href="/logo.jpg" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body, h1, h2, h3, h4, h5, h6, p, a, .navbar-custom, .card, .list-group-item {
            font-family: 'Nunito Sans', sans-serif;
        }
        .navbar-custom {
            background-color: #0056b3;
            padding: 1.5rem 1rem;
        }
        .navbar-custom .navbar-brand, 
        .navbar-custom .nav-link {
            color: white;
            font-size: 1.1rem;
        }
        .navbar-custom .nav-link {
            transition: color 0.3s ease, transform 0.3s ease;
        }
        .navbar-custom .nav-link:hover {
            color: #ffc107;
            transform: scale(1.1);
        }
        .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.5);
        }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba%28255, 255, 255, 0.5%29' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
        }
        .card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }
        .card img {
            object-fit: cover;
            height: 200px;
        }
        .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .card-text {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .card-text.description {
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3; /* Количество строк для обрезки */
            -webkit-box-orient: vertical;
        }
        .category-icon {
            margin-right: 10px;
            font-size: 1.2rem;
            color: #0056b3;
        }
        .subcategory-icon {
            margin-right: 10px;
            font-size: 1rem;
            color: #0056b3;
        }
        .list-group-item a {
            text-decoration: none;
            color: #0056b3; /* Цвет текста ссылки, можно заменить на любой другой */
        }

        .list-group-item a:hover {
            color: #003d7a; /* Цвет текста ссылки при наведении, можно заменить на любой другой */
        }
        
        /* Стили для кнопок */
        .btn {
            border-radius: 50px;
        }

        .btn-success {
            background-color: #28a745;
            border: none;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .btn-warning {
            background-color: #ffc107;
            border: none;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        .btn-danger {
            background-color: #dc3545;
            border: none;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }
        /* Стили для цены */
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

    </style>
</head>

<body>
    <!-- Навигация -->
    <?php include 'header.php'; ?>

    <!-- Основной контент -->
    <div class="container mt-5">
        <div class="row">
            <!-- Боковая панель категорий -->
            <div class="col-md-3">
                <h4>Категории</h4>
                <div class="accordion" id="categoryAccordion">
                    <?php foreach ($categories as $category): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?= $category['id'] ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $category['id'] ?>" aria-expanded="false" aria-controls="collapse<?= $category['id'] ?>">
                                    <i class="fas fa-cogs category-icon"></i> <?= htmlspecialchars($category['name']) ?>
                                </button>
                            </h2>
                            <div id="collapse<?= $category['id'] ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $category['id'] ?>" data-bs-parent="#categoryAccordion">
                                <div class="accordion-body">
                                    <ul class="list-group">
                                        <li class="list-group-item"><a href="index.php?category_id=<?= $category['id'] ?>"><i class="fas fa-box"></i> Все товары в категории</a></li>
                                        <?php
                                        $subcategories = $pdo->prepare("SELECT * FROM subcategories WHERE category_id = :category_id");
                                        $subcategories->execute(['category_id' => $category['id']]);
                                        $subcategories = $subcategories->fetchAll(PDO::FETCH_ASSOC);
                                        ?>
                                        <?php if (count($subcategories) > 0): ?>
                                            <?php foreach ($subcategories as $subcategory): ?>
                                                <li class="list-group-item">
                                                    <a href="index.php?subcategory_id=<?= $subcategory['id'] ?>"><i class="fas fa-tag subcategory-icon"></i> <?= htmlspecialchars($subcategory['name']) ?></a>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <li class="list-group-item">Нет подкатегорий</li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Основной контент с товарами -->
            <div class="col-md-9">
                <form method="GET" action="index.php" class="mb-4">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Поиск товаров..." name="search" value="<?= htmlspecialchars($search_query) ?>">
                        <button class="btn btn-primary" type="submit">Искать</button>
                    </div>
                </form>

                <div class="row">
                    <?php if (count($products) > 0): ?>
                        <?php foreach ($products as $product): ?>
                            <?php
                            $price_in_kzt = $product['price'] * $exchange_rate;
                            ?>
                            <div class="col-md-4">
                                <div class="card mb-4">
                                    <img src="images/<?= htmlspecialchars($product['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                        <p class="card-text description"><?= htmlspecialchars($product['description']) ?></p>
                                        <p class="card-text price">
    <strong class="price-usd"><?= htmlspecialchars($product['price']) ?> $</strong> / 
    <strong class="price-kzt"><?= number_format($price_in_kzt, 2, ',', ' ') ?> ₸</strong>
</p>

                                        <p class="card-text"><strong>Категория:</strong> <?= htmlspecialchars($product['category_name']) ?></p>
                                        <p class="card-text"><strong>Подкатегория:</strong> <?= htmlspecialchars($product['subcategory_name']) ?></p>
                                        <a href="product.php?id=<?= $product['id'] ?>" class="btn btn-primary">Посмотреть</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-info" role="alert">
                                Товары не найдены.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div>
    <?php include 'footer.php'; ?>
    </div>

    <!-- Подключение скриптов -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>
