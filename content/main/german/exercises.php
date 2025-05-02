<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practice - Language Project</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4 font-[Quicksand]">
    <div class="max-w-2xl w-full bg-white rounded-2xl shadow-lg p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Practice</h2>
            <span id="questionCounter" class="text-sm text-gray-500">Question 1</span>
        </div>

        <!-- Back Button -->
        <a href="german.php" class="flex items-center text-blue-600 hover:text-blue-800 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.707 14.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L4.414 9H18a1 1 0 110 2H4.414l3.293 3.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back
        </a>

        <!-- Question Area -->
        <div id="questionArea" class="mb-6"></div>

        <!-- Next Button -->
        <div class="mt-6 flex justify-end">
            <button id="nextButton" onclick="handleNextQuestion()" class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-full hover:bg-blue-700 transition">Next</button>
        </div>
    </div>

    <script>
        let currentQuestion = 1;
        let score = 0;
        const totalQuestions = 10;
        let answered = false;

        const vocabQuestions = [
            {
                question: "What does this word mean in German?",
                word: "Train",
                image: "https://cdn-icons-png.flaticon.com/512/167/167707.png",
                options: ["Auto", "Zug", "Flugzeug", "Fahrrad"],
                answer: "Zug"
            },
            {
                question: "What does this word mean in German?",
                word: "Passport",
                image: "https://cdn-icons-png.flaticon.com/512/1828/1828911.png",
                options: ["Führerschein","Ticket","pass", "Karte"],
                answer: "Pass"
            }
        ];

        const fillQuestions = [
            {
                question: "Fill in the blank: Ich ___ müde.",
                answer: "bin"
            },
            {
                question: "Fill in the blank: Er ___ nach Hause.",
                answer: "geht"
            }
        ];

        function loadQuestion() {
            const questionArea = document.getElementById("questionArea");
            const counter = document.getElementById("questionCounter");
            counter.textContent = `Question ${currentQuestion} / ${totalQuestions}`;
            answered = false;

            const isVocab = Math.random() > 0.5;

            if (isVocab && vocabQuestions.length > 0) {
                const q = vocabQuestions[Math.floor(Math.random() * vocabQuestions.length)];

                questionArea.innerHTML = `
                    <p class="text-lg font-medium text-gray-700 mb-4">${q.question}</p>
                    <div class="flex justify-center mb-6">
                        <img src="${q.image}" alt="${q.word}" class="w-24 h-24">
                        <h3>${q.word}</h3>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        ${q.options.map(option => `
                            <button class="option-btn bg-white border border-gray-300 rounded-xl p-4 hover:bg-blue-100 transition" onclick="checkAnswerVocab('${option}', '${q.answer}')">${option}</button>
                        `).join('')}
                        <h3>${q.word}</h3>
                    </div>
                `;
            } else if (fillQuestions.length > 0) {
                const q = fillQuestions[Math.floor(Math.random() * fillQuestions.length)];

                questionArea.innerHTML = `
                    <p class="text-lg font-medium text-gray-700 mb-4">${q.question}</p>
                    <input type="text" id="fillInput" class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Type your answer here...">
                    <div class="mt-6 flex justify-end">
                        <button onclick="checkAnswerFill('${q.answer}')" class="bg-green-600 text-white font-semibold px-6 py-2 rounded-full hover:bg-green-700 transition">Check</button>
                    </div>
                `;

                setTimeout(() => {
                    const fillInput = document.getElementById("fillInput");
                    fillInput?.addEventListener("keypress", function(event) {
                        if (event.key === "Enter") {
                            event.preventDefault();
                            checkAnswerFill('${q.answer}');
                        }
                    });
                }, 0);
            } else {
                questionArea.innerHTML = "<p class='text-center text-gray-700'>No more questions available.</p>";
                document.getElementById("nextButton").style.display = "none";
            }
        }

        function checkAnswerVocab(selected, correct) {
            if (answered) return;
            answered = true;

            document.querySelectorAll('.option-btn').forEach(btn => {
                if (btn.textContent === correct) {
                    btn.classList.add('bg-green-200', 'border-green-500');
                } else if (btn.textContent === selected && selected !== correct) {
                    btn.classList.add('bg-red-200', 'border-red-500');
                }
                btn.disabled = true;
            });

            if (selected === correct) score++;
            document.getElementById("nextButton").textContent = currentQuestion < totalQuestions ? "Next" : "Finish";
        }

        function checkAnswerFill(correct) {
            if (answered) return;
            answered = true;

            const input = document.getElementById("fillInput");
            const value = input.value.trim();
            const btn = input.nextElementSibling;

            if (value.toLowerCase() === correct.toLowerCase()) {
                score++;
                input.classList.add('bg-green-200', 'border-green-500');
            } else {
                input.classList.add('bg-red-200', 'border-red-500');
            }

            input.disabled = true;
            btn.disabled = true;

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
