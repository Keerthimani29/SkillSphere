<?php
include('db.php');
session_start();

if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'student') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $faculty_id = $_POST['faculty_id'];
        $message = $_POST['message'];
        $student_id = $_SESSION['user_id'];

        // Insert the request into the mentor_request table
        $query = "INSERT INTO mentor_requests (student_id, faculty_id, request, status) VALUES (?, ?, ?, 3)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iis", $student_id, $faculty_id, $message);

        if ($stmt->execute()) {
            echo "Request submitted successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
} else {
    echo "Unauthorized access.";
}

$conn->close();
?>
