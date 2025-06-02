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
    <title>Student Dashboard</title>
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
        }        /* Modal Form Styles */
        .support-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }

        .support-check {
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 10px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .support-check:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .form-check-input:checked ~ .form-check-label {
            color: var(--primary-color);
            font-weight: 500;
        }

        .payment-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid var(--primary-color);
        }

        .input-group-text {
            background: var(--primary-color);
            color: white;
            border: none;
        }

        .success-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            background: var(--secondary-color);
            color: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            display: none;
            animation: slideInRight 0.5s ease;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Modal Form Styles */
        .support-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }

        .support-check {
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 10px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .support-check:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .form-check-input:checked ~ .form-check-label {
            color: var(--primary-color);
            font-weight: 500;
        }

        .payment-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid var(--primary-color);
        }

        .input-group-text {
            background: var(--primary-color);
            color: white;
            border: none;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-content">
            <div class="logo">
                <h1><i class="fas fa-graduation-cap"></i> Student Dashboard</h1>
            </div>
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </nav>

    <div class="container">
        <h2 class="section-title">Available Faculty Mentors</h2>
        
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
                        <button class="request-btn" onclick="requestMentor('<?php echo $faculty['faculty_id']; ?>')" data-bs-toggle="modal" data-bs-target="#mentorRequestModal">
                            <i class="fas fa-user-plus"></i> Request Mentorship
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>    <!-- Mentor Request Modal -->
    <div class="modal fade" id="mentorRequestModal" tabindex="-1" aria-labelledby="mentorRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mentorRequestModalLabel">
                        <i class="fas fa-user-plus"></i> Mentor Support Request
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="mentor_request.php" id="mentorRequestForm">
                    <div class="modal-body">
                        <input type="hidden" id="facultyName" name="faculty_id" value="">
                        
                        <div class="mb-4">
                            <label for="reason" class="form-label">
                                <i class="fas fa-comment-alt"></i> Why would you like to be mentored?
                            </label>
                            <textarea class="form-control" id="reason" name="reason" rows="4" required 
                                placeholder="Please describe your goals and what you hope to learn..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label"><i class="fas fa-comments"></i> Preferred Support Methods</label>
                            <div class="support-options">
                                <div class="form-check support-check">
                                    <input type="checkbox" class="form-check-input" name="support_methods[]" value="WhatsApp Group" id="whatsapp_support">
                                    <label class="form-check-label" for="whatsapp_support">
                                        <i class="fab fa-whatsapp"></i> WhatsApp Group
                                    </label>
                                </div>
                                <div class="form-check support-check">
                                    <input type="checkbox" class="form-check-input" name="support_methods[]" value="Zoom" id="zoom_support">
                                    <label class="form-check-label" for="zoom_support">
                                        <i class="fas fa-video"></i> Zoom
                                    </label>
                                </div>
                                <div class="form-check support-check">
                                    <input type="checkbox" class="form-check-input" name="support_methods[]" value="Google Meet" id="meet_support">
                                    <label class="form-check-label" for="meet_support">
                                        <i class="fas fa-video"></i> Google Meet
                                    </label>
                                </div>
                                <div class="form-check support-check">
                                    <input type="checkbox" class="form-check-input" name="support_methods[]" value="Slack" id="slack_support">
                                    <label class="form-check-label" for="slack_support">
                                        <i class="fab fa-slack"></i> Slack
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">
                                    <i class="fas fa-calendar"></i> Start Date
                                </label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required 
                                    min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="duration" class="form-label">
                                    <i class="fas fa-clock"></i> Duration (Days)
                                </label>
                                <input type="number" class="form-control" id="duration" name="duration" 
                                    min="1" max="365" required placeholder="Number of days">
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="payment-section">
                                <label for="amount" class="form-label">
                                    <i class="fas fa-money-bill"></i> Payment Amount (₹)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" class="form-control" id="amount" name="amount" 
                                        min="0" step="100" required placeholder="Enter amount">
                                </div>
                                <small class="text-muted">* Payment details will be shared after request approval</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary modal-btn" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary modal-btn">
                            <i class="fas fa-paper-plane"></i> Submit Request
                        </button>
                    </div>
                </form>
            </div>
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
        document.addEventListener('DOMContentLoaded', animateCards);        // Form submission animation and feedback
        const form = document.getElementById('mentorRequestForm');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

            fetch('mentor_request.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const modal = document.getElementById('mentorRequestModal');
                const toast = document.getElementById('success-toast');
                const bsModal = bootstrap.Modal.getInstance(modal);
                
                bsModal.hide();
                toast.innerHTML = `<i class="fas fa-check-circle"></i> ${data.message}`;
                toast.style.display = 'block';
                
                setTimeout(() => {
                    toast.style.display = 'none';
                    if (data.status === 'success') {
                        window.location.reload();
                    }
                }, 3000);
            })
            .catch(error => {
                const toast = document.getElementById('success-toast');
                toast.style.background = '#dc3545';
                toast.innerHTML = '<i class="fas fa-times-circle"></i> Failed to send request';
                toast.style.display = 'block';
                
                setTimeout(() => {
                    toast.style.display = 'none';
                }, 3000);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Request';
            });
        });

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
