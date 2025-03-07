<?php
function getDatabaseConnection() {
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

    return $conn;
}
?>
<?php
// Common Database Connection File

function getDbConnection() {
    $servername = "localhost";
    $username = "root"; // Replace with your DB username
    $password = "";     // Replace with your DB password
    $dbname = "exam_hall"; // Replace with your DB name

    // Create a new database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check if the connection is successful
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn; // Return the connection object
}
?>
<?php
// Common database connection using PDO
function getPDOConnection() {
    $host = 'localhost';
    $dbname = 'exam_hall';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
?>
<?php
// Database configuration
$host = 'localhost';
$dbname = 'exam_hall';
$username = 'root';
$password = '';

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
<?php
// Database configuration
$host = 'localhost';
$db = 'exam_hall';
$user = 'root'; // Change to your database username
$pass = '';     // Change to your database password

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<?php
// Database configuration
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = "";     // Replace with your database password
$dbname = "exam_hall"; // Replace with your database name

// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
