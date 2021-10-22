<?php
include "config.php";
$options = [
    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    \PDO::ATTR_EMULATE_PREPARES   => false,
];
$conn = new \PDO($dsn, $user, $pass, $options);
$messages = array();
try {
    $sql = "SELECT message, sms_messages.messageId, sentTo, sentOn, statusCode, status, statusDescription, sms_call_back.timestamp
    FROM sms_messages
    LEFT JOIN sms_call_back ON sms_call_back.messageId = sms_messages.messageId";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $messages = $stmt->fetchAll();


    $countSuccess = "SELECT COUNT(sms_messages.id) AS totalSent FROM sms_messages 
    LEFT JOIN sms_call_back ON sms_call_back.messageId = sms_messages.messageId
    WHERE statusCode = 4";

    $stmt2 = $conn->prepare($countSuccess);
    $stmt2->execute();
    $rowCount1 = $stmt2->fetch();

    $countFailed = "SELECT COUNT(sms_messages.id) AS totalFailed FROM sms_messages 
    LEFT JOIN sms_call_back ON sms_call_back.messageId = sms_messages.messageId
    WHERE statusCode = 23";

    $stmt2 = $conn->prepare($countSuccess);
    $stmt2->execute();
    $rowCount1 = $stmt2->fetch();
    
} catch (\PDOException $e) {
    var_dump($e);
}
?>




<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Assignment</title>
    <meta name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <meta name="description" content="This is an example dashboard created using build-in elements and components.">
    <meta name="msapplication-tap-highlight" content="no">

    <link rel="stylesheet" href="css/boostrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/dataTable.css">


</head>

<body>
    <div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
        <div class="app-header header-shadow">
            <div class="app-header__logo">
                <div>
                    <img style="width: 80px !important; height:58px !important;" src="logo.png"
                        alt="logo">
                </div>
            </div>
            <div class="app-header__mobile-menu">
                <div>
                    <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                        <span class="hamburger-box">
                            <span class="hamburger-inner"></span>
                        </span>
                    </button>
                </div>
            </div>

            <div class="app-header__content">
                <div class="app-header-left">
                    <ul class="header-menu nav">
                        <li class="nav-item">
                            <a id="sendSms" class="nav-link">
                                <i class="nav-link-icon bi bi-stack text-primary fw-bold"></i>
                                Send Sms
                            </a>
                        </li>
                       
                    </ul>
                </div>
                <div class="app-header-right">
                    <div class="header-btn-lg pr-0">
                        <div class="widget-content p-0">
                            <div class="widget-content-wrapper">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card p-2">
                        <div class="app-main__outer">
                            <div class="app-main__inner">
                                <div class="app-page-title">
                                    <div class="page-title-wrapper">
                                        <div class="page-title-heading">
                                            <div class="page-title-icon">
                                                <i class="pe-7s-car icon-gradient bg-mean-fruit">
                                                </i>
                                            </div>
                                            <div>Dashboard
                                                <div class="page-title-subheading">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-xl-4">
                                        <div class="card mb-3 widget-content bg-midnight-bloom">
                                            <div class="widget-content-wrapper text-white">
                                                <div class="widget-content-left">
                                                    <div class="widget-heading">SMS Sent</div>
                                                    <div class="widget-subheading"></div>
                                                </div>
                                                <div class="widget-content-right">
                                                    <div class="widget-numbers text-white"><span
                                                            id="totalIssued">
                                                            <?= $rowCount1["totalSent"] ?>
                                                        </span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="card mb-3 widget-content bg-arielle-smile">
                                            <div class="widget-content-wrapper text-white">
                                                <div class="widget-content-left">
                                                    <div class="widget-heading">Failed SMS</div>
                                                    <div class="widget-subheading">Registered</div>
                                                </div>
                                                <div class="widget-content-right">
                                                    <div class="widget-numbers text-white"><span
                                                            id="totalClients">0</span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card" style="height:70vH;">
                                            <div class="card-header bg-success">
                                                <span>SMS Logs</span>
                                            </div>
                                            <div class="table-responsive">
                                                <div id="smsMesages" class="card-body table-wrapper-scroll-y my-custom-scrollbar">
                                                    <table class="table">
                                                        <tr>
                                                            <th>id</th>
                                                            <th>MessageID</th>
                                                            <th>Message</th>
                                                            <th>Sent To</th>
                                                            <th>statusCode</th>
                                                            <th>status</th>
                                                            <th>statusDescription</th>
                                                            <th>timestamp</th>
                                                        </tr>
                                                        <?php
                                                            $id = 1;
                                                            foreach($messages as $message){
                                                               echo "<tr>";
                                                                $id ++;
                                                               echo "<td>".$id."</td>";
                                                               echo "<td>".$message['messageId']."</td>";
                                                               echo "<td>".$message['message']."</td>";
                                                               echo "<td>".$message['sentTo']."</td>";
                                                               echo "<td>".$message['statusCode']."</td>";
                                                               echo "<td>".$message['status']."</td>";
                                                               echo "<td>".$message['statusDescription']."</td>";
                                                               echo "<td>".$message['timestamp']."</td>";

                                                               echo "</tr>";

                                                            }

                                                        ?>

                                                    </table>
                                                   
                                                </div>

                                            </div>
                                            <div class="card-footer">

                                            </div>
                                        </div>
                                    </div>
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- The Modal -->
        <div class="modal fade" id="smsModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Create SMS</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form id="closingForm">
                            <div id="msg" class="bg-success text-white"></div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="value">Phone Number</label>
                                        <input type="text" class="form-control" name="phonenumber" value="" id="phonenumber" value="" required="">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="value">Message</label>
                                        <textarea class="form-control" id="message" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        
                        </form>

                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <div class="form-group float-right">
                            <button type="submit" class="btn btn-primary btn-sm" id="post">Send</button>
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
   
        <script type="text/javascript" src="js/main.js"></script>
        <script type="text/javascript" src="js/jquery.min.js"></script>
        <script type="text/javascript" src="js/boostrap.js"></script>
        <script type="text/javascript" src="js/popper.min.js"></script>
        <script type="text/javascript" src="js/dataTables.min.js"></script>
</body>

</html>


<script>


$("#sendSms").click(function(e) {
    $("#smsModal").modal('show');
});

$("#post").click(function(e) {
  
    var phonenumber = $("#phonenumber").val();
    var message = $("#message").val();
    $.ajax({
        url: "endpoints/send_sms.php",
        type: "POST",
        dataType: "json",
        data: {
            phonenumber: phonenumber,
            message: message
        },
        success: function(response) {
            if (response.success == true) {
                $("#msg").html(response.message);
                setTimeout(function() {
                    $('#msg').fadeOut('slow');
                    $("#smsModal").modal('hide');

                }, 2000);
                location.reload();


            } else {
                $("#msg").removeClass("bg-success");
                $("#msg").addClass("bg-danger");
                $("#msg").html(response.message);

                setTimeout(function() {
                    $('#msg').fadeOut('slow');
                    $(this).removeData('modal');
                    $("#smsModal").modal('hide');
                    location.reload();
                }, 3000);
                

            }


        }
    });

});







</script>