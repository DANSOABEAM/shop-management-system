<?php
// Database connection
$host = 'localhost';
$dbname = 'shop_managemen';
$user = 'root';
$pass = '';
$conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);

// Fetch customers
$query = "SELECT * FROM customers";
$stmt = $conn->prepare($query);
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Search functionality
if (isset($_POST['search'])) {
    $searchTerm = $_POST['searchTerm'];
    $query = "SELECT * FROM customers WHERE customer_name LIKE :searchTerm";
    $stmt = $conn->prepare($query);
    $searchParam = '%' . $searchTerm . '%';
    $stmt->bindParam(':searchTerm', $searchParam);
    $stmt->execute();
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer List</title>
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
            background-image: linear-gradient(rgba(0, 0, 0, 0.382),rgba(0, 0, 0, 0.974)),url(WhatsApp\ Image\ 2024-09-26\ at\ 13.09.52_c57ebbf2.jpg);
            color: white;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 28px;
            color: #4A90E2;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .search-form {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .search-form input[type="text"] {
            padding: 10px;
            margin-right: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            width: 250px;
        }

        .search-form button {
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            background-color: #4A90E2;
            color: white;
            cursor: pointer;
        }

        .search-form button:hover {
            background-color: #357ABD;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #4A90E2;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f4f4f9;
        }

        .view-profile {
            color: #4A90E2;
            text-decoration: none;
            padding: 5px 10px;
            border: 1px solid #4A90E2;
            border-radius: 5px;
        }

        .view-profile:hover {
            background-color: #4A90E2;
            color: white;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Customer List</h1>

        <form method="POST" class="search-form">
            <input type="text" name="searchTerm" placeholder="Search by customer name" required>
            <button type="submit" name="search">Search</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Customer ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($customers)): ?>
                    <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td><?php echo $customer['customer_id']; ?></td>
                            <td><?php echo $customer['customer_name']; ?></td>
                            <td><?php echo $customer['customer_email']; ?></td>
                            <td><?php echo $customer['customer_phone']; ?></td>
                            <td>
                                <a href="customer_profile.php?id=<?php echo $customer['customer_id']; ?>" class="view-profile">View Profile</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No customers found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
