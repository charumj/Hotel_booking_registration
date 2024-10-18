<?php
$conn = new mysqli("localhost", "root", "", "hotel_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$reservation_id = isset($_GET['id']) ? $_GET['id'] : null;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_reservation"])) {    
    header("Location: reservation.php?edit=$reservation_id");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["cancel_reservation"])) {
    $stmt = $conn->prepare("DELETE FROM reservations WHERE id = ?");
    $stmt->bind_param("i", $reservation_id);

    if ($stmt->execute()) {
        echo "Reservation canceled successfully!";  
        header("Location: reservation.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$stmt = $conn->prepare("SELECT * FROM reservations WHERE id = ?");
$stmt->bind_param("i", $reservation_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            height: 100vh;
        }
        .container {
            display: flex;
            justify-content: space-between;
            max-width: 800px;
            margin: 0 auto;
        }
        .details {
            flex: 1;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .details h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .details label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }
        .details form {
            margin-top: 20px;
        }
        .update-button {
            background-color: #3498db;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .update-button:hover {
            background-color: #2980b9;
        }
        .cancel-button {
            background-color: #e74c3c;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .cancel-button:hover {
            background-color: #c0392b;
        }
        .image {
            flex: 1;
            height: 100vh;
            object-fit: cover;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="details">
        <?php
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo "<h2>Reservation Details:</h2>";
            echo "Name: " . $row["name"] . "<br>";
            echo "Phone Number: " . $row["phone_number"] . "<br>";
            echo '<form method="post" action="">';
            echo '<input type="submit" class="update-button" name="update_reservation" value="Update Reservation">';
            echo '<input type="submit" class="cancel-button" name="cancel_reservation" value="Cancel Reservation">';
            echo '</form>';
        } else {
            echo "Reservation not found.";
        }
        ?>
    </div>
    <div class="image">
        <img src="walkway.jpeg" alt="Walkway Image">
    </div>
</div>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
