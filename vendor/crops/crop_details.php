<style>
    ::-webkit-scrollbar {
    width: 12px; /* Width of the scrollbar */
    height: 12px; /* Height of the scrollbar (for horizontal scrollbars) */
}

::-webkit-scrollbar-track {
    background: #f1f1f1; /* Background of the track */
    border-radius: 10px; /* Rounded corners for the track */
}

::-webkit-scrollbar-thumb {
    background-color: #888; /* Scrollbar color */
    border-radius: 10px; /* Rounded corners for the thumb */
    border: 2px solid #f1f1f1; /* Adds space around the thumb */
}

::-webkit-scrollbar-thumb:hover {
    background-color: #555; /* Color when hovered */
}
</style>
<?php
// Fetch crop details based on crop ID
$crop_id = $_GET['id'] ?? null;

if (!$crop_id) {
    die("Crop ID is required.");
}

// Fetch crop details
$crop_query = "
    SELECT 
        crop.Id as cropId,
        crop.Name as crop_name, 
        crop.Type as crop_type,
        crop.PlannedPlantingDate as planned_planting_date,
        crop.DatePlanted as date_planted,
        crop.SizeofPlantation as size_of_plantation,
        crop.Description as crop_description,
        crop.Picture1 as crop_image1,
        crop.Picture2 as crop_image2,
        crop.Picture3 as crop_image3
    FROM crop 
    WHERE crop.Id = $crop_id AND crop.is_deleted = 0
";

$crop_result = $conn->query($crop_query);
$crop = $crop_result->fetch_assoc();

if (!$crop) {
    die("Crop not found.");
}

// Fetch pest and disease details for the crop
$pest_query = "
    SELECT
        Id, 
        Name, 
        SizeOfAreaAffected, 
        Status, Image1
    FROM croppestdisease 
    WHERE CropID = $crop_id
";
$pest_result = $conn->query($pest_query);
$pests = [];
while ($row = $pest_result->fetch_assoc()) {
    $pests[] = $row;
}

// Fetch harvested information for the crop
$harvest_query = "
    SELECT harvest.Id as HarvestId,
        harvest.HarvestedDate as harvest_date, 
        harvest.AmountOfHarvest as harvest_quantity
    FROM harvest 
    WHERE CropID = $crop_id
";
$harvest_result = $conn->query($harvest_query);
$harvests = [];
while ($row = $harvest_result->fetch_assoc()) {
    $harvests[] = $row;
}

// Fetch pests and diseases that have not been reported yet
$not_been_reported = "
    SELECT 
        cp.Id, 
        cp.Name, 
        cp.SizeOfAreaAffected, 
        cp.Status
    FROM croppestdisease cp
    LEFT JOIN pestanddiseasereport pr
    ON cp.Id = pr.pestordisease_id
    WHERE cp.CropID = $crop_id
    AND pr.pestordisease_id IS NULL
";
$not_reported_result = $conn->query($not_been_reported);
$not_reported_pests = [];
while ($row = $not_reported_result->fetch_assoc()) {
    $not_reported_pests[] = $row;
}

$searchQuery = "How to grow {$crop['crop_name']} {$crop['crop_type']}";
$image_paths = [];
if (!empty($crop['crop_image1'])) {
    $image_paths[] = "./" . htmlspecialchars($crop['crop_image1']);
}
if (!empty($crop['crop_image2'])) {
    $image_paths[] = "./" . htmlspecialchars($crop['crop_image2']);
}
if (!empty($crop['crop_image3'])) {
    $image_paths[] = "./" . htmlspecialchars($crop['crop_image3']);
}


?>

 <style>
    /* Existing CSS styles here */
    .card {
        border: none;
        border-radius: 8px;
        padding: 13px;
        margin-bottom: 20px;
    }
    .card h2, .card h3 {
        margin-top: 0;
    }
    .crop-details-card {
        display: flex;
        gap: 20px;
        justify-content: flex-start;
        border-radius: 10px;

    }
    .card-img-container {
        flex: 1;
        min-width: 200px;
        margin-right: 20px;
        width: 500px;
        height: 500px;
    }
    .card-img-container img {
        width: 100%;
        height: auto;
        border-radius: 8px;
    }
    .crop-details {
        flex: 2;
        padding-left: 20px;
        display: flex;
        justify-content: space-around;
    }
    .crop-details p {
        margin: 0;
    }
    .scrollable-container {
        max-height: 270px;
        overflow-y: auto;
        margin-top: 16px;
        position: relative;
        z-index: 1;
    }
    .scrollable-container2 {
        max-height: 320px;
        overflow-y: auto;
        margin-top: 16px;
        position: relative;
        z-index: 1;
    }
    .info-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between; /* Adjust alignment */
    }
    .info-card p {
        margin: 0;
    }
    .info-card img {
        max-width: 100px;
        height: auto;
        margin-right: 15px;
        border-radius: 8px;
    }
    .farm_card {
        cursor: pointer;
        transition: transform 0.3s;
        width: 100%; /* Adjust width */
    }
    .farm_card:hover {
        transform: scale(1.05);
    }
    .pd_card {
        width: 250px;
        height: 250px;
        border-radius: 10px;
        border: solid 1px #ccc;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.3s, box-shadow 0.3s;
        background-color: white;
        cursor: pointer;
        text-align: left;
        position: relative;
        z-index: 2;
    }
    .pd_card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
    .card_img_container {
        width: 100%;
        height: 130px;
    }
    .pd_image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .farm_list {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start;
        gap: 20px;
    }
    .crop_name {
        height: 70px;
        overflow-y: auto;
    }
    /* Adjustments for sidebar layout */
    .sidebar-container {
        display: flex;
        gap: 20px;
    }
    .sidebar {
        width: 500px; /* Adjust width as needed */
        position: sticky;
        top: 20px; /* Adjust top spacing */
        height: calc(100vh - 40px); /* Adjust height to fit screen */
        overflow-y: auto;
    }
    .main-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .dropdown-menu {
    min-width: auto;
    right: 0;
    left: auto;
}

.dropdown-item.edit_crop:hover {
    background-color: #007bff;
    color: #fff;
}

