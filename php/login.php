<?php
session_start(); // Start the session

// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "earnify";

$mysqli = new mysqli($servername, $username, $password, $dbname);

// Check for errors
if ($mysqli->connect_error) {
    die("Failed to connect to the database: " . $mysqli->connect_error);
}

// Get the login data from the request
$input = json_decode(file_get_contents('php://input'), true);
$mobile = $input["mobile"];
$password = $input["password"];

// Escape special characters to prevent SQL injection
$mobile = $mysqli->real_escape_string($mobile);
$password = $mysqli->real_escape_string($password);

// Query the database to check if the mobile number and password match
$result = $mysqli->query("SELECT * FROM register WHERE mobile = '$mobile'");

if ($result->num_rows === 1) {
    // Mobile number exists, check password
    $row = $result->fetch_assoc();
    if ($row["password"] === $password) {
        // Login successful
        $_SESSION['logged_in'] = true; // Set the logged_in session variable
        $_SESSION['mobile'] = $mobile; // Store the mobile number in the session
        $balance = $row["balance"]; // Retrieve the balance from the database

        $response = array(
            "success" => true,
            "message" => "Login successful",
            "balance" => $balance
        );
    } else {
        // Invalid password
        $response = array(
            "success" => false,
            "message" => "Invalid password"
        );
    }
} else {
    // Invalid mobile number
    $response = array(
        "success" => false,
        "message" => "Mobile number not registered"
    );
}

// Send the JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
