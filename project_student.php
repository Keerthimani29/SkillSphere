<?php
include('db.php');
session_start();

if ($_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $link = $_POST['github_link'];
    $description = $_POST['description'];
    $student_name_input = $_POST['student_name'];

    $screenshot = '';
    if (isset($_FILES['screenshot']) && $_FILES['screenshot']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = basename($_FILES['screenshot']['name']);
        $targetPath = $uploadDir . uniqid() . '_' . $fileName;
        if (move_uploaded_file($_FILES['screenshot']['tmp_name'], $targetPath)) {
            $screenshot = $targetPath;
        }
    }

    $stmt = $conn->prepare("INSERT INTO projects (student_id, student_name, title, github_link, description, screenshot_path) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $student_id, $student_name_input, $title, $link, $description, $screenshot);
    $stmt->execute();
    $stmt->close();

    header("Location: project_student.php");
    exit;
}

$projects = $conn->query("SELECT * FROM projects WHERE student_id = '$student_id' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Project Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        .layout {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 20%;
            background-color: #28a745;
            color: white;
            padding: 20px;
            min-height: 100vh;
        }

        .sidebar h2 {
            font-size: 1.5rem;
            margin-bottom: 30px;
        }


        .nav-link {
            color: white;
            display: block;
            margin: 10px 0;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .nav-link:hover {
            text-decoration: underline;
        }

        .content {
            width: 80%;
            background-color: #f8f9fa;
            padding: 20px;
        }

        .top-bar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 20px;
        }

        .add-project-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-add-small {
            padding: 6px 12px;
            font-size: 14px;
        }

        .card-title-header {
            background-color: #28a745;
            color: white;
            padding: 8px 12px;
            border-radius: 6px 6px 0 0;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .card {
            border-radius: 10px;
            border: 2px solid #28a745;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .screenshot {
            width: 100%;
            max-height: 150px;
            object-fit: contain;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .card-footer {
            background-color: #28a745;
            height: 6px;
            border-radius: 0 0 10px 10px;
        }

        .sidebar i {
            margin-right: 10px;
        }

    </style>
</head>
<body>
<div class="layout">
    

    <!-- Main Content -->
    <div class="content">
        <!-- Top Bar -->
        <div class="top-bar">
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>

        <!-- Projects Title -->
        <div class="mb-3">
            <h3>Projects</h3>
        </div>

        <!-- Add Project and Button -->
        <div class="add-project-header mb-3">
            <button class="btn btn-success btn-add-small" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                <i class="bi bi-plus-circle me-1"></i>Add Project
            </button>
        </div>

        <!-- Projects -->
        <div class="row">
            <?php while ($row = $projects->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-title-header"><?= htmlspecialchars($row['title']) ?></div>
                        <div class="card-body">
                            <?php if (!empty($row['screenshot_path'])): ?>
                                <img src="<?= htmlspecialchars($row['screenshot_path']) ?>" alt="Screenshot" class="screenshot">
                            <?php endif; ?>
                            <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                            <a href="<?= htmlspecialchars($row['github_link']) ?>" target="_blank" class="btn btn-primary btn-sm">View on GitHub</a>
                            <?php if ($row['is_approved'] == 1): ?>
                                <button class="btn btn-outline-success btn-sm mt-2" disabled>Approved</button>
                            <?php else: ?>
                                <button class="btn btn-outline-secondary btn-sm mt-2" disabled>Pending Approval</button>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer"></div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addProjectModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" name="student_name" placeholder="Your Name" class="form-control mb-2" required>
                    <input type="text" name="title" placeholder="Project Title" class="form-control mb-2" required>
                    <input type="url" name="github_link" placeholder="GitHub Link" class="form-control mb-2" required>
                    <textarea name="description" placeholder="Description" class="form-control mb-2" rows="4" required></textarea>
                    <label for="screenshot" class="form-label">Screenshot (optional):</label>
                    <input type="file" name="screenshot" accept="image/*" class="form-control mb-2">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add Project</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
