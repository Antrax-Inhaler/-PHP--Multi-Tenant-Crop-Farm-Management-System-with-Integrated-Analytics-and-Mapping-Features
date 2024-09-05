<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Notification System</title>
    <style>
        #notification-container {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 300px;
        }
        .notification {
            background-color: #44c767;
            border-radius: 5px;
            color: white;
            margin-bottom: 10px;
            padding: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div id="notification-container"></div>

    <script>
        function fetchOrders() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'fetch_orders.php', true);
            xhr.onload = function() {
                if (this.status === 200) {
                    const orders = JSON.parse(this.responseText);
                    const container = document.getElementById('notification-container');
                    orders.forEach(order => {
                        const div = document.createElement('div');
                        div.className = 'notification';
                        div.innerText = `Order ID: ${order.id}\nUpdated At: ${order.date_updated}`;
                        container.appendChild(div);

                        // Automatically remove the notification after 5 seconds
                        setTimeout(() => {
                            container.removeChild(div);
                        }, 5000);
                    });
                }
            };
            xhr.send();
        }

        setInterval(fetchOrders, 10000); // Fetch orders every 10 seconds

        // Fetch orders immediately on page load
        fetchOrders();
    </script>
</body>
</html>
