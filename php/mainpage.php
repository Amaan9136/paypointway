<?php
// update_balance.php

// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "earnify";
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Receive and process the updated balance data
$requestPayload = json_decode(file_get_contents('php://input'), true);
$newBalance = $requestPayload['balance'];
$mobile = $requestPayload['mobile'];

// Perform necessary operations to update the balance in your backend system
$sql = "UPDATE register SET balance = $newBalance WHERE mobile = '$mobile'";
if (mysqli_query($conn, $sql)) {
    // Balance updated successfully, send success response
    $response = [
        'success' => true,
        'message' => 'Balance updated successfully.'
    ];
} else {
    // Error occurred while updating balance, send error response
    $response = [
        'success' => false,
        'message' => 'Error occurred while updating balance.'
    ];
}

mysqli_close($conn);

header('Content-Type: application/json');
echo json_encode($response);
?>
