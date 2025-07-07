<?php
$servername = "localhost"; // or your database host
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "lebron_database"; // The database we created

try {
    // Create a PDO connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // echo "Connected successfully"; // Uncomment to check the connection
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
