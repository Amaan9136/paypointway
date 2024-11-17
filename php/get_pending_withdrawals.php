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

$sql = "SELECT mobile, withdrawamount, holder_name, ifsc_code, account_number, status FROM withdrawApp WHERE status = 'Pending'";
$result = mysqli_query($conn, $sql);

$response = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $response[] = $row;
    }
} else {
    $response[] = "No Pending cards found in the database!";
}

// Close the database connection
mysqli_close($conn);

// Send the response to JavaScript as JSON
echo json_encode($response);
?>
