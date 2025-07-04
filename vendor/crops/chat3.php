<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap">
<style>
.chatbot-toggler {
            position: fixed;
            bottom: 30px;
            right: 11px;
            outline: none;
            border: none;
            height: 50px;
            width: 50px;
            display: flex;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
background: linear-gradient(to bottom right, #9CDC78, #74DCB0) !important;
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
            width: 40px;
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
            background: linear-gradient(to bottom right, #9CDC78, #74DCB0) !important;
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
            height: 430px;
            padding: 2px 20px 100px;
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
        #chat-input {
            display: none;
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
                right: 1tpx;
                bottom: 490px;
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
                padding: 2px 15px 100px;
            }
            .chat-input {
                padding: 5px 15px;
            }
            .chatbot header span {
                display: block;
            }
        }

        .user-container {
            display: flex;
            overflow-x: auto;
            overflow-y: hidden;
            border-bottom: 1px solid #ddd;
            width: 420px;
            white-space: nowrap;
        }

        .conversation-container {
            display: flex;
            flex-direction: column;
            max-height: 300px;  
            min-height: 300px;  
            overflow-x: auto;
            padding: 10px;
            width: 100%;
        }

        .user {
            text-align: center;
            margin: 10px;
            width: 80px;
            display: flex;
            flex-direction: column;
            height: 80px;
            align-items: center;
        }
        .conversation{
            max-width: 420px;
    display: flex;
    flex-direction: row;
    height: 80px;
    align-items: center;
    padding: 14px;
        }
        .user img, .conversation img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .user-name-container{
            overflow: hidden;
            width: 80px;
        }
        .user-name, .conversation-info .username {
            margin-top: 2px;
            font-size: 14px;
        }

        .conversation-info {
            display: flex;
            flex-direction: column;
            margin-left: 10px;
            width: 100%;
            position: relative;
        }

        .latest-message {
            font-size: 12px;
            color: #888;
        }

        .timestamp {
            font-size: 12px;
            color: #aaa;
            text-align: right;
        }
        #search{
            background-color: rgba(255, 255, 255, 0.7);
    border-radius: 20px;
    border: none;
    padding: 10px 20px;
    outline: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding-right: 90px;
    position: relative;
    width: 96%;
    margin: 5px 10px;
        }
        @media (max-width: 490px) {
            .conversation-container {
            max-height: 540px;  
          
        }

        }
        #back-btn{
            width: 50px;
            height: 50px;
            color: green;
        }
        .no-messages{
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: small;
            gap: 3px;
        }
        #no{
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin: 10px;
            width: 80px;
        }
        .unread {
    font-weight: bold;
    background: linear-gradient(to bottom right, rgba(156, 220, 120, 0.5), rgba(116, 220, 176, 0.5)) !important;
}

.badge {
        position: absolute;
        top: 0;
        left: 0;
        background-color: red;
        color: white;
        border-radius: 50%;
        padding: 3px 8px;
        font-size: 12px;
        font-weight: bold;
        display: none; /* Hidden by default */
    }
    .last-seen {
        font-size: 10px;
        color: #aaa;
    text-align: right;
    position: absolute;
        top: 0;
        right: 0;
}
</style>

<button class="chatbot-toggler">
    <span><img src="../uploads/messenger.png" alt=""></span>
    <span class="badge" id="unread-count" style="display: none;">0</span>
    <span><img src="https://img.icons8.com/?size=256&id=71200&format=png" alt=""></span>

</button>
<div class="chatbot">
    <header>
        <h2>Agronet Messenger</h2>
    </header>
    <button id="back-btn" style="display:none;">
    <i class="fa fa-arrow-left"></i>
</button>

    <input type="text" id="search" placeholder="Search users...">
    <div class="user-container" id="user-container">
        <!-- User list will be populated here by AJAX -->
    </div>
    <div class="conversation-container" id="conversation-container">
        <!-- Conversation list will be populated here by AJAX -->
    </div>
    <ul class="chatbox" id="chatbox">
        <!-- Chat messages will be appended here -->
    </ul>
    <div id="chat-input">
    <div class="chat-input">
        <textarea placeholder="Enter a message..." spellcheck="false" required id="messageInput"></textarea>
        <span id="sendBtn">
            <img src="https://img.icons8.com/?size=256&id=ZznWGhUzgWtS&format=png" alt="">
        </span>
    </div>
</div>
</div>

<audio id="notification-sound" src="../sounds/notification.wav" preload="auto"></audio>

