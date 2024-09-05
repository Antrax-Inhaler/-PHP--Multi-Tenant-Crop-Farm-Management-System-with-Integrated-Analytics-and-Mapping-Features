<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agronet Chat</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .chat-container {
            max-width: 600px;
            margin: 50px auto;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .chat-message {
            margin-bottom: 10px;
        }
        .user-message {
            text-align: right;
            color: #007bff;
        }
        .ai-message {
            text-align: left;
            color: #28a745;
        }
        .error-message {
            text-align: left;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="chat-container">
            <div id="chat-box"></div>
            <form id="chat-form">
                <div class="form-group">
                    <input type="text" class="form-control" id="message-input" placeholder="Type your message...">
                </div>
                <button type="submit" class="btn btn-primary">Send</button>
            </form>
        </div>
    </div>

    <!-- Use full jQuery library instead of slim version -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#chat-form').submit(function(event) {
                event.preventDefault();
                var message = $('#message-input').val().trim();
                if (message !== '') {
                    sendMessage(message);
                    $('#message-input').val('');
                }
            });

            function sendMessage(message) {
                $('#chat-box').append('<div class="chat-message user-message">' + message + '</div>');
                $.ajax({
                    url: 'gemini_ai/ai.php', // Tiyaking ito ay tama depende sa lokasyon ng ai.php
                    method: 'POST',
                    dataType: 'json',
                    data: { message: message },
                    success: function(response) {
                        $('#chat-box').append('<div class="chat-message ai-message">' + response.content + '</div>');
                    },
                    error: function(xhr, status, error) {
                        var errorMessage = xhr.responseJSON && xhr.responseJSON.content ? xhr.responseJSON.content : 'Oops! Something went wrong.';
                        $('#chat-box').append('<div class="chat-message error-message">' + errorMessage + '</div>');
                    }
                });
            }
        });
    </script>
</body>
</html>
