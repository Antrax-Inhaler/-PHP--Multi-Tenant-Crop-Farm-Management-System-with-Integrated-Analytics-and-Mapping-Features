<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Add this in your existing head section -->
    <style>
        /* Modal styles */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgb(0,0,0); 
            background-color: rgba(0,0,0,0.4); 
            padding-top: 60px; 
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 80%; 
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

    </style>
</head>
<body>
    <!-- Existing HTML content -->

    <!-- The Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="modal-body"></div>
        </div>
    </div>

    <!-- Existing Scripts and new Scripts below -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Function to open the modal and fetch data
        function openModal(cropId) {
            $.ajax({
                url: 'result/crop_info.php',
                type: 'GET',
                data: { id: cropId },
                success: function(response) {
                    $('#modal-body').html(response);
                    $('#myModal').show();
                },
                error: function(error) {
                    alert('Error fetching crop details');
                }
            });
        }

        // When the user clicks on <span> (x), close the modal
        $(document).ready(function() {
            $('.close').click(function() {
                $('#myModal').hide();
            });

            // Optionally, you can close the modal when clicking outside of the modal-content
            $(window).click(function(event) {
                if (event.target.id == 'myModal') {
                    $('#myModal').hide();
                }
            });

            // Example usage: Open modal when the page loads with a specific crop ID
            // You can replace '1' with the dynamic crop ID
            const urlParams = new URLSearchParams(window.location.search);
            const cropId = urlParams.get('id');
            if (cropId) {
                openModal(cropId);
            }
        });
    </script>
</body>
</html>