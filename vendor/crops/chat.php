
    <style>
        .user-container {
            display: flex;
            flex-wrap: wrap;
        }
        .user {
            text-align: center;
            margin: 10px;
            width: 80px;
            display: flex;
            flex-direction: column;
            height: 80px;
            overflow: hidden;
            align-items: center;
        }
        .user img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .user-name {
            margin-top: 2px;
            font-size: 14px;
        }
    </style>
    <input type="text" id="search" placeholder="Search users...">
    <div class="user-container" id="user-list">
        <!-- User list will be populated here by AJAX -->
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            function fetchUsers(query = '') {
                $.ajax({
                    url: 'crops/fetch_users.php',
                    method: 'GET',
                    data: {query: query},
                    success: function(data) {
                        $('#user-list').html(data);
                    }
                });
            }

            fetchUsers();

            $('#search').on('keyup', function() {
                let query = $(this).val();
                fetchUsers(query);
            });
        });
    </script>