<?php
require('db_connection.php'); 
// Get department from URL
$dept = isset($_GET['dept']) ? $_GET['dept'] : '';

// Handle delete request for the entire year
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_year'])) {
    $year_to_delete = $_POST['year'];
    $delete_year_sql = "DELETE FROM students WHERE year = ? AND dept = ?";
    $delete_year_stmt = $conn->prepare($delete_year_sql);

    if ($delete_year_stmt) {
        $delete_year_stmt->bind_param('is', $year_to_delete, $dept);
        $delete_year_stmt->execute();
        $delete_year_stmt->close();
    } else {
        echo "Error preparing delete statement: " . $conn->error;
    }
}

// Fetch students for the selected department
$sql = "SELECT reg_no, year FROM students WHERE dept = ?";
$stmt = $conn->prepare($sql);

// Check if the prepare was successful
if (!$stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}

$stmt->bind_param('s', $dept);
$stmt->execute();
$result = $stmt->get_result();

// Fetch unique years for the dropdown
$year_sql = "SELECT DISTINCT year FROM students WHERE dept = ?";
$year_stmt = $conn->prepare($year_sql);
$year_stmt->bind_param('s', $dept);
$year_stmt->execute();
$year_result = $year_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students in <?php echo htmlspecialchars($dept); ?></title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        form {
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h1>Students in <?php echo htmlspecialchars($dept); ?></h1>

    <!-- Year Selection Form -->
    <form method="POST" action="">
        <label for="year">Select Year to Delete:</label>
        <select name="year" id="year" required>
            <option value="">-- Select Year --</option>
            <?php
            while ($year_row = $year_result->fetch_assoc()) {
                echo '<option value="' . htmlspecialchars($year_row['year']) . '">' . htmlspecialchars($year_row['year']) . '</option>';
            }
            ?>
        </select>
        <button type="submit" name="delete_year" onclick="return confirm('Are you sure you want to delete all records for this year?');">Delete Entire Year</button>
    </form>

    <!-- Students Table -->
    <table>
        <thead>
            <tr>
                <th>Register No</th>
                <th>Year</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<tr><td>' . htmlspecialchars($row['reg_no']) . '</td>' .
                         '<td>' . htmlspecialchars($row['year']) . '</td></tr>'; // Display reg_no and year
                }
            } else {
                echo '<tr><td colspan="2">No students found in this department.</td></tr>';
            }
            ?>
        </tbody>
    </table>

    <?php
    $stmt->close();
    $year_stmt->close();
    $conn->close();
    ?>
</body>
</html>
