<?php
session_start();

$selectedSubject = $_GET['subject'] ?? '';

$validSubjects = ['Physics', 'Mathematics', 'Biology', 'Chemistry', 'English'];

if (in_array($selectedSubject, $validSubjects)) {
    $subjectFile = strtolower($selectedSubject) . '.php';
    header("Location: $subjectFile?subject=" . urlencode($selectedSubject));
    exit();
} else {
    $error = "Invalid subject selected. Please go back and choose a valid subject.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subject Options</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <?php if (isset($error)): ?>
            <h1>Error</h1>
            <p><?php echo htmlspecialchars($error); ?></p>
            <a href="options.php">Back to Subject Selection</a>
        <?php endif; ?>
    </div>
</body>
</html>