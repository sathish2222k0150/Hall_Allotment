<?php
// Include the database connection
include 'db_connection.php';

// Fetch counts for dashboard
$roomCountQuery = $conn->query("SELECT COUNT(*) AS count FROM rooms");
$roomCount = $roomCountQuery ? $roomCountQuery->fetch_assoc()['count'] : 0;

$teacherCountQuery = $conn->query("SELECT COUNT(*) AS count FROM teachers");
$teacherCount = $teacherCountQuery ? $teacherCountQuery->fetch_assoc()['count'] : 0;

$studentCountQuery = $conn->query("SELECT COUNT(*) AS count FROM students");
$studentCount = $studentCountQuery ? $studentCountQuery->fetch_assoc()['count'] : 0;

$deptCountQuery = $conn->query("SELECT COUNT(DISTINCT dept) AS count FROM students");
$deptCount = $deptCountQuery ? $deptCountQuery->fetch_assoc()['count'] : 0;

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Hall Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            display: flex;
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f0f2f5;
        }

        .sidebar {
            width: 250px;
            background: #2FCCF8;
            color: #fff;
            height: 100vh;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar h2 {
            text-align: center;
            font-size: 1.5em;
            margin-bottom: 20px;
            color: #ffffff;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            color: #fff;
            text-decoration: none;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 8px;
            transition: background 0.3s ease;
            cursor: pointer;
        }

        .sidebar a:hover {
            background: #FA8D3D;
        }

        .sidebar a i {
            margin-right: 10px;
            font-size: 1.2em;
        }

        .content {
            flex: 1;
            margin: 0px 20px 0px 0px;
            margin-bottom: 50px;
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .dropdown {
            display: none;
            margin-top: 10px;
            border-radius: 4px;
            z-index: 1000;
            padding: 0;
            list-style: none;
        }

        .dropdown a {
            padding: 10px 15px;
            display: block;
            color: #fff;
            text-decoration: none;
        }

        .dropdown a:hover {
            background: #FA8D3D;
        }

        .show {
            display: block;
        }

        .card {
            background: #ffffff;
            margin: 5px 25px 0px 20px;
            padding: 20px;
            width: 400px;
            height: 200px;
            border-radius: 50px;
            text-align: end;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card h2 {
            font-family: "League Spartan", sans-serif;
            font-weight: 400;
            font-style: normal;
            font-size: 1.5em;
            color: #fff;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }

        .orange {
            background-color: #FA8D3D;
        }

        .blue-light {
            background-color: #2FCCF8;
        }

        .blue-dark {
            background-color: #5A90EA;
        }

        .red {
            background-color: #F76E53;
        }

        .red img{
            transform: scaleX(-1);
        }

        .box{
            margin-top: 40px;
        }

        .box h2{
            margin-top: -160px;
        }

        .card .count{
            font-family: "Halant", serif;
            font-size: 3.5rem;
            font-weight: 600;
            margin-right: 80px;
        }

        .blue-light .count{
            margin-right: 50px;
        }

        .card img {
            display: flex;
            height: 180px;
            align-self: start;
            margin: 0 0 -120px 0;
            margin-left: 3vh;
            object-fit: cover;
        }

    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Dashboard</h2>
        <a href="Home.php"><i class="fas fa-home"></i>Home</a>
        <a href="teachers.php"><i class="fas fa-chalkboard-teacher"></i>Teachers</a>
        <!-- Dropdown trigger -->
        <a id="student-link"><i class="fas fa-door-open"></i>Students</a>
        <!-- Dropdown menu -->
        <ul class="dropdown" id="student-dropdown">
            <li><a href="dashboard.php">Students Dashboard</a></li>
            <li><a href="index.html">Add Students</a></li>
        </ul>
        <!-- Dropdown trigger -->
        <a id="rooms-link"><i class="fas fa-door-open"></i>Rooms</a>
        <!-- Dropdown menu -->
        <ul class="dropdown" id="rooms-dropdown">
            <li><a href="room_entry.php">Room Entry</a></li>
            <li><a href="room_allocate.php">Room Allocation</a></li>
        </ul>
        <a href="insert.php"><i class="fas fa-door-open"></i>Allotments</a>
        <a href="departments.php"><i class="fas fa-building"></i>Departments</a>
    </div>

    <div class="content">
        <div class="card orange">
            <div class="box">
            <img src="./img/sta_3.png" alt="Staff">
                <h2>
                    <span class="count teacher"><?php echo $teacherCount; ?></span><br>
                    <span class="label">Number of Staff</span> 
                </h2>
            </div>
        </div>
        <div class="card blue-light">
            <div class="box">
            <img src="./img/stu_6.png" alt="Student">
                <h2>
                    <span class="count student"><?php echo $studentCount; ?></span><br>
                    <span class="label"> Number of Students</span>
                </h2>
            </div>
        </div>
        <div class="card blue-dark">
            <div class="box">
            <img src="./img/stu_10.png" alt="Classroom">
                <h2>
                    <span class="count room"><?php echo $roomCount; ?></span><br>
                    <span class="label">Number of Rooms</span>
                </h2>
            </div>
        </div>
        <div class="card red">
            <div class="box">
            <img src="./img/sta_4.png" alt="Department">
                <h2>
                    <span class="count dept"><?php echo $deptCount; ?></span><br>
                    <span class="label">Number of Departments</span>
                </h2>
            </div>
        </div>
    </div>

    <script>
        // JavaScript to toggle dropdown visibility on click
        document.getElementById("rooms-link").addEventListener("click", function (e) {
            e.preventDefault(); // Prevent default link behavior
            const dropdown = document.getElementById("rooms-dropdown");
            dropdown.classList.toggle("show"); // Toggle visibility
        });

        // Close the dropdown if clicked outside
        document.addEventListener("click", function (e) {
            const dropdown = document.getElementById("rooms-dropdown");
            const roomsLink = document.getElementById("rooms-link");
            if (!dropdown.contains(e.target) && e.target !== roomsLink) {
                dropdown.classList.remove("show");
            }
        });

        // JavaScript to toggle dropdown visibility on click
        document.getElementById("student-link").addEventListener("click", function (e) {
            e.preventDefault(); // Prevent default link behavior
            const dropdown = document.getElementById("student-dropdown");
            dropdown.classList.toggle("show"); // Toggle visibility
        });

        // Close the dropdown if clicked outside
        document.addEventListener("click", function (e) {
            const dropdown = document.getElementById("student-dropdown");
            const roomsLink = document.getElementById("student-link");
            if (!dropdown.contains(e.target) && e.target !== roomsLink) {
                dropdown.classList.remove("show");
            }
        });
    </script>

</body>
</html>
