<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "earnify";
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$FormMobile = $_POST['mobile'];
$FormStatus = $_POST['status'];

$responseMessage = '';

$updateSql = "UPDATE withdrawApp SET status = '$FormStatus' WHERE `limit` = 1 AND mobile = '$FormMobile'";

if (mysqli_query($conn, $updateSql)) {
    $responseMessage = "Updated status: $FormStatus!";
} else {
    $responseMessage = "Error updating status: " . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);

// Send the response message back to JavaScript as JSON
$response = [
    'message' => $responseMessage
];

echo json_encode($response);

?>
