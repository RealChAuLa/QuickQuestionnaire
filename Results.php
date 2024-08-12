<?php
global $conn;
session_start();
include('DBconnection.php');

if (isset($_GET['id'])) {
    $questionnaire_id = $_GET['id'];

    $query = "SELECT student_id, correct_count, time_taken FROM marks WHERE questionnaire_id = '$questionnaire_id'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $totalQuestionsQuery = "SELECT COUNT(*) as total FROM questions WHERE questionnaire_id = '$questionnaire_id'";
        $totalResult = mysqli_query($conn, $totalQuestionsQuery);
        $totalRow = mysqli_fetch_assoc($totalResult);
        $totalQuestions = $totalRow['total'];
    }
} else {
    echo "<p>No questionnaire selected.</p>";
    header("Location:index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #2C3E50;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: rgba(44, 62, 80, 0.6);
        }

        tr:hover {
            background-color: rgba(44, 62, 80, 0.8);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(120deg, #2C3E50, #4CA1AF);
            color: #ddd;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 2.8em;
            color: #fff;
            margin: 0;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            max-width: 1200px;
            gap: 30px;
        }

        .section {
            flex: 1;
            background: rgba(44, 62, 80, 0.8);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            max-width: 450px;
        }

        h2 {
            font-size: 2em;
            color: #fff;
            margin-bottom: 20px;
            text-align: center;
            margin-top: auto;
        }

        label {
            display: block;
            font-size: 1.2em;
            color: #ccc;
            margin-bottom: 10px;
        }

        input[type="text"],input[type="password"],select {
            width: calc(100% - 24px);
            padding: 12px;
            font-size: 1.1em;
            border: none;
            border-radius: 10px;
            margin-bottom: 20px;
            background: #34495E;
            color: #fff;
            outline: none;
        }

        button {
            font-family: 'Poppins', sans-serif;
            width: 100%;
            padding: 12px;
            font-size: 1.2em;
            border: 1px solid white;
            border-radius: 10px;
            background-color: rgba(52, 152, 219, 0);
            color: white;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        button:hover {
            background-color: #2980B9;
            transform: scale(1.05);
            border: none;
        }

        #TakeQuiz {
            background-color: #1ABC9C;
            border: none;
        }

        #TakeQuiz:hover {
            background-color: #16a042;
        }


    </style>
</head>
<body>
<div class="header">
    <h1>Quiz Results</h1>
</div>
<div class="container">
    <div class="section">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <table>
                <tr>
                    <th>Student ID</th>
                    <th>Marks</th>
                    <th>Time Spent</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['student_id']; ?></td>
                        <td><?php echo $row['correct_count'] . " / " . $totalQuestions; ?></td>
                        <td><?php echo $row['time_taken'] . " minutes"; ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No results found for this questionnaire.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
