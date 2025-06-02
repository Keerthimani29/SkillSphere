<?php
include('db.php');
session_start();

$result = null; // Initialize $result to avoid undefined variable warnings
$stmt = null;   // Initialize $stmt to avoid undefined variable warnings

if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'faculty') {
    $faculty_id = $_SESSION['user_id'];

    // Fetch mentor requests
    $query = "SELECT id, student_id, request, status 
              FROM mentor_requests 
              WHERE faculty_id = ? AND status IN (3, 4, 5)";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $faculty_id);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}
?>

<html>
    <head>
        <title>Faculty Dashboard</title>
         <link rel="stylesheet" href="roadmap.css"> 
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
            .status-accepted {
                color: green;
                font-weight: bold;
            }
            .status-rejected {
                color: red;
                font-weight: bold;
            }
        </style>
        <script>
            async function handleRequest(action, requestId, studentId) {
                if (action === 'accept' || action === 'reject') {
                    try {
                        const formData = new FormData();
                        formData.append('action', action);
                        formData.append('request_id', requestId);

                        const response = await fetch('update_request_status.php', {
                            method: 'POST',
                            body: formData
                        });

                        const data = await response.json();

                        if (data.status === 'success') {
                            const buttonContainer = document.querySelector(`[data-request-id="${requestId}"] .button-container`);
                            if (buttonContainer) {
                                if (data.updated_status === 4) {
                                    buttonContainer.innerHTML = '<p class="status-accepted">Accepted</p><button class="open-modal-btn" onclick="openRoadmapModal(' + studentId + ', <?php echo $faculty_id; ?>)">Create Roadmap</button>';
                                } else if (data.updated_status === 5) {
                                    buttonContainer.innerHTML = '<p class="status-rejected">Rejected</p>';
                                }
                            }
                        } else {
                            alert('Error: ' + data.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('An error occurred while updating the request.');
                    }
                }
            }

            function openRoadmapModal(studentId, facultyId) {
                document.getElementById('mentee_id').value = studentId;
                document.getElementById('mentor_id').value = facultyId;
                document.getElementById('roadmapModal').style.display = 'flex';
            }
        </script>
    </head>
    <body>
        <h1>Faculty Dashboard</h1>
        <div id="mentorRequests">
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="card" data-request-id="' . $row['id'] . '">';
                    echo '<h3>Student ID: ' . htmlspecialchars($row['student_id']) . '</h3>';
                    echo '<p>Request: ' . htmlspecialchars($row['request']) . '</p>';
                    echo '<div class="button-container">';
                    if (isset($row['status']) && $row['status'] == 3) { // Pending
                        echo '<button onclick="handleRequest(\'accept\', ' . $row['id'] . ', ' . $row['student_id'] . ')">Accept</button>';
                        echo '<button onclick="handleRequest(\'reject\', ' . $row['id'] . ')">Reject</button>';
                    } elseif (isset($row['status']) && $row['status'] == 4) { // Accepted
                        echo '<p class="status-accepted">Accepted</p>';
                        echo '<button class="open-modal-btn" onclick="openRoadmapModal(' . $row['student_id'] . ', ' . $faculty_id . ')">Create Roadmap</button>';
                    } elseif (isset($row['status']) && $row['status'] == 5) { // Rejected
                        echo '<p class="status-rejected">Rejected</p>';
                    }
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No mentor requests found.</p>';
            }

            if ($stmt) {
                $stmt->close();
            }
            $conn->close();
            ?>
        </div>

        

    <!-- Main Content -->
   

    <!-- Modal Structure -->
    <div class="modal-overlay" id="roadmapModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Create Learning Roadmap</h3>
                <span class="close-modal">&times;</span>
            </div>
            <div class="modal-body">
                <form id="roadmapForm">
                    <input type="hidden" id="mentee_id" value="456">
                    <input type="hidden" id="mentor_id" value="123">

                    <div class="form-group">
                        <label for="topic">Topic/Subject:</label>
                        <input type="text" id="topic" required>
                    </div>

                    <div class="form-group">
                        <label for="concepts">Key Concepts:</label>
                        <textarea id="concepts" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="materials">Reference Links:</label>
                        <textarea id="materials"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="uploads">Resource Files:</label>
                        <div class="file-upload-container">
                            <input type="file" id="fileUpload" style="display: none;">
                            <button type="button" class="upload-btn" onclick="document.getElementById('fileUpload').click()">Upload File</button>
                            <span id="fileName" class="file-name"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="duration">Estimated Duration:</label>
                        <select id="duration" name="duration" required>
                            <option value="3 days">3 days</option>
                            <option value="4 days">4 days</option>
                            <option value="5 days">5 days</option>
                            <option value="1 week">1 week</option>
                            <option value="2 weeks">2 weeks</option>
                            <option value="3 weeks">3 weeks</option>
                            <option value="1 month">1 month</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-cancel">Cancel</button>
                        <button type="submit" class="btn-submit">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        // Modal Control
        const modal = document.getElementById('roadmapModal');
        const openBtn = document.querySelector('.open-modal-btn');
        const closeBtn = document.querySelector('.close-modal');
        const cancelBtn = document.querySelector('.btn-cancel');

        // Only opens when button clicked
        openBtn.addEventListener('click', () => {
            modal.style.display = 'flex';
        });

        // Close handlers
        function closeModal() {
            modal.style.display = 'none';
        }

        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        // Close when clicking outside modal
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
        document.getElementById('fileUpload').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            document.getElementById('fileName').textContent = fileName || '';
        });

        // Form submission
        document.getElementById('roadmapForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Roadmap created!'); // Replace with actual submission
            closeModal();
        });

        document.getElementById('roadmapForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    
    // Add form fields
    formData.append('mentee_id', document.getElementById('mentee_id').value);
    formData.append('mentor_id', document.getElementById('mentor_id').value);
    formData.append('topic', document.getElementById('topic').value);
    formData.append('concepts', document.getElementById('concepts').value);
    formData.append('materials', document.getElementById('materials').value);
    formData.append('duration', document.getElementById('duration').value);
    
    // Add file if selected
    const fileInput = document.getElementById('fileUpload');
    if (fileInput.files[0]) {
        formData.append('file', fileInput.files[0]);
    }
    
    try {
        const response = await fetch('backend.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        if (data.status === 'success') {
            alert('Roadmap created successfully!');
            closeModal();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while creating the roadmap');
    }
});


  
        // Ensure DOM is loaded before running fetchRoadmaps
        document.addEventListener('DOMContentLoaded', function() {
            async function fetchRoadmaps() {
                try {
                    const response = await fetch('fetch_roadmaps.php');
                    const data = await response.json();
                    
                    const container = document.getElementById('roadmapsContainer');
                    if (!container) {
                        console.error('Container element not found');
                        return;
                    }
                    
                    container.innerHTML = ''; // Clear existing content
                    
                    data.forEach(roadmap => {
                        const card = `
                            <div class="roadmap-card">
                                <div class="roadmap-title">${roadmap.topic}</div>
                                <div class="roadmap-info">
                                    <strong>Created by:</strong> Mentor ID ${roadmap.mentor_id}
                                </div>
                                <div class="roadmap-concepts">
                                    <strong>Key Concepts:</strong><br>
                                    ${roadmap.concepts}
                                </div>
                                <div class="roadmap-info">
                                    <strong>Reference Materials:</strong><br>
                                    ${roadmap.materials}
                                </div>
                                ${roadmap.file_path ? `
                                    <div class="roadmap-file">
                                        <span class="file-icon">📎</span>
                                        <a href="${roadmap.file_path}" target="_blank">View Resource</a>
                                    </div>
                                ` : ''}
                                <div class="duration-badge">
                                    Duration: ${roadmap.duration}
                                </div>
                            </div>
                        `;
                        container.innerHTML += card;
                    });
                } catch (error) {
                    console.error('Error fetching roadmaps:', error);
                }
            }

            // Initial fetch
            fetchRoadmaps();

            // Refresh after form submission
            document.getElementById('roadmapForm').addEventListener('submit', async function(e) {
                // ...existing form submission code...
                if (data.status === 'success') {
                    alert('Roadmap created successfully!');
                    closeModal();
                    fetchRoadmaps(); // Refresh the roadmaps
                }
            });
        });
    
    </script>


    </body>
</html>