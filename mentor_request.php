<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit;
}

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $faculty_id = $_POST['faculty_id'];
    $reason = $_POST['reason'];
    
    // Handle support methods
    $support_methods = isset($_POST['support_methods']) ? implode(', ', $_POST['support_methods']) : '';
    
    // Handle other new fields
    $start_date = $_POST['start_date'];
    $duration = (int)$_POST['duration'];
    $amount = (float)$_POST['amount'];

    // Insert into database with new fields
    $query = "INSERT INTO mentor_requests (
        student_id, 
        faculty_id, 
        reason, 
        support_methods, 
        start_date, 
        duration, 
        amount,
        status,
        created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        'iisssid', 
        $_SESSION['user_id'], 
        $faculty_id, 
        $reason, 
        $support_methods, 
        $start_date, 
        $duration, 
        $amount
    );

    if ($stmt->execute()) {
        // Return success response for AJAX
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Request sent successfully']);
    } else {
        // Return error response for AJAX
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to send request']);
    }
    exit;
}

// If not POST request, redirect back to student page
header('Location: student.php');
exit;
?>
