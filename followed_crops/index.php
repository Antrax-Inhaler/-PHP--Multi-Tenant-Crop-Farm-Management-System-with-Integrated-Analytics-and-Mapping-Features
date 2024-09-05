<?php
$client_id = $_settings->userdata('id');
$query = $conn->query("SELECT ic.*, c.Id as CropId, c.Name as CropName, c.Type, c.PlannedPlantingDate, 
                              c.DatePlanted, c.SizeOfPlantation, c.Description, c.Picture1, 
                              c.Latitude, c.Longitude, v.shop_name as VendorName
                       FROM `interested_clients` ic 
                       JOIN `crop` c ON ic.crop_id = c.Id 
                       JOIN `vendor_list` v ON c.VendorId = v.id 
                       WHERE ic.client_id = '$client_id' AND c.delete_flag = 0 
                       ORDER BY ic.timestamp DESC");

?>

<div class="content py-3">
    <div class="container">
        <h2 class="text-center">My Followed Crops</h2>
        <div class="row">
            <?php while($row = $query->fetch_assoc()): ?>
            <div class="col-md-4 mb-3">
                <div class="card shadow">
                    <img src="<?= validate_image($row['Picture1']) ?>" class="card-img-top" alt="Crop Image">
                    <div class="card-body">
                        <h5 class="card-title"><?= $row['CropName'] ?></h5>
                        <p class="card-text">
                            <strong>Type:</strong> <?= $row['Type'] ?><br>
                            <strong>Vendor:</strong> <?= $row['VendorName'] ?><br>
                            <strong>Size:</strong> <?= $row['SizeOfPlantation'] ?> hectares<br>
                        </p>
                        <?php if ($row['status'] == 'approved'): ?>
                        <p class="text-success"><strong>Status:</strong> Approved</p>
                        <a href="./?page=crop_details&id=<?= $row['CropId'] ?>" class="btn btn-primary">View Crop Details</a>
                        <?php else: ?>
                        <p class="text-warning"><strong>Status:</strong> Waiting for Approval</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>