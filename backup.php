<?php
// Start session and connect to the database
session_start();

$host = 'localhost';
$dbname = 'shop_managemen';
$user = 'root';
$pass = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Backup function
function backupDatabase($host, $user, $pass, $dbname, $backupFile) {
    $command = "mysqldump --opt -h $host -u $user -p$pass $dbname > $backupFile";
    system($command, $result);
    return $result;
}

// Restore function
function restoreDatabase($host, $user, $pass, $dbname, $backupFile) {
    $command = "mysql -h $host -u $user -p$pass $dbname < $backupFile";
    system($command, $result);
    return $result;
}

// Handle backup request
if (isset($_POST['backup'])) {
    $backupFile = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    if (backupDatabase($host, $user, $pass, $dbname, $backupFile) === 0) {
        $success_message = "Backup created successfully: $backupFile";
    } else {
        $error_message = "Failed to create backup.";
    }
}

// Handle restore request
if (isset($_POST['restore'])) {
    $backupFile = $_POST['backup_file'];
    if (file_exists($backupFile)) {
        if (restoreDatabase($host, $user, $pass, $dbname, $backupFile) === 0) {
            $success_message = "Database restored successfully from $backupFile.";
        } else {
            $error_message = "Failed to restore the database.";
        }
    } else {
        $error_message = "Backup file does not exist.";
    }
}

// List backup files
$backupFiles = glob('backup_*.sql');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup and Restore</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .form-container {
            background-color: white;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        input[type="submit"], select {
            padding: 10px;
            margin: 10px 0;
            border: none;
            background-color: #5cb85c;
            color: white;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #4cae4c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <h2>Backup and Restore Page</h2>

    <div class="form-container">
        <form method="POST" action="">
            <h3>Manual Backup</h3>
            <?php if (isset($success_message)) echo "<p style='color:green;'>$success_message</p>"; ?>
            <?php if (isset($error_message)) echo "<p style='color:red;'>$error_message</p>"; ?>
            <input type="submit" name="backup" value="Create Backup">
        </form>
    </div>

    <div class="form-container">
        <form method="POST" action="">
            <h3>Restore from Backup</h3>
            <select name="backup_file" required>
                <option value="">Select Backup File</option>
                <?php foreach ($backupFiles as $file): ?>
                    <option value="<?php echo $file; ?>"><?php echo $file; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="submit" name="restore" value="Restore Backup">
        </form>
    </div>

    <div class="form-container">
        <h3>Available Backup Files</h3>
        <table>
            <tr>
                <th>Backup File</th>
                <th>Date Created</th>
            </tr>
            <?php foreach ($backupFiles as $file): ?>
                <tr>
                    <td><?php echo $file; ?></td>
                    <td><?php echo date("Y-m-d H:i:s", filemtime($file)); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

</body>
</html>
