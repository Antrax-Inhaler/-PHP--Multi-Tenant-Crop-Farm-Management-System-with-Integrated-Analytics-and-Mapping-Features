<?php
require_once('config.php');


$client_id = $_SESSION['userdata']['id'];

// Fetch orders for the current user
$qry = $conn->query("SELECT * FROM `order_list` WHERE `client_id` = $client_id ORDER BY `date_updated` DESC");

$orders = [];
if ($qry->num_rows > 0) {
    while ($row = $qry->fetch_assoc()) {
        $orders[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">My Orders</h1>
        <?php if (count($orders) > 0): ?>
            <div class="card">
                <div class="card-header">
                    <h2>Order List</h2>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Status</th>
                                <th>Date Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?= htmlspecialchars($order['id']) ?></td>
                                    <td><?= htmlspecialchars($order['status']) ?></td>
                                    <td><?= htmlspecialchars($order['date_updated']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No orders found.</div>
        <?php endif; ?>
    </div>
</body>
</html>
