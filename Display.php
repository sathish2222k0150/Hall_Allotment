Display.php page


<?php
session_start();

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

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['role'])) {
        $role = $_GET['role'];

        if ($role === 'student') {
            if (isset($_SESSION['reg_no'])) {
                $reg_no = $_SESSION['reg_no'];

                $sql = "SELECT * FROM students WHERE reg_no = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $reg_no);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    echo "<h1>Student Details</h1>";
                    echo "<p>Register Number: " . htmlspecialchars($row['reg_no']) . "</p>";
                    echo "<p>Name: " . htmlspecialchars($row['name']) . "</p>";
                    echo "<p>Email: " . htmlspecialchars($row['email']) . "</p>";
                    echo "<p>Course: " . htmlspecialchars($row['course']) . "</p>";
                } else {
                    echo "No details found for the student.";
                }
            } else {
                echo "Student session not set.";
            }
        } elseif ($role === 'teacher') {
            if (isset($_SESSION['staff_id'])) {
                $staff_id = $_SESSION['staff_id'];

                $sql = "SELECT * FROM teachers WHERE staff_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $staff_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    echo "<h1>Teacher Details</h1>";
                    echo "<p>Staff ID: " . htmlspecialchars($row['staff_id']) . "</p>";
                    echo "<p>Email: " . htmlspecialchars($row['email']) . "</p>";
                    echo "<p?>Staff Name:". htmlspecialchars($row['teacher_name'])."</p>";
                } else {
                    echo "No details found for the teacher.";
                }
            } else {
                echo "Teacher session not set.";
            }
        } elseif ($role === 'admin') {
            if (isset($_SESSION['admin_email'])) {
                $admin_email = $_SESSION['admin_email'];

                $sql = "SELECT * FROM admins WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $admin_email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    echo "<h1>Admin Details</h1>";
                    echo "<p>Email: " . htmlspecialchars($row['email']) . "</p>";
                    echo "<p>Name: " . htmlspecialchars($row['name']) . "</p>";
                } else {
                    echo "No details found for the admin.";
                }
            } else {
                echo "Admin session not set.";
            }
        } else {
            echo "Invalid role.";
        }
    } else {
        echo "Role not specified in the URL.";
    }
}

$conn->close();
?>