.dropdown-item.delete_crop:hover {
    background-color: #dc3545;
    color: #fff;
}
.dropleft{
    top: 0;
    right: 0;
    position: absolute;
}
.color-strip {
    background-color: #2ddc9a; /* Primary blue color */
    height: 5px;
    width: 100%;
    border-radius: 10px;
    top: 0;
    position: absolute;
    left: 0;
}
.farm_image, .crop_image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
.topNavCrop{
    display: flex;
    justify-content: space-between;
}
    .icon-sidebar {
        display: flex;
        justify-content: flex-start;
        gap: 10px;
    }
    .icon-item {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 25%;
        font-size: 1.5rem;
        border: 2px solid transparent;
        cursor: pointer;
        transition: transform 0.3s, border-color 0.3s, color 0.3s;
    }
    .icon-item[data-target="pd-scroll"] {
        color: #e74c3c; /* Red */
        border-color: #e74c3c;
    }
    .icon-item[data-target="harvest-scroll"] {
        color: #27ae60; /* Green */
        border-color: #27ae60;
    }
    .icon-item[data-target="activity-scroll"] {
        color: #3498db; /* Blue */
        border-color: #3498db;
    }
    .icon-item[data-target="report-scroll"] {
        color: #f39c12; /* Orange */
        border-color: #f39c12;
    }
    .icon-item[data-target="help-scroll"] {
        color: #8e44ad; /* Purple */
        border-color: #8e44ad;
    }
    .icon-item:hover {
        transform: scale(1.1);
        border-color: currentColor;
        color: currentColor;
    }
@media (max-width: 768px) {
    .sidebar-container {
        flex-direction: column;
        align-items: flex-start;
    }

    .sidebar-toggle {
        display: block; /* Show sidebar toggle button */
    }

    .icon-sidebar {
        display: flex; /* Show icon sidebar on small screens */
    }

    .main-content {
        margin-top: 20px; /* Adjust main content margin */
    }
    .farm_card{
        width: 100%;
    }
    /* .card{
        max-width: 400px  ;
    } */
    .pd_card{
        width: 100%;
    }
    .off-scroll{
        overflow-y: initial;
    }
    .crop-details{
        flex-direction: column;
    }
    #videos-container {
        gap: 0px;
        width: 100%;

    }
    .info-card{
        flex-direction: column;
    }
    .sidebar{
        width: 100%;
    }
    .icon-item {
        width: 25px;
        height: 25px;
        font-size: .8rem;
        border: 2px solid transparent;
    }
    .crop-name{
        font-size: 2.2rem;
    }


}
.tooltip-inner {
    max-width: 300px !important;
    padding: 0.25rem 0.5rem !important;
    color: black !important;
    text-align: center !important;
    background-color: #fff !important;
    border-radius: 0.25rem !important;
    box-shadow: #000 !important;
    text-align: left !important;
    z-index: 99999999;
    box-shadow: 0 0 10px hsla(0, 0%, 0%, 0.1);

}
.info{
    color: #007bff;
}
.pd_card {
    position: relative;
}

.ai-button {
    position: absolute;
    bottom: 10px;
    right: 10px;
}

.ai-btn {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    font-size: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
}
.modal2 {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: white;
    padding: 20px;
    border-radius: 5px;
    width: 80%;
    max-width: 600px;
}

.ai-response h6 {
    margin: 0;
    font-size: 16px;
}
.response-container{
    max-width: 600px;
    max-height: 500px;
    overflow-y: auto;
}
.card-header {
    position: relative;
}

.ai-btn2 {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 10;
}


</style>

<div class="sidebar-container">
 

    <div class="main-content">
    <div class="card crop-details-card">
            <div class="dropdown dropleft">
        <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-ellipsis-v"></i>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item edit_crop" href="#" data-id="<?= $crop_id ?>">Edit Crop</a>
            <a class="dropdown-item delete_crop" href="#" data-id="<?= $crop_id ?>">Delete Crop</a>
        </div>
    </div>
    <div class="topNavCrop">
        <div style="display: flex;" >
        <h1 class="crop-name" ><?= htmlspecialchars($crop['crop_name']) ?></h1>
        <i class="fas fa-info-circle info" data-toggle="tooltip" data-placement="top" title="Ang paglagay ng data sa crops ay mahalaga upang masubaybayan ang progreso ng iyong mga pananim, matukoy ang tamang panahon ng pagtatanim at pag-aani, pati na rin ang pag-specify ng variety at laki ng plantation na kinakailangan para sa mapping. Huwag kalimutang i-update ang status ng iyong pananim, mula sa active, inactive, not productive, hanggang sa end of lifespan."></i>
        </h1>
        </div>
  
<div class="icon-sidebar">
    
    <button class="icon-item" data-target="pd-scroll"><i class="fas fa-bug"></i></button>
    <button class="icon-item" data-target="harvest-scroll"><i class="fas fa-tractor"></i></button>
    <button class="icon-item" data-target="activity-scroll"><i class="fas fa-running"></i></button>
    <button class="icon-item" data-target="report-scroll"><i class="fas fa-file-alt"></i></button>
    <button class="icon-item" data-target="help-scroll"><i class="fas fa-question-circle"></i></button>
    <div></div>
</div>



</div>

    <div class="carousel" id="product-carousel">
        <div class="carousel-inner">
            <?php foreach ($image_paths as $index => $path) : ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                    <img src="<?= validate_image($path) ?>" class="d-block carousel-image" alt="Product Image">
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" id="carousel-prev">
            <span class="carousel-control-icon">&lt;</span>
        </button>
        <button class="carousel-control-next" type="button" id="carousel-next">
            <span class="carousel-control-icon">&gt;</span>
        </button>
    </div>
    <div class="crop-details">
    <p><i class="fas fa-seedling"></i><strong> Name:</strong> <?= htmlspecialchars($crop['crop_name']) ?></p>
    <p><i class="fas fa-leaf"></i><strong> Type:</strong> <?= htmlspecialchars($crop['crop_type']) ?></p>
    <p><i class="fas fa-calendar-alt"></i><strong> Planned Planting Date:</strong> <?= htmlspecialchars($crop['planned_planting_date']) ?></p>
    <p><i class="fas fa-calendar-check"></i><strong> Date Planted:</strong> <?= htmlspecialchars($crop['date_planted']) ?></p>
    <p><i class="fas fa-tree"></i><strong> Size of Plantation:</strong> <?= htmlspecialchars($crop['size_of_plantation']) ?> hectares</p>
    <p><i class="fas fa-info-circle"></i><strong> Description:</strong> <?= htmlspecialchars($crop['crop_description']) ?></p>
    <button id="generate_qr_btn" class="" style="text-align: center; align-items: center; display: flex; flex-direction: column; border: solid 1px; border-radius: 10px;" data-toggle="modal" data-target="#qrModal"> <ion-icon name="qr-code-outline"></ion-icon>QR Code</button>

