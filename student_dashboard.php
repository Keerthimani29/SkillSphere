<?php
session_start();
include('db.php');

$student = null;
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'student') {
    $student_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT student_id, name, email FROM student WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .student-profile-section {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            margin-top: 40px;
            margin-bottom: 40px;
            gap: 32px;
        }
        .student-card {
            background: linear-gradient(135deg, #e3f0ff 0%, #f9f9f9 100%);
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0,123,255,0.10), 0 1.5px 6px rgba(0,0,0,0.07);
            padding: 48px 36px 36px 36px; /* Increased padding */
            min-width: 300px;             /* Increased min-width */
            max-width: 350px;             /* Increased max-width */
            text-align: center;
            border: 2.5px solid #007BFF;
            transition: box-shadow 0.3s, transform 0.2s;
            position: relative;
        }
        .student-card:hover {
            box-shadow: 0 16px 48px rgba(0,123,255,0.18), 0 3px 12px rgba(0,0,0,0.09);
            transform: translateY(-4px) scale(1.03);
        }
        .student-card h2 {
            margin-bottom: 12px;
            color: #007BFF;
            font-size: 1.3em;
            letter-spacing: 1px;
        }
        .student-card .student-value {
            font-size: 1.25em;
            color: #222;
            margin-top: 12px;
            font-weight: bold;
        }
        .student-card .student-label {
            font-size: 1.1em;
            color: #555;
            font-weight: 500;
        }
        .student-avatar {
            width: 80px;   /* Increased size */
            height: 80px;  /* Increased size */
            border-radius: 50%;
            background: #e0e7ff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px auto;
            font-size: 2.5em; /* Increased icon size */
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
                    
                </div>
                <h3>SKILL SPHERE</h3>
            </div>
            <nav class="menu">
                <a href="student_dashboard.php" class="menu-item active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="student_profile.php" class="menu-item"><i class="fas fa-user-circle"></i> Profile</a>
                <a href="view_mentors.php" class="menu-item"><i class="fas fa-chalkboard-teacher"></i> Mentors</a>
                <a href="mentorship.php" class="menu-item"><i class="fas fa-user-plus"></i> Request mentors</a>
                <a href="std_notify.php" class="menu-item"><i class="fas fa-bell"></i> Request status</a>
                <a href="sessions.php" class="menu-item"><i class="fas fa-video"></i> Live sessions</a>
                <a href="project_student.php" class="menu-item"><i class="fas fa-lightbulb"></i> Projects</a>
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
                <!-- Student Info Cards -->
                <div class="student-profile-section">
                <?php if ($student): ?>
                    <div class="student-card" id="student_id_card">
                        <div class="student-avatar">
                            <i class="fas fa-id-badge"></i>
                        </div>
                        <div class="student-label">Student ID</div>
                        <div class="student-value"><?php echo htmlspecialchars($student['student_id']); ?></div>
                    </div>
                    <div class="student-card" id="student_name_card">
                        <div class="student-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="student-label">Name</div>
                        <div class="student-value"><?php echo htmlspecialchars($student['name']); ?></div>
                    </div>
                    <div class="student-card" id="student_email_card">
                        <div class="student-avatar">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="student-label">Email</div>
                        <div class="student-value"><?php echo htmlspecialchars($student['email']); ?></div>
                    </div>
                <?php else: ?>
                    <div class="student-card">
                        <div class="student-avatar">
                            <i class="fas fa-user-slash"></i>
                        </div>
                        <div class="student-label">Student Profile</div>
                        <div class="student-value">No student details found.</div>
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
