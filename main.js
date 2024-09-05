// Function to fetch and display the latest order details or show a notification if no data
function displayLatestOrder() {
    fetch('fetch_latest_order.php')
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.id) {
            const latestOrderDetails = document.getElementById('latestOrderDetails');
            latestOrderDetails.innerHTML = `
                <table border="1">
                    <tr>
                        <th>Order Code</th>
                        <th>Client ID</th>
                        <th>Total Amount</th>
                        <th>Delivery Address</th>
                    </tr>
                    <tr>
                        <td>${data.code}</td>
                        <td>${data.client_id}</td>
                        <td>$${data.total_amount}</td>
                        <td>${data.delivery_address}</td>
                    </tr>
                </table>
            `;
        } else {
            showNoDataNotification();
        }
    })
    .catch(error => {
        console.error('Error fetching latest order:', error);
        showNoDataNotification();
    });
}

// Function to show a notification when no data is available
function showNoDataNotification() {
    const title = 'No Latest Order Data';
    const options = {
        body: 'There is currently no latest order data available.',
        icon: 'icon.png',
        tag: 'no-data-notification'
    };
    Notification.requestPermission().then(function(permission) {
        if (permission === 'granted') {
            new Notification(title, options);
        } else {
            console.error('Notification permission denied');
        }
    });
}

// Function to request notification for latest order
function sendNotificationForLatestOrder() {
    // Code to send notification
}

// Add event listener to the button
document.getElementById('notifyButton').addEventListener('click', sendNotificationForLatestOrder);

// Call the function to display latest order when the page loads
window.addEventListener('load', displayLatestOrder);
