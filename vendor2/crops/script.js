const chatMessages = document.getElementById("chat-messages");
const inputText = document.getElementById("text");

function scrollToBottom() {
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function addMessage(role, content) {
    const messageElement = document.createElement("div");
    messageElement.classList.add(`${role}-message`);
    messageElement.innerHTML = `<strong>[${role.charAt(0).toUpperCase() + role.slice(1)}]:</strong> ${content}`;
    chatMessages.appendChild(messageElement);
    scrollToBottom();
}

function generateResponse() {
    const text = inputText.value.trim();
    if (!text) return;

    addMessage("user", text);
    inputText.value = "";

    fetch("response.php", {
        method: "POST",
        body: JSON.stringify({ text: text }),
        headers: {
            "Content-Type": "application/json"
        }
    })
    .then((res) => {
        if (!res.ok) {
            throw new Error(`HTTP error! Status: ${res.status}`);
        }
        return res.json();
    })
    .then((data) => {
        addMessage("assistant", data.content);
    })
    .catch((error) => {
        console.error("Error:", error);
        addMessage("assistant", "Error fetching or processing data");
    });
}

// Initial message to introduce the bot
window.onload = function() {
    addMessage("assistant", "Welcome! I'm AgroBot. Ask me about crops or farms.");
    setTimeout(() => {
        // Simulate the initial conversation without user seeing it
        fetch("response.php", {
            method: "POST",
            body: JSON.stringify({ text: "Can I call you AgroBot, and this is my crops data on Agronet: " + JSON.stringify([{
                "Id": "1",
                "Name": "Rice",
                "Age": "1 month",
                "PestIssue": {
                    "Name": "Pest A",
                    "Status": "Existing",
                    "SizeOfAreaAffected": "10.50"
                }
            }])}),
            headers: {
                "Content-Type": "application/json"
            }
        })
        .then((res) => res.json())
        .then((data) => {
            addMessage("assistant", data.content);
        })
        .catch((error) => {
            console.error("Error:", error);
        });
    }, 500);
};
