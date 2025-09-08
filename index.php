<?php
require_once 'config/config.php';
require_once 'includes/Restaurant.php';
require_once 'includes/MenuItem.php';

$restaurant = new Restaurant();
$menuItem = new MenuItem();

// Get featured restaurants
$featuredRestaurants = $restaurant->getFeaturedRestaurants();

// Get popular menu items
$popularItems = $menuItem->getPopularItems();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Delivery - Order Delicious Food Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 4rem 0;
        }
        .restaurant-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }
        .restaurant-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
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
        .section-title {
            position: relative;
            margin-bottom: 3rem;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            border-radius: 2px;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/partials/flash.php'; ?>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand text-primary" href="index.php">
                <i class="fas fa-utensils me-2"></i>Hungry Hub
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

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Delicious Food Delivered to Your Door</h1>
                    <p class="lead mb-4">Order from your favorite restaurants and enjoy fresh, hot meals delivered right to your doorstep.</p>
                    <div class="d-flex gap-3">
                        <a href="restaurants.php" class="btn btn-light btn-lg">
                            <i class="fas fa-store me-2"></i>Browse Restaurants
                        </a>
                        <a href="menu.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-utensils me-2"></i>View Menu
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <i class="fas fa-motorcycle" style="font-size: 8rem; opacity: 0.8;"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Restaurants -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center section-title">Featured Restaurants</h2>
            <div class="row">
                <?php foreach ($featuredRestaurants as $rest): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card restaurant-card h-100">
                        <img src="images/restaurants/<?php echo $rest['image'] ?: 'default.jpg'; ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($rest['name']); ?>" 
                             style="height: 200px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($rest['name']); ?></h5>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($rest['description']); ?></p>
                            <div class="mt-auto">
                                <a href="restaurant.php?id=<?php echo $rest['id']; ?>" class="btn btn-primary w-100">
                                    <i class="fas fa-eye me-2"></i>View Menu
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Popular Menu Items -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center section-title">Popular Dishes</h2>
            <div class="row">
                <?php foreach ($popularItems as $item): ?>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card menu-item-card h-100">
                        <img src="images/menu/<?php echo $item['image'] ?: 'default.jpg'; ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($item['name']); ?>" 
                             style="height: 150px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h6>
                            <p class="card-text text-muted small"><?php echo htmlspecialchars(substr($item['description'], 0, 60)) . '...'; ?></p>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-primary">$<?php echo number_format($item['price'], 2); ?></span>
                                <form method="POST" action="actions/cart_add.php" class="d-inline">
                                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                    <input type="hidden" name="name" value="<?php echo htmlspecialchars($item['name']); ?>">
                                    <input type="hidden" name="price" value="<?php echo $item['price']; ?>">
                                    <input type="hidden" name="restaurant_id" value="<?php echo $item['restaurant_id']; ?>">
                                    <input type="hidden" name="restaurant_name" value="<?php echo htmlspecialchars($item['restaurant_name']); ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
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
