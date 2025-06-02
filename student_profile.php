<?php
include('db.php');

// Fetch existing student details
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit;
}

 $query = "SELECT * FROM student WHERE student_id = ?";
$sql = $conn->prepare($query);
$sql->bind_param('i', $_SESSION['user_id']);
$sql->execute();
$result = $sql->get_result();
$student = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4A90E2;
            --secondary-color: #2ECC71;
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
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 30px;
            font-size: 2.5em;
            animation: fadeInDown 0.8s ease;
        }

        .profile-form {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: var(--card-shadow);
            animation: fadeIn 1s ease;
        }

        .form-group {
            margin-bottom: 20px;
            opacity: 0;
            animation: slideInLeft 0.5s ease forwards;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--primary-color);
        }

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-group input:focus, .form-group textarea:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.2);
            outline: none;
        }

        .checkbox-group {
            margin-top: 10px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .checkbox-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .checkbox-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 10px;
        }

        .selected-modes {
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid var(--secondary-color);
        }

        .submit-btn {
            background: var(--primary-color);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            background: #357ABD;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 144, 226, 0.3);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .success-message {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            background: var(--secondary-color);
            color: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            opacity: 0;
            transform: translateX(100%);
        }

        .success-message.show {
            animation: slideInRight 0.5s ease forwards;
        }

        @keyframes slideInRight {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>


    <div class="container">
        <h2><i class="fas fa-user-edit"></i> Update Your Profile</h2>
        <form method="POST" action="update_student.php" class="profile-form">
            <div class="form-group" style="animation-delay: 0.1s;">
                <label for="name"><i class="fas fa-user"></i> Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>
            </div>

            <div class="form-group" style="animation-delay: 0.2s;">
                <label for="contact"><i class="fas fa-phone"></i> Contact:</label>
                <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($student['contact']); ?>" required>
            </div>

            <div class="form-group" style="animation-delay: 0.3s;">
                <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
            </div>

            

            <div class="form-group" style="animation-delay: 0.5s;">
                <label for="department"><i class="fas fa-code"></i> Department:</label>
                <input type="text" id="department" name="department" value="<?php echo htmlspecialchars($student['department']); ?>" required>
            </div>

            

        
            <div class="form-group" style="animation-delay: 0.8s;">
                <label for="skills"><i class="fas fa-tools"></i> Skills:</label>
                <textarea id="skills" name="skills" rows="4"><?php echo htmlspecialchars($student['skills'] ?? ''); ?></textarea>
            </div>

            <div class="form-group" style="animation-delay: 0.9s;">
                <label for="area_of_interest"><i class="fas fa-star"></i> Area of Interest:</label>
                <input type="text" id="area_of_interest" name="area_of_interest" value="<?php echo htmlspecialchars($student['area_of_interest'] ?? ''); ?>">
            </div>

            <div class="form-group" style="animation-delay: 1s;">
                <label for="location"><i class="fas fa-map-marker-alt"></i> Location:</label>
                <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($student['location'] ?? ''); ?>">
            </div>

           

            <button type="submit" name="profile_submit" class="submit-btn">
                <i class="fas fa-save"></i> Update Profile
            </button>
        </form>
    </div>

    <div id="success-message" class="success-message">
        <i class="fas fa-check-circle"></i> Profile updated successfully!
    </div>

    <script>
        // Add animation delay to form groups
        document.querySelectorAll('.form-group').forEach((group, index) => {
            group.style.animationDelay = `${0.1 * (index + 1)}s`;
        });

        // Checkbox interaction
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        const selectedModesText = document.getElementById('selected-modes-text');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedModes);
        });

        function updateSelectedModes() {
            const selected = Array.from(checkboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
            selectedModesText.textContent = selected.length ? selected.join(', ') : 'None';
        }

        // Animate form fields on focus
        const inputs = document.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.parentElement.style.transform = 'translateX(10px)';
            });
            input.addEventListener('blur', () => {
                input.parentElement.style.transform = 'translateX(0)';
            });
        });

        // Form submission animation
        const form = document.querySelector('form');
        const successMessage = document.getElementById('success-message');

        form.addEventListener('submit', (e) => {
            const btn = document.querySelector('.submit-btn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
        });
    </script>
</body>
</html> 

