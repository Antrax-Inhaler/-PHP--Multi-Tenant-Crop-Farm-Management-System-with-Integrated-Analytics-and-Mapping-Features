<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap">
<style>
    /* Existing CSS styles */
    .chatbot-toggler {
        position: fixed;
        bottom: 30px;
        right: 35px;
        outline: none;
        border: none;
        height: 50px;
        width: 50px;
        display: flex;
        cursor: pointer;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #c6e1ff;
        transition: all 0.2s ease;
        box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
        z-index: 2000;
    }
    @media (max-width: 768px) {
        .chatbot-toggler {
            bottom: 260px;
        }
    }
    .show-chatbot .chatbot-toggler {
        transform: rotate(90deg);
    }
    .chatbot-toggler span {
        position: absolute;
        display: flex;
    }
    .chatbot-toggler span:first-child img {
        width: 30px;
    }
    .chatbot-toggler span:last-child img {
        width: 15px;
    }
    .chatbot-toggler span:last-child,
    .show-chatbot .chatbot-toggler span:first-child {
        opacity: 0;
    }
    .show-chatbot .chatbot-toggler span:last-child {
        opacity: 1;
    }
    .chatbot {
        position: fixed;
        right: 35px;
        bottom: 90px;
        width: 420px;
        background: #fff;
        border-radius: 15px;
        overflow: hidden;
        opacity: 0;
        pointer-events: none;
        transform: scale(0.5);
        transform-origin: bottom right;
        box-shadow: 0 0 128px 0 rgba(0, 0, 0, 0.1),
        0 32px 64px -48px rgba(0, 0, 0, 0.5);
        transition: all 0.1s ease;
        z-index: 1000;
    }
    .show-chatbot .chatbot {
        opacity: 1;
        pointer-events: auto;
        transform: scale(1);
    }
    .chatbot header {
        padding: 16px 0;
        position: relative;
        color: #fff;
        background: #529cf1;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    .chatbot header small, .chatbot header h2 {
        display: flex;
        align-items: center;
        margin-left: 20px;
    }
    .chatbot header small img {
        width: 10px;
        height: 10px;
        margin: 5px;
    }
    .chatbot header h2 {
        font-size: 1.4rem;
    }
    .chatbox {
        overflow-y: auto;
        height: 510px;
        padding: 30px 20px 100px;
    }
    .chatbox::-webkit-scrollbar {
        width: 6px;
    }
    .chatbox::-webkit-scrollbar-track {
        background: #fff;
        border-radius: 25px;
    }
    .chatbox::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 25px;
    }
    .chat {
        display: flex;
        list-style: none;
    }
    .outgoing {
        margin: 20px 0;
        justify-content: flex-end;
    }
    .incoming span {
        width: 32px;
        color: #fff;
        cursor: default;
        text-align: center;
        line-height: 32px;
        align-self: flex-end;
        background: #c9d4e2;
        border-radius: 4px;
        margin: 0 10px 2px 0;
    }
    .chat h6 {
        padding: 12px 16px;
        border-radius: 10px 10px 0 10px;
        max-width: 75%;
        color: #fff;
        font-size: 0.95rem;
        background: #33a6fd;
    }
    .incoming h6 {
        border-radius: 10px 10px 10px 0;
        color: #000;
        background: #f2f2f2;
    }
    .chat h6.error {
        color: #721c24;
        background: #f8d7da;
    }
    .incoming span img {
        width: 26px;
    }
    .chat-input {
        display: flex;
        position: absolute;
        bottom: 0;
        width: 100%;
        background: #c8e2ff;
        padding: 13px 20px;
        border-top: 1px solid #ddd;
    }
    .chat-input textarea {
        height: 50px;
        width: 90%;
        border: none;
        border-bottom: 1px solid #949494;
        outline: none;
        resize: none;
        max-height: 180px;
        padding: 15px 15px 5px 5px;
        font-size: 0.95rem;
        margin-left: 10px;
        background-color: transparent;
    }
    .chat-input span {
        align-self: flex-end;
        color: #0b4be1;
        cursor: pointer;
        height: 55px;
        display: flex;
        align-items: center;
    }
    .chat-input span img {
        width: 35px;
    }
    .chat-input textarea:valid ~ span {
        visibility: visible;
    }
    @media (max-width: 490px) {
        .chatbot-toggler {
            right: 10px;
            bottom: 460px;
        }
        .chatbot {
            right: 0;
            bottom: 0;
            height: 100%;
            border-radius: 0;
            width: 100%;
        }
        .chatbox {
            height: 90%;
            padding: 25px 15px 100px;
        }
        .chat-input {
            padding: 5px 15px;
        }
        .chatbot header span {
            display: block;
        }
    }
    #questionList {
        background-color: white;
        align-items: right;
        display: none;
        width: 100%;
        overflow-y: auto;
        position: fixed;
        height: 510px;
        margin-bottom: 40px;
    }

    .question-item {
        cursor: pointer;
        padding: 12px 16px;
        border-radius: 10px;
        max-width: 75%;
        color: #fff;
        font-size: 0.95rem;
        background: #33a6fd;
        margin-bottom: 5px;
    }

    .question-item:hover {
        background-color: #33a6fd;
    }
    .thinking-emoji { /* Adjusted size to match the send button */
        font-size: 30px;
        display: flex;
        text-align: center;
        flex-direction: column;
        align-items: center;
    }

    @keyframes thinking {
        0%, 100% {
            transform: translate(-50%, -50%) rotate(0deg);
        }
        50% {
            transform: translate(-50%, -50%) rotate(10deg);
        }
    }

    .chat-heads {
        display: flex;
        overflow-x: auto;
        padding: 10px;
        background-color: #fff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .chat-head {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        overflow: hidden;
        margin-right: 10px;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .chat-head img {
        width: 100%;
        height: 100%;
    }

    .chat-heads::-webkit-scrollbar {
        height: 6px;
    }

    .chat-heads::-webkit-scrollbar-track {
        background: #fff;
        border-radius: 25px;
    }

    .chat-heads::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 25px;
    }

