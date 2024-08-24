<?php
include '../dataBase/db.php'; // Подключаемся к базе данных

// Проверяем, передан ли ID категории
if (isset($_GET['category_id'])) {
    $category_id = intval($_GET['category_id']);

    // Получаем подкатегории для данной категории
    $stmt = $pdo->prepare("SELECT * FROM subcategories WHERE category_id = :category_id");
    $stmt->execute(['category_id' => $category_id]);
    $subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Формируем HTML для выпадающего списка подкатегорий
    foreach ($subcategories as $subcategory) {
        echo '<option value="' . $subcategory['id'] . '">' . htmlspecialchars($subcategory['name']) . '</option>';
    }
}
