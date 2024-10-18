<?php
session_start();
$conn = new mysqli("localhost", "root", "", "hotel_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$update_reservation_id = isset($_GET['edit']) ? $_GET['edit'] : null;
if (isset($_GET["delete_reservation"])) {
    $reservation_id = $_GET["delete_reservation"];
    $user_id = $_SESSION["user_id"];
    $stmt = $conn->prepare("DELETE FROM reservations WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $reservation_id, $user_id);

    if ($stmt->execute()) {
        echo "Reservation canceled successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION["user_id"])) {
        $user_id = $_SESSION["user_id"];
        $name = $_POST["name"];
        $phone_number = $_POST["phone_number"];
        $adults = $_POST["adults"];
        $children = $_POST["children"];
        $check_in_date = $_POST["check_in_date"];
        $check_out_date = $_POST["check_out_date"];
        $no_of_rooms = $_POST["no_of_rooms"];
        if (strlen($phone_number) !== 10 || !ctype_digit($phone_number)) {
            echo "Error: Phone number must be 10 digits.";
            exit();
        }
        if ($update_reservation_id) {   
            $stmt = $conn->prepare("UPDATE reservations SET name=?, phone_number=?, adults=?, children=?, check_in_date=?, check_out_date=?, no_of_rooms=? WHERE id=? AND user_id=?");
            $stmt->bind_param("siiissiii", $name, $phone_number, $adults, $children, $check_in_date, $check_out_date, $no_of_rooms, $update_reservation_id, $user_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO reservations (user_id, name, phone_number, adults, children, check_in_date, check_out_date, no_of_rooms) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isiiissi", $user_id, $name, $phone_number, $adults, $children, $check_in_date, $check_out_date, $no_of_rooms);
        }
        if ($stmt->execute()) {
            echo "Reservation ";
            echo $update_reservation_id ? "updated" : "booked";
            echo "!";
            $reservation_id = $update_reservation_id ? $update_reservation_id : $conn->insert_id;
            header("Location: confirmation.php?id=$reservation_id");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "User not authenticated";
    }
}
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
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        form {
            flex: 1;
            max-width: 400px;
            margin: 20px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }
        input[type="text"],
        input[type="tel"],
        input[type="number"],
        input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #3498db;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <form method="post" action="">
        <h2>Grand Hyatt Doha Hotel Room Booking - Reservation Form</h2>
        <label for="name">Name:</label>
        <input type="text" name="name" required><br>
        <label for="phone_number">Phone Number:</label>
        <input type="tel" name="phone_number" required><br>
        <label for="adults">Adults:</label>
        <input type="number" name="adults" required><br>
        <label for="children">Children:</label>
        <input type="number" name="children" required><br>
        <label for="check_in_date">Check-in Date:</label>
        <input type="date" name="check_in_date" required><br>
        <label for="check_out_date">Check-out Date:</label>
        <input type="date" name="check_out_date" required><br>
        <label for="no_of_rooms">Number of Rooms:</label>
        <input type="number" name="no_of_rooms" required><br>
        <input type="submit" value="<?php echo isset($_GET['edit']) ? 'Update Reservation' : 'Book Now'; ?>">
    </form>
    <img src="rooms1.jpeg" alt="Rooms1 Image" style="flex: 1; height: 100vh; object-fit: cover;">
</body>
</html>


