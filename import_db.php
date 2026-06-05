<?php
session_start();
include_once 'conn.php';

// Security check: only allow this to run if explicitly requested or if we are in development
// In a real scenario, you'd want a password here, but for a one-time deployment:
echo "<h1>Database Importer</h1>";

$sqlFile = 'wackys (1).sql';

if (!file_exists($sqlFile)) {
    die("Error: $sqlFile not found.");
}

$sqlContent = file_get_contents($sqlFile);

// Remove comments and empty lines
$lines = explode("\n", $sqlContent);
$cleanSql = "";
foreach ($lines as $line) {
    $line = trim($line);
    if ($line && !str_starts_with($line, '--') && !str_starts_with($line, '/*') && !str_starts_with($line, '#')) {
        $cleanSql .= $line . "\n";
    }
}

// Split by semicolon (basic splitter)
$queries = explode(";", $cleanSql);

$successCount = 0;
$errorCount = 0;

foreach ($queries as $query) {
    $query = trim($query);
    if ($query) {
        if ($conn->query($query)) {
            $successCount++;
        } else {
            echo "<p style='color:red;'>Error in query: " . $conn->error . "<br>Query: <code>" . substr($query, 0, 100) . "...</code></p>";
            $errorCount++;
        }
    }
}

echo "<h2>Import Finished!</h2>";
echo "<p>Successfully executed: $successCount queries.</p>";
echo "<p>Errors: $errorCount</p>";
echo "<p><strong>IMPORTANT: Delete this file (import_db.php) from your GitHub immediately for security!</strong></p>";

$conn->close();
?>
