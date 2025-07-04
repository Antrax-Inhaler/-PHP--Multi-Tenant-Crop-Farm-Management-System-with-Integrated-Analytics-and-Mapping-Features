<?php
// Fetch crop details based on crop ID
$crop_id = $_GET['id'] ?? 1;

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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Sidebar</title>
    <style>
    
        .sidebar-container {
            display: flex;
            gap: 20px;
        }
        .sidebar {
            width: 30%;
            top: 20px;
            height: calc(90vh - 40px);
            overflow-y: auto;
            gap: 20px;
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .card {
            background-color: #fff;
            border: 2px solid #929292;
            border-radius: 10px;
            padding: 13px;

        }
        .main-info {
            height: 300px;
        }
        .clients {
            height: 150px;
        }
        .reports {
            height: 300px;
        }
        .activities {
            height: 240px;
            width: 100%;
        }
        .inner {
            display: flex;
            gap: 20px;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .vacant {
            height: 500px;
            flex-grow: 2;
            flex-basis: 60%;
            min-width: 300px;
        }
        .small-inner {
            display: flex;
            flex-direction: column;
            gap: 20px;
            flex-grow: 1;
            flex-basis: 35%;
            min-width: 250px;
        }
        .notification {
            height: 237px;
        }
        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
            }
            .sidebar-container {
                flex-direction: column;
            }
            .container {
                padding: 0px;
            }
            .main-content .inner {
                flex-direction: column;
            }
            .vacant, .notification {
                width: 100%; /* Make cards full width on mobile */
            }
        }
        .centerer{
            width: 100%;
        }
        .carousel-inner {
            border-radius: 10px;
            overflow: hidden;
            max-height: 200px;
            position: relative;
        }

        /* Text overlay on top of the carousel */
        .carousel-text-overlay {
            position: absolute;
            top: 10px;
            left: 20px;
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            z-index: 10;
        }

        /* Indicators container at the bottom of the carousel */
        .carousel-indicators {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            justify-content: center;
            gap: 5px;
            z-index: 10;
        }

        .carousel-indicators .dot {
            width: 10px;
            height: 10px;
            background-color: #ddd;
            border-radius: 50%;
            display: inline-block;
            cursor: pointer;
        }

        .carousel-indicators .dot.active {
            background-color: #333;
        }

        /* Adjust image to take full height */
        .carousel-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar-container">
            <div class="sidebar">
                <div class="card main-info">
                    <b>Card</b>
                    <div class="carousel" id="product-carousel">
                        <div class="carousel-inner">
                            <!-- Text overlay on top of the carousel -->
                            <div class="carousel-text-overlay">Featured Products</div>

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
                        <!-- Add carousel indicators -->
                        <div class="carousel-indicators" id="carousel-indicators">
                            <?php foreach ($image_paths as $index => $path) : ?>
                                <span class="dot <?= $index === 0 ? 'active' : '' ?>" data-slide="<?= $index ?>"></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="card clients">
                    <h3>Card</h3>
                </div>
                <div class="card reports"></div>
            </div>
            <div class="main-content">
                <div class="card activities"></div>
                <div class="inner">
                    <div class="card vacant"></div>
                    <div class="small-inner">
                        <div class="card notification"></div>
                        <div class="card notification"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var carousel = document.getElementById("product-carousel");
        var prevButton = document.getElementById("carousel-prev");
        var nextButton = document.getElementById("carousel-next");
        var indicators = document.querySelectorAll("#carousel-indicators .dot");

        var currentSlide = 0;
        var slides = carousel.querySelectorAll(".carousel-item");

        function updateIndicators(index) {
            indicators.forEach(function(dot) {
                dot.classList.remove("active");
            });
            indicators[index].classList.add("active");
        }

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
            updateIndicators(index);
            currentSlide = index;
        }

        prevButton.addEventListener("click", function() {
            showSlide(currentSlide - 1);
        });

        nextButton.addEventListener("click", function() {
            showSlide(currentSlide + 1);
        });

        indicators.forEach(function(dot) {
            dot.addEventListener("click", function() {
                var index = parseInt(this.getAttribute("data-slide"));
                showSlide(index);
            });
        });

        // Show the initial slide
        showSlide(currentSlide);
    });
</script>
