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
        body {
            background: linear-gradient(135deg, #2b2e4a, #e84545);
            font-family: 'Poppins', sans-serif;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #1f1b24;
            padding: 2em;
            border-radius: 10px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2);
        }

        h1 {
            text-align: center;
            margin-bottom: 1em;
            font-size: 2rem;
        }

        .form-group {
            margin-bottom: 1em;
        }

        label {
            display: block;
            margin-bottom: 0.5em;
        }

        input[type="text"], input[type="number"], select {
            width: calc(100% - 20px);
            padding: 0.5em;
            margin-bottom: 1em;
            border-radius: 5px;
            border: none;
            background: #2b2e4a;
            color: #fff;
        }

        .question-item {
            margin-bottom: 1em;
            padding: 1em;
            border: 1px solid #3a3f58;
            border-radius: 5px;
            background-color: #2b2e4a;
        }

        .question-item input[type="text"] {
            margin-right: 1em;
            width: 70%;
        }

        .answer-text {
            margin-right: 0.5em;
        }

        .difficulty {
            margin-top: 0.5em;
            padding: 0.5em;
            width: 100%;
            border-radius: 5px;
            background: #3a3f58;
            color: #fff;
            border: none;
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 1.5em;
        }

        .button-group button {
            padding: 0.8em 1.2em;
            border-radius: 5px;
            border: none;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }

        #remove-question {
            background-color: #e84545;
        }

        #remove-question:hover {
            background-color: #d73737;
        }

        #add-question {
            background-color: #28a745;
        }

        #add-question:hover {
            background-color: #218838;
        }

        .post-btn {
            margin-top: 2em;
            width: 100%;
            padding: 1em;
            background-color: #007bff;
            border-radius: 5px;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            transition: background 0.3s;
        }

        .post-btn:hover {
            background-color: #0056b3;
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
        const questionTemplate = document.querySelector('.question-item').cloneNode(true);
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
