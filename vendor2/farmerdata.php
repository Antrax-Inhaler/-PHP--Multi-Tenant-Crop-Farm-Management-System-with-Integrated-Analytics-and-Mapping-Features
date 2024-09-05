<?php

require_once('./../config.php');

// Assuming you have already established a database connection

// Get vendor details
$vendor_query = "SELECT * FROM `vendor_list` WHERE id = '{$_settings->userdata('id')}'";
$vendor_result = $conn->query($vendor_query);
$vendor_data = $vendor_result->fetch_assoc();

// Get crop details
$crops_query = "SELECT * FROM `crop` WHERE VendorId = '{$_settings->userdata('id')}'";
$crops_result = $conn->query($crops_query);

// Get harvest details
$harvest_query = "SELECT * FROM `harvest` WHERE CropId IN (SELECT Id FROM `crop` WHERE VendorId = '{$_settings->userdata('id')}')";
$harvest_result = $conn->query($harvest_query);

// Get pest and disease information
$pest_disease_query = "SELECT * FROM `croppestdisease` WHERE CropID IN (SELECT Id FROM `crop` WHERE VendorId = '{$_settings->userdata('id')}')";
$pest_disease_result = $conn->query($pest_disease_query);
?><!DOCTYPE html>
<html>
<head>
    <title>Vendor Dashboard</title>
</head>
<body>
    <?php
        // Fetch vendor data
        $vendor_query = "SELECT * FROM `vendor_list` WHERE id = '{$_settings->userdata('id')}'";
        $vendor_result = $conn->query($vendor_query);
        $vendor_data = $vendor_result->fetch_assoc();
    ?>

    <h1>Vendor Information</h1>
    <p>The name of this user is <?php echo $vendor_data['shop_owner']; ?>, who has a shop named <?php echo $vendor_data['shop_name']; ?> and his contact is <?php echo $vendor_data['contact']; ?>.</p>

    <?php
        // Fetch crops data
        $crops_query = "SELECT * FROM `crop` WHERE VendorId = '{$_settings->userdata('id')}'";
        $crops_result = $conn->query($crops_query);
    ?>

    <h2>Crops Information</h2>
    <?php while($crop = $crops_result->fetch_assoc()): ?>
        <p>This is his crop with ID <?php echo $crop['Id']; ?> and the name of the crop is <?php echo $crop['Name']; ?>. Below are its details:</p>

        <ul>
            <li>Type: <?php echo $crop['Type']; ?></li>
            <li>Planned Planting Date: <?php echo $crop['PlannedPlantingDate']; ?></li>
            <li>Date Planted: <?php echo $crop['DatePlanted']; ?></li>
            <li>Size Of Plantation: <?php echo $crop['SizeOfPlantation']; ?></li>
            <li>Description: <?php echo $crop['Description']; ?></li>
            <!-- Add more details as needed -->
        </ul>

        <?php
            // Fetch disease information for this crop
            $disease_query = "SELECT * FROM `croppestdisease` WHERE CropID = '{$crop['Id']}'";
            $disease_result = $conn->query($disease_query);
        ?>

        <?php if($disease = $disease_result->fetch_assoc()): ?>
            <p>This crop has a disease, below is the information:</p>

            <ul>
                <li>Disease Name: <?php echo $disease['Name']; ?></li>
                <li>Area Affected: <?php echo $disease['SizeOfAreaAffected']; ?></li>
                <li>Status: <?php echo $disease['Status']; ?></li>
                <!-- Add more details as needed -->
            </ul>
        <?php endif; ?>

        <?php
            // Fetch harvest information for this crop
            $harvest_query = "SELECT * FROM `harvest` WHERE CropId = '{$crop['Id']}'";
            $harvest_result = $conn->query($harvest_query);
        ?>

        <?php if($harvest = $harvest_result->fetch_assoc()): ?>
            <p>This crop has harvest information:</p>

            <ul>
                <li>Harvested Date: <?php echo $harvest['HarvestedDate']; ?></li>
                <li>Amount: <?php echo $harvest['AmountOfHarvest']; ?></li>
                <li>Paid: <?php echo $harvest['Paid']; ?></li>
                <!-- Add more details as needed -->
            </ul>
        <?php endif; ?>
    <?php endwhile; ?>
</body>
</html>
