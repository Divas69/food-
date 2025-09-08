# Quick Setup Guide

## 1. Database Setup

**IMPORTANT:** You need to create the database first!

1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Click "New" to create a new database
3. Name it: `food_delivery`
4. Click "Create"
5. Select the `food_delivery` database
6. Click "Import" tab
7. Choose the `database.sql` file from this project
8. Click "Go" to import

## 2. Test Database Connection

Visit: http://localhost/food/test_db.php

This will show you if the database is set up correctly.

## 3. Access the Application

- **User Interface:** http://localhost/food/
- **Admin Panel:** http://localhost/food/admin/ (redirects to login)

## 4. Admin Login

- **URL:** http://localhost/food/admin/login.php
- **Username:** admin
- **Password:** admin123

## 5. Test Cart Functionality

1. Go to http://localhost/food/
2. Click "Add to Cart" on any item
3. Check browser console (F12) for debug messages
4. Go to Cart page to see items

## Troubleshooting

### Cart is Empty
- Check browser console for JavaScript errors
- Make sure you're logged in as a user
- Try adding items from different pages (menu.php, restaurant.php)

### Database Issues
- Make sure XAMPP MySQL is running
- Check if database `food_delivery` exists
- Verify tables are created (use test_db.php)

### Admin Access Issues
- Use the correct URL: http://localhost/food/admin/login.php
- Default credentials: admin / admin123
- Check if admin table has data
