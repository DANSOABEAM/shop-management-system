<?php
session_start();

// Example session data (you should set this upon login)
$_SESSION['username'] = 'Admin'; // Replace with actual username logic
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .header {
            background-color: #007bff;
            color: #fff;
            padding: 15px 20px;
            border-radius: 0 0 20px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 26px;
        }
        .user-info {
            float: right;
            font-size: 14px;
        }
        .dashboard-container {
            padding: 20px;
        }
        .widget {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            transition: transform 0.2s;
        }
        .widget:hover {
            transform: scale(1.05);
        }
        .widget h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        .widget i {
            font-size: 24px;
            color: #007bff;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Dashboard</h1>
        <div class="user-info">
            <?php echo $_SESSION['username']; ?> | <a href="logout.php" style="color: white;">Logout</a>
        </div>
    </div>

    <div class="container dashboard-container">
        <div class="row">
            <div class="col-md-4">
                <div class="widget">
                    <i class="fas fa-plus"></i>
                    <h3><a href="add_product.php">Add Product</a></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="widget">
                    <i class="fas fa-shopping-cart"></i>
                    <h3><a href="view_sales.php">View Sales</a></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="widget">
                    <i class="fas fa-users"></i>
                    <h3><a href="manage_customers.php">Manage Customers</a></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="widget">
                    <i class="fas fa-chart-line"></i>
                    <h3><a href="analytics.php">View Analytics</a></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="widget">
                    <i class="fas fa-cogs"></i>
                    <h3><a href="settings.php">Settings</a></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="widget">
                    <i class="fas fa-box"></i>
                    <h3><a href="inventory.php">Inventory Management</a></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="widget">
                    <i class="fas fa-tags"></i>
                    <h3><a href="discounts.php">Manage Discounts</a></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="widget">
                    <i class="fas fa-barcode"></i>
                    <h3><a href="barcode.php">Barcode Management</a></h3>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
