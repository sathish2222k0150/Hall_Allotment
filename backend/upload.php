<?php
require('db_connection.php'); 

// Initialize a variable to accumulate messages
$messages = [];

// Function to show dialog with all messages and then redirect
function showDialog($messages) {
    if (!empty($messages)) {
        $messageString = implode("\\n", $messages); // Join messages with newline characters
        echo "<script type='text/javascript'>
                alert('$messageString');
                window.location.href = 'index.html'; // Redirect to index.html
              </script>";
    } else {
        echo "<script type='text/javascript'>
                window.location.href = 'index.html'; // Redirect if no messages
              </script>";
    }
}

// Handling form submission for single student
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reg_no'])) {
    // Get the form data
    $reg_no = $_POST['reg_no'];
    $dept = $_POST['dept'];  // Get the department from the form
    $year = $_POST['year'];

    // Check if the registration number already exists
    $check_sql = "SELECT * FROM students WHERE reg_no = '$reg_no'";
    $result = $conn->query($check_sql);

    if ($result->num_rows > 0) {
        // Registration number already exists
        $messages[] = "Error: Registration number already exists. Please try another.";
    } else {
        // Insert data into the students table
        $sql = "INSERT INTO students (reg_no, dept, year) VALUES ('$reg_no', '$dept', '$year')";

        if ($conn->query($sql) === TRUE) {
            $messages[] = "New record created successfully.";
        } else {
            $messages[] = "Error: " . $conn->error;
        }
    }
}

// Handling batch upload via file
if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == 0) {
    // Get the year and department from the form if uploaded with a file
    $year_upload = $_POST['year_upload'];
    $dept_upload = $_POST['dept_upload'];  // Get the department from the form

    // Get the uploaded file's temporary path
    $file = $_FILES['pdf_file']['tmp_name'];

    // Read the contents of the uploaded text file
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // Trim the line to remove any extra spaces
        $reg_no = trim($line);

        // Check if the registration number is not empty
        if (!empty($reg_no)) {
            // Check if the registration number already exists
            $check_sql = "SELECT * FROM students WHERE reg_no = '$reg_no'";
            $result = $conn->query($check_sql);

            if ($result->num_rows > 0) {
                // Registration number already exists
                $messages[] = "Error: Registration number $reg_no already exists.";
            } else {
                // Prepare and bind for batch student insert with dynamic department
                $stmt = $conn->prepare("INSERT INTO students (reg_no, dept, year) VALUES (?, ?, ?)");
                $stmt->bind_param("ssi", $reg_no, $dept_upload, $year_upload);

                // Execute the statement
                if ($stmt->execute()) {
                    $messages[] = "New record created successfully: $reg_no";
                } else {
                    $messages[] = "Error: " . $stmt->error;
                }
            }
        } else {
            // Log an error if the line is empty
            $messages[] = "Error: Empty registration number in line.";
        }
    }

    // Close the statement
    if (isset($stmt)) {
        $stmt->close();
    }
} else {
    if (isset($_FILES['pdf_file'])) {
        $messages[] = "Error uploading file.";
    }
}

// Show all messages at once in a dialog and redirect
showDialog($messages);

// Close the connection
$conn->close();
?>
