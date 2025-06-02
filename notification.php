<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    header('Location: login.php');
    exit;
}

require 'db.php';

// Updated query to include new fields
$query = "SELECT 
    mr.id, 
    mr.student_id, 
    mr.reason, 
    mr.support_methods,
    mr.start_date,
    mr.duration,
    mr.amount,
    s.name as student_name
FROM mentor_requests mr
JOIN student s ON mr.student_id = s.student_id
WHERE mr.faculty_id = ? AND mr.status = 0";

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
    <title>Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4A90E2;
            --secondary-color: #2ECC71;
            --accent-color: #9B59B6;
            --background-color: #f5f6fa;
            --text-color: #2c3e50;
            --card-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        body {
            background: var(--background-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }

        .request-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: var(--card-shadow);
            animation: slideInUp 0.5s ease;
        }

        .request-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .student-avatar {
            width: 50px;
            height: 50px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            margin-right: 15px;
        }

        .request-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .detail-item {
            margin-bottom: 10px;
        }

        .detail-label {
            font-weight: 600;
            color: var(--primary-color);
        }

        .support-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .support-badge {
            background: #e9ecef;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-action {
            padding: 10px 25px;
            border-radius: 25px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-accept {
            background: var(--secondary-color);
            color: white;
        }

        .btn-reject {
            background: #dc3545;
            color: white;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        @keyframes slideInUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-bell"></i> Mentees Request</h1>
            <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($request = $result->fetch_assoc()): ?>
                <div class="request-card">
                    <div class="request-header">
                        <div class="student-avatar">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div>
                            <h4><?php echo htmlspecialchars($request['student_name']); ?></h4>
                            <small class="text-muted">Student ID: <?php echo htmlspecialchars($request['student_id']); ?></small>
                        </div>
                    </div>

                    <div class="request-details">
                        <div class="detail-item">
                            <div class="detail-label"><i class="fas fa-comment-alt"></i> Reason for Mentorship</div>
                            <p class="mb-0"><?php echo htmlspecialchars($request['reason']); ?></p>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label"><i class="fas fa-comments"></i> Preferred Support Methods</div>
                            <div class="support-badges">
                                <?php 
                                $methods = explode(', ', $request['support_methods']);
                                foreach($methods as $method): ?>
                                    <span class="support-badge">
                                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($method); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="detail-item">
                                    <div class="detail-label"><i class="fas fa-calendar"></i> Start Date</div>
                                    <p class="mb-0"><?php echo date('F j, Y', strtotime($request['start_date'])); ?></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-item">
                                    <div class="detail-label"><i class="fas fa-clock"></i> Duration</div>
                                    <p class="mb-0"><?php echo htmlspecialchars($request['duration']); ?> days</p>
                                </div>
                            </div>
                            
                        </div>
                    </div>

                    <form method="POST" action="process_request.php" class="action-buttons">
                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                        <button type="submit" name="action" value="accept" class="btn-action btn-accept">
                            <i class="fas fa-check"></i> Accept
                        </button>
                        <button type="submit" name="action" value="reject" class="btn-action btn-reject">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="request-card text-center">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h3>No New Requests</h3>
                <p class="text-muted">You're all caught up! Check back later for new mentor requests.</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>