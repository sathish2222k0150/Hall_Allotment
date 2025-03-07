<?php
// Database connection
require('db_connection.php'); 

// Fetch unique departments
$sql = "SELECT DISTINCT dept FROM students";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departments</title>
    <style>
        
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.6;
        }

        h1 {
            text-align: center;
            color: #2FCCF8;
            margin-bottom: 20px;
            font-size: 2em;
        }

        /* Table Styles */
        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #5A90EA;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }

        td a {
            color: #FA8D3D;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        td a:hover {
            color: #F76E53;
            text-decoration: underline;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        tr:last-child td {
            border-bottom: none;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            table {
                width: 100%;
                font-size: 14px;
            }

            th, td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <h1>Departments</h1>
    <table>
        <thead>
            <tr>
                <th>Department</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<tr><td><a href="department.php?dept=' . urlencode($row['dept']) . '">' . htmlspecialchars($row['dept']) . '</a></td></tr>';
                }
            } else {
                echo '<tr><td>No departments found.</td></tr>';
            }
            ?>
        </tbody>
    </table>

    <?php $conn->close(); ?>
</body>
</html>
