<?php
// Fetch crop details based on crop ID
$crop_id = $_GET['id'] ?? null;

if (!$crop_id) {
    die("Crop ID is required.");
}

// Fetch crop details
$crop_query = "
    SELECT 
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
</style>

<div class="sidebar-container">
 

    <div class="main-content">
    <div class="card crop-details-card">
        
    <div class="color-strip"></div>
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
    <h1 class="crop-name" ><?= htmlspecialchars($crop['crop_name']) ?></h1>
   

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
</div>

</div>
        <div id="pd-scroll" class="card pest-disease-card">
        <div class="color-strip"  style="background-color:#dc3545"></div>

    <h4>Pest and Disease</h4>
    <p>Pest and Disease Count: <?= htmlspecialchars(count($pests)) ?></p>
    <div class="scrollable-container farm_list">
    <?php foreach ($pests as $pest): ?>
    <div class="pd_card position-relative" data-pest-id="<?= htmlspecialchars($pest['Id']) ?>">
        <div class="card_img_container">
        <img class="crop_image" src="../<?= htmlspecialchars($pest['Image1']) ?>" alt="<?= htmlspecialchars($pest['Image1']) ?>">
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
    </div>
<?php endforeach; ?>

        <button id="add_pd" class="btn btn-success btn-sm mt-2">Add Pest/Disease</button>
    </div>
</div>


        <div id="harvest-scroll" class="card harvest-card">
        <div  class="color-strip"  style="background-color: gold"></div>

            <h3>Harvest Information</h3>
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
                <button id="add_harvest" class="btn btn-success btn-sm mt-2">Add Harvest</button>
            </div>
        </div>
    </div>
    <div class="sidebar">
        <div id="activity-scroll" class="card crop-activity-card">
        <div class="color-strip"></div>

            <div class="card-header">
                <h4>Crop Activity</h4>
                <button id="add_activity" class="btn btn-success btn-sm mt-2">Add Activity</button>
            </div>
            <div class="card-body scrollable-container off-scroll" style="height: 400px; overflow-y: auto; padding: 7px;">
                <?php
                // Fetch crop activity data for the current vendor's crop (replace with your query)
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
                          <div class="card" >
                          <div class="activity-item position-relative">
    <p><strong><?= $activity_date ?>:</strong> <?= $activity['activity_type'] ?></p>
    <p><?= $activity['description'] ?></p>
    
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
        <div id="report-scroll" class="card crop-report-card">
        <div class="color-strip"  style="background-color: orange"></div>

    <div class="card-header">

        <h5>Pest and Disease Reports</h5>
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

    </div>
</div>

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
<h3 id="help-scroll" >Helper Videos</h3>
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
<?php
include 'gemini.php';
?>>