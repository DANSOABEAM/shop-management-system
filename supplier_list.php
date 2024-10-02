<?php
// Database connection
$host = 'localhost';
$dbname = 'shop_managemen';
$user = 'root';
$pass = '';
$conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);

// Search and filter functionality
$search_term = '';
if (isset($_GET['search'])) {
    $search_term = trim($_GET['search']);
}

// Fetch suppliers from the database
$query = "SELECT * FROM suppliers";
if ($search_term) {
    $query .= " WHERE supplier_name LIKE :search_term";
}
$stmt = $conn->prepare($query);

if ($search_term) {
    $search_param = "%" . $search_term . "%";
    $stmt->bindParam(':search_term', $search_param);
}
$stmt->execute();
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier List</title>
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
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4A90E2;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .search-container {
            margin-bottom: 20px;
            text-align: center;
        }

        input[type="text"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            width: 80%;
            max-width: 400px;
        }

        button {
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            background-color: #4A90E2;
            color: white;
            cursor: pointer;
            margin-left: 10px;
        }

        button:hover {
            background-color: #357ABD;
        }

        .no-results {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Supplier List</h1>

        <div class="search-container">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Search suppliers..." value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Supplier ID</th>
                    <th>Supplier Name</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($suppliers): ?>
                    <?php foreach ($suppliers as $supplier): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($supplier['supplier_id']); ?></td>
                            <td><?php echo htmlspecialchars($supplier['supplier_name']); ?></td>
                            <td><?php echo htmlspecialchars($supplier['contact_number']); ?></td>
                            <td><?php echo htmlspecialchars($supplier['email']); ?></td>
                            <td>
                                <a href="edit_supplier.php?id=<?php echo $supplier['supplier_id']; ?>">Edit</a> |
                                <a href="delete_supplier.php?id=<?php echo $supplier['supplier_id']; ?>" onclick="return confirm('Are you sure you want to delete this supplier?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="no-results">No suppliers found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
