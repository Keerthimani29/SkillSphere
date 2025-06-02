<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit;
}

require 'db.php';

$query = "SELECT * FROM faculty WHERE active = 1";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Mentors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        :root {
            --primary-color: #4A90E2;
            --secondary-color: #2ECC71;
            --accent-color: #9B59B6;
            --background-color: #f5f6fa;
            --text-color: #2c3e50;
            --card-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        .navbar {
            background: white;
            padding: 20px 40px;
            box-shadow: var(--card-shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
            animation: slideInDown 0.5s ease;
        }

        .navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo h1 {
            color: var(--primary-color);
            margin: 0;
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logout-btn {
            background: linear-gradient(45deg, #FF416C, #FF4B2B);
            color: white;
            padding: 10px 25px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 65, 108, 0.4);
            color: white;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .section-title {
            text-align: center;
            margin-bottom: 40px;
            color: var(--primary-color);
            font-size: 32px;
            position: relative;
            padding-bottom: 15px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border-radius: 2px;
        }

        .faculty-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            padding: 20px;
        }

        .faculty-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
        }

        .faculty-card.show {
            opacity: 1;
            transform: translateY(0);
        }

        .faculty-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }

        .faculty-header {
            background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
            padding: 20px;
            color: white;
        }

        .faculty-avatar {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .faculty-avatar i {
            font-size: 40px;
            color: var(--primary-color);
        }

        .faculty-name {
            text-align: center;
            font-size: 24px;
            margin: 0;
        }

        .faculty-designation {
            text-align: center;
            font-size: 16px;
            opacity: 0.9;
            margin: 5px 0 0;
        }

        .faculty-details {
            padding: 20px;
        }

        .detail-item {
            margin-bottom: 15px;
            padding-left: 25px;
            position: relative;
        }

        .detail-item i {
            position: absolute;
            left: 0;
            top: 4px;
            color: var(--primary-color);
        }

        .detail-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .detail-value {
            color: var(--text-color);
        }

        .request-btn {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 500;
            width: 100%;
            margin-top: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .request-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 144, 226, 0.3);
        }

        .modal-content {
            border-radius: 20px;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 20px;
            border: none;
        }

        .modal-body {
            padding: 30px;
        }

        .form-label {
            color: var(--primary-color);
            font-weight: 500;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px;
            border: 2px solid #e1e1e1;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.2);
        }

        .modal-footer {
            border: none;
            padding: 20px;
        }

        .modal-btn {
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .modal-btn:hover {
            transform: translateY(-2px);
        }

        @keyframes slideInDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .support-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .support-badge {
            background: #f0f0f0;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-content" style="display: flex; align-items: center; justify-content: space-between; position: relative;">
            <div class="logo">
                <h1><i class="fas fa-chalkboard-teacher"></i> Mentors</h1>
            </div>
            <div style="margin-left: auto;">
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    
    <div class="container">
        <h2 class="section-title">Available Mentors</h2>
        <div class="faculty-grid">
            <?php while ($faculty = $result->fetch_assoc()): ?>
                <div class="faculty-card">
                    <div class="faculty-header">
                        <div class="faculty-avatar">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h3 class="faculty-name"><?php echo htmlspecialchars($faculty['name']); ?></h3>
                        <p class="faculty-designation"><?php echo htmlspecialchars($faculty['designation']); ?></p>
                    </div>
                    <div class="faculty-details">
                        <div class="detail-item">
                            <i class="fas fa-briefcase"></i>
                            <div class="detail-label">Experience</div>
                            <div class="detail-value"><?php echo htmlspecialchars($faculty['experience']); ?></div>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-code"></i>
                            <div class="detail-label">Domain</div>
                            <div class="detail-value"><?php echo htmlspecialchars($faculty['domain']); ?></div>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-graduation-cap"></i>
                            <div class="detail-label">Graduation</div>
                            <div class="detail-value"><?php echo htmlspecialchars($faculty['graduation']); ?></div>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-tools"></i>
                            <div class="detail-label">Skills</div>
                            <div class="detail-value"><?php echo htmlspecialchars($faculty['skills']); ?></div>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-star"></i>
                            <div class="detail-label">Area of Interest</div>
                            <div class="detail-value"><?php echo htmlspecialchars($faculty['area_of_interest']); ?></div>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div class="detail-label">Location</div>
                            <div class="detail-value"><?php echo htmlspecialchars($faculty['location']); ?></div>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-comments"></i>
                            <div class="detail-label">Mentor Support Through</div>
                            <div class="support-badges">
                                <?php 
                                $support_methods = explode(',', $faculty['mentor_support']);
                                foreach($support_methods as $method): 
                                    $method = trim($method);
                                    $icon = '';
                                    switch($method) {
                                        case 'WhatsApp Group': $icon = 'fab fa-whatsapp'; break;
                                        case 'Zoom': $icon = 'fas fa-video'; break;
                                        case 'Google Classroom': $icon = 'fas fa-chalkboard'; break;
                                        case 'Google Meet': $icon = 'fas fa-video'; break;
                                        case 'Slack': $icon = 'fab fa-slack'; break;
                                        default: $icon = 'fas fa-comment';
                                    }
                                ?>
                                    <span class="support-badge">
                                        <i class="<?php echo $icon; ?>"></i> <?php echo htmlspecialchars($method); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <form method="POST"  style="margin-top:20px;">
                            <input type="hidden" name="faculty_id" value="<?php echo htmlspecialchars($faculty['faculty_id']); ?>">
                            <input type="hidden" name="reason" value="Request for mentorship">
                            <button type="submit" class="request-btn">
                                <i class="fas fa-paper-plane"></i> Request
                            </button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    
    <div id="success-toast" class="success-toast">
        <i class="fas fa-check-circle"></i> Request sent successfully!
    </div>
    <script>
        // Animate faculty cards on scroll
        function animateCards() {
            const cards = document.querySelectorAll('.faculty-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('show');
                }, index * 200);
            });
        }

        // Run animation when page loads
        document.addEventListener('DOMContentLoaded', animateCards);

        // Mentor request modal logic
        function requestMentor(facultyId) {
            document.getElementById('facultyName').value = facultyId;
            var mentorRequestModal = new bootstrap.Modal(document.getElementById('mentorRequestModal'));
            mentorRequestModal.show();
        }

        

        // Hover animations for buttons
        document.querySelectorAll('.request-btn, .modal-btn').forEach(button => {
            button.addEventListener('mouseover', function() {
                this.style.transform = 'translateY(-2px)';
            });
            button.addEventListener('mouseout', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>

