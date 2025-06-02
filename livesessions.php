<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Mentor - Live Sessions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">

<div class="container mt-4">
    <h2>Mentor Panel - Live Sessions</h2>

    <!-- Add Session Button -->
    <button class="btn btn-success mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#sessionForm">
        ➕ Add Session
    </button>

    <!-- Collapsible Form -->
    <div class="collapse" id="sessionForm">
        <form action="backend.php" method="post" class="row g-3 bg-white p-4 rounded shadow-sm">
            <div class="col-md-6">
                <label for="mentor_name" class="form-label">Mentor Name</label>
                <input type="text" name="mentor_name" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="topic" class="form-label">Topic</label>
                <input type="text" name="topic" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label for="session_date" class="form-label">Date</label>
                <input type="date" name="session_date" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label for="session_time" class="form-label">Time</label>
                <input type="time" name="session_time" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label for="meeting_link" class="form-label">Meeting Link</label>
                <input type="url" name="meeting_link" class="form-control" required>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Submit Session</button>
            </div>
        </form>
    </div>

    <!-- Scheduled Sessions Section -->
    <h4 class="mt-5">Your Scheduled Sessions</h4>
    <div class="row mt-3">
        <?php
        $result = $conn->query("SELECT * FROM sessions ORDER BY session_date DESC");
        if ($result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
        ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($row['topic']) ?></h5>
                    <p class="card-text"><strong>Date:</strong> <?= $row['session_date'] ?></p>
                    <p class="card-text"><strong>Time:</strong> <?= $row['session_time'] ?></p>
                </div>
            </div>
        </div>
        <?php endwhile; else: ?>
            <div class="col-12"><div class="alert alert-warning">No sessions scheduled yet.</div></div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
