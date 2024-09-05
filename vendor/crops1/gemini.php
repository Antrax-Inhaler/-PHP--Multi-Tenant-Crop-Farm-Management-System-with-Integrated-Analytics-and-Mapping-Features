<?php
// Fetch crop details based on crop ID
$crop_id = $_GET['id'] ?? null;

if (!$crop_id) {
    die("Crop ID is required.");
}

$sql = "SELECT crop.Name AS crop_name, crop.Type AS crop_type, crop_activity.activity_type, crop_activity.activity_date, crop_activity.description, crop.SizeOfPlantation
        FROM crop
        LEFT JOIN crop_activity ON crop.Id = crop_activity.crop_id
        WHERE crop.Id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $crop_id);
$stmt->execute();
$result = $stmt->get_result();

$crop_details = [];
while ($row = $result->fetch_assoc()) {
    $crop_details[] = $row;
}
?>


    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap">
<style>
    /* Your existing CSS styles */
    .chatbot-toggler1 {
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
    .chatbot-toggler1 {
        bottom: 260px;
    }
}
.show-chatbot .chatbot-toggler1 {
    transform: rotate(90deg);
}
.chatbot-toggler1 span {
    position: absolute;
    display: flex;
}
.chatbot-toggler1 span:first-child img {
    width: 30px;
}
.chatbot-toggler1 span:last-child img {
    width: 15px;
}
.chatbot-toggler1 span:last-child,
.show-chatbot .chatbot-toggler1 span:first-child  {
    opacity: 0;
}
.show-chatbot .chatbot-toggler1 span:last-child {
    opacity: 1;
}
.chatbot1 {
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
.show-chatbot .chatbot1 {
    opacity: 1;
    pointer-events: auto;
    transform: scale(1);
}
.chatbot1 header {
    padding: 16px 0;
    position: relative;
    color: #fff;
    background: #529cf1;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.chatbot1 header small, .chatbot1 header h2 {
    display: flex;
    align-items: center;
    margin-left: 20px;
}
.chatbot1 header small img {
    width: 10px;
    height: 10px;
    margin: 5px;
}
.chatbot1 header h2 {
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
    .chatbot-toggler1 {
        right: 10px;
        bottom: 460px;
    }
    .chatbot1 {
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
    .chatbot1 header span {
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
.thinking-emoji {
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

<button class="chatbot-toggler1">
    <span><img src="crops/chatbot.png" alt=""></span>
    <span><img src="https://img.icons8.com/?size=256&id=71200&format=png" alt=""></span>
</button>
<div class="chatbot1">
    <header>
        <h2>AgroNet's AI Powered Chatbot</h2>
        <small><img src="https://img.icons8.com/?size=256&id=JIlJqN3SJL07&format=png" alt="">Online</small>
        <span class="close-btn"></span>
    </header>
    <ul class="chatbox" id="chatbox">
        <div id="questionList">
            <ul>
                <?php if (!empty($crop_details)): ?>
                    <?php
                    $crop_name = htmlspecialchars($crop_details[0]['crop_name']);
                    $crop_type = htmlspecialchars($crop_details[0]['crop_type']);
                    $SizeOfPlantation = htmlspecialchars($crop_details[0]['SizeOfPlantation']);
                    $activities = array_map(function($detail) {
                        $date = new DateTime($detail['activity_date']);
                        $formatted_date = $date->format('F j, Y');
                        return $formatted_date . ": " . htmlspecialchars($detail['activity_type']) . " - " . htmlspecialchars($detail['description']);
                    }, $crop_details);
                    $activity_list = implode(", ", $activities);

                    // Fetch pest and disease details
                    $sql_pest_disease = "SELECT Name, Status FROM croppestdisease WHERE CropID = ?";
                    $stmt_pest_disease = $conn->prepare($sql_pest_disease);
                    $stmt_pest_disease->bind_param("i", $crop_id);
                    $stmt_pest_disease->execute();
                    $result_pest_disease = $stmt_pest_disease->get_result();
                    $pest_disease_list = [];
                    while ($row = $result_pest_disease->fetch_assoc()) {
                        $pest_disease_list[] = htmlspecialchars($row['Name']) . " (" . htmlspecialchars($row['Status']) . ")";
                    }
                    $pest_disease_list_str = implode(", ", $pest_disease_list);

                    // Fetch harvest details
                    $sql_harvest = "SELECT HarvestedDate, AmountOfHarvest FROM harvest WHERE CropId = ?";
                    $stmt_harvest = $conn->prepare($sql_harvest);
                    $stmt_harvest->bind_param("i", $crop_id);
                    $stmt_harvest->execute();
                    $result_harvest = $stmt_harvest->get_result();
                    $harvest_records = [];
                    while ($row = $result_harvest->fetch_assoc()) {
                        $date = new DateTime($row['HarvestedDate']);
                        $formatted_date = $date->format('F j, Y');
                        $harvest_records[] = $formatted_date . ": " . htmlspecialchars($row['AmountOfHarvest']) . " kg";
                    }
                    $harvest_records_str = implode(", ", $harvest_records);

                    // Fetch variety list
                    $sql_variety = "SELECT DISTINCT Type FROM crop WHERE FarmId = ?";
                    $stmt_variety = $conn->prepare($sql_variety);
                    $stmt_variety->bind_param("i", $crop_details[0]['farm_id']);
                    $stmt_variety->execute();
                    $result_variety = $stmt_variety->get_result();
                    $variety_list = [];
                    while ($row = $result_variety->fetch_assoc()) {
                        $variety_list[] = htmlspecialchars($row['Type']);
                    }
                    $variety_list_str = implode(", ", $variety_list);
                    ?>
                    <li class="question-item">How can I manage pests effectively for  <?php echo $crop_type; ?> <?php echo $crop_name; ?>?</li>
                    <li class="question-item">What are the best practices for watering  <?php echo $crop_type; ?> <?php echo $crop_name; ?>?</li>
                    <li class="question-item">
                        I have <?php echo $crop_name; ?> with a variety of <?php echo $crop_type; ?> and these are the activities I do for these crops: <?php echo $activity_list; ?>. Based on this, what do you recommend for me and what is the next activity I should do?
                    </li>
                    <li class="question-item">
                        I have planted <?php echo $crop_name; ?> with a variety of <?php echo $crop_type; ?>. Currently, these are the pests and diseases recorded: <?php echo $pest_disease_list_str; ?>. What steps should I take to maintain the health of my crops?
                    </li>
                    <li class="question-item">
                        On my farm, I have <?php echo $crop_name; ?> with a variety of <?php echo $crop_type; ?> planted on <?php echo $SizeOfPlantation; ?> hectares of land. The details of my past harvests are: <?php echo $harvest_records_str; ?>. What should I do to increase my yield in the next planting season?
                    </li>
                    <li class="question-item">
                        I have planted <?php echo $crop_name; ?> with different varieties: <?php echo $variety_list_str; ?>. Based on my past harvests and current conditions, which of these varieties is most suitable for my farm and why?
                    </li>
                    <li class="question-item">
                        Given that I have planted <?php echo $crop_name; ?> with a variety of <?php echo $crop_type; ?>, and considering the activities recorded: <?php echo $activity_list; ?>, what is the best time for the next fertilizer application?
                    </li>
                    <li class="question-item">
                        For my crop of <?php echo $crop_name; ?> (variety: <?php echo $crop_type; ?>), what are the recommended pest control measures based on the pests and diseases recorded: <?php echo $pest_disease_list_str; ?>?
                    </li>
                    <li class="question-item">
                        I have observed these activities on my <?php echo $crop_name; ?> (<?php echo $crop_type; ?>): <?php echo $activity_list; ?>. What specific practices should I follow to enhance soil fertility for better crop yield?
                    </li>
                    <li class="question-item">
                        Based on the harvest records: <?php echo $harvest_records_str; ?>, what are the key factors that could influence the yield of my <?php echo $crop_name; ?> with a variety of <?php echo $crop_type; ?>?
                    </li>
                    <li class="question-item">
                        I have planted <?php echo $crop_name; ?> on <?php echo $size_of_plantation; ?> hectares. How can I effectively manage water usage and irrigation for this crop?
                    </li>
                    <li class="question-item">
                        For my <?php echo $crop_name; ?> with different varieties: <?php echo $variety_list_str; ?>, which pest control methods are most effective and why?
                    </li>
                    <li class="question-item">
                        Given the pest and disease issues: <?php echo $pest_disease_list_str; ?>, what are the early signs I should look for to prevent further spread in my <?php echo $crop_name; ?> crops?
                    </li>
                    <li class="question-item">
                        What are the optimal weather conditions for planting <?php echo $crop_name; ?> with a variety of <?php echo $crop_type; ?>, and how can I protect the crop from adverse weather conditions?
                    </li>
                    <li class="question-item">
                        Based on my past harvests: <?php echo $harvest_records_str; ?>, what are the best practices for storing <?php echo $crop_name; ?> to maintain its quality?
                    </li>
                    <li class="question-item">
                        I am planning to expand my plantation of <?php echo $crop_name; ?> (variety: <?php echo $crop_type; ?>). What soil preparation steps should I take to ensure a successful planting season?
                    </li>
                    <li class="question-item">No crop details found for the given ID.</li>
                <?php else: ?>
                    <li class="question-item">No crop details found for the given ID.</li>
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
    var chatbotToggler = $('.chatbot-toggler1');
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
