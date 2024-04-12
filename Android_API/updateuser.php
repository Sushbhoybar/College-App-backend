<?php
include "config.php";


$useremail = $_POST["email_id"];
$username = $_POST['full_name'];
$mobileno = $_POST['mobile_no'];
$registration_id = $_POST["reg_id"];

$update = "UPDATE student_data SET full_name='$username', mobile_no=$mobileno, reg_id='$registration_id' WHERE email_id= '$useremail'";

$checkresult= mysqli_query($con, $update);
if($checkresult) {
    $output["Message"] = "Data Updated";
}else{
    $output["Message"] = "Failed";
}
echo json_encode($output);
?>
