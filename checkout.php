<?php
require_once 'config/config.php';
require_once 'includes/User.php';
require_once 'includes/Order.php';

requireLogin();

$user = new User();
$order = new Order();
$user_details = $user->getUserById($_SESSION['user_id']);

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $delivery_address = sanitizeInput($_POST['delivery_address']);
    $phone = sanitizeInput($_POST['phone']);
    $notes = sanitizeInput($_POST['notes']);
    
    if (empty($delivery_address)) {
        $errors[] = 'Delivery address is required';
    }
    if (empty($phone)) {
        $errors[] = 'Phone number is required';
    }
    
    if (empty($errors)) {
        // Prefer server-side session cart
        $cart_data = isset($_SESSION['cart']) ? array_values($_SESSION['cart']) : [];

        if (empty($cart_data)) {
            $errors[] = 'Cart is empty';
        } else {
            // Group items by restaurant
            $restaurants = [];
            foreach ($cart_data as $item) {
                $restaurant_id = $item['restaurant_id'];
                if (!isset($restaurants[$restaurant_id])) {
                    $restaurants[$restaurant_id] = [
                        'restaurant_id' => $restaurant_id,
                        'restaurant_name' => $item['restaurant_name'],
                        'items' => [],
                        'total' => 0
                    ];
                }
                $restaurants[$restaurant_id]['items'][] = $item;
                $restaurants[$restaurant_id]['total'] += $item['price'] * $item['quantity'];
            }
            
            $order_ids = [];
            foreach ($restaurants as $restaurant_data) {
                $order_data = [
                    'user_id' => $_SESSION['user_id'],
                    'restaurant_id' => $restaurant_data['restaurant_id'],
                    'total_amount' => $restaurant_data['total'],
                    'delivery_address' => $delivery_address,
                    'phone' => $phone,
                    'notes' => $notes,
                    'items' => $restaurant_data['items']
                ];
                
                $result = $order->createOrder($order_data);
                if ($result['success']) {
                    $order_ids[] = $result['order_id'];
                } else {
                    $errors = array_merge($errors, $result['errors']);
                }
            }
            
            if (empty($errors)) {
                $success = 'Order placed successfully! Order ID(s): ' . implode(', ', $order_ids);
                // Clear cart (session and client)
                $_SESSION['cart'] = [];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Food Delivery</title>
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
        .checkout-section {
            background: #f8f9fa;
            padding: 2rem 0;
        }
        .order-summary {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
                        <a class="nav-link" href="cart.php">
                            <i class="fas fa-shopping-cart me-1"></i>Cart
                            <span class="badge bg-primary" id="cart-count"><?php echo getCartCount(); ?></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Checkout Section -->
    <section class="checkout-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="mb-4"><i class="fas fa-credit-card me-2"></i>Checkout</h2>
                </div>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                    <div class="mt-3">
                        <a href="user/orders.php" class="btn btn-primary me-2">View My Orders</a>
                        <a href="index.php" class="btn btn-outline-primary">Continue Shopping</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Delivery Information</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($errors)): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        <ul class="mb-0">
                                            <?php foreach ($errors as $error): ?>
                                                <li><?php echo $error; ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                                <form method="POST" action="" id="checkout-form">
                                    <div class="mb-3">
                                        <label for="delivery_address" class="form-label">Delivery Address *</label>
                                        <textarea class="form-control" id="delivery_address" name="delivery_address" 
                                                  rows="3" required><?php echo htmlspecialchars($user_details['address']); ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number *</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" 
                                               value="<?php echo htmlspecialchars($user_details['phone']); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="notes" class="form-label">Special Instructions (Optional)</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="2" 
                                                  placeholder="Any special delivery instructions..."></textarea>
                                    </div>
                                    
                                    
                                    
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-credit-card me-2"></i>Place Order
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="order-summary">
                            <h5 class="mb-3">Order Summary</h5>
                            <div id="order-summary-content">
                                <?php
                                $cart_data = isset($_SESSION['cart']) ? array_values($_SESSION['cart']) : [];
                                if (empty($cart_data)) {
                                    echo '<p class="text-muted">Your cart is empty.</p>';
                                } else {
                                    // Group by restaurant
                                    $grouped = [];
                                    $grandTotal = 0;
                                    foreach ($cart_data as $it) {
                                        $rid = $it['restaurant_id'];
                                        if (!isset($grouped[$rid])) {
                                            $grouped[$rid] = [
                                                'restaurant_name' => $it['restaurant_name'],
                                                'items' => [],
                                                'total' => 0,
                                            ];
                                        }
                                        $grouped[$rid]['items'][] = $it;
                                        $grouped[$rid]['total'] += $it['price'] * $it['quantity'];
                                        $grandTotal += $it['price'] * $it['quantity'];
                                    }

                                    foreach ($grouped as $g) {
                                        echo '<div class="restaurant-group mb-3">';
                                        echo '<h6 class="text-primary">' . htmlspecialchars($g['restaurant_name']) . '</h6>';
                                        foreach ($g['items'] as $it) {
                                            echo '<div class="d-flex justify-content-between mb-1">';
                                            echo '<span>' . htmlspecialchars($it['name']) . ' x' . (int)$it['quantity'] . '</span>';
                                            echo '<span>$' . number_format($it['price'] * $it['quantity'], 2) . '</span>';
                                            echo '</div>';
                                        }
                                        echo '<div class="d-flex justify-content-between border-top pt-2 mb-2">';
                                        echo '<strong>Subtotal:</strong><strong>$' . number_format($g['total'], 2) . '</strong>';
                                        echo '</div>';
                                        echo '</div>';
                                    }

                                    echo '<div class="border-top pt-3">';
                                    echo '<div class="d-flex justify-content-between">';
                                    echo '<h5>Total:</h5><h5 class="text-primary">$' . number_format($grandTotal, 2) . '</h5>';
                                    echo '</div></div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
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
