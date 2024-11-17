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

// Fetch Purchase Records
$purchaseSql = "SELECT mobile, product, invest_money, income_daily, total_income, purchase_date, daily_count , income_days FROM purchases WHERE mobile = '$mobile'";
$purchaseResult = mysqli_query($conn, $purchaseSql);

if (mysqli_num_rows($purchaseResult) > 0) {
    while ($row = mysqli_fetch_assoc($purchaseResult)) {
        $mobile = $row['mobile'];
        $product = $row['product'];
        $invest_money = $row['invest_money'];
        $income_daily = $row['income_daily'];
        $income_days = $row['income_days'];
        $total_income = $row['total_income'];
        $purchaseDateTime = $row['purchase_date'];
        $daily_count = $row['daily_count'];

        // Splitting purchaseTime and purchaseDate
        $dateTimeParts = explode(" ", $purchaseDateTime);
        $purchase_time = $dateTimeParts[1];
        $purchase_date = $dateTimeParts[0];

        $response[] = array(
            "mobile" => $mobile,
            "product" => $product,
            "invest_money" => $invest_money,
            "income_daily" => $income_daily,
            "daily_count" => $daily_count,
            "income_days" => $income_days,
            "purchase_date" => $purchase_date,
            "purchase_time" => $purchase_time,
            "total_income" => $total_income
        );
    }
}

mysqli_close($conn);

header("Content-Type: application/json");
echo json_encode($response);
?>
