
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Gemini Language Tutor</title>
  <style>
    body {
      font-family: sans-serif;
      max-width: 600px;
      margin: 2rem auto;
      padding: 1rem;
    }

    h1 {
      text-align: center;
    }

    select, input, button {
      margin: 0.5rem 0;
      padding: 0.5rem;
      width: 100%;
      font-size: 1rem;
    }

    #messages p {
      padding: 0.5rem;
      background: #f4f4f4;
      margin: 0.5rem 0;
      border-radius: 4px;
    }

    #messages p strong {
      color: #555;
    }
  </style>
</head>
<body>
  <main>
    <h1>Esperanto AI Language Tutor</h1>

    <label for="lang">Choose a language:</label>
    <select id="lang">
      <option value="EN">English</option>
      <option value="FR">French</option>
      <option value="IT">Italian</option>
      <option value="ES">Spanish</option>
      <option value="DE">German</option>
    </select>

    <div id="messages"></div>

    <input type="text" id="userInput" placeholder="Type your message..." />
    <button class="btn-primary" onclick="promptGemini()">Send</button>
  </main>

  <script>
    const API = 'AIzaSyDgvsWWTws0f3Xg35N5Iz0g5N6bIX2-5fs';

    const languageMap = {
      EN: 'English',
      FR: 'French',
      IT: 'Italian',
      ES: 'Spanish',
      DE: 'German'
    };

    const messages = [];

    async function promptGemini() {
      const inputElem = document.getElementById('userInput');
      const langElem = document.getElementById('lang');
      const msgContainer = document.getElementById('messages');

      const input = inputElem.value.trim();
      const selectedLang = langElem.value;

      if (!input) return;

      messages.push({ role: 'user', message: input });
      renderMessages();

      inputElem.value = '';

      const instruction = `Act as a professional ${languageMap[selectedLang]} language tutor.
Explain, correct, and guide me using simple explanations in ${languageMap[selectedLang]}.
Here is the student input: "${input}".
PLEASE RETURN THE RESPONSE IN JSON FORMAT ONLY. DO NOT ADD ANY OTHER TEXT.
your responce should not be in mark down format.
THE JSON STRUCTURE SHOULD BE: {"message": "YOUR RESPONSE HERE"}`;

      try {
        const res = await fetch(
          `https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=${API}`,
          {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              contents: [
                {
                  parts: [{ text: instruction }]
                }
              ]
            })
          }
        );

        if (!res.ok) {
          const errData = await res.json();
          messages.push({ role: 'error', message: errData.error?.message || 'Unknown error' });
          renderMessages();
          return;
        }

        const data = await res.json();
        const text = data.candidates?.[0]?.content?.parts?.[0]?.text || '';

        let parsed;
        try {
          const match = text.match(/json\n([\s\S]*?)\n/) || text.match(/{[\s\S]*}/);
          parsed = JSON.parse(match?.[1] || match?.[0]);
        } catch {
          parsed = { message: 'Failed to parse JSON response.' };
        }

        messages.push({ role: 'Esperanto ai', message: parsed.message });
        renderMessages();
      } catch (err) {
        messages.push({ role: 'error: check if your internet connection is okay or there is no error', message: err.message });
        renderMessages();
      }
    }

    function renderMessages() {
      const msgContainer = document.getElementById('messages');
      msgContainer.innerHTML = '';
      for (const msg of messages) {
        const p = document.createElement('p');
        p.innerHTML = `<strong>${msg.role}:</strong> ${msg.message}`;
        msgContainer.appendChild(p);
      }
    }
  </script>
  <style>
  @import url("https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap");

  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Quicksand", sans-serif;
  }
  
  body {
    background-color: #f9f4ff;
    color: #333;
    padding: 40px 20px;
    background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%237f57f1' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
  }
  
  main {
    max-width: 700px;
    margin: 0 auto;
    background: white;
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(127, 87, 241, 0.1);
  }
  
  h1 {
    text-align: center;
    color: #7F57F1;
    margin-bottom: 30px;
    font-size: 30px;
  }
  
  label {
    display: block;
    margin-bottom: 10px;
    font-weight: 600;
    color: #444;
  }
  
  select {
    width: 100%;
    padding: 12px;
    font-size: 16px;
    border-radius: 10px;
    border: 2px solid #e0e0e0;
    margin-bottom: 20px;
    background-color: #fefefe;
    transition: border 0.2s ease;
  }
  
  select:focus {
    border-color: #7F57F1;
    outline: none;
  }
  
  #messages {
    margin-bottom: 20px;
    min-height: 100px;
    background-color: #f6f6ff;
    padding: 20px;
    border-radius: 10px;
    overflow-y: auto;
    max-height: 300px;
    box-shadow: inset 0 0 10px rgba(127, 87, 241, 0.05);
  }
  
  #messages p {
    margin-bottom: 10px;
    line-height: 1.6;
  }
  
  #messages strong {
    color: #7F57F1;
  }
  
  input[type="text"] {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 16px;
    margin-bottom: 15px;
    transition: border 0.2s ease;
  }
  
  input[type="text"]:focus {
    border-color: #7F57F1;
    outline: none;
  }
  
  .btn-primary {
    background-color: #7F57F1;
    color: white;
    padding: 12px 25px;
    font-size: 16px;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  
  .btn-primary:hover {
    background-color: #6d45e0;
  }
  
  @media (max-width: 600px) {
    main {
      padding: 20px;
    }
  
    h1 {
      font-size: 24px;
    }
  
    .btn-primary {
      width: 100%;
    }
  }
  </style>  
</body>
</html>

