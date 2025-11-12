<!DOCTYPE html>
<html>
<head>
    <title>PHP Test</title>
</head>
<body>
    <h1>✅ PHP Test</h1>
    <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
    <p><strong>Server Name:</strong> <?php echo $_SERVER['SERVER_NAME'] ?? 'N/A'; ?></p>
    <p><strong>Request URI:</strong> <?php echo $_SERVER['REQUEST_URI'] ?? 'N/A'; ?></p>
    <p><strong>Waktu:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
    
    <h2>Test Database Connection:</h2>
    <?php
    // Test tanpa include db.php
    $mysqli = new mysqli('localhost', 'root', '', 'arraihan_db');
    
    if ($mysqli->connect_error) {
        echo '<p style="color: red;">❌ Database Error: ' . $mysqli->connect_error . '</p>';
    } else {
        echo '<p style="color: green;">✅ Database Connected!</p>';
        
        // Test query
        $result = $mysqli->query("SELECT COUNT(*) as total FROM packages");
        if ($result) {
            $row = $result->fetch_assoc();
            echo '<p>Total Packages: ' . $row['total'] . '</p>';
        }
    }
    ?>
</body>
</html>
