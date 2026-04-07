<?php
session_start();
require 'dbconnect.php';

// if (!isset($_SESSION["username"])) {
//     header("Location: signin.php");
//     exit();
// }

// $username = $_SESSION["username"];
$role = $_SESSION["role"];

if ($role === "advisor") {
    $sql = "SELECT a.*, s.slot_datetime, u.first_name, u.last_name 
            FROM appointments a
            JOIN add_slot s ON a.slot_id = s.slot_id
            JOIN users u ON a.booked_by = u.username
            WHERE a.advisor_username = ?
            ORDER BY s.slot_datetime ASC";
} else {
    $sql = "SELECT a.*, s.slot_datetime, u.first_name, u.last_name 
            FROM appointments a
            JOIN add_slot s ON a.slot_id = s.slot_id
            JOIN users u ON a.advisor_username = u.username
            WHERE a.booked_by = ?
            ORDER BY s.slot_datetime ASC";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Appointments - RESEARCHVERSE</title>
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
            width: 80%;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
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

        button {
            background-color: #ae157d;
            border: none;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            width: auto;
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

        /* Appointment Card Styles */
        .appointment-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            margin: 15px 0;
            border-radius: 8px;
            border-left: 4px solid #ae157d;
            text-align: left;
        }

        .appointment-card h3 {
            margin-top: 0;
            color: #fff;
            font-size: 18px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            padding-bottom: 8px;
        }

        .appointment-detail {
            margin: 8px 0;
            display: flex;
        }

        .detail-label {
            font-weight: bold;
            min-width: 120px;
            color: #e6dfff;
        }

        .detail-value {
            flex-grow: 1;
        }

        .status-confirmed { 
            color: #4CAF50;
            font-weight: bold;
        }
        
        .status-cancelled { 
            color: #F44336;
            font-weight: bold;
        }
        
        .status-completed { 
            color: #2196F3;
            font-weight: bold;
        }

        .no-appointments {
            font-size: 16px;
            color: #e6dfff;
            padding: 20px;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>My Appointments</h2>
        <p><?= $role === "advisor" ? "Your scheduled sessions" : "Your booked appointments" ?></p>
        
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="appointment-card">
                    <h3><?= htmlspecialchars($row['booking_type']) ?></h3>
                    
                    <div class="appointment-detail">
                        <span class="detail-label">
                            <?= $role === "advisor" ? "With:" : "Advisor:" ?>
                        </span>
                        <span class="detail-value">
                            <?= htmlspecialchars($row['first_name']) ?> <?= htmlspecialchars($row['last_name']) ?>
                        </span>
                    </div>
                    
                    <div class="appointment-detail">
                        <span class="detail-label">Date:</span>
                        <span class="detail-value">
                            <?= date('l, F j, Y \a\t g:i A', strtotime($row['slot_datetime'])) ?>
                        </span>
                    </div>
                    
                    <div class="appointment-detail">
                        <span class="detail-label">Status:</span>
                        <span class="status-<?= $row['status'] ?> detail-value">
                            <?= ucfirst($row['status']) ?>
                        </span>
                    </div>
                    
                    <div class="appointment-detail">
                        <span class="detail-label">Booked on:</span>
                        <span class="detail-value">
                            <?= date('F j, Y', strtotime($row['booking_time'])) ?>
                        </span>
                    </div>
                    
                    <?php if ($row['status'] === 'confirmed'): ?>
                        <form action="cancel_appointment.php" method="POST" style="margin-top: 15px;">
                            <input type="hidden" name="appointment_id" value="<?= $row['appointment_id'] ?>">
                            <button type="submit">Cancel Appointment</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-appointments">
                You have no appointments scheduled.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>