<?php
session_start();
require 'dbconnect.php';


// if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "advisor") {
//     header("Location: signin.php");
//     exit();
// }

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $slot_datetime = $_POST["slot_datetime"];
    $advisor_username = $_SESSION["username"];
    $stmt = $conn->prepare("INSERT INTO add_slot (advisor_username, slot_datetime) VALUES (?, ?)");
    $stmt->bind_param("ss", $advisor_username, $slot_datetime);
    
    if ($stmt->execute()) {
        $message = "Slot added successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Time Slots</title>
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

    </style>
</head>
<body>
    <div class="form-container">
        <h2>Add Available Time Slot</h2>
        <?php if (!empty($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        
        <form method="POST">
            <input type="datetime-local" name="slot_datetime" required>
            <button type="submit">Add Slot</button>
        </form>
    </div>
</body>
</html>