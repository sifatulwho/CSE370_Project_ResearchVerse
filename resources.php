<?php
session_start();
$conn = new mysqli("localhost", "root", "", "researchverse");

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $url = $conn->real_escape_string($_POST["url"]);
    $title = $conn->real_escape_string($_POST["title"]);
    $uploaded_by = $conn->real_escape_string($_POST["uploaded_by"]);
    $uploaded_date = $conn->real_escape_string($_POST["uploaded_date"]);
    $description = $conn->real_escape_string($_POST["description"]);

    $user_check = $conn->query("SELECT * FROM users WHERE username = '$uploaded_by'");

    if ($user_check->num_rows > 0) {
        $conn->query("INSERT INTO resources (url, title, uploaded_by, uploaded_date, description) 
                      VALUES ('$url', '$title', '$uploaded_by', '$uploaded_date', '$description')");
        header("Location: resources.php");
        exit();
    } else {
        $error = "Uploader username does not exist!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Resource</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0c0020, #2b001b);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 60px 0;
            min-height: 100vh;
            position: relative;
        }

        .hamburger {
            font-size: 30px;
            cursor: pointer;
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1001;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            top: 60px;
            right: 20px;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 10px;
            box-shadow: 0 0 15px rgba(255, 0, 100, 0.4);
            z-index: 1000;
        }

        .dropdown-content a {
            display: block;
            padding: 8px 12px;
            text-decoration: none;
            color: #ffd1dc;
            font-weight: bold;
            border-radius: 6px;
        }

        .dropdown-content a:hover {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .container {
            width: 500px;
            background: rgba(255,255,255,0.08);
            padding: 40px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 25px rgba(255, 0, 100, 0.4);
        }

        h1 {
            text-align: center;
        }

        .upload-form {
            margin-top: 30px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .upload-form input {
            padding: 10px;
            border: none;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .upload-form input::placeholder {
            color: #eee;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .upload-form button,
        .view-button {
            background: #ae157d;
            padding: 10px;
            border: none;
            border-radius: 6px;
            color: white;
            cursor: pointer;
            font-weight: bold;
            flex: 1;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .upload-form button:hover,
        .view-button:hover {
            background: #73034d;
        }

        .error-message {
            color: #ff6b6b;
            text-align: center;
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="hamburger" onclick="toggleDropdown()">☰</div>
    <div id="dropdown-menu" class="dropdown-content">
        <a href="profile1.php">Profile</a>
        <!-- <a href="connection.php">Find peer</a> -->
        <a href="resources.php">Resources</a>
        <!-- <a href="appointment.php">Updated</a> -->
        <a href="add_slot.php">Add slot</a>
        <a href="book_slot.php">Book slot</a>
        <a href="myappointment.php">Appointments</a>
        <a href="wishlist.php">Wishlist</a>
        <a href="progress_tracker.php">Progress Tracker</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h1>Upload a Resource</h1>
        <form method="POST" class="upload-form">
            <input type="url" name="url" placeholder="Resource URL" required>
            <input type="text" name="title" placeholder="Title" required>
            <input type="text" name="uploaded_by" placeholder="Username" required>
            <input type="date" name="uploaded_date" required>
            <input type="text" name="description" placeholder="Description" required>

            <div class="button-group">
                <button type="submit"> Upload Resource </button>
                <a href="show_resources.php" class="view-button">View All Resources</a>
                <a href="wishlist.php" class="view-button"> add to Wishlist? </a>
            </div>
        </form>
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
    </div>

   
    <script>
        function toggleDropdown() {
            const menu = document.getElementById("dropdown-menu");
            menu.style.display = (menu.style.display === "block") ? "none" : "block";
        }
    </script>
</body>
</html>