</div>
</div>
<script>
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
        <div id="pd-scroll" class="card pest-disease-card">
        <div class="color-strip"  style="background-color:#dc3545"></div>
    <div style="display: flex;">
    <h4>Pest and Disease</h4>
    <i class="fas fa-info-circle info" data-toggle="tooltip" data-placement="top" title="Ang pag-record ng pest at disease data ay mahalaga upang masubaybayan ang kalusugan ng iyong pananim at mapigilan ang pagkalat ng sakit. Sa pamamagitan ng tamang pag-alam sa lawak ng apektadong lugar, maaari mong mas maayos na maipatupad ang mga hakbang na makakatulong sa pagprotekta sa iyong tanim. Sa pamamagitan ng paglagay ng impormasyon tungkol sa pest o disease, makakalikha ang Artificial Intelligence ng mga posibleng solusyon para mamanage ito. Manonotify din ang mga kalapit na farm upang maiwasan ang pagkalat ng naturang pest o disease. Maaari mo rin itong ireport sa iyong association para sa mas masusing pagsusuri kung kinakailangan."></i>

    </div>
   
    <p>Pest and Disease Count: <?= htmlspecialchars(count($pests)) ?></p>
    <div class="scrollable-container farm_list">
    <?php foreach ($pests as $pest): ?>
    <div class="pd_card position-relative" data-pest-id="<?= htmlspecialchars($pest['Id']) ?>">
        <div class="card_img_container">
        <img class="crop_image" src="../<?= htmlspecialchars($pest['Image1'] ?: 'uploads/alternative-pest.jpg') ?>" alt="<?= htmlspecialchars($pest['Image1'] ?: 'Alternative Image Description') ?>">
        </div>
        <div style="padding: 10px;">
        <p><i class="fas fa-bug"></i><strong> Name:</strong> <?= htmlspecialchars($pest['Name']) ?></p>
<p><i class="fas fa-map-marker-alt"></i><strong> Size of Affected Area:</strong> <?= htmlspecialchars($pest['SizeOfAreaAffected']) ?> hectares</p>
<p><i class="fas fa-tasks"></i><strong> Status:</strong> <?= htmlspecialchars($pest['Status']) ?></p>

        </div>
        <div class="dropdown dropleft top-right-dropdown">
            <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton_<?= htmlspecialchars($pest['Id']) ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-ellipsis-v"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton_<?= htmlspecialchars($pest['Id']) ?>">
                <a class="dropdown-item edit_pd" href="#" data-pest-id="<?= htmlspecialchars($pest['Id']) ?>">Edit</a>
                <a class="dropdown-item delete_croppestanddisease" href="#" data-pd-id="<?= htmlspecialchars($pest['Id']) ?>">Delete</a>
                <?php if ($not_been_reported): ?>
                    <a class="dropdown-item report_pd" href="#" data-pdr-id="<?= htmlspecialchars($pest['Id']) ?>">Report</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="ai-button">
            <!-- Remove input field -->
            <button class="btn btn-primary rounded-circle ai-btn" data-pest-id="<?= htmlspecialchars($pest['Id']) ?>">AI</button>
        </div>
    </div>
<?php endforeach; ?>

        <button id="add_pd" class="btn btn-success btn-sm mt-2">Add Pest/Disease</button>
    </div>
</div>
<script>
$(document).ready(function() {
    function fetchPestDiseaseData(pestDiseaseId) {
        $.ajax({
            url: 'crops/fetch_pest_disease_data.php',
            method: 'GET',
            dataType: 'json',
            data: { pest_disease_id: pestDiseaseId },
            success: function(response) {
                if (response.success) {
                    let data = response.data;
                    let generatedMessage = `I am currently managing a crop of ${data.cropName} (${data.cropType}), which is ${data.cropAge} days old, covering an area of ${data.SizeOfPlantation} hectares, located at (${data.Location}) (Philippines). 
                    The current status of the crop is '${data.cropStatus}'. 
                    However, there is a concern regarding a pest/disease identified as '${data.pestName}', which is affecting an area of ${data.affectedArea} hectares.
                    The pest/disease status is '${data.pestStatus}'. 
                    Can you recommend a management plan for this situation?(Make your grammar simple for Filipino Farmers, speak FIlipino if you can.`;

                    // Automatically trigger AI processing with generated message
                    submitToAI(pestDiseaseId, generatedMessage);
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Error fetching data');
            }
        });
    }

    function submitToAI(pestDiseaseId, generatedMessage) {
        if (!generatedMessage) {
            alert('No message generated.');
            return;
        }

        // Show loading indicator in the modal
        $('#response').html('<div class="loading"><span>Analyzing...</span></div>');
        $('#currentPestId').val(pestDiseaseId); // Store the current pest ID
        openModal();

        $.ajax({
    url: 'crops/ai.php',
    method: 'POST',
    dataType: 'json',
    data: { message: generatedMessage },
    success: function(response) {
        let content = response.content
            .replace(/\*\*(.*?)\*\*/g, '<b>$1</b>') // Handle bold text
            .replace(/(?<!\d)(\d+)\.\s/g, '<br>$1. ') // Add line break before numbered steps
            .replace(/\n/g, '<br>'); // Convert newlines to <br>

        $('#response').html('<h6>' + content + '</h6>');
    },
    error: function() {
        $('#response').html('<h6>Oops! Something went wrong.</h6>');
    }
});

    }

    $(document).on('click', '.ai-btn', function() {
        let pestDiseaseId = $(this).data('pest-id');
        fetchPestDiseaseData(pestDiseaseId);
    });

    $('#takeNoteBtn').click(function() {
        let pestDiseaseId = $('#currentPestId').val();
        let aiResponse = $('#response').text();

        if (!aiResponse) {
            alert('No response to save.');
            return;
        }

        // Send the AI response to the server to save as a note
        $.ajax({
            url: 'crops/save_note.php',
            method: 'POST',
            dataType: 'json',
            data: { pest_id: pestDiseaseId, note: aiResponse },
            success: function(response) {
                if (response.success) {
                    alert('Note saved successfully!');
                    $('#aiResponseModal').hide();
                } else {
                    alert('Failed to save the note: ' + response.message);
                }
            },
            error: function() {
                alert('Error saving note.');
            }
        });
    });

    function openModal() {
        var modal = document.getElementById("aiResponseModal");
        modal.style.display = "flex";
    }

    var modal = document.getElementById("aiResponseModal");
    var closeButton = document.getElementsByClassName("close")[0];

    closeButton.onclick = function() {
        modal.style.display = "none";
    };

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };
});


