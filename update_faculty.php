<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    header('Location: login.php');
    exit;
}

include('db.php');

if (isset($_POST['profile_submit'])) {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $experience = $_POST['experience'];
    $domain = $_POST['domain'];
    $designation = $_POST['designation'];
    $graduation = $_POST['graduation'];
    $skills = $_POST['skills'];
    $area_of_interest = $_POST['area_of_interest'];
    $location = $_POST['location'];
    $mentor_support = isset($_POST['mentor_support']) ? implode(', ', $_POST['mentor_support']) : 'Not Selected';

    $query = "UPDATE faculty SET name = ?, contact = ?, email = ?, experience = ?, domain = ?, designation = ?, graduation = ?, skills = ?, area_of_interest = ?, location = ?, mentor_support = ? WHERE faculty_id = ?";
    $sql = $conn->prepare($query);

    if ($sql === false) {
        die("Prepare failed: " . $conn->error);
    }

    $sql->bind_param('sssssssssssi', $name, $contact, $email, $experience, $domain, $designation, $graduation, $skills, $area_of_interest, $location, $mentor_support, $_SESSION['user_id']);
    
    $sql->execute();

    header('Location: faculty_dashboard.php');
    exit;
}
?>
