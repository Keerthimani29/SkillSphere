<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit;
}

include('db.php');

if (isset($_POST['profile_submit'])) {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $department = $_POST['department'];
    $skills = $_POST['skills'];
    $area_of_interest = $_POST['area_of_interest'];
    $location = $_POST['location'];
    
    $query = "UPDATE student SET name = ?, contact = ?, email = ?, department  = ?, skills = ?, area_of_interest = ?, location  = ? WHERE student_id = ?";
    $sql = $conn->prepare($query);

    if ($sql === false) {
        die("Prepare failed: " . $conn->error);
    }

    $sql->bind_param('sssssssi', $name, $contact, $email, $department, $skills, $area_of_interest, $location, $_SESSION['user_id']);
    
    $sql->execute();

    header('Location: student_dashboard.php');
    exit;
}
?>
