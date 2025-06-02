<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentor List</title>
    <style>
        .card {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 16px;
            margin: 16px;
            width: 300px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card h3 {
            margin: 0;
        }
        .card p {
            margin: 8px 0;
        }
        .card button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        .card button:hover {
            background-color: #0056b3;
        }
        .form-container {
            display: none;
            margin-top: 16px;
        }
    </style>
    <script>
        function openForm(facultyId) {
            const form = document.getElementById('requestForm');
            form.style.display = 'block';
            document.getElementById('facultyIdInput').value = facultyId;
        }
    </script>
</head>
<body>
    <h1>Mentor List</h1>
    <div id="mentorCards">
        <?php
        // ...existing PHP code to fetch mentors...
        include('db.php');
        session_start();

        if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'student') {
            $query = "SELECT f.faculty_id, f.name, f.domain 
                      FROM faculty f 
                      INNER JOIN mentor_requests mr ON f.faculty_id = mr.faculty_id 
                      WHERE mr.status = 1";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="card">';
                    echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                    echo '<p>Domain: ' . htmlspecialchars($row['domain']) . '</p>';
                    echo '<button onclick="openForm(\'' . htmlspecialchars($row['faculty_id']) . '\')">Request Guidance</button>';
                    echo '</div>';
                }
            } else {
                echo '<p>No active faculty found.</p>';
            }

            $stmt->close();
        } else {
            echo '<p>User not logged in.</p>';
        }
     ?>
    </div>

    <div id="requestForm" class="form-container">
        <h2>Request Guidance</h2>
        <form action="submit_request.php" method="POST">
            <input type="hidden" id="facultyIdInput" name="faculty_id">
            <label for="message">Message:</label><br>
            <textarea id="message" name="message" rows="4" cols="50" required></textarea><br><br>
            <button type="submit">Submit Request</button>
        </form>
    </div>
</body>
</html>
