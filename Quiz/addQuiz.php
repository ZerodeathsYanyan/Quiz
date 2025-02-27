<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="design.css?v=<?php echo time(); ?>">
   <title>Create Quiz</title>
   <script>
       function toggleOptions(selectElement) {
           let parent = selectElement.closest('.question-box');
           parent.querySelector('.multiple-choice').style.display = (selectElement.value === "multiple_choice") ? "block" : "none";
           parent.querySelector('.problem-solving').style.display = (selectElement.value === "problem_solving") ? "block" : "none";
       }

       function addQuestionSet() {
           let container = document.getElementById('questions-container');
           let questionIndex = container.dataset.questionIndex ? parseInt(container.dataset.questionIndex) : 1;

           let difficulties = ['easy', 'normal', 'hard'];
           for (let difficulty of difficulties) {
               let column = document.getElementById(difficulty + '-column');
               let questionHTML = `
                   <div class="question-box" id="question-${questionIndex}-${difficulty}">
                       <div class="question-number">${questionIndex}</div>
                       <div class="question-content">
                           <label>Question:</label>
                           <input type="text" name="questions[${difficulty}][]" required value="What is the capital of France?"><br>

                           <label>Type:</label>
                           <select name="question_type[${difficulty}][]" onchange="toggleOptions(this)">
                               <option value="multiple_choice" selected>Multiple Choice</option>
                               <option value="problem_solving">Problem Solving</option>
                           </select><br>

                           <label>Upload Image:</label>
                           <input type="file" name="question_images[${difficulty}][]"><br>

                           <div class="multiple-choice">
                               <div class="options-container">
                                   <div class="option-pair">
                                       <label>Option A:</label>
                                       <input type="text" name="option_a[${difficulty}][]" value="Paris"><br>
                                       <label>Option C:</label>
                                       <input type="text" name="option_c[${difficulty}][]" value="Berlin"><br>
                                   </div>
                                   <div class="option-pair">
                                       <label>Option B:</label>
                                       <input type="text" name="option_b[${difficulty}][]" value="Rome"><br>
                                       <label>Option D:</label>
                                       <input type="text" name="option_d[${difficulty}][]" value="Madrid"><br>
                                   </div>
                               </div>

                               <label>Correct Answer:</label>
                               <select name="correct_option[${difficulty}][]">
                                   <option value="A">A</option>
                                   <option value="B">B</option>
                                   <option value="C">C</option>
                                   <option value="D">D</option>
                               </select><br>
                           </div>
                           <div class="problem-solving" style="display:none;">
                               <label>Correct Code:</label>
                               <textarea name="problem_answer[${difficulty}][]"></textarea><br>
                           </div>
                       </div>
                       <button type="button" onclick="removeQuestionSet(${questionIndex})">Delete Question</button>
                       <hr>
                   </div>
               `;
               column.insertAdjacentHTML('beforeend', questionHTML);
           }
           container.dataset.questionIndex = questionIndex + 1;
           toggleCreateQuizButton();
       }

       function removeQuestionSet(questionIndex) {
           let difficulties = ['easy', 'normal', 'hard'];
           for (let difficulty of difficulties) {
               let questionElement = document.getElementById(`question-${questionIndex}-${difficulty}`);
               if (questionElement) {
                   questionElement.remove();
               }
           }
           renumberQuestions();
           toggleCreateQuizButton();
       }

       function renumberQuestions() {
           let container = document.getElementById('questions-container');
           let questionIndex = 1;
           let difficulties = ['easy', 'normal', 'hard'];

           let maxLength = Math.max(
               document.querySelectorAll('#easy-column .question-box').length,
               document.querySelectorAll('#normal-column .question-box').length,
               document.querySelectorAll('#hard-column .question-box').length
           );

           for (let i = 0; i < maxLength; i++) {
               difficulties.forEach(difficulty => {
                   let questionElement = document.querySelector(`#${difficulty}-column .question-box:nth-of-type(${i + 1})`);
                   if (questionElement) {
                       questionElement.id = `question-${questionIndex}-${difficulty}`;
                       questionElement.querySelector('.question-number').textContent = questionIndex;
                   }
               });
               questionIndex++;
           }

           container.dataset.questionIndex = questionIndex;
       }

       function toggleCreateQuizButton() {
           let createQuizButton = document.querySelector('button[name="createQuiz"]');
           let questionsContainer = document.getElementById('questions-container');
           let hasQuestions = questionsContainer.querySelectorAll('.question-box').length > 0;
           createQuizButton.disabled = !hasQuestions;
       }

       window.onload = function() {
           addQuestionSet();
           toggleCreateQuizButton();
       };
   </script>
</head>

<body class="addQuiz">

<h1>Create a Quiz</h1>

<form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
    <label>Quiz Title:</label>
    <input type="text" name="title" required value="Geography Quiz"><br>

    <label>Description:</label>
    <textarea name="description" required>This quiz tests your knowledge on world geography.</textarea><br>

    <label>Max Attempts:</label>
    <input type="number" name="max_attempts" value="1" min="1" required><br>

    <h3>Add Questions</h3>
    <div class="question-container" id="questions-container" data-question-index="1">
        <div id="easy-column" class="question-column">
            <h4>Easy</h4>
        </div>
        <div id="normal-column" class="question-column">
            <h4>Normal</h4>
        </div>
        <div id="hard-column" class="question-column">
            <h4>Hard</h4>
        </div>
    </div>

    <button type="button" onclick="addQuestionSet()">Add Another Set of Questions</button>
    <button type="submit" name="createQuiz">Create Quiz</button>
</form>

</body>
</html>
