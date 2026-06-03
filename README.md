# 💼 Online Job Portal System

A full-stack web-based **Job Portal Application** developed using **PHP and MySQL**, designed to connect job seekers with employers through a structured and efficient platform.

This project demonstrates backend development, database design, and CRUD operations in a real-world recruitment system.

---

## 🚀 Live Preview

> This is a local XAMPP-based project  
> (Can be deployed to hosting or cloud server)


http://localhost/online_job_portal/


---

## 📌 Key Features

### 👤 Job Seekers
- Secure user registration and login system
- Browse and search available job listings
- Apply for jobs with resume upload
- View application status (Pending / Reviewed / Accepted / Rejected)
- Submit feedback and company ratings

### 🏢 Companies
- Create and manage company profiles
- Post new job vacancies
- View and manage job applications
- Review candidate details

### 🛠️ System Features
- Role-based access (User / Company / Admin)
- Relational database with foreign key constraints
- Feedback and rating system
- Application tracking system
- Secure session-based authentication

---

## 🧰 Tech Stack

| Layer        | Technology |
|--------------|------------|
| Frontend     | HTML5, CSS3, Bootstrap |
| Backend      | PHP (Core PHP) |
| Database     | MySQL (MariaDB via XAMPP) |
| Server       | Apache (XAMPP Local Server) |

---



---

## 🗄️ Database Design

The system consists of relational tables:

- `users` → Stores job seekers and admins
- `companies` → Company profiles and details
- `jobs` → Job postings
- `applications` → Job applications with status tracking
- `feedbacks` → User reviews and ratings

### Relationships
- Users → Applications (1:M)
- Companies → Jobs (1:M)
- Jobs → Applications (1:M)
- Users → Feedbacks (1:M)

---

## ⚙️ Installation Guide

### 1. Clone or Download Project
Place the project inside:

C:\xampp\htdocs\


---

### 2. Start Server
Open XAMPP Control Panel:

- Start **Apache**
- Start **MySQL**

---

### 3. Create Database

Open:

http://localhost/phpmyadmin


### Create database:

online_job_portal

### Import:

database.sql


---

### 4. Run Application


http://localhost/online_job_portal/


or


http://localhost/online_job_portal/src/Home.php


---

## 🔐 Sample Credentials

User:
Email: john.doe@example.com

Password: hashedpassword1

Admin:
Email: jane.smith@example.com

Password: hashedpassword2

---

## 📊 System Highlights
✔ Secure authentication system

✔ Foreign key relational database design

✔ Dynamic job application workflow

✔ Admin, user, and company role separation

✔ Clean modular PHP structure

✔ Scalable database design

---

## ⚠️ Known Issues / Notes
Requires XAMPP (Apache + MySQL)

Ensure MySQL port is correctly configured (default: 3306 or 3307)

phpMyAdmin login may require root reset in some setups

Database must be imported before running the system

---

## 👨‍💻 Developer
### 2024-MTR-Y1S2-WD-G04

---

## 📜 License

## This project is developed for educational purposes only.
