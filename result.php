<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit();
}

require_once 'db_connect.php';

$score = 0;
$subject = $_POST['subject'] ?? 'Unknown';
$questionIds = $_POST['question_ids'] ?? [];

if (!is_array($questionIds)) {
    $questionIds = [];
}

$questionIds = array_values(array_filter(array_map('intval', $questionIds), function ($id) {
    return $id > 0;
}));

$correctOptions = fetchCorrectOptions($conn, $subject, $questionIds);
$totalQuestions = count($questionIds);

foreach ($questionIds as $index => $questionId) {
    $answerKey = 'q' . ($index + 1);
    $selected = $_POST[$answerKey] ?? '';
    if ($selected !== '' && isset($correctOptions[$questionId]) && $selected === $correctOptions[$questionId]) {
        $score++;
    }
}

$username = $_SESSION['user'];
$stmt = $conn->prepare("INSERT INTO results (username, subject, score, total_questions) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssid", $username, $subject, $score, $totalQuestions);
$stmt->execute();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Result</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Your Result</h1>
    <h2>Subject: <?php echo htmlspecialchars($subject); ?></h2>
    <h2>You scored: <?php echo $score; ?> / 5</h2>

    <?php
    if ($score == 5) {
        echo "<p>Excellent! 🎉</p>";
    } elseif ($score >= 3) {
        echo "<p>Good Job 👍</p>";
    } else {
        echo "<p>Try Again 😢</p>";
    }
    ?>

    <a href="login.php"><button>Back to Home</button></a>
</div>

</body>
</html>