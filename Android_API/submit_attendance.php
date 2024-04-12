<?php
// Establishing connection to MySQL
include "config.php"; // Include file containing database connection details

// Get JSON data from POST request body
$requestBody = json_decode(file_get_contents('php://input'), true);

// Extract fields
$academic_year = $requestBody['academic_year'];
$subject = $requestBody['subject'];
$branch = $requestBody['branch'];
$date_time = $requestBody['date_time'];
$division = $requestBody['division'];
$teacher_name = $requestBody['teacher_name'];
$year = $requestBody['year']; // Added field

// Prepare SQL statement
$sql = "INSERT INTO attendance_table (academic_year, subject, branch, division, year, roll_no, reg_no, date_time, teacher_name, attendance_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

// Prepare and bind parameters
$stmt = $con->prepare($sql);
if (!$stmt) {
    die("Error preparing statement: " . $con->error);
}
$stmt->bind_param("ssssssssss", $academic_year, $subject, $branch, $division, $year, $roll_no, $reg_no, $date_time, $teacher_name, $attendance_status);

// Inserting data for each row
foreach ($requestBody['attendance_data'] as $row) {
    // Assigning values for each row
    $roll_no = $row['roll_no'];
    $reg_no = $row['reg_no'];
    $attendance_status = $row['attendance_status'];

    // Execute the statement
    $result = $stmt->execute();
    if (!$result) {
        die("Error executing statement: " . $stmt->error);
    }
}

echo "Data inserted successfully";

// Close statement and connection
$stmt->close();
$con->close();
?>
