<?php
session_start();
require 'dbconnect.php';

// if (!isset($_SESSION["username"]) || ($_SESSION["role"] !== "beginner" && $_SESSION["role"] !== "researcher")) {
//     header("Location: signin.php");
//     exit();
// }

// $username = $_SESSION["username"];
$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $slot_id = (int)$_POST["slot_id"];
    $type = $_POST["booking_type"];
    
    $conn->begin_transaction();
    
    try {
        $check = $conn->query("SELECT advisor_username FROM add_slot WHERE slot_id = $slot_id AND is_booked = 0 FOR UPDATE");
        
        if ($check->num_rows === 1) {
            $row = $check->fetch_assoc();
            $advisor_username = $row["advisor_username"];
            
            $conn->query("UPDATE add_slot SET is_booked = 1 WHERE slot_id = $slot_id");
            
            $stmt = $conn->prepare("INSERT INTO appointments (slot_id, advisor_username, booked_by, booking_type) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $slot_id, $advisor_username, $username, $type);
            $stmt->execute();
            $stmt->close();
            
            $conn->commit();
            $message = "Slot booked successfully!";
        } else {
            $message = "Slot not available or already booked.";
            $conn->rollback();
        }
    } catch (Exception $e) {
        $conn->rollback();
        $message = "Booking failed. Please try again.";
    }
}

$result = $conn->query("SELECT * FROM add_slot WHERE is_booked = 0 ORDER BY slot_datetime ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Time Slots - RESEARCHVERSE</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0c0020, #2b001b);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 30px rgba(255, 0, 100, 0.4);
            text-align: center;
            width: 350px;
        }

        .form-container h2 {
            margin-bottom: 5px;
            font-size: 26px;
        }

        .form-container p {
            font-size: 14px;
            margin-bottom: 15px;
            color: #e6dfff;
        }

        input, select {
            width: 95%;
            padding: 10px;
            margin: 8px 0;
            border: none;
            border-radius: 6px;
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }

        input::placeholder, select {
            color: #eee;
        }

        select option {
            background-color: #260032;
            color: white;
        }

        button {
            background-color: #ae157d;
            border: none;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 10px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #73034d;
        }

        .message {
            color: #ffd1dc;
            font-size: 14px;
            margin-bottom: 10px;
        }

        /* Slot-specific styles */
        .slot-container {
            margin: 20px 0;
            text-align: left;
        }

        .slot-option {
            background: rgba(255, 255, 255, 0.1);
            padding: 12px;
            margin: 10px 0;
            border-radius: 6px;
            border-left: 3px solid #ae157d;
        }

        .slot-option label {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .slot-option input[type="radio"] {
            width: auto;
            margin-right: 10px;
        }

        .slot-details {
            flex-grow: 1;
        }

        .advisor-name {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .slot-time {
            font-size: 13px;
            color: #e6dfff;
        }

        .booking-type {
            margin: 20px 0;
        }

        .booking-type h3 {
            margin-bottom: 10px;
            font-size: 16px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Book a Time Slot</h2>
        <p> </p>
        
        <?php if (!empty($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        
        <?php if ($result->num_rows > 0): ?>
            <form method="POST" action="">
                <div class="slot-container">
                    <h3>Available Slots:</h3>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="slot-option">
                            <label>
                                <input type="radio" name="slot_id" value="<?= $row['slot_id'] ?>" required>
                                <div class="slot-details">
                                    <div class="advisor-name"><?= htmlspecialchars($row['advisor_username']) ?></div>
                                    <div class="slot-time"><?= date('M j, Y g:i A', strtotime($row['slot_datetime'])) ?></div>
                                </div>
                            </label>
                        </div>
                    <?php endwhile; ?>
                </div>

                <div class="booking-type">
                    <h3>Booking Type:</h3>
                    <select name="booking_type" required>
                        <option value="">Select session type</option>
                        <option value="Paper Review">Paper Review</option>
                        <option value="Mentorship Session">Mentorship Session</option>
                        <option value="Research Discussion">Research Discussion</option>
                    </select>
                </div>

                <button type="submit">Book Slot</button>
            </form>
        <?php else: ?>
            <p class="message">No scheduled slot booked</p>
            <p>Please check back later or contact an advisor directly.</p>
        <?php endif; ?>
    </div>
</body>
</html>