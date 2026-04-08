# Exam Q

A simple PHP-based online MCQ exam system for XAMPP/WAMP.

## Project Overview

`Exam_q` is a lightweight PHP application for managing multiple-choice exams with distinct admin and student experiences. Admins can add subjects and questions, while students can register, log in, select subjects, and complete timed exams.

## Features

- Admin and student login flows
- Student registration and access controls
- Add and manage MCQ questions across subjects
- Quiz subjects: Physics, Mathematics, Chemistry, Biology, English
- Exam timing settings stored in the database
- Result scoring and student result storage
- Basic student and admin dashboard pages

## Project Structure

- `index.html` — Public landing/sample exam page
- `login.php` — Login page for students and admins
- `access.php` — Authentication handler
- `new_register.php` — Student registration page
- `student_register.html` — Static registration form
- `admin_dashboard.php` — Admin metrics dashboard
- `student_dashboard.php` — Student dashboard
- `options.php` — Exam subject selection page
- `subject_option.php` — Subject-based exam routing
- `add_question.php` — Add new exam questions
- `result.php` — Display exam results
- `exam_timing.php` — Manage exam duration settings
- `view_students.php` / `view_students_new.php` — Student listings
- `logout.php` — Logout script
- `db_connect.php` — Database connection and helper functions
- `style.css` — Shared UI styling
- `database_setup.sql` — Database schema and initial data

## Setup Instructions

1. Install XAMPP or WAMP and start Apache and MySQL.
2. Place the `Exam_q` folder inside your web root (`C:\xampp\htdocs\Exam_q`).
3. Open phpMyAdmin or MySQL CLI and import `database_setup.sql`.
4. Confirm the database name matches `exam_db`.
5. Update database credentials in `db_connect.php` if necessary.
6. Open your browser at `http://localhost/Exam_q/login.php`.

## Default Admin Credentials

- Username: `admin`
- Password: `admin123`

## Usage

- Admins can log in, add questions, and view dashboard metrics.
- Students can register, log in, choose a subject, and take an exam.
- Exam scores are stored in the database and available via the result flow.

## Notes

- This app is intended for learning and demonstration.
- For production use, strengthen security, password hashing, validation, and session handling.
