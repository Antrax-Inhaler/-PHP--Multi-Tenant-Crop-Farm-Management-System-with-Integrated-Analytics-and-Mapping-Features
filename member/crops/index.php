<?php if($_settings->chk_flashdata('success')): ?>
<script>
    alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<style>
    .scrollable-container {
        overflow-x: auto;
        white-space: nowrap;
    }
    .info-card {
        display: inline-block;
        width: 300px; /* Adjust width as needed */
        margin-right: 10px; /* Adjust margin as needed */
        vertical-align: top;
    }
    .crop_info{
        border: solid 1px grey;
        border-radius: 30px;
        padding: 10px;
        margin-bottom: 20px;
        background-color: lightgreen;
    }
    .recommendation-card {
        border: solid 1px #17a2b8; /* Bootstrap info color */
        border-radius: 10px;
        padding: 10px;
        margin-top: 20px;
    }
    i{
        color: green;
    }
    .custom-img {
        width: 110%; /* Adjust the width as needed */
        height: auto; /* Maintain aspect ratio */
    }
    .full-width {
        width: 100vw;
        padding-left: 15px; /* Offset for Bootstrap container padding */
        padding-right: 15px; /* Offset for Bootstrap container padding */
    }
    /* Custom styles */
    .recommendation-container {
        position: absolute;
        top: 0;
        right: 0;
        width: 300px; /* Adjust width as needed */
        margin-top: 10px; /* Adjust margin as needed */
    }
    .activity-container {
            max-height: 400px; /* Adjust the max-height as needed */
            overflow-y: auto;
            padding-right: 15px; /* Offset for scrollbar */
        }
</style>

