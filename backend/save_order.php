<?php
session_start();
require('db_connection.php'); // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['roomNumber']) && isset($data['order'])) {
        $roomNumber = $data['roomNumber'];
        $order = $data['order'];

        $_SESSION['rooms'][$roomNumber] = array_map(function ($regNo) use ($roomNumber) {
            foreach ($_SESSION['rooms'][$roomNumber] as $student) {
                if ($student['reg_no'] === $regNo) {
                    return $student;
                }
            }
        }, $order);

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    }
}
?>
