<?php
$conn = new mysqli("localhost", "root", "", "exam_hall");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $email = $_POST['email'];
    $password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
    $reg_no = $_POST['reg_no'] ?? null;
    $staff_id = $_POST['staff_id'] ?? null;
    $teacher_name = $_POST['teacher_name'] ?? null;

    if ($role === "admin") {
        // Check for duplicate email
        $checkSql = "SELECT email FROM admins WHERE email = ?";
        $stmt = $conn->prepare($checkSql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "Error: Email is already registered as an admin.";
            exit;
        }

        // Insert admin record
        $sql = "INSERT INTO admins (email, password) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $password);

    } elseif ($role === "student" && $reg_no) {
        // Ensure register number does not already have an email
        $checkSql = "SELECT email FROM students WHERE reg_no = ? AND email IS NOT NULL";
        $stmt = $conn->prepare($checkSql);
        $stmt->bind_param("s", $reg_no);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "Error: This register number already has an email.";
            exit;
        }

        // Update student email
        $sql = "UPDATE students SET email = ? WHERE reg_no = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $reg_no);

    } elseif ($role === "teacher" && $staff_id && $teacher_name) {
        // Ensure staff_id and email are unique
        $checkSql = "SELECT staff_id, email FROM teachers WHERE staff_id = ? OR email = ?";
        $stmt = $conn->prepare($checkSql);
        $stmt->bind_param("ss", $staff_id, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "Error: Staff ID or Email is already associated with another teacher.";
            exit;
        }

        // Insert teacher record (room_number is not included as it is a foreign key)
        $sql = "INSERT INTO teachers (teacher_name, staff_id, email, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $teacher_name, $staff_id, $email, $password);
    } else {
        echo "Invalid role or missing information.";
        exit;
    }

    // Execute query
    if ($stmt->execute()) {
        echo ucfirst($role) . " registered/updated successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