</script>
<!-- Modal for AI Response -->
<div id="aiResponseModal" class="modal2">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="response-container">
        <div id="response"></div>
        </div>
        <input type="hidden" id="currentPestId" value="">
        <button id="takeNoteBtn" class="btn btn-secondary">Take a Note</button>
    </div>
</div>
        <div id="harvest-scroll" class="card harvest-card">
        <div  class="color-strip"  style="background-color: gold"></div>
            <div style="display: flex;">
            <h3>Harvest Information</h3>
            <i class="fas fa-info-circle info" data-toggle="tooltip" data-placement="top" title="Ang paglalagay ng tamang impormasyon tungkol sa ani ay mahalaga upang masubaybayan ang dami ng ani at ang tamang petsa ng pag-aani. Sa pamamagitan ng pagtatala ng harvest data, mas madali mong makikita ang trend ng iyong ani, makakagawa ng tamang plano sa pagbebenta, at mapapakinabangan ang impormasyon para sa pagpapabuti ng iyong mga pananim. Ang maagang paglalagay ng harvesting schedule ay nagbibigay ng pagkakataon na makita agad ng mga buyers ang iyong produkto, na maaaring humantong sa mas mabilis na pagkakaroon ng potensyal na buyers."></i>
            </div>
            
            <button id="add_harvest" class="btn btn-success btn-sm mt-2">Add Harvest</button>
            <button id="add_harvest_sched" class="btn btn-success btn-sm mt-2">Add Harvest Schedule</button>

            <div class="scrollable-container">
                <?php if (!empty($harvests)): ?>
                    <?php foreach ($harvests as $harvest): ?>
                        <?php foreach ($harvests as $harvest): ?>
    <div class="info-card position-relative">
    <p><i class="fas fa-calendar-alt"></i> <strong>Harvested Date:</strong> <?= htmlspecialchars($harvest['harvest_date']) ?></p>
<p><i class="fas fa-shopping-basket"></i> <strong>Amount Harvested:</strong> <?= htmlspecialchars($harvest['harvest_quantity']) ?></p>

        
        <div class="dropdown dropleft top-right-dropdown">
            <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton_<?= htmlspecialchars($harvest['HarvestId']) ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-ellipsis-v"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton_<?= htmlspecialchars($harvest['HarvestId']) ?>">
                <a class="dropdown-item edit_harvest" href="#" data-harvest-id="<?= htmlspecialchars($harvest['HarvestId']) ?>">Edit</a>
                <a class="dropdown-item delete_harvest" href="#" data-harvest-id="<?= htmlspecialchars($harvest['HarvestId']) ?>">Delete</a>
            </div>
        </div>
    </div>
