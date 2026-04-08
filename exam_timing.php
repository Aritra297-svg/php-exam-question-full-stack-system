<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

require_once 'db_connect.php';

$subjects = ['Physics', 'Mathematics', 'Chemistry', 'Biology', 'English'];
$timings = [];
$message = '';
$error = '';

// Fetch current timings
foreach ($subjects as $subject) {
    $timings[$subject] = getExamDuration($conn, $subject);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated = false;
    
    foreach ($subjects as $subject) {
        $duration = (int)($_POST["duration_$subject"] ?? 30);
        
        if ($duration < 1 || $duration > 180) {
            $error = "Duration must be between 1 and 180 minutes.";
            break;
        }
        
        if (updateExamDuration($conn, $subject, $duration)) {
            $timings[$subject] = $duration;
            $updated = true;
        }
    }
    
    if ($updated && !$error) {
        $message = "Exam timings updated successfully.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Timing Settings</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .timing-form {
            max-width: 600px;
            margin: 20px auto;
        }
        .timing-item {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .timing-item label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .timing-item input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .timing-display {
            margin-top: 5px;
            font-size: 14px;
            color: #666;
        }
        .alert {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .alert.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px 10px 0;
        }
        button:hover {
            background-color: #0056b3;
        }
        .back-link {
            display: inline-block;
            margin: 10px 5px;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .back-link:hover {
            background-color: #545b62;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Exam Timing Configuration</h1>
    <p>Set the duration (in minutes) for each subject exam:</p>
    
    <?php if ($message): ?>
        <div class="alert success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" action="exam_timing.php" class="timing-form">
        <?php foreach ($subjects as $subject): ?>
            <div class="timing-item">
                <label for="duration_<?php echo htmlspecialchars($subject); ?>">
                    <?php echo htmlspecialchars($subject); ?> Exam Duration
                </label>
                <input 
                    type="number" 
                    id="duration_<?php echo htmlspecialchars($subject); ?>" 
                    name="duration_<?php echo htmlspecialchars($subject); ?>" 
                    value="<?php echo $timings[$subject]; ?>"
                    min="1"
                    max="180"
                    required
                >
                <div class="timing-display">
                    Current: <?php echo $timings[$subject]; ?> minutes (<?php echo intval($timings[$subject] / 60); ?>h <?php echo $timings[$subject] % 60; ?>m)
                </div>
            </div>
        <?php endforeach; ?>

        <button type="submit">Save Exam Timings</button>
    </form>

    <div>
        <a href="admin_dashboard.php" class="back-link">Back to Admin Dashboard</a>
    </div>
</div>

</body>
</html>
