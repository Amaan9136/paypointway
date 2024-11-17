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
$claimValue = intval($requestPayload['claimValue']); // Convert to integer for safety
$clickbtn = intval($requestPayload['clickbtn']); // Convert to integer for safety

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

// Fetch the reward buttons data for the user from the "flags" table
$sql = "SELECT rewardbtn1, rewardbtn2, rewardbtn3 FROM flags WHERE mobile = '$mobile'";
$result = mysqli_query($conn, $sql);

if (!$result) {
    $response = array("success" => false, "message" => "Error fetching reward buttons: " . mysqli_error($conn));
    echo json_encode($response);
    exit();
}

// Fetch the row from the result
$row = mysqli_fetch_assoc($result);
$rewardbtn1 = $row['rewardbtn1'];
$rewardbtn2 = $row['rewardbtn2'];
$rewardbtn3 = $row['rewardbtn3'];

// Check if the clicked button is already claimed
if ($rewardbtn1 == 1 && $clickbtn === 1) {
    $response = array("success" => false, "message" => "Already claimed ₹100 reward!");
} elseif ($rewardbtn2 == 1 && $clickbtn === 2) {
    $response = array("success" => false, "message" => "Already claimed ₹400 reward!");
} elseif ($rewardbtn3 == 1 && $clickbtn === 3) {
    $response = array("success" => false, "message" => "Already claimed ₹1,000 reward!");
} else {
    // Update the balance in the "register" table for the logged-in user
    $sqlUpdateBalance = "UPDATE register
                        SET balance = balance + $claimValue,
                            byinvite = byinvite + $claimValue
                        WHERE mobile = '$mobile'";

    if (mysqli_query($conn, $sqlUpdateBalance)) {
        // Balance updated successfully, now update the clicked button in the "flags" table
        $sql2 = "";
        if ($clickbtn === 1) {
            $sql2 = "UPDATE flags SET rewardbtn1 = 1 WHERE mobile = '$mobile'";
        } elseif ($clickbtn === 2) {
            $sql2 = "UPDATE flags SET rewardbtn2 = 1 WHERE mobile = '$mobile'";
        } elseif ($clickbtn === 3) {
            $sql2 = "UPDATE flags SET rewardbtn3 = 1 WHERE mobile = '$mobile'";
        }

        if (!empty($sql2)) {
            if (mysqli_query($conn, $sql2)) {
                // Button updated successfully, send success response
                $response = array("success" => true, "message" => "Reward claimed successfully!");
            } else {
                // Error occurred while updating reward button, send error response
                $response = array("success" => false, "message" => "Error occurred while updating reward button: " . mysqli_error($conn));
            }
        } else {
            // Invalid button click, send error response
            $response = array("success" => false, "message" => "Invalid button click.");
        }
    } else {
        // Error occurred while updating balance, send error response
        $response = array("success" => false, "message" => "Error occurred while updating balance: " . mysqli_error($conn));
    }
}

mysqli_close($conn);

// Send the response back to the JavaScript code
header("Content-Type: application/json");
echo json_encode($response);
?>
