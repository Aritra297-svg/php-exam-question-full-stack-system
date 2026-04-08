<?php
$error = isset($_GET['error']) ? 'Invalid credentials. Please try again.' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Exam Q Login</title>
</head>
<body>
    <div class="login-page">
        <div class="login-container">
            <div class="login-brand">
                <h1>Exam Q Access</h1>
                <p>Choose your role and sign in to continue.</p>
            </div>
            <form class="login-form" action="access.php" method="post">
                <label for="username" placeholder="Enter your username">Username</label>
                <input type="text" id="username" name="username" required>

                <label for="password" placeholder="Enter your password">Password</label>
                <input type="password" id="password" name="password" required>

                <div class="radio-group">
                    <label><input type="radio" name="user_type" value="admin" required> Admin</label>
                    <label><input type="radio" name="user_type" value="student" required> Student</label>
                </div>

                <input class="button-primary" type="submit" value="Login">
                <div class="register-row">
                    <a class="button-secondary" href="new_register.php">New Register</a>
                </div>
            </form>
            <?php if ($error): ?>
                <div class="alert"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>