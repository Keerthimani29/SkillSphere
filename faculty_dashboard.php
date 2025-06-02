<?php
session_start();
include('db.php');

$faculty = null;
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'faculty') {
    $faculty_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT faculty_id, name, email FROM faculty WHERE faculty_id = ?");
    $stmt->bind_param("i", $faculty_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $faculty = $result->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .faculty-profile-section {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            margin-top: 40px;
            margin-bottom: 40px;
            gap: 32px;
        }
        .faculty-card {
            background: linear-gradient(135deg, #e3f0ff 0%, #f9f9f9 100%);
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0,123,255,0.10), 0 1.5px 6px rgba(0,0,0,0.07);
            padding: 48px 36px 36px 36px;
            min-width: 300px;
            max-width: 350px;
            text-align: center;
            border: 2.5px solid #007BFF;
            transition: box-shadow 0.3s, transform 0.2s;
            position: relative;
        }
        .faculty-card:hover {
            box-shadow: 0 16px 48px rgba(0,123,255,0.18), 0 3px 12px rgba(0,0,0,0.09);
            transform: translateY(-4px) scale(1.03);
        }
        .faculty-card h2 {
            margin-bottom: 12px;
            color: #007BFF;
            font-size: 1.3em;
            letter-spacing: 1px;
        }
        .faculty-card .faculty-value {
            font-size: 1.25em;
            color: #222;
            margin-top: 12px;
            font-weight: bold;
        }
        .faculty-card .faculty-label {
            font-size: 1.1em;
            color: #555;
            font-weight: 500;
        }
        .faculty-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #e0e7ff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px auto;
            font-size: 2.5em;
            color: #007BFF;
            box-shadow: 0 2px 8px rgba(0,123,255,0.08);
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="sidebar" id="sidebar">
            <div class="profile">
                <div class="profile-image">
                    <!-- Optionally add faculty image here -->
                </div>
                <h3>SKILL SPHERE</h3>
            </div>
            <nav class="menu">
                <a href="faculty_dashboard.php" class="menu-item active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="faculty_profile.php" class="menu-item"><i class="fas fa-user-circle"></i> Profile</a>
                <a href="notification.php" class="menu-item"><i class="fas fa-bell"></i> Mentees Request</a>
                <a href="view_mentees.php" class="menu-item"><i class="fas fa-users"></i> View Mentees</a>
                <a href="roadmap.php" class="menu-item"><i class="fas fa-route"></i> Roadmaps</a>
                <a href="livesessions.php" class="menu-item"><i class="fas fa-chalkboard-teacher"></i> Live Sessions</a>
                <a href="project_faculty.php" class="menu-item"><i class="fas fa-lightbulb"></i> Projects</a>
                <!-- Add more menu items as needed -->
            </nav>
        </div>
        <div class="main-content">
            <div class="topbar">
                <button id="sidebar-toggle"><i class="fas fa-bars"></i></button>
                <div class="user-actions">
                    <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
            <div class="content">
                <!-- Faculty Info Cards -->
                <div class="faculty-profile-section">
                <?php if ($faculty): ?>
                    <div class="faculty-card" id="faculty_id_card">
                        <div class="faculty-avatar">
                            <i class="fas fa-id-badge"></i>
                        </div>
                        <div class="faculty-label">Faculty ID</div>
                        <div class="faculty-value"><?php echo htmlspecialchars($faculty['faculty_id']); ?></div>
                    </div>
                    <div class="faculty-card" id="faculty_name_card">
                        <div class="faculty-avatar">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="faculty-label">Name</div>
                        <div class="faculty-value"><?php echo htmlspecialchars($faculty['name']); ?></div>
                    </div>
                    <div class="faculty-card" id="faculty_email_card">
                        <div class="faculty-avatar">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="faculty-label">Email</div>
                        <div class="faculty-value"><?php echo htmlspecialchars($faculty['email']); ?></div>
                    </div>
                <?php else: ?>
                    <div class="faculty-card">
                        <div class="faculty-avatar">
                            <i class="fas fa-user-slash"></i>
                        </div>
                        <div class="faculty-label">Faculty Profile</div>
                        <div class="faculty-value">No faculty details found.</div>
                    </div>
                <?php endif; ?>
                </div>
                <!-- ...existing code... -->
            </div>
        </div>
    </div>
    <script>
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('collapsed');
        });
    </script>
</body>
</html>