<?php endforeach; ?>

                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No harvest information available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="sidebar">
    <div id="activity-scroll" class="card crop-activity-card">
    <div class="color-strip" style="background-color: #3498db;"></div>

    <div class="card-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h4>Crop Activity</h4>
            <div>
                <i class="fas fa-info-circle info ml-2" data-toggle="tooltip" data-placement="top" 
                    title="Ang paglagay ng crop activity data ay mahalaga upang masubaybayan ang mga aktibidad na isinasagawa sa iyong tanim tulad ng pagtatanim, pagdidilig, paglalagay ng pataba, at iba pa. Ito ay nakakatulong upang makita ang mga pattern ng pag-aalaga ng pananim at makagawa ng mga tamang hakbang upang mapabuti ang ani. Magagamit din ito para sa pagsasaayos ng scheduling ng mga farm tasks. Palagiang paglalagay ng data ay mahalaga upang makakuha ng mas accurate na impormasyon at mungkahi mula sa Artificial Intelligence na makakatulong sa pamamahala ng iyong farm."></i>
            </div>
            <button class="btn btn-primary ai-btn-activities" data-crop-id="<?= htmlspecialchars($crop_id) ?>">AI</button>
        </div>
        <button id="add_activity" class="btn btn-success btn-sm mt-2">Add Activity</button>
    </div>
    <div class="card-body scrollable-container off-scroll" style="height: 400px; overflow-y: auto; padding: 7px;">
        <?php
        // Fetch crop activity data for the current vendor's crop
        $crop_activity_query = "
            SELECT 
                ca.id,
                ca.activity_date,
                ca.activity_type,
                ca.description
            FROM crop_activity ca
            WHERE ca.crop_id = $crop_id
        ";
        $crop_activity_result = $conn->query($crop_activity_query);

        if ($crop_activity_result->num_rows > 0) {
            while ($activity = $crop_activity_result->fetch_assoc()) {
                $activity_date = date("F d, Y", strtotime($activity['activity_date'])); // Format date
                ?>
                <div class="card">
                    <div class="activity-item position-relative">
                        <p><strong><?= $activity_date ?>:</strong> <?= htmlspecialchars($activity['activity_type']) ?></p>
                        <p><?= htmlspecialchars($activity['description']) ?></p>
                        <div class="dropdown dropleft top-right-dropdown">
                            <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton_<?= htmlspecialchars($activity['id']) ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton_<?= htmlspecialchars($activity['id']) ?>">
                                <a class="dropdown-item edit-activity" href="#" data-activity-id="<?= htmlspecialchars($activity['id']) ?>">Edit</a>
                                <a class="dropdown-item delete_activity" href="#" data-activity-id="<?= htmlspecialchars($activity['id']) ?>">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p>No crop activities recorded yet.</p>";
        }
        ?>
    </div>
</div>
<!-- Modal for AI Insights Response -->
<div id="aiResponseModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div id="aiInsightResponse"></div>
        <input type="hidden" id="currentCropId" value="">
        <button id="saveNoteBtn" class="btn btn-secondary">Take a Note</button>
    </div>
</div>

<script>
$(document).ready(function() {
    function fetchCropData(cropId) {
        $.ajax({
            url: 'crops/fetch_crop_activities.php',
            method: 'GET',
            dataType: 'json',
            data: { crop_id: cropId },
            success: function(response) {
                if (response.success) {
                    let data = response.data;
                    let activities = data.activities.map(activity => 
                        `On ${activity.activityDate}, ${activity.activityType}: ${activity.activityDescription}`
                    ).join('<br>');

                    let generatedMessage = `I am currently managing a crop of ${data.cropName} (${data.cropType}), which is ${data.cropAge} days old, covering an area of ${data.SizeOfPlantation} hectares, located at (${data.Location}). 
                    The current status of the crop is '${data.cropStatus}'.<br><br>
                    Crop activities:<br>${activities}<br><br>
                    What are the next steps I should take to ensure the best outcome for this crop?`;

                    // Show the generated message in the modal before sending to AI
                    $('#aiInsightResponse').html('<div class="message-preview"><h5>Generated Message:</h5><p>' + generatedMessage.replace(/\n/g, '<br>') + '</p></div>');
                    $('#currentCropId').val(cropId); // Store the current crop ID
                    openModal();

                    // Automatically trigger AI processing with generated message
                    submitToAI(cropId, generatedMessage);
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Error fetching data');
            }
        });
    }

    function submitToAI(cropId, generatedMessage) {
        if (!generatedMessage) {
            alert('No message generated.');
            return;
        }

        // Show loading indicator in the modal
        $('#aiInsightResponse').append('<div class="loading"><span>Analyzing...</span></div>');

        $.ajax({
            url: 'crops/ai.php',
            method: 'POST',
            dataType: 'json',
            data: { message: generatedMessage },
            success: function(response) {
                let content = response.content
                    .replace(/\*\*(.*?)\*\*/g, '<b>$1</b>') // Handle bold text
                    .replace(/(?<!\d)(\d+)\.\s/g, '<br>$1. ') // Add line break before numbered steps
                    .replace(/\n/g, '<br>'); // Convert newlines to <br>

                $('#aiInsightResponse').html('<h6>' + content + '</h6>');
            },
            error: function() {
                $('#aiInsightResponse').html('<h6>Oops! Something went wrong.</h6>');
            }
        });
    }

    function openModal() {
        var modal = document.getElementById("aiResponseModal");
        modal.style.display = "flex";
    }

    var modal = document.getElementById("aiResponseModal");
    var closeButton = document.getElementsByClassName("close")[0];

    closeButton.onclick = function() {
        modal.style.display = "none";
    };

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };

    // Event handler for AI button click
    $(document).on('click', '.ai-btn-activities', function() {
        let cropId = $(this).data('crop-id'); // Read crop ID from data attribute
        fetchCropData(cropId);
    });
});

</script>




        <div id="report-scroll" class="card crop-report-card">
        <div class="color-strip"  style="background-color: orange"></div>

    <div class="card-header">
                <div style="display: flex;" >
                <h5>Pest and Disease Reports</h5>
        <i class="fas fa-info-circle info ml-2" data-toggle="tooltip" data-placement="top" 
   title="Ang seksyon ng Pest and Disease Reports ay nagpapakita ng mga nireport na pest or disease sa association. Mahalaga na patuloy na i-update ang impormasyon ng mga report na ito upang malaman kung ito ba ay nabisita na, naresolba, o nangangailangan pa ng aksyon. Sa pamamagitan ng regular na pag-update, makakatulong ito sa iyong association na mas mabilis na makapagbigay ng nararapat na tulong at sa Artificial Intelligence upang makapagbigay ng mas tumpak na mungkahi para maiwasan ang pagkalat ng pest o disease."></i>
                </div>
        
    </div>
    <div class="card-body scrollable-container" style="height: 400px; overflow-y: auto; padding: 7px;">
        <?php
        // Fetch pest and disease report data for the current crop
        $pest_disease_report_query = "
        SELECT
            pr.id as report_id,
            pr.pestordisease_id,
            pr.created_at,
            cp.Name as pest_name,
            pr.description,
            pr.status
        FROM pestanddiseasereport pr
        JOIN croppestdisease cp ON pr.pestordisease_id = cp.Id
        WHERE cp.CropID = $crop_id
        ";
        $pest_disease_report_result = $conn->query($pest_disease_report_query);

        if ($pest_disease_report_result->num_rows > 0) {
            while ($report = $pest_disease_report_result->fetch_assoc()) {
                $report_date = date("F d, Y", strtotime($report['created_at'])); // Format date
                ?>
                <div class="card" >
                <div class="activity-item">
                <p><strong><?= $report_date ?>:</strong> <i class="fas fa-bug"></i> <?= htmlspecialchars($report['pest_name']) ?></p>
<p><i class="fas fa-info-circle"></i> Description: <?= htmlspecialchars($report['description']) ?></p>
<p><i class="fas fa-check-circle"></i> Status: <?= htmlspecialchars($report['status'] == 0 ? 'Pending' : 'Resolved') ?></p>

                    <div class="action-buttons">
                        <button class="btn btn-sm btn-primary edit-report" data-report-id="<?= htmlspecialchars($report['report_id']) ?>">Update</button>
                        <button class="btn btn-sm btn-danger delete_report" data-report-id="<?= htmlspecialchars($report['report_id']) ?>">Delete</button>
                    </div>
                </div>
                </div>
                <?php
            }
        } else {
            echo "<p>No report recorded yet.</p>";
        }
        ?>
    </div>
</div>
<div id="report-scroll" class="card crop-report-card">
    <div class="color-strip" style="background-color: orange"></div>

    <div class="card-header">
        <div style="display: flex;" >
        <h5>Client Interest and Messages</h5>
        <i class="fas fa-info-circle info ml-2" data-toggle="tooltip" data-placement="top" 
   title="Sa seksyon na ito, makikita mo ang mga intersadong cliente o customer na nagmula sa farm map sa e-commerce platform. Dito rin makikita ang mga mensaheng iniwan ng mga cliente para sa iyo. Mahalaga na regular mong suriin ang mga interes ng kliyente at mensahe upang makita ang kanilang feedback o alamin ang kanilang mga tanong tungkol sa iyong crops. Maaari mong aprobahan ang kanilang access upang makita nila ang mga detalyadong impormasyon ng iyong pananim, o maaari mong gawing eksklusibo ang impormasyon ng crops para sa mga piling cliente lamang."></i>

        </div>

    </div>
    
    <div class="card-body scrollable-container" style="height: 400px; overflow-y: auto; padding: 7px;">
        <?php
        // Fetch interested clients and their messages for the current crop
        $client_interest_query = "
        SELECT
            ic.id as interest_id,
            ic.client_id,
            ic.message,
            ic.status,
            c.firstname,
            c.lastname,
            ic.timestamp
        FROM interested_clients ic
        INNER JOIN client_list c ON ic.client_id = c.id
        WHERE ic.crop_id = $crop_id
        ORDER BY ic.timestamp DESC
        ";
        $client_interest_result = $conn->query($client_interest_query);

        if ($client_interest_result->num_rows > 0) {
            while ($interest = $client_interest_result->fetch_assoc()) {
                $interest_id = $interest['interest_id']; // Ensure this variable is correctly set
                $client_name = htmlspecialchars($interest['firstname'] . ' ' . $interest['lastname']);
                $message = htmlspecialchars($interest['message']);
                $status = htmlspecialchars($interest['status']);
                $timestamp = date("F d, Y", strtotime($interest['timestamp'])); // Format date
                ?>
                <div class="card">
                    <div class="activity-item">
                        <p><strong><?= $timestamp ?>:</strong> <i class="fas fa-user"></i> <?= $client_name ?></p>
                        <p><i class="fas fa-envelope"></i> Message: <?= $message ?></p>
                        <p><i class="fas fa-check-circle"></i> Status: <?= $status == 'pending' ? 'Pending' : ($status == 'approved' ? 'Approved' : 'Denied') ?></p>

                        <div class="action-buttons">
                        <button class="btn btn-sm btn-primary view-btn" data-interest-id="<?= $interest['interest_id'] ?>">View</button>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p>No client interest recorded yet.</p>";
        }
        ?>
    </div>
</div>    </div>
</div>
<script>
$(document).ready(function() {
    // Approve Button Click Handler
    $('.approve-btn').click(function(e) {
        e.preventDefault();
        var interestId = $(this).data('interest-id');
        updateInterestStatus(interestId, 'approved');
    });

    // Deny Button Click Handler
    $('.deny-btn').click(function(e) {
        e.preventDefault();
        var interestId = $(this).data('interest-id');
        updateInterestStatus(interestId, 'denied');
    });
    $('.view-btn').click(function(e) {
        e.preventDefault();
        var interestId = $(this).data('interest-id');
        uni_modal('View Client Interest', 'crops/view_client_interest.php?id=' + interestId, 'large');
    });
    // Function to Update Interest Status
    function updateInterestStatus(interestId, status) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=update_client_interest_status",
            method: "POST",
            data: { interest_id: interestId, status: status },
            dataType: "json",
            error: function(err) {
                console.log(err);
                alert_toast("An error occurred.", 'error');
                end_loader();
            },
            success: function(resp) {
                end_loader(); // Always end the loader
                if (typeof resp === 'object' && resp.status === 'success') {
                    alert_toast(resp.msg, 'success');
                    location.reload(); // Reload the page on success
                } else {
                    alert_toast(resp.msg || "An error occurred.", 'error');
                }
            }
        });
    }
});
</script>
</style>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var carousel = document.getElementById("product-carousel");
        var prevButton = document.getElementById("carousel-prev");
        var nextButton = document.getElementById("carousel-next");

        var currentSlide = 0;
        var slides = carousel.querySelectorAll(".carousel-item");

        function showSlide(index) {
            if (index >= slides.length) {
                index = 0;
            } else if (index < 0) {
                index = slides.length - 1;
            }

            slides.forEach(function(slide) {
                slide.classList.remove("active");
            });

            slides[index].classList.add("active");
            currentSlide = index;
        }

        prevButton.addEventListener("click", function() {
            showSlide(currentSlide - 1);
        });

        nextButton.addEventListener("click", function() {
            showSlide(currentSlide + 1);
        });

        // Show the initial slide
        showSlide(currentSlide);
    });
