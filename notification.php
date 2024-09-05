<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Test</title>
</head>
<body>
    <h1>Notification Test</h1>
    <button id="notifyBtn">Enable Notifications</button>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function requestNotificationPermission() {
                if (Notification.permission === "granted") {
                    displayWelcomeNotification();
                } else if (Notification.permission !== "denied") {
                    Notification.requestPermission().then(permission => {
                        if (permission === "granted") {
                            displayWelcomeNotification();
                        } else {
                            alert("Notification permission denied.");
                        }
                    });
                } else {
                    alert("Notification permission denied.");
                }
            }

            function displayWelcomeNotification() {
                if (Notification.permission === "granted") {
                    new Notification("Welcome!", {
                        body: "Thank you for allowing notifications.",
                        icon: 'https://via.placeholder.com/150' // Sample icon
                    });
                }
            }

            document.getElementById("notifyBtn").addEventListener("click", function() {
                requestNotificationPermission();
            });
        });
    </script>
</body>
</html>
