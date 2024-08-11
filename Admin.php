<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "questionnaire";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topic = $_POST['topic'];
    $time = $_POST['time'];
    $questions = json_decode($_POST['questions'], true);

    // Insert into questionnaire table
    $stmt = $conn->prepare("INSERT INTO questionnaire (topic, time, date) VALUES (?, ?, NOW())");
    $stmt->bind_param("si", $topic, $time);
    $stmt->execute();
    $questionnaire_id = $stmt->insert_id;
    $stmt->close();

    // Insert each question into the questions table
    foreach ($questions as $q) {
        $stmt = $conn->prepare("INSERT INTO questions (question, correct_answer, wrong_answer_1, wrong_answer_2, wrong_answer_3, difficulty, questionnaire_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $q['question'], $q['correct_answer'], $q['wrong_answer_1'], $q['wrong_answer_2'], $q['wrong_answer_3'], $q['difficulty'], $questionnaire_id);
        $stmt->execute();
    }
    $stmt->close();

    $conn->close();
    echo "<script>alert('Questionnaire posted successfully!');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Questionnaire</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        * {
            border-radius: 30px;
        }

        body {
            background: -webkit-linear-gradient(180deg, #2C3E50, #4CA1AF, #2C3E50);
            font-family: 'Poppins', sans-serif;
            color: #ddd;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: rgba(44, 62, 80, 0.8);
            padding: 2em;
            border-radius: 30px;
            max-width: 880px;
            width: 100%;
            margin-top: 5vh;
            margin-bottom: 5vh;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.3);
        }

        h1 {
            text-align: center;
            margin-bottom: 1em;
            font-size: 2.5rem;
            color: #fff;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 1em;
        }

        label {
            display: block;
            margin-bottom: 0.5em;
            font-size: 1.2em;
            color: #ccc;
            font-weight: 500;
        }

        input[type="text"], input[type="number"], select {
            width: calc(100% - 20px);
            padding: 0.8em;
            margin-bottom: 1em;
            border-radius: 10px;
            border: none;
            background: #34495E;
            color: #fff;
            outline: none;
            font-size: 1.1em;
            font-weight: 500;
        }

        .question-item {
            margin-bottom: 1em;
            padding: 1em;
            border: 1px solid #3a3f58;
            border-radius: 15px;
            background-color: #2b2e4a;
        }

        .question-item input[type="text"] {
            margin-right: 1em;
            width: 70%;
            font-weight: 500;
        }

        .answer-text {
            margin-right: 0.5em;
            font-weight: 500;
        }

        .difficulty {
            margin-top: 0.5em;
            padding: 0.8em;
            width: 100%;
            border-radius: 10px;
            background: #3a3f58;
            color: #fff;
            font-size: 1.1em;
            font-weight: 500;
            border: none;
            outline: none;
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 1.5em;
        }

        .button-group button {
            padding: 0.8em 1.2em;
            border-radius: 10px;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s, transform 0.3s;
            color: white;
        }

        #remove-question {
            background-color: #E74C3C;
        }

        #remove-question:hover {
            background-color: #C0392B;
            transform: scale(1.05);
        }

        #add-question {
            background-color: #2ECC71;
        }

        #add-question:hover {
            background-color: #27AE60;
            transform: scale(1.05);
        }

        .post-btn {
            margin-top: 2em;
            width: 100%;
            padding: 1em;
            background-color: #3498DB;
            border-radius: 10px;
            border: none;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
            color: white;
        }

        .post-btn:hover {
            background-color: #2980B9;
            transform: scale(1.05);
        }

    </style>
