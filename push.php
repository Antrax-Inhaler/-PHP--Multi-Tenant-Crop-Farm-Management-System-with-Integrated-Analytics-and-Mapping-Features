<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Push Notification Example</title>
</head>
<body>
    <h1>Push Notification Example</h1>

    <script>
        // Check if the browser supports notifications
        if (!('Notification' in window)) {
            alert('This browser does not support desktop notifications.');
        } else {
            // Function to fetch order details
            function fetchOrderDetails() {
                return fetch('order.php')
                    .then(response => response.json())
                    .then(data => {
                        if (Object.keys(data).length === 0) {
                            throw new Error('No order found');
                        }
                        return data;
                    });
            }

            // Function to show notification
            function showNotification(order) {
                const title = 'Order Notification';
                const options = {
                    body: `Order ID: ${order.order_id}\nProduct: ${order.product_name}\nStatus: ${order.status}\nDate: ${order.date_updated}`,
                    icon: order.image_path,  // Using the product image as the icon
                    data: {
                        url: './?page=orders/my_orders'  // URL to redirect on click
                    }
                };
                const notification = new Notification(title, options);

                // Add click event to the notification
                notification.onclick = function(event) {
                    event.preventDefault();  // Prevent the default action (e.g., navigating to the clicked URL in a new tab)
                    window.location.href = notification.data.url;  // Redirect to the specified URL
                };
            }

            // Check if notification permissions have already been granted
            if (Notification.permission === 'granted') {
                // Fetch order details and show notification
                fetchOrderDetails().then(showNotification).catch(error => alert(error.message));
            } 
            // Otherwise, we need to ask the user for permission
            else if (Notification.permission !== 'denied' || Notification.permission === 'default') {
                Notification.requestPermission().then(function (permission) {
                    // If the user accepts, fetch order details and show notification
                    if (permission === 'granted') {
                        fetchOrderDetails().then(showNotification).catch(error => alert(error.message));
                    }
                });
            }
        }
        
        // Automatically fetch and show notification when the page loads
        window.onload = function() {
            if (Notification.permission === 'granted') {
                fetchOrderDetails().then(showNotification).catch(error => alert(error.message));
            } else {
                Notification.requestPermission().then(function (permission) {
                    if (permission === 'granted') {
                        fetchOrderDetails().then(showNotification).catch(error => alert(error.message));
                    }
                });
            }
        }
    </script>
</body>
</html>
