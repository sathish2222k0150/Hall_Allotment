<?php
session_start();
require('fpdf.php');
require('db_connection.php'); // Include the common connection file

// Fetch students allocated in room_allocations
function getAllocatedStudents() {
    $conn = getDatabaseConnection();
    $sql = "SELECT students.reg_no, students.dept, students.year, room_allocations.room_number
            FROM students
            JOIN room_allocations ON 
                students.reg_no BETWEEN room_allocations.start_register_number AND room_allocations.end_register_number
                AND students.dept = room_allocations.dept
                AND students.year = room_allocations.year";
    $result = $conn->query($sql);

    $students = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
    }

    $conn->close();
    return $students;
}

// Fetch teachers and their associated rooms
function getTeachers() {
    $conn = getDatabaseConnection();
    $sql = "SELECT room_number, teacher_name FROM teachers";
    $result = $conn->query($sql);

    $teachers = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $teachers[$row['room_number']] = $row['teacher_name'];
        }
    }

    $conn->close();
    return $teachers;
}

// Fetch available room numbers and their seat limits
function getRoomNumbers() {
    $conn = getDatabaseConnection();
    $sql = "SELECT room_number, seats FROM rooms";
    $result = $conn->query($sql);

    $roomData = [];
    if ($result && $result->num_rows > 0) {
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

// Shuffle and allocate students to rooms
function shuffleAndAllocate($students, $roomData) {
    $allocatedRooms = [];
    shuffle($students); // Shuffle all students once

    foreach ($roomData as $roomInfo) {
        $roomNumber = $roomInfo['room_number'];
        $seatsPerRoom = $roomInfo['seats'];

        // Allocate students to the room
        $allocatedRooms[$roomNumber] = array_splice($students, 0, $seatsPerRoom);
        if (empty($students)) {
            break;
        }
    }

    return $allocatedRooms;
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

// Main Code Execution
$students = getAllocatedStudents();
$totalStudents = count($students);
$teachers = getTeachers();
$roomData = getRoomNumbers();

if (!isset($_SESSION['rooms'])) {
    $_SESSION['rooms'] = shuffleAndAllocate($students, $roomData); // Allocate rooms to students
}

$rooms = $_SESSION['rooms']; // Get allocated rooms from session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['room_index'])) {
        $roomIndex = $_POST['room_index'];
        $roomNumber = $roomData[$roomIndex]['room_number'];
        $room = $rooms[$roomNumber] ?? [];
        $teacherName = $teachers[$roomNumber] ?? 'Not Assigned';
        saveAllocationForRoom($room, $roomNumber, $teacherName);
    } elseif (isset($_POST['shuffle'])) {
        $_SESSION['rooms'] = shuffleAndAllocate($students, $roomData); // Shuffle the rooms
        $rooms = $_SESSION['rooms']; // Update the rooms variable
    } elseif (isset($_POST['search_reg_no'])) {
        $searchRegNo = $_POST['search_reg_no'];
        $found = false;

        foreach ($rooms as $roomNumber => $room) {
            foreach ($room as $seat => $student) {
                if ($student['reg_no'] === $searchRegNo) {
                    $searchResult = [
                        'room_number' => $roomNumber,
                        'seat_number' => $seat + 1,
                    ];
                    $found = true;
                    break 2;
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
         
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            color: #333;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }

        h2, h3 {
            text-align: center;
            color: #5A90EA;
        }

        h3 {
            margin-top: 20px;
            font-size: 1.5em;
            color: #5A90EA;
        }

        /* Form Styles */
        form {
            max-width: 500px;
            margin: 20px auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        form input[type="text"],
        form input[type="submit"],
        form input[type="hidden"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
        }

        form input[type="submit"] {
            background-color: #5A90EA;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        form input[type="submit"]:hover {
            background-color: #2FCCF8;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px auto;
            max-width: 900px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        td {
            font-size: 14px;
        }

        /* Button Styles */
        form input[type="submit"][value="Shuffle Rooms"],
        form input[type="submit"][value="Download Allocations as PDF"],
        form input[type="submit"][value="Search"] {
            background-color: #FA8D3D;
            color: #fff;
            font-size: 16px;
            margin-top: 10px;
            width: auto;
            padding: 8px 20px;
            transition: background-color 0.3s ease;
        }

        form input[type="submit"][value="Shuffle Rooms"]:hover,
        form input[type="submit"][value="Download Allocations as PDF"]:hover,
        form input[type="submit"][value="Search"]:hover {
            background-color: #F76E53;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            table {
                font-size: 14px;
            }

            th, td {
                padding: 8px;
            }

            form input[type="text"],
            form input[type="submit"] {
                font-size: 14px;
            }
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

<?php if (isset($searchResult)): ?>
    <h3>Search Result:</h3>
    <?php if ($found): ?>
        <p>Register No: <strong><?php echo htmlspecialchars($searchRegNo); ?></strong> found in Room <strong><?php echo htmlspecialchars($searchResult['room_number']); ?></strong>, Seat No <strong><?php echo htmlspecialchars($searchResult['seat_number']); ?></strong>.</p>
    <?php else: ?>
        <p>Register No: <strong><?php echo htmlspecialchars($searchRegNo); ?></strong> not found.</p>
    <?php endif; ?>
<?php endif; ?>

<?php
foreach ($rooms as $roomNumber => $room) {
    $teacherName = $teachers[$roomNumber] ?? 'Teacher Not Assigned';

    echo '<h3>Room ' . htmlspecialchars($roomNumber) . ' (' . htmlspecialchars($teacherName) . ')</h3>';
    echo '<form method="post">';
    echo '<input type="hidden" name="room_index" value="' . array_search($roomNumber, array_column($roomData, 'room_number')) . '">';
    echo '<input type="submit" value="Download Allocations as PDF">';
    echo '</form>';
    echo '<table>';
    echo '<tr><th>Seat No</th><th>Register No</th><th>Department</th><th>Year</th></tr>';

    foreach ($room as $seat => $student) {
        echo '<tr>';
        echo '<td>' . ($seat + 1) . '</td>';
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
