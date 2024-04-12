<?php
// Establishing connection to MySQL
include "config.php"; // Include file containing database connection details

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Parameters
$start_date_time = $_POST['start_date_time']; // Assuming you're getting these values from a form
$end_date_time = $_POST['end_date_time'];
$academic_year = $_POST['academic_year'];
$branch = $_POST['branch'];
$year = $_POST['year'];

// SQL query to calculate attendance percentage and retrieve roll numbers with less than 75% attendance
$sql = "SELECT division, subject, roll_no, reg_no,
        SUM(CASE WHEN attendance_status = 'P' THEN 1 ELSE 0 END) AS present_count,
        COUNT(*) AS total_count,
        (SUM(CASE WHEN attendance_status = 'P' THEN 1 ELSE 0 END) / COUNT(*)) * 100 AS attendance_percentage
        FROM attendance_table
        WHERE academic_year = '$academic_year' AND attendance_status != 'X'
        AND branch = '$branch'
        AND year = '$year'
        AND date_time BETWEEN '$start_date_time' AND '$end_date_time'
        GROUP BY division, subject, roll_no, reg_no
        HAVING attendance_percentage < 75";

$result = $con->query($sql);

// Initialize an array to store the result
$attendance_data = array();

// Process the result
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $division = $row['division'];
        $subject = $row['subject'];
        $roll_no = $row['roll_no'];
        $reg_no = $row['reg_no'];
        $attendance_percentage = $row['attendance_percentage'];

        // Add the roll number and registration number along with percentage to the appropriate division and subject
        $attendance_data[$division][$subject][] = $roll_no;
    }
}

// Output the result in JSON format
header('Content-Type: application/json');
echo json_encode($attendance_data);

$con->close();
?>
