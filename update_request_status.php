<?php
include('db.php');
session_start();

header('Content-Type: application/json');

if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'faculty') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'];
        $request_id = $_POST['request_id'];

        $status = ($action === 'accept') ? 4 : (($action === 'reject') ? 5 : null);

        if ($status !== null) {
            $query = "UPDATE mentor_requests SET status = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $status, $request_id);

            if ($stmt->execute()) {
                echo json_encode([
                    'status' => 'success',
                    'updated_status' => $status
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => $stmt->error
                ]);
            }

            $stmt->close();
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid action.'
            ]);
        }
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access.'
    ]);
}

$conn->close();
?>
