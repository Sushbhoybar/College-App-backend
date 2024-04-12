<?php
include "config.php";


$useremail = $_POST["email_id"];

$checkuser = "SELECT * FROM student_data WHERE email_id='$useremail'";
$result = mysqli_query($con,$checkuser);

if(mysqli_num_rows($result) >0){
$update = "DELETE FROM student_data WHERE email_id= '$useremail'";

$checkresult= mysqli_query($con, $update); 
    $output["Message"] = "Data Deleted";
}else{
    $output["Message"] = "Not Found";
}
echo json_encode($output);
?>
