<?php
$servername = "localhost";
$username = "root";  // Your MySQL username
$password = "";      // Your MySQL password
$dbname = "questionnaire";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM questions";
$result = $conn->query($sql);

$questions = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
} else {
    echo "0 results";
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shuffled Questionnaire</title>
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

            while (current) {
                if (current.data.userAnswer === current.data.correct_answer) {
                    correctCount++;
                }
                current = current.next;
            }

            alert(`You answered ${correctCount} questions correctly!`);
        }


        // Initial render
        window.onload = function() {
            renderQuestion();
        }
    </script>
</head>
<body>
<h1>Shuffled Questionnaire</h1>
<div>
    <button onclick="showPreviousQuestion()">Previous</button>
    <button onclick="showNextQuestion()">Next</button>
    <select onchange="sortQuestions(this.value)">
        <option value="">Sort By</option>
        <option value="Easy to Hard">Easy to Hard</option>
        <option value="Large to Small">Large to Small</option>
    </select>
    <button onclick="endQuestionnaire()">End Questionnaire</button>
</div>
<div id="questionnaire"></div>
</body>
</html>
