<?php
session_start();

// Create an empty response array
$response = array();

// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "earnify";
$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    $response = array(
        'success' => false,
        'message' => 'Database connection failed: ' . mysqli_connect_error()
    );
} else {
    // Prepare and sanitize the data for insertion
    $withdrawamount = mysqli_real_escape_string($conn, $_POST['amount']);
    $withdrawalPassword = mysqli_real_escape_string($conn, $_POST['withdrawalPassword']);

    // Retrieve mobile from session
    $mobile = $_SESSION['mobile'];

    // Retrieve withdrawalPassword and balance from the register table
    $sql = "SELECT withdrawalPassword, balance , withdraw
            FROM register
            WHERE mobile = '$mobile'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $registeredWithdrawalPassword = $row['withdrawalPassword'];
        $balance = $row['balance'];
        $oldwithdrawamount = $row['withdraw'];

        if ($withdrawalPassword !== $registeredWithdrawalPassword) {
            $response = array(
                'success' => false,
                'message' => 'Incorrect Withdrawal password!'
            );
        } elseif ($withdrawamount > $balance) {
            $response = array(
                'success' => false,
                'message' => 'Insufficient Balance!'
            );
        } elseif ($withdrawamount < 150) {
            $response = array(
                'success' => false,
                'message' => 'Withdrawal amount should be at least ₹150'
            );
        } else {

            // Check if any row has a non-zero limit value in the withdrawApp table (for already submitted requests)
            $sql = "SELECT COUNT(*) AS count
            FROM withdrawApp
            WHERE mobile = '$mobile' AND `limit` <> 0";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            $countSubmitted = $row['count'];

            
            if ($countSubmitted > 0) { //more than limit=0 is more than 1
                $response = array(
                    'success' => false,
                    'message' => 'Withdrawal Application already submitted!'
                );
            }
            else {
                // Retrieve bank details
                $sql = "SELECT holder_name, account_number, ifsc_code 
                        FROM bankdetails
                        WHERE mobile = '$mobile'";
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    $account_number = mysqli_real_escape_string($conn, $row['account_number']);
                    $ifsc_code = mysqli_real_escape_string($conn, $row['ifsc_code']);
                    $holder_name = mysqli_real_escape_string($conn, $row['holder_name']);
                    $newwithdrawamount=($withdrawamount - (0.1 * $withdrawamount)); //cut the tax

                    // Insert the data into the withdrawApp table if no errors occurred
                    $sql = "INSERT INTO withdrawApp (mobile, withdrawamount, account_number, ifsc_code, holder_name, `limit`)
                            VALUES ('$mobile', '$newwithdrawamount', '$account_number', '$ifsc_code', '$holder_name', 1)";

                    $withdrawamountstore= $newwithdrawamount + $oldwithdrawamount;

                    if (mysqli_query($conn, $sql)) {
                        $newbalance = $balance - $withdrawamount;

                        // Update the balance in the register table
                        $sql = "UPDATE register SET balance = '$newbalance' , withdraw=$withdrawamountstore WHERE mobile = '$mobile'";
                        if (mysqli_query($conn, $sql)) {
                            $response = array(
                                'success' => true,
                                'message' => 'Withdrawal Application Successfully Sent!'
                            );
                        } else {
                            $response = array(
                                'success' => false,
                                'message' => 'Error updating balance: ' . mysqli_error($conn)
                            );
                        }
                    } else {
                        $response = array(
                            'success' => false,
                            'message' => 'Error inserting withdrawal application: ' . mysqli_error($conn)
                        );
                    }
                } else {
                    $response = array(
                        'success' => false,
                        'message' => 'Bank details not found for the mobile number.'
                    );
                }
            }
        }
    } else {
        $response = array(
            'success' => false,
            'message' => 'Mobile Number does not exist! Please Login and Retry again.'
        );
    }

    // Close the database connection
    mysqli_close($conn);
}

// Send the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
