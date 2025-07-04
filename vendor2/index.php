<?php require_once('../config.php'); 

// Fetch all vendors
$vendors = $conn->query("SELECT id FROM vendor_list WHERE delete_flag = 0");

while($vendor = $vendors->fetch_assoc()) {
    $vendor_id = $vendor['id'];

    // Fetch all distinct months from order_list for the vendor using date_updated
    $months_query = $conn->query("SELECT DISTINCT DATE_FORMAT(date_updated, '%Y-%m') as month FROM order_list WHERE vendor_id = '{$vendor_id}'");

    while($month_row = $months_query->fetch_assoc()) {
        $month = $month_row['month'];

        // Calculate total sales for the month using date_updated
        $sales_query = $conn->query("SELECT SUM(total_amount) as total_sales FROM order_list WHERE vendor_id = '{$vendor_id}' AND DATE_FORMAT(date_updated, '%Y-%m') = '{$month}'")->fetch_assoc();
        $total_sales = $sales_query['total_sales'] ? $sales_query['total_sales'] : 0;

        // Fetch commission rate for the vendor's user
        $vendor_user_query = $conn->query("SELECT user_id FROM vendor_list WHERE id = '{$vendor_id}'")->fetch_assoc();
        $user_id = $vendor_user_query['user_id'];
        $commission_rate_query = $conn->query("SELECT commission FROM users WHERE id = '{$user_id}'")->fetch_assoc();
        $commission_rate = $commission_rate_query['commission'];
        $total_commission = $total_sales * $commission_rate;

        // Check if entry already exists
        $existing_entry = $conn->query("SELECT id FROM vendor_commissions WHERE vendor_id = '{$vendor_id}' AND month = '{$month}'")->fetch_assoc();

        if ($existing_entry) {
            // Update existing entry
            $conn->query("UPDATE vendor_commissions SET total_sales = '{$total_sales}', total_commission = '{$total_commission}' WHERE id = '{$existing_entry['id']}'");
        } else {
            // Insert new entry
            $conn->query("INSERT INTO vendor_commissions (vendor_id, month, total_sales, total_commission) VALUES ('{$vendor_id}', '{$month}', '{$total_sales}', '{$total_commission}')");
        }
    }
}
?>
<?php
require_once('../config.php');

// Fetch orders where sms = 0
$orders_query = $conn->query("SELECT id, client_id, vendor_id, status FROM order_list WHERE sms = 0");

while ($order = $orders_query->fetch_assoc()) {
    $order_id = $order['id'];
    $client_id = $order['client_id'];
    $vendor_id = $order['vendor_id'];
    $status = $order['status'];

    // Fetch client's phone number
    $client_query = $conn->query("SELECT contact FROM client_list WHERE id = '{$client_id}'");
    $client = $client_query->fetch_assoc();
    $phoneNumber = $client['contact'];

    // Prepare message based on status
    switch ($status) {
        case 0:
            $message = "Hi! This is AgroNet. Your order with ID: {$order_id} is pending. Please wait for the seller to process the order.";
            break;
        case 1:
            $message = "Hi! This is AgroNet. Your order with ID: {$order_id} has been confirmed.";
            break;
        case 2:
            $message = "Hi! This is AgroNet. Your order with ID: {$order_id} has been packed and is ready for delivery.";
            break;
        case 3:
            $message = "Hi! This is AgroNet. Your order with ID: {$order_id} is out for delivery. The amount of your order is {$order['total_amount']}.";
            break;
        case 4:
            $message = "Hi! This is AgroNet. Your order with ID: {$order_id} has been delivered.";
            break;
        case 5:
            $message = "Hi! This is AgroNet. Your order with ID: {$order_id} has been cancelled.";
            break;
        default:
            $message = "Hi! This is AgroNet. Your order with ID: {$order_id} has been processed.";
            break;
    }

    // Send SMS
    $apiKey = ""; // Replace with your Semaphore API key
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
        // SMS sent successfully
        // Update the sms column in the order_list table
        $updateQuery = $conn->query("UPDATE order_list SET sms = 1 WHERE id = '{$order_id}'");
        if (!$updateQuery) {
            // Handle update failure
            // Log error or take necessary action
        }
    }
}
?>


<?php require_once('../config.php'); ?>
<!DOCTYPE html>
<html lang="en" style="height: 100%;">

