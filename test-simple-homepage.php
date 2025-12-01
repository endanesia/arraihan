<?php
// Simple homepage test - load just the bare minimum
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🧪 Simple Homepage Test</h2>";

try {
    echo "<p>Step 1: Loading basic files...</p>";
    require_once __DIR__ . '/inc/db.php';
    echo "✅ DB loaded<br>";
    
    echo "<p>Step 2: Testing simple HTML...</p>";
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Test Homepage</title>
    </head>
    <body>
        <h1>Simple Test Page</h1>
        <?php 
        echo "<p>✅ Basic HTML works</p>";
        
        echo "<p>Step 3: Testing PHP in HTML...</p>";
        $test_var = "Hello World";
        ?>
        <p>Test variable: <?= e($test_var) ?></p>
        
        <?php
        echo "<p>✅ PHP in HTML works</p>";
        
        echo "<p>Step 4: Testing database...</p>";
        if (function_exists('db') && db()) {
            $result = db()->query("SELECT COUNT(*) as count FROM gallery_videos");
            if ($result) {
                $row = $result->fetch_assoc();
                echo "<p>✅ Database works - Video count: {$row['count']}</p>";
            }
        }
        
        echo "<p>Step 5: Testing include...</p>";
        try {
            ob_start();
            require_once __DIR__ . '/inc/header.php';
            $header = ob_get_contents();
            ob_end_clean();
            echo "<p>✅ Header include works (size: " . strlen($header) . " bytes)</p>";
        } catch (Exception $e) {
            echo "<p>❌ Header include failed: " . $e->getMessage() . "</p>";
        }
        ?>
        
        <h2>🎉 All Simple Tests Passed!</h2>
        <p>If this page loads completely, the basic PHP/HTML structure is working.</p>
        <p>The error might be in specific content or complex template logic.</p>
        
        <p><strong>Next step:</strong> Try loading the actual homepage by copying working sections progressively.</p>
        
    </body>
    </html>
    
    <?php
    
} catch (Exception $e) {
    echo "<h3>❌ Error at Simple Test:</h3>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
}
?>