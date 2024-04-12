<?php
header('Content-Type: application/json');
include "config.php";

$useremail = $_POST["email_id"];
$username = $_POST['full_name'];
$mobileno = $_POST['mobile_no'];
$password = $_POST["password"];
$img = $_POST["img"];

$email_password = md5($password);

// Validate email format
if (!filter_var($useremail, FILTER_VALIDATE_EMAIL)) {
    $output['Error'] = "500";
    $output["Message"] = "Invalid email format";
    echo json_encode($output);
    exit; // Stop further execution
}

// Check if the email ends with "@sggs.ac.in"
if (!preg_match("/@sggs\.ac\.in$/", $useremail)) {
    $output['Error'] = "201";
    $output["Message"] = "Email must end with @sggs.ac.in";
    echo json_encode($output);
    exit; // Stop further execution
}

// Check if the password meets certain criteria (e.g., minimum length)
if(strlen($password) < 8) {
    $output['Error'] = "300";
    $output["Message"] = "Invalid password. Password should be at least 8 characters long.";
    echo json_encode($output);
    exit; // Stop further execution
}

$checkuser = "SELECT * FROM teacher_data WHERE mobile_no = $mobileno OR email_id = '$useremail'";
$checkresult = mysqli_query($con, $checkuser);

if(mysqli_num_rows($checkresult) > 0) {
    $output['Error'] = "400";
    $output["Message"] = "You are already registered";
} else {
    $insert = "INSERT INTO teacher_data(email_id, full_name, mobile_no, password, img) VALUES('$useremail','$username',$mobileno,'$email_password', '$img')";
    $result = mysqli_query($con, $insert);
    
    if($result) {
        $output['Error'] = "200";
        $output["Message"] = "Success";
    } else {
        $output['Error'] = "100";
        $output["Message"] = "Failed";
    }
}

echo json_encode($output);
?>
