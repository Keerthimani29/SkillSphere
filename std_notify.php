<?php
 session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
   header('Location: login.php');
    exit;
}  

require 'db.php';

// Enhanced query to get faculty details and all request information
$query = "SELECT mr.*, f.name as faculty_name, f.designation, f.domain, f.skills, f.area_of_interest 
          FROM mentor_requests mr 
          JOIN faculty f ON mr.faculty_id = f.faculty_id 
          WHERE mr.student_id = ? AND mr.status IN (1,2) 
          ORDER BY mr.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentor Request Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4A90E2;
            --secondary-color: #2ECC71;
            --danger-color: #E74C3C;
            --warning-color: #F39C12;
            --background-color: #f5f6fa;
            --text-color: #2c3e50;
            --card-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        body {
            background: var(--background-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            min-height: 100vh;
            line-height: 1.6;
        }

        .navbar {
            background: white;
            padding: 15px 30px;
            border-radius: 15px;
            box-shadow: var(--card-shadow);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: slideInDown 0.5s ease;
        }

        @keyframes slideInDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .navbar h1 {
            color: var(--primary-color);
            margin: 0;
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-group .btn {
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-left: 10px;
        }

        .btn-home {
            background: var(--primary-color);
            color: white;
        }

        .btn-logout {
            background: linear-gradient(45deg, #FF416C, #FF4B2B);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .notification-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            animation: slideIn 0.5s ease forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        .notification-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0,0,0,0.15);
        }

        @keyframes slideIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .faculty-header {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }

        .faculty-avatar {
            width: 70px;
            height: 70px;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            flex-shrink: 0;
        }

        .faculty-avatar i {
            font-size: 35px;
            color: white;
        }

        .faculty-info {
            flex-grow: 1;
        }

        .faculty-info h3 {
            margin: 0 0 5px;
            color: var(--text-color);
            font-size: 24px;
        }

        .faculty-info p {
            margin: 5px 0;
            color: #666;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-badge {
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(46, 204, 113, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(46, 204, 113, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(46, 204, 113, 0);
            }
        }

        .status-accepted {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status-rejected {
            background: #fde7e7;
            color: #d32f2f;
            animation: none;
        }

        .request-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
            margin: 20px 0;
            position: relative;
            overflow: hidden;
        }

        .request-details::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--primary-color);
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .detail-item {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .detail-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .detail-value {
            font-weight: 600;
            color: var(--text-color);
            font-size: 16px;
        }

        .request-message {
            margin-top: 20px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .request-message h4 {
            color: var(--primary-color);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .support-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .support-badge {
            background: #e9ecef;
            padding: 8px 15px;
            border-radius: 25px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .support-badge:hover {
            transform: translateX(5px);
            background: #dee2e6;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: var(--card-shadow);
            max-width: 600px;
            margin: 40px auto;
        }

        .empty-state i {
            font-size: 60px;
            color: #ddd;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: var(--text-color);
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #666;
            margin-bottom: 20px;
        }

        .empty-state .btn {
            padding: 12px 30px;
            font-weight: 500;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .empty-state .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 144, 226, 0.3);
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 15px;
                text-align: center;
                padding: 20px;
            }

            .btn-group {
                display: flex;
                width: 100%;
                gap: 10px;
            }

            .btn-group .btn {
                flex: 1;
                margin: 0;
            }

            .faculty-header {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .faculty-avatar {
                margin-right: 0;
                margin-bottom: 15px;
            }

            .status-badge {
                margin-top: 15px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1><i class="fas fa-bell"></i> Mentor Request Status</h1>
        <div class="btn-group">
            <a href="student.php" class="btn btn-home">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="logout.php" class="btn btn-logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </nav>

    <div class="container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($notification = $result->fetch_assoc()): 
                // Set animation delay for each card
              //  $delay = $loop_count * 0.1;
               // $loop_count++;
            ?>
                <div class="notification-card" style="animation-delay: <?php echo $delay; ?>s">
                    <div class="faculty-header">
                        <div class="faculty-avatar">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="faculty-info">
                            <h3><?php echo htmlspecialchars($notification['faculty_name']); ?></h3>
                            <p>
                                <i class="fas fa-briefcase"></i> 
                                <?php echo htmlspecialchars($notification['designation']); ?>
                            </p>
                            <p>
                                <i class="fas fa-code"></i> 
                                <?php echo htmlspecialchars($notification['domain']); ?>
                            </p>
                            <p>
                                <i class="fas fa-star"></i>
                                <?php echo htmlspecialchars($notification['area_of_interest']); ?>
                            </p>
                        </div>
                        <div class="ms-auto">
                            <span class="status-badge <?php echo $notification['status'] == 1 ? 'status-accepted' : 'status-rejected'; ?>">
                                <?php if($notification['status'] == 1): ?>
                                    <i class="fas fa-check-circle"></i> Request Accepted
                                <?php else: ?>
                                    <i class="fas fa-times-circle"></i> Request Declined
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>

                    <div class="request-details">
                        <div class="detail-grid">
                            <div class="detail-item">
                                <div class="detail-label">
                                    <i class="fas fa-calendar"></i> Start Date
                                </div>
                                <div class="detail-value">
                                    <?php echo date('F j, Y', strtotime($notification['start_date'])); ?>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">
                                    <i class="fas fa-clock"></i> Duration
                                </div>
                                <div class="detail-value">
                                    <?php echo htmlspecialchars($notification['duration']); ?> days
                                </div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-label">
                                    <i class="fas fa-calendar-check"></i> Requested On
                                </div>
                                <div class="detail-value">
                                    <?php echo date('F j, Y', strtotime($notification['created_at'])); ?>
                                </div>
                            </div>
                        </div>

                        <div class="request-message">
                            <h4><i class="fas fa-comment-alt"></i> Your Request Message</h4>
                            <p><?php echo htmlspecialchars($notification['reason']); ?></p>
                        </div>

                        <div class="request-message">
                            <h4><i class="fas fa-comments"></i> Support Methods</h4>
                            <div class="support-badges">
                                <?php 
                                $methods = explode(', ', $notification['support_methods']);
                                foreach($methods as $method): 
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
                                        <i class="<?php echo $icon; ?>"></i>
                                        <?php echo htmlspecialchars($method); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <?php if($notification['status'] == 1): ?>
                            <div class="request-message">
                                <h4><i class="fas fa-info-circle"></i> Next Steps</h4>
                                <p>Your mentor request has been accepted! You will receive further instructions about the payment and session details through your registered email address.</p>
                                <div class="support-badges">
                                    <span class="support-badge">
                                        <i class="fas fa-envelope"></i> Check your email
                                    </span>
                                    <span class="support-badge">
                                        <i class="fas fa-money-bill"></i> Complete payment
                                    </span>
                                    <span class="support-badge">
                                        <i class="fas fa-calendar-check"></i> Schedule sessions
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-bell-slash"></i>
                <h3>No Notifications Yet</h3>
                <p>You haven't received any responses to your mentor requests yet. Why not explore available mentors and submit a request?</p>
                <a href="student.php" class="btn btn-primary">
                    <i class="fas fa-search"></i> Find Mentors
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animate notification cards on load
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.notification-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });
        });
    </script>
</body>
</html>