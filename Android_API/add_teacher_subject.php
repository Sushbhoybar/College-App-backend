<?php
// Establishing connection to MySQL
include "config.php"; // Include file containing database connection details

// Get data from POST request body
$teacher_email = $_POST['teacher_email'];
$academic_year = $_POST['academic_year'];
$branch = $_POST['branch'];
$year = $_POST['year'];
$division = $_POST['division'];
$subject = $_POST['subject'];

// Check if the record already exists
$sql_check = "SELECT * FROM teacher_subject_list WHERE teacher_email = ? AND academic_year = ? AND branch = ? AND year = ? AND division = ? AND subject = ?";
$stmt_check = $con->prepare($sql_check);
if (!$stmt_check) {
    die("Error preparing check statement: " . $con->error);
}
$stmt_check->bind_param("ssssss", $teacher_email, $academic_year, $branch, $year, $division, $subject);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    // Record already exists, do not insert
    echo "Record already exists";
} else {
    // Prepare SQL statement for insertion
    $sql_insert = "INSERT INTO teacher_subject_list (teacher_email, academic_year, branch, year, division, subject) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_insert = $con->prepare($sql_insert);
    if (!$stmt_insert) {
        die("Error preparing insert statement: " . $con->error);
    }
    $stmt_insert->bind_param("ssssss", $teacher_email, $academic_year, $branch, $year, $division, $subject);
    $result_insert = $stmt_insert->execute();
    if (!$result_insert) {
        die("Error executing insert statement: " . $stmt_insert->error);
    } else {
        echo "Data inserted successfully";
    }
    // Close insert statement
    $stmt_insert->close();
}

// Close check statement
$stmt_check->close();

// Close connection
$con->close();
?>
