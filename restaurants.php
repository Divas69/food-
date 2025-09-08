<?php
require_once 'config/config.php';
require_once 'includes/Restaurant.php';

$restaurant = new Restaurant();
$restaurants = $restaurant->getAllRestaurants();

// Handle search
$search = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = sanitizeInput($_GET['search']);
    $restaurants = $restaurant->searchRestaurants($search);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurants - Food Delivery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
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
    </style>
</head>
<body>
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
                        <a class="nav-link active" href="restaurants.php">Restaurants</a>
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

    <!-- Search Section -->
    <section class="search-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h2 class="text-center mb-4">Find Your Favorite Restaurant</h2>
                    <form method="GET" action="restaurants.php">
                        <div class="input-group input-group-lg">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Search restaurants..." 
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

    <!-- Restaurants Section -->
    <section class="py-5">
        <div class="container">
            <?php if (!empty($search)): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <h4>Search Results for "<?php echo htmlspecialchars($search); ?>"</h4>
                        <p class="text-muted"><?php echo count($restaurants); ?> restaurant(s) found</p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (empty($restaurants)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-store" style="font-size: 4rem; color: #ccc;"></i>
                    <h4 class="mt-3">No restaurants found</h4>
                    <p class="text-muted">Try adjusting your search criteria</p>
                    <a href="restaurants.php" class="btn btn-primary">View All Restaurants</a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($restaurants as $rest): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card restaurant-card h-100">
                            <img src="images/restaurants/<?php echo $rest['image'] ?: 'default.jpg'; ?>" 
                                 class="card-img-top" alt="<?php echo htmlspecialchars($rest['name']); ?>" 
                                 style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($rest['name']); ?></h5>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($rest['description']); ?></p>
                                
                                <div class="restaurant-info mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                        <small class="text-muted"><?php echo htmlspecialchars($rest['address']); ?></small>
                                    </div>
                                    <?php if ($rest['phone']): ?>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-phone text-primary me-2"></i>
                                        <small class="text-muted"><?php echo htmlspecialchars($rest['phone']); ?></small>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
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
