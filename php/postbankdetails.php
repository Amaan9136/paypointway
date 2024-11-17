<?php
session_start();

// Create an empty response array
$response = array();

// Check if the user is logged in and the session mobile number matches the input mobile
if (!isset($_SESSION['mobile']) || $_SESSION['mobile'] !== $_POST['mobile']) {
    $response = array(
        'success' => false,
        'message' => 'Registered Mobile Number does not match'
    );
} else {
    // Connect to the database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "earnify";
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check if the connection was successful
    if (!$conn) {
        $response = array(
            'success' => false,
            'message' => 'Database connection failed: ' . mysqli_connect_error()
        );
    } else {
        // Prepare and sanitize the data for insertion
        $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
        $bankName = mysqli_real_escape_string($conn, $_POST['holder_name']);
        $accountNumber = mysqli_real_escape_string($conn, $_POST['account_number']);
        $ifscCode = mysqli_real_escape_string($conn, $_POST['ifsc_code']);
        $withdrawalPassword = mysqli_real_escape_string($conn, $_POST['withdrawalPassword']);

        // Check if the withdrawal password matches the one in the register table
        $sql = "SELECT withdrawalPassword
            FROM register
            WHERE mobile = '$mobile'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $registeredWithdrawalPassword = $row['withdrawalPassword'];

            if ($withdrawalPassword !== $registeredWithdrawalPassword) {
                $response = array(
                    'success' => false,
                    'message' => 'Incorrect Withdrawal password!'
                );
            }
        } else {
            $response = array(
                'success' => false,
                'message' => 'Mobile Number does not exist! Please Login and Retry again.'
            );
        }

        // Check if the account number matches the confirm account number
        $confirmAccountNumber = $_POST['confirm_account_number'];
        if ($accountNumber !== $confirmAccountNumber) {
            $response = array(
                'success' => false,
                'message' => 'Account Number should be the same as Confirm Account Number'
            );
        }

        // Insert the data into the bankdetails table if no errors occurred
        if (empty($response)) {
            $sql = "INSERT INTO bankdetails (mobile, holder_name, account_number, ifsc_code)
                VALUES ('$mobile', '$bankName', '$accountNumber', '$ifscCode')";

            if (mysqli_query($conn, $sql)) {
                $response = array(
                    'success' => true,
                    'message' => 'Bank Details Successfully Saved!'
                );
            } else {
                $response = array(
                    'success' => false,
                    'message' => 'Error: ' . mysqli_error($conn)
                );
            }
        }

        // Close the database connection
        mysqli_close($conn);
    }
}

// Set the content type header to JSON
header('Content-Type: application/json');

// Send the JSON response
echo json_encode($response);
?>
