<?php
include('db.php');
session_start();

if ($_SESSION['role'] !== 'faculty') {
    header("Location: login.php");
    exit;
}

// Handle approval action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_id'])) {
    $project_id = $_POST['approve_id'];
    $stmt = $conn->prepare("UPDATE projects SET is_approved = 1 WHERE id = ?");
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch only unapproved student projects
$query = "SELECT * FROM projects WHERE is_approved = 0 ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Faculty Project Approval</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { padding-top: 40px; }
        .card { margin-bottom: 20px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); border-radius: 10px; }
        .screenshot { max-height: 150px; width: 100%; object-fit: contain; margin-bottom: 10px; border-radius: 8px; border: 1px solid #ccc; }
    </style>
</head>
<body>
<div class="container">
    <h2 class="mb-4">Faculty Dashboard - Project Approval</h2>
    <a href="logout.php" class="btn btn-danger mb-4">Logout</a>

    <div class="row">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 p-3">
                    <?php if (!empty($row['screenshot_path'])): ?>
                        <img src="<?= htmlspecialchars($row['screenshot_path']) ?>" alt="Screenshot" class="screenshot">
                    <?php endif; ?>
                    <h4><?= htmlspecialchars($row['title']) ?></h4>
                    <p><strong>Student:</strong> <?= htmlspecialchars($row['student_name']) ?> (ID: <?= htmlspecialchars($row['student_id']) ?>)</p>
                    <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                    <a href="<?= htmlspecialchars($row['github_link']) ?>" class="btn btn-primary" target="_blank">GitHub</a>

                    <?php if ($row['is_approved'] == 0): ?>
                        <form method="POST" class="d-inline-block mt-2">
                            <input type="hidden" name="approve_id" value="<?= $row['id'] ?>">
                            <button type="submit" class="btn btn-success w-100">Approve</button>
                        </form>
                    <?php else: ?>
                        <span class="badge bg-success mt-2">Approved</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
