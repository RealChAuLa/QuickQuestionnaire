<?php
global $conn;
include('DBconnection.php');
session_start();


$stmt = $conn->prepare("INSERT INTO marks (student_id, student_name, questionnaire_id, correct_count, time_taken) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssis", $student_id, $student_name, $questionnaire_id, $correct_count, $time_taken);

$student_id = $_POST['student_id'];
$student_name = $_POST['student_name'];
$questionnaire_id = $_POST['questionnaire_id'];
$correct_count = $_POST['correct_count'];
$time_taken = $_POST['time_taken'];

if ($stmt->execute()) {
    $_SESSION['index_number'] = "";
    $_SESSION['name'] = "";
    $_SESSION['questionnaire_id'] = "";
    header('Content-Type: application/json');

    $response = array(
        'status' => 'redirect',
        'url' => 'http://localhost/QuickQuestionnaire/index.php' // URL to redirect to
    );

    echo json_encode($response);
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();

