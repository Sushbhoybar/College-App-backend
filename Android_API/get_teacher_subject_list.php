<?php
// Establishing connection to MySQL
include "config.php"; // Include file containing database connection details

// Get data from POST request body
$teacher_email = $_POST['teacher_email'];
$academic_year = $_POST['academic_year'];

// Prepare SQL statement
$sql = "SELECT teacher_email, academic_year, branch, year, division, subject 
        FROM teacher_subject_list 
        WHERE teacher_email = ? AND academic_year = ?";

// Prepare and bind parameters
$stmt = $con->prepare($sql);
if (!$stmt) {
    die("Error preparing statement: " . $con->error);
}
$stmt->bind_param("ss", $teacher_email, $academic_year);

// Execute the statement
$result = $stmt->execute();
if (!$result) {
    die("Error executing statement: " . $stmt->error);
}

// Bind result variables
$stmt->bind_result($teacher_email, $academic_year, $branch, $year, $division, $subject);

// Fetch results and store them in an array
$teacher_subject_list = array();
while ($stmt->fetch()) {
    $teacher_subject_list[] = array(
        "teacher_email" => $teacher_email,
        "academic_year" => $academic_year,
        "branch" => $branch,
        "year" => $year,
        "division" => $division,
        "subject" => $subject
    );
}

// Close statement
$stmt->close();

// Close connection
$con->close();

// Return the teacher's subject list as JSON response
echo json_encode($teacher_subject_list);
?>