</head>
<body>
<div class="container">
    <h1>Create a New Questionnaire</h1>
    <form method="POST" onsubmit="return submitForm()">
        <div class="form-group">
            <label for="topic">Questionnaire Topic</label>
            <input type="text" id="topic" name="topic" placeholder="Enter questionnaire topic" required>
        </div>

        <div class="form-group">
            <label for="time">Time (in minutes)</label>
            <input type="number" id="time" name="time" min="1" value="10" required>
        </div>

        <div id="question-section">
            <h2>Questions</h2>
            <!-- Initial Question Template -->
            <div class="question-item">
                <input type="radio" name="selected-question" class="select-question">
                <input type="text" class="question-text" placeholder="Enter the question">
                <input type="text" class="answer-text" placeholder="Correct Answer">
                <input type="text" class="answer-text" placeholder="Wrong Answer 1">
                <input type="text" class="answer-text" placeholder="Wrong Answer 2">
                <input type="text" class="answer-text" placeholder="Wrong Answer 3">
                <select class="difficulty">
                    <option value="easy">Easy</option>
                    <option value="medium">Medium</option>
                    <option value="hard">Hard</option>
                </select>
            </div>
        </div>

        <div class="button-group">
            <button type="button" id="remove-question">Remove Selected Question</button>
            <button type="button" id="add-question">Add Question</button>
        </div>

        <button type="submit" id="post-questionnaire" class="post-btn">Post Questionnaire</button>

        <!-- Hidden field to store questions JSON -->
        <input type="hidden" id="questions" name="questions">
    </form>
</div>

<script>
    class ListNode {
        constructor(data) {
            this.data = data;
            this.next = null;
        }
    }

    class SinglyLinkedList {
        constructor() {
            this.head = null;
            this.size = 0;
        }

        append(data) {
            const newNode = new ListNode(data);
            if (!this.head) {
                this.head = newNode;
            } else {
                let current = this.head;
                while (current.next) {
                    current = current.next;
                }
                current.next = newNode;
            }
            this.size++;
        }

        remove(index) {
            if (index < 0 || index >= this.size) return;

            if (index === 0) {
                this.head = this.head.next;
            } else {
                let previous = null;
                let current = this.head;
                let i = 0;

                while (i < index) {
                    previous = current;
                    current = current.next;
                    i++;
                }

                previous.next = current.next;
            }
            this.size--;
        }

        toArray() {
            const arr = [];
            let current = this.head;
            while (current) {
                arr.push(current.data);
                current = current.next;
            }
            return arr;
        }
    }

    const questionList = new SinglyLinkedList();

    document.getElementById('add-question').addEventListener('click', () => {
        // Clone the question template node
        const questionTemplate = document.querySelector('.question-item').cloneNode(true);

        // Clear the values of the input fields in the cloned node
        questionTemplate.querySelectorAll('input[type="text"]').forEach(input => input.value = '');
        questionTemplate.querySelectorAll('input[type="radio"]').forEach(radio => radio.checked = false);
        questionTemplate.querySelector('select').selectedIndex = 0;

        // Append the cloned and cleared node to the question section
        document.getElementById('question-section').appendChild(questionTemplate);
    });


    document.getElementById('remove-question').addEventListener('click', () => {
        const selectedQuestion = document.querySelector('input[name="selected-question"]:checked');
        if (selectedQuestion) {
            const index = Array.from(document.querySelectorAll('input[name="selected-question"]')).indexOf(selectedQuestion);
            questionList.remove(index);
            selectedQuestion.closest('.question-item').remove();
        }
    });

    function submitForm() {
        const questions = [];
        document.querySelectorAll('.question-item').forEach(item => {
            const questionData = {
                question: item.querySelector('.question-text').value,
                correct_answer: item.querySelectorAll('.answer-text')[0].value,
                wrong_answer_1: item.querySelectorAll('.answer-text')[1].value,
                wrong_answer_2: item.querySelectorAll('.answer-text')[2].value,
                wrong_answer_3: item.querySelectorAll('.answer-text')[3].value,
                difficulty: item.querySelector('.difficulty').value
            };
            questions.push(questionData);
            questionList.append(questionData);
        });
        document.getElementById('questions').value = JSON.stringify(questionList.toArray());
        return true; // Allows form submission
    }



</script>
</body>
</html>
