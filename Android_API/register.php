<?php
header('Content-Type: application/json');
include "config.php";

$useremail = $_POST["email_id"];
$username = $_POST['full_name'];
$mobileno = $_POST['mobile_no'];
$registration_id = $_POST["reg_id"];
$password = $_POST["password"];
$image = $_POST["image"]; // assuming image data is passed in the request
$email_password = md5($password);

// Check if the password meets certain criteria (e.g., minimum length)
if(strlen($password) < 8) {
    $output['Status'] = "300";
    $output["Message"] = "Invalid password. Password should be at least 8 characters long.";
    echo json_encode($output);
    exit; // Stop further execution
}

$email_prefix_array = explode('@', $useremail);
$email_prefix = $email_prefix_array[0]; // Extract the prefix before '@'
$reg_id_lowercase = strtolower($registration_id); // Convert reg_id to lowercase
$email_prefix_lowercase = strtolower($email_prefix); // Convert email prefix to lowercase

// Compare the lowercase versions of email prefix and reg_id
if ($email_prefix_lowercase !== $reg_id_lowercase) {
    $output['Status'] = "700";
    $output["Message"] = "Email prefix does not match reg_id";
    echo json_encode($output);
    exit; // Stop further execution
}

$checkuser = "SELECT * FROM student_data WHERE mobile_no = $mobileno OR email_id = '$useremail'";
$checkresult = mysqli_query($con, $checkuser);

if(mysqli_num_rows($checkresult) > 0) {
    $output['Status'] = "400";
    $output["Message"] = "You are already registered";
} else {
    $insert = "INSERT INTO student_data(email_id, full_name, mobile_no, reg_id, password, image) VALUES('$useremail','$username',$mobileno,'$registration_id','$email_password', '$image')";
    $result = mysqli_query($con, $insert);
    
    if($result) {
        $output['Status'] = "200";
        $output["Message"] = "Registered Sucessfully";
    
    } else {
        $output['Status'] = "100";
        $output["Message"] = "Failed";
    }
}  

header('Content-Type: application/json');
echo json_encode($output);

?>
