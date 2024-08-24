<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Контакты - Опто Маркет</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .contacts-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 5% auto;
        }
        .contacts-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .contacts-header i {
            font-size: 4rem;
            color: #007bff;
        }
        .contacts-header h1 {
            font-size: 2.5rem;
            color: #333;
        }
        .contact-info {
            font-size: 1.1rem;
            color: #555;
            line-height: 1.6;
        }
        .contact-info p {
            margin-bottom: 20px;
        }
        .contact-info i {
            color: #007bff;
            margin-right: 10px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="contacts-container">
        <div class="contacts-header">
            <i class="fas fa-address-card"></i>
            <h1>Контакты</h1>
        </div>
        <div class="contact-info">
            <p><i class="fas fa-envelope"></i> Электронная почта: <a href="mailto:santehshop2022@gmail.com">santehshop2022@gmail.com</a></p>
            <p><i class="fas fa-map-marker-alt"></i> Адрес: Северное кольцо 25</p>
            <p><i class="fas fa-phone"></i> Телефон: <a href="tel:+77087533430">8 (708) 753 34 30</a></p>
            <a href="index.php" class="btn btn-primary mt-3">Вернуться на главную</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
