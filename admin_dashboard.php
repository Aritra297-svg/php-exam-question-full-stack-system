<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$username = htmlspecialchars($_SESSION['user'] ?? 'Admin');
$host = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'exam_db';

$studentCount = 0;
$examCount = 0;
$pendingCount = 0;
$feedbackCount = 0;
$dbError = '';

$conn = new mysqli($host, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    $dbError = 'Unable to load dashboard metrics at this time.';
} else {
    $result = $conn->query("SELECT COUNT(*) AS total FROM students");
    if ($result) {
        $studentCount = (int) ($result->fetch_assoc()['total'] ?? 0);
        $result->free();
    }

    $result = $conn->query("SELECT COUNT(*) AS total FROM results");
    if ($result) {
        $examCount = (int) ($result->fetch_assoc()['total'] ?? 0);
        $result->free();
    }

    // Note: pendingCount query was duplicated and causing error, removed for now
    $pendingCount = 0;

    $tableExists = $conn->query("SHOW TABLES LIKE 'feedback'");
    if ($tableExists && $tableExists->num_rows) {
        $result = $conn->query("SELECT COUNT(*) AS total FROM feedback");
        if ($result) {
            $feedbackCount = (int) ($result->fetch_assoc()['total'] ?? 0);
            $result->free();
        }
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Admin Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: 'Inter', system-ui, sans-serif;
            background: radial-gradient(circle at top left, rgba(48, 112, 236, 0.18), transparent 28%),
                        linear-gradient(180deg, #0a1223 0%, #101d40 55%, #131c32 100%);
            color: #f5f8ff;
        }

        .dashboard-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 24px;
        }

        .dashboard-card {
            width: min(1180px, 100%);
            background: rgba(17, 28, 68, 0.94);
            box-shadow: 0 32px 80px rgba(0, 0, 0, 0.32);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 28px;
            overflow: hidden;
            backdrop-filter: blur(12px);
        }

        .dashboard-topbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 18px;
            justify-content: space-between;
            padding: 30px 36px;
            background: linear-gradient(135deg, rgba(18, 40, 94, 0.96), rgba(24, 54, 112, 0.92));
        }

        .brand-panel {
            display: grid;
            gap: 6px;
        }

        .brand-title {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: -0.04em;
        }

        .brand-title::before {
            content: '';
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: radial-gradient(circle, #76d6ff 0%, #2674ff 100%);
            box-shadow: 0 0 20px rgba(38, 116, 255, 0.65);
        }

        .brand-tagline {
            color: #afc7ff;
            font-size: 0.95rem;
            max-width: 420px;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .user-chip {
            padding: 10px 16px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            color: #e6f0ff;
            font-size: 0.95rem;
            letter-spacing: 0.01em;
        }

        .logout-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 22px;
            border-radius: 999px;
            color: #fff;
            background: linear-gradient(135deg, #41c9ff 0%, #1f8cff 100%);
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .logout-link:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 40px rgba(15, 107, 217, 0.28);
        }

        .dashboard-hero {
            display: grid;
            grid-template-columns: 1.6fr 1fr;
            gap: 30px;
            padding: 40px 36px 14px;
        }

        .hero-copy {
            display: grid;
            gap: 20px;
        }

        .eyebrow {
            margin: 0;
            color: #7fb4ff;
            text-transform: uppercase;
            letter-spacing: 0.24em;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .dashboard-hero h1 {
            margin: 0;
            font-size: clamp(2.6rem, 4vw, 4.4rem);
            line-height: 1.02;
            letter-spacing: -0.06em;
            max-width: 720px;
            color: #e3f0ff;
        }

        .dashboard-summary {
            margin: 0;
            max-width: 660px;
            color: #cad8ff;
            font-size: 1rem;
            line-height: 1.8;
        }

        .hero-quick {
            display: flex;
            align-items: center;
        }

        .hero-card {
            width: 100%;
            padding: 28px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.02);
        }

        .hero-card h2 {
            margin: 0 0 14px;
            font-size: 1.4rem;
            color: #e3f0ff;
        }

        .hero-card p {
            margin: 0 0 22px;
            color: #c3d4ff;
            line-height: 1.75;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .action-btn,
        .action-card {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            padding: 0 24px;
            border-radius: 999px;
            font-weight: 700;
            text-decoration: none;
            color: #fff;
            background: linear-gradient(135deg, #4f8cff, #7fb4ff);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .action-btn:hover,
        .action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 30px rgba(34, 109, 237, 0.22);
        }

        .action-btn.secondary {
            background: rgba(255, 255, 255, 0.08);
            color: #d9e6ff;
            border: 1px solid rgba(255, 255, 255, 0.14);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 22px;
            padding: 0 36px 36px;
        }

        .stat-card {
            padding: 28px;
            border-radius: 24px;
            background: linear-gradient(180deg, rgba(33, 48, 98, 0.9), rgba(18, 28, 65, 0.9));
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 24px 40px rgba(6, 14, 37, 0.18);
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            border-color: rgba(111, 189, 255, 0.22);
        }

        .stat-label {
            margin: 0 0 12px;
            color: #8eb8ff;
            letter-spacing: 0.08em;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .stat-card h2 {
            margin: 0;
            font-size: 3rem;
            color: #fffffe;
            line-height: 1;
        }

        .stat-helper {
            margin: 12px 0 0;
            color: #b3c7ff;
            font-size: 0.95rem;
            line-height: 1.7;
        }

        .dashboard-actions {
            padding: 0 36px 36px;
        }

        .dashboard-actions h2 {
            margin: 0 0 18px;
            font-size: 1.9rem;
            color: #f5f9ff;
        }

        .action-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 18px;
        }

        .action-card {
            min-height: 114px;
            padding: 24px 20px;
            border-radius: 26px;
            display: grid;
            place-items: center;
            text-align: center;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .action-card:hover {
            background: rgba(68, 118, 241, 0.18);
        }

        .alert {
            margin-top: 18px;
            padding: 16px 20px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #ffde8a;
        }

        @media (max-width: 960px) {
            .dashboard-hero {
                grid-template-columns: 1fr;
            }
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .action-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .dashboard-page {
                padding: 18px 14px;
            }
            .dashboard-topbar,
            .dashboard-hero,
            .stats-grid,
            .dashboard-actions {
                padding-left: 18px;
                padding-right: 18px;
            }
            .stats-grid,
            .action-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-page">
        <div class="dashboard-card">
            <header class="dashboard-topbar">
                <div class="brand-panel">
                    <span class="brand-title">ExamQ Admin</span>
                    <span class="brand-tagline">Overview & management</span>
                </div>
                <div class="topbar-actions">
                    <span class="user-chip">Signed in as <?php echo $username; ?></span>
                    <a class="logout-link" href="logout.php">Logout</a>
                </div>
            </header>

            <section class="dashboard-hero">
                <div class="hero-copy">
                    <p class="eyebrow">Admin dashboard</p>
                    <h1>Manage students, exams, and results from one place.</h1>
                    <p class="dashboard-summary">Use the quick action cards below to access student records, review exam results, and keep your system settings organized.</p>
                    <?php if ($dbError): ?>
                        <div class="alert"><?php echo htmlspecialchars($dbError); ?></div>
                    <?php endif; ?>
                </div>
                <div class="hero-quick">
                    <div class="hero-card">
                        <h2>Ready to act</h2>
                        <p>Quickly navigate to the sections you use most often and keep your admin workflow moving.</p>
                        <div class="hero-actions">
                            <a class="action-btn" href="view_students.php">View Students</a>
                            <a class="action-btn secondary" href="options.php">Student Options</a>
                        </div>
                    </div>
                </div>
            </section>

            <section class="stats-grid">
                <article class="stat-card">
                    <p class="stat-label">Registered students</p>
                    <h2><?php echo $studentCount; ?></h2>
                    <p class="stat-helper">Total students in the system.</p>
                </article>
                <article class="stat-card">
                    <p class="stat-label">Exams configured</p>
                    <h2><?php echo $examCount ?: '0'; ?></h2>
                    <p class="stat-helper">Exam records available.</p>
                </article>
                <article class="stat-card">
                    <p class="stat-label">Pending results</p>
                    <h2><?php echo $pendingCount ?: '0'; ?></h2>
                    <p class="stat-helper">Results awaiting review.</p>
                </article>
                <article class="stat-card">
                    <p class="stat-label">Feedback items</p>
                    <h2><?php echo $feedbackCount ?: '0'; ?></h2>
                    <p class="stat-helper">Open feedback requests.</p>
                </article>
            </section>

            <section class="dashboard-actions">
                <h2>Quick actions</h2>
                <div class="action-grid">
                    <a class="action-card" href="student_register.html">Register student</a>
                    <a class="action-card" href="view_students.php">Manage students</a>
                    <a class="action-card" href="add_question.php">Add Questions</a>
                    <a class="action-card" href="exam_timing.php">Set Exam Timing</a>
                    <a class="action-card" href="result.php">Review results</a>
                </div>
            </section>
        </div>
    </div>
</body>
</html>
