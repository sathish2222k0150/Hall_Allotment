<?php
include 'db_connection.php';

// Fetch teacher name based on selected staff_id (AJAX request)
if (isset($_POST['action']) && $_POST['action'] == 'fetch_name') {
    $staffId = $_POST['staff_id'];
    $query = "SELECT teacher_name FROM teachers WHERE staff_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $staffId);
    $stmt->execute();
    $result = $stmt->get_result();
    $teacherName = ($result->num_rows > 0) ? $result->fetch_assoc()['teacher_name'] : '';
    echo json_encode(['teacher_name' => $teacherName]);
    exit;
}

// Handle AJAX add or update teacher
if (isset($_POST['action']) && $_POST['action'] == 'add_teacher') {
    $staffId = $_POST['staff_id'];
    $roomNumber = $_POST['room_number'];

    // Check if the room number is already assigned to another teacher
    $checkRoomQuery = "SELECT teacher_id FROM teachers WHERE room_number = ? AND staff_id != ?";
    $checkRoomStmt = $conn->prepare($checkRoomQuery);
    $checkRoomStmt->bind_param("ss", $roomNumber, $staffId);
    $checkRoomStmt->execute();
    $checkRoomResult = $checkRoomStmt->get_result();

    if ($checkRoomResult->num_rows > 0) {
        // If the room number is already assigned to another teacher
        echo json_encode(['status' => 'error', 'message' => 'This room number is already assigned to another teacher.']);
        exit;
    }

    // Check if the staff_id already exists
    $checkQuery = "SELECT teacher_id FROM teachers WHERE staff_id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("s", $staffId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // If the teacher already exists, update the room number in both tables
        $updateStmt = $conn->prepare("UPDATE teachers SET room_number = ? WHERE staff_id = ?");
        $updateStmt->bind_param("ss", $roomNumber, $staffId);
        $updateStmt->execute();

        $assignmentUpdateStmt = $conn->prepare("UPDATE teacher_assignments SET room_number = ? WHERE staff_id = ?");
        $assignmentUpdateStmt->bind_param("ss", $roomNumber, $staffId);
        $assignmentUpdateStmt->execute();

        echo json_encode([
            'status' => 'success',
            'message' => 'Room number updated successfully',
            'staff_id' => $staffId,
            'teacher_name' => '', // Update logic for teacher name
            'room_number' => $roomNumber
        ]);
    } else {
        // If the teacher doesn't exist, insert new teacher record in both tables
        $stmt = $conn->prepare("INSERT INTO teachers (staff_id, teacher_name, room_number) VALUES (?, '', ?)");
        $stmt->bind_param("ss", $staffId, $roomNumber);
        if ($stmt->execute()) {
            // Get the newly added teacher's details
            $newTeacherId = $stmt->insert_id;
            $teacherName = ''; // Can be updated with logic to fetch teacher name

            // Insert into teacher_assignments table
            $assignmentStmt = $conn->prepare("INSERT INTO teacher_assignments (staff_id, teacher_name, room_number) VALUES (?, '', ?)");
            $assignmentStmt->bind_param("ss", $staffId, $roomNumber);
            $assignmentStmt->execute();

            echo json_encode([
                'status' => 'success',
                'message' => 'Teacher added successfully',
                'staff_id' => $staffId,
                'teacher_name' => $teacherName,
                'room_number' => $roomNumber
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        }
    }
    exit;
}

// Fetch available room numbers
$roomQuery = "SELECT room_number FROM rooms";
$rooms = $conn->query($roomQuery)->fetch_all(MYSQLI_ASSOC);

// Fetch teachers (staff_id, teacher_name, room_number) for the dropdown and table
$teacherQuery = "SELECT staff_id, teacher_name, room_number FROM teachers";
$teachers = $conn->query($teacherQuery)->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        form {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        input, select, button {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
        }
        button {
            background: #FA8D3D;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>

<h1>Add or Update Teacher</h1>
<form id="addTeacherForm">
    <label>Staff ID</label>
    <select id="staff_id" name="staff_id" required>
        <option value="">Select Staff</option>
        <?php foreach ($teachers as $teacher): ?>
            <option value="<?= $teacher['staff_id'] ?>"><?= $teacher['staff_id'] ?> - <?= $teacher['teacher_name'] ?></option>
        <?php endforeach; ?>
    </select>

    <label>Teacher Name</label>
    <input type="text" id="teacher_name" name="teacher_name" readonly>

    <label>Room Number</label>
    <select id="room_number" name="room_number" required>
        <option value="">Select Room</option>
        <?php foreach ($rooms as $room): ?>
            <option value="<?= $room['room_number'] ?>"><?= $room['room_number'] ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Add or Update Teacher</button>
</form>

<!-- Display Teacher Details Table -->
<h2>Teacher Details</h2>
<table>
    <thead>
        <tr>
            <th>Staff ID</th>
            <th>Teacher Name</th>
            <th>Room Number</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($teachers as $teacher): ?>
            <tr data-staff-id="<?= $teacher['staff_id'] ?>">
                <td><?= $teacher['staff_id'] ?></td>
                <td class="teacher_name"><?= $teacher['teacher_name'] ?></td>
                <td class="room_number"><?= $teacher['room_number'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
document.getElementById('staff_id').addEventListener('change', function() {
    const staffId = this.value;
    if (staffId) {
        fetch('', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=fetch_name&staff_id=' + staffId
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('teacher_name').value = data.teacher_name || '';
        });
    }
});

document.getElementById('addTeacherForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', 'add_teacher');
    
    fetch('', {
        method: 'POST',
        body: new URLSearchParams(formData)
    })
    .then(response => response.json())
    .then(data => {
        const message = data.message || 'Action completed successfully';
        const status = data.status || 'success';
        
        // Show success/error message
        const messageDiv = document.createElement('div');
        messageDiv.textContent = message;
        messageDiv.style.padding = '10px';
        messageDiv.style.marginTop = '15px';
        messageDiv.style.textAlign = 'center';
        
        if (status === 'success') {
            messageDiv.style.backgroundColor = '#4CAF50'; // Green for success
            messageDiv.style.color = 'white';
        } else {
            messageDiv.style.backgroundColor = '#f44336'; // Red for error
            messageDiv.style.color = 'white';
        }
        
        document.body.appendChild(messageDiv); // Append the message to the body
        
        // Optionally, remove the message after a few seconds
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);

        // Update the table without reloading the page
        let tableBody = document.querySelector('table tbody');
        
        // Check if the staff_id already exists in the table (to update it)
        let row = document.querySelector(`tr[data-staff-id="${data.staff_id}"]`);
        
        if (row) {
            // Update the existing row
            row.querySelector('.teacher_name').textContent = data.teacher_name;
            row.querySelector('.room_number').textContent = data.room_number;
        } else {
            // Add new row for the new teacher
            let newRow = document.createElement('tr');
            newRow.setAttribute('data-staff-id', data.staff_id);
            newRow.innerHTML = `
                <td>${data.staff_id}</td>
                <td class="teacher_name">${data.teacher_name}</td>
                <td class="room_number">${data.room_number}</td>
            `;
            tableBody.appendChild(newRow);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
</script>

</body>
</html>
