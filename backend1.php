<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // These names MUST match your form input names
    $mentor = $_POST["mentor_name"];
    $topic = $_POST["topic"];
    $date = $_POST["session_date"];
    $time = $_POST["session_time"];
    $link = $_POST["meeting_link"];

    $stmt = $conn->prepare("INSERT INTO sessions (mentor_name, topic, session_date, session_time, meeting_link) VALUES (?, ?, ?, ?, ?)");
    
    if ($stmt === false) {
        die("MySQL prepare error: " . $conn->error);
    }

    $stmt->bind_param("sssss", $mentor, $topic, $date, $time, $link);
    $stmt->execute();
    $stmt->close();

    header("Location: livesessions.php");
    exit;
}
?>
