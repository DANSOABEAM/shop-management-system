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

// Fetch all categories from the database
$category_stmt = $conn->prepare("SELECT * FROM categories");
$category_stmt->execute();
$categories = $category_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'];
    $category = $_POST['category']; // Category now comes from dropdown
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    $supplier = $_POST['supplier'];

    // Validate form data (basic validation)
    if (empty($name) || empty($category) || empty($price) || empty($stock_quantity) || empty($supplier)) {
        $error = "All fields are required!";
    } else {
        // Insert the new product into the database
        $stmt = $conn->prepare("INSERT INTO products (name, category, price, stock_quantity, supplier) VALUES (:name, :category, :price, :stock_quantity, :supplier)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':stock_quantity', $stock_quantity);
        $stmt->bindParam(':supplier', $supplier);

        if ($stmt->execute()) {
            $success = "<h6>Product added successfully!</h6>";
        } else {
            $error = "Failed to add the product!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="register.css">
    <link rel="stylesheet" href="nav.css">
   
</head>
<body>
<ul>
    <li>   <a href="leave_apply.php">Leave Application <i class="fas fa-users"></i></a></li> 
    <li>  <a href="add_employee.php">Add Employee <i class="fas fa-calendar-alt"></i></a></li> 
    <li> <a href="attendance .php">Attendance <i class="fas fa-money-check-alt"></i></a></li> 
    <li>   <a href="view_emplyee.php">View Employees <i class="fas fa-clock"></i></a></li> 
    <li>  <a href="reporting.php">View Reports <i class="fas fa-chart-line"></i></a></li> 
    <li> <a href="leave.php">Leave Management <i class="fas fa-chart-line"></i></a>
        <li> <a href="payroll.php">Payroll <i class="fas fa-chart-line"></i></a></li> 
        <li> <a href="employee_payroll.php">Employee Payroll <i class="fas fa-chart-line"></i></a><br>
            <li> <a href="employee_attendance.php">employee attendance record <i class="fas fa-chart-line"></i></a><br>

    </ul>



<div class="form-container">
    <h4>Add New Product</h4>

    <!-- Success or Error Message -->
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php elseif (isset($success)): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>

    <!-- Product Form -->
    <form method="POST" action="">
        <label for="name">Product Name:</label> <br>
        <input type="text" id="name" name="name" required class="inputs"> <br>

        <label for="category">Category:</label> <br>
        <select id="category" name="category" required class="inputs">
            <option value="">Select a category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['name']; ?>"><?php echo $category['name']; ?></option>
            <?php endforeach; ?>
        </select>
<br>
        <label for="price">Price (in GHS):</label> <br>
        <input type="number" id="price" name="price" step="0.01" required class="inputs"> <br>

        <label for="stock_quantity">Stock Quantity:</label> <br>
        <input type="number" id="stock_quantity" name="stock_quantity" required class="inputs"> <br>

        <label for="supplier">Supplier:</label> <br>
        <input type="text" id="supplier" name="supplier" required class="inputs"> <br>

        <button type="submit" class="btn2">Add Product</button>
    </form>
</div>

</body>
</html>
