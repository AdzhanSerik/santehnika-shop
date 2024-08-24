<?php
include '../dataBase/db.php';

$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

$subcategories = $pdo->prepare("SELECT * FROM subcategories WHERE category_id = :category_id");
$subcategories->execute(['category_id' => $category_id]);
$subcategories = $subcategories->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($subcategories);
