<?php
require('db_connection.php'); 
// Check if a file is uploaded
if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == 0) {
    // Get the uploaded file's temporary path
    $file = $_FILES['pdf_file']['tmp_name'];

    // Read the contents of the uploaded text file
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // Split each line into registration number and name
        list($reg_no, $name) = explode(' ', $line, 2);
        
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO students (reg_no, dept, year) VALUES (?, 'B.Sc Information Technology', '1')"); // Adjust year as needed
        $stmt->bind_param("s", $reg_no);

        // Execute the statement
        if ($stmt->execute()) {
            echo "New record created successfully: $reg_no - $name<br>";
        } else {
            echo "Error: " . $stmt->error . "<br>";
        }
    }

    // Close the statement and connection
    $stmt->close();
} else {
    echo "Error uploading file.";
}

$conn->close();
?>
