# Mosque Donation & Expense Management System

A lightweight **web-based management system** designed for mosques to easily track **donations, monthly contributions, and expenses**.  
It provides a clear dashboard with charts and reports so administrators can maintain transparency and manage funds effectively.  

---

## 🚀 Features

### 🏠 Dashboard
- **Fund Balance Chart** – Pie chart showing previous fund balance vs. this month’s contributions.  
- **Monthly Contribution Chart** – 12-month bar chart displaying total contributions for each month of the current year.  
- **Expense Chart** – Pie chart showing past expenses vs. current month’s expenses.  
- **Monthly Expense Chart** – 12-month bar chart displaying total expenses for each month of the current year.  

### 👤 Profile Management
- **Add Profile** – Register people who donate a fixed monthly amount.  
- **Manage Profile** – View, update, and manage donor profiles and their monthly contributions.  

### 💰 Contributions
- **Monthly Support** – Track recurring donations from registered donors.  
- **Extended Support** – Record one-time or additional contributions beyond fixed monthly support.  

### 📑 Expense Management
- **Expense Log** – Add and manage mosque expenses with proper categorization.  

### 📊 Reports
- **Contribution Report** – Generate detailed reports of contributions (monthly & extended).  
- **Expense Report** – Generate detailed reports of all expenses.  

---

## 🛠️ Tech Stack
- **Frontend:** HTML, CSS, Bootstrap  
- **Backend:** PHP  
- **Database:** MySQL (via phpMyAdmin)  

---

## 🚀 Setup Instructions

### ✅ Requirements

- PHP 7.4+
- MySQL
- Apache Server (e.g., XAMPP, WAMP, LAMP)
- phpMyAdmin (for DB import)

### 📥 1. Clone the Project

```bash
git clone https://github.com/SyedRafid/Mosque-Donation-Expense-Management-System.git
cd Mosque-Donation-Expense-Management-System
```

### 📂 2. Importing the Database using phpMyAdmin

This project uses a MySQL database named **`mosque`**. To set it up locally, follow these steps:

1. **Create the Database:**

   - Open **phpMyAdmin** in your browser (e.g., http://localhost/phpmyadmin).
   - Click on the **Databases** tab.
   - In the "Create database" field, enter the name:
     ```
     mosque
     ```
   - Choose the collation (e.g., `utf8mb4_general_ci`) and click **Create**.

2. **Import the SQL File:**

   - Click on the newly created `mosque` database in phpMyAdmin.
   - Go to the **Import** tab.
   - Click **Choose File** and browse to the project folder's `db` directory.
   - Select the SQL file (e.g., `mosque.sql`).
   - Click **Go** at the bottom to start the import.
   - Wait for the success message confirming the import.

### 🗝️ Admin Login (Default)

- **Email:** syed.shuvon@gmail.com
- **Password:** syed.shuvon@gmail.com


> ⚠️ This is the default account. Please log in and change the password immediately after setup for security.

---


## 🙏 Thank You!

Thank you for checking out the **Mosque Donation & Expense Management System**!  
If you find this project useful, please consider giving it a ⭐️ on GitHub.  

Feel free to open issues or submit pull requests — feedback and contributions are always welcome!  

Happy coding — and best of luck managing your **mosque donations, contributions, and expenses** with ease and transparency! 🕌💰📊
