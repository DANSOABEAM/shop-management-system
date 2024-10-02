<?php
session_start();

// Ensure the user is logged in and their user_id is in the session
if (!isset($_SESSION['user_id'])) {
    die('Error: User not logged in!');
}

// Database connection
$host = 'localhost';
$db = 'shop_managemen'; // Update to your database name
$user = 'root'; // Your database username
$pass = ''; // Your database password
$conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Assuming the user is logged in and their ID is stored in the session
$user_id = $_SESSION['user_id'];

// Fetch current user data
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission for updating profile information
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $update_stmt = $conn->prepare("UPDATE users SET username = :username, email = :email, phone = :phone WHERE user_id = :user_id");
    $update_stmt->bindParam(':username', $username);
    $update_stmt->bindParam(':email', $email);
    $update_stmt->bindParam(':phone', $phone);
    $update_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $update_stmt->execute();

    $success_message = "Profile updated successfully!";
}

// Handle form submission for changing password
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verify current password
    if (password_verify($current_password, $user_data['password'])) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $password_stmt = $conn->prepare("UPDATE users SET password = :password WHERE user_id = :user_id");
            $password_stmt->bindParam(':password', $hashed_password);
            $password_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $password_stmt->execute();

            $success_message = "Password changed successfully!";
        } else {
            $error_message = "New passwords do not match!";
        }
    } else {
        $error_message = "Current password is incorrect!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
        }
        h1, h2 {
            text-align: center;
        }
        form {
            border: 1px solid #ccc;
            padding: 20px;
            margin-top: 20px;
            border-radius: 5px;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Profile Page</h1>

    <?php
    if (isset($success_message)) {
        echo "<p class='success'>$success_message</p>";
    }
    if (isset($error_message)) {
        echo "<p class='error'>$error_message</p>";
    }
    ?>

    <h2>Update Profile Information</h2>
    <form method="POST">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>

        <label for="phone">Phone</label>
        <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($user_data['phone']); ?>">

        <button type="submit" name="update_profile">Update Profile</button>
    </form>

    <h2>Change Password</h2>
    <form method="POST">
        <label for="current_password">Current Password</label>
        <input type="password" name="current_password" id="current_password" required>

        <label for="new_password">New Password</label>
        <input type="password" name="new_password" id="new_password" required>

        <label for="confirm_password">Confirm New Password</label>
        <input type="password" name="confirm_password" id="confirm_password" required>

        <button type="submit" name="change_password">Change Password</button>
    </form>
</div>

</body>
</html>
