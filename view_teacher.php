<?php
session_start();

// Ensure the user is logged in as a teacher
if ($_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

// Database connection
$mysqli = new mysqli("localhost", "root", "", "exam_hall");

// Check for connection errors
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fetch all teacher details
$stmt = $mysqli->prepare("SELECT teacher_id, teacher_name, room_number FROM teachers");
$stmt->execute();
$result = $stmt->get_result();

// Display the details of all teachers
echo "<h1>All Teachers' Details:</h1>";

if ($result->num_rows > 0) {
    // Output data of each row
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Name</th><th>Assigned Room</th></tr>"; // Table headers

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['teacher_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['teacher_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['room_number'] ? $row['room_number'] : 'No room assigned') . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p>No teachers found.</p>";
}

// Close the statement and connection
$stmt->close();
$mysqli->close();
?>
