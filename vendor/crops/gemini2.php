<?php
$farm_id = $_GET['id'] ?? null;

if (!$farm_id) {
    die("Farm ID is required.");
}

// Fetch farm details
$farm_query = "
    SELECT 
        farm.Name as farm_name, 
        farm.Latitude as farm_latitude, 
        farm.Longitude as farm_longitude, 
        farm.Size as farm_size,
        farm.Description as farm_description,
        farm.Image as farm_image
    FROM farm 
    WHERE farm.Id = ?
";
$stmt = $conn->prepare($farm_query);
$stmt->bind_param("i", $farm_id);
$stmt->execute();
$result = $stmt->get_result();

$farm_details = [];
while ($row = $result->fetch_assoc()) {
    $farm_details[] = $row;
}
?>
<style>
    /* Your existing CSS styles */
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
    .show-chatbot .chatbot-toggler span:first-child  {
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
        box-shadow: 0 0 128px 0 rgba(0,0,0,0.1),
                    0 32px 64px -48px rgba(0,0,0,0.5);
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
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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

</style>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap">

<button class="chatbot-toggler">
    <span><img src="crops/chatbot.png" alt=""></span>
    <span><img src="https://img.icons8.com/?size=256&id=71200&format=png" alt=""></span>
</button>
<div class="chatbot">
    <header>
        <h2>AgroNet's AI Powered Chatbot</h2>
        <small><img src="https://img.icons8.com/?size=256&id=JIlJqN3SJL07&format=png" alt="">Online</small>
        <span class="close-btn"></span>
    </header>
    <ul class="chatbox" id="chatbox">
        <div id="questionList">
            <ul>
                <?php if (!empty($farm_details)): ?>
                    <?php
                    $farm_name = htmlspecialchars($farm_details[0]['farm_name']);
                    $farm_latitude = htmlspecialchars($farm_details[0]['farm_latitude']);
                    $farm_longitude = htmlspecialchars($farm_details[0]['farm_longitude']);
                    $farm_size = htmlspecialchars($farm_details[0]['farm_size']);
                    $farm_description = htmlspecialchars($farm_details[0]['farm_description']);
                    $farm_image = htmlspecialchars($farm_details[0]['farm_image']);
                    ?>
                    <li class="question-item">What are the best practices for managing <?php echo $farm_name; ?> farm located at latitude <?php echo $farm_latitude; ?> and longitude <?php echo $farm_longitude; ?>?</li>
                    <li class="question-item">Given that <?php echo $farm_name; ?> is <?php echo $farm_size; ?> hectares in size, what are the optimal crop rotation strategies?</li>
                    <li class="question-item">Can you provide recommendations for improving soil health on <?php echo $farm_name; ?>?</li>
                    <li class="question-item">What are the common pests and diseases affecting farms in the region of latitude <?php echo $farm_latitude; ?> and longitude <?php echo $farm_longitude; ?>, and how can they be managed?</li>
                    <li class="question-item">How can I improve the irrigation system for <?php echo $farm_name; ?> farm?</li>
                    <li class="question-item">What are the best organic farming practices for <?php echo $farm_name; ?>?</li>
                    <li class="question-item">What weather conditions are most favorable for planting in <?php echo $farm_name; ?> farm?</li>
                    <li class="question-item">Given the current soil conditions and description of <?php echo $farm_description; ?>, what crops would be most suitable for planting?</li>
                    <li class="question-item">How can I optimize the yield for crops grown in <?php echo $farm_name; ?>?</li>
                    <li class="question-item">What modern farming technologies can be implemented in <?php echo $farm_name; ?> to enhance productivity?</li>
                <?php else: ?>
                    <li class="question-item">No farm details found for the given ID.</li>
                <?php endif; ?>
            </ul>
        </div>
        <li class="chat incoming">
            <span><img src="crops/chatbot.png" alt=""></span>
            <h6>Hi there! Welcome to AgroNet's AI Powered Chatbot. How can I assist you today?</h6>
        </li>
    </ul>
    <div class="chat-input">
        <textarea placeholder="Enter a message..." spellcheck="false" required id="messageInput"></textarea>
        <span id="sendBtn">
            <span style="display: flex; flex-direction: column;" class="thinking-emoji" id="toggleQuestionList">🤔</span>
            <img src="https://img.icons8.com/?size=256&id=ZznWGhUzgWtS&format=png" alt="">
        </span>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    var chatbotToggler = $('.chatbot-toggler');
    var closeBtn = $('.close-btn');
    var chatInput = $('#messageInput');
    var sendChatBtn = $('#sendBtn');
    $('#toggleQuestionList').click(function() {
            $('#questionList').toggle();
        });
        function hideQuestionList() {
            $('#questionList').hide();
        }
    chatbotToggler.click(function() {
        $('body').toggleClass('show-chatbot');
    });

    closeBtn.click(function() {
        $('body').removeClass('show-chatbot');
    });

    $('#chatForm').submit(function(event) {
        event.preventDefault();
        var message = chatInput.val().trim();
        if (message !== '') {
            sendMessage(message);
            chatInput.val('');
        }
    });
 // Auto-send question to AI when clicked
 $('.question-item').click(function() {
            var message = $(this).text().trim();
            sendMessage(message);
            hideQuestionList();
        });
    sendChatBtn.click(function() {
        var message = chatInput.val().trim();
        if (message !== '') {
            sendMessage(message);
            chatInput.val('');
        }
    });

    function sendMessage(message) {
        $('#chatbox').append('<li class="chat outgoing"><h6>' + message + '</h6></li>');
        $('#chatbox').append('<li class="chat incoming"><span><img src="crops/chatbot.png" alt=""></span><h6></h6></li>');

        $.ajax({
            url: 'crops/ai.php',
            method: 'POST',
            dataType: 'json',
            data: { message: message },
            success: function(response) {
                var lastIncoming = $('#chatbox').children('li.chat.incoming').last();
                var content = response.content;

                content = content.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');
                content = content.replace(/\*/g, '<br><br>');

                lastIncoming.find('h6').html(content);
            },
            error: function(xhr, status, error) {
                var lastIncoming = $('#chatbox').children('li.chat.incoming').last();
                lastIncoming.find('h6').text('Oops! Something went wrong.');
            }
        });
    }
});
</script>
