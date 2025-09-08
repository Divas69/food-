<?php
require_once '../config/config.php';
require_once '../includes/Order.php';

requireLogin();

$order = new Order();
$orders = $order->getOrdersByUser($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Food Delivery</title>
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
        .order-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .order-card:hover {
            transform: translateY(-3px);
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }
        .status-pending { background-color: #ffc107; color: #000; }
        .status-confirmed { background-color: #17a2b8; color: #fff; }
        .status-preparing { background-color: #fd7e14; color: #fff; }
        .status-out_for_delivery { background-color: #6f42c1; color: #fff; }
        .status-delivered { background-color: #28a745; color: #fff; }
        .status-cancelled { background-color: #dc3545; color: #fff; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand text-primary" href="../index.php">
                <i class="fas fa-utensils me-2"></i>Food Delivery
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../restaurants.php">Restaurants</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../menu.php">Menu</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['full_name']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item active" href="orders.php"><i class="fas fa-shopping-bag me-2"></i>My Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../cart.php">
                            <i class="fas fa-shopping-cart me-1"></i>Cart
                            <span class="badge bg-primary" id="cart-count">0</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Orders Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="mb-4"><i class="fas fa-shopping-bag me-2"></i>My Orders</h2>
                </div>
            </div>

            <?php if (empty($orders)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-shopping-bag" style="font-size: 4rem; color: #ccc;"></i>
                    <h4 class="mt-3">No orders yet</h4>
                    <p class="text-muted">Start ordering delicious food from our restaurants!</p>
                    <a href="../menu.php" class="btn btn-primary">
                        <i class="fas fa-utensils me-2"></i>Browse Menu
                    </a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($orders as $order_item): ?>
                    <div class="col-12 mb-4">
                        <div class="card order-card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <h6 class="mb-1">Order #<?php echo $order_item['id']; ?></h6>
                                        <p class="text-muted mb-0"><?php echo htmlspecialchars($order_item['restaurant_name']); ?></p>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <p class="mb-1"><strong>$<?php echo number_format($order_item['total_amount'], 2); ?></strong></p>
                                        <small class="text-muted"><?php echo date('M j, Y', strtotime($order_item['created_at'])); ?></small>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <span class="status-badge status-<?php echo $order_item['status']; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $order_item['status'])); ?>
                                        </span>
                                    </div>
                                    
                                    <div class="col-md-4 text-md-end">
                                        <button class="btn btn-outline-primary btn-sm" 
                                                onclick="viewOrderDetails(<?php echo $order_item['id']; ?>)">
                                            <i class="fas fa-eye me-1"></i>View Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    <!-- Order details will be loaded here -->
                </div>
            </div>
        </div>
    </div>

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
        function viewOrderDetails(orderId) {
            // Fetch order details via AJAX
            fetch(`../api/get_order_details.php?id=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayOrderDetails(data.order, data.items);
                        const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
                        modal.show();
                    } else {
                        alert('Failed to load order details');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load order details');
                });
        }
        
        function displayOrderDetails(order, items) {
            const content = document.getElementById('orderDetailsContent');
            
            let html = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6>Order Information</h6>
                        <p><strong>Order ID:</strong> #${order.id}</p>
                        <p><strong>Restaurant:</strong> ${order.restaurant_name}</p>
                        <p><strong>Status:</strong> <span class="status-badge status-${order.status}">${order.status.replace('_', ' ')}</span></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Delivery Information</h6>
                        <p><strong>Address:</strong> ${order.delivery_address}</p>
                        <p><strong>Phone:</strong> ${order.phone}</p>
                        <p><strong>Order Date:</strong> ${new Date(order.created_at).toLocaleString()}</p>
                    </div>
                </div>
                
                <h6>Order Items</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            items.forEach(item => {
                html += `
                    <tr>
                        <td>${item.item_name}</td>
                        <td>${item.quantity}</td>
                        <td>$${parseFloat(item.price).toFixed(2)}</td>
                        <td>$${(item.price * item.quantity).toFixed(2)}</td>
                    </tr>
                `;
            });
            
            html += `
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3">Total Amount:</th>
                                <th>$${parseFloat(order.total_amount).toFixed(2)}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            `;
            
            if (order.notes) {
                html += `<p><strong>Special Instructions:</strong> ${order.notes}</p>`;
            }
            
            content.innerHTML = html;
        }
    </script>
</body>
</html>
