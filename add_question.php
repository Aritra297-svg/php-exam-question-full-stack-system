<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

require_once 'db_connect.php';

$subjects = ['Physics', 'Mathematics', 'Biology', 'Chemistry', 'English'];
$message = '';
$error = '';
$formData = [
    'subject' => '',
    'question' => '',
    'option_a' => '',
    'option_b' => '',
    'option_c' => '',
    'option_d' => '',
    'correct_option' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData['subject'] = $_POST['subject'] ?? '';
    $formData['question'] = trim($_POST['question'] ?? '');
    $formData['option_a'] = trim($_POST['option_a'] ?? '');
    $formData['option_b'] = trim($_POST['option_b'] ?? '');
    $formData['option_c'] = trim($_POST['option_c'] ?? '');
    $formData['option_d'] = trim($_POST['option_d'] ?? '');
    $formData['correct_option'] = strtolower(trim($_POST['correct_option'] ?? ''));

    if (!in_array($formData['subject'], $subjects, true)) {
        $error = 'Please choose a valid subject.';
    } elseif ($formData['question'] === '') {
        $error = 'The question text cannot be empty.';
    } elseif ($formData['option_a'] === '' || $formData['option_b'] === '' || $formData['option_c'] === '' || $formData['option_d'] === '') {
        $error = 'All four answer choices are required.';
    } elseif (!in_array($formData['correct_option'], ['a', 'b', 'c', 'd'], true)) {
        $error = 'Please select the correct option.';
    } else {
        $tableName = getQuestionTableName($formData['subject']);
        if (!$tableName) {
            $error = 'Unable to determine the question table for that subject.';
        } else {
            $stmt = $conn->prepare("INSERT INTO {$tableName} (question, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                'ssssss',
                $formData['question'],
                $formData['option_a'],
                $formData['option_b'],
                $formData['option_c'],
                $formData['option_d'],
                $formData['correct_option']
            );

            if ($stmt->execute()) {
                $message = 'Question added successfully.';
                $formData = [
                    'subject' => '',
                    'question' => '',
                    'option_a' => '',
                    'option_b' => '',
                    'option_c' => '',
                    'option_d' => '',
                    'correct_option' => ''
                ];
            } else {
                $error = 'Unable to save the question. Please try again.';
            }

            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Question</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Add MCQ Question</h1>
    <?php if ($message): ?>
        <div class="alert success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" action="add_question.php">
        <label for="subject">Subject</label>
        <select id="subject" name="subject" required>
            <option value="">Choose subject:</option>
            <?php foreach ($subjects as $subjectOption): ?>
                <option value="<?php echo htmlspecialchars($subjectOption); ?>" <?php echo $formData['subject'] === $subjectOption ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($subjectOption); ?>
                </option>
            <?php endforeach; ?>
        </select>
<br>        <label for="question">Question</label>
        <textarea id="question" name="question" rows="4"  required><?php echo htmlspecialchars($formData['question']); ?></textarea>
        <br>

        <label for="option_a">Option A:</label>
        <input id="option_a" type="text" name="option_a" value="<?php echo htmlspecialchars($formData['option_a']); ?>" required>
  <br>
        <label for="option_b">Option B:</label>
        <input id="option_b" type="text" name="option_b" value="<?php echo htmlspecialchars($formData['option_b']); ?>" required>
  <br>
        <label for="option_c">Option C:</label>
        <input id="option_c" type="text" name="option_c" value="<?php echo htmlspecialchars($formData['option_c']); ?>" required>
  <br>
        <label for="option_d">Option D:</label>
        <input id="option_d" type="text" name="option_d" value="<?php echo htmlspecialchars($formData['option_d']); ?>" required>
  <br>
        <label for="correct_option">Correct Option</label>
        <select id="correct_option" name="correct_option" required>
            <option value="">Choose correct answer</option>
            <option value="a" <?php echo $formData['correct_option'] === 'a' ? 'selected' : ''; ?>>A</option>
            <option value="b" <?php echo $formData['correct_option'] === 'b' ? 'selected' : ''; ?>>B</option>
            <option value="c" <?php echo $formData['correct_option'] === 'c' ? 'selected' : ''; ?>>C</option>
            <option value="d" <?php echo $formData['correct_option'] === 'd' ? 'selected' : ''; ?>>D</option>
        </select>

        <button type="submit">Save Question</button>
    </form>

    <a href="admin_dashboard.php"><button type="button">Back to Admin Dashboard</button></a>
</div>

</body>
</html>
