<?php
// Establishing connection to MySQL
include "config.php"; // Include file containing database connection details

// Get registration number, academic year, and subject from POST request body
$reg_no = $_POST['reg_no']; // Assuming reg_no is sent in the POST request
$academic_year = $_POST['academic_year']; // Assuming academic_year is sent in the POST request
$subject = $_POST['subject']; // Assuming subject is sent in the POST request

$branch = $_POST['branch'];
$year = $_POST['year'];
$division = $_POST['division'];


// Initialize variables
$full_name = null;
$attendance_percentage = null;

// Prepare SQL statement to retrieve full name
$name_sql = "SELECT full_name FROM student_data WHERE reg_id = ?";
$name_stmt = $con->prepare($name_sql);
if (!$name_stmt) {
    die("Error preparing name statement: " . $con->error);
}
$name_stmt->bind_param("s", $reg_no);
$name_stmt->execute();
$name_stmt->bind_result($full_name);
$name_stmt->fetch();
$name_stmt->close();


// Prepare SQL statement
$sql = "SELECT 
               SUM(CASE WHEN attendance_status = 'P' THEN 1 ELSE 0 END) AS present_count,
               COUNT(*) AS total_count,
               (SUM(CASE WHEN attendance_status = 'P' THEN 1 ELSE 0 END) / COUNT(*)) * 100 AS percentage
        FROM attendance_table
        WHERE reg_no = ? AND academic_year = ? AND subject = ? AND attendance_status != 'X'";

// Prepare and bind parameters
$stmt = $con->prepare($sql);
if (!$stmt) {
    die("Error preparing statement: " . $con->error);
}
$stmt->bind_param("sss", $reg_no, $academic_year, $subject);

// Execute the statement
$result = $stmt->execute();
if (!$result) {
    die("Error executing statement: " . $stmt->error);
}

// Bind result variables
$stmt->bind_result($present_count, $total_count, $percentage);

// Fetch result
$stmt->fetch();

// Close statement
$stmt->close();

// Close connection
$con->close();

if ($percentage === null) {
    $percentage = 0;
}

// Return the percentage as JSON response
echo json_encode(array(
    "full_name" => $full_name,
    "percentage" => $percentage
));
?>
