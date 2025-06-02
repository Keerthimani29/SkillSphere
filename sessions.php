<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Student - Live Sessions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <h2>Live Sessions for Students</h2>
    <div class="row mt-3">
        <?php
        $result = $conn->query("SELECT * FROM sessions ORDER BY session_date DESC");
        if ($result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
        ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-primary">
                <div class="card-body">
                    <h5 class="card-title text-primary"><?= htmlspecialchars($row['topic']) ?></h5>
                    <p class="card-text"><strong>Mentor:</strong> <?= htmlspecialchars($row['mentor_name']) ?></p>
                    <p class="card-text"><strong>Date:</strong> <?= $row['session_date'] ?></p>
                    <p class="card-text"><strong>Time:</strong> <?= $row['session_time'] ?></p>
                    <a href="<?= htmlspecialchars($row['meeting_link']) ?>" class="btn btn-outline-primary" target="_blank">Join Session</a>
                </div>
            </div>
        </div>
        <?php endwhile; else: ?>
            <div class="col-12"><div class="alert alert-info">No sessions available.</div></div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