<?php require_once('inc/header.php') ?>
<link rel="stylesheet" href="../assets/css/styles.css">
<?php require_once('inc/header2.php') ?>

<body>

    <!-- Table layout -->
    <table style="width: 100%; height: 100%;">
        <!-- Top Bar -->
        <tr>
        <?php require_once('inc/topBarNav.php') ?>
        </tr>
        <tr>
            <td>
            </td>
        </tr>
        <!-- Content -->
        <tr>
            <td style="vertical-align: top;">
                <!-- Content Wrapper. Contains page content -->
                <section class="content">
                    <div class="container-fluid">
                        <?php 
                            $page = isset($_GET['page']) ? $_GET['page'] : 'home';  
                            if($_settings->chk_flashdata('success')):
                        ?>
                        <script>
                            alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
                        </script>
                        <?php endif;?>
                        <?php 
                            if(!file_exists($page.".php") && !is_dir($page)){
                                include '404.html';
                            } else {
                                if(is_dir($page))
                                    include $page.'/index.php';
                                else
                                    include $page.'.php';
                            }
                        ?>
                    </div>
                </section>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        function requestNotificationPermission() {
                            if (Notification.permission === "granted") {
                                displayWelcomeNotification();
                            } else if (Notification.permission !== "denied") {
                                Notification.requestPermission().then(permission => {
                                    if (permission === "granted") {
                                        displayWelcomeNotification();
                                    }
                                });
                            }
                        }

                        function displayWelcomeNotification() {
                            if (Notification.permission === "granted") {
                                new Notification("Welcome!", {
                                    body: "Thank you for allowing notifications.",
                                    icon: 'path/to/icon.png' // Optional: Add an icon
                                });
                            }
                        }

                        // Trigger the notification permission request
                        requestNotificationPermission();
                    });
                </script>

            
                <?php require_once('notification.php') ?>

                <!-- /.content -->
  
                <div class="modal fade" id="uni_modal" role='dialog'>
                    <div class="modal-dialog modal-md modal-dialog-centered rounded-0" role="document">
                        <div class="modal-content rounded-0">
                            <div class="modal-header rounded-0">
                                <h5 class="modal-title"></h5>
                            </div>
                            <div class="modal-body rounded-0">
                            </div>
                            <div class="modal-footer rounded-0">
                                <button type="button" class="btn btn-sm btn-flat btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
                                <button type="button" class="btn btn-sm btn-flat btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="uni_modal_second" role='dialog'>
                    <div class="modal-dialog modal-md modal-dialog-centered rounded-0" role="document">
                        <div class="modal-content rounded-0">
                            <div class="modal-header rounded-0">
                                <h5 class="modal-title"></h5>
                            </div>
                            <div class="modal-body rounded-0">
                            </div>
                            <div class="modal-footer rounded-0">
                                <button type="button" class="btn btn-sm btn-flat btn-primary" id='submit' onclick="$('#uni_modal_second form').submit()">Save</button>
                                <button type="button" class="btn btn-sm btn-flat btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="uni_modal_right" role='dialog'>
                    <div class="modal-dialog modal-full-height  modal-md" role="document">
                        <div class="modal-content">
                            <div class="modal-header rounded-0">
                                <h5 class="modal-title"></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span class="fa fa-arrow-right"></span>
                                </button>
                            </div>
                            <div class="modal-body rounded-0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="viewer_modal" role='dialog'>
                    <div class="modal-dialog modal-md" role="document">
                        <div class="modal-content rounded-0">
                            <button type="button" class="btn-close" data-dismiss="modal"><span class="fa fa-times"></span></button>
                            <img src="" alt="">
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="confirm_modal" role='dialog'>
                    <div class="modal-dialog modal-md modal-dialog-centered rounded-0" role="document">
                        <div class="modal-content">
                            <div class="modal-header rounded-0">
                                <h5 class="modal-title">Confirmation</h5>
                            </div>
                            <div class="modal-body rounded-0">
                                <div id="delete_content"></div>
                            </div>
                            <div class="modal-footer rounded-0">
                                <button type="button" class="btn btn-sm btn-flat btn-primary" id='confirm' onclick="">Continue</button>
                                <button type="button" class="btn btn-sm btn-flat btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <?php require_once('inc/footer.php') ?>
</body>
</html>
<?php
include 'crops/chat3.php';
?>