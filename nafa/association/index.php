<div class="content py-3">
<a href="?page=user/list" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>Manage Barangay Association</a>
    <div class="container-fluid">
        <div class="row">
            <?php 
            // Fetch all users from the database
            $users = $conn->query("SELECT * FROM `users`");
            while($user = $users->fetch_assoc()):
                $userId = $user['id'];

                // Fetch commission
                $commission = $user['commission'];

                // Fetch number of vendors associated with the user
                $vendorCount = $conn->query("SELECT COUNT(*) as vendor_count FROM `vendor_list` WHERE `user_id` = '$userId'")->fetch_assoc()['vendor_count'];

                // Fetch total crops associated with the user via vendors
                $totalCrops = $conn->query("SELECT COUNT(*) as total_crops FROM `product_list` WHERE `vendor_id` IN (SELECT id FROM `vendor_list` WHERE `user_id` = '$userId')")->fetch_assoc()['total_crops'];

                // Fetch total sales of the vendors associated with the user
                $totalSales = $conn->query("SELECT SUM(total_amount) as total_sales FROM `order_list` WHERE `vendor_id` IN (SELECT id FROM `vendor_list` WHERE `user_id` = '$userId')")->fetch_assoc()['total_sales'];

                // Fetch total products associated with the user
                $totalProducts = $conn->query("SELECT COUNT(*) as total_products FROM `product_list` WHERE `vendor_id` IN (SELECT id FROM `vendor_list` WHERE `user_id` = '$userId') AND `delete_flag` = 0")->fetch_assoc()['total_products'];
            ?>
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <img src="<?php echo validate_image($user['avatar']) ?>" alt="User Avatar" class="img-thumbnail rounded-circle" style="width: 50px; height: 50px;">
                            <div class="ms-3">
                                <h1 class="card-title mb-0"><?= $user['firstname'] . ' ' . $user['lastname'] ?></h1>
                                <p class="card-text">@<?= $user['username'] ?></p>
                            </div>
                        </div>
                        <hr>
                        <p><strong>Last Login:</strong> <?= !empty($user['last_login']) ? date("Y-m-d H:i", strtotime($user['last_login'])) : 'Never' ?></p>

                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-gradient-success elevation-1"><i class="fas fa-percentage"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Commission</span>
                                <span class="info-box-number text-right h4">₱<?= number_format($commission, 2) ?></span>
                            </div>
                        </div>

                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-gradient-primary elevation-1"><i class="fas fa-store"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Vendors</span>
                                <span class="info-box-number text-right h4"><?= $vendorCount ?></span>
                            </div>
                        </div>

                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-gradient-info elevation-1"><i class="fas fa-seedling"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Crops</span>
                                <span class="info-box-number text-right h4"><?= $totalCrops ?></span>
                            </div>
                        </div>

                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-gradient-warning elevation-1"><i class="fas fa-dollar-sign"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Sales</span>
                                <span class="info-box-number text-right h4">₱<?= number_format($totalSales, 2) ?></span>
                            </div>
                        </div>

                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-gradient-secondary elevation-1"><i class="fas fa-boxes"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Products</span>
                                <span class="info-box-number text-right h4"><?= $totalProducts ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<script>
    $(function(){
        // You can add any JavaScript/jQuery here if needed
    })
</script>
