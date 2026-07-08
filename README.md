# 🌍 NGO Management System

> A web-based NGO Management System developed using **Core PHP**, **MySQL**, **HTML**, **CSS**, and **JavaScript** to simplify NGO operations including donor management, volunteer coordination, proposal management, donations, and reporting.

> **Developed during my internship at SpaceECE India Foundation.**

---

## 📌 Project Overview

The NGO Management System is designed to digitize and streamline the daily operations of non-governmental organizations by providing an integrated platform for managing donors, volunteers, projects, proposals, and financial records.

The application aims to improve transparency, efficiency, and data management while reducing manual administrative work.

---

## ✨ Features

### 🔐 Authentication
- Admin Login
- Volunteer Login
- Secure Session Management
- Logout Functionality

### 👥 Donor Management
- Register Donors
- Manage Donor Profiles
- View Donation History
- Donor Dashboard

### 💰 Donation Management
- Add Donations
- Update Donation Records
- Donation Reports
- Fundraising Goals

### 🙋 Volunteer Management
- Volunteer Registration
- Volunteer Dashboard
- Store Applications

### 📄 Proposal Management
- Proposal Generator
- Proposal Records

### 📊 Reports
- Donation Reports
- Financial Summary
- NGO Statistics

### 📞 Other Modules
- About Page
- Contact Page
- Survey Module
- Feedback System

---

# 🛠 Technology Stack

- PHP (Core PHP)
- MySQL
- HTML5
- CSS3
- JavaScript
- Apache (XAMPP)

---

# 📂 Project Structure

```
NGO_Management_System
│
├── database/
│   ├── db_connect.php
│   └── ngo_db.sql
│
├── donation_system/
│
├── images/
│
├── index.php
├── admin.php
├── login.php
├── logout.php
├── volunteer.php
├── volunteer_dashboard.php
├── volunteer_signin.php
├── about.php
├── contact.php
├── survey.php
├── store_application.php
│
├── README.md
└── LICENSE
```

---

# 🚀 Installation

## Clone Repository

```bash
git clone https://github.com/NUPUR32/NGO_Management_System.git
```

## Move Project

Copy the folder into

```
xampp/htdocs/
```

## Start XAMPP

Start

- Apache
- MySQL

## Database Setup

Open

```
http://localhost/phpmyadmin
```

Create a database

```
ngo_management
```

Import

```
database/ngo_db.sql
```

## Configure Database

Update

```
database/db_connect.php
```

```php
$host="localhost";
$user="root";
$password="";
$database="ngo_management";
```

## Run Project

```
http://localhost/NGO_Management_System/
```

---

# 📈 Future Improvements

- Online Payment Gateway
- Email Notifications
- Beneficiary Management
- Event Management
- REST API
- Responsive UI
- Analytics Dashboard

---

# 🤝 Contributing

Contributions are welcome.

1. Fork the repository.
2. Create a feature branch.
3. Commit your changes.
4. Push your branch.
5. Open a Pull Request.

---

# 🙏 Acknowledgement

This project was developed during my internship at **SpaceECE India Foundation** as part of building a digital solution for NGO management and donor engagement.

---

# 👩‍💻 Author

**Nupur Joon**

B.Tech Computer Science Engineering (AI & ML)

University of Petroleum and Energy Studies (UPES)

GitHub: https://github.com/NUPUR32

---

## ⭐ Star this repository if you found it useful!
