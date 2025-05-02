<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Practice - Language Project</title>

  <link rel="stylesheet" href="../style-exo.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
  <div class="max-w-2xl w-full bg-white rounded-2xl shadow-lg p-6">
    
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Practice</h2>
      <span id="questionCounter" class="text-sm text-gray-500">Question 1</span>
    </div>

    <a href="../index.html" class="back-button">
  <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 20 20" fill="currentColor">
    <path fill-rule="evenodd" d="M7.707 14.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L4.414 9H18a1 1 0 110 2H4.414l3.293 3.293a1 1 0 010 1.414z" clip-rule="evenodd" />
  </svg>
  <span>Back</span>
</a>



    <!-- Question Area -->
    <div id="questionArea" class="mb-6"></div>

    <!-- Footer -->
    <div class="mt-6 flex justify-end">
      <button onclick="loadRandomQuestion()" class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-full hover:bg-blue-700 transition">Next</button>
    </div>
  </div>

  <script>
    let currentQuestion = 1;
    let score = 0;
    const totalQuestions = 10;


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
        options: ["Führerschein", "Pass", "Ticket", "Karte"],
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

    function loadRandomQuestion() {
      const questionArea = document.getElementById("questionArea");
      const counter = document.getElementById("questionCounter");
      counter.textContent = `Question ${currentQuestion++}`;

      const isVocab = Math.random() > 0.5;

      if (isVocab) {
        const q = vocabQuestions[Math.floor(Math.random() * vocabQuestions.length)];

        questionArea.innerHTML = `
          <div class="mb-4">
            <p class="text-lg font-medium text-gray-700">${q.question}</p>
          </div>
          <div class="flex justify-center mb-6">
            <img src="${q.image}" alt="${q.word}" class="w-24 h-24">
          </div>
          <div class="grid grid-cols-1 gap-4">
            ${q.options.map(option => `
              <button class="bg-white border border-gray-300 rounded-xl p-4 text-left hover:bg-blue-100 transition duration-200">${option}</button>
            `).join("")}
          </div>
        `;
      } else {
        const q = fillQuestions[Math.floor(Math.random() * fillQuestions.length)];

        questionArea.innerHTML = `
          <div class="mb-4">
            <p class="text-lg font-medium text-gray-700">${q.question}</p>
          </div>
          <input type="text" class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Type your answer here...">
          <div class="mt-6 flex justify-end">
            <button class="bg-green-600 text-white font-semibold px-6 py-2 rounded-full hover:bg-green-700 transition">Check</button>
          </div>
        `;
      }
    }

    function submitResult() {
    fetch("save_score.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ score: score, total: totalQuestions })
    })
    .then(res => res.json())
    .then(data => {
      document.getElementById("questionArea").innerHTML = `
        <div class="text-center">
          <h2 class="text-2xl font-bold mb-4">Results</h2>
          <p class="text-xl">You scored <strong>${score}</strong> out of <strong>${totalQuestions}</strong></p>
        </div>
      `;
    });
  }

  function loadRandomQuestion() {
    if (currentQuestion >= totalQuestions) {
      submitResult();
      return;
    }

    const isVocab = Math.random() > 0.5;
    const questionArea = document.getElementById("questionArea");
    currentQuestion++;

    if (isVocab) {
      const q = vocabQuestions[Math.floor(Math.random() * vocabQuestions.length)];
      questionArea.innerHTML = `
        <div class="mb-4">${q.question}</div>
        <img src="${q.image}" class="w-20 mx-auto mb-4">
        ${q.options.map(option => `
          <button class="option-btn" onclick="handleAnswer('${option}', '${q.answer}')">${option}</button>
        `).join('')}
      `;
    } else {
      const q = fillQuestions[Math.floor(Math.random() * fillQuestions.length)];
      questionArea.innerHTML = `
        <div class="mb-4">${q.question}</div>
        <input id="fillInput" type="text" class="border rounded p-2 w-full mb-4">
        <button class="bg-blue-500 text-white px-4 py-2 rounded" onclick="handleAnswer(document.getElementById('fillInput').value, '${q.answer}')">Submit</button>
      `;
    }
  }

  function handleAnswer(userAnswer, correctAnswer) {
    if (checkAnswer(userAnswer, correctAnswer)) {
      score++;
    }
    loadRandomQuestion();
  }



    window.onload = loadRandomQuestion;
  </script>
</body>
</html>

