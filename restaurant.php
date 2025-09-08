<?php
require_once 'config/config.php';
require_once 'includes/Restaurant.php';
require_once 'includes/MenuItem.php';

$restaurant = new Restaurant();
$menuItem = new MenuItem();

// Get restaurant ID from URL
$restaurant_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$restaurant_id) {
    header('Location: restaurants.php');
    exit();
}

// Get restaurant details
$restaurant_details = $restaurant->getRestaurantById($restaurant_id);

if (!$restaurant_details) {
    header('Location: restaurants.php');
    exit();
}

// Get menu items for this restaurant
$menu_items = $menuItem->getMenuItemsByRestaurant($restaurant_id);

// Group menu items by category
$menu_by_category = [];
foreach ($menu_items as $item) {
    $category = $item['category'] ?: 'Other';
    if (!isset($menu_by_category[$category])) {
        $menu_by_category[$category] = [];
    }
    $menu_by_category[$category][] = $item;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($restaurant_details['name']); ?> - Food Delivery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .restaurant-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
        }
        .menu-item-card {
            transition: transform 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }
        .menu-item-card:hover {
            transform: translateY(-3px);
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
        .category-section {
            margin-bottom: 3rem;
        }
        .category-title {
            border-bottom: 3px solid #ff6b6b;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
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
                        <a class="nav-link" href="menu.php">Menu</a>
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

    <!-- Restaurant Header -->
    <section class="restaurant-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-5 fw-bold mb-3"><?php echo htmlspecialchars($restaurant_details['name']); ?></h1>
                    <p class="lead mb-4"><?php echo htmlspecialchars($restaurant_details['description']); ?></p>
                    
                    <div class="restaurant-info">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-map-marker-alt me-3"></i>
                            <span><?php echo htmlspecialchars($restaurant_details['address']); ?></span>
                        </div>
                        <?php if ($restaurant_details['phone']): ?>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-phone me-3"></i>
                            <span><?php echo htmlspecialchars($restaurant_details['phone']); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <img src="images/restaurants/<?php echo $restaurant_details['image'] ?: 'default.jpg'; ?>" 
                         alt="<?php echo htmlspecialchars($restaurant_details['name']); ?>" 
                         class="img-fluid rounded shadow" style="max-height: 300px;">
                </div>
            </div>
        </div>
    </section>

    <!-- Menu Section -->
    <section class="py-5">
        <div class="container">
            <?php if (empty($menu_items)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-utensils" style="font-size: 4rem; color: #ccc;"></i>
                    <h4 class="mt-3">No menu items available</h4>
                    <p class="text-muted">This restaurant hasn't added any menu items yet.</p>
                    <a href="restaurants.php" class="btn btn-primary">Browse Other Restaurants</a>
                </div>
            <?php else: ?>
                <?php foreach ($menu_by_category as $category => $items): ?>
                <div class="category-section">
                    <h3 class="category-title"><?php echo htmlspecialchars($category); ?></h3>
                    <div class="row">
                        <?php foreach ($items as $item): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card menu-item-card h-100">
                                <img src="images/menu/<?php echo $item['image'] ?: 'default.jpg'; ?>" 
                                     class="card-img-top" alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                     style="height: 200px; object-fit: cover;">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                                    <p class="card-text text-muted"><?php echo htmlspecialchars($item['description']); ?></p>
                                    
                                    <div class="mt-auto d-flex justify-content-between align-items-center">
                                        <span class="h5 text-primary mb-0">$<?php echo number_format($item['price'], 2); ?></span>
                                        <form method="POST" action="actions/cart_add.php" class="d-inline">
                                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                            <input type="hidden" name="name" value="<?php echo htmlspecialchars($item['name']); ?>">
                                            <input type="hidden" name="price" value="<?php echo $item['price']; ?>">
                                            <input type="hidden" name="restaurant_id" value="<?php echo $restaurant_details['id']; ?>">
                                            <input type="hidden" name="restaurant_name" value="<?php echo htmlspecialchars($restaurant_details['name']); ?>">
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
                </div>
                <?php endforeach; ?>
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
