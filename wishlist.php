<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: signin.php");
    exit();
}

$host = "localhost";
$user = "root";
$pass = "";
$db = "researchverse";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$current_user = $_SESSION["user"];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['remove_url'])) {
    $remove_url = $conn->real_escape_string($_POST['remove_url']);
    $conn->query("DELETE FROM wishlist WHERE username = '$current_user' AND url = '$remove_url'");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['url'], $_POST['deadline'], $_POST['status']) && !isset($_POST['remove_url'])) {
    $username = $current_user;
    $url = $_POST['url'];
    $deadline = $_POST['deadline'];
    $status = $_POST['status'];

    $user_stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
    $user_stmt->bind_param("s", $username);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $username = $user_result->fetch_assoc()['username'] ?? null;
    $user_stmt->close();

    $res_stmt = $conn->prepare("SELECT url FROM resources WHERE url = ?");
    $res_stmt->bind_param("s", $url);
    $res_stmt->execute();
    $res_result = $res_stmt->get_result();
    $url = $res_result->fetch_assoc()['url'] ?? null;
    $res_stmt->close();

    if ($username && $url) {
        $insert = $conn->prepare("INSERT INTO wishlist (username, url, deadline, status) VALUES (?, ?, ?, ?)");
        $insert->bind_param("ssss", $username, $url, $deadline, $status);
        $insert->execute();
        $insert->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "<script>alert('Invalid username or resource URL.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Wishlist</title>
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0c0020, #2b001b);
            height: 100vh;
            color: white;
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

        .form-container {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 30px rgba(255, 0, 100, 0.4);
            text-align: center;
            width: 350px;
            margin: 80px auto 30px;
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

        .wishlist {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 30px rgba(255, 0, 100, 0.3);
            width: 600px;
            margin: auto;
        }

        .wishlist-entry {
            border-bottom: 1px solid #555;
            padding: 10px 0;
        }

        .remove-btn {
            margin-top: 8px;
            background-color:rgb(174, 21, 123);
            border: none;
            color: white;
            padding: 6px 14px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .remove-btn:hover {
            background-color:rgb(113, 3, 79);
        }

        a {
            color: #ffd1dc;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>User Wishlist</h2>
    <form method="POST" action="">
        <input type="hidden" name="username" value="<?php echo htmlspecialchars($current_user); ?>">
        <input type="text" name="url" placeholder="Resource URL" required>
        <input type="date" name="deadline" required>
        <select name="status" required>
            <option value="pending">Pending</option>
            <option value="completed">Completed</option>
        </select>
        <button type="submit">Add to Wishlist</button>
    </form>
</div>

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

<div class="wishlist">
    <h2>Your Wishlist</h2>
    <?php
    $sql = "SELECT r.title, r.url, w.deadline, w.status
            FROM wishlist w
            JOIN resources r ON w.url = r.url
            WHERE w.username = ?
            ORDER BY w.deadline ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $current_user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='wishlist-entry'>";
            echo "<strong>Title:</strong> " . htmlspecialchars($row['title']) . "<br>";
            echo "<strong>URL:</strong> <a href='" . htmlspecialchars($row['url']) . "'>" . htmlspecialchars($row['url']) . "</a><br>";
            echo "<strong>Deadline:</strong> " . htmlspecialchars($row['deadline']) . "<br>";
            echo "<strong>Status:</strong> " . htmlspecialchars($row['status']) . "<br>";
            echo "<form method='POST' style='margin-top: 6px;'>
                    <input type='hidden' name='remove_url' value='" . htmlspecialchars($row['url']) . "'>
                    <button type='submit' class='remove-btn'>Remove from Wishlist</button>
                  </form>";
            echo "</div>";
        }
    } else {
        echo "<p>No entries found in your wishlist.</p>";
    }

    $stmt->close();
    $conn->close();
    ?>
</div>

<script>
function toggleDropdown() {
    const menu = document.getElementById("dropdown-menu");
    menu.style.display = (menu.style.display === "block") ? "none" : "block";
}
</script>

</body>
</html>
