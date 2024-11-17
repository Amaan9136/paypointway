<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['mobile'])) {
    $response = array("success" => false, "message" => "User not logged in");
    echo json_encode($response);
    exit();
}

// Retrieve the mobile number from the session
$mobileNumber = $_SESSION['mobile'];

// Validate the mobile number
if (!is_numeric($mobileNumber)) {
    $response = array("success" => false, "message" => "Invalid mobile number");
    echo json_encode($response);
    exit();
}
// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "earnify";

$mysqli = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($mysqli->connect_error) {
    $response = array("success" => false, "message" => "Failed to connect to the database: " . $mysqli->connect_error);
    echo json_encode($response);
    exit();
}

// Get the purchase data from the request
$input = json_decode(file_get_contents('php://input'), true);

// Validate the required fields
if (!isset($input["product"]) || !isset($input["investMoney"]) || !isset($input["incomeDaily"]) || !isset($input["incomeDays"]) || !isset($input["total_income"]) || !isset($input["gift"])) {
    $response = array("success" => false, "message" => "Required fields are missing");
    echo json_encode($response);
    exit();
}

// Sanitize and validate the input values
$product = $mysqli->real_escape_string($input["product"]);
$investMoney = (int) str_replace(',', '', $mysqli->real_escape_string($input["investMoney"])); // Remove commas from the investMoney value
$incomeDaily = (int) str_replace(',', '', $mysqli->real_escape_string($input["incomeDaily"]));
$incomeDays = (int) $mysqli->real_escape_string($input["incomeDays"]);
$total_income = (int) str_replace(',', '', $mysqli->real_escape_string($input["total_income"]));
$gift = (int) $mysqli->real_escape_string($input["gift"]);

// Insert the purchase data into the database
$query = "INSERT INTO purchases (mobile, product, invest_money, income_daily, income_days, total_income, gift) VALUES ('$mobileNumber', '$product', $investMoney, $incomeDaily, $incomeDays, $total_income, $gift)";

if ($mysqli->query($query)) {
    // Purchase data inserted successfully
    $response = array(
        "success" => true,
        "message" => "Purchase data stored successfully"
    );
} else {
    // Error occurred while inserting the purchase data
    $response = array(
        "success" => false,
        "message" => "Failed to store purchase data: " . $mysqli->error
    );
}

// Close the database connection
$mysqli->close();

// Send the JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
