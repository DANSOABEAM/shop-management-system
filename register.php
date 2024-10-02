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
    $confirm_password = $_POST['confirm_password'];

    // Validate input
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Check if the username already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $error = "Username is already taken";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert the new user into the database
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashed_password);
            
            if ($stmt->execute()) {
                // Redirect to the login page
                header("Location: login.php");
                exit;
            } else {
                $error = "There was an error registering the user";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>  
    <link rel="stylesheet" href="register.css">
</head>
<body>
<img src="img.png" alt="juric logo"/>


<div class="container">
    <h4>LoVe enterprice</h4>
    <h2>Register</h2>
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST" action="">
       
        <input type="text" id="username" name="username" required  required placeholder="username"> <br>

        
        <input type="password" id="password" name="password" required placeholder="password"> <br>

      
        <input type="password" id="confirm_password" name="confirm_password" required  required placeholder="confirm password">  <br>
        <p></p>

        <input type="submit" value="Register" class="btn">
    </form>
</div>
<script>

    document.querySelector(".btn2").addEventListener("click", show)

    function show(){
        alert(h6);
    }
</script>

</body>
</html>
