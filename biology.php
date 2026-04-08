<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit();
}

require_once 'db_connect.php';
$subject = 'Biology';
$questions = fetchQuestions($conn, $subject, 5);
$durationMinutes = getExamDuration($conn, $subject);
$durationSeconds = $durationMinutes * 60;
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Biology MCQ Exam</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .timer-container {
            background-color: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            border: 2px solid #007bff;
        }
        .timer-display {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
            font-family: monospace;
        }
        .timer-label {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        .timer-display.warning {
            color: #ff9800;
        }
        .timer-display.danger {
            color: #f44336;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Biology MCQ Exam</h1>

    <div class="timer-container">
        <div class="timer-label">Time Remaining:</div>
        <div class="timer-display" id="timerDisplay">00:00:00</div>
    </div>

    <form action="result.php" method="post" id="examForm">
        <input type="hidden" name="subject" value="<?php echo htmlspecialchars($subject); ?>">

        <?php if (!empty($questions)): ?>
            <?php foreach ($questions as $index => $question): ?>
                <input type="hidden" name="question_ids[]" value="<?php echo $question['id']; ?>">
                <div class="question">
                    <p><?php echo ($index + 1) . '. ' . htmlspecialchars($question['question']); ?></p>
                    <input type="radio" name="q<?php echo $index + 1; ?>" value="a"> <?php echo htmlspecialchars($question['option_a']); ?><br>
                    <input type="radio" name="q<?php echo $index + 1; ?>" value="b"> <?php echo htmlspecialchars($question['option_b']); ?><br>
                    <input type="radio" name="q<?php echo $index + 1; ?>" value="c"> <?php echo htmlspecialchars($question['option_c']); ?><br>
                    <input type="radio" name="q<?php echo $index + 1; ?>" value="d"> <?php echo htmlspecialchars($question['option_d']); ?><br>
                </div>
            <?php endforeach; ?>
            <button type="submit">Submit Exam</button>
        <?php else: ?>
            <div class="question">
                <p>No Biology questions are available. Please ask the admin to add questions first.</p>
            </div>
        <?php endif; ?>
    </form>
</div>

<script>
    const durationSeconds = <?php echo $durationSeconds; ?>;
    let remainingSeconds = durationSeconds;

    function formatTime(seconds) {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        return String(hours).padStart(2, '0') + ':' +
               String(minutes).padStart(2, '0') + ':' +
               String(secs).padStart(2, '0');
    }

    function updateTimer() {
        const timerDisplay = document.getElementById('timerDisplay');
        timerDisplay.textContent = formatTime(remainingSeconds);

        if (remainingSeconds <= 60) {
            timerDisplay.classList.add('danger');
            timerDisplay.classList.remove('warning');
        } else if (remainingSeconds <= 300) {
            timerDisplay.classList.add('warning');
            timerDisplay.classList.remove('danger');
        } else {
            timerDisplay.classList.remove('warning', 'danger');
        }

        if (remainingSeconds <= 0) {
            alert('Time is up! Your exam will be submitted automatically.');
            document.getElementById('examForm').submit();
        }

        remainingSeconds--;
    }

    // Update timer every second
    setInterval(updateTimer, 1000);
    // Initial display
    updateTimer();
</script>

</body>
</html>
