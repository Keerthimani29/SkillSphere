<?php
require_once 'db.php';
header('Content-Type: application/json');

try {
    $sql = "SELECT * FROM roadmaps ORDER BY id DESC";
    $result = $conn->query($sql);
    
    $roadmaps = array();
    while ($row = $result->fetch_assoc()) {
        $roadmaps[] = $row;
    }
    
    echo json_encode($roadmaps);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>