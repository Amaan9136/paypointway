<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['mobile'])) {
    $response = array("success" => false, "message" => "User not logged in");
    echo json_encode($response);
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get the updated balance data from the request
$requestPayload = json_decode(file_get_contents('php://input'), true);
$presentUserMobile = $requestPayload['presentUserMobile'];
$investMoney = $requestPayload['investMoney'];
$level1Mobile = $requestPayload['level1Mobile'];
$level2Mobile = $requestPayload['level2Mobile'];
$level3Mobile = $requestPayload['level3Mobile'];
$newBalance1 = $requestPayload['balance1'];
$newBalance2 = $requestPayload['balance2'];
$newBalance3 = $requestPayload['balance3'];
$newbyinvite1 = $requestPayload['byinvite1'];
$newbyinvite2 = $requestPayload['byinvite2'];
$newbyinvite3 = $requestPayload['byinvite3'];

// Check if the investMoney value is empty
if (empty($investMoney)) {
    $response = array("success" => false, "message" => "Investment amount is missing or invalid.");
    echo json_encode($response);
    exit();
}

// Retrieve the mobile number from the session
$mobile = $_SESSION['mobile'];

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

// Flag variable to track errors
$errorFlag = false;

// Update the invested for presentUserMobile
if (!empty($presentUserMobile)) {
    $sql = "UPDATE register SET invested = invested + $investMoney WHERE mobile = '$presentUserMobile'";
    if (!mysqli_query($conn, $sql)) {
        // Error occurred while updating invested for presentUserMobile
        $response = array("success" => false, "message" => "Error occurred while updating invested for presentUserMobile: " . mysqli_error($conn));
        $errorFlag = true;
    }
}

// Update the balances for level1Mobile, level2Mobile, and level3Mobile
if (!empty($level1Mobile)) {
    $sql = "UPDATE register SET balance = $newBalance1 , byinvite = $newbyinvite1 WHERE mobile = '$level1Mobile'";
    if (!mysqli_query($conn, $sql)) {
        // Error occurred while updating balance for level1Mobile
        $response = array("success" => false, "message" => "Error occurred while updating balance for level1Mobile: " . mysqli_error($conn));
        $errorFlag = true;
    }
}

if (!empty($level2Mobile)) {
    $sql = "UPDATE register SET balance = $newBalance2 , byinvite = $newbyinvite2 WHERE mobile = '$level2Mobile'";
    if (!mysqli_query($conn, $sql)) {
        // Error occurred while updating balance for level2Mobile
        $response = array("success" => false, "message" => "Error occurred while updating balance for level2Mobile: " . mysqli_error($conn));
        $errorFlag = true;
    }
}

if (!empty($level3Mobile)) {
    $sql = "UPDATE register SET balance = $newBalance3 , byinvite = $newbyinvite3 WHERE mobile = '$level3Mobile'";
    if (!mysqli_query($conn, $sql)) {
        // Error occurred while updating balance for level3Mobile
        $response = array("success" => false, "message" => "Error occurred while updating balance for level3Mobile: " . mysqli_error($conn));
        $errorFlag = true;
    }
}

// Check if any errors occurred during the updates
if ($errorFlag) {
    mysqli_close($conn);
    echo json_encode($response);
    exit();
}

// Success response
$response = array("success" => true, "message" => "Balances & Byinvite updated successfully.");
mysqli_close($conn);

// Send the response back to the JavaScript code
header("Content-Type: application/json");
echo json_encode($response);
?>
