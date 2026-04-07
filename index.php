<!DOCTYPE html>
<html>
<head>
  <title>Homepage</title>
  <style>
    body {
      background-image: url('image/indeximage.png');
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
      height: 100vh;
      margin: 0;
      font-family: Arial, sans-serif;
      color: white;
    }

    .center-content {
      text-align: center;
      position: absolute;
      top: 70%;
      left: 50%;
      transform: translate(-50%, -50%);
      padding: 40px;
      border-radius: 12px;
    }

    .center-content h1 {
      margin: 0;
      font-size: 3em;
    }

    .center-content p {
      margin: 10px 0 20px;
      font-size: 1.2em;
    }

    .auth-buttons a {
      background-color: #b835a0;
      color: #000000;
      text-decoration: none;
      margin: 0 10px;
      padding: 10px 20px;
      border-radius: 6px;
      font-family: cursive;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .auth-buttons a:hover {
      background-color: #611d50;
    }
  </style>
</head>
<body>
  <div class="center-content">

    <div class="auth-buttons">
      <a href="signin.php">Kick off the quest</a>
    </div>
  </div>
</body>
</html>

