<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['mobile'])) {
    $response = array("success" => false, "message" => "User not logged in");
    echo json_encode($response);
    exit();
}

// Get the updated balance data from the request
$requestPayload = json_decode(file_get_contents('php://input'), true);
$newBalance = $requestPayload['recharge'];

// Retrieve the mobile number from the session
$mobile = $_SESSION['mobile'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "earnify";
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Update the balance in the "register" table for the logged-in user
$sql = "UPDATE register SET recharge = $newBalance WHERE mobile = '$mobile'";
if (mysqli_query($conn, $sql)) {
    // Balance updated successfully, send success response
    $response = array("success" => true, "message" => "Balance updated successfully.");
} else {
    // Error occurred while updating balance, send error response
    $response = array("success" => false, "message" => "Error occurred while updating balance.");
}

mysqli_close($conn);

// Send the response back to the JavaScript code
header("Content-Type: application/json");
echo json_encode($response);
?>
