<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "earnify";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Retrieve previous user's mobile number from session
session_start();
$mobile = $_SESSION['mobile'];

// SQL query
$sql = "SELECT previousUserMobile, presentUserMobile, level1Mobile, level2Mobile, level3Mobile
        FROM invite
        WHERE previousUserMobile = '$mobile'";

// Execute the query
$result = mysqli_query($conn, $sql);

// Prepare an array to store the results
$response = array();

if ($result) {
    // Fetch the data
    while ($inviteRow = mysqli_fetch_assoc($result)) {
        // Get the next users based on the previous user's mobile number
        $level1Mobile = $inviteRow['presentUserMobile'];
        $level2Mobile = null;
        $level3Mobile = null;

        // Get the next user's mobile number for level 2
        $sqlLevel2 = "SELECT presentUserMobile FROM invite WHERE previousUserMobile = '$level1Mobile'";
        $resultLevel2 = mysqli_query($conn, $sqlLevel2);
        if ($resultLevel2 && $rowLevel2 = mysqli_fetch_assoc($resultLevel2)) {
            $level2Mobile = $rowLevel2['presentUserMobile'];

            // Get the next user's mobile number for level 3
            $sqlLevel3 = "SELECT presentUserMobile FROM invite WHERE previousUserMobile = '$level2Mobile'";
            $resultLevel3 = mysqli_query($conn, $sqlLevel3);
            if ($resultLevel3 && $rowLevel3 = mysqli_fetch_assoc($resultLevel3)) {
                $level3Mobile = $rowLevel3['presentUserMobile'];
            }
        }

        // Build the response array for each record
        $record = array(
            "previousUserMobile" => $inviteRow['previousUserMobile'],
            "presentUserMobile" => $inviteRow['presentUserMobile'],
            "level1Mobile" => $level1Mobile,
            "level2Mobile" => $level2Mobile,
            "level3Mobile" => $level3Mobile
        );

        // Retrieve the level 1 balance, byinvite, and recharge
        $sqlLevel1 = "SELECT balance, byinvite, recharge, invested, withdraw FROM register WHERE mobile = '$level1Mobile'";
        $resultLevel1 = mysqli_query($conn, $sqlLevel1);
        if ($resultLevel1 && $rowLevel1 = mysqli_fetch_assoc($resultLevel1)) {
            $level1Balance = $rowLevel1['balance'];
            $level1ByInvite = $rowLevel1['byinvite'];
            $level1Recharge = $rowLevel1['recharge'];
            $level1Invested = $rowLevel1['invested'];
            $level1withdraw = $rowLevel1['withdraw'];

            // Add level 1 details to the record
            $record['level1Balance'] = $level1Balance;
            $record['level1ByInvite'] = $level1ByInvite;
            $record['level1Recharge'] = $level1Recharge;
            $record['level1Invested'] = $level1Invested;
            $record['level1withdraw'] = $level1withdraw;
        }

        // Retrieve the level 2 balance, byinvite, and recharge
        if ($level2Mobile) {
            $sqlLevel2 = "SELECT balance, byinvite, recharge, invested, withdraw FROM register WHERE mobile = '$level2Mobile'";
            $resultLevel2 = mysqli_query($conn, $sqlLevel2);
            if ($resultLevel2 && $rowLevel2 = mysqli_fetch_assoc($resultLevel2)) {
                $level2Balance = $rowLevel2['balance'];
                $level2ByInvite = $rowLevel2['byinvite'];
                $level2Recharge = $rowLevel2['recharge'];
                $level2Invested = $rowLevel2['invested'];
                $level2withdraw = $rowLevel2['withdraw'];

                // Add level 2 details to the record
                $record['level2Balance'] = $level2Balance;
                $record['level2ByInvite'] = $level2ByInvite;
                $record['level2Recharge'] = $level2Recharge;
                $record['level2Invested'] = $level2Invested;
                $record['level2withdraw'] = $level2withdraw;
            }
        }

        // Retrieve the level 3 balance, byinvite, and recharge
        if ($level3Mobile) {
            $sqlLevel3 = "SELECT balance, byinvite, recharge, invested, withdraw FROM register WHERE mobile = '$level3Mobile'";
            $resultLevel3 = mysqli_query($conn, $sqlLevel3);
            if ($resultLevel3 && $rowLevel3 = mysqli_fetch_assoc($resultLevel3)) {
                $level3Balance = $rowLevel3['balance'];
                $level3ByInvite = $rowLevel3['byinvite'];
                $level3Recharge = $rowLevel3['recharge'];
                $level3Invested = $rowLevel3['invested'];
                $level3withdraw = $rowLevel3['withdraw'];

                // Add level 3 details to the record
                $record['level3Balance'] = $level3Balance;
                $record['level3ByInvite'] = $level3ByInvite;
                $record['level3Recharge'] = $level3Recharge;
                $record['level3Invested'] = $level3Invested;
                $record['level3withdraw'] = $level3withdraw;
            }
        }

        // Calculate the sum of investments for all levels
        $sumLevelInvested = ($level1Invested ?? 0) + ($level2Invested ?? 0) + ($level3Invested ?? 0);

        // Add the sum of investments to the record
        $record['sumLevelInvested'] = $sumLevelInvested;

        // Calculate the sum of withdrawals for all levels
        $sumLevelWithdraw = ($level1withdraw ?? 0) + ($level2withdraw ?? 0) + ($level3withdraw ?? 0);

        // Add the sum of withdrawals to the record
        $record['sumLevelWithdraw'] = $sumLevelWithdraw;

        // Add the modified record to the response array
        $response[] = $record;
    }
} else {
    $response = array(
        "success" => false,
        "message" => "Query failed: " . mysqli_error($conn)
    );
}

// If there are no records, set default values
if (empty($response)) {
    $defaultRecord = array(
        "previousUserMobile" => null,
        "presentUserMobile" => null,
        "level1Mobile" => null,
        "level2Mobile" => null,
        "level3Mobile" => null,
        "level1Balance" => 0,
        "level1ByInvite" => 0,
        "level1Recharge" => 0,
        "level1Invested" => 0,
        "level2Balance" => 0,
        "level2ByInvite" => 0,
        "level2Recharge" => 0,
        "level2Invested" => 0,
        "level1withdraw" => 0,
        "level2withdraw" => 0,
        "level3withdraw" => 0,
        "level3Balance" => 0,
        "level3ByInvite" => 0,
        "level3Recharge" => 0,
        "level3Invested" => 0,
        "sumLevelInvested" => 0,
        "sumlevelWithdraw" => 0
    );

    $response[] = $defaultRecord;
}

// Close the connection
mysqli_close($conn);

// Send the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
