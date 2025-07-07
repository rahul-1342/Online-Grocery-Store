# 🛒 Online Grocery Store

An Online Grocery Store web application built to simplify grocery shopping. It allows users to browse products, add them to a cart, and place orders seamlessly from the comfort of their homes.

---

## 🚀 Features

- 🧾 User Registration & Login
- 🛍️ Browse grocery products by category
- 🛒 Add to Cart & Checkout
- 💳 Online/Offline Payment (optional for now)
- 📦 Order Tracking
- 🧑 Admin Panel for Product & Order Management

---

## 🛠️ Tech Stack

| Frontend      | Backend       | Database    | Tools         |
| ------------- | ------------- | ----------- | ------------- |
| HTML, CSS, JavaScript | PHP / Node.js / Java (based on your project) | MySQL / MongoDB | Git, GitHub, VS Code |

---

## 📂 Project Structure

Step-by-Step Project Setup Guide (PHP + MySQL)
🔧 Prerequisites
Make sure the following tools are installed:

Tool	Download Link
XAMPP/WAMP/MAMP	https://www.apachefriends.org
Code Editor	VS Code
Git (optional)	https://git-scm.com/
Web Browser	Chrome, Firefox, etc.

📁 Step 1: Clone or Download the Project
bash
Copy
Edit
git clone https://github.com/your-username/online-grocery-store.git
OR simply download the ZIP from GitHub and extract it.

📁 Step 2: Move Project to Server Directory
If you're using XAMPP, move the project folder into:

makefile
Copy
Edit
C:\xampp\htdocs\
The final path should look like:

makefile
Copy
Edit
C:\xampp\htdocs\online-grocery-store\
🛠️ Step 3: Start Apache and MySQL
Open XAMPP Control Panel

Click Start for both Apache and MySQL

🗃️ Step 4: Set Up the Database
Open your browser and go to:
http://localhost/phpmyadmin

Click Import → Upload database.sql file from your project.

Click Go to create all tables.

⚙️ Step 5: Update Database Configuration
Open backend/db_connection.php and update:

php
Copy
Edit
$host = 'localhost';
$user = 'root';        // default for XAMPP
$password = '';        // leave blank
$database = 'grocery_store'; // or your DB name
🌐 Step 6: Run the Project in Browser
Go to:

arduino
Copy
Edit
http://localhost/online-grocery-store/
Start browsing, login/register, add products to cart, etc.

👨‍💻 Admin Panel
Navigate to:
http://localhost/online-grocery-store/admin/dashboard.html

Login using default admin credentials (from database or hardcoded).

📌 Optional Enhancements
Enable responsive design with Bootstrap

Add JavaScript form validation

Integrate payment gateway like Razorpay

Host it on GitHub Pages + backend on Render or any PHP hosting

