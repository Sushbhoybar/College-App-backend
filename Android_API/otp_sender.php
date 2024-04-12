<?php
include "config.php";
$output = array(); // Initialize $output
    
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

$useremail = isset($_POST["email_id"]) ? mysqli_real_escape_string($con, $_POST["email_id"]) : '';

// Check if email is provided in the POST request
if(empty($useremail)) {
    $output["Error"] = "400";
    $output["Message"] = "Email not provided";
    header("Content-type: application/json");
    echo json_encode($output);
    exit;
}

// Instantiation and passing true enables exceptions
$mail = new PHPMailer(true);

function generateOTP() {
    return rand(100000, 999999);
}

// Generate OTP
$otp = generateOTP();

try {
    // Server settings
    $mail->SMTPDebug = 0;                                       // Enable verbose debug output
    $mail->isSMTP();                                            // Set mailer to use SMTP
    $mail->Host       = 'mail.sggsapp.co.in';                   // Specify main and backup SMTP servers
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'verify@sggsapp.co.in';                 // SMTP username
    $mail->Password   = 'projectsggsapp@123';                   // SMTP password
    $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, ssl also accepted
    $mail->Port       = 587;                                    // TCP port to connect to
 
    //Recipients
    $mail->setFrom('verify@sggsapp.co.in', 'SGGS App');
    $mail->addAddress($useremail, 'SGGS APP User');             // Add a recipient
    $mail->addReplyTo('verify@sggsapp.co.in', 'Information');
 
    // Content
    $mail->isHTML(false);                                       // Set email format to HTML
    $mail->Subject = 'SGGS APP Verification OTP';               // Email subject
    $mail->Body    = "Dear SGGS APP user,\n\nYour SGGS APP Account One Time PIN is: $otp, and is valid for 5 minutes.\n\nThis is an auto-generated email. Do not reply to this email.\n\nThis is an auto-generated email. Do not reply to this email.";
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
 
    // Send the email
    $mail->send();
    
    $output['Error'] = "200";
    $output["Message"] = "Mail sent";
    $output["OTP"] = "$otp"; // Include OTP in the output

} catch (Exception $e) {
    $output['Error'] = "100";
    $output["Message"] = "Message could not be sent. Mailer Error: " . $mail->ErrorInfo;
}

header("Content-type: application/json");
echo json_encode($output);
?>
