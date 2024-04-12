<?php
// Establishing connection to MySQL
include "config.php"; // Include file containing database connection details

// Get registration number and subject name from POST request body
$reg_no = $_POST['reg_no']; // Assuming reg_no is sent in the POST request
$subject = $_POST['subject']; // Assuming subject is sent in the POST request

// Prepare SQL statement
$sql = "SELECT date_time, attendance_status
        FROM attendance_table
        WHERE reg_no = ? AND subject = ?";

// Prepare and bind parameters
$stmt = $con->prepare($sql);
if (!$stmt) {
    die("Error preparing statement: " . $con->error);
}
$stmt->bind_param("ss", $reg_no, $subject);

// Execute the statement
$result = $stmt->execute();
if (!$result) {
    die("Error executing statement: " . $stmt->error);
}

// Bind result variables
$stmt->bind_result($date_time, $attendance_status);

// Fetch results and store them in an array
$attendance_data = array();
while ($stmt->fetch()) {
    $attendance_data[] = array(
        "date_time" => $date_time,
        "attendance_status" => $attendance_status
    );
}

// Close statement
$stmt->close();

// Close connection
$con->close();

// Return the attendance data as JSON response
echo json_encode($attendance_data);
?>
