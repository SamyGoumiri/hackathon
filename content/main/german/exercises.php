<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Practice - Language Project</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="../style-exo.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
  <div class="max-w-2xl w-full bg-white rounded-2xl shadow-lg p-6">
    
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
    <img width="50" height="50" src="https://img.icons8.com/fluency/100/goal--v1.png" alt="goal--v1"/>
      <h2 class="text-2xl font-bold text-gray-800">Practice</h2>
      <span class="text-sm text-gray-500">Question 1 of 10</span>
    </div>

    <!-- Question Prompt -->
    <div class="mb-4">
      <p class="text-lg font-medium text-gray-700">What does this word mean in German?</p>
    </div>

    <!-- Image/Icon -->
    <div class="flex justify-center mb-6">
      <img src="https://cdn-icons-png.flaticon.com/512/167/167707.png" alt="Train icon" class="w-24 h-24">
    </div>

    <!-- Options -->
    <div class="grid grid-cols-1 gap-4">
      <button class="bg-white border border-gray-300 rounded-xl p-4 text-left hover:bg-blue-100 transition duration-200">
        🚗 Auto
      </button>
      <button class="bg-white border border-gray-300 rounded-xl p-4 text-left hover:bg-blue-100 transition duration-200">
        🚉 Zug
      </button>
      <button class="bg-white border border-gray-300 rounded-xl p-4 text-left hover:bg-blue-100 transition duration-200">
        ✈️ Flugzeug
      </button>
      <button class="bg-white border border-gray-300 rounded-xl p-4 text-left hover:bg-blue-100 transition duration-200">
        🚲 Fahrrad
      </button>
    </div>

    <!-- Footer -->
    <div class="mt-6 flex justify-end">
      <button class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-full hover:bg-blue-700 transition">Next</button>
    </div>

    <!-- Divider -->
    <hr class="my-8">

    <!-- Another Type: Fill in the blank -->
    <div class="mb-4">
      <p class="text-lg font-medium text-gray-700">Fill in the blank: <span class="font-bold">Ich ___ müde.</span></p>
    </div>
    <input type="text" class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Type your answer here...">
    
    <div class="mt-6 flex justify-end">
      <button class="bg-green-600 text-white font-semibold px-6 py-2 rounded-full hover:bg-green-700 transition">Check</button>
    </div>
    
  </div>
</body>
</html>
