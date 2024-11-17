<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "earnify";
$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to calculate and return the reward
function calculateReward($conn) {
    $mobile = $_SESSION['mobile'];

    // Query to get previousUserMobile and presentUserMobile
    $sql = "SELECT previousUserMobile, presentUserMobile
            FROM invite
            WHERE presentUserMobile = '$mobile'";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $inviteRow = mysqli_fetch_assoc($result);
        $previousUserMobile = $inviteRow['previousUserMobile'];
        $presentUserMobile = $inviteRow['presentUserMobile'];

        // Query to get reward for the previousUserMobile
        $sql = "SELECT reward
                FROM flags
                WHERE mobile = '$previousUserMobile'";

        $sql2 = "SELECT purchase
        FROM flags
        WHERE mobile = '$presentUserMobile'";

        $result = mysqli_query($conn, $sql);
        $result2 = mysqli_query($conn, $sql2);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $row2 = mysqli_fetch_assoc($result2);
            $reward = intval($row['reward']);
            $purchase = intval($row2['purchase']);

            // Check if purchase is initially 0 for presentUserMobile
            if ($purchase == 0) {
                // Increment the purchase by 1 for presentUserMobile
                $purchase += 1;
                $updatePurchaseSql = "UPDATE flags SET purchase = $purchase WHERE mobile = '$presentUserMobile'";
                mysqli_query($conn, $updatePurchaseSql);

                // Increment the reward by 1 for previousUserMobile
                $reward += 1;
                $updateRewardSql = "UPDATE flags SET reward = $reward WHERE mobile = '$previousUserMobile'";
                mysqli_query($conn, $updateRewardSql);
            }

            // Return the reward
            return $reward;
        }
    }

    return 0; // Return 0 if no reward is available or applicable
}

$reward = calculateReward($conn);

// Close the database connection if it's no longer needed
mysqli_close($conn);

// Send the reward value as a response
echo $reward;
?>
