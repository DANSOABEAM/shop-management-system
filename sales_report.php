<?php
// Database connection
$host = 'localhost';
$dbname = 'shop_managemen';
$user = 'root';
$pass = '';
$conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);

// Set default date range to today
$startDate = date('Y-m-d');
$endDate = date('Y-m-d');

if (isset($_POST['range'])) {
    $range = $_POST['range'];
    switch ($range) {
        case 'daily':
            $startDate = $endDate = date('Y-m-d');
            break;
        case 'weekly':
            $startDate = date('Y-m-d', strtotime('-7 days'));
            break;
        case 'monthly':
            $startDate = date('Y-m-01');
            break;
    }
}

// Fetch sales based on selected range
$query = "SELECT product_id, SUM(quantity) as total_quantity, SUM(total_price) as total_sales, sale_date 
          FROM sales WHERE sale_date BETWEEN :startDate AND :endDate GROUP BY sale_date";
$stmt = $conn->prepare($query);
$stmt->bindParam(':startDate', $startDate);
$stmt->bindParam(':endDate', $endDate);
$stmt->execute();
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Export as CSV or PDF if requested
if (isset($_POST['export'])) {
    $exportType = $_POST['export'];

    if ($exportType == 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="sales_report.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, array('Product ID', 'Quantity Sold', 'Total Sales', 'Sale Date'));
        foreach ($sales as $sale) {
            fputcsv($output, $sale);
        }
        fclose($output);
        exit();
    } elseif ($exportType == 'pdf') {
        require('fpdf.php');
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);
        
        $pdf->Cell(40, 10, 'Product ID');
        $pdf->Cell(40, 10, 'Quantity Sold');
        $pdf->Cell(40, 10, 'Total Sales');
        $pdf->Cell(40, 10, 'Sale Date');
        $pdf->Ln();

        foreach ($sales as $sale) {
            $pdf->Cell(40, 10, $sale['product_id']);
            $pdf->Cell(40, 10, $sale['total_quantity']);
            $pdf->Cell(40, 10, $sale['total_sales']);
            $pdf->Cell(40, 10, $sale['sale_date']);
            $pdf->Ln();
        }

        $pdf->Output('D', 'sales_report.pdf');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Reports</title>
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
            font-size: 28px;
            color: #4A90E2;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: #f1f1f1;
            padding: 20px;
            border-radius: 10px;
          
        }

        .report-form {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .report-form select, 
        .report-form button {
            padding: 10px;
            margin-right: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .report-form button {
            background-color: #f1f1f1;
            color: white;
            border: none;
            cursor: pointer;
        }

        .report-form button:hover {
           
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
            background-color: #f1f1f1;
        }

        th {
            color:white;
            font-size:10px;
            background-color: rgba(255, 0, 0, 0.711);
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f4f4f9;
        }

        .export-buttons {
            text-align: center;
            margin-top: 20px;
            color:white;
            font-size:10px;
          
          
        }

        .export-buttons button {
            padding: 10px 20px;
            margin-right: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            color:white;
            font-size:10px;
          
          background-color: rgba(255, 0, 0, 0.711);
        }

        .export-buttons button.csv {
            color:white;
          
          background-color: green;
            color: white;
            font-size:10px;
        }

        .export-buttons button.pdf {
           
          
          background-color: green;
            color: white;
            font-size:10px;
        }

        .export-buttons button:hover {
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            .report-form {
                flex-direction: column;
            }

            .report-form select, 
            .report-form button {
                width: 100%;
                margin-bottom: 10px;
            }
            
        }

        #button{
            color:white;
            font-size:10px;
          background-color: rgba(255, 0, 0, 0.711);
          margin-right:70%;
          
        }

        h1{
            font-size:15px;
        }

        #range{
          margin-right:auto;
            font-size:13px;
            width:20%;
            border: 0px;
            border-bottom: 1px solid red;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Sales Reports</h1>

        <form method="POST" class="report-form">
            <select name="range" id="range">
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
            </select>
            <button type="submit" id="button">Generate Report</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Quantity Sold</th>
                    <th>Total Sales</th>
                    <th>Sale Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($sales)): ?>
                    <?php foreach ($sales as $sale): ?>
                        <tr>
                            <td><?php echo $sale['product_id']; ?></td>
                            <td><?php echo $sale['total_quantity']; ?></td>
                            <td><?php echo $sale['total_sales']; ?></td>
                            <td><?php echo $sale['sale_date']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No sales data found for this period.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="export-buttons">
            <form method="POST">
                <input type="hidden" name="range" value="<?php echo $range; ?>">
                <button type="submit" name="export" value="csv" class="csv">Export as CSV</button>
                <button type="submit" name="export" value="pdf" class="pdf">Export as PDF</button>
            </form>
        </div>
    </div>

</body>
</html>
