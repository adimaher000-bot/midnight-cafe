# Modern Cafe Website & Management System

## Overview
A responsive, feature-rich website for a Cafe with a "Midnight Cyber-Botanic Theme". Includes menu browsing, cart management, table booking, and a comprehensive admin panel.

## Features
- **Frontend**: Glassmorphism UI, Responsive Design, Dynamic Menu and Cart.
- **Backend**: PHP/MySQL with Session Management.
- **Admin**: Dashboard, Order Management, Menu CRUD, Bookings.
- **Infrastructure**: Dockerized environment.

## Quick Start
1.  **Start Docker**:
    ```bash
    docker-compose up -d --build
    ```

2.  **Initialize Admin**:
    Visit: [http://localhost:8080/setup_admin.php](http://localhost:8080/setup_admin.php)
    - Default credentials: `admin@cafe.com` / `admin123`

3.  **Access App**:
    - **Frontend**: [http://localhost:8080](http://localhost:8080)
    - **Admin Login**: [http://localhost:8080/admin/login.php](http://localhost:8080/admin/login.php)

## Directory Structure
- `admin/`: Admin panel files.
- `css/`: Theme stylesheets.
- `js/`: Frontend scripts.
- `includes/`: Header, Footer, Functions.
- `config/`: Database connection.
- `pages/`: Processing logic (Cart, Booking).
- `sql_fixed/`: Database initialization script.
