<?php
// Database connection
$host = 'localhost';
$dbname = 'shop_managemen';
$user = 'root';
$pass = '';
$conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);

// Fetch the supplier data
$supplier_id = $_GET['id'] ?? null;

if ($supplier_id) {
    $stmt = $conn->prepare("SELECT * FROM suppliers WHERE supplier_id = :supplier_id");
    $stmt->bindParam(':supplier_id', $supplier_id);
    $stmt->execute();
    $supplier = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if the supplier was found
    if (!$supplier) {
        echo "<script>alert('Supplier not found.'); window.location.href='supplier_list.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Invalid supplier ID.'); window.location.href='supplier_list.php';</script>";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_name = $_POST['supplier_name'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];

    // Update supplier in the database
    $stmt = $conn->prepare("UPDATE suppliers SET supplier_name = :supplier_name, contact_number = :contact_number, email = :email WHERE supplier_id = :supplier_id");
    $stmt->bindParam(':supplier_name', $supplier_name);
    $stmt->bindParam(':contact_number', $contact_number);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':supplier_id', $supplier_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Supplier updated successfully!'); window.location.href='supplier_list.php';</script>";
    } else {
        echo "<script>alert('Error updating supplier.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Supplier</title>
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
    <h1>Edit Supplier</h1>
    <form method="POST" action="">
        <input type="text" name="supplier_name" placeholder="Supplier Name" value="<?php echo htmlspecialchars($supplier['supplier_name']); ?>" required>
        <input type="text" name="contact_number" placeholder="Contact Number" value="<?php echo htmlspecialchars($supplier['contact_number']); ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($supplier['email']); ?>" required>
        <button type="submit">Update Supplier</button>
    </form>
</div>

</body>
</html>
