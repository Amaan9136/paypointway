<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "earnify";
$conn = mysqli_connect($servername, $username, $password, $dbname);

$response = array();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Retrieve the recharge amount and UTR number from the request
$rechargeAmount = $_POST['rechargeAmount'];
$utrNumber = $_POST['utrNumber'];

$mobileNumber = $_SESSION['mobile']; // Retrieve the mobile number from the session variable

// Check if the UTR number already exists in the database
$existingUtrQuery = "SELECT * FROM rechargeapp WHERE utr_number = '$utrNumber'";
$existingUtrResult = mysqli_query($conn, $existingUtrQuery);

if ($existingUtrResult !== false) {
    if (mysqli_num_rows($existingUtrResult) > 0) {
        // UTR number already exists
        $existingUtrRow = mysqli_fetch_assoc($existingUtrResult);
        $existingUtrStatus = $existingUtrRow['status'];

        if ($existingUtrStatus === 'Success') {
            // UTR number already used
            $response = array('status' => 'error', 'message' => 'UTR number already used!');
        } elseif ($existingUtrStatus === 'Rejected') {
            // UTR number already used
            $response = array('status' => 'error', 'message' => 'UTR number already used!');
        } elseif ($existingUtrStatus === 'Pending') {
            // UTR number under verification
            $response = array('status' => 'error', 'message' => 'UTR number under verification!');
        }
    } else {
        // UTR number is new or first application, store the recharge details in the database
        $sql = "INSERT INTO rechargeapp (mobile, recharge_amount, utr_number) VALUES ('$mobileNumber', '$rechargeAmount', '$utrNumber')";

        if (mysqli_query($conn, $sql)) {
            $response = array('status' => 'success', 'message' => 'UTR Number submitted! Please wait until verification is complete.');
        } else {
            $response = array('status' => 'error', 'message' => 'Failed to store recharge details: ' . mysqli_error($conn));
        }
    }
} else {
    // UTR number is new or first application, store the recharge details in the database
    $sql = "INSERT INTO rechargeapp (mobile, recharge_amount, utr_number) VALUES ('$mobileNumber', '$rechargeAmount', '$utrNumber')";

    if (mysqli_query($conn, $sql)) {
        $response = array('status' => 'success', 'message' => 'UTR Number submitted! Please wait until verification is complete.');
    } else {
        $response = array('status' => 'error', 'message' => 'Failed to store recharge details: ' . mysqli_error($conn));
    }
}

// Send the response back to the client
header('Content-Type: application/json');
echo json_encode($response);

// Close the database connection
mysqli_close($conn);
?>
