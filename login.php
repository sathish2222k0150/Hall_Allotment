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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];

    if ($role === 'student') {
        $reg_no = $_POST['reg_no'];
        $email = $_POST['student_email'];

        $sql = "SELECT * FROM students WHERE reg_no = ? AND email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $reg_no, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Set session variables and redirect to display.php
            $_SESSION['role'] = 'student';
            $_SESSION['reg_no'] = $reg_no;
            header("Location: display.php?role=student");
            exit();
        } else {
            echo "Invalid register number or email for student.";
        }
    } elseif ($role === 'teacher') {
        $staff_id = $_POST['staff_id'];
        $email = $_POST['teacher_email'];

        $sql = "SELECT * FROM teachers WHERE staff_id = ? AND email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $staff_id, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Set session variables and redirect to display.php
            $_SESSION['role'] = 'teacher';
            $_SESSION['staff_id'] = $staff_id;
            header("Location: display.php?role=teacher");
            exit();
        } else {
            echo "Invalid staff ID or email for teacher.";
        }
    } elseif ($role === 'admin') {
        $admin_email = $_POST['admin_email'];
        $admin_password = $_POST['admin_password'];

        $sql = "SELECT * FROM admins WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $admin_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($admin_password, $row['password'])) {
                // Set session variables and redirect to admin dashboard
                $_SESSION['role'] = 'admin';
                $_SESSION['admin_email'] = $admin_email;
                header("Location: ./backend/admin.php");
                exit();
            } else {
                echo "Invalid password for admin.";
            }
        } else {
            echo "Invalid email for admin.";
        }
    } else {
        echo "Invalid role selected.";
    }
}
?>
