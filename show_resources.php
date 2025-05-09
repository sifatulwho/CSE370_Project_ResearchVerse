?php
session_start();
$conn = new mysqli("localhost", "root", "", "researchverse");

$query = "
    SELECT r.url, r.title, r.uploaded_by, r.uploaded_date, r.description, u.username
    FROM resources r
    JOIN users u ON r.uploaded_by = u.username
    ORDER BY r.uploaded_date DESC";

$resources = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Resources</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0c0020, #2b001b);
            color: white;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 60px 0;
            min-height: 100vh;
        }

        .container {
            width: 800px;
            background: rgba(255,255,255,0.08);
            padding: 40px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 25px rgba(255, 0, 100, 0.4);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .resources-list ul {
            list-style: none;
            padding: 0;
        }

        .resources-list li {
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 6px;
            line-height: 1.6;
        }

        .resources-list a {
            color: #ff69b4;
            text-decoration: underline;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-bottom: 20px;
            color: #ddd;
        }

        .back-link:hover {
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="resources.php" class="back-link">← Back to Upload</a>
        <h1>All Uploaded Resources</h1>
        <div class="resources-list">
            <ul>
                <?php while ($res = $resources->fetch_assoc()): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($res["title"]); ?></strong><br>
                        <small>
                            <?php echo htmlspecialchars($res["description"]); ?><br>
                            Uploaded by <?php echo htmlspecialchars($res["uploaded_by"]); ?>
                            on <?php echo htmlspecialchars($res["uploaded_date"]); ?>
                        </small><br>
                        <a href="<?php echo htmlspecialchars($res["url"]); ?>" target="_blank">View Resource</a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>
</body>
</html>
