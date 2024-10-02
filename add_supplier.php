<?php
// Database connection
$host = 'localhost';
$dbname = 'shop_managemen';
$user = 'root';
$pass = '';
$conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_name = $_POST['supplier_name'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];

    // Insert new supplier into the database
    $stmt = $conn->prepare("INSERT INTO suppliers (supplier_name, contact_number, email) VALUES (:supplier_name, :contact_number, :email)");
    $stmt->bindParam(':supplier_name', $supplier_name);
    $stmt->bindParam(':contact_number', $contact_number);
    $stmt->bindParam(':email', $email);
    
    if ($stmt->execute()) {
        echo "<script>alert('Supplier added successfully!'); window.location.href='supplier_list.php';</script>";
    } else {
        echo "<script>alert('Error adding supplier.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Supplier</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #4A90E2;
            margin-bottom: 20px;
        }

        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #4A90E2;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #357ABD;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Add Supplier</h1>
    <form method="POST" action="">
        <input type="text" name="supplier_name" placeholder="Supplier Name" required>
        <input type="text" name="contact_number" placeholder="Contact Number" required>
        <input type="email" name="email" placeholder="Email" required>
        <button type="submit">Add Supplier</button>
    </form>
</div>

</body>
</html>
