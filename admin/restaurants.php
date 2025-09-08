<?php
require_once '../config/config.php';
require_once '../includes/Restaurant.php';

requireAdminLogin();

$restaurant = new Restaurant();
$restaurants = $restaurant->getAllRestaurants();

$errors = [];
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    if ($action === 'add') {
        $data = [
            'name' => sanitizeInput($_POST['name']),
            'description' => sanitizeInput($_POST['description']),
            'address' => sanitizeInput($_POST['address']),
            'phone' => sanitizeInput($_POST['phone']),
            'image' => sanitizeInput($_POST['image'])
        ];
        
        $result = $restaurant->addRestaurant($data);
        if ($result['success']) {
            $success = $result['message'];
            $restaurants = $restaurant->getAllRestaurants(); // Refresh list
        } else {
            $errors = $result['errors'];
        }
    } elseif ($action === 'edit') {
        $id = (int)$_POST['id'];
        $data = [
            'name' => sanitizeInput($_POST['name']),
            'description' => sanitizeInput($_POST['description']),
            'address' => sanitizeInput($_POST['address']),
            'phone' => sanitizeInput($_POST['phone']),
            'image' => isset($_FILES['image_file']) ? $_FILES['image_file']['name'] : ''
        ];

        

        $result = $restaurant->updateRestaurant($id, $data);
        if ($result['success']) {
            $success = $result['message'];
            $restaurants = $restaurant->getAllRestaurants(); // Refresh list
        } else {
            $errors = $result['errors'];
        }
    } elseif ($action === 'delete') {
        $id = (int)$_POST['id'];
        $result = $restaurant->deleteRestaurant($id);
        if ($result['success']) {
            $success = $result['message'];
            $restaurants = $restaurant->getAllRestaurants(); // Refresh list
        } else {
            $errors = $result['errors'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Management - Admin Panel</title>
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
                    <a class="nav-link active" href="restaurants.php">
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
                        <h4 class="mb-0">Restaurant Management</h4>
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

                    <!-- Add Restaurant Button -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5>Restaurants</h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#restaurantModal" onclick="openRestaurantModal()">
                            <i class="fas fa-plus me-2"></i>Add Restaurant
                        </button>
                    </div>

                    <!-- Restaurants Table -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>Address</th>
                                            <th>Phone</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($restaurants as $rest): ?>
                                        <tr>
                                            <td><?php echo $rest['id']; ?></td>
                                            <td><?php echo htmlspecialchars($rest['name']); ?></td>
                                            <td><?php echo htmlspecialchars(substr($rest['description'], 0, 50)) . '...'; ?></td>
                                            <td><?php echo htmlspecialchars(substr($rest['address'], 0, 30)) . '...'; ?></td>
                                            <td><?php echo htmlspecialchars($rest['phone']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $rest['status'] === 'active' ? 'success' : 'danger'; ?>">
                                                    <?php echo ucfirst($rest['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary me-1" 
                                                        onclick="editRestaurant(<?php echo htmlspecialchars(json_encode($rest)); ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteRestaurant(<?php echo $rest['id']; ?>, '<?php echo htmlspecialchars($rest['name']); ?>')">
                                                    <i class="fas fa-trash"></i>
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

    <!-- Restaurant Modal -->
    <div class="modal fade" id="restaurantModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Restaurant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="action" id="action" value="add">
                        <input type="hidden" name="id" id="restaurantId">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Restaurant Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address *</label>
                            <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" class="form-control" id="image" name="image_file" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Restaurant</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the restaurant "<span id="deleteRestaurantName"></span>"?</p>
                    <p class="text-danger"><strong>Warning:</strong> This action cannot be undone and will also delete all associated menu items.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="deleteRestaurantId">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openRestaurantModal() {
            document.getElementById('modalTitle').textContent = 'Add Restaurant';
            document.getElementById('action').value = 'add';
            document.getElementById('restaurantId').value = '';
            document.getElementById('name').value = '';
            document.getElementById('description').value = '';
            document.getElementById('address').value = '';
            document.getElementById('phone').value = '';
            document.getElementById('image').value = '';
        }
        
        function editRestaurant(restaurant) {
            document.getElementById('modalTitle').textContent = 'Edit Restaurant';
            document.getElementById('action').value = 'edit';
            document.getElementById('restaurantId').value = restaurant.id;
            document.getElementById('name').value = restaurant.name;
            document.getElementById('description').value = restaurant.description;
            document.getElementById('address').value = restaurant.address;
            document.getElementById('phone').value = restaurant.phone;
            document.getElementById('image').value = restaurant.image;
            
            const modal = new bootstrap.Modal(document.getElementById('restaurantModal'));
            modal.show();
        }
        
        function deleteRestaurant(id, name) {
            document.getElementById('deleteRestaurantId').value = id;
            document.getElementById('deleteRestaurantName').textContent = name;
            
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }
    </script>
</body>
</html>
