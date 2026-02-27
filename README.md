<p align="center">
    <h1 align="center">🛍️ Gadget Shop Ecommerce</h1>
    <p align="center">
        A Modern Laravel Based Ecommerce Application
    </p>
</p>

<p align="center">
<img src="https://img.shields.io/badge/PHP-8.2-blue">
<img src="https://img.shields.io/badge/Laravel-12-red">
<img src="https://img.shields.io/badge/MySQL-Database-orange">
<img src="https://img.shields.io/badge/License-MIT-green">
</p>

---

## 🚀 About The Project

**Gadget Shop Ecommerce** is a full-featured ecommerce web application built with the Laravel framework.

This project includes:

- Product Management
- Category & Brand Management
- Product Variants & Warranty Support
- Advanced Search System
- Cart & Checkout
- Order Management
- Admin Dashboard
- Role Based Authentication

Built using the powerful **Laravel Framework**.

---

## 🛠 Tech Stack

- Backend: Laravel
- Frontend: Blade Template Engine
- Database: MySQL
- Styling: Tailwind CSS / Custom CSS
- Authentication: Laravel Auth (Breeze)

---

## ✨ Features

### 👤 Customer Side

- Browse Products
- Search Products
- Filter by Category & Brand
- Product Details (SEO Slug Based)
- Add to Cart
- Wishlist
- Checkout System
- Order History

### 🛠 Admin Panel

- Dashboard Overview
- Manage Categories
- Manage Brands
- Manage Products
- Product Variants
- Warranty Management
- Order Management
- Site Settings

---

## 📦 Installation Guide

Follow the steps below to run the project locally.

---

```bash

### 1️⃣ Clone the Repository


git clone https://github.com/mishimanto/e-commerce-Laravel-blade.git
cd e-commerce-Laravel-blade


### 2️⃣ Install Dependencies
composer install
npm install
npm run build

### 3️⃣ Environment Setup
.env.example -> .env

DB_DATABASE=gadget_shop
DB_USERNAME=root
DB_PASSWORD=


### 4️⃣ Generate App Key
php artisan key:generate


### 5️⃣ Run Migrations
php artisan migrate
php artisan db:seed


### 6️⃣ Storage Link
php artisan storage:link


### 7️⃣ Run the Project
php artisan serve
