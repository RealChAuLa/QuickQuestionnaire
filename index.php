<?php
global $conn;
session_start();
include('DBconnection.php'); // Include your database connection script

// Handle form submission for Quiz Portal


// Fetch questionnaire topics for current date
date_default_timezone_set('Asia/Colombo');

$currentDate = date('Y-m-d');
$query = "SELECT questionnaire_id, topic FROM questionnaire WHERE date = '$currentDate'";
$result = mysqli_query($conn, $query);

// Fetch all questionnaire topics for Results section
$resultsQuery = "SELECT questionnaire_id, topic FROM questionnaire WHERE date <= CURDATE()";
$resultsResult = mysqli_query($conn, $resultsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to the Quiz Portal</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

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
    <h1>Challenge Your Knowledge, Anytime, Anywhere</h1>
</div>
<div class="container">
    <div class="section" style="height:320px">
        <h2>Results</h2>
        <form method="GET" action="Results.php" style="margin-top: 46px;">
            <label for="id">Questionnaire Topic</label>
            <select name="id" required>
                <option value="">Select</option>
                <?php while ($row = mysqli_fetch_assoc($resultsResult)): ?>
                    <option value="<?php echo $row['questionnaire_id']; ?>"><?php echo $row['topic']; ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit" style="margin-top: 77px;">Show Results</button>
        </form>
    </div>
    <div class="section">
        <h2>Quiz Portal</h2>
        <form method="POST" action="Questionnaire.php">
            <label for="questionnaire_id">Questionnaire Topic</label>
            <select name="questionnaire_id" required>
                <option value="">Select</option>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <option value="<?php echo $row['questionnaire_id']; ?>"><?php echo $row['topic']; ?></option>
                <?php endwhile; ?>
            </select>

            <label for="index_number">Index Number:</label>
            <input type="text" id="index_number" name="index_number" required>

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <button id="TakeQuiz" type="submit">Take The Quiz</button>
        </form>
    </div>
    <div class="section right-section" style="height:320px">
        <h2>Create A Quiz</h2>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="button" onclick="location.href='Admin.php'">Create A Quiz</button>
        </form>

    </div>
</div>

</body>
</html>
