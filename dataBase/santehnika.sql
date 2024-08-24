-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Авг 24 2024 г., 08:55
-- Версия сервера: 8.0.30
-- Версия PHP: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `santehnika`
--

-- --------------------------------------------------------

--
-- Структура таблицы `addresses`
--

CREATE TABLE `addresses` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `address_line_1` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `address_line_2` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `state` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `zip_code` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `country` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `parent_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `parent_id`, `created_at`, `updated_at`) VALUES
(1, 'Бойлер', 'Бойлер кушти зат кой фыв', NULL, '2024-08-23 12:02:07', '2024-08-23 12:33:55'),
(2, 'Краны', 'Краны гой короче', NULL, '2024-08-23 13:39:30', '2024-08-23 13:39:30'),
(3, 'Туалеты', 'Туалеты для людей прикинь', NULL, '2024-08-23 14:05:21', '2024-08-23 14:05:29');

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('processing','shipped','delivered') COLLATE utf8mb4_general_ci DEFAULT 'processing',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `created_at`, `updated_at`, `name`, `address`, `city`, `email`, `phone`, `payment_method`) VALUES
(4, 2, '22.00', 'delivered', '2024-08-23 13:46:36', '2024-08-23 13:54:08', 'Амир', 'Суюнбая', '', 'amir@gmail.com', '87074148201', ''),
(5, 2, '30.00', 'delivered', '2024-08-23 14:07:24', '2024-08-23 14:08:01', 'Тима', 'Абая 140', '', 'adzhan.serik@gmail.com', '87074148201', ''),
(6, 2, '400.00', 'processing', '2024-08-23 14:35:51', '2024-08-23 14:35:51', 'Serikbolsyn Adzhan', 'Oksky sezd', 'Almaty', 'adzhan.serik@gmail.com', '87074148201', ''),
(7, 2, '134.00', 'processing', '2024-08-23 14:48:04', '2024-08-23 14:48:04', 'Serikbolsyn Adzhan', 'Oksky sezd', 'Almaty', 'adzhan.serik@gmail.com', '87074148201', ''),
(8, 2, '32.00', 'processing', '2024-08-23 17:52:51', '2024-08-23 14:52:51', 'Adzhan Serik', 'qwert', 'Алматы', 'adzhan.serik@gmail.com', '87074148201', ''),
(9, 2, '10.00', 'processing', '2024-08-23 17:55:02', '2024-08-23 14:55:02', 'Serikbolsyn Adzhan', 'Oksky sezd', 'Almaty', 'adzhan.serik@gmail.com', '87074148201', ''),
(10, 2, '10.00', 'processing', '2024-08-23 16:56:33', '2024-08-23 14:56:33', 'Serikbolsyn Adzhan', 'Oksky sezd', 'Almaty', 'adzhan.serik@gmail.com', '87074148201', ''),
(11, 2, '12.00', 'processing', '2024-08-23 16:57:07', '2024-08-23 14:57:07', 'Adzhan Serik', 'qwert', 'Алматы', 'adzhan.serik@gmail.com', '87074148201', ''),
(12, 2, '10.00', 'processing', '2024-08-23 16:57:29', '2024-08-23 14:57:29', 'Serikbolsyn Adzhan', 'Oksky sezd', 'Almaty', 'adzhan.serik@gmail.com', '87074148201', ''),
(13, 2, '22.00', 'delivered', '2024-08-23 16:59:17', '2024-08-23 17:05:18', 'Serikbolsyn Adzhan', 'Oksky sezd', 'Almaty', 'adzhan.serik@gmail.com', '87074148201', ''),
(14, 2, '288.00', 'processing', '2024-08-23 17:54:40', '2024-08-23 15:54:40', 'Serikbolsyn Adzhan', 'Oksky sezd', 'Almaty', 'adzhan.serik@gmail.com', '87074148201', 'qr'),
(15, 2, '12.00', 'processing', '2024-08-23 18:26:26', '2024-08-23 16:26:26', 'Serikbolsyn Adzhan', 'Oksky sezd', 'Almaty', 'adzhan.serik@gmail.com', '87074148201', 'cash'),
(16, 2, '24.00', 'processing', '2024-08-23 18:31:57', '2024-08-23 16:31:57', 'Serikbolsyn Adzhan', 'Oksky sezd', 'Almaty', 'adzhan.serik@gmail.com', '87074148201', 'qr'),
(17, 2, '12.00', 'processing', '2024-08-23 18:32:45', '2024-08-23 16:32:45', 'Serikbolsyn Adzhan', 'Oksky sezd', 'Almaty', 'adzhan.serik@gmail.com', '87074148201', 'qr'),
(18, 2, '12.00', 'processing', '2024-08-23 18:35:56', '2024-08-23 16:35:56', 'Serikbolsyn Adzhan', 'Oksky sezd', 'Almaty', 'adzhan.serik@gmail.com', '87074148201', 'qr');

-- --------------------------------------------------------

--
-- Структура таблицы `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `created_at`, `updated_at`) VALUES
(21, 14, 5, 24, '12.00', '2024-08-23 17:54:40', '2024-08-23 15:54:40'),
(22, 15, 5, 1, '12.00', '2024-08-23 18:26:26', '2024-08-23 16:26:26'),
(23, 16, 5, 2, '12.00', '2024-08-23 18:31:57', '2024-08-23 16:31:57'),
(24, 17, 5, 1, '12.00', '2024-08-23 18:32:45', '2024-08-23 16:32:45'),
(25, 18, 5, 1, '12.00', '2024-08-23 18:35:56', '2024-08-23 16:35:56');

-- --------------------------------------------------------

--
-- Структура таблицы `payments`
--

CREATE TABLE `payments` (
  `id` int NOT NULL,
  `order_id` int DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('credit_card','paypal') COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('success','failed') COLLATE utf8mb4_general_ci DEFAULT 'success',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int NOT NULL,
  `category_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `image` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock_quantity`, `category_id`, `created_at`, `updated_at`, `image`) VALUES
(5, 'хай', 'хуй', '12.00', 100, 2, '2024-08-23 15:40:56', '2024-08-23 15:40:56', 'hello.jpg'),
(6, 'ффф', 'ффф', '1.00', 100, 1, '2024-08-23 16:17:21', '2024-08-23 16:17:21', 'w1.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `product_images`
--

CREATE TABLE `product_images` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_url`) VALUES
(1, 5, 'hello.jpg'),
(2, 5, 'w1.jpg'),
(3, 6, 'hello.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `reviews`
--

CREATE TABLE `reviews` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `comment` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','customer') COLLATE utf8mb4_general_ci DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `created_at`, `updated_at`) VALUES
(2, 'Амир', 'amir@gmail.com', '$2y$10$LathiP.lwf8KfcxKVuV1fuNzZXVmE8ppUsBgJbAsifmuiT.v36HRe', 'customer', '2024-08-23 11:58:25', '2024-08-23 11:58:25'),
(3, 'admin', 'admin@admin.com', '$2y$10$VSQ3s8THy61RqPy3/nxydu/pAVv7k.TeML35UsaV4qwtPO04lQMBy', 'admin', '2024-08-23 12:00:36', '2024-08-23 12:01:07');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Индексы таблицы `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Индексы таблицы `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Индексы таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT для таблицы `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT для таблицы `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
