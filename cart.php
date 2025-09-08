<?php
require_once 'config/config.php';
requireLogin();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Food Delivery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
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
        .cart-item {
            transition: all 0.3s ease;
        }
        .cart-item:hover {
            background-color: #f8f9fa;
        }
        .quantity-controls .btn {
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
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
                        <a class="nav-link" href="restaurants.php">Restaurants</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="menu.php">Menu</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
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
                        <a class="nav-link active" href="cart.php">
                            <i class="fas fa-shopping-cart me-1"></i>Cart
                            <span class="badge bg-primary" id="cart-count">0</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Cart Section -->
    <section class="py-5">
        <div class="container">
            <?php $flash = getFlash(); if ($flash): ?>
                <div class="alert alert-<?php echo $flash['type']==='success'?'success':'info'; ?>">
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-12">
                    <h2 class="mb-4"><i class="fas fa-shopping-cart me-2"></i>Shopping Cart</h2>
                </div>
            </div>

            <!-- Empty Cart Message -->
            <div id="empty-cart" class="text-center py-5" style="display: none;">
                <i class="fas fa-shopping-cart" style="font-size: 4rem; color: #ccc;"></i>
                <h4 class="mt-3">Your cart is empty</h4>
                <p class="text-muted">Add some delicious items to get started!</p>
                <a href="menu.php" class="btn btn-primary">
                    <i class="fas fa-utensils me-2"></i>Browse Menu
                </a>
            </div>

            <!-- Cart Content -->
            <div id="cart-content">
                <div class="row">
                    <div class="col-lg-8">
                        <div id="cart-items">
                            <?php
                            $items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
                            if (empty($items)) {
                                echo '<div class="text-muted">No items in cart.</div>';
                            } else {
                                foreach ($items as $it) {
                                    $lineTotal = $it['price'] * $it['quantity'];
                                    echo '<div class="cart-item d-flex justify-content-between align-items-center p-3 border rounded mb-2">';
                                    echo '<div class="item-info">';
                                    echo '<h6 class="mb-1">' . htmlspecialchars($it['name']) . '</h6>';
                                    echo '<span class="text-muted">$' . number_format($it['price'], 2) . ' each</span>';
                                    echo '</div>';
                                    echo '<div class="item-controls d-flex align-items-center">';
                                    echo '<form method="POST" action="actions/cart_update.php" class="d-flex align-items-center me-3">';
                                    echo '<button class="btn btn-sm btn-outline-secondary quantity-btn" type="button" onclick="this.nextElementSibling.stepDown(); this.nextElementSibling.dispatchEvent(new Event(\'change\'));">-</button>';
                                    echo '<input type="number" class="form-control form-control-sm text-center mx-2" name="qty[' . htmlspecialchars($it['id']) . ']" value="' . (int)$it['quantity'] . '" min="1" style="width: 60px;" onchange="this.form.submit()">';
                                    echo '<button class="btn btn-sm btn-outline-secondary quantity-btn" type="button" onclick="this.previousElementSibling.stepUp(); this.previousElementSibling.dispatchEvent(new Event(\'change\'));">+</button>';
                                    echo '</form>';
                                    echo '<div class="item-total me-3"><strong>$' . number_format($lineTotal, 2) . '</strong></div>';
                                    echo '<a class="btn btn-sm btn-outline-danger remove-item" href="actions/cart_remove.php?id=' . urlencode($it['id']) . '"><i class="fas fa-trash"></i></a>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Order Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Items (<span id="cart-count-display"><?php echo getCartCount(); ?></span>):</span>
                                    <span id="cart-total">$<?php echo number_format(getCartTotal(), 2); ?></span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-3">
                                    <strong>Total:</strong>
                                    <strong id="cart-total-final">$<?php echo number_format(getCartTotal(), 2); ?></strong>
                                </div>
                                
                                <a class="btn btn-primary w-100 mb-3" href="checkout.php">
                                    <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                                </a>
                                
                                <a class="btn btn-outline-danger w-100" href="actions/cart_clear.php">
                                    <i class="fas fa-trash me-2"></i>Clear Cart
                                </a>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <a href="menu.php" class="btn btn-outline-primary w-100">
                                <i class="fas fa-plus me-2"></i>Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
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
    <script>
        // Initialize cart display when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Restore cart from session if available
            try {
                const serverCart = <?php echo json_encode(isset($_SESSION['cart_items']) ? $_SESSION['cart_items'] : []); ?>;
                if (Array.isArray(serverCart) && serverCart.length > 0) {
                    localStorage.setItem('food_cart', JSON.stringify(serverCart));
                }
            } catch(e) {}
            updateCartPage();
        });
    </script>
</body>
</html>
