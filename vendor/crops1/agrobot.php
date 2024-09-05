<?php

include "openai_api.php"; // API
// or
// include "openai_chat.php"; // chat.openai.com (GPT-3.5)

?>
  <style>
    #chatbotContainer {
      display: none;
      position: fixed;
      bottom: 20px;
      right: 10px;
      width: 300px;
      max-height: 400px;
      border-radius: 10px;
      background-color: #2ddc9a;
      color: white;
      overflow: hidden;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      z-index: 1000;
    }
    @media (max-width: 768px) {
    #chatbotContainer {
      width: 95%;
    }
  }

    #chatbotHeader {
      background-color: #2ddc9a;
      padding: 10px;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
      cursor: pointer;
      user-select: none;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    #chatbotBody {
      padding: 10px;
      max-height: 300px;
      overflow-y: auto;
      background-color: white;
      color: black;
    }

    #toggleButton {
  position: fixed;
  top: 200px;
  right: 5px;
  background: radial-gradient(circle at 30% 30%, #2ddc9a, #1ca76a);
  color: white;
  padding: 10px;
  border-radius: 50%;
  border: none;
  cursor: pointer;
  z-index: 1000;
  width: 60px;
  height: 60px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2), inset 0 1px 2px rgba(255, 255, 255, 0.5);
  transition: box-shadow 0.3s ease;
}

#toggleButton:hover {
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4), inset 0 2px 4px rgba(255, 255, 255, 0.6);
}


    .message {
      margin-bottom: 10px;
    }

    .message p {
      margin: 0;
    }

    .message.you {
      text-align: right;
    }

    .message.server {
      text-align: left;
    }
    .chatbot-img{
      width: 100%;
    }
    .submitStyle{
      background: linear-gradient(circle at 30% 30%, #2ddc9a, #1ca76a);
      border: none;
      border-radius: 20px;
    }
  </style>

  <button id="toggleButton">
    <img class="chatbot-img" src="crops/chatbot.png" alt="">
    
  </button>
  <div id="chatbotContainer">
    <div id="chatbotHeader">
      <span>AgroNet's AI Powered Chatbot</span>
      <button id="closeButton" style="background:none; border:none; color:white;">&times;</button>
    </div>
    <div id="chatbotBody">
      <form method="post" id="chatForm">
        <div class="form-group">
          <input type="text" class="form-control" id="conversationId" placeholder="Set chat ID or leave blank to start a new chat" <?= !function_exists('openai_chat') ? 'style="display:none"' : '' ?> value="">
        </div>
        <div id="messages" class="messages">
          <!-- Messages will be displayed here -->
        </div>
        <div class="form-group">
          <textarea rows="1" class="form-control" id="messageInput" placeholder="Ask anything about your crops"></textarea>
        </div>
        <button  type="submit" class="btn btn-primary w-100 submitStyle" id="sendMessage">Send</button>
      </form>
    </div>
  </div>
  <script>
    const toggleButton = document.getElementById("toggleButton");
    const closeButton = document.getElementById("closeButton");
    const chatbotContainer = document.getElementById("chatbotContainer");
    const conversationId = document.getElementById("conversationId");
    const messageInput = document.getElementById("messageInput");
    const sendMessage = document.getElementById("sendMessage");
    const messages = document.getElementById("messages");
  
    var parent_message_id = '';
  
    conversationId.value = getCookie("conversation_id");
  
    messageInput.focus();
  
    toggleButton.addEventListener("click", () => {
      chatbotContainer.style.display = chatbotContainer.style.display === "none" ? "block" : "none";
    });
  
    closeButton.addEventListener("click", () => {
      chatbotContainer.style.display = "none";
    });
  
    document.getElementById("chatForm").addEventListener("submit", sendMessageHandler);
  
    function sendMessageHandler(event) {
      event.preventDefault();
  
      const message = messageInput.value;
      if (!message.trim()) {
        messageInput.focus();
        return;
      }
  
      const conversation_id = conversationId.value;
      
      sendMessage.innerText = 'loading...';
      conversationId.disabled = true;
      sendMessage.disabled = true;
      messageInput.disabled = true;
  
      const messageElement = document.createElement("div");
      messageElement.className = "message you";
      messageElement.innerHTML = '<p>You: ' + replaceHTML(message) + '</p>';
      messages.appendChild(document.createElement("hr"));
      messages.appendChild(messageElement);
  
      messageInput.value = "";
      messageInputResize();
  
      fetch('', {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          message: message,
          conversation_id: conversation_id,
          parent_message_id: parent_message_id
        }),
      })
      .then((response) => response.json())
      .then((data) => {
        sendMessage.innerText = 'Send';
        sendMessage.disabled = false;
        messageInput.disabled = false;
        messageInput.focus();
  
        const messageElement = document.createElement("div");
        messageElement.className = "message server";
        const p = document.createElement("p");
  
        if(data.hasOwnProperty('error')){
          p.innerHTML = 'Server: ' + data.error.msg;
        }
        else if (data.hasOwnProperty('message')) {
          p.innerHTML = 'Server: ' + marked(data.message);
          conversationId.value = data.conversation_id;
          parent_message_id = data.parent_message_id;
          setCookie("conversation_id", conversation_id, 365);
        }
        messageElement.appendChild(p);
        messages.appendChild(document.createElement("hr"));
        messages.appendChild(messageElement);
        document.querySelectorAll("pre code").forEach((block) => {
          hljs.highlightBlock(block);
        });
        window.scrollTo(0, document.body.scrollHeight);
      })
      .catch(error => {
        console.error(error);
        sendMessage.innerText = 'Send';
        sendMessage.disabled = false;
        messageInput.disabled = false;
        messageInput.focus();
      });
    }
  
    function messageInputResize() {
      messageInput.style.height = "auto";
      messageInput.style.height = (messageInput.scrollHeight + 2) + "px";
    }
    messageInput.addEventListener("input", messageInputResize);
  
    function replaceHTML(str) {
      const jsEntities = [
        ['&', '&amp;'],
        ['<', '&lt;'],
        ['>', '&gt;'],
        ['\'', '&#39;'],
        ['"', '&quot;'],
        ['\n', '<br>'],
        ['\t', '&nbsp;&nbsp;']
      ];
      for (let i = 0; i < jsEntities.length; i++) {
        str = str.replace(new RegExp(jsEntities[i][0], 'g'), jsEntities[i][1]);
      }
      return str;
    }
  
    function setCookie(name, value, days) {
      let expires = "";
      if (days) {
        let date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
      }
      document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }
  
    function getCookie(name) {
      let nameEQ = name + "=";
      let ca = document.cookie.split(';');
      for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
      }
      return '';
    }
  </script>