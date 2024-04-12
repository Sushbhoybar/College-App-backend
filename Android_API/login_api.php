<?php
include "config.php";

$useremail = $_POST["email_id"];
$email_password = md5($_POST["password"]);

$checkuser = "SELECT * FROM student_data WHERE email_id ='$useremail'";
$checkresult = mysqli_query($con, $checkuser);

if (mysqli_num_rows($checkresult) > 0) {
    $row = mysqli_fetch_assoc($checkresult);
    $hashed_password = $row['password'];
    
    if ($email_password === $hashed_password) {
        $output["Status"] = "200";
        $output["message"] = "Login Successfully";
        $output["USER"] = $row;
    } else {
        $output["Status"] = "100";
        $output["message"] = "Invalid password";
        //$output["USER"] = (object)[];
    }
} else {
    $output["Status"] = "500";
    $output["message"] = "Account not found";
   // $output["USER"] = (object)[];
}

header("Content-type: application/json");
echo json_encode($output);
?>
