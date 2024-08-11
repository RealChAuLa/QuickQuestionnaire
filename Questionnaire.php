<?php
global $conn;
include('DBconnection.php');
session_start();
date_default_timezone_set('Asia/Colombo');
$today = date('Y-m-d');
// Get the questionnaire_id from POST method
 // Assuming the questionnaire_id is passed as 'questionnaire_topic'

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['index_number']) && isset($_POST['name']) && isset($_POST['questionnaire_id'])) {
        $_SESSION['index_number'] = $_POST['index_number'];
        $_SESSION['name'] = $_POST['name'];
        $_SESSION['questionnaire_id'] = $_POST['questionnaire_id'];

        header("Location: Questionnaire.php");
        exit();
    }
}

$questionnaire_id = $_SESSION['questionnaire_id'];

// Get the questionnaire details using the questionnaire_id
$sql = "SELECT * FROM questionnaire WHERE questionnaire_id = '$questionnaire_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $questionnaire = $result->fetch_assoc();
    $questionnaire_id = $questionnaire['questionnaire_id'];
    $questionnaire_topic = $questionnaire['topic'];
    $questionnaire_time = $questionnaire['time'];

    // Retrieve questions from the 'questions' table for the selected questionnaire_id
    $sql = "SELECT * FROM questions WHERE questionnaire_id = $questionnaire_id";
    $result = $conn->query($sql);

    $questions = array();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $questions[] = $row;
        }
    } else {
        echo "No questions found for today's questionnaire.";
        exit();
    }



    // Set the countdown timer to the questionnaire's time
    if (!isset($_SESSION['countdown_start'])) {
        $_SESSION['countdown_start'] = time();
    }

    $total_seconds = $questionnaire_time * 60;
    $elapsed_seconds = time() - $_SESSION['countdown_start'];
    $remaining_seconds = max(0, $total_seconds - $elapsed_seconds);

    // Reset countdown if it's finished
    if ($remaining_seconds <= 0) {
        unset($_SESSION['countdown_start']);
    }
} else {
    echo "No questionnaire for today.";
    exit();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shuffled Questionnaire</title>
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

        h1 {
            font-size: 3em;
            color: #fff;
            margin-bottom: 40px;
        }

        #questionnaire {
            background: rgba(44, 62, 80, 0.85);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 800px;
            margin-bottom: 20px;
            position: relative;
        }

        p {
            font-size: 1.6em;
            color: #fff;
            margin-bottom: 20px;
        }

        label {
            font-size: 1.4em;
            color: #ccc;
            margin-left: 15px;
        }

        div[id^="questionnaire"] div {
            margin-bottom: 20px;
        }

        input[type="radio"] {
            margin-right: 10px;
            transform: scale(1.2);
        }

        button, select {
            padding: 15px 25px;
            font-size: 1.4em;
            font-family: 'Poppins', sans-serif;
            border: none;
            border-radius: 12px;
            background-color: #34495E;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
            outline: none;
            margin-right: 15px;
        }

        button:hover, select:hover {
            transform: scale(1.05);
        }

        select {
            background-color: #34495E;
        }

        select:hover {
            background-color: #229954;
        }

        select option {
            background-color: #34495E;
            color: #fff;
        }

        button:last-of-type {
            background-color: #34495E;
        }

        button:last-of-type:hover {
            background-color: #C0392B;
        }

        #confirmBtn {
            background-color: #2ECC71;
            position: absolute;
            right: 20px;
            bottom: 20px;
        }

        #confirmBtn:hover {
            background-color: #28A745;
        }

        .top-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 880px;
            margin-bottom: 20px;
        }

        .bottom-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 880px;
            margin-top: 20px;
        }
    </style>
    <script>
        // Pass PHP data to JavaScript
        const questions = <?php echo json_encode($questions); ?>;

        // Function to shuffle an array
        function shuffle(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
            return array;
        }

        // Create a doubly linked list node
        class ListNode {
            constructor(data) {
                this.data = data;
                this.next = null;
                this.prev = null;
            }
        }

        // Create a doubly linked list
        class DoublyLinkedList {
            constructor() {
                this.head = null;
                this.tail = null;
                this.size = 0;
            }

            append(data) {
                const newNode = new ListNode(data);
                if (this.size === 0) {
                    this.head = this.tail = newNode;
                } else {
                    this.tail.next = newNode;
                    newNode.prev = this.tail;
                    this.tail = newNode;
                }
                this.size++;
            }

            toArray() {
                let current = this.head;
                const array = [];
                while (current) {
                    array.push(current.data);
                    current = current.next;
                }
                return array;
            }
        }

        // Shuffle the questions
        shuffle(questions);

        // Shuffle answers for each question
        questions.forEach(question => {
            let answers = [
                question.correct_answer,
                question.wrong_answer_1,
                question.wrong_answer_2,
                question.wrong_answer_3
            ];
            shuffle(answers);
            question.answers = answers;
        });

        // Convert questions array to doubly linked list
        const questionList = new DoublyLinkedList();
        questions.forEach(q => questionList.append(q));

        // Rendering logic
        let currentNode = questionList.head;

        function renderQuestion() {
            const questionContainer = document.getElementById('questionnaire');
            questionContainer.innerHTML = '';

            if (currentNode) {
                const questionElement = document.createElement('p');
                questionElement.textContent = currentNode.data.question;
                questionContainer.appendChild(questionElement);

                currentNode.data.answers.forEach(answer => {
                    const answerElement = document.createElement('div');
                    const radioInput = document.createElement('input');
                    radioInput.type = 'radio';
                    radioInput.name = 'answer';
                    radioInput.value = answer;

                    // Check if this answer was previously selected
                    if (currentNode.data.userAnswer === answer) {
                        radioInput.checked = true;
                    }

                    answerElement.appendChild(radioInput);

                    const label = document.createElement('label');
                    label.textContent = answer;
                    answerElement.appendChild(label);

                    questionContainer.appendChild(answerElement);
                });

                // Confirm button
                const confirmBtn = document.createElement('button');
                confirmBtn.textContent = 'Confirm';
                confirmBtn.id = 'confirmBtn';
                confirmBtn.onclick = saveAnswer;
                questionContainer.appendChild(confirmBtn);
            }
        }


        function saveAnswer() {
            const selectedAnswer = document.querySelector('input[name="answer"]:checked');
            if (selectedAnswer) {
                currentNode.data.userAnswer = selectedAnswer.value;
            }
        }

        function showNextQuestion() {
            if (currentNode.next) {
                currentNode = currentNode.next;
                renderQuestion();
            }
        }

        function showPreviousQuestion() {
            if (currentNode.prev) {
                currentNode = currentNode.prev;
                renderQuestion();
            }
        }

        function sortQuestions(criteria) {
            const sortedArray = questionList.toArray();

            if (criteria === 'Easy to Hard') {
                sortedArray.sort((a, b) => {
                    const difficultyOrder = { 'easy': 1, 'medium': 2, 'hard': 3 };
                    return difficultyOrder[a.difficulty] - difficultyOrder[b.difficulty];
                });
            } else if (criteria === 'Large to Small') {
                sortedArray.sort((a, b) => b.question.length - a.question.length);
            } else {
                // Default option: reshuffle questions
                shuffle(sortedArray);
            }

            questionList.head = null;
            questionList.tail = null;
            questionList.size = 0;

            sortedArray.forEach(q => questionList.append(q));
            currentNode = questionList.head;
            renderQuestion();
        }

        function endQuestionnaire() {
            let correctCount = 0;
            let current = questionList.head;

            // Calculate the correct answers
            while (current) {
                if (current.data.userAnswer === current.data.correct_answer) {
                    correctCount++;
                }
                current = current.next;
            }

            // Prepare the form data
            const formData = new FormData();
            formData.append('student_id', '<?php echo $_SESSION['index_number']; ?>');
            formData.append('student_name', '<?php echo $_SESSION['name']; ?>');
            formData.append('questionnaire_id', '<?php echo $questionnaire_id; ?>');
            formData.append('correct_count', correctCount);
            formData.append('time_taken', '<?php echo $questionnaire_time * 60 - $remaining_seconds; ?>');

            // Submit results to the server using fetch
            fetch('submit_results.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())
                .then(data => {
                    if (data.includes("Error")) {
                        alert("There was an error submitting your results: " + data);
                    } else {
                        alert(data);
                    }
                })
                .catch(error => {
                    alert("There was a problem submitting your results: " + error.message);
                });

            alert(`You answered ${correctCount} questions correctly!`);
        }



        function startCountdown() {
            let timeLeft = <?php echo $remaining_seconds; ?>;

            function updateCountdown() {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                const countdownString = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                document.getElementById('countdown').textContent = countdownString;

                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    endQuestionnaire();
                } else {
                    timeLeft--;
                }
            }

            updateCountdown();
            const countdownInterval = setInterval(updateCountdown, 1000);
        }

        window.addEventListener('beforeunload', function (e) {
            // Cancel the event
            e.preventDefault();
            // Chrome requires returnValue to be set
            e.returnValue = '';
        });


        // Initial render
        window.onload = function() {
            renderQuestion();
            startCountdown();
        }
    </script>
</head>
<body>
<h1><?php echo $questionnaire_topic; ?> Questionnaire</h1>
<div class="top-controls">
    <div>
        <button onclick="showPreviousQuestion()">Previous</button>
        <button onclick="showNextQuestion()">Next</button>
    </div>
    <select onchange="sortQuestions(this.value)">
        <option value="">Sort By</option>
        <option value="Default">Default</option>
        <option value="Easy to Hard">Easy to Hard</option>
        <option value="Large to Small">Large to Small</option>
    </select>
</div>
<div id="questionnaire">
    <!-- The question and answer options will be dynamically inserted here -->
</div>
<div class="bottom-controls">
    <button onclick="endQuestionnaire()">End Questionnaire</button>
    <div id="countdown"></div>
</div>
</body>
</html>
