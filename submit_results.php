<?php
global $conn;
include('DBconnection.php');
// Prepare and bind
$stmt = $conn->prepare("INSERT INTO marks (student_id, student_name, questionnaire_id, correct_count, time_taken) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssiii", $student_id, $student_name, $questionnaire_id, $correct_count, $time_taken);

// Set parameters and execute
$student_id = $_POST['student_id'];
$student_name = $_POST['student_name'];
$questionnaire_id = $_POST['questionnaire_id'];
$correct_count = $_POST['correct_count'];
$time_taken = $_POST['time_taken'];

if ($stmt->execute()) {
    echo "Results successfully submitted.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