</style>

<button class="chatbot-toggler">
    <span><img src="crops/chatbot.png" alt=""></span>
    <span><img src="https://img.icons8.com/?size=256&id=71200&format=png" alt=""></span>
</button>
<div class="chatbot">
    <header>
        <h2>AgroNet's AI Powered Chatbot</h2>
        <small><img src="https://img.icons8.com/?size=256&id=10591&format=png" alt=""><span>Online</span></small>
    </header>
    <div class="chat-heads">
        <div class="chat-head"><img src="user1.jpg" alt="User 1"></div>
        <div class="chat-head"><img src="user2.jpg" alt="User 2"></div>
        <div class="chat-head"><img src="user3.jpg" alt="User 3"></div>
        <!-- Add more chat heads as needed -->
    </div>
    <div id="questionList"></div>
    <div class="chatbox"></div>
    <div class="chat-input">
        <textarea placeholder="Type your message here..."></textarea>
        <span><img src="https://img.icons8.com/?size=256&id=3065&format=png" alt=""></span>
    </div>
</div>
<script>
    const chatbotToggler = document.querySelector('.chatbot-toggler');
const chatbot = document.querySelector('.chatbot');
const chatbox = document.querySelector('.chatbox');
const chatInput = document.querySelector('.chat-input textarea');
const sendButton = document.querySelector('.chat-input span');
const chatHeads = document.querySelectorAll('.chat-head');

chatbotToggler.addEventListener('click', () => {
    chatbot.classList.toggle('show-chatbot');
});

chatHeads.forEach(chatHead => {
    chatHead.addEventListener('click', () => {
        // You can fetch and load the chat history of the selected user here.
        chatbox.innerHTML = ''; // Clear current chatbox
        // Load chat history of the selected user
    });
});

sendButton.addEventListener('click', () => {
    const userMessage = chatInput.value.trim();
    if (userMessage) {
        // Display the user's message in the chatbox
        const outgoingMessage = document.createElement('div');
        outgoingMessage.classList.add('chat', 'outgoing');
        outgoingMessage.innerHTML = `<h6>${userMessage}</h6>`;
        chatbox.appendChild(outgoingMessage);
        
        // Clear the textarea
        chatInput.value = '';
        
        // Scroll to the bottom of the chatbox
        chatbox.scrollTop = chatbox.scrollHeight;

        // Send the message to the server and get the response
        // This is where you integrate with your chatbot backend
        const thinking = document.createElement('div');
        thinking.classList.add('chat', 'incoming');
        thinking.innerHTML = `<span class="thinking-emoji">🤔</span><h6 class="thinking-emoji">Thinking...</h6>`;
        chatbox.appendChild(thinking);

        // Simulate chatbot response
        setTimeout(() => {
            thinking.remove();
            const chatbotResponse = document.createElement('div');
            chatbotResponse.classList.add('chat', 'incoming');
            chatbotResponse.innerHTML = `<span><img src="https://img.icons8.com/?size=256&id=10591&format=png" alt="AgroNet"></span><h6>Here is a simulated response.</h6>`;
            chatbox.appendChild(chatbotResponse);

            // Scroll to the bottom of the chatbox
            chatbox.scrollTop = chatbox.scrollHeight;
        }, 2000); // Simulate a delay for the response
    }
});

</script>