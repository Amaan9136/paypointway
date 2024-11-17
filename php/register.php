<?php
// register.php

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

// Get registration data from the request body
$data = json_decode(file_get_contents("php://input"), true);
$mobile = $data["mobile"];
$password = $data["password"];
$withdrawalPassword = $data["withdrawalPassword"];
$invitationCode2store = strtoupper(bin2hex(random_bytes(8))); //generating invite code
$balance = 80; // Specify the signup bonus amount here

$invitationCode = $data["invitationCode"];

// Check if the mobile number already exists in the "register" table
$sql = "SELECT * FROM register WHERE mobile = '$mobile'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    // Mobile number already exists, send error response
    $response = array("success" => false, "message" => "User already exists.");
} else {

    // Check if the invitation code exists in the "register" table
if (!empty($invitationCode)) {
    // Sanitize the input to prevent SQL injection
    $sanitizedInvitationCode = mysqli_real_escape_string($conn, $invitationCode);

    $inviteCodeSql = "SELECT mobile FROM register WHERE invitationCode2store = '$sanitizedInvitationCode'";
    $inviteCodeResult = mysqli_query($conn, $inviteCodeSql);

    if (mysqli_num_rows($inviteCodeResult) > 0) {
        $inviteCodeRow = mysqli_fetch_assoc($inviteCodeResult);
        $previousUserMobile = $inviteCodeRow['mobile'];
        $presentUserMobile = $mobile;

        // Store the hierarchical relationship in the invite table
        $inviteSql = "INSERT INTO invite (previousUserMobile, presentUserMobile) 
                        VALUES ('$previousUserMobile', '$presentUserMobile')";
        mysqli_query($conn, $inviteSql);

        // Calculate and update the hierarchical levels for the present user
        $level1Mobile = $previousUserMobile;

        $previousSql = "SELECT previousUserMobile FROM invite WHERE presentUserMobile = '$level1Mobile'";
        $previousResult = mysqli_query($conn, $previousSql);
        $previousRow = mysqli_fetch_assoc($previousResult);

        $level2Mobile = $previousRow['previousUserMobile'];

        if (!empty($level2Mobile)) {
            $previousSql = "SELECT previousUserMobile FROM invite WHERE presentUserMobile = '$level2Mobile'";
            $previousResult = mysqli_query($conn, $previousSql);
            $previousRow = mysqli_fetch_assoc($previousResult);

            $level3Mobile = $previousRow['previousUserMobile'];
        } else {
            $level3Mobile = null;
        }

        // Update the hierarchical levels for the present user
        $updateSql = "UPDATE invite SET 
                      level1Mobile = '$level1Mobile',
                      level2Mobile = " . ($level2Mobile ? "'$level2Mobile'" : "NULL") . ",
                      level3Mobile = " . ($level3Mobile ? "'$level3Mobile'" : "NULL") . "
                      WHERE presentUserMobile = '$presentUserMobile'";
        mysqli_query($conn, $updateSql);
    } else {
        // No matching invitation code found, send error response
        $response = array("success" => false, "message" => "Wrong Invite Code!");
        mysqli_close($conn);
        header("Content-Type: application/json");
        echo json_encode($response);
        exit;
    }
}

    
$sql1 = "INSERT INTO register (mobile, password, withdrawalPassword, invitationCode2store, balance) 
        VALUES ('$mobile', '$password', '$withdrawalPassword', '$invitationCode2store', $balance)";

if (mysqli_query($conn, $sql1)) {

    $sql2 = "INSERT INTO flags (mobile, reward , purchase , rewardbtn1, rewardbtn2, rewardbtn3)
            VALUES ('$mobile', 0, 0, 0, 0, 0)";

    if (mysqli_query($conn, $sql2)) {
        $response = array("success" => true);
    } else {
        $response = array("success" => false, "message" => "Error occurred while registering, please try again later.");
    }
} else {
    $response = array("success" => false, "message" => "Error occurred while registering, please try again later.");
}
}

mysqli_close($conn);

// Send the response back to the JavaScript code
header("Content-Type: application/json");
echo json_encode($response);
?>
