<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mobileProject";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function validate($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Check if all required fields are set
if (isset($_POST['name'], $_POST['year'], $_POST['price'], $_POST['imageUrl'])) {
    $name = validate($_POST['name']);
    $year = validate($_POST['year']);
    $price = validate($_POST['price']);
    $image = $_POST['imageUrl'];

    // Decode the image from base64
    $decodedImage = base64_decode($image);

    // Directory to save uploaded images
    $targetDir = "uploads/";

    // Generate a unique filename for the image
    $imageName = uniqid() . '.jpg';

    // Path to save the image
    $targetFilePath = $targetDir . $imageName;

    // Check if uploads directory exists
    if (!file_exists($targetDir)) {
        // Create the uploads directory if it doesn't exist
        if (!mkdir($targetDir, 0777, true)) {
            die("failure: Failed to create uploads directory");
        }
    }

    // Save the image to the server
    if (file_put_contents($targetFilePath, $decodedImage) !== false) {
        // Image saved successfully, now insert data into database
        $stmt = $conn->prepare("INSERT INTO cars (name, year, price, imageUrl) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sids", $name, $year, $price, $targetFilePath);
        
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "failure: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "failure: Failed to save the image.";
    }
} else {
    echo "failure: Missing required POST parameters";
}

$conn->close();
?>
