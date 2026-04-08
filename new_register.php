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

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Check if username already exists in students or admins table
    $check_sql = "SELECT username FROM students WHERE username='$username' UNION SELECT username FROM admins WHERE username='$username'";
    $result = $conn->query($check_sql);
    
    if ($result->num_rows > 0) {
        $message = "Username already exists. Please choose a different username.";
    } else {
        $sql = "INSERT INTO students (name, username, email, phone, password) VALUES ('$name', '$username', '$email', '$phone', '$password')";
        
        if ($conn->query($sql) === TRUE) {
            header("Location: login.php");
            exit();
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Registration</title>
    <style>
        body { font-family: Arial; max-width: 400px; margin: 50px auto; background: linear-gradient(135deg, #f5f7fa, #c3cfe2); }
        .form-group { margin: 15px 0; border: 1px solid #ccc; padding: 15px; border-radius: 8px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; cursor: pointer; }
        .message { padding: 10px; margin-bottom: 15px; background-color: #d4edda; color: #155724; }
        .container { background-color: #b6b8c1; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .h2{ text-align: center; color: #333; }
    </style>
</head>
<body>
<div class="container">
<h2 class="h2">Student Registration</h2>

<?php if ($message): ?>
    <div class="message"><?php echo $message; ?></div>
<?php endif; ?>

<form class="form1" method="POST">
    <div class="form-group">
        <label>Name:</label>
        <input placeholder="Enter your name" type="text" name="name" required>
    </div>

    <div class="form-group">
        <label>Username:</label>
        <input placeholder="Enter your username" type="text" name="username" required>
    </div>

    <div class="form-group">
        <label>Email:</label>
        <input placeholder="Enter your email" type="email" name="email" required>
    </div>

    <div class="form-group">
        <label>Phone Number:</label>
        <input placeholder="Enter your phone number" type="text" name="phone" required>
    </div>

    <div class="form-group">
        <label>Password:</label>
        <input placeholder="Enter your password" type="password" name="password" required>
    </div>

    <button type="submit">Register</button>
</form>
</div>
</body>
</html>

<?php $conn->close(); ?>