<div class="container-fluid mt-4">
    <div class="row">
        <!-- Crop List -->
        <div class="col-md-3 order-md-first">
            <div class="list-group" id="cropList">
                <?php
                $crops_query = $conn->query("SELECT * FROM crop WHERE delete_flag = 0");
                if ($crops_query->num_rows > 0) {
                    while ($crop = $crops_query->fetch_assoc()) :
                ?>
                <a href="#" class="list-group-item list-group-item-action" data-cropid="<?php echo $crop['Id']; ?>">
                    <i class="fas fa-seedling"></i>
                    <?php echo $crop['Id']; ?> . <?php echo $crop['Name']; ?>
                </a>
                <?php endwhile; ?>
                <?php } else { ?>
                <p>No crops selected. Your crop information will appear here.</p>
                <?php } ?>
            </div>
            <button class="btn btn-flat btn-primary mt-3" id="add_crop"><span class="fas fa-plus"></span>  Add Crop</button>
        </div>

        <!-- Detailed Information Container -->
        <div class="col-md-9 full-width"> <!-- Add the 'full-width' class here -->
            <?php
            if ($crops_query->num_rows > 0) {
                $crops_query->data_seek(0); // Reset query pointer
                while ($crop = $crops_query->fetch_assoc()) :
                    $datePlanted = new DateTime($crop['DatePlanted']);
                    $currentDate = new DateTime();
                    $age = $datePlanted->diff($currentDate)->format('%y years, %m months, %d days');
            ?>
            <div class="crop-details" id="cropDetails_<?php echo $crop['Id']; ?>" style="display:none;">
                <div class="crop_info bg-light p-3">
                    <div class="row">
                        <div class="col-md-6">
                            <h3><i class="fas fa-seedling"></i> <?php echo $crop['Name']; ?></h3>
                            <div class="row mt-3">
                                <!-- Crop Images -->
                                <div class="col-md-4">
                                    <img src="../<?php echo $crop['Picture1']; ?>" class="img-fluid custom-img" alt="Crop Image 1">
                                </div>
                                <div class="col-md-4">
                                    <img src="../<?php echo $crop['Picture2']; ?>" class="img-fluid custom-img" alt="Crop Image 2">
                                </div>
                                <div class="col-md-4">
                                    <img src="../<?php echo $crop['Picture3']; ?>" class="img-fluid custom-img" alt="Crop Image 3">
                                </div>
                            </div>
                            <!-- Crop Details -->
                            <p><i class="fas fa-leaf"></i> Type: <?php echo $crop['Type']; ?></p>
                            <p><i class="far fa-calendar-alt"></i> Planned Planting Date: <?php echo $crop['PlannedPlantingDate']; ?></p>
                            <p><i class="far fa-calendar-check"></i> Date Planted: <?php echo $crop['DatePlanted']; ?></p>
                            <p><i class="far fa-clock"></i> Age of Crop: <?php echo $age; ?></p>
                            <p><i class="fas fa-ruler"></i> Size of Plantation: <?php echo $crop['SizeOfPlantation']; ?></p>
                            <p><i class="fas fa-info-circle"></i> Description: <?php echo $crop['Description']; ?></p>
                            <div class="row">
                                <div class="col-md-6">
                                    <button class="btn btn-primary edit_data" href="javascript:void(0)" data-id="<?php echo $crop['Id']; ?>">
                                        <span class="fa fa-edit"></span> Edit Crop
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-danger delete_data" data-id="<?php echo $crop['Id']; ?>">
                                        <span class="fa fa-trash"></span> Delete Crop
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Crop Recommendations -->
                        <div class="col-md-6">
                            <div class="recommendation-container">
                                <div class="recommendation-card mt-3">
                                    <h5>Crop Activity Recommendation</h5>
                                    <?php
                                    $cropName = $crop['Name'];
                                    $cropType = $crop['Type'];
                
                                    $recommendation_query = $conn->query("SELECT * FROM cropactivityrecommendation WHERE CropType = '$cropName' AND Variety = '$cropType'");
                                    if ($recommendation_query->num_rows > 0) {
                                        while ($recommendation = $recommendation_query->fetch_assoc()) :
                                            // Check if the crop age and AgeInDays are within a range (e.g., 10 days difference)
                                            $ageDifference = abs($age - $recommendation['AgeInDays']);
                                            if ($ageDifference <= 10) {
                                                echo "<p>{$recommendation['Explanation']}</p>";
                                                echo "<p>Recommended Activity: {$recommendation['RecommendedActivity']}</p>";
                                            }
                                        endwhile;
                                    } else {
                                        echo "<p>Your crop activity recommendation will appear here.</p>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                        <div class="activity-container">
                            <h5>Activities</h5>
                            <?php
                        $activities_query = $conn->query("SELECT * FROM crop_activity WHERE crop_id = {$crop['Id']}");
                        if ($activities_query->num_rows > 0) {
                            while ($activity = $activities_query->fetch_assoc()) {
                        ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $activity['activity_date']; ?></h5>
                                <p class="card-text"><?php echo $activity['activity_description']; ?></p>
                                <!-- Add buttons for updating and deleting -->
                                <button class="btn btn-primary btn-sm">Update</button>
                                <button class="btn btn-danger btn-sm">Delete</button>
                            </div>
                        </div>
                        <?php
                            }
                        } else {
                            echo '<p>No crop activities found.</p>';
                        }
                        ?>
                        </div>
                        <!-- Add Crop Activity Button -->
                        <div class="text-center mt-3">
                            <button class="btn btn-success" data-toggle="modal" data-target="#addActivityModal">Add Crop Activity</button>
                        </div>
                    </div>
                    </div>

                    <!-- Harvesting Information -->
                    <h5 class="mt-3">Harvesting Information</h5>
                    <div class="scrollable-container">
                        <?php
                        $harvest_query = $conn->query("SELECT * FROM harvest WHERE CropId = {$crop['Id']}");
                        if ($harvest_query->num_rows > 0) {
                            while ($harvest = $harvest_query->fetch_assoc()) :
                        ?>
                        <div class="info-card card m-2 p-2">
                            <p class="card-text">Harvested Date: <?php echo $harvest['HarvestedDate']; ?></p>
                            <p class="card-text">Amount Harvested: <?php echo $harvest['AmountOfHarvest']; ?></p>
                            <!-- Add buttons for updating and deleting -->
                            <button class="btn btn-primary btn-sm">Update</button>
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </div>
                        <?php endwhile; ?>
                        <?php } ?>
                        <div class="info-card card m-2 p-2">
                            <!-- Add button at the end of scroll for Harvest Information -->
                            <button class="btn btn-success btn-sm mt-2">Add Harvest</button>
                        </div>
                    </div>

                    <!-- Pest and Disease Information -->
                    <h5 class="mt-3">Pest and Disease Information</h5>
                    <div class="scrollable-container">
                        <?php
                        $pest_query = $conn->query("SELECT * FROM croppestdisease WHERE CropID = {$crop['Id']}");
                        if ($pest_query->num_rows > 0) {
                            while ($pest = $pest_query->fetch_assoc()) :
                        ?>
                        <div class="info-card card m-2 p-2">
                            <p class="card-text">Name: <?php echo $pest['Name']; ?></p>
                            <p class="card-text">Size of Affected Area: <?php echo $pest['SizeOfAreaAffected']; ?></p>
                            <p class="card-text">Status: <?php echo $pest['Status']; ?></p>
                            <!-- Add buttons for updating and deleting -->
                            <button class="btn btn-primary btn-sm">Update</button>
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </div>
                        <?php endwhile; ?>
                        <?php } ?>
                        <!-- Add button at the end of scroll for Pest and Disease Information -->
                        <button class="btn btn-success btn-sm mt-2">Add Pest/Disease</button>
                    </div>

                    <!-- Crop Activity Recommendation -->
                </div>
            </div>
            <?php endwhile; ?>
            <?php } ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Function to show detailed information when a crop is clicked
    $('#cropList').on('click', '.list-group-item', function(e) {
        e.preventDefault();
        var cropId = $(this).data('cropid');
        
        // Hide all crop details
        $('.crop-details').hide();
        
        // Show the details of the selected crop
        $('#cropDetails_' + cropId).show();
    });
    $('.delete_data').click(function() {
        var cropId = $(this).data('id');
        _conf("Are you sure to delete this crop permanently?", "delete_crop", [cropId]);
    });

    // Function to handle adding a new crop
    $('#add_crop').click(function(){
        uni_modal('Add New Crop', 'member/crops/manage_crops.php', 'large');
    });

    // Function to handle editing crop information
    $('.edit_data').click(function(){
        var cropId = $(this).data('id');
        uni_modal('Update Crop', 'member/crops/manage_crops.php?id=' + cropId, 'large');
    });

    // Function to handle adding harvest information
    $('#btnAddHarvest').on('click', function() {
        // Redirect or open a modal for adding harvest information
        // Example: window.location.href = 'add_harvest.php';
    });

    // Function to handle adding pest or disease information
    $('#btnAddPestDisease').on('click', function() {
        // Redirect or open a modal for adding pest or disease information
        // Example: window.location.href = 'add_pest_disease.php';
    });

    // Other functions...
});
function delete_crop(cropId) {
    start_loader();
    $.ajax({
        url: _base_url_ + "classes/Master.php?f=delete_crop",
        method: "POST",
        data: { id: cropId },
        dataType: "json",
        error: function(err) {
            console.log(err);
            alert_toast("An error occurred.", 'error');
            end_loader();
        },
        success: function(resp) {
            if (typeof resp == 'object' && resp.status == 'success') {
                location.reload();
            } else {
                alert_toast("An error occurred.", 'error');
                end_loader();
            }
        }
    });
	}
</script>

