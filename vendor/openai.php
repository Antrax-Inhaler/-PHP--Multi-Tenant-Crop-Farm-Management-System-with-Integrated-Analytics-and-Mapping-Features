<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom styles */
        #chat {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .message {
            margin-bottom: 10px;
        }
        .user-message {
            text-align: right;
        }
    </style>
</head>
<body>
    <div id="chat" class="container">
        <div id="messages" class="mb-3"></div>
        <div class="input-group">
            <input type="text" id="userInput" class="form-control" placeholder="Type your message...">
            <button class="btn btn-primary" onclick="sendMessage()">Send</button>
        </div>
    </div>
    
    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    
    <!-- OpenAI API Browser -->
    <script src="https://cdn.jsdelivr.net/npm/@openai/openai-api-browser@1/dist/openai-api-browser.min.js"></script>
    <script>
        const openai = new OpenAI('sk-proj-dyC1mGDlzMrcFHC7lNm3T3BlbkFJVODY6mdEWC0aUcQs5z6Y');

let messages = [];

// Initial system message
const systemMsg = prompt("The data of the vendor gathered in database:\n");
messages.push({ role: "system", content: systemMsg });

// Display initial message
appendMessage("Hi, this is NAFA robot, your farming assistant is ready!");

function appendMessage(message, role) {
    const messagesDiv = document.getElementById("messages");
    const messageElement = document.createElement("div");
    messageElement.classList.add("message");
    if (role === "user") {
        messageElement.classList.add("user-message");
    }
    messageElement.innerText = message;
    messagesDiv.appendChild(messageElement);
}

function sendMessage() {
    const userInput = document.getElementById("userInput").value;
    messages.push({ role: "user", content: userInput });
    appendMessage(userInput, "user");

    // Send request to OpenAI
    openai.complete({
        engine: "text-davinci-002",
        prompt: messages,
        max_tokens: 150
    }).then((response) => {
        const assistantReply = response.data.choices[0].text.trim();
        messages.push({ role: "assistant", content: assistantReply });
        appendMessage(assistantReply);
        
        // Clear input field
        document.getElementById("userInput").value = "";
    });
}
    </script>
</body>
</html>
