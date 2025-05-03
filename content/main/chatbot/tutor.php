<?php
session_start();
require_once '../../../database/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_query = "SELECT username, first_name, last_name FROM users WHERE user_id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../dashboard.css">
    <link rel="stylesheet" href="chatbot.css">
    <title>Esperanto - Language Tutor</title>
</head>
<body>

    <header>
        <div class="header-container">
            <div class="logo">
                <h1>Esperanto</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="../dashboard.php">Dashboard</a></li>
                    <li><a href="../games/games.php">Games</a></li>
                    <li><a href="chatbot.php" class="active">ChatBot</a></li>
                </ul>
            </nav>
            <div class="user-menu">
                <div class="user-info">
                    <span><?php echo htmlspecialchars(ucfirst($user['first_name']) . ' ' . ucfirst($user['last_name'])); ?></span>
                    <div class="user-avatar">
                        <span class="user-initials">
                            <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                        </span>
                    </div>
                </div>
                <div class="dropdown-menu">
                    <a href="../profile.php"><i class='bx bx-user'></i> Profile</a>
                    <a href="../settings.php"><i class='bx bx-cog'></i> Settings</a>
                    <a href="../../auth/logout.php"><i class='bx bx-log-out'></i> Log Out</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="chat-container">
            <div class="chat-header">
                <h1>Esperanto AI Language Tutor</h1>
                <div class="language-selector">
                    <label for="lang">Choose a language:</label>
                    <select id="lang">
                        <option value="EN">English</option>
                        <option value="FR">French</option>
                        <option value="IT">Italian</option>
                        <option value="ES">Spanish</option>
                        <option value="DE">German</option>
                    </select>
                </div>
            </div>
            
            <div id="messages" class="messages-container"></div>

            <div class="chat-input">
                <input type="text" id="userInput" placeholder="Type your message..." />
                <button class="btn-primary" onclick="promptGemini()">Send</button>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-container">
            <p>&copy; <?php echo date('Y'); ?> Esperanto. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.querySelector('.user-info').addEventListener('click', function() {
            document.querySelector('.dropdown-menu').classList.toggle('active');
        });

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

                messages.push({ role: 'Esperanto AI', message: parsed.message });
                renderMessages();
            } catch (err) {
                messages.push({ role: 'error', message: err.message });
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
            msgContainer.scrollTop = msgContainer.scrollHeight;
        }
        document.getElementById('userInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                promptGemini();
            }
        });
    </script>
</body>
</html>

