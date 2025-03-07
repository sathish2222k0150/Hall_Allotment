<?php
session_start();  // Start the session

require('fpdf.php');

// Connect to MySQL and fetch all students
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
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
    }

    $conn->close();
    return $students;
}

// Fetch teachers and their associated rooms
function getTeachers() {
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

    // Fetch teachers
    $sql = "SELECT room_number, teacher_name FROM teachers";
    $result = $conn->query($sql);

    $teachers = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $teachers[$row['room_number']] = $row['teacher_name'];
        }
    }

    $conn->close();
    return $teachers;
}

// Fetch available room numbers and their seat limits
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

    // Fetch room numbers and seat limits
    $sql = "SELECT room_number, seats FROM rooms";
    $result = $conn->query($sql);

    $roomData = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $roomData[] = [
                'room_number' => $row['room_number'],
                'seats' => $row['seats']
            ];
        }
    }

    $conn->close();
    return $roomData;
}

// Save room allocation as a PDF for a specific room
function saveAllocationForRoom($room, $roomNumber, $teacherName) {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Room ' . $roomNumber . ' Allocation', 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->SetFont('Arial', '', 12);
    
    $pdf->Cell(0, 10, 'Teacher: ' . ($teacherName ?: 'Not Assigned'), 0, 1);
    $pdf->Ln(5);

    $pdf->SetFillColor(200, 220, 255);
    $pdf->Cell(30, 10, 'Seat No', 1, 0, 'C', true);
    $pdf->Cell(50, 10, 'Register No', 1, 0, 'C', true);
    $pdf->Cell(50, 10, 'Department', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'Year', 1, 1, 'C', true);

    foreach ($room as $seat => $student) {
        $pdf->Cell(30, 10, ($seat + 1), 1);
        $pdf->Cell(50, 10, $student['reg_no'], 1);
        $pdf->Cell(50, 10, $student['dept'], 1);
        $pdf->Cell(30, 10, $student['year'], 1);
        $pdf->Ln();
    }

    $pdf->Output('D', 'room_' . $roomNumber . '_allocations.pdf');
    exit();
}

// Shuffle and allocate students to rooms while respecting seat limits
function shuffleAndAllocate($students, $roomData) {
    shuffle($students);

    $allocatedRooms = [];
    $studentIndex = 0;

    foreach ($roomData as $roomInfo) {
        $roomNumber = $roomInfo['room_number'];
        $seatsPerRoom = $roomInfo['seats'];
        $room = [];

        // Fill the room up to its seat limit
        for ($i = 0; $i < $seatsPerRoom && $studentIndex < count($students); $i++) {
            $room[] = $students[$studentIndex++];
        }

        $allocatedRooms[] = $room;

        // Stop if all students have been allocated
        if ($studentIndex >= count($students)) {
            break;
        }
    }

    return $allocatedRooms;
}

// Main code execution
$students = getStudents();
$teachers = getTeachers();
$roomData = getRoomNumbers();

if (!isset($_SESSION['rooms'])) {
    $_SESSION['rooms'] = shuffleAndAllocate($students, $roomData); // Allocate rooms to students
}

$rooms = $_SESSION['rooms']; // Get allocated rooms from session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['room_index'])) {
        $roomIndex = $_POST['room_index'];
        $room = $rooms[$roomIndex];
        $roomNumber = $roomData[$roomIndex]['room_number'];
        $teacherName = $teachers[$roomNumber] ?? 'Not Assigned';
        saveAllocationForRoom($room, $roomNumber, $teacherName);
    } elseif (isset($_POST['shuffle'])) {
        $_SESSION['rooms'] = shuffleAndAllocate($students, $roomData); // Shuffle the rooms
        $rooms = $_SESSION['rooms']; // Update the rooms variable
    } elseif (isset($_POST['search_reg_no'])) {
        $searchRegNo = $_POST['search_reg_no'];
        $found = false;

        foreach ($rooms as $roomIndex => $room) {
            foreach ($room as $seat => $student) {
                if ($student['reg_no'] === $searchRegNo) {
                    $searchResult = [
                        'room_number' => $roomData[$roomIndex]['room_number'],
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
    <title>Room Allocation</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<h2>Room Allocations</h2>

<!-- Shuffle Button -->
<form method="post">
    <input type="submit" name="shuffle" value="Shuffle Rooms">
</form>

<!-- Search Bar -->
<form method="post">
    <input type="text" name="search_reg_no" placeholder="Enter Register No" required>
    <input type="submit" value="Search">
</form>

<?php
if (isset($searchResult)): ?>
    <h3>Search Result:</h3>
    <?php if ($found): ?>
        <p>Register No: <strong><?php echo htmlspecialchars($searchRegNo); ?></strong> found in Room <strong><?php echo htmlspecialchars($searchResult['room_number']); ?></strong>, Seat No <strong><?php echo htmlspecialchars($searchResult['seat_number']); ?></strong>.</p>
    <?php else: ?>
        <p>Register No: <strong><?php echo htmlspecialchars($searchRegNo); ?></strong> not found.</p>
    <?php endif; ?>
<?php endif; ?>

<?php
// Display room allocations
foreach ($rooms as $roomIndex => $room) {
    $roomNumber = $roomData[$roomIndex]['room_number'];
    $teacherName = $teachers[$roomNumber] ?? 'Teacher Not Assigned';
    
    echo '<h3>Room ' . htmlspecialchars($roomNumber) . ' (' . htmlspecialchars($teacherName) . ')</h3>';
    echo '<form method="post" action="">';
    echo '<input type="hidden" name="room_index" value="' . $roomIndex . '">';
    echo '<input type="submit" value="Download Allocations as PDF">';
    echo '</form>';
    
    echo '<table>';
    echo '<tr><th>Seat No</th><th>Register No</th><th>Department</th><th>Year</th></tr>';
    
    foreach ($room as $seatIndex => $student) {
        echo '<tr>';
        echo '<td>' . ($seatIndex + 1) . '</td>';
        echo '<td>' . htmlspecialchars($student['reg_no']) . '</td>';
        echo '<td>' . htmlspecialchars($student['dept']) . '</td>';
        echo '<td>' . htmlspecialchars($student['year']) . '</td>';
        echo '</tr>';
    }
    
    echo '</table>';
}
?>

</body>
</html>
