<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Student Dashboard</title>
</head>
<body>
    <?php
    session_start();
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
        header('Location: login.php');
        exit;
    }
    $username = htmlspecialchars($_SESSION['user']);
    ?>
    <div class="dashboard-page">
        <div class="dashboard-card">
            <div class="dashboard-hero">
                <h1>Student Dashboard</h1>
                <p class="dashboard-summary">Hello <?php echo $username; ?>. Check your assignments, exam status, and review your latest scores.</p>
                <div class="dashboard-list">
                    <div class="dashboard-item">Next Exam: Mathematics</div>
                    <div class="dashboard-item">Completed Tests: 3</div>
                    <div class="dashboard-item">Current Grade: B+</div>
                </div>
            </div>
            <div class="dashboard-sidebar">
                <h2>Student Tools</h2>
                <div>
                    <p>• Review study material</p>
                    <p>• See exam schedule</p>
                    <p>• Contact instructor</p>
                </div>
                <a  href="options.php"><button type="button" >Choose Exam</button></a>
                <a href="logout.php"><button type="button">Logout</button></a>
            </div>
        </div>
    </div>
</body>
</html>
