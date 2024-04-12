<?php
// Establishing connection to MySQL
include "config.php"; // Include file containing database connection details

// Get registration number, academic year, datetime start, and datetime end from POST request body
$reg_no = $_POST['reg_no']; // Assuming reg_no is sent in the POST request
$academic_year = $_POST['academic_year']; // Assuming academic_year is sent in the POST request
$datetime_start = $_POST['datetime_start']; // Assuming datetime_start is sent in the POST request
$datetime_end = $_POST['datetime_end']; // Assuming datetime_end is sent in the POST request

// Prepare SQL statement
$sql = "SELECT subject, 
               SUM(CASE WHEN attendance_status = 'P' THEN 1 ELSE 0 END) AS present_count,
               COUNT(*) AS total_count,
               (SUM(CASE WHEN attendance_status = 'P' THEN 1 ELSE 0 END) / COUNT(*)) * 100 AS percentage,
               SUM(CASE WHEN date_time BETWEEN ? AND ? THEN 1 ELSE 0 END) AS count_within_range,
               SUM(CASE WHEN date_time BETWEEN ? AND ? AND attendance_status = 'P' THEN 1 ELSE 0 END) AS count_p_within_range
        FROM attendance_table
        WHERE reg_no = ? AND academic_year = ? AND attendance_status != 'X' 
        GROUP BY subject";

// Prepare and bind parameters
$stmt = $con->prepare($sql);
if (!$stmt) {
    die("Error preparing statement: " . $con->error);
}
$stmt->bind_param("ssssss", $datetime_start, $datetime_end, $datetime_start, $datetime_end, $reg_no, $academic_year);

// Execute the statement
$result = $stmt->execute();
if (!$result) {
    die("Error executing statement: " . $stmt->error);
}

// Bind result variables
$stmt->bind_result($subject, $present_count, $total_count, $percentage, $count_within_range, $count_p_within_range);

// Fetch results and store them in an array
$subject_percentages = array();
while ($stmt->fetch()) {
    $percentage_p_within_range = $count_within_range > 0 ? (100/$count_within_range*$count_p_within_range) : 0;
    $formatted_percentage_p_within_range = number_format($percentage_p_within_range, 2);
    
    $subject_percentages[] = array(
        "subject" => $subject,
        "present_count" => $present_count,
        "total_count" => $total_count,
        "percentage" => number_format($percentage, 2),
        "count_within_range" => $count_within_range,
        "count_p_within_range" => $count_p_within_range,
        "percentage_p_within_range" => $formatted_percentage_p_within_range
    );
}

// Close statement
$stmt->close();

// Close connection
$con->close();

// Return the subject percentages as JSON response
echo json_encode($subject_percentages);
?>
