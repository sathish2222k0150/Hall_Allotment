<?php
require('db_connection.php'); // Include the common connection file

// Fetch students from the database with optional search filters
function getAllStudents($search = '') {
    $conn = getDbConnection(); // Use the common connection

    $sql = "SELECT id, reg_no, dept, year FROM students";
    if (!empty($search)) {
        $sql .= " WHERE reg_no LIKE ? OR dept LIKE ? OR year LIKE ?";
        $stmt = $conn->prepare($sql);
        $searchParam = "%$search%";
        $stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
    } else {
        $stmt = $conn->prepare($sql);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    $stmt->close();
    $conn->close();
    return $students;
}

// Update student information
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    if (isset($_POST['id'], $_POST['reg_no'], $_POST['dept'], $_POST['year'])) {
        $id = $_POST['id'];
        $reg_no = $_POST['reg_no'];
        $dept = $_POST['dept'];
        $year = $_POST['year'];

        $conn = getDbConnection();
        $stmt = $conn->prepare("UPDATE students SET reg_no = ?, dept = ?, year = ? WHERE id = ?");
        $stmt->bind_param("sssi", $reg_no, $dept, $year, $id);

        if ($stmt->execute()) {
            echo "Student details updated successfully.";
        } else {
            echo "Error updating student: " . $conn->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Missing required fields!";
    }
}

// Delete student information
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        $conn = getDbConnection();
        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "Student deleted successfully.";
        } else {
            echo "Error deleting student: " . $conn->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Missing student ID!";
    }
}

// Capture search input
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch all students with search filter
$students = getAllStudents($search);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Allotment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .edit-btn {
            margin-right: 5px;
        }
        .search-bar {
            margin-bottom: 20px;
        }
        .search-bar input[type="text"] {
            padding: 10px;
            width: 200px;
            font-size: 16px;
        }
        .search-bar button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
    <script>
        // Function to toggle edit mode for a specific row
        function enableEditMode(rowId) {
            var row = document.getElementById('row-' + rowId);
            var cells = row.getElementsByClassName('editable');

            for (var i = 0; i < cells.length; i++) {
                cells[i].style.display = 'none'; // Hide text
            }

            var inputs = row.getElementsByClassName('edit-input');
            for (var i = 0; i < inputs.length; i++) {
                inputs[i].style.display = 'inline'; // Show input fields
            }

            // Show save button, hide edit button
            document.getElementById('edit-' + rowId).style.display = 'none';
            document.getElementById('save-' + rowId).style.display = 'inline';
        }
    </script>
</head>
<body>

<h1>Manage Room Allotment</h1>

<!-- Search form -->
<div class="search-bar">
    <form action="" method="GET">
        <input type="text" name="search" placeholder="Search by Reg No, Dept, Year" value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Register Number</th>
            <th>Department</th>
            <th>Year</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($students)): ?>
            <?php foreach ($students as $student): ?>
                <tr id="row-<?= $student['id'] ?>">
                    <!-- ID remains static -->
                    <td><?= $student['id'] ?></td>

                    <!-- Register Number column -->
                    <td>
                        <span class="editable"><?= $student['reg_no'] ?></span>
                        <input type="text" name="reg_no" class="edit-input" style="display:none;" value="<?= $student['reg_no'] ?>" form="form-<?= $student['id'] ?>">
                    </td>

                    <!-- Department column -->
                    <td>
                        <span class="editable"><?= $student['dept'] ?></span>
                        <input type="text" name="dept" class="edit-input" style="display:none;" value="<?= $student['dept'] ?>" form="form-<?= $student['id'] ?>">
                    </td>

                    <!-- Year column -->
                    <td>
                        <span class="editable"><?= $student['year'] ?></span>
                        <input type="text" name="year" class="edit-input" style="display:none;" value="<?= $student['year'] ?>" form="form-<?= $student['id'] ?>">
                    </td>

                    <!-- Actions column -->
                    <td>
                        <form id="form-<?= $student['id'] ?>" action="" method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?= $student['id'] ?>">
                            <button type="button" id="edit-<?= $student['id'] ?>" class="edit-btn" onclick="enableEditMode(<?= $student['id'] ?>)">Edit</button>
                            <button type="submit" name="update" id="save-<?= $student['id'] ?>" style="display:none;">Save</button>
                        </form>
                        <form action="" method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?= $student['id'] ?>">
                            <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this student?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No students found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
