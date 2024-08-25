<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="admin.php">Админка</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="admin_products.php">Товары</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_categories.php">Категории</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_subcategories.php">Подкатегории</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_orders.php">Заказы</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_users.php">Пользователи</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <span class="nav-link">Админ: <?= htmlspecialchars($username) ?></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Выход</a>
                </li>
            </ul>
        </div>
    </div>
</nav>