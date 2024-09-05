<?php
$vendors = $conn->query("SELECT id, contact FROM vendor_list WHERE delete_flag = 0");

while ($vendor = $vendors->fetch_assoc()) {
    $vendor_id = $vendor['id'];
    $phoneNumber = $vendor['contact'];

    // Fetch notifications where is_sent = 0
    $notifications_query = $conn->query("SELECT id, farm_id, crop_id, pest_disease_id, time_created
                                        FROM near_pest_notification 
                                        WHERE vendor_id = '{$vendor_id}' AND is_sent = 0  LIMIT 1");

    while ($notification = $notifications_query->fetch_assoc()) {
        $pest_disease_id = $notification['pest_disease_id'];
        $farm_id = $notification['farm_id'];
        $crop_id = $notification['crop_id'];

        // Fetch pest or disease name
        $pest_disease_query = $conn->query("SELECT Name FROM croppestdisease WHERE Id = '{$pest_disease_id}'");
        $pest_disease = $pest_disease_query->fetch_assoc();
        $pest_disease_name = $pest_disease['Name'];

        // Fetch farm name
        $farm_query = $conn->query("SELECT Name FROM farm WHERE Id = '{$farm_id}'");
        $farm = $farm_query->fetch_assoc();
        $farm_name = $farm['Name'];

        // Fetch crop name
        $crop_query = $conn->query("SELECT Name FROM crop WHERE Id = '{$crop_id}'");
        $crop = $crop_query->fetch_assoc();
        $crop_name = $crop['Name'];

        // Prepare SMS message
        $message = "Hi! This is AgroNet. There has been a report of {$pest_disease_name} at a nearby farm: '{$farm_name}' where '{$crop_name}' is planted. For more details, visit: https://agronetnafa.website/vendor/?page=map/farm-pestanddisease";

        // Send SMS
        $apiKey = "1$1++0074+3n0w+0$4ychUR1-cHUr1'x"; // Replace with your Semaphore API key
        $parameters = [
            'apikey' => $apiKey,
            'number' =>  $phoneNumber,
            'message' => $message,
            'sendername' => 'Semaphore'
        ];

        // Initialize cURL
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, 'http://api.semaphore.co/api/sms');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL certificate verification (not recommended for production)

        // Execute cURL request
        $output = curl_exec($ch);

        // Close cURL resource
        curl_close($ch);

        // Check if the SMS was sent successfully
        if ($output !== false) {
            // Update the is_sent column in the near_pest_notification table
            $updateQuery = $conn->query("UPDATE near_pest_notification SET is_sent = 1 WHERE id = '{$notification['id']}'");
            if (!$updateQuery) {
                // Handle update failure
                // Log error or take necessary action
            }
        }
    }
}
?>
