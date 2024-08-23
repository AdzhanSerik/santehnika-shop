<?php
session_start();
include './dataBase/db.php';

// Ваш API-ключ от Exchangerate API
$api_key = 'ec8ab46cf58b7b9696710f7d'; // Замените 'YOUR_API_KEY' на ваш реальный API-ключ

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
$exchange_rate = $response_data['conversion_rates']['KZT'] + 3.68; // Если API не сработал, используем запасной курс

// Получаем список всех категорий
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// Инициализация переменных для поиска и фильтрации
$search_query = '';
$category_filter = '';

// Проверка на наличие поискового запроса
if (isset($_GET['search'])) {
    $search_query = htmlspecialchars($_GET['search']);
}

// Проверка на наличие фильтра по категории
if (isset($_GET['category_id'])) {
    $category_filter = intval($_GET['category_id']);
}

// Создаем базовый SQL-запрос для получения товаров
$query = "SELECT * FROM products WHERE 1=1";

// Добавляем условия поиска
if ($search_query !== '') {
    $query .= " AND (name LIKE :search_query OR description LIKE :search_query)";
}

// Добавляем фильтр по категории
if ($category_filter !== '') {
    $query .= " AND category_id = :category_id";
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
    <title>Магазин сантехники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <ul class="list-group">
                    <li class="list-group-item"><a href="index.php">Все товары</a></li>
                    <?php foreach ($categories as $category): ?>
                        <li class="list-group-item">
                            <a href="index.php?category_id=<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
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
                            // Конвертируем цену в тенге
                            $price_in_kzt = $product['price'] * $exchange_rate;
                            ?>
                            <div class="col-md-4">
                                <div class="card mb-4">
                                    <img src="images/<?= htmlspecialchars($product['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                        <p class="card-text"><?= htmlspecialchars($product['description']) ?></p>
                                        <p class="card-text"><strong><?= htmlspecialchars($product['price']) ?> $ / <?= number_format($price_in_kzt, 2, ',', ' ') ?> ₸</strong></p>
                                        <a href="product.php?id=<?= $product['id'] ?>" class="btn btn-primary">Посмотреть</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Товары не найдены.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>