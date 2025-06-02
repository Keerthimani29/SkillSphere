<?php
include('db.php');
session_start();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $id = $_POST['id'];
    $password = $_POST['password'];

    if (isset($_POST['register'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $contact = $_POST['contact'];
        $confirm = $_POST['confirm_password'];

        if ($password !== $confirm) {
            $message = "Passwords do not match!";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            if ($role == 'student') {
                $sql = "INSERT INTO student (student_id, name, email, contact, password)
                        VALUES ('$id', '$name', '$email', '$contact', '$hashed')";
                        
            } else {
                $sql = "INSERT INTO faculty (faculty_id, name, email, contact, password)
                        VALUES ('$id', '$name', '$email', '$contact', '$hashed')";
                       
            }

            if ($conn->query($sql) === TRUE) {
                $message = "Registered successfully!";
            } else {
                $message = "Error: " . $conn->error;
            }
        }
    }

    if (isset($_POST['login'])) {
        if ($role == 'student') {
            $sql = "SELECT * FROM student WHERE student_id = '$id'";
        } else {
            $sql = "SELECT * FROM faculty WHERE faculty_id = '$id'";
        }
        $result = $conn->query($sql);

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['faculty_id'] ?? $user['student_id'];
                $_SESSION['role'] = $role;
                header("Location: " . $role . "_dashboard.php");
                exit;
            } else {
                $message = "Incorrect password!";
            }
        } else {
            $message = ucfirst($role) . " not found!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Skillsphere</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f3f3;
            display: flex;
            justify-content: center;
            padding-top: 50px;
        }

        .form-container {
            width: 400px;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .role-selector {
            display: flex;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            overflow: hidden;
        }

        .role-selector button {
            flex: 1;
            padding: 10px;
            border: none;
            background: #eee;
            cursor: pointer;
            font-weight: bold;
        }

        .role-selector button.active {
            background: #4CAF50;
            color: white;
        }

        input {
            width: 100%;
            margin: 5px 0;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button[type="submit"] {
            width: 100%;
            margin-top: 10px;
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            font-weight: bold;
            border-radius: 4px;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #007BFF;
            text-decoration: none;
        }

        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="role-selector">
            <button id="studentBtn" class="active" onclick="setRole('student')">Student</button>
            <button id="facultyBtn" onclick="setRole('faculty')">Faculty</button>
        </div>

        <div id="loginForm">
            <h2>Login</h2>
            <p class="error"><?php echo $message; ?></p>
            <form method="POST">
                <input type="text" name="id" placeholder="ID" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="hidden" name="role" id="roleInput" value="student">
                <button type="submit" name="login">Login</button>
            </form>
            <a href="#" onclick="toggleForms()">Register here</a>
        </div>

        <div id="registerForm" style="display:none;">
            <h2>Register</h2>
            <p class="error"><?php echo $message; ?></p>
            <form method="POST">
                <input type="text" name="id" placeholder="ID" required>
                <input type="text" name="name" placeholder="Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="contact" placeholder="Contact" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <input type="hidden" name="role" id="registerRoleInput" value="student">
                <button type="submit" name="register">Register</button>
            </form>
            <a href="#" onclick="toggleForms()">Back to login</a>
        </div>
    </div>

    <script>
        function setRole(role) {
            document.getElementById('roleInput').value = role;
            document.getElementById('registerRoleInput').value = role;

            document.getElementById('studentBtn').classList.remove('active');
            document.getElementById('facultyBtn').classList.remove('active');

            if (role === 'student') {
                document.getElementById('studentBtn').classList.add('active');
            } else {
                document.getElementById('facultyBtn').classList.add('active');
            }
        }

        function toggleForms() {
            const login = document.getElementById('loginForm');
            const register = document.getElementById('registerForm');
            login.style.display = login.style.display === 'none' ? 'block' : 'none';
            register.style.display = register.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>
