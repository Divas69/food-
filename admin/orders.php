<?php
require_once '../config/config.php';
require_once '../includes/Order.php';

requireAdminLogin();

$order = new Order();
$orders = $order->getAllOrders();

$errors = [];
$success = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = sanitizeInput($_POST['status']);
    
    $result = $order->updateOrderStatus($order_id, $status);
    if ($result['success']) {
        $success = $result['message'];
        $orders = $order->getAllOrders(); // Refresh list
    } else {
        $errors = $result['errors'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            min-height: 100vh;
            padding: 0;
        }
        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 1rem 1.5rem;
            border-radius: 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: #3498db;
            color: white;
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .btn-primary {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
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
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="p-3">
                    <h5 class="text-white mb-0">
                        <i class="fas fa-shield-alt me-2"></i>Admin Panel
                    </h5>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link" href="dashboard.php">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="restaurants.php">
                        <i class="fas fa-store me-2"></i>Restaurants
                    </a>
                    <a class="nav-link" href="menu.php">
                        <i class="fas fa-utensils me-2"></i>Menu Items
                    </a>
                    <a class="nav-link active" href="orders.php">
                        <i class="fas fa-shopping-bag me-2"></i>Orders
                    </a>
                    <hr class="text-white">
                    
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Top Navigation -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                    <div class="container-fluid">
                        <h4 class="mb-0">Order Management</h4>
                        <div class="navbar-nav ms-auto">
                            <span class="navbar-text">
                                Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                            </span>
                        </div>
                    </div>
                </nav>

                <!-- Content -->
                <div class="p-4">
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
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                        </div>
                    <?php endif; ?>

                    <h5 class="mb-4">All Orders</h5>

                    <!-- Orders Table -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Restaurant</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order_item): ?>
                                        <tr>
                                            <td>#<?php echo $order_item['id']; ?></td>
                                            <td><?php echo htmlspecialchars($order_item['user_name']); ?></td>
                                            <td><?php echo htmlspecialchars($order_item['restaurant_name']); ?></td>
                                            <td>$<?php echo number_format($order_item['total_amount'], 2); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo $order_item['status']; ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $order_item['status'])); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M j, Y H:i', strtotime($order_item['created_at'])); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary me-1" 
                                                        onclick="viewOrderDetails(<?php echo $order_item['id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-success" 
                                                        onclick="updateOrderStatus(<?php echo $order_item['id']; ?>, '<?php echo $order_item['status']; ?>')">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    <!-- Update Status Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="order_id" id="statusOrderId">
                        <input type="hidden" name="update_status" value="1">
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Order Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="preparing">Preparing</option>
                                <option value="out_for_delivery">Out for Delivery</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewOrderDetails(orderId) {
            // Fetch order details via AJAX
            fetch(`../api/get_order_details_admin.php?id=${orderId}`)
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
                        <p><strong>Customer:</strong> ${order.user_name}</p>
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
        
        function updateOrderStatus(orderId, currentStatus) {
            document.getElementById('statusOrderId').value = orderId;
            document.getElementById('status').value = currentStatus;
            
            const modal = new bootstrap.Modal(document.getElementById('statusModal'));
            modal.show();
        }
    </script>
</body>
</html>
