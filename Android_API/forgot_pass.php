<?php
include "config.php";
$output = array(); // Initialize $output

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

function generateOTP() {
    return rand(100000, 999999);
}

$useremail = mysqli_real_escape_string($con, $_POST["email_id"]);
$sender_email = "2021bcs073@sggs.ac.in"; // Fixed sender's email

$checkuser = "SELECT * FROM student_data WHERE email_id ='$useremail'";
$checkresult = mysqli_query($con, $checkuser);

if (mysqli_num_rows($checkresult) > 0) {
    $row = mysqli_fetch_assoc($checkresult);

    // Invalidate old OTP
    $update_otp_query = "UPDATE student_data SET otp = NULL WHERE email_id = '$useremail'";
    mysqli_query($con, $update_otp_query);

    // Generate and save a new OTP
    $otp = generateOTP();
    $current_time = time();
    $expiration_time = $current_time + (60 * 5); // 5 minutes expiration time

    $update_otp_query = "UPDATE student_data SET otp = '$otp', otp_expiration = FROM_UNIXTIME($expiration_time) WHERE email_id = '$useremail'";
    $update_otp_result = mysqli_query($con, $update_otp_query);

    if (!$update_otp_result) {
        $output["Error"] = "300";
        $output["Message"] = "Failed to generate OTP";
        header("Content-type: application/json");
        echo json_encode($output);
        exit;
    }

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                       //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = '2021bcs073@sggs.ac.in';                     //SMTP username
        $mail->Password   = 'gkplyniankrgcbuy';                          //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    
        //Recipients
        $mail->setFrom('2021bcs073@sggs.ac.in', 'Mailer');
       // $mail->addAddress('', '');     //Add a recipient
        $mail->addAddress($useremail);               //Name is optional
        $mail->addReplyTo('2021bcs073@sggs.ac.in', 'Information');
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');
    
        //Attachments
       // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
       // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
    
         //Content
         $mail->isHTML(true);                                  // Set email format to HTML
         $mail->Subject = 'OTP for verification';
         $mail->Body    = 'Hello, <br><br>Your OTP for verification is: <b>' . $otp . '</b>';
         $mail->AltBody = 'Your OTP for verification is: ' . $otp; // Plain text version for non-HTML mail clients
     
        $res = $mail->send();
        if($res)
            echo $otp;
        else
            echo 'Message has not been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    $output["Error"] = "500";
    $output["Message"] = "Account not found";
}

header("Content-type: application/json");
echo json_encode($output);
?>
