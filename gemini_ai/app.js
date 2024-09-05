// app.js

document.getElementById('aiForm').addEventListener('submit', function(event) {
    event.preventDefault();

    let userInput = document.getElementById('userInput').value;

    fetch('response.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ text: userInput })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('aiResponse').innerText = data.response;
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('aiResponse').innerText = 'Error fetching response.';
    });
});
