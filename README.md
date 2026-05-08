# 📚 Library Management System

A full-stack PHP + MySQL web application to manage book inventory, student borrowing activity, returns, and admin access control. Built as part of an academic portfolio, this project demonstrates real-world CRUD operations, secure login, and session-based workflows in a self-contained library system.

---

## 🔧 Features

- **Full CRUD Management**: 
  - Manage **Books** (Add, View, Edit, Delete).
  - Manage **Students** (Add, View, Edit, Delete).
- **Circulation System**: 
  - Issue books with real-time student lookup.
  - Return books and update inventory automatically.
- **Reporting & Logs**:
  - View detailed transaction history.
  - Export logs to CSV for reporting.
- **Modern UI/UX**:
  - Responsive design using Vanilla CSS.
  - Dark Mode support with persistence.
  - Tabbed dashboard for quick data access.

## 🛠️ Tech Stack

- **Backend**: PHP 8.x (Native)
- **Database**: MySQL / MariaDB
- **Frontend**: HTML5, Vanilla CSS, Vanilla JavaScript
- **Icons**: Emoji-based for lightweight performance

## 📦 Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/yuumuu/library-management-system.git
   ```
2. **Database Setup**:
   - Create a database named `library_system` (or as configured in `config/db_config.php`).
   - Import the SQL schema from `sql/database.sql`.
3. **Configuration**:
   - Update `config/db_config.php` with your local database credentials.
4. **Run**:
   - Move the project to your local server directory (e.g., `htdocs` for XAMPP).
   - Access via `http://localhost/library-management-system`.

## 📝 Credentials

- **Username**: `admin`
- **Password**: `admin123`