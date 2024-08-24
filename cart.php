<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

include './dataBase/db.php'; // Подключаемся к базе данных

// Инициализация корзины, если она еще не существует
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Подсчет общей суммы
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина - Опто Маркет</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column;
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .container-cart {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
            flex: 1; /* Раздвигает контейнер, чтобы заполнить пространство */
        }

        h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .btn {
            border-radius: 50px;
            font-size: 14px;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
        }

        .table td, .table th {
            vertical-align: middle;
            text-align: center;
        }

        .table th {
            background-color: #f1f1f1;
            font-weight: bold;
            color: #333;
        }

        .table-container {
            overflow-x: auto;
        }

        @media (max-width: 768px) {
            .btn {
                font-size: 12px;
            }

            .container-cart {
                padding: 15px;
                margin-top: 20px;
            }

            h1 {
                font-size: 20px;
            }

            .table th, .table td {
                font-size: 12px;
            }
        }

        @media (max-width: 576px) {
            .btn {
                font-size: 10px;
            }

            .container-cart {
                padding: 10px;
                margin-top: 10px;
            }

            h1 {
                font-size: 18px;
            }

            .table th, .table td {
                font-size: 10px;
                padding: 5px;
            }

            .table {
                font-size: 12px;
            }
        }

        footer {
            background-color: #fff;
            padding: 10px;
            text-align: center;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container-cart">
        <h1>Корзина</h1>
        <?php if (empty($_SESSION['cart'])): ?>
            <p>Ваша корзина пуста.</p>
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-home"></i> Вернуться к покупкам
            </a>
        <?php else: ?>
            <div class="table-container">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Наименование</th>
                            <th>Цена</th>
                            <th>Количество</th>
                            <th>Итого</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td><?= htmlspecialchars($item['price']) ?> $</td>
                                <td><?= htmlspecialchars($item['quantity']) ?></td>
                                <td><?= htmlspecialchars($item['price'] * $item['quantity']) ?> $</td>
                                <td>
                                    <a href="cart.php?action=remove&id=<?= $id ?>" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash-alt"></i> Удалить
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p><strong>Общая сумма: <?= $total ?> $</strong></p>
            <a href="checkout.php" class="btn btn-success">
                <i class="fas fa-credit-card"></i> Оформить заказ
            </a>
            <a href="index.php" class="btn btn-warning">
                <i class="fas fa-shopping-cart"></i> Продолжить покупки
            </a>
        <?php endif; ?>
    </div>
    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
