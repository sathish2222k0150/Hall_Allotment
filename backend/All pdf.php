<?php
session_start();  // Start the session

require('fpdf.php');

// Get the `seatsPerRoom` and allocated rooms from session
$seatsPerRoom = $_SESSION['seats_per_room'] ?? 35;
$rooms = $_SESSION['rooms'];

// Connect to MySQL
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

// Fetch available room numbers
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

    // Fetch room numbers
    $sql = "SELECT room_number FROM rooms";
    $result = $conn->query($sql);

    $roomNumbers = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $roomNumbers[] = $row['room_number'];
        }
    }

    $conn->close();
    return $roomNumbers;
}

// Save all allocations as a single PDF
function saveAllAllocationsAsPDF($rooms, $roomNumbers, $teachers) {
    $pdf = new FPDF();
    $pdf->SetFont('Arial', 'B', 16);

    foreach ($rooms as $roomIndex => $room) {
        $pdf->AddPage();

        // Teacher and Room Information at the top right
        $teacherName = $teachers[$roomNumbers[$roomIndex]] ?? 'Not Assigned';
        $pdf->SetXY(150, 10); // Set position for the teacher and room number
        $pdf->Cell(0, 10, 'Room: ' . $roomNumbers[$roomIndex], 0, 1, 'R');
        $pdf->Cell(0, 10, 'Teacher: ' . $teacherName, 0, 1, 'R');

        $pdf->Ln(10); // Add some space after the header

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
    }

    $pdf->Output('D', 'all_room_allocations.pdf');
    exit();
}

// Shuffle and allocate students to rooms while avoiding consecutive seats with the same first four digits
function shuffleAndAllocate($students, $roomNumbers) {
    // Shuffle the students initially
    shuffle($students);

    $allocatedRooms = [];
    $room = [];

    foreach ($students as $student) {
        // Check if the room has reached the seat limit
        if (count($room) < 35) {
            // Get the first four digits of the current student's register number
            $currentRegPrefix = substr($student['reg_no'], 0, 4);
            
            // If there's a student in the last seat, check their register number prefix
            if (!empty($room)) {
                $lastRegPrefix = substr(end($room)['reg_no'], 0, 4);
                
                // If the current student's prefix matches the last one, find a non-conflicting position
                if ($currentRegPrefix === $lastRegPrefix) {
                    $repositioned = false;

                    // Attempt to find a non-conflicting position in the remaining students
                    foreach ($students as $key => $potentialStudent) {
                        $potentialPrefix = substr($potentialStudent['reg_no'], 0, 4);
                        if ($potentialPrefix !== $lastRegPrefix) {
                            // Swap the current student with a non-conflicting one
                            $students[$key] = $student;
                            $student = $potentialStudent;
                            $repositioned = true;
                            break;
                        }
                    }

                    // If repositioning fails, reshuffle
                    if (!$repositioned) {
                        shuffle($students);
                        return shuffleAndAllocate($students, $roomNumbers);
                    }
                }
            }
            
            $room[] = $student; // Add the student to the room
        } else {
            // Store the completed room and start a new one
            $allocatedRooms[] = $room;
            $room = [$student];
        }
    }

    // Add the last room if it has any students
    if (!empty($room)) {
        $allocatedRooms[] = $room;
    }

    return $allocatedRooms;
}

// Main code execution
$students = getStudents();
$teachers = getTeachers();
$roomNumbers = getRoomNumbers();

if (!isset($_SESSION['rooms'])) {
    $_SESSION['rooms'] = shuffleAndAllocate($students, $roomNumbers); // Allocate rooms to students
}

$rooms = $_SESSION['rooms']; // Get allocated rooms from session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['download_all'])) {
        saveAllAllocationsAsPDF($rooms, $roomNumbers, $teachers); // Save all allocations as PDF
    } elseif (isset($_POST['shuffle'])) {
        $_SESSION['rooms'] = shuffleAndAllocate($students, $roomNumbers); // Shuffle the rooms
        $rooms = $_SESSION['rooms']; // Update the rooms variable
    } elseif (isset($_POST['search_reg_no'])) {
        $searchRegNo = $_POST['search_reg_no'];
        $found = false;

        foreach ($rooms as $roomIndex => $room) {
            foreach ($room as $seat => $student) {
                if ($student['reg_no'] === $searchRegNo) {
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
    <input type="submit" name="download_all" value="Download All Allocations as PDF">
</form>

<!-- Search Bar -->
<form method="post">
    <input type="text" name="search_reg_no" placeholder="Enter Register Number">
    <input type="submit" value="Search">
</form>

<?php if (isset($searchResult)): ?>
    <h3>Search Result</h3>
    <p>Register No: <?php echo $searchRegNo; ?> is in Room: <?php echo $searchResult['room_number']; ?>, Seat No: <?php echo $searchResult['seat_number']; ?></p>
<?php endif; ?>

<table>
    <tr>
        <th>Room Number</th>
        <th>Seat Number</th>
        <th>Register No</th>
        <th>Department</th>
        <th>Year</th>
    </tr>
    <?php foreach ($rooms as $roomIndex => $room): ?>
        <?php foreach ($room as $seat => $student): ?>
            <tr>
                <td><?php echo $roomNumbers[$roomIndex]; ?></td>
                <td><?php echo $seat + 1; ?></td>
                <td><?php echo $student['reg_no']; ?></td>
                <td><?php echo $student['dept']; ?></td>
                <td><?php echo $student['year']; ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
</table>

</body>
</html>
