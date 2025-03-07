<?php
require('db_connection.php');
// Fetch students based on search criteria
function searchStudents($searchTerm = '') {
    $conn = getDbConnection();
    $sql = "SELECT id, reg_no, dept, year FROM students";
    
    if ($searchTerm) {
        $searchTerm = $conn->real_escape_string($searchTerm);
        $sql .= " WHERE reg_no LIKE '%$searchTerm%' OR dept LIKE '%$searchTerm%' OR year LIKE '%$searchTerm%'";
    }

    $result = $conn->query($sql);

    $students = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
    }
    $conn->close();
    return $students;
}

// Handle update logic
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $reg_no = $_POST['reg_no'];
    $dept = $_POST['dept'];
    $year = $_POST['year'];

    $conn = getDbConnection();
    $sql = "UPDATE students SET reg_no = '$reg_no', dept = '$dept', year = '$year' WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully!";
    } else {
        echo "Error updating record: " . $conn->error;
    }
    $conn->close();
}

// Fetch student by ID for editing
$editStudent = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $conn = getDbConnection();
    $sql = "SELECT * FROM students WHERE id = $id";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        $editStudent = $result->fetch_assoc();
    }
    $conn->close();
}

// Handle the search term from the request
$searchTerm = '';
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
}

// Fetch the search results
$students = searchStudents($searchTerm);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Hall Allotment Dashboard</title>

    <style>
    
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        h2 {
            color: #555;
        }

        /* Search Box */
        .search-box {
            text-align: center;
            margin-bottom: 20px;
        }

        .search-box input[type="text"] {
            padding: 10px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 10px;
        }

        .search-box button {
            padding: 10px 20px;
            border: none;
            background-color: #5A90EA;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .search-box button:hover {
            background-color: #2FCCF8;
        }

        /* Edit Form */
        .edit-form {
            max-width: 400px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .edit-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .edit-form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .edit-form button {
            padding: 10px 20px;
            background-color: #FA8D3D;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .edit-form button:hover {
            background-color: #F76E53;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        table th, table td {
            text-align: left;
            padding: 12px;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #5A90EA;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        /* Actions */
        .actions {
            display: flex;
            gap: 10px;
        }

        button.edit-btn, button.delete-btn {
            padding: 8px 12px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        /* Edit Button */
        button.edit-btn {
            background-color: #5A90EA;
            color: white;
        }

        button.edit-btn:hover {
            background-color: #2FCCF8;
        }

        /* Delete Button */
        button.delete-btn {
            background-color: #d9534f;
            color: white;
        }

        button.delete-btn:hover {
            background-color: #c9302c;
        }

        /* No Records */
        table td[colspan="5"] {
            text-align: center;
            color: #999;
        }

    </style>

</head>
<body>

<h1>Exam Hall Allotment Dashboard</h1>

<div class="search-box">
    <form action="" method="GET">
        <input type="text" name="search" placeholder="Search by Register Number, Department, or Year" value="<?= htmlspecialchars($searchTerm) ?>">
        <button type="submit">Search</button>
    </form>
</div>

<?php if ($editStudent): ?>
<div class="edit-form">
    <h2>Edit Student</h2>
    <form action="" method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($editStudent['id']) ?>">
        <label for="reg_no">Register Number:</label>
        <input type="text" name="reg_no" value="<?= htmlspecialchars($editStudent['reg_no']) ?>" required>
        <br>
        <label for="dept">Department:</label>
        <input type="text" name="dept" value="<?= htmlspecialchars($editStudent['dept']) ?>" required>
        <br>
        <label for="year">Year:</label>
        <input type="text" name="year" value="<?= htmlspecialchars($editStudent['year']) ?>" required>
        <br>
        <button type="submit" name="update">Update</button>
    </form>
</div>
<?php endif; ?>

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
        <?php if (count($students) > 0): ?>
            <?php foreach ($students as $student): ?>
            <tr>
                <td><?= htmlspecialchars($student['id']) ?></td>
                <td><?= htmlspecialchars($student['reg_no']) ?></td>
                <td><?= htmlspecialchars($student['dept']) ?></td>
                <td><?= htmlspecialchars($student['year']) ?></td>
                <td class="actions">
                    <form action="" method="GET" style="display: inline;">
                        <input type="hidden" name="edit" value="<?= $student['id'] ?>">
                        <button type="submit" class="edit-btn">Edit</button>
                    </form>
                    <form action="delete.php" method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?= $student['id'] ?>">
                        <button type="submit" class="delete-btn">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No records found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
