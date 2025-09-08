# Food Delivery System

A comprehensive full-stack online food delivery system built with PHP, MySQL, HTML, CSS, and JavaScript. Features Bootstrap and TailwindCSS for responsive UI design.

## Features

### User Section
- **User Registration & Login**: Secure authentication with password hashing
- **Home Page**: Displays featured restaurants and popular dishes
- **Restaurant Listing**: Browse all available restaurants
- **Menu Display**: View restaurant menus with categories
- **Shopping Cart**: Add items to cart and manage quantities
- **Order Placement**: Complete checkout process with delivery information
- **Search Functionality**: Search food items by name
- **Sorting**: Sort menu items by price (low to high, high to low)
- **User Profile**: Update personal information
- **Order History**: View past orders and their status

### Admin Section
- **Admin Login**: Secure admin authentication
- **Dashboard**: Overview of orders, revenue, and statistics
- **Restaurant Management**: Add, edit, and delete restaurants
- **Menu Management**: Add, edit, and delete menu items for each restaurant
- **Order Management**: View all orders and update order status
- **Order Statistics**: Track total orders, revenue, and status distribution

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **UI Framework**: Bootstrap 5.1.3
- **Icons**: Font Awesome 6.0.0
- **Server**: XAMPP (Apache + MySQL + PHP)

## Installation & Setup

### Prerequisites
- XAMPP installed and running
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Installation Steps

1. **Clone/Download the project**
   ```bash
   # Place the project in your XAMPP htdocs folder
   # Path: /Applications/XAMPP/xamppfiles/htdocs/food
   ```

2. **Start XAMPP Services**
   - Start Apache and MySQL from XAMPP Control Panel

3. **Create Database**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Import the `database.sql` file to create the database and sample data

4. **Configure Database Connection**
   - Edit `config/database.php` if needed (default settings work with XAMPP)

5. **Set Permissions**
   ```bash
   chmod 755 /Applications/XAMPP/xamppfiles/htdocs/food
   chmod 755 /Applications/XAMPP/xamppfiles/htdocs/food/uploads
   ```

6. **Access the Application**
   - User Interface: http://localhost/food/
   - Admin Panel: http://localhost/food/admin/login.php

## Default Credentials

### Admin Login
- **Username**: admin
- **Password**: admin123

### Sample Data
The database includes sample restaurants and menu items:
- Pizza Palace
- Burger House
- Sushi Zen
- Taco Fiesta

## Project Structure

```
food/
├── admin/                  # Admin panel files
│   ├── dashboard.php      # Admin dashboard
│   ├── login.php          # Admin login
│   ├── logout.php         # Admin logout
│   ├── restaurants.php    # Restaurant management
│   ├── menu.php           # Menu item management
│   └── orders.php         # Order management
├── api/                   # API endpoints
│   ├── get_order_details.php
│   └── get_order_details_admin.php
├── config/                # Configuration files
│   ├── config.php         # Main configuration
│   └── database.php       # Database connection
├── includes/              # PHP classes and models
│   ├── User.php           # User model
│   ├── Admin.php          # Admin model
│   ├── Restaurant.php     # Restaurant model
│   ├── MenuItem.php       # Menu item model
│   └── Order.php          # Order model
├── user/                  # User section files
│   ├── login.php          # User login
│   ├── register.php       # User registration
│   ├── logout.php         # User logout
│   ├── profile.php        # User profile
│   └── orders.php         # User orders
├── images/                # Image assets
│   ├── restaurants/       # Restaurant images
│   └── menu/              # Menu item images
├── js/                    # JavaScript files
│   └── cart.js            # Shopping cart functionality
├── uploads/               # File uploads directory
├── index.php              # Home page
├── restaurants.php        # Restaurant listing
├── restaurant.php         # Individual restaurant page
├── menu.php               # Menu listing
├── cart.php               # Shopping cart
├── checkout.php           # Checkout process
├── database.sql           # Database schema and sample data
└── README.md              # This file
```

## Key Features Implementation

### Security Features
- Password hashing using PHP's `password_hash()`
- Input sanitization and validation
- SQL injection prevention with prepared statements
- Session management
- CSRF token generation (ready for implementation)

### Database Design
- Normalized database structure
- Foreign key relationships
- Proper indexing for performance
- Status tracking for orders and items

### User Experience
- Responsive design for all devices
- Real-time cart updates
- Search and filter functionality
- Order status tracking
- Clean and intuitive interface

### Admin Features
- Comprehensive dashboard with statistics
- CRUD operations for restaurants and menu items
- Order management with status updates
- Real-time data updates

## Customization

### Adding New Restaurants
1. Login to admin panel
2. Go to Restaurant Management
3. Click "Add Restaurant"
4. Fill in restaurant details

### Adding Menu Items
1. Login to admin panel
2. Go to Menu Management
3. Click "Add Menu Item"
4. Select restaurant and fill item details

### Styling Customization
- Modify CSS in individual PHP files
- Update Bootstrap classes for layout changes
- Customize color schemes in the style sections

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check if MySQL is running in XAMPP
   - Verify database credentials in `config/database.php`

2. **Images Not Loading**
   - Ensure images are placed in correct directories
   - Check file permissions on images folder

3. **Session Issues**
   - Clear browser cookies and cache
   - Restart Apache server

4. **Permission Errors**
   - Set proper permissions on uploads directory
   - Ensure PHP has write access to required folders

## Development Notes

- The system uses PDO for database operations
- JavaScript handles client-side cart functionality
- AJAX is used for dynamic content loading
- Bootstrap provides responsive grid system
- Font Awesome icons enhance UI elements

## Future Enhancements

- Payment gateway integration
- Real-time notifications
- Advanced search filters
- Restaurant ratings and reviews
- Delivery tracking
- Mobile app development
- Email notifications
- Advanced admin analytics

## Support

For issues or questions:
1. Check the troubleshooting section
2. Verify all installation steps
3. Check XAMPP error logs
4. Ensure all dependencies are properly installed

## License

This project is created for educational purposes. Feel free to modify and enhance as needed.
# food-
