<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "earnify";
$conn = mysqli_connect($servername, $username, $password, $dbname);

$response = array();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Retrieve the mobile number from the session
session_start();
$mobile = $_SESSION['mobile'];

// Fetch Withdraw Records
$withdrawSql = "SELECT withdrawamount, holder_name, status, created_at FROM withdrawApp WHERE mobile = '$mobile'";
$withdrawResult = mysqli_query($conn, $withdrawSql);

if ($withdrawResult) {
    $withdrawIndex = 1; // Start index from 1 for withdraw records

    if (mysqli_num_rows($withdrawResult) > 0) {
        while ($row = mysqli_fetch_assoc($withdrawResult)) {
            $withdrawAmount = $row['withdrawamount'];
            $bankName = $row['holder_name'];
            $status = $row['status'];
            $withdrawDateTime = $row['created_at'];

            // Splitting withdrawTime and withdrawDate
            $dateTimeParts = explode(" ", $withdrawDateTime);
            $withdrawTime = $dateTimeParts[1];
            $withdrawDate = $dateTimeParts[0];

            $response[] = array(
                "withdrawId" => $withdrawIndex,
                "withdrawAmount" => $withdrawAmount,
                "bankName" => $bankName,
                "status" => $status,
                "withdrawTime" => $withdrawTime,
                "withdrawDate" => $withdrawDate
            );

            $withdrawIndex++; // Increment the withdraw index
        }
    } else {
        $response[] = array(
            "withdrawId" => null,
            "withdrawAmount" => null,
            "bankName" => null,
            "status" => null,
            "withdrawTime" => null,
            "withdrawDate" => null
        );
    }
}

// Fetch Recharge Records
$rechargeSql = "SELECT recharge_amount, utr_number, status, created_at FROM rechargeapp WHERE mobile = '$mobile'";
$rechargeResult = mysqli_query($conn, $rechargeSql);

if ($rechargeResult) {
    $rechargeIndex = 1; // Start index from 1 for recharge records

    if (mysqli_num_rows($rechargeResult) > 0) {
        while ($row = mysqli_fetch_assoc($rechargeResult)) {
            $rechargeAmount = $row['recharge_amount'];
            $utrNumber = $row['utr_number'];
            $status = $row['status'];
            $rechargeDateTime = $row['created_at'];

            // Splitting rechargeTime and rechargeDate
            $dateTimeParts = explode(" ", $rechargeDateTime);
            $rechargeTime = $dateTimeParts[1];
            $rechargeDate = $dateTimeParts[0];

            $response[] = array(
                "rechargeId" => $rechargeIndex,
                "rechargeAmount" => $rechargeAmount,
                "utrNumber" => $utrNumber,
                "status" => $status,
                "rechargeTime" => $rechargeTime,
                "rechargeDate" => $rechargeDate
            );

            $rechargeIndex++; // Increment the recharge index
        }
    } else {
        $response[] = array(
            "rechargeId" => null,
            "rechargeAmount" => null,
            "utrNumber" => null,
            "status" => null,
            "rechargeTime" => null,
            "rechargeDate" => null
        );
    }
}

mysqli_close($conn);

header("Content-Type: application/json");
echo json_encode($response);
?>
