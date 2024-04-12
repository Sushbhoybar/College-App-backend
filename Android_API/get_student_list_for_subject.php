<?php
// Establishing connection to MySQL
include "config.php"; // Include file containing database connection details

// Get parameters
$academic_year = $_POST['academic_year'];
$subject = $_POST['subject'];
$branch = $_POST['branch'];
$division = $_POST['division'];
$year = $_POST['year']; // Add year parameter
$attendance_status = $_POST['attendance_status']; // Add attendance_status parameter

// Prepare SQL statement
$sql = "SELECT roll_no, reg_no, attendance_status
        FROM attendance_table
        WHERE academic_year = ? AND subject = ? AND branch = ? AND division = ? AND year = ? AND attendance_status = ?";
$stmt = $con->prepare($sql);
if (!$stmt) {
    die("Error preparing statement: " . $con->error);
}

// Bind parameters
$stmt->bind_param("ssssss", $academic_year, $subject, $branch, $division, $year, $attendance_status);

// Execute the statement
$result = $stmt->execute();
if (!$result) {
    die("Error executing statement: " . $stmt->error);
}

// Bind result variables
$stmt->bind_result($rollNo, $regNo, $attendanceStatus);

// Fetch and output the rows
$output = array();
$uniqueRegs = array(); // Array to store unique registration numbers
while ($stmt->fetch()) {
    // Check if the registration number has already been added and the attendance status matches
    if (!in_array($regNo, $uniqueRegs) && $attendanceStatus == $attendance_status) {
        $output[] = array(
            "roll_no" => $rollNo,
            "reg_no" => $regNo,
            "attendance_status" => "P"
        );
        // Add the registration number to the array of unique registration numbers
        $uniqueRegs[] = $regNo;
    }
}

// Close statement and connection
$stmt->close();
$con->close();

// Output the result as JSON
echo json_encode($output);
?>
