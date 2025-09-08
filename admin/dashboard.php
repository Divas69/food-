<?php
require_once '../config/config.php';
require_once '../includes/Order.php';
require_once '../includes/Restaurant.php';
require_once '../includes/MenuItem.php';

requireAdminLogin();

$order = new Order();
$restaurant = new Restaurant();
$menuItem = new MenuItem();

// Get statistics
$stats = $order->getOrderStats();
$totalRestaurants = count($restaurant->getAllRestaurants());
$totalMenuItems = count($menuItem->getAllMenuItems());

// Get recent orders
$recentOrders = array_slice($order->getAllOrders(), 0, 5);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Food Delivery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }
        .stat-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-3px);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
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
                    <a class="nav-link active" href="dashboard.php">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="restaurants.php">
                        <i class="fas fa-store me-2"></i>Restaurants
                    </a>
                    <a class="nav-link" href="menu.php">
                        <i class="fas fa-utensils me-2"></i>Menu Items
                    </a>
                    <a class="nav-link" href="orders.php">
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
                        <h4 class="mb-0">Dashboard</h4>
                        <div class="navbar-nav ms-auto">
                            <span class="navbar-text">
                                Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                            </span>
                        </div>
                    </div>
                </nav>

                <!-- Dashboard Content -->
                <div class="p-4">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body d-flex align-items-center">
                                    <div class="stat-icon bg-primary me-3">
                                        <i class="fas fa-shopping-bag"></i>
                                    </div>
                                    <div>
                                        <h6 class="card-title mb-0">Total Orders</h6>
                                        <h4 class="mb-0"><?php echo $stats['total_orders']; ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body d-flex align-items-center">
                                    <div class="stat-icon bg-success me-3">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                    <div>
                                        <h6 class="card-title mb-0">Total Revenue</h6>
                                        <h4 class="mb-0">$<?php echo number_format($stats['total_revenue'], 2); ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body d-flex align-items-center">
                                    <div class="stat-icon bg-warning me-3">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <div>
                                        <h6 class="card-title mb-0">Restaurants</h6>
                                        <h4 class="mb-0"><?php echo $totalRestaurants; ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body d-flex align-items-center">
                                    <div class="stat-icon bg-info me-3">
                                        <i class="fas fa-utensils"></i>
                                    </div>
                                    <div>
                                        <h6 class="card-title mb-0">Menu Items</h6>
                                        <h4 class="mb-0"><?php echo $totalMenuItems; ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Status Overview -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Order Status Overview</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'confirmed' => 'info',
                                            'preparing' => 'primary',
                                            'out_for_delivery' => 'secondary',
                                            'delivered' => 'success',
                                            'cancelled' => 'danger'
                                        ];
                                        
                                        foreach ($stats['status_counts'] as $status => $count):
                                            $color = $statusColors[$status] ?? 'secondary';
                                        ?>
                                        <div class="col-md-2 mb-3">
                                            <div class="text-center">
                                                <div class="badge bg-<?php echo $color; ?> fs-6 mb-2">
                                                    <?php echo ucfirst(str_replace('_', ' ', $status)); ?>
                                                </div>
                                                <h4 class="mb-0"><?php echo $count; ?></h4>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Recent Orders</h5>
                                    <a href="orders.php" class="btn btn-primary btn-sm">View All</a>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($recentOrders)): ?>
                                        <p class="text-muted text-center">No orders yet</p>
                                    <?php else: ?>
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
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($recentOrders as $order_item): ?>
                                                    <tr>
                                                        <td>#<?php echo $order_item['id']; ?></td>
                                                        <td><?php echo htmlspecialchars($order_item['user_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($order_item['restaurant_name']); ?></td>
                                                        <td>$<?php echo number_format($order_item['total_amount'], 2); ?></td>
                                                        <td>
                                                            <span class="badge bg-<?php echo $statusColors[$order_item['status']] ?? 'secondary'; ?>">
                                                                <?php echo ucfirst(str_replace('_', ' ', $order_item['status'])); ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo date('M j, Y', strtotime($order_item['created_at'])); ?></td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
