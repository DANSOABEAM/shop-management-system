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

// Handle delete product request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
    $stmt->bindParam(':id', $delete_id);
    $stmt->execute();
    header("Location: product_list.php");
    exit;
}

// Fetch products from the database
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

$query = "SELECT * FROM products WHERE name LIKE :search";
$params = [':search' => "%$search%"];

if ($category) {
    $query .= " AND category = :category";
    $params[':category'] = $category;
}

if ($sort == 'name') {
    $query .= " ORDER BY name ASC";
} elseif ($sort == 'price') {
    $query .= " ORDER BY price ASC";
}

$stmt = $conn->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
            font-size: 15px;
            text-align: center;
           
        }
        .actions a {
            margin-right: 10px;
            color: #007bff;
            text-decoration: none;
         
            border: 0px;
            border-bottom: 1px solid red;
            padding:5px;
            cursor: pointer;

        }
        .actions a:hover {
            text-decoration: underline;
        }
        form {
            margin-bottom: 20px;
        }
        .search-input, .filter-select, .sort-select {
            padding: 5px;
            margin-right: 10px;
            border: 0px;
            border-bottom: 1px solid red;
        }
        .delete-btn {
            color: red;
            cursor: pointer;
        }

        thead{
            background-color: rgba(255, 0, 0, 0.711);
            color:white;
            font-size:10px;
        }

        button{
            background-color: rgba(255, 0, 0, 0.711);
            color:white;
            border: 0px;
            border-bottom: 1px solid red;
            width: 10%;
            cursor: pointer;
        }
    </style>
</head>
<body>

<h2>Product List</h2>

<!-- Search, Filter, and Sort Form -->
<form method="GET" action="">
    <input type="text" name="search" class="search-input" placeholder="Search by name" value="<?php echo $search; ?>">
    <select name="category" class="filter-select">
        <option value="">Filter by Category</option>
        <option value="Electronics" <?php if ($category == 'Electronics') echo 'selected'; ?>>Electronics</option>
        <option value="Clothing" <?php if ($category == 'Clothing') echo 'selected'; ?>>Clothing</option>
        <option value="Food" <?php if ($category == 'Food') echo 'selected'; ?>>Food</option>
    </select>
    <select name="sort" class="sort-select">
        <option value="">Sort by</option>
        <option value="name" <?php if ($sort == 'name') echo 'selected'; ?>>Name</option>
        <option value="price" <?php if ($sort == 'price') echo 'selected'; ?>>Price</option>
    </select>
    <button type="submit">Apply</button>
</form>

<!-- Product Table -->
<table>
    <thead>
        <tr>
            <th>Product Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['category']); ?></td>
                    <td><?php echo htmlspecialchars($product['price']); ?></td>
                    <td class="actions">
                        <a href="edit_product.php?id=<?php echo $product['id']; ?>">Edit</a>
                        <a href="product_list.php?delete_id=<?php echo $product['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">No products found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
