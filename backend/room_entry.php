<?php
// Include the database connection
include 'db_connection.php';

// Check if the form is submitted to add a room
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_room'])) {
    $roomNumber = trim($_POST['room_number']);
    $seats = (int)$_POST['seats'];  // Get the number of seats

    if (!empty($roomNumber) && $seats > 0) {
        $stmt = $pdo->prepare("INSERT INTO rooms (room_number, seats) VALUES (:room_number, :seats)");
        $stmt->bindParam(':room_number', $roomNumber);
        $stmt->bindParam(':seats', $seats);
        if ($stmt->execute()) {
            echo "<script>alert('Room added successfully!');</script>";
        } else {
            echo "<script>alert('Error adding room.');</script>";
        }
    } else {
        echo "<script>alert('Please provide a valid room number and number of seats.');</script>";
    }
}

// Check if the form is submitted to delete a room
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_room'])) {
    $roomNumber = $_POST['delete_room'];
    $stmt = $pdo->prepare("DELETE FROM rooms WHERE room_number = :room_number");
    $stmt->bindParam(':room_number', $roomNumber);
    if ($stmt->execute()) {
        echo "<script>alert('Room number deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting room number.');</script>";
    }
}

// Fetch all room numbers and their seat counts from the database
$query = $pdo->query("SELECT room_number, seats FROM rooms ORDER BY room_number ASC");
$rooms = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Entry</title>

    <style>
     
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        h2, h3 {
            text-align: center;
            color: #5A90EA;
        }

        /* Form Styles */
        form {
            max-width: 400px;
            margin: 0 auto 20px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        form input[type="text"],
        form input[type="number"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        form input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #5A90EA;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        form input[type="submit"]:hover {
            background-color: #2FCCF8;
        }

        /* Room List Styles */
        .room-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px; /* Adds space between items */
            justify-content: center; /* Centers the items horizontally */
            padding: 0;
            margin: 20px auto;
            list-style-type: none;
            max-width: 800px;
        }

        .room-item {
            padding: 10px 15px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background-color 0.3s ease;
            font-size: 14px;
        }

        .room-item:hover {
            background-color: #f1f1f1;
        }

        /* Delete Button Styles */
        .room-item form {
            display: inline;
        }

        .room-item input[type="submit"] {
            padding: 6px 10px;
            background-color: #d9534f;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 12px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .room-item input[type="submit"]:hover {
            background-color: #c9302c;
        }

        /* No Rooms Message */
        .room-grid li {
            text-align: center;
            color: #999;
        }
    </style>
    
</head>
<body>

<h2>Enter Room Number and Seats</h2>
<form method="POST">
    <input type="text" name="room_number" placeholder="Enter room number" required>
    <input type="number" name="seats" placeholder="Enter number of seats" required min="1">
    <input type="submit" name="add_room" value="Add Room">
</form>

<h3>Entered Room Numbers:</h3>
<ul class="room-grid">
    <?php if (!empty($rooms)): ?>
        <?php foreach ($rooms as $room): ?>
            <li class="room-item">
                <?php echo htmlspecialchars($room['room_number']) . " (Seats: " . $room['seats'] . ")"; ?>
                <!-- Delete button for each room number -->
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="delete_room" value="<?php echo htmlspecialchars($room['room_number']); ?>">
                    <input type="submit" value="Delete">
                </form>
            </li>
        <?php endforeach; ?>
    <?php else: ?>
        <li>No room numbers entered yet.</li>
    <?php endif; ?>
</ul>

</body>
</html>
