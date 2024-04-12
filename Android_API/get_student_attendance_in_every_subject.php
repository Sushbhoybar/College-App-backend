<?php
// Establishing connection to MySQL
include "config.php"; // Include file containing database connection details

// Assuming the user inputs are stored in variables
$division = $_POST['division']; // Example of getting division from POST data, adjust as needed
$academic_year = $_POST['academic_year']; // Example of getting academic year from POST data, adjust as needed
$branch = $_POST['branch']; // Example of getting branch from POST data, adjust as needed
$year = $_POST['year']; // Example of getting year from POST data, adjust as needed
$datetime_start = $_POST['datetime_start']; // Example of getting datetime_start from POST data, adjust as needed
$datetime_end = $_POST['datetime_end']; // Example of getting datetime_end from POST data, adjust as needed

// Prepare SQL statement to fetch all unique reg_no for the specified filters
$sql_reg_no = "SELECT DISTINCT reg_no FROM attendance_table 
               WHERE division = ? AND academic_year = ? AND branch = ? AND year = ? 
               AND date_time BETWEEN ? AND ?";
$stmt_reg_no = $con->prepare($sql_reg_no);
if (!$stmt_reg_no) {
    die("Error preparing statement: " . $con->error);
}
$stmt_reg_no->bind_param("ssssss", $division, $academic_year, $branch, $year, $datetime_start, $datetime_end);
$result_reg_no = $stmt_reg_no->execute();
if (!$result_reg_no) {
    die("Error executing statement: " . $stmt_reg_no->error);
}
$result_reg_no = $stmt_reg_no->get_result();

// Array to store the final output for students in the specified filters
$all_students_attendance = array();

// Loop through each reg_no to fetch attendance data
while ($row_reg_no = $result_reg_no->fetch_assoc()) {
    $reg_no = $row_reg_no['reg_no'];

    // Prepare SQL statement to fetch attendance data for the current reg_no and filters
    $sql = "SELECT roll_no, subject, 
                   SUM(CASE WHEN attendance_status = 'P' THEN 1 ELSE 0 END) AS present_count,
                   COUNT(*) AS total_count,
                   (SUM(CASE WHEN attendance_status = 'P' THEN 1 ELSE 0 END) / COUNT(*)) * 100 AS percentage,
                   SUM(CASE WHEN attendance_status = 'P' THEN 1 ELSE 0 END) AS count_p_within_range
            FROM attendance_table
            WHERE reg_no = ? AND division = ? AND academic_year = ? AND branch = ? AND year = ?
            AND date_time BETWEEN ? AND ? AND attendance_status != 'X' 
            GROUP BY subject";

    // Prepare and bind parameters
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement: " . $con->error);
    }
    $stmt->bind_param("sssssss", $reg_no, $division, $academic_year, $branch, $year, $datetime_start, $datetime_end);

    // Execute the statement
    $result = $stmt->execute();
    if (!$result) {
        die("Error executing statement: " . $stmt->error);
    }

    // Bind result variables
    $stmt->bind_result($roll_no, $subject, $present_count, $total_count, $percentage, $count_p_within_range);

    // Fetch results and store them in an array for the current student
    $student_attendance = array();
    while ($stmt->fetch()) {
        $percentage_p_within_range = $total_count > 0 ? (100/$total_count*$count_p_within_range) : 0;
        $formatted_percentage_p_within_range = number_format($percentage_p_within_range, 2);

        $student_attendance[] = array(
            "subject" => $subject,
            "present_count" => $present_count,
            "total_count" => $total_count,
            "percentage" => number_format($percentage, 2),
            "count_within_range" => $present_count,
            "count_p_within_range" => $count_p_within_range,
            "percentage_p_within_range" => $formatted_percentage_p_within_range
        );
    }

    // Close statement
    $stmt->close();

    // Add the student's attendance data to the final output array
    $all_students_attendance[] = array(
        "roll_no" => $roll_no, // Assuming you have the roll_no in the database
        "reg_no" => $reg_no,
        "attendance_data" => $student_attendance
    );
}

// Close connection
$con->close();

// Return the final output as JSON response
echo json_encode($all_students_attendance);
?>
