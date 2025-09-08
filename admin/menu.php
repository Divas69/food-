<?php
require_once '../config/config.php';
require_once '../includes/MenuItem.php';
require_once '../includes/Restaurant.php';

requireAdminLogin();

$menuItem = new MenuItem();
$restaurant = new Restaurant();

$menuItems = $menuItem->getAllMenuItems();
$restaurants = $restaurant->getAllRestaurants();

$errors = [];
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    if ($action === 'add') {
        $data = [
            'restaurant_id' => (int)$_POST['restaurant_id'],
            'name' => sanitizeInput($_POST['name']),
            'description' => sanitizeInput($_POST['description']),
            'price' => (float)$_POST['price'],
            'image' => sanitizeInput($_POST['image']),
            'category' => sanitizeInput($_POST['category'])
        ];
        
        $result = $menuItem->addMenuItem($data);
        if ($result['success']) {
            $success = $result['message'];
            $menuItems = $menuItem->getAllMenuItems(); // Refresh list
        } else {
            $errors = $result['errors'];
        }
    } elseif ($action === 'edit') {
        $id = (int)$_POST['id'];
        $data = [
            'name' => sanitizeInput($_POST['name']),
            'description' => sanitizeInput($_POST['description']),
            'price' => (float)$_POST['price'],
            'image' => sanitizeInput($_POST['image']),
            'category' => sanitizeInput($_POST['category'])
        ];
        
        $result = $menuItem->updateMenuItem($id, $data);
        if ($result['success']) {
            $success = $result['message'];
            $menuItems = $menuItem->getAllMenuItems(); // Refresh list
        } else {
            $errors = $result['errors'];
        }
    } elseif ($action === 'delete') {
        $id = (int)$_POST['id'];
        $result = $menuItem->deleteMenuItem($id);
        if ($result['success']) {
            $success = $result['message'];
            $menuItems = $menuItem->getAllMenuItems(); // Refresh list
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
    <title>Menu Management - Admin Panel</title>
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
                    <a class="nav-link" href="restaurants.php">
                        <i class="fas fa-store me-2"></i>Restaurants
                    </a>
                    <a class="nav-link active" href="menu.php">
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
                        <h4 class="mb-0">Menu Management</h4>
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

                    <!-- Add Menu Item Button -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5>Menu Items</h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#menuModal" onclick="openMenuModal()">
                            <i class="fas fa-plus me-2"></i>Add Menu Item
                        </button>
                    </div>

                    <!-- Menu Items Table -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Restaurant</th>
                                            <th>Category</th>
                                            <th>Price</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($menuItems as $item): ?>
                                        <tr>
                                            <td><?php echo $item['id']; ?></td>
                                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                                            <td><?php echo htmlspecialchars($item['restaurant_name']); ?></td>
                                            <td><?php echo htmlspecialchars($item['category']); ?></td>
                                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $item['status'] === 'available' ? 'success' : 'danger'; ?>">
                                                    <?php echo ucfirst($item['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary me-1" 
                                                        onclick="editMenuItem(<?php echo htmlspecialchars(json_encode($item)); ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteMenuItem(<?php echo $item['id']; ?>, '<?php echo htmlspecialchars($item['name']); ?>')">
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

    <!-- Menu Item Modal -->
    <div class="modal fade" id="menuModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Menu Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="action" id="action" value="add">
                        <input type="hidden" name="id" id="menuItemId">
                        
                        <div class="mb-3">
                            <label for="restaurant_id" class="form-label">Restaurant *</label>
                            <select class="form-select" id="restaurant_id" name="restaurant_id" required>
                                <option value="">Select Restaurant</option>
                                <?php foreach ($restaurants as $rest): ?>
                                    <option value="<?php echo $rest['id']; ?>"><?php echo htmlspecialchars($rest['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Item Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Price *</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Category</label>
                                <input type="text" class="form-control" id="category" name="category" placeholder="e.g., Pizza, Burger">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Image Filename</label>
                            <input type="text" class="form-control" id="image" name="image" placeholder="item.jpg">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Menu Item</button>
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
                    <p>Are you sure you want to delete the menu item "<span id="deleteMenuItemName"></span>"?</p>
                    <p class="text-danger"><strong>Warning:</strong> This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="deleteMenuItemId">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openMenuModal() {
            document.getElementById('modalTitle').textContent = 'Add Menu Item';
            document.getElementById('action').value = 'add';
            document.getElementById('menuItemId').value = '';
            document.getElementById('restaurant_id').value = '';
            document.getElementById('name').value = '';
            document.getElementById('description').value = '';
            document.getElementById('price').value = '';
            document.getElementById('category').value = '';
            document.getElementById('image').value = '';
        }
        
        function editMenuItem(item) {
            document.getElementById('modalTitle').textContent = 'Edit Menu Item';
            document.getElementById('action').value = 'edit';
            document.getElementById('menuItemId').value = item.id;
            document.getElementById('restaurant_id').value = item.restaurant_id;
            document.getElementById('name').value = item.name;
            document.getElementById('description').value = item.description;
            document.getElementById('price').value = item.price;
            document.getElementById('category').value = item.category;
            document.getElementById('image').value = item.image;
            
            // Disable restaurant selection for editing
            document.getElementById('restaurant_id').disabled = true;
            
            const modal = new bootstrap.Modal(document.getElementById('menuModal'));
            modal.show();
        }
        
        function deleteMenuItem(id, name) {
            document.getElementById('deleteMenuItemId').value = id;
            document.getElementById('deleteMenuItemName').textContent = name;
            
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }
    </script>
</body>
</html>