</script>




<style>
    #videos-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
    }

    .video-card {
        position: relative;
        width: calc(100% / 3 - 20px); /* Adjust this value to set the number of cards per row */
        max-width: 360px; /* Same width as YouTube's video cards */
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
        height: 350px;
        cursor: pointer;
    }

    .video-card:hover {
        box-shadow: 0 0 10px hsla(0, 0%, 0%, 0.1);
    }

    .video-container {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 aspect ratio (for responsive video) */
        height: 0;
        overflow: hidden;
        margin-bottom: 15px;
    }
    @media (max-width: 1024px) {
                    .video-card{
                        width: 100%;
                    }
                }
    .video-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    #meta-container {
        padding: 10px;
    }

    #video-title {
        line-height: 2.2rem;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        text-overflow: ellipsis;
        white-space: normal;
        margin: 12px 0 4px 0;
    }

    p {
        color: var(--yt-spec-text-secondary);
        font-family: "Roboto", "Arial", sans-serif;
        font-size: 15px;
        line-height: 1.8rem;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        text-overflow: ellipsis;
        white-space: normal;
        margin: 0;
    }

    #video-player-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    #video-player-modal.active {
        display: flex;
    }

    #video-player {
        width: 80%;
        max-width: 800px;
        height: 60vh;
        background: #000;
        overflow: hidden;
        position: relative;
    }

    #video-player iframe {
        width: 100%;
        height: 100%;
        border: none;
    }

    #close-video {
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: pointer;
        color: #fff;
        font-size: 24px;
        z-index: 1100;
    }
</style>
<div style="display: flex;" >
<h3 id="help-scroll" >Helper Videos</h3>
<i class="fas fa-info-circle info ml-2" data-toggle="tooltip" data-placement="top" 
   title="Ang mga Helper Videos na ito ay awtomatikong napili batay sa uri ng iyong mga pananim. Ang mga video ay konektado sa iyong kasalukuyang mga crops at naglalaman ng mahalagang impormasyon mula sa YouTube upang matulungan ka sa tamang pag-aalaga, pagpaparami, at pagprotekta ng iyong pananim. Ang mga videos ay maaaring maglaman ng mga step-by-step na gabay sa pagsasaka, pati na rin ang mga solusyon para sa mga karaniwang pest at disease na maaaring makaapekto sa iyong mga pananim. I-click lamang ang isang video upang panoorin ito."></i>

</div>

<div id="videos-container"></div>
<div id="video-player-modal">
    <div id="video-player">
        <span id="close-video">&times;</span>
        <iframe id="large-video-iframe" frameborder="0" allowfullscreen></iframe>
    </div>
</div>

