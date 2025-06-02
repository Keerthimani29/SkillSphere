<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    header('Location: login.php');
    exit;
}

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];

    $status = ($action === 'accept') ? 1 : 2;

    $query = "UPDATE mentor_requests SET status = ? WHERE id = ? AND faculty_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sii', $status, $request_id, $_SESSION['user_id']);
    $stmt->execute();

    header('Location: notification.php');
    exit;
}
?>
