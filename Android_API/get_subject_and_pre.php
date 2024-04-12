<?php
// Establishing connection to MySQL
include "config.php"; // Include file containing database connection details

// Get registration number and academic year from POST request body
$reg_no = $_POST['reg_no']; // Assuming reg_no is sent in the POST request
$academic_year = $_POST['academic_year']; // Assuming academic_year is sent in the POST request

// Prepare SQL statement
$sql = "SELECT subject, 
               SUM(CASE WHEN attendance_status = 'P' THEN 1 ELSE 0 END) AS present_count,
               COUNT(*) AS total_count,
               (SUM(CASE WHEN attendance_status = 'P' THEN 1 ELSE 0 END) / COUNT(*)) * 100 AS percentage
        FROM attendance_table
        WHERE reg_no = ? AND academic_year = ? AND attendance_status != 'X'
        GROUP BY subject";

// Prepare and bind parameters
$stmt = $con->prepare($sql);
if (!$stmt) {
    die("Error preparing statement: " . $con->error);
}
$stmt->bind_param("ss", $reg_no, $academic_year);

// Execute the statement
$result = $stmt->execute();
if (!$result) {
    die("Error executing statement: " . $stmt->error);
}

// Bind result variables
$stmt->bind_result($subject, $present_count, $total_count, $percentage);

// Fetch results and store them in an array
$subject_percentages = array();
while ($stmt->fetch()) {
    $subject_percentages[] = array(
        "subject" => $subject,
        "percentage" => $percentage
    );
}

// Close statement
$stmt->close();

// Close connection
$con->close();

// Return the subject percentages as JSON response
echo json_encode($subject_percentages);
?>