<script>
    $(document).ready(function() {
        var selectedOtherId = null;
        var selectedOtherRole = null;
        var currentUserId = <?php echo json_encode($user_id); ?>;

            function updateUnreadCount() {
        $.ajax({
            url: 'crops/fetch_unread_count.php',
            method: 'GET',
            success: function(count) {
                if (count > 0) {
                    $('#unread-count').text(count).show();
                } else {
                    $('#unread-count').hide();
                }
            }
        });
    }


    $('.chatbot-toggler').click(function() {
        $('body').toggleClass('show-chatbot');
        var isChatbotVisible = $('body').hasClass('show-chatbot');
        $('#unread-count').toggle(!isChatbotVisible); // Hide badge if chatbot is visible
        $('#unread-count').hide();
        updateUnreadCount(); // Update unread count when toggling chatbot
    });

    $('#sendBtn').click(function() {
        var message = $('#messageInput').val().trim();
        if (message !== '' && selectedOtherId !== null && selectedOtherRole !== null) {
            sendMessage(message);
            $('#messageInput').val('');
        }
    });

    function sendMessage(message) {
        $.ajax({
            url: 'crops/send_message.php',
            method: 'POST',
            data: { 
                other_id: selectedOtherId, 
                other_role: selectedOtherRole, 
                message: message 
            },
            success: function(data) {
                $('#chatbox').append('<li class="chat outgoing"><h6>' + message + '</h6></li>');
                $('#chatbox').append(data); // Appends the response (incoming message)
                scrollToBottom(); // Scroll to the bottom after appending the message
                updateUnreadCount(); // Update unread count after sending a message
            }
        });
    }

    function fetchUsers(query = '') {
        $.ajax({
            url: 'crops/fetch_users.php',
            method: 'GET',
            data: { query: query },
            success: function(data) {
                $('#user-container').html(data);
            }
        });
    }

    function fetchConversations(query = '') {
    $.ajax({
        url: 'crops/fetch_conversations.php',
        method: 'GET',
        data: { query: query },
        success: function(data) {
            $('#conversation-container').html(data);

            // Add a class to highlight unread messages
            $('.conversation').each(function() {
                let isRead = $(this).data('is-read');
                let lastSenderId = $(this).data('last-sender-id');
                let lastSeen = $(this).data('last-seen'); // Get the last_seen time

                if (isRead == 0 && lastSenderId != currentUserId) {
                    $(this).addClass('unread');
                }

                // Display last seen time if the current user is the last sender
                if (lastSenderId == currentUserId && lastSeen) {
                    let timeAgo = timeSince(new Date(lastSeen));
                    $(this).find('.last-seen').text(`Seen ${timeAgo} ago`).show();
                } else {
                    $(this).find('.last-seen').hide();
                }
            });

            // Update unread count
            updateUnreadCount();
        }
    });
}

function timeSince(date) {
    var seconds = Math.floor((new Date() - date) / 1000);

    var interval = seconds / 31536000;
    if (interval > 1) {
        return Math.floor(interval) + " years";
    }
    interval = seconds / 2592000;
    if (interval > 1) {
        return Math.floor(interval) + " months";
    }
    interval = seconds / 86400;
    if (interval > 1) {
        return Math.floor(interval) + " days";
    }
    interval = seconds / 3600;
    if (interval > 1) {
        return Math.floor(interval) + " hours";
    }
    interval = seconds / 60;
    if (interval > 1) {
        return Math.floor(interval) + " minutes";
    }
    return Math.floor(seconds) + " seconds";
}


    function fetchMessages(otherId, otherRole) {
        $.ajax({
            url: 'crops/fetch_messages.php',
            method: 'GET',
            data: { other_id: otherId, other_role: otherRole },
            success: function(data) {
                selectedOtherId = otherId;
                selectedOtherRole = otherRole;
                $('#chatbox').html(data);
                $('#chatbox').show();
                $('#user-container').hide();
                $('#conversation-container').hide();
                $('#search').hide();
                $('#back-btn').show();
                $('#chat-input').show();
                scrollToBottom(); // Scroll to the bottom after fetching messages
            }
        });
    }
    
    $('#conversation-container').on('click', '.conversation', function() {
        let otherId = $(this).data('other-id');
        let otherRole = $(this).data('other-role');
        
        $.ajax({
            url: 'crops/mark_messages_read.php',
            method: 'POST',
            data: { other_id: otherId, other_role: otherRole },
            success: function() {
                fetchMessages(otherId, otherRole);
                fetchConversations(); // Refresh the conversation list
            }
        });
    });

    fetchUsers();
    fetchConversations();

    $('#search').on('keyup', function() {
        let query = $(this).val();
        fetchUsers(query);
        fetchConversations(query);
    });

    $('#user-container').on('click', '.user', function() {
        let otherId = $(this).data('other-id');
        let otherRole = $(this).data('other-role');
        fetchMessages(otherId, otherRole);
    });

    $('#back-btn').click(function() {
        $('#chatbox').hide();
        $('#user-container').show();
        $('#conversation-container').show();
        $('#search').show();
        $('#back-btn').hide();
        $('#chat-input').hide();
        selectedOtherId = null;
        selectedOtherRole = null;
    });

    $('#chatbox').hide();

    function scrollToBottom() {
        $('#chatbox').scrollTop($('#chatbox')[0].scrollHeight);
    }
});

</script>
