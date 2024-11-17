<?php

// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "earnify";
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Retrieve the form data from the POST request
$utrNumber = $_POST['utrNumber'];
$rechargeAmount = $_POST['rechargeAmount'];

// Initialize the response message
$responseMessage = '';

// Perform further processing with the form data
// Retrieve the UTR number, recharge amount, mobile, and status from the 'rechargeapp' table
$sql = "SELECT recharge_amount, mobile, status FROM rechargeapp WHERE utr_number = '$utrNumber'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $dbUTRNumber = $utrNumber; // Set the UTR number from the form since it was found in the table
    $dbRechargeAmount = $row['recharge_amount'];
    $dbMobile = $row['mobile'];

    // Check the previous status and set the response message accordingly
    if ($row['status'] === 'Rejected') {
        $responseMessage = "Previous status: Rejected. ";
    } elseif ($row['status'] === 'Success') {
        $responseMessage = "Previous status: Success. ";
        echo json_encode(['message' => $responseMessage]); // Send the response and exit
        exit;
    }

    // Compare the UTR number and recharge amount from the form with the values in the table
    if ($utrNumber === $dbUTRNumber && $rechargeAmount === $dbRechargeAmount) {
        // Set the status of the recharge app to 'Success'
        $status = 'Success';
        $responseMessage .= "Updated: Success! \nUser Number: $dbMobile";
        $updateSql = "UPDATE rechargeapp SET status = '$status' WHERE utr_number = '$dbUTRNumber'";
        mysqli_query($conn, $updateSql);
        $updateRegisterSql = "UPDATE register SET recharge = recharge + $rechargeAmount WHERE mobile = '$dbMobile'";
        mysqli_query($conn, $updateRegisterSql);
    } else {
        // Set the status of the recharge app to 'Rejected'
        $status = 'Rejected';
        $responseMessage .= "Updated: Rejected! \nUser Number: $dbMobile";
        $updateSql = "UPDATE rechargeapp SET status = '$status' WHERE utr_number = '$dbUTRNumber'";
        if (mysqli_query($conn, $updateSql)) {
            echo json_encode(['message' => $responseMessage]); // Send the response and exit
            exit;
        } else {
            $responseMessage = "Error updating status: " . mysqli_error($conn);
        }
    }
} else {
    $responseMessage = "UTR number not found in the database!";
}

// Close the database connection
mysqli_close($conn);

// Send the response message back to JavaScript as JSON
$response = [
    'message' => $responseMessage
];

echo json_encode($response);
?>
