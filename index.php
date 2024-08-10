<?php
// Start the session to save user details
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Save user input to session variables
    $_SESSION['index_number'] = $_POST['index_number'];
    $_SESSION['name'] = $_POST['name'];

    // Redirect to Questionnaire.php
    header("Location: Questionnaire.php");
    exit();
}
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
        }

        label {
            display: block;
            font-size: 1.2em;
            color: #ccc;
            margin-bottom: 10px;
        }

        input[type="text"],input[type="password"] {
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
            width: 100%;
            padding: 12px;
            font-size: 1.2em;
            border: none;
            border-radius: 10px;
            background-color: #3498DB;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        button:hover {
            background-color: #2980B9;
            transform: scale(1.05);
        }

        .right-section button {
            background-color: #1ABC9C;
        }

        .right-section button:hover {
            background-color: #16A085;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>Challenge Your Knowledge, Anytime, Anywhere</h1>
</div>
<div class="container">
    <div class="section">
        <h2>Quiz Portal</h2>
        <form method="POST" action="">
            <label for="index_number">Index Number:</label>
            <input type="text" id="index_number" name="index_number" required>

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <button type="submit">Take The Quiz</button>
        </form>
    </div>
    <div class="section right-section">
        <h2>Create A Quiz</h2>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button onclick="location.href='Admin.php'">Create A Quiz</button>
        </form>

    </div>
</div>
</body>
</html>
