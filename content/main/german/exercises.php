<?php
require_once '../../../database/connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practice - Language Project</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../style.css">

</head>
<body class="min-h-screen flex items-center justify-center p-4 font-[Quicksand]">
    <div class="max-w-2xl w-full bg-white rounded-2xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Practice</h2>
            <span id="questionCounter" class="text-sm text-gray-500">Question 1</span>
        </div>

        <a href="german.php" class="flex items-center color :violet; hover:text-violet-600 mb-4">
            <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.707 14.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L4.414 9H18a1 1 0 110 2H4.414l3.293 3.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back
        </a>

        <div id="questionArea" class="mb-6"></div>

        <div class="mt-6 flex justify-end">
            
            <button id="nextButton" onclick="handleNextQuestion()" class="btn-primary">Next</button>
        </div>
    </div>

    <script>
        let currentQuestion = 1;
        let score = 0;
        const totalQuestions = 10;
        let answered = false;

        let vocabQuestions = [
            { question: "What does this word mean in German?", word: "Train", image: "https://img.icons8.com/dusk/64/train.png", options: ["Auto", "Zug", "Flugzeug", "Fahrrad"], answer: "Zug" },
            { question: "What does this word mean in German?", word: "Passport", image: "https://img.icons8.com/dusk/64/passport.png", options: ["Führerschein","Ticket","Pass", "Karte"], answer: "Pass" },
            { question: "What does this word mean in German?", word: "Apple", image: "https://img.icons8.com/external-vitaliy-gorbachev-lineal-color-vitaly-gorbachev/60/external-apple-fruit-vitaliy-gorbachev-lineal-color-vitaly-gorbachev.png", options: ["Banane", "Apfel", "Traube", "Orange"], answer: "Apfel" },
            { question: "What does this word mean in German?", word: "Book", image: "https://img.icons8.com/stickers/100/book-1.png", options: ["Buch", "Heft", "Papier", "Stift"], answer: "Buch" },
            { question: "What does this word mean in German?", word: "House", image: "https://img.icons8.com/plasticine/50/cottage.png", options: ["Tur", "Zimmer", "Flugzeug", "Haus"], answer: "Haus" }


        ];

        let fillQuestions = [
            { question: "Fill in the blank: Ich ___ müde.", answer: "bin" },
            { question: "Fill in the blank: Er ___ nach Hause.", answer: "geht" },
            { question: "Fill in the blank: Ich ___ Fußball.", answer: "spiele" },
            { question: "Fill in the blank: Sie ___ ein Buch.", answer: "liest" },
            { question: "Du ___ sehr schnell.", answer: "laufst" }
        ];

        function loadQuestion() {
            const questionArea = document.getElementById("questionArea");
            const counter = document.getElementById("questionCounter");
            counter.textContent = `Question ${currentQuestion} / ${totalQuestions}`;
            answered = false;

            // Choose from vocab or fill, only if questions remain
            let q;
            if (vocabQuestions.length > 0 && fillQuestions.length > 0) {
                if (Math.random() > 0.5) {
                    q = vocabQuestions.splice(Math.floor(Math.random() * vocabQuestions.length), 1)[0];
                    renderVocabQuestion(q);
                } else {
                    q = fillQuestions.splice(Math.floor(Math.random() * fillQuestions.length), 1)[0];
                    renderFillQuestion(q);
                }
            } else if (vocabQuestions.length > 0) {
                q = vocabQuestions.splice(Math.floor(Math.random() * vocabQuestions.length), 1)[0];
                renderVocabQuestion(q);
            } else if (fillQuestions.length > 0) {
                q = fillQuestions.splice(Math.floor(Math.random() * fillQuestions.length), 1)[0];
                renderFillQuestion(q);
            } else {
                questionArea.innerHTML = "<p class='text-center text-gray-700'>No more questions available.</p>";
                document.getElementById("nextButton").style.display = "none";
            }
        }

        function renderVocabQuestion(q) {
            document.getElementById("questionArea").innerHTML = `
                <p class="text-lg font-medium text-gray-700 mb-4">${q.question}</p>
                <div class="flex flex-col items-center mb-4">
                    <img src="${q.image}" alt="${q.word}" class="w-24 h-24 mb-2">
                    <h3 class="text-xl font-semibold">${q.word}</h3>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    ${q.options.map(option => `
                        <button class="option-btn bg-white border border-gray-300 rounded-xl p-4 hover:bg-violet-100 transition" onclick="checkAnswerVocab('${option}', '${q.answer}')">${option}</button>
                    `).join('')}
                </div>
            `;
        }

        function renderFillQuestion(q) {
            document.getElementById("questionArea").innerHTML = `
                <p class="text-lg font-medium text-gray-700 mb-4">${q.question}</p>
                <input type="text" id="fillInput" class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Type your answer here...">
                <div class="mt-4 flex justify-end">
                    <button onclick='checkAnswerFill("${q.answer}")' class="bg-violet-500 text-white font-semibold px-6 py-2 rounded-full hover:bg-violet-700 transition">Check</button>
                </div>
            `;

            setTimeout(() => {
                document.getElementById("fillInput").addEventListener("keypress", function(event) {
                    if (event.key === "Enter") {
                        event.preventDefault();
                        checkAnswerFill(q.answer);
                    }
                });
            }, 0);
        }

        function checkAnswerVocab(selected, correct) {
            if (answered) return;
            answered = true;

            document.querySelectorAll('.option-btn').forEach(btn => {
                if (btn.textContent === correct) {
                    btn.classList.add('bg-green-200', 'border-green-500');
                } else if (btn.textContent === selected) {
                    btn.classList.add('bg-red-200', 'border-red-500');
                }
                btn.disabled = true;
            });

            if (selected === correct) score++;
            updateNextButton();
        }

        function checkAnswerFill(correct) {
            if (answered) return;
            answered = true;

            const input = document.getElementById("fillInput");
            const value = input.value.trim();
            const checkBtn = input.nextElementSibling;

            if (value.toLowerCase() === correct.toLowerCase()) {
                score++;
                input.classList.add('bg-green-200', 'border-green-500');
            } else {
                input.classList.add('bg-red-200', 'border-red-500');
            }

            input.disabled = true;
            checkBtn.disabled = true;
            updateNextButton();
        }

        function updateNextButton() {
            document.getElementById("nextButton").textContent = currentQuestion < totalQuestions ? "Next" : "Finish";
        }

        function handleNextQuestion() {
            if (!answered) {
                alert("Please answer the question first.");
                return;
            }

            if (currentQuestion < totalQuestions) {
                currentQuestion++;
                loadQuestion();
                document.getElementById("nextButton").textContent = "Next";
            } else {
                submitResult();
            }
        }

        function submitResult() {
            const payload = { score, total: totalQuestions };

            fetch("save_score.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload)
            })
            .then(res => {
                if (!res.ok) throw new Error(`HTTP Error: ${res.status}`);
                return res.json();
            })
            .then(data => {
                document.getElementById("questionArea").innerHTML = `
                    <div class="text-center">
                        <h2 class="text-2xl font-bold mb-4">Results</h2>
                        <p class="text-xl">You scored <strong>${score}</strong> out of <strong>${totalQuestions}</strong></p>
                        ${data.rating ? `<p class="mt-2">Your rating: <strong>${data.rating}</strong></p>` : ""}
                    </div>
                `;
                document.getElementById("questionCounter").style.display = "none";
                document.getElementById("nextButton").style.display = "none";
            })
            .catch(err => {
                console.error("Error:", err);
                document.getElementById("questionArea").innerHTML = "<p class='text-center text-red-500'>Error submitting results. Try again later.</p>";
            });
        }

        window.onload = loadQuestion;
    </script>
</body>
</html>
