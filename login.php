<?php
// Start the session
session_start();

// Database connection parameters
$host = 'localhost';
$dbname = 'shop_managemen';
$user = 'root'; // Change this to your DB username
$pass = ''; // Change this to your DB password

// Connect to the database
$conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to fetch the user from the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // If user exists, verify the password
    if ($user && password_verify($password, $user['password'])) {
        // Set session variables and redirect to dashboard
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: chart.php");
        exit;
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
   <link rel="stylesheet" href="register.css">
       
</head>
<body>
<img src="img.png" alt="juric logo"/>

<div class="container">
    <h4>Juric enterprise</h4>
    <h2>Login</h2>
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST" action="">
      
        <input type="text" id="username" name="username" required  placeholder="username"> <br>

       
        <input type="password" id="password" name="password" required  placeholder="password"> <br>

        <input type="submit" value="Login" class="btn">
    </form>
</div>

</body>
</html>
