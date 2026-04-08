<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "exam_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];

    if ($user_type === 'admin') {
        // Check for hardcoded admin credentials
        if ($username === 'admin' && $password === 'admin123') {
            $_SESSION['user'] = $username;
            $_SESSION['role'] = 'admin';
            header('Location: admin_dashboard.php');
            exit();
        }
    } elseif ($user_type === 'student') {
        // Check students table
        $stmt = $conn->prepare("SELECT id, name, password FROM students WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['user'] = $username;
                $_SESSION['role'] = 'student';
                $_SESSION['name'] = $row['name'];
                header('Location: student_dashboard.php');
                exit();
            }
        }
    }

    // If authentication fails
    header('Location: login.php?error=1');
    exit();
}

$conn->close();
?>