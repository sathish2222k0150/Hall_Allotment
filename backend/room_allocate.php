<?php
require 'db_connection.php'; // Include the common connection file

$pdo = getPDOConnection(); // Get the PDO connection

// Fetch unique departments and years from the students table
$deptQuery = $pdo->query("SELECT DISTINCT dept FROM students ORDER BY dept ASC");
$yearQuery = $pdo->query("SELECT DISTINCT year FROM students ORDER BY year ASC");

// Fetch all register numbers with their corresponding departments and years
$regNoQuery = $pdo->query("SELECT reg_no, dept, year FROM students ORDER BY reg_no ASC");

// Fetch all room numbers from the rooms table
$roomQuery = $pdo->query("SELECT room_number FROM rooms ORDER BY room_number ASC");

$departments = $deptQuery->fetchAll(PDO::FETCH_COLUMN);
$years = $yearQuery->fetchAll(PDO::FETCH_COLUMN);
$registerNumbers = $regNoQuery->fetchAll(PDO::FETCH_ASSOC);
$roomDetails = $roomQuery->fetchAll(PDO::FETCH_COLUMN); // Fetch room numbers as an array

// Check if the form is submitted to add a room with ranges
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['allocate_seats'])) {
    $departmentsData = $_POST['departments'];

    foreach ($departmentsData as $deptData) {
        $roomNumber = trim($deptData['room_number']);
        $department = trim($deptData['dept']);
        $year = (int)$deptData['year'];
        $startRegisterNumber = trim($deptData['start_register_number']);
        $endRegisterNumber = trim($deptData['end_register_number']);

        if (!empty($startRegisterNumber) && !empty($endRegisterNumber) && !empty($department) && $year > 0) {
            $stmt = $pdo->prepare("INSERT INTO room_allocations (room_number, start_register_number, end_register_number, dept, year) 
                                   VALUES (:room_number, :start_register_number, :end_register_number, :dept, :year)");
            $stmt->bindParam(':room_number', $roomNumber);
            $stmt->bindParam(':start_register_number', $startRegisterNumber);
            $stmt->bindParam(':end_register_number', $endRegisterNumber);
            $stmt->bindParam(':dept', $department);
            $stmt->bindParam(':year', $year);
            $stmt->execute();
        } else {
            echo "<script>alert('Please fill in all required fields for each department.');</script>";
        }
    }

    echo "<script>alert('Seat allocation completed successfully!');</script>";
}

// Handle deletion of a specific allocation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_allocation'])) {
    $allocationId = (int)$_POST['allocation_id'];
    $stmt = $pdo->prepare("DELETE FROM room_allocations WHERE id = :id");
    $stmt->bindParam(':id', $allocationId);
    $stmt->execute();
    echo "<script>alert('Allocation deleted successfully!');</script>";
}

