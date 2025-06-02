<?php
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->begin_transaction();
        
        // Create uploads directory if it doesn't exist
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Handle form data
        $mentee_id = $conn->real_escape_string($_POST['mentee_id']);
        $mentor_id = $conn->real_escape_string($_POST['mentor_id']);
        $topic = $conn->real_escape_string($_POST['topic']);
        $concepts = $conn->real_escape_string($_POST['concepts']);
        $materials = $conn->real_escape_string($_POST['materials']);
        $duration = $conn->real_escape_string($_POST['duration']);
        
        $file_path = null;
        
        // Handle file upload if present
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['file'];
            $file_name = basename($file['name']);
            $file_path = $upload_dir . uniqid() . '_' . $file_name;
            
            if (!move_uploaded_file($file['tmp_name'], $file_path)) {
                throw new Exception("Failed to upload file");
            }
        }
        
        // Insert into roadmaps table with file path
        $sql = "INSERT INTO roadmaps (mentee_id, mentor_id, topic, concepts, materials, duration, file_path)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisssss", 
            $mentee_id, 
            $mentor_id, 
            $topic, 
            $concepts, 
            $materials, 
            $duration,
            $file_path
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error creating roadmap: " . $conn->error);
        }
        
        $conn->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'Roadmap created successfully'
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    } finally {
        $stmt->close();
        $conn->close();
    }
}
?>