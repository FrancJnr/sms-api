<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
include "../config.php";


$options = [
    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    \PDO::ATTR_EMULATE_PREPARES   => false,
];
$conn = new \PDO($dsn, $user, $pass, $options);


$messageId =  isset($_GET["messageId"]) ? $_GET["messageId"] : '';
$statusCode =  isset($_GET["statusCode"]) ? $_GET["statusCode"] : '';
$status =  isset($_GET["status"]) ? $_GET["status"] : '';
$statusDescription =  isset($_GET["statusDescription"]) ? $_GET["statusDescription"] : '';
$timestamp = isset($_GET["timestamp"]) ? $_GET["timestamp"] : '';


try {
    $sql = "INSERT INTO `sms_call_back`(`messageId`, `statusCode`, `status`, `statusDescription`, `timestamp`)
    VALUES(?,?,?,?,current_timestamp)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $messageId);
    $stmt->bindParam(2, $statusCode);
    $stmt->bindParam(3, $status);
    $stmt->bindParam(4, $statusDescription);
    $stmt->execute();
    echo json_encode(array("success"=>"true"));

} catch (\PDOException $e) {
    var_dump($e);
}