// Fetch all room allocations
$allocationQuery = $pdo->query("SELECT id, room_number, start_register_number, end_register_number, dept, year FROM room_allocations ORDER BY room_number ASC");
$allocationDetails = $allocationQuery->fetchAll(PDO::FETCH_ASSOC);
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
            margin: 20px;
            background-color: #f9f9f9;
            color: #333;
        }

        h2 {
            color: #444;
            border-bottom: 2px solid #f76e53;
            padding-bottom: 10px;
        }

        /* Form Styles */
        form {
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        label {
            font-weight: bold;
            margin-right: 10px;
        }

        select, input[type="button"], input[type="submit"] {
            padding: 8px 10px;
            margin: 5px 0 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            width: 100%;
            box-sizing: border-box;
        }

        input[type="button"], input[type="submit"] {
            background-color: #5a90ea;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="button"]:hover, input[type="submit"]:hover {
            background-color: #2fccf8;
        }

        .departmentEntry {
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #fa8d3d;
            color: white;
            font-size: 16px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        input[type="submit"][name="delete_allocation"] {
            background-color: #f76e53;
            border: none;
            padding: 5px 5px;
            color: white;
            border-radius: 3px;
            cursor: pointer;
        }

        input[type="submit"][name="delete_allocation"]:hover {
            background-color: #e05a3b;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            table, th, td {
                font-size: 12px;
            }

            form {
                padding: 15px;
            }

            select, input[type="button"], input[type="submit"] {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>

<h2>Allocate Seats to Departments</h2>
<form method="POST" id="departmentForm">
    <div id="departmentContainer">
        <div class="departmentEntry">
            <label for="room_number">Select Room Number:</label>
            <select name="departments[0][room_number]" id="room_number" required>
                <option value="">--Select Room--</option>
                <?php foreach ($roomDetails as $roomNumber): ?>
                    <option value="<?php echo htmlspecialchars($roomNumber); ?>">
                        <?php echo htmlspecialchars($roomNumber); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="dept">Select Department:</label>
            <select name="departments[0][dept]" required>
                <option value="">--Select Department--</option>
                <?php foreach ($departments as $department): ?>
                    <option value="<?php echo htmlspecialchars($department); ?>"><?php echo htmlspecialchars($department); ?></option>
                <?php endforeach; ?>
            </select>

            <label for="year">Select Year:</label>
            <select name="departments[0][year]" required>
                <option value="">--Select Year--</option>
                <?php foreach ($years as $year): ?>
                    <option value="<?php echo htmlspecialchars($year); ?>"><?php echo htmlspecialchars($year); ?></option>
                <?php endforeach; ?>
            </select>

            <label for="start_register_number">Start Register Number:</label>
            <select name="departments[0][start_register_number]" required></select>

            <label for="end_register_number">End Register Number:</label>
            <select name="departments[0][end_register_number]" required></select>
        </div>
    </div>
    <input type="button" value="Add Department" onclick="addDepartmentEntry()">
    <input type="submit" name="allocate_seats" value="Allocate Seats">
</form>

<h2>Allocated Seats</h2>
<table>
    <thead>
        <tr>
            <th>Room Number</th>
            <th>Department</th>
            <th>Year</th>
            <th>Start Register Number</th>
            <th>End Register Number</th>
            <th>Length (Number of Students)</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Assume $allocationDetails is already fetched with the allocation data.
        foreach ($allocationDetails as $allocation): 
            // Extract the start and end register numbers (as strings, since reg_no is varchar)
            $startReg = $allocation['start_register_number']; 
            $endReg = $allocation['end_register_number'];

            // Fetch the room_number from the rooms table based on the room_number in the allocation
            $stmt = $conn->prepare("SELECT room_number FROM rooms WHERE room_number = ?");
            if ($stmt === false) {
                die('MySQL prepare error: ' . $conn->error . ' - Query: SELECT room_number FROM rooms WHERE room_number = ?');
            }
            $stmt->bind_param("s", $allocation['room_number']); // Bind room_number to fetch the room_number
            $stmt->execute();
            $result = $stmt->get_result();

            // Fetch the room number from the query result
            $roomNumber = '';
            if ($row = $result->fetch_assoc()) {
                $roomNumber = $row['room_number']; // Fetch the room number
            }

            // Fetch register numbers from the students table for the given room and the range of register numbers
            $stmt = $conn->prepare("SELECT reg_no FROM students WHERE dept = ? AND year = ? AND reg_no BETWEEN ? AND ?");
            if ($stmt === false) {
                die('MySQL prepare error: ' . $conn->error . ' - Query: SELECT reg_no FROM students WHERE dept = ? AND year = ? AND reg_no BETWEEN ? AND ?');
            }

            // Bind department, year, and register number range
            $stmt->bind_param("siss", $allocation['dept'], $allocation['year'], $startReg, $endReg); 
            $stmt->execute();
            $result = $stmt->get_result();

            // Initialize an array to hold the allocated register numbers
            $allocatedRegisters = [];

            // Fetch all register numbers within the range
            while ($row = $result->fetch_assoc()) {
                $allocatedRegisters[] = $row['reg_no']; // Store each reg_no in the array
            }

            // Calculate the length (number of students) based on the allocated registers
            $length = count($allocatedRegisters); // Count of allocated register numbers
        ?>
        <tr>
            <td><?php echo htmlspecialchars($roomNumber); ?></td> <!-- Display room number fetched from rooms table -->
            <td><?php echo htmlspecialchars($allocation['dept']); ?></td>
            <td><?php echo htmlspecialchars($allocation['year']); ?></td>
            <td><?php echo htmlspecialchars($startReg); ?></td>
            <td><?php echo htmlspecialchars($endReg); ?></td>
            <td><?php echo $length; ?> Students</td> <!-- Displaying the length -->
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="allocation_id" value="<?php echo htmlspecialchars($allocation['id']); ?>">
                    <input type="submit" name="delete_allocation" value="Delete">
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const regNoData = <?php echo json_encode($registerNumbers); ?>;

        function updateRegisterOptions(deptSelect, yearSelect, startRegSelect, endRegSelect) {
            const selectedDept = deptSelect.value;
            const selectedYear = yearSelect.value;
            const filteredRegNos = regNoData.filter(reg => reg.dept === selectedDept && reg.year === selectedYear);

            startRegSelect.innerHTML = '<option value="">--Select Start Register Number--</option>';
            endRegSelect.innerHTML = '<option value="">--Select End Register Number--</option>';

            filteredRegNos.forEach(reg => {
                const optionStart = document.createElement('option');
                optionStart.value = reg.reg_no;
                optionStart.textContent = reg.reg_no;

                const optionEnd = optionStart.cloneNode(true);

                startRegSelect.appendChild(optionStart);
                endRegSelect.appendChild(optionEnd);
            });
        }

        document.getElementById('departmentContainer').addEventListener('change', function (e) {
            if (e.target.matches('select[name*="[dept]"], select[name*="[year]"]')) {
                const parent = e.target.closest('.departmentEntry');
                const deptSelect = parent.querySelector('select[name*="[dept]"]');
                const yearSelect = parent.querySelector('select[name*="[year]"]');
                const startRegSelect = parent.querySelector('select[name*="[start_register_number]"]');
                const endRegSelect = parent.querySelector('select[name*="[end_register_number]"]');
                updateRegisterOptions(deptSelect, yearSelect, startRegSelect, endRegSelect);
            }
        });
    });

    function addDepartmentEntry() {
        const container = document.getElementById('departmentContainer');
        const newEntry = document.querySelector('.departmentEntry').cloneNode(true);
        const entriesCount = container.children.length;

        Array.from(newEntry.querySelectorAll('select, input')).forEach(input => {
            const newName = input.name.replace(/\[0\]/, `[${entriesCount}]`);
            input.name = newName;
            input.value = '';
        });

        container.appendChild(newEntry);
    }
</script>

</body>
</html>