<script>
    // Function to fetch YouTube videos based on search query
    function fetchYouTubeVideos(query) {
      start_loader();
        const apiKey = 'AIzaSyApi6uOjFhhw7mBQ4J5bIw0uEb9IoAKsW0';  // Replace with your actual API key
        const apiUrl = `https://www.googleapis.com/youtube/v3/search?key=${apiKey}&part=snippet&type=video&q=${query}`;

        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                const videos = data.items;
                const videosContainer = document.getElementById('videos-container');

                videos.forEach(video => {
                    const videoId = video.id.videoId;
                    const videoTitle = video.snippet.title;
                    const videoDescription = video.snippet.description;
                    const videoThumbnail = video.snippet.thumbnails.medium.url;
                    const videoUrl = `https://www.youtube.com/embed/${videoId}`;

                    // Create video card HTML
                    const videoCardHTML = `
                        <div class="video-card" data-video-id="${videoId}">
                            <div class="video-container">
                                <iframe src="${videoUrl}" frameborder="0" allowfullscreen></iframe>
                            </div>
                            <div id="meta-container">
                                <b id="video-title">${videoTitle}</b>
                                <p>${videoDescription}</p>
                            </div>
                        </div>
                    `;

                    // Append video card to container
                    videosContainer.innerHTML += videoCardHTML;
                });

                // Add event listeners to video cards
                const videoCards = document.querySelectorAll('.video-card');
                videoCards.forEach(card => {
                    card.addEventListener('click', () => {
                        const videoId = card.getAttribute('data-video-id');
                        const videoUrl = `https://www.youtube.com/embed/${videoId}`;

                        // Set the large video iframe source
                        const largeVideoIframe = document.getElementById('large-video-iframe');
                        largeVideoIframe.src = videoUrl;

                        // Display the modal
                        const videoPlayerModal = document.getElementById('video-player-modal');
                        videoPlayerModal.classList.add('active');
                    });
                });

                // Close video player modal when close button is clicked
                const closeVideoBtn = document.getElementById('close-video');
                closeVideoBtn.addEventListener('click', () => {
                    const videoPlayerModal = document.getElementById('video-player-modal');
                    videoPlayerModal.classList.remove('active');

                    // Pause the video
                    const largeVideoIframe = document.getElementById('large-video-iframe');
                    largeVideoIframe.src = '';
                });
                end_loader();
            })
            .catch(error => console.error('Error fetching videos:', error));
    }

    // Example usage: fetch videos related to how to grow the specific crop
    fetchYouTubeVideos('<?php echo $searchQuery; ?>'); // Replace with your actual search query
</script>
<script>
    $(document).ready(function(){
        // Function to get URL parameter
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            var results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

        $('#add_pd').click(function(){
            // Get the crop ID from the URL
            var cropid = getUrlParameter('id');
            // Open the modal and pass the crop ID as a parameter
            uni_modal('Add Pest or Disease', "crops/add_pestordisease.php?id=" + cropid, 'large');
        });
        $(document).ready(function(){
    $('.edit_crop').click(function(){
        uni_modal('Update Crop',"crops/edit_crop.php?id="+$(this).attr('data-id'),'large')
    });
    $('.edit_pd').click(function(){
            var pestID = $(this).attr('data-pest-id'); // Ensure correct attribute selector
            uni_modal('Update Pest/Disease', "crops/edit_pd.php?id=" + pestID, 'large'); // Correct modal title
        });
        $('.report_pd').click(function(){
            var pestID = $(this).attr('data-pdr-id'); // Ensure correct attribute selector
            uni_modal('File Report', "crops/file_report.php?id=" + pestID, 'small'); // Correct modal title
        });
        $('.edit_harvest').click(function(){
            var harvestId = $(this).attr('data-harvest-id'); // Ensure correct attribute selector
            uni_modal('Update Harvest', "crops/edit_harvest.php?id=" + harvestId, 'large'); // Correct modal title
        });
        $('.edit-activity').click(function(){
    var activityId = $(this).attr('data-activity-id'); // Ensure correct attribute selector
    uni_modal('Update Activity', "crops/edit_activity.php?id=" + activityId, 'large'); // Correct modal title
});
$('.edit-report').click(function(){
    var reportId = $(this).attr('data-report-id'); // Ensure correct attribute selector
    uni_modal('Update Report', "crops/edit_report.php?id=" + reportId, 'small'); // Correct modal title
});


});

    });
</script>

<script>
    $(document).ready(function(){
        // Function to get URL parameter
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            var results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

        $('#add_harvest').click(function(){
            // Get the crop ID from the URL
            var cropid = getUrlParameter('id');
            // Open the modal and pass the crop ID as a parameter
            uni_modal('Add Harvest', "crops/add_harvest.php?id=" + cropid, 'large');
        });
        $('#add_harvest_sched').click(function(){
            // Get the crop ID from the URL
            var cropid = getUrlParameter('id');
            // Open the modal and pass the crop ID as a parameter
            uni_modal('Add Harvest', "crops/add_harvest_sched.php?id=" + cropid, 'large');
        });
        $('#add_activity').click(function(){
            // Get the crop ID from the URL
            var cropid = getUrlParameter('id');
            // Open the modal and pass the crop ID as a parameter
            uni_modal('Add Activity', "crops/add_activity.php?id=" + cropid, 'large');
        });
    });
</script>
<script>
    $(function(){
        $('.delete_crop').click(function(){
            _conf("Are you sure you want to delete this crop?", 'delete_crop', [$(this).attr('data-id')]);
        });
    });

    function delete_crop(id) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=delete_crop",
            method: "POST",
            data: {id: id},
            dataType: "json",
            error: function(err) {
                console.log(err);
                alert_toast("An error occurred in deleting crop.", 'error');
                end_loader();
            },
            success: function(resp) {
                if (typeof resp === 'object' && resp.status === 'success') {
                    location.reload();
                } else {
                    alert_toast("An error occurred 2333333.", 'error');
                    end_loader();
                }
            }
        });
    }
</script>

<script>
    $(function(){
        $('.delete_activity').click(function(){
            _conf("Are you sure you want to delete this activity?", 'delete_activity', [$(this).attr('data-activity-id')]); // Corrected data attribute
        });
    });

    function delete_activity(id) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=delete_activity",
            method: "POST",
            data: {id: id},
            dataType: "json",
            error: function(err) {
                console.log(err);
                alert_toast("An error occurred in deleting the activity.", 'error');
                end_loader();
            },
            success: function(resp) {
                if (typeof resp === 'object' && resp.status === 'success') {
                    location.reload();
                } else {
                    alert_toast("An error occurred.", 'error'); // Removed the specific error code part
                    end_loader();
                }
            }
        });
    }
