<?php
// Database connection
$host = 'localhost';
$dbname = 'shop_managemen';
$user = 'root';
$pass = '';
$conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);

// Initialize variables for form data
$customer_id = $_GET['id'] ?? '';
$customer_name = $customer_email = $customer_phone = '';
$errors = [];

// Fetch existing customer data
if ($customer_id) {
    $stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = :customer_id");
    $stmt->bindParam(':customer_id', $customer_id);
    $stmt->execute();
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($customer) {
        $customer_name = $customer['customer_name'];
        $customer_email = $customer['customer_email'];
        $customer_phone = $customer['customer_phone'];
    } else {
        $errors[] = 'Customer not found.';
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $customer_name = trim($_POST['customer_name']);
    $customer_email = trim($_POST['customer_email']);
    $customer_phone = trim($_POST['customer_phone']);

    // Validate the input
    if (empty($customer_name)) {
        $errors[] = 'Customer name is required.';
    }
    if (empty($customer_email) || !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email address is required.';
    }
    if (empty($customer_phone)) {
        $errors[] = 'Customer phone is required.';
    }

    // If no errors, update the database
    if (empty($errors)) {
        $query = "UPDATE customers SET customer_name = :customer_name, customer_email = :customer_email, customer_phone = :customer_phone WHERE customer_id = :customer_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':customer_name', $customer_name);
        $stmt->bindParam(':customer_email', $customer_email);
        $stmt->bindParam(':customer_phone', $customer_phone);
        $stmt->bindParam(':customer_id', $customer_id);

        if ($stmt->execute()) {
            echo "<script>alert('Customer updated successfully!'); window.location.href='customer_list.php';</script>";
        } else {
            $errors[] = 'Failed to update customer.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            color: #333;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #4A90E2;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input[type="text"], input[type="email"] {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            background-color: #4A90E2;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #357ABD;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Edit Customer</h1>

        <!-- Display errors if any -->
        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="text" name="customer_name" placeholder="Customer Name" value="<?php echo htmlspecialchars($customer_name); ?>" required>
            <input type="email" name="customer_email" placeholder="Customer Email" value="<?php echo htmlspecialchars($customer_email); ?>" required>
            <input type="text" name="customer_phone" placeholder="Customer Phone" value="<?php echo htmlspecialchars($customer_phone); ?>" required>
            <button type="submit">Update Customer</button>
        </form>
    </div>

</body>
</html>
