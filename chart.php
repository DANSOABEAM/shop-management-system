<?php
// Start session and connect to the database
session_start();

$host = 'localhost';
$dbname = 'shop_managemen'; // Corrected typo in database name
$user = 'root';
$pass = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch sales data
$salesDataQuery = $conn->query("SELECT sale_date, SUM(total_amount) as total_sales FROM sales GROUP BY sale_date ORDER BY sale_date ASC");
$salesData = $salesDataQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch inventory levels
$inventoryDataQuery = $conn->query("SELECT id, stock_quantity FROM products");
$inventoryData = $inventoryDataQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch customer data
$customerDataQuery = $conn->query("SELECT customer_id, COUNT(sale_id) as purchases FROM sales GROUP BY customer_id ORDER BY purchases DESC LIMIT 5");
$customerData = $customerDataQuery->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for charts
$saleDates = [];
$totalSales = [];
foreach ($salesData as $sale) {
    $saleDates[] = $sale['sale_date'];
    $totalSales[] = $sale['total_sales'];
}

$productIds = [];
$quantities = [];
foreach ($inventoryData as $inventory) {
    $productIds[] = "Product " . $inventory['id'];
    $quantities[] = $inventory['stock_quantity'];
}

$customerIds = [];
$purchases = [];
foreach ($customerData as $customer) {
    $customerIds[] = "Customer " . $customer['customer_id'];
    $purchases[] = $customer['purchases'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
        }
        h5{
            font-size:15px;
        }
        h2 {
            text-align: center;
            color: #333;
            margin: 10px 0;
            font-size: 15px;
        }
        .card {
            border: none;
            border-radius: 8px;
            border-bottom: 1px solid rgb(243, 18, 18);
            padding: 10px;
            margin-top:13%;
        }
        .chart-container {
            position: relative;
            height: 200px; /* Set a fixed height for charts */
        }
        .nav-link {
            font-size: 14px;
            padding: 10px 5px;
            color:white;
            text-align: center;

        }
        .logo {
            max-width: 50%; /* Responsive logo */
            height: auto;
        }
        .logo-container {
            text-align: center;
            padding: 10px 0; /* Space around the logo */
        }

        .sidebar-sticky{
            background-color: rgba(255, 0, 0, 0.711);
            margin-top:9%;
           
        }
    </style>
</head>
<body>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="sidebar-sticky">
                    <div class="logo-container">
                        <img src="img.png" alt="Company Logo" class="logo"> <!-- Add your logo image path -->
                    </div>
                 
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                <i class="fas fa-home"></i> Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="add_product.php">
                                <i class="fas fa-plus"></i> Add Product
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="view_sales.php">
                                <i class="fas fa-shopping-cart"></i> View Sales
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_customers.php">
                                <i class="fas fa-users"></i> Manage Customers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="inventory.php">
                                <i class="fas fa-archive"></i> Inventory Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="discounts.php">
                                <i class="fas fa-tags"></i> Manage Discounts
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="analytics.php">
                                <i class="fas fa-chart-line"></i> Analytics
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="settings.php">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ml-sm-auto col-lg-10 px-2">
                <h2>Analytics Dashboard</h2>
                <div class="row">
                    <!-- Sales Chart -->
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-chart-line"></i> Sales Trend</h5>
                                <div class="chart-container">
                                    <canvas id="salesChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Inventory Chart -->
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-warehouse"></i> Inventory Levels</h5>
                                <div class="chart-container">
                                    <canvas id="inventoryChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Purchases Chart -->
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-users"></i> Top Customers</h5>
                                <div class="chart-container">
                                    <canvas id="customersChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Sales Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($saleDates); ?>,
                datasets: [{
                    label: 'Total Sales',
                    data: <?php echo json_encode($totalSales); ?>,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Sales over Time'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Sales Amount'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Sale Date'
                        }
                    }
                }
            }
        });

        // Inventory Chart
        const inventoryCtx = document.getElementById('inventoryChart').getContext('2d');
        const inventoryChart = new Chart(inventoryCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($productIds); ?>,
                datasets: [{
                    label: 'Inventory Quantity',
                    data: <?php echo json_encode($quantities); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Current Inventory Levels'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Stock Quantity'
                        }
                    }
                }
            }
        });

        // Customer Purchases Chart
        const customersCtx = document.getElementById('customersChart').getContext('2d');
        const customersChart = new Chart(customersCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($customerIds); ?>,
                datasets: [{
                    label: 'Customer Purchases',
                    data: <?php echo json_encode($purchases); ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(255, 159, 64, 0.6)',
                        'rgba(255, 205, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(54, 162, 235, 0.6)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(255, 205, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Top Customers'
                    }
                }
            }
        });
    </script>
</body>
</html>
