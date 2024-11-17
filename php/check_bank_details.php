<?php
session_start();

// Perform the necessary database connection and setup

// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "earnify";
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the mobile number from the session
$mobile = $_SESSION['mobile']; // Replace 'mobile' with the actual session key for mobile number

// Query the bankdetails table using the mobile number
$query = "SELECT holder_name FROM bankdetails WHERE mobile = '$mobile'";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    // Bank details exist, retrieve the holder_name value
    $row = $result->fetch_assoc();
    $holder_name = $row["holder_name"];
    
    // Return the holder_name value as a JSON response
    echo json_encode($holder_name);
} else {
    // Bank details not found, set holder_name to null
    $holder_name = null;
    
    // Return an error message as a JSON response
    $error_message = "Bank details not filled for mobile number: " . $mobile;
    echo json_encode(array('error' => $error_message));
}

// Close the database connection
$conn->close();
?>
