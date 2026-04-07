<?php
session_start();


ini_set('display_errors', 1);
error_reporting(E_ALL);


if (!isset($_SESSION['user'])) {
    header("Location: profile.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "researchverse");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['user'];
$upload_dir = "uploads/";


$stmt = $conn->prepare("SELECT 
    u.username, u.first_name, u.last_name, u.email, u.role, u.dob,
    p.bio, p.synopsis, p.phone, p.photo 
FROM users u
LEFT JOIN profile p ON u.username = p.username 
WHERE u.username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bio = $conn->real_escape_string($_POST["bio"]);
    $synopsis = $conn->real_escape_string($_POST["synopsis"]);
    $phone = $conn->real_escape_string($_POST["phone"]);

    
    if (!empty($_FILES["photo"]["name"])) {
        $photo_name = basename($_FILES["photo"]["name"]);
        $target = $upload_dir . $photo_name;

        if ($_FILES["photo"]["error"] === UPLOAD_ERR_OK) {
            move_uploaded_file($_FILES["photo"]["tmp_name"], $target);
            $conn->query("UPDATE profile SET photo='$photo_name' WHERE username='$username'");
        }
    }

    
    $checkProfile = $conn->prepare("SELECT username FROM profile WHERE username = ?");
    $checkProfile->bind_param("s", $username);
    $checkProfile->execute();
    $exists = $checkProfile->get_result()->num_rows > 0;

    if ($exists) {
        $update = $conn->prepare("UPDATE profile SET bio=?, synopsis=?, phone=? WHERE username=?");
        $update->bind_param("ssss", $bio, $synopsis, $phone, $username);
        $update->execute();
    } else {
        $insert = $conn->prepare("INSERT INTO profile (username, bio, synopsis, phone, photo) VALUES (?, ?, ?, ?, '')");
        $insert->bind_param("ssss", $username, $bio, $synopsis, $phone);
        $insert->execute();
    }

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #0c0020, #2b001b);
            color: #fff;
            padding: 40px;
        }

        .container {
            max-width: 700px;
            margin: auto;
            background: rgba(255, 255, 255, 0.05);
            padding: 30px;
            border-radius: 10px;
            backdrop-filter: blur(12px);
            box-shadow: 0 0 20px rgba(255, 105, 180, 0.3);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #ffb6d5;
        }

        label {
            display: block;
            margin-top: 15px;
        }

        input[type="text"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: none;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        input[type="file"] {
            opacity: 0.6;
        }

        textarea {
            resize: vertical;
        }

        button {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            background: #ff69b4;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }

        img {
            display: block;
            margin: 20px auto 10px auto;
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #ff69b4;
        }

        .info {
            margin-bottom: 20px;
            text-align: center;
        }

        .info p {
            margin: 5px 0;
        }

        .file-input-wrapper {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>
        <?php echo htmlspecialchars(ucfirst($user["first_name"] ?? '')) . " " . htmlspecialchars(ucfirst($user["last_name"] ?? '')); ?>'s Profile
    </h2>

    <div class="info">
        <p><strong>Username:</strong> <?php echo htmlspecialchars($user["username"] ?? ''); ?></p>
    </div>

 
    <img src="<?php echo (!empty($user['photo']) ? $upload_dir . $user['photo'] :"image/jpg,image/jpeg,image/png"); ?>" >

    
    <div class="file-input-wrapper">
        <input type="file" class="box" accept="image/jpg,image/jpeg,image/png" name="photo" form="profileForm">
    </div>

    <form method="POST" enctype="multipart/form-data" id="profileForm">
        <label>First Name</label>
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" disabled>

        <label>Last Name</label>
        <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" disabled>

        <label>Date Of Birth</label>
        <input type="text" name="dob" value="<?php echo htmlspecialchars($user['dob'] ?? ''); ?>" disabled>

        <label>Role</label>
        <input type="text" name="role" value="<?php echo htmlspecialchars($user['role'] ?? ''); ?>" disabled>

        <label>Email</label>
        <input type="text" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" disabled>

        <label>Phone</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">

        <label>Bio</label>
        <textarea name="bio" rows="4"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>

        <label>Synopsis</label>
        <textarea name="synopsis" rows="4"><?php echo htmlspecialchars($user['synopsis'] ?? ''); ?></textarea>

        <button type="submit">Update Profile</button>
    </form>
</div>

</body>
</html>




