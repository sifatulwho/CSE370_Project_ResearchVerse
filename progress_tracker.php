<?php
session_start();
$conn = new mysqli("localhost", "root", "", "researchverse");

if (!isset($_SESSION["user"])) {
    header("Location: signin.php");
    exit();
}

$username = $_SESSION["user"];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_task"])) {
    $track_id = $_POST["track_id"];
    $task = $_POST["task_name"];
    $type = $_POST["progress_type"];
    $priority = $_POST["priority"];
    $deadline = $_POST["deadline"];

    $conn->query("INSERT INTO tracker 
        (track_id, task_name, progress_type, priority, deadline) 
        VALUES 
        ('$track_id', '$task', '$type', '$priority', '$deadline')");
}


$track_id_filter = isset($_POST["track_id"]) ? $_POST["track_id"] : null;
if ($track_id_filter) {
    $result = $conn->query("SELECT * FROM tracker WHERE track_id='$track_id_filter'");
} else {
    $result = $conn->query("SELECT * FROM tracker");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Progress Tracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0c0020, #2b001b);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 40px;
            color: white;
        }

        .container {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 30px rgba(255, 0, 100, 0.4);
            text-align: center;
            width: 95%;
            max-width: 1100px;
            margin-bottom: 40px;
        }

        h1 {
            margin-bottom: 20px;
            font-size: 26px;
            color: #ffd1dc;
        }

        .task-form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            margin-bottom: 30px;
        }

        .task-form input,
        .task-form select {
            flex: 1 1 180px;
            padding: 10px;
            border: none;
            border-radius: 6px;
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            width: 100%;
        }

        input::placeholder,
        select {
            color: #eee;
        }

        select option {
            background-color: #260032;
            color: white;
        }

        .task-form button {
            flex: 1 1 150px;
            padding: 12px;
            background-color: #ae157d;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .task-form button:hover {
            background-color: #73034d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.05);
            color: white;
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        thead {
            background-color: rgba(255, 255, 255, 0.1);
            font-weight: bold;
        }

        .progress-bar {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            overflow: hidden;
            height: 20px;
            color: black;
            font-weight: bold;
            text-align: center;
        }

        .progress-bar > div {
            background: #ff4c9b;
            height: 100%;
            line-height: 20px;
        }

        .priority.low { color: #b3f0b3; }
        .priority.medium { color: #ffd700; }
        .priority.high { color: #ff8c00; }
        .priority.critical { color: #ff3b3b; }
    </style>
</head>
<body>

<div class="container">
    <h1>My Progress Tracker</h1>

    <form method="POST" class="task-form">
        <input type="text" name="track_id" placeholder="Track_id" required>
        <input type="text" name="task_name" placeholder="Task Name" required>
        <select name="progress_type" required>
            <option value="Not Started">Not Started</option>
            <option value="In Progress">In Progress</option>
            <option value="Completed">Completed</option>
        </select>
        <select name="priority" required>
            <option value="Low">Low</option>
            <option value="Medium">Medium</option>
            <option value="High">High</option>
        </select>
        <!-- <input type="number" name="percent_complete" min="0" max="100" placeholder="% Complete" required> -->
        <input type="date" name="deadline" required>
        <button type="submit" name="add_task">Add Task</button>
    </form>

    <table>
        <thead>
        <tr>
            <th>Track_id</th>
            <th>Task Name</th>
            <th>Progress Type</th>
            <th>Priority</th>
            <th>Deadline</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row["track_id"]); ?></td>
                <td><?= htmlspecialchars($row["task_name"]); ?></td>
                <td><?= htmlspecialchars($row["progress_type"]); ?></td>

                <td class="priority <?= strtolower($row["priority"]); ?>"><?= $row["priority"]; ?></td>

                <td><?= htmlspecialchars($row["deadline"]); ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>




