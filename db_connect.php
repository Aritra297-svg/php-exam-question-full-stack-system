<?php
$host = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'exam_db';

$conn = new mysqli($host, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

function getQuestionTableName($subject)
{
    $map = [
        'Physics' => 'physics_questions',
        'Mathematics' => 'mathematics_questions',
        'Chemistry' => 'chemistry_questions',
        'Biology' => 'biology_questions',
        'English' => 'english_questions'
    ];

    return $map[$subject] ?? null;
}

function fetchQuestions($conn, $subject, $limit = 5)
{
    $table = getQuestionTableName($subject);
    if (!$table) {
        return [];
    }

    $stmt = $conn->prepare("SELECT id, question, option_a, option_b, option_c, option_d FROM {$table} LIMIT ?");
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $questions = [];
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }

    $stmt->close();
    return $questions;
}

function fetchCorrectOptions($conn, $subject, array $questionIds)
{
    $table = getQuestionTableName($subject);
    if (!$table) {
        return [];
    }

    $questionIds = array_values(array_filter(array_map('intval', $questionIds), function ($id) {
        return $id > 0;
    }));

    if (empty($questionIds)) {
        return [];
    }

    $in = implode(',', $questionIds);
    $sql = "SELECT id, correct_option FROM {$table} WHERE id IN ({$in})";
    $result = $conn->query($sql);

    $correctOptions = [];
    while ($row = $result->fetch_assoc()) {
        $correctOptions[(int)$row['id']] = $row['correct_option'];
    }

    return $correctOptions;
}

function getExamDuration($conn, $subject)
{
    $stmt = $conn->prepare("SELECT duration_minutes FROM exam_settings WHERE subject = ?");
    $stmt->bind_param('s', $subject);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row ? $row['duration_minutes'] : 30;
}

function updateExamDuration($conn, $subject, $duration)
{
    $stmt = $conn->prepare("UPDATE exam_settings SET duration_minutes = ? WHERE subject = ?");
    $stmt->bind_param('is', $duration, $subject);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}
