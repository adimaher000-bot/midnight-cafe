# CafeOnline - Website Setup Guide

This guide will help you set up and run the CafeOnline website on your local machine using XAMPP (Windows) or any PHP/MySQL environment.

## 1. Prerequisites / Requirements
To run this website, you need a local server environment. We recommend **XAMPP** for Windows.

- **XAMPP**: [Download Here](https://www.apachefriends.org/download.html)
- **Web Browser**: Chrome, Firefox, or Edge.
- **Text Editor (Optional)**: VS Code, Notepad++, etc. (if you want to edit code).

## 2. Installation Steps

### Step A: Install XAMPP
1. Download and install XAMPP.
2. Open **XAMPP Control Panel**.
3. Start **Apache** and **MySQL** modules. They should turn green.

### Step B: Setup Project Files
1. Copy the `cafeonline` folder.
2. Paste it inside your XAMPP installation directory:
   - Typical path: `C:\xampp\htdocs\`
   - You should end up with: `C:\xampp\htdocs\cafeonline\`

### Step C: Setup Database
There are two ways to set up the database. **Method 1 is recommended.**

#### Method 1: Automatic Setup (Recommended)
1. Open your browser.
2. Go to: [http://localhost/cafeonline/setup_database.php](http://localhost/cafeonline/setup_database.php)
3. You should see a success message: "Database 'cafe_db' checked/created... Setup Complete!"
4. Click "Go to Website".

#### Method 2: Manual Setup (If Method 1 fails)
1. Open **phpMyAdmin**: [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/)
2. Click **New** (sidebar) > Create database name: `cafe_db`
3. Click "Import" tab (top menu).
4. Choose File: `C:\xampp\htdocs\cafeonline\sql_fixed\init.sql`
5. Click **Go** (bottom right).

## 3. Running the Website
After setup, you can access the website anytime:

- **Customer View**: [http://localhost/cafeonline/](http://localhost/cafeonline/)
- **Admin Panel**: [http://localhost/cafeonline/login.php](http://localhost/cafeonline/login.php)

### Default Login Credentials
- **Admin Email**: `admin@cafe.com`
- **Password**: `admin123`

## 4. Troubleshooting

- **Database Error?**
  - Check `config/db_connect.php`.
  - Ensure XAMPP **MySQL** is running.
  - Default password for XAMPP is empty. If you changed it, update `config/db_connect.php`.

- **Images not loading?**
  - Ensure the `cafeonline` folder is directly inside `htdocs`.
  - URL should be `localhost/cafeonline`, not `localhost/htdocs/cafeonline`.
