<?php

// Fetch all students
function getStudents() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "exam_hall";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch all students
    $sql = "SELECT reg_no, dept, year FROM students";
    $result = $conn->query($sql);

    $students = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Ensure that reg_no is set
            if (isset($row['reg_no'])) {
                $students[] = $row;
            }
        }
    } else {
        die("Query failed: " . $conn->error); // Display error if query fails
    }

    $conn->close();
    return $students;
}

// Fetch all room numbers
function getRoomNumbers() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "exam_hall";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch all room numbers
    $sql = "SELECT room_number FROM rooms"; // Adjust this query based on your table structure
    $result = $conn->query($sql);

    // Check if the query was successful
    if (!$result) {
        die("Query failed: " . $conn->error); // Display error if query fails
    }

    $roomNumbers = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $roomNumbers[] = $row['room_number']; // Assuming 'room_number' is the column name
        }
    }

    $conn->close();
    return $roomNumbers;
}

// Add your getTeachers function or logic here, if needed

// Shuffle and allocate rooms (function not provided, implement as needed)
function shuffleAndAllocate($students, $roomNumbers) {
    // Your room allocation logic here
    return []; // Placeholder for allocated rooms
}

// Save allocation for a room (function not provided, implement as needed)
function saveAllocationForRoom($room, $roomNumber, $teacherName) {
    // Your save logic here
}

// Main code execution
$students = getStudents();
$roomNumbers = getRoomNumbers();

if (!isset($_SESSION['rooms'])) {
    $_SESSION['rooms'] = shuffleAndAllocate($students, $roomNumbers); // Allocate rooms to students
}

$rooms = $_SESSION['rooms']; // Get allocated rooms from session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['room_index'])) {
        $roomIndex = $_POST['room_index'];
        $room = $rooms[$roomIndex];
        $teacherName = ''; // Replace with actual logic to get teacher name
        saveAllocationForRoom($room, $roomNumbers[$roomIndex], $teacherName);
    } elseif (isset($_POST['shuffle'])) {
        $_SESSION['rooms'] = shuffleAndAllocate($students, $roomNumbers); // Shuffle the rooms
        $rooms = $_SESSION['rooms']; // Update the rooms variable
    } elseif (isset($_POST['search_reg_no'])) {
        $searchRegNo = $_POST['search_reg_no'];
        $found = false;

        foreach ($rooms as $roomIndex => $room) {
            foreach ($room as $seat => $student) {
                // Ensure that reg_no is being checked
                if (isset($student['reg_no']) && $student['reg_no'] === $searchRegNo) {
                    $searchResult = [
                        'room_number' => $roomNumbers[$roomIndex],
                        'seat_number' => $seat + 1,
                    ];
                    $found = true;
                    break 2; // Break out of both loops
                }
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link your CSS file -->
</head>
<body>
    <h1>Welcome, Student!</h1>
    
    <form method="post" action="">
        <input type="text" name="search_reg_no" placeholder="Enter Registration Number" required>
        <button type="submit">Search</button>
    </form>

    <?php if (isset($searchResult)): ?>
        <div>
            <p>Student found in Room: <?php echo htmlspecialchars($searchResult['room_number']); ?>, Seat: <?php echo htmlspecialchars($searchResult['seat_number']); ?></p>
        </div>
    <?php elseif (isset($found) && !$found): ?>
        <p>Student not found.</p>
    <?php endif; ?>

    <h2>Allocated Rooms</h2>
    <!-- Display allocated rooms and options here -->
    
</body>
</html>