</script>
<script>
    $(function(){
        $('.delete_croppestanddisease').click(function(){
            _conf("Are you sure you want to delete this pest or disease record?", 'delete_croppestanddisease', [$(this).attr('data-pd-id')]);
        });
    });

    function delete_croppestanddisease(id) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=delete_croppestanddisease",
            method: "POST",
            data: {id: id},
            dataType: "json",
            error: function(err) {
                console.log(err);
                alert_toast("An error occurred in deleting the pest or disease record.", 'error');
                end_loader();
            },
            success: function(resp) {
                if (typeof resp === 'object' && resp.status === 'success') {
                    location.reload();
                } else {
                    alert_toast("An error occurred.", 'error');
                    end_loader();
                }
            }
        });
    }
</script>
<script>
    $(function(){
        $('.delete_harvest').click(function(){
            _conf("Are you sure you want to delete this harvest record?", 'delete_harvest', [$(this).attr('data-harvest-id')]);
        });
    });

    function delete_harvest(id) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=delete_harvest",
            method: "POST",
            data: {id: id},
            dataType: "json",
            error: function(err) {
                console.log(err);
                alert_toast("An error occurred in deleting the harvest record.", 'error');
                end_loader();
            },
            success: function(resp) {
                if (typeof resp === 'object' && resp.status === 'success') {
                    location.reload();
                } else {
                    alert_toast("An error occurred.", 'error');
                    end_loader();
                }
            }
        });
    }
</script>
<script>
    $(function(){
        $('.delete_report').click(function(){
            _conf("Are you sure you want to delete this report?", 'delete_report', [$(this).attr('data-report-id')]);
        });
    });

    function delete_report(id) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=delete_report",
            method: "POST",
            data: {id: id},
            dataType: "json",
            error: function(err) {
                console.log(err);
                alert_toast("An error occurred in deleting the report.", 'error');
                end_loader();
            },
            success: function(resp) {
                if (typeof resp === 'object' && resp.status === 'success') {
                    location.reload();
                } else {
                    alert_toast("An error occurred.", 'error');
                    end_loader();
                }
            }
        });
    }
</script>
<script>
  // Smooth scroll to different sections of the page
document.addEventListener('DOMContentLoaded', function () {
    const iconButtons = document.querySelectorAll('.icon-item');

    iconButtons.forEach(button => {
        button.addEventListener('click', function () {
            const target = this.getAttribute('data-target');
            const element = document.getElementById(target);

            if (element) {
                element.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
});

</script>
<script>
    $(document).ready(function() {
        $('.generate_qr').click(function(){
            uni_modal('Your Crop QR Code', "crops/qr_generator.php?id=" + $(this).attr('data-id'), 'small');
        });
        $('#generate_qr').click(function(){
            uni_modal('Your Crop QR Code', "crops/scanner.php", 'small');
        });
        $('#generate_qr').click(function(){
            // Get the crop ID from the URL
            var cropid = getUrlParameter('id');
            // Open the modal and pass the crop ID as a parameter
            uni_modal('Add Harvest', "crops/qr_generator.php?id=" + cropid, 'large');
        });
    });
</script>

<?php
include 'gemini.php';
?>
  <div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="qrModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrModalLabel">QR Code</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <canvas id="qrCanvas"></canvas>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="downloadQR" class="btn btn-primary">Download QR</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="scannerModal" tabindex="-1" role="dialog" aria-labelledby="scannerModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="scannerModalLabel">Scanner</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container1">
                        <h1>Scan QR Codes</h1>
                        <div class="section1">
                            <div id="my-qr-reader"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <style>
        .modal-body canvas {
            display: block;
            margin: 0 auto;
        }
    </style>
  <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js"></script>
    <!-- Custom script to generate QR code -->
    <script>
        $(document).ready(function () {
            const cropId = '<?= $crop_id ?>';
            const qrUrl = 'https://agronetnafa.website/?page=result/crop_info&id=' + cropId;
            const qrCanvas = document.getElementById('qrCanvas');
            const logoUrl = '../uploads/nafalogo.png'; // Replace with your logo image path

            $('#qrModal').on('shown.bs.modal', function () {
                generateQR(qrUrl, logoUrl);
            });

            $('#downloadQR').click(function () {
                downloadQR();
            });

            function generateQR(url, logoUrl) {
                const qrCode = qrcode(0, 'L');
                qrCode.addData(url);
                qrCode.make();
                const ctx = qrCanvas.getContext('2d');
                ctx.clearRect(0, 0, qrCanvas.width, qrCanvas.height);
                qrCanvas.width = qrCode.getModuleCount() * 10;
                qrCanvas.height = qrCode.getModuleCount() * 10;

                const gradient = ctx.createLinearGradient(0, 0, qrCanvas.width, qrCanvas.height);
                gradient.addColorStop(0, '#9CDC78');
                gradient.addColorStop(1, '#74DCB0');
                ctx.fillStyle = gradient;

                // Draw the QR code using the gradient
                for (let r = 0; r < qrCode.getModuleCount(); r++) {
                    for (let c = 0; c < qrCode.getModuleCount(); c++) {
                        if (qrCode.isDark(r, c)) {
                            ctx.fillRect(c * 10, r * 10, 10, 10);
                        }
                    }
                }

                // Add the logo image at the center
                const logo = new Image();
                logo.src = logoUrl;
                logo.onload = function () {
                    const logoSize = qrCanvas.width / 4; // Adjust the size of the logo
                    const logoX = (qrCanvas.width - logoSize) / 2;
                    const logoY = (qrCanvas.height - logoSize) / 2;
                    ctx.drawImage(logo, logoX, logoY, logoSize, logoSize);
                };
            }

            function downloadQR() {
                const link = document.createElement('a');
                link.href = qrCanvas.toDataURL('image/png');
                link.download = 'qrcode.png';
                link.click();
            }
            $('#scannerModal').on('shown.bs.modal', function () {
                html5QrCode = new Html5Qrcode("my-qr-reader");
                html5QrCode.start(
                    { facingMode: "environment" },
                    {
                        fps: 10, // Optional, frame per seconds for qr code scanning
                        qrbox: { width: 250, height: 250 } // Optional, if you want bounded box UI
                    },
                    qrCodeMessage => {
                        // Redirect to the scanned QR code URL
                        window.location.href = qrCodeMessage;
                    },
                    errorMessage => {
                        // Parse error, ignore it
                    })
                .catch(err => {
                    // Start failed, handle it.
                    console.error(`Unable to start scanning, error: ${err}`);
                });
            });

            $('#scannerModal').on('hidden.bs.modal', function () {
                if (html5QrCode) {
                    html5QrCode.stop().then(ignore => {
                        // QR Code scanning stopped
                    }).catch(err => {
                        console.error(`Unable to stop scanning, error: ${err}`);
                    });
                }
            });
        });
    </script>