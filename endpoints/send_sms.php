<?php
include "../config.php";

$phonenumber = isset($_POST["phonenumber"]) ? $_POST["phonenumber"] : '';
$message = isset($_POST["message"]) ? $_POST["message"] : '';

$smsObject = json_encode(array(
    "content"=>$message,
    "to"=>array(
        $phonenumber
    )
    ));
    $options = [
        \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES   => false,
    ];
  $conn = new \PDO($dsn, $user, $pass, $options);

  sendSms($conn, $smsObject, $baseurl, $apiKey);


function sendSms($conn, $smsObject, $baseurl, $apiKey) {

        $sms = curl_init();

        curl_setopt_array($sms, array(
            CURLOPT_URL => $baseurl."messages",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $smsObject,
            CURLOPT_FRESH_CONNECT=> TRUE,
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Content-Type: application/json",
                "Authorization: $apiKey",
               
            ),
        ));
        $response = json_decode(curl_exec($sms));

        foreach($response->messages as $message){
            logMessage($conn, $message->apiMessageId, $smsObject->content, $message->to);
        }

        curl_close($sms);
        echo json_encode(array("success"=>"true", "message"=>"Sms Sent Successfully"));

}

function logMessage($conn, $messageId, $message, $sentTo){
    try {
        $sql = "INSERT INTO `sms_messages`(`messageId`, `message`, `sentTo`)
        VALUES (?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $messageId);
        $stmt->bindParam(2, $message);
        $stmt->bindParam(3, $sentTo);
        $stmt->execute();

    } catch (Exception $e) {
        var_dump($e);
    }
    
}
