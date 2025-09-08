<?php
require_once 'config/config.php';
require_once 'includes/MenuItem.php';

$menuItem = new MenuItem();
$menuItems = $menuItem->getAllMenuItems();
$categories = $menuItem->getAllCategories();

// Handle search
$search = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = sanitizeInput($_GET['search']);
    $menuItems = $menuItem->searchMenuItems($search);
}

// Handle category filter
$category = '';
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category = sanitizeInput($_GET['category']);
    $menuItems = $menuItem->getMenuItemsByCategory($category);
}

// Handle sorting
$sort = '';
if (isset($_GET['sort']) && !empty($_GET['sort'])) {
    $sort = sanitizeInput($_GET['sort']);
    if ($sort === 'price_low' || $sort === 'price_high') {
        $order = $sort === 'price_low' ? 'asc' : 'desc';
        $menuItems = $menuItem->sortMenuItemsByPrice($order);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Food Delivery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .menu-item-card {
            transition: transform 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }
        .menu-item-card:hover {
            transform: translateY(-3px);
        }
        .search-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
        }
        .filter-section {
            background: #f8f9fa;
            padding: 2rem 0;
            border-bottom: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/partials/flash.php'; ?>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand text-primary" href="index.php">
                <i class="fas fa-utensils me-2"></i>Food Delivery
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="restaurants.php">Restaurants</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="menu.php">Menu</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['full_name']); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="user/profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="user/orders.php"><i class="fas fa-shopping-bag me-2"></i>My Orders</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="user/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="cart.php">
                                <i class="fas fa-shopping-cart me-1"></i>Cart
                                <span class="badge bg-primary" id="cart-count"><?php echo getCartCount(); ?></span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="user/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="user/register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Search Section -->
    <section class="search-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h2 class="text-center mb-4">Browse Our Menu</h2>
                    <form method="GET" action="menu.php">
                        <div class="input-group input-group-lg">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Search menu items..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-light" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Filter Section -->
    <section class="filter-section">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <form method="GET" action="menu.php" class="d-flex gap-2">
                        <select name="category" class="form-select" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>" 
                                        <?php echo $category === $cat ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    </form>
                </div>
                <div class="col-md-6">
                    <form method="GET" action="menu.php" class="d-flex gap-2">
                        <select name="sort" class="form-select" onchange="this.form.submit()">
                            <option value="">Sort by</option>
                            <option value="price_low" <?php echo $sort === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price_high" <?php echo $sort === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                        </select>
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Menu Items Section -->
    <section class="py-5">
        <div class="container">
            <?php if (!empty($search) || !empty($category) || !empty($sort)): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <h4>Filtered Results</h4>
                        <p class="text-muted">
                            <?php echo count($menuItems); ?> item(s) found
                            <?php if (!empty($search)): ?>
                                for "<?php echo htmlspecialchars($search); ?>"
                            <?php endif; ?>
                            <?php if (!empty($category)): ?>
                                in <?php echo htmlspecialchars($category); ?>
                            <?php endif; ?>
                        </p>
                        <a href="menu.php" class="btn btn-outline-primary btn-sm">Clear Filters</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (empty($menuItems)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-utensils" style="font-size: 4rem; color: #ccc;"></i>
                    <h4 class="mt-3">No menu items found</h4>
                    <p class="text-muted">Try adjusting your search criteria</p>
                    <a href="menu.php" class="btn btn-primary">View All Menu Items</a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($menuItems as $item): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card menu-item-card h-100">
                            <img src="images/menu/<?php echo $item['image'] ?: 'default.jpg'; ?>" 
                                 class="card-img-top" alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                 style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($item['description']); ?></p>
                                
                                <div class="menu-item-info mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-store text-primary me-2"></i>
                                        <small class="text-muted"><?php echo htmlspecialchars($item['restaurant_name']); ?></small>
                                    </div>
                                    <?php if ($item['category']): ?>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-tag text-primary me-2"></i>
                                        <small class="text-muted"><?php echo htmlspecialchars($item['category']); ?></small>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <span class="h5 text-primary mb-0">$<?php echo number_format($item['price'], 2); ?></span>
                                    <form method="POST" action="actions/cart_add.php" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                        <input type="hidden" name="name" value="<?php echo htmlspecialchars($item['name']); ?>">
                                        <input type="hidden" name="price" value="<?php echo $item['price']; ?>">
                                        <input type="hidden" name="restaurant_id" value="<?php echo $item['restaurant_id']; ?>">
                                        <input type="hidden" name="restaurant_name" value="<?php echo htmlspecialchars($item['restaurant_name']); ?>">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Add to Cart
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-utensils me-2"></i>Food Delivery</h5>
                    <p class="mb-0">Delivering happiness, one meal at a time.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; 2024 Food Delivery. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
