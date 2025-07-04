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
        body{
            background-color: #FBFBFD;
        }
        .container{
            background-color: #FBFBFD;
        }
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
            border: 0.5px solid #ccc;  /* Super thin border */
            border-radius: 10px;
            padding: 13px;
            box-shadow: none;
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
            height: 260px;
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
        .notif {
            height: 237px;
        }
        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
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
            .landscape {
    flex-direction: column;
    justify-content: center;
    height: auto;
    gap: 10px;
    overflow: hidden;
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
            bottom: -25px;
            left: 36%;
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
            background-color: #008773;
        }

        /* Adjust image to take full height */
        .carousel-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .carousel-item img {
    width: 100%;
    height: 190px;
    object-fit: cover;
}
        #videos-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
        flex-direction: column;
        overflow: auto;
    }

    .video-card {
        position: relative;
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
    ::-webkit-scrollbar {
    width: 12px; /* Width of the scrollbar */
    height: 8px; /* Height of the scrollbar (for horizontal scrollbars) */
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
#map {
            height: auto;
            width: 100%;
        }
        .carousel-control-icon{
            color: #087E6E;
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
.carousel-control-prev,
.carousel-control-next {
  top: auto;
  bottom: auto;
}
.client-container {
    display: flex;
    overflow-x: auto;
    overflow-y: hidden;
    border-bottom: 1px solid #ddd;
    width: 100%; /* Adjusted width to allow flexible container */
    white-space: nowrap;
    padding: 10px 0;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* Internet Explorer and Edge */
}
.client-container::-webkit-scrollbar {
    display: none; /* Hide scrollbar for Chrome, Safari, and Opera */
}
.client-avatar {
    text-align: center;
    margin: 2px;
    width: 80px;
    display: flex;
    flex-direction: column;
    height: 80px;
    align-items: center;
}

.client-avatar img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    cursor: pointer; /* Make it clear that the avatar is clickable */
}

.client-name-container {
    overflow: hidden;
    width: 80px;
}

.client-name {
    margin-top: 2px;
    font-size: 14px;
}
#map {
    width: 100%; /* Adjust width as needed */
    height: 400px; /* Set height for the map container */
    margin-top: 10px; /* Optional: Add some space above the map */
}

.gm-style-iw-ch {
    padding-top: 1px;
    overflow: hidden;
}

.gm-ui-hover-effect {
    margin-top: 1000px;
}

.gm-style-iw-chr button {
    margin-top: -30px !important;
    font-size: x-large !important;
    top: 0 !important;
    right: 4 !important;
}
element.style {
    padding-top: 0px;
    min-width: 549px !important;
    max-width: 549px !important;
    max-height: 536px;
}
.timeline {
            display: flex;
            justify-content: space-between;
            position: relative;
            padding: 30px 0;
            width: 100%;
            width: 1000px;
            margin-left: auto;
            margin-right: auto;
            margin: 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 4px;
            background: #068574;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
        }

        .timeline-item {
            position: relative;
            width: 20%;
            text-align: center;
            cursor: pointer;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            width: 12px;
            height: 12px;
            background: #068574;
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
            transition: background-color 0.3s ease;
        }

        .timeline-item:hover::before {
            background-color: #EFAB00;
        }
    
        .timeline-item .content {
            padding: 10px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: relative;
            top: 80px;
            width: 100px;
            margin: 0 auto;
            opacity: 0.8;
            transition: transform 0.3s ease, opacity 0.3s ease;
            font-size: 10px;
        }

        .timeline-item:hover .content {
            transform: scale(1.1);
            opacity: 1;
        }

        .timeline-item:nth-child(even) .content {
            top: -25px;
        }

        .timeline-item .date {
            margin-top: 10px;
            font-size: 0.9em;
            color: #068574;
        }

        .timeline-item:hover .date {
            color: #EFAB00;
        }
        .activity-timeline:hover .timeline{
            animation-play-state: paused;
        }
        /* Action Menu */
        .action-menu {
            display: none;
            position: absolute;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 10;
            top: 50px;
            left: 50%;
            transform: translateX(-50%);
            width: 300px;

        }


        .action-menu a {
            display: block;
            padding: 5px 0;
            text-decoration: none;
            color: #068574;
            font-size: 0.9em;
        }

        .action-menu a:hover {
            color: #EFAB00;
        }
        .action-menu-a{
            display: flex;
            flex-direction: row;
            justify-content: center;
            gap: 10px;
            
        }
        .activity-timeline {
    max-width: 500px;
    overflow: auto;
    height: 230px;
}
        .qr-container{
            width: 100px;
            height: 100px;
        }
        .landscape{
            display: flex;
            flex-direction: row;
            justify-content: flex-start;
            height: auto;
            gap: 10px;
            overflow: hidden;
        }
        #downloadQR {
    padding: 8px;
    font-size: 1.2em; /* Adjust the size of the icon */
    background-color: transparent;
    border: none;
    color: black;
}

#downloadQR i {
    margin: 0;
}
.vertical-line {
    width: 1px; /* Thin line */
    height: 90%; /* Adjust height */
    background-color: #ccc; /* Color of the line */
    margin: 0 10px; /* Optional: Add margin around the line */
}
.right-act{
    display: flex;
    flex-direction: column;
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
    .pod-card {
            display: flex;
            align-items: center;
            background-color: white;
            border-radius: 8px;
            padding: 15px;
            width: 100%;
            border-left: 4px solid #4caf50;
            position: relative;
            height: 62px;
        }
        .outer-card{
            display: flex;
            align-items: center;
            background-color: white;
            border-radius: 8px;
            width: 100%;
            position: relative;
            height: 62px;
        }
        .ai-button{
            top: 0;
            right: 0;
            position: absolute;
            z-index: 10000;
        }
        .icon {
    width: 50px;
    height: 40px;
    background-color: #f0f0f0;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-right: 15px;
}
        .icon img {
            width: 20px;
            height: 20px;
        }
        .details {
    display: flex;
    justify-content: space-between;
    width: 100%;
}
        .details h4 {
            margin: 0;
            font-size: 16px;
            font-weight: normal;
        }
        .details span {
            color: gray;
            font-size: 14px;
        }
        .amount {
            color: #333;
            font-weight: bold;
        }
        /* Removing the gap between cards */
        .pod-card + .pod-card {
            margin-top: 0;
        }
        .ai-btn-activities{
            background: linear-gradient(to bottom right, #9CDC78, #74DCB0) !important;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            font-size: 10px;
            color: white;
            border: #007bff;
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
    top: 0;
    position: absolute;
    right: 0;
    background: linear-gradient(to bottom right, #9CDC78, #74DCB0) !important;
}
.carousel-text-overlay {
    position: absolute;
    color: white;
    z-index: 10;
}

.planted-date {
    font-size: 1rem;
    font-weight: 300;
    left: 165px;
    position: relative;
}

.crop-type {
    position: relative;
    bottom: -124px;
    right: -169px;
    font-size: 1.2rem;
    font-weight: 300;
    font-weight: bold;
}

.plantation-size {
    bottom: -88px;
    left: -9px;
    font-size: 1.5rem;
    font-weight: bold;
    position: relative;
}

.plantation-size .hec-unit {
    font-size: 0.9rem; /* Smaller font for hectares */
    font-weight: normal;
}

    </style>
    </head>
<body>
    <div class="container">
        <div class="sidebar-container">
            <div class="sidebar">
                <div class="card main-info">
                <div style="display: flex;" >
        <b class="crop-name" ><?= htmlspecialchars($crop['crop_name']) ?></b>
        <i class="fas fa-info-circle info" data-toggle="tooltip" data-placement="top" title="Ang paglagay ng data sa crops ay mahalaga upang masubaybayan ang progreso ng iyong mga pananim, matukoy ang tamang panahon ng pagtatanim at pag-aani, pati na rin ang pag-specify ng variety at laki ng plantation na kinakailangan para sa mapping. Huwag kalimutang i-update ang status ng iyong pananim, mula sa active, inactive, not productive, hanggang sa end of lifespan."></i>
        </h1>
        </div>
                    <div class="carousel" id="product-carousel">
                        <div class="carousel-inner">
                            <!-- Text overlay on top of the carousel -->
                            <div class="carousel-text-overlay">
        <!-- Top right: Date Planted -->
        <div class="planted-date">
            <?= htmlspecialchars($crop['date_planted']) ?>
        </div>

        <!-- Bottom right: Crop Type -->
        <div class="crop-type">
            <i><?= htmlspecialchars($crop['crop_type']) ?></i>
        </div>

        <!-- Bottom left: Plantation Size -->
        <div class="plantation-size">
            <?= htmlspecialchars($crop['size_of_plantation']) ?> 
            <span class="hec-unit">hectares</span>
        </div>
    </div>

                            <?php foreach ($image_paths as $index => $path) : ?>
                                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                    <img src="<?= validate_image($path) ?>" class="d-block carousel-image" alt="Product Image">
                                </div>
                            <?php endforeach; ?>
                        </div>
                       
                        <!-- Add carousel indicators -->
                        <div class="carousel-indicators" id="carousel-indicators">
                            <?php foreach ($image_paths as $index => $path) : ?>
                                <span class="dot <?= $index === 0 ? 'active' : '' ?>" data-slide="<?= $index ?>"></span>
                            <?php endforeach; ?>
                        </div>
                        <button class="carousel-control-prev" type="button" id="carousel-prev">
                            <span class="carousel-control-icon">&lt;</span>
                        </button>
                        <button class="carousel-control-next" type="button" id="carousel-next">
                            <span class="carousel-control-icon">&gt;</span>
                        </button>
                    </div>
                </div>
                <div class="card clients">
                    <b>Clients</b>
                    <div class="client-container" id="client-container">
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
            c.avatar, -- Ensure the avatar column is included
            ic.timestamp
        FROM interested_clients ic
        INNER JOIN client_list c ON ic.client_id = c.id
        WHERE ic.crop_id = $crop_id
        ORDER BY ic.timestamp DESC
        ";
        $client_interest_result = $conn->query($client_interest_query);

        if ($client_interest_result->num_rows > 0) {
            while ($interest = $client_interest_result->fetch_assoc()) {
                $interest_id = $interest['interest_id'];
                $client_name = htmlspecialchars($interest['firstname']);
                $avatar = htmlspecialchars($interest['avatar']); // Ensure the avatar data is set
                ?>
                <div class="client-avatar" data-interest-id="<?= $interest_id ?>">
                    <img src="../<?= $avatar ?>" alt="Avatar" class="view-btn" data-interest-id="<?= $interest_id ?>">
                    <div class="client-name-container">
                        <div class="client-name"><?= $client_name ?></div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p>No client interest recorded yet.</p>";
        }
        ?>
    </div>
                </div>
                <div class="card reports">
                <div class="">
                <div style="display: flex;" >
                <b>Pest and Disease Reports</b>
        <i class="fas fa-info-circle info ml-2" data-toggle="tooltip" data-placement="top" 
   title="Ang seksyon ng Pest and Disease Reports ay nagpapakita ng mga nireport na pest or disease sa association. Mahalaga na patuloy na i-update ang impormasyon ng mga report na ito upang malaman kung ito ba ay nabisita na, naresolba, o nangangailangan pa ng aksyon. Sa pamamagitan ng regular na pag-update, makakatulong ito sa iyong association na mas mabilis na makapagbigay ng nararapat na tulong at sa Artificial Intelligence upang makapagbigay ng mas tumpak na mungkahi para maiwasan ang pagkalat ng pest o disease."></i>
                </div>
        
    </div>
    <style>
        .card-item {
    display: flex;
    align-items: center;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 15px;
    width: 100%;
    border-left: 4px solid #4caf50;
    position: relative;
    height: 62px;
    margin-bottom: 15px;
    cursor: pointer;
}

    </style>
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
            // Determine the border color based on the status
            $borderColor = '';
            if ($report['status'] == 0) {
                $borderColor = '#ff9800'; // Orange for 'Pending'
            } else {
                $borderColor = '#4caf50'; // Green for 'Resolved'
            }
            ?>
            <div class="card-item" style="border-left-color: <?= htmlspecialchars($borderColor) ?>;" onclick="toggleActionMenu(this)">
                <div class="icon">
                    <i class="fas fa-bug"></i>
                </div>
                <div class="details">
                    <h4><?= htmlspecialchars($report['pest_name']) ?></h4>
                    <span class="amount" title="Report Date"><?= htmlspecialchars($report_date) ?></span>
                </div>
                <div class="action-menu-pod">
                    <div class="arrow-up"></div>
                    <a class="dropdown-item edit_report" href="#" data-report-id="<?= htmlspecialchars($report['report_id']) ?>">Update</a>
                    <a class="dropdown-item delete_report" href="#" data-report-id="<?= htmlspecialchars($report['report_id']) ?>">Delete</a>
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
            <div class="main-content">
                <div class="card activities">
                   
                    <div class="landscape">
                    <div class="left-act">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
            
            <div>
            <b>Crop Activity</b>
                <i class="fas fa-info-circle info ml-2" data-toggle="tooltip" data-placement="top" 
                    title="Ang paglagay ng crop activity data ay mahalaga upang masubaybayan ang mga aktibidad na isinasagawa sa iyong tanim tulad ng pagtatanim, pagdidilig, paglalagay ng pataba, at iba pa. Ito ay nakakatulong upang makita ang mga pattern ng pag-aalaga ng pananim at makagawa ng mga tamang hakbang upang mapabuti ang ani. Magagamit din ito para sa pagsasaayos ng scheduling ng mga farm tasks. Palagiang paglalagay ng data ay mahalaga upang makakuha ng mas accurate na impormasyon at mungkahi mula sa Artificial Intelligence na makakatulong sa pamamahala ng iyong farm."></i>
            </div>
            <div>
            <button class="ai-btn-activities" data-crop-id="<?= htmlspecialchars($crop_id) ?>">AI</button>
            <button id="add_activity" class="btn btn-success btn-sm mt-2"><i class="fas fa-plus" ></i> Add</button>
            </div>
           
            </div>
                    <div class="activity-timeline">
                <div class="timeline">
        <?php
        // Fetch crop activity data for the current crop
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
                <div class="timeline-item" onclick="toggleActionMenu(this)">
                    <div class="content"><?= htmlspecialchars($activity['activity_type']) ?></div>
                    <div class="date"><?= htmlspecialchars($activity_date) ?></div>
                    <div class="action-menu">
                        <p><?= htmlspecialchars($activity['description']) ?></p>
                        <div class="action-menu-a">
                        <a class="dropdown-item edit-activity" href="#" data-activity-id="<?= htmlspecialchars($activity['id']) ?>">Edit</a>
                        <a class="dropdown-item delete_activity" href="#" data-activity-id="<?= htmlspecialchars($activity['id']) ?>">Delete</a>
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
    </div>
    <div class="vertical-line"></div>
<div class="right-act">
    <div class="qr-header">
    <b>This Crop QRCode</b>
<button id="downloadQR" class="btn btn-primary" style="">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
        <path d="M.5 9.9a.5.5 0 0 1 .5.5v3.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V10.4a.5.5 0 0 1 1 0v3.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V10.4a.5.5 0 0 1 .5-.5z"/>
        <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
    </svg>
</button>
    </div>
   
    <div class="qr-container" style="width: 100%; max-width: 180px; height: auto;">
    <canvas id="qrCanvas" ></canvas>
</div>
</div>


    </div>
                </div>
                <div class="inner">
                    <div class="card vacant">
                        <b>Farm Map</b>    <input type="text" id="searchInput" placeholder="Search farms, crops, etc.">

                        <div id="map"></div>
                    </div>
                    <div class="small-inner">
                        <div class="card notif">
                        <div style="display: flex;" >
<b id="help-scroll" >Farmer TV</b>
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
                        </div>
                        <style>
                            .pest-list{
                                overflow: auto;
                            }
                            .action-menu-pod {
    display: none;
    position: absolute;
    background-color: white;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 5px 10px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 10;
    top: 110%;
    left: 50%;
    transform: translateX(-50%);
    width: 200px;
}

.action-menu-pod a {
    display: block;
    padding: 8px;
    text-decoration: none;
    color: #068574;
    font-size: 0.9em;
}

.action-menu-pod a:hover {
    color: #EFAB00;
}

/* Tooltip arrow */
.action-menu-pod .arrow-up {
    width: 0; 
    height: 0; 
    border-left: 10px solid transparent;
    border-right: 10px solid transparent;
    border-bottom: 10px solid #ddd;
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
}

.action-menu-pod .arrow-up::after {
    content: "";
    position: absolute;
    top: 1px;
    left: -9px;
    width: 0;
    height: 0;
    border-left: 9px solid transparent;
    border-right: 9px solid transparent;
    border-bottom: 9px solid white;
}
                        </style>
                        <div class="card notif">
                        <div style="display: flex;">
    <b>Pest and Disease</b>
    <i class="fas fa-info-circle info" data-toggle="tooltip" data-placement="top" title="Ang pag-record ng pest at disease data ay mahalaga upang masubaybayan ang kalusugan ng iyong pananim at mapigilan ang pagkalat ng sakit. Sa pamamagitan ng tamang pag-alam sa lawak ng apektadong lugar, maaari mong mas maayos na maipatupad ang mga hakbang na makakatulong sa pagprotekta sa iyong tanim. Sa pamamagitan ng paglagay ng impormasyon tungkol sa pest o disease, makakalikha ang Artificial Intelligence ng mga posibleng solusyon para mamanage ito. Manonotify din ang mga kalapit na farm upang maiwasan ang pagkalat ng naturang pest o disease. Maaari mo rin itong ireport sa iyong association para sa mas masusing pagsusuri kung kinakailangan."></i>

    </div>
                        <div class="pest-list" >
                        <?php foreach ($pests as $pest): 
    // Determine the border color based on the status
    $borderColor = '';
    if ($pest['Status'] === 'Existing') {
        $borderColor = '#ff9800'; // Orange for 'Existing'
    } elseif ($pest['Status'] === 'Fixed') {
        $borderColor = '#4caf50'; // Green for 'Fixed'
    } elseif ($pest['Status'] === 'Worsened') {
        $borderColor = '#f44336'; // Red for 'Worsened'
    }
?>
<div class="outer-card">
<div class="ai-button">
            <!-- Remove input field -->
            <button class="btn btn-primary rounded-circle ai-btn" data-pest-id="<?= htmlspecialchars($pest['Id']) ?>">AI</button>
        </div>
<div class="pod-card" style="border-left-color: <?= htmlspecialchars($borderColor) ?>;" onclick="toggleActionMenu(this)">

    <div class="icon">
        <img class="crop_image" src="../<?= htmlspecialchars($pest['Image1'] ?: 'uploads/alternative-pest.jpg') ?>" alt="<?= htmlspecialchars($pest['Image1'] ?: 'Alternative Image Description') ?>">
    </div>
    <div class="details">
        <h4><?= htmlspecialchars($pest['Name']) ?></h4>
        <span class="amount" title="Affected Area"><?= htmlspecialchars($pest['SizeOfAreaAffected']) ?> Ha</span>
    </div>
    <div class="action-menu-pod">
        <div class="arrow-up"></div>
        <a class="dropdown-item edit_pd" href="" data-pest-id="<?= htmlspecialchars($pest['Id']) ?>">Edit</a>
        <a class="dropdown-item delete_croppestanddisease" href="" data-pd-id="<?= htmlspecialchars($pest['Id']) ?>">Delete</a>
        <?php if ($not_been_reported): ?>
            <a class="dropdown-item report_pd" href="" data-pdr-id="<?= htmlspecialchars($pest['Id']) ?>">Report</a>
        <?php endif; ?>
    </div>

</div>
</div>
<?php endforeach; ?>

                        </div>
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
<script>
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
<script>
    // Function to fetch YouTube videos based on search query
    function fetchYouTubeVideos(query) {
      start_loader();
        const apiKey = 'AIzaSyApi6uOjFhhw7mBQ4J5bIw0uEb9IoAKsW01';  // Replace with your actual API key
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
document.addEventListener('DOMContentLoaded', function () {
    // Convert activity data into labels and datasets
    const formattedData = activityData.map(item => ({
        x: new Date(item.date),
        y: 1, // Set y-value for placement (or use different y-values if needed)
        label: item.type,
        description: item.description,
    }));

    // Chart.js setup
    const ctx = document.getElementById('activityTimeGraph').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'Crop Activities',
                data: formattedData,
                backgroundColor: '#4caf50',
                borderColor: '#4caf50',
                showLine: true, // Display as a line connecting the points
                fill: false,
            }]
        },
        options: {
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'day', // Adjusts based on the data, e.g., 'day', 'month', 'year'
                    },
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    ticks: {
                        display: false, // Hide y-axis ticks
                    },
                    title: {
                        display: true,
                        text: 'Activity'
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        title: (tooltipItems) => tooltipItems[0].raw.label,
                        label: (tooltipItem) => tooltipItem.raw.description,
                    }
                }
            }
        }
    });
});
</script>

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
<!-- <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBaEr_ZLsaoWcbipd--a1S5EPQe2RaEfio&libraries=drawing&callback=initMap"
    async defer>
</script> -->
<script>
// Initialize the map
function initMap() {
    // Create a map centered at a default location
    const map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 13.232900, lng: 121.156900 }, // Default center coordinates (adjust as needed)
        zoom: 12, // Adjust the zoom level as needed
    });

    // Custom marker icon
    const customMarker = '../uploads/marker100.png';

    // Array to store farm locations, boundaries, and associated crops with harvest information fetched from PHP
    const farmData = [
        <?php
        // PHP code to fetch farm locations, boundaries, and associated crops with harvest information
        $user_id = $_settings->userdata('id');
        $sql = "SELECT f.Name as FarmName, f.Latitude as FarmLat, f.Longitude as FarmLng,
                       f.boundary, -- Fetch the boundary field
                       c.Name as CropName, c.Type, c.Description, c.Picture1,
                       h.HarvestedDate, h.AmountOfHarvest, h.Paid
                FROM farm f
                INNER JOIN crop c ON f.Id = c.FarmId
                INNER JOIN harvest h ON c.Id = h.CropId
                WHERE c.delete_flag = 0
                ORDER BY f.Name ASC, c.Name ASC";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Initialize an array to hold farm data
            $farmDataArray = [];
            while($row = $result->fetch_assoc()) {
                // Group crops by farm
                $farmDataArray[$row["FarmName"]][] = [
                    "cropName" => $row["CropName"],
                    "cropDetails" => [
                        "Type" => $row["Type"],
                        "Description" => $row["Description"],
                        "Picture1" => $row["Picture1"],
                        "HarvestedDate" => $row["HarvestedDate"],
                        "AmountOfHarvest" => $row["AmountOfHarvest"],
                        "Paid" => $row["Paid"]
                    ],
                    "lat" => $row["FarmLat"],
                    "lng" => $row["FarmLng"],
                    "boundary" => $row["boundary"] // Include boundary in the data
                ];
            }

            // Generate JavaScript array for farm data
            foreach ($farmDataArray as $farmName => $crops) {
                echo "{ farmName: '{$farmName}', crops: [";
                foreach ($crops as $crop) {
                    echo "{ cropName: '{$crop['cropName']}', cropDetails: " . json_encode($crop['cropDetails']) . ", lat: {$crop['lat']}, lng: {$crop['lng']}, boundary: '{$crop['boundary']}' },";
                }
                echo "] },\n";
            }
        } else {
            echo "{ farmName: 'No farms found', crops: [] }"; // Default empty data
        }
        ?>
    ];

    // Array to store markers for filtering
    const markers = [];

    // Add farm markers, boundaries, and crop info windows to the map
    farmData.forEach((farm) => {
        // Add a marker for the farm
        const farmMarker = new google.maps.Marker({
            position: { lat: parseFloat(farm.crops[0].lat), lng: parseFloat(farm.crops[0].lng) }, // Use first crop coordinates
            map: map,
            title: farm.farmName,
            icon: customMarker, // Use custom marker icon
        });

        // Draw farm boundary if boundary data exists
        if (farm.crops[0].boundary) {
            // Parse the boundary JSON string
            const boundaryCoords = JSON.parse(farm.crops[0].boundary);

            // Convert boundary coordinates to a path for the polygon
            const boundaryPath = boundaryCoords.map(coord => ({
                lat: parseFloat(coord.lat),
                lng: parseFloat(coord.lng)
            }));

            // Create a polygon for the farm boundary
            const farmBoundary = new google.maps.Polygon({
                paths: boundaryPath,
                strokeColor: '#FF0000',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#FF0000',
                fillOpacity: 0.35
            });

            // Set the polygon on the map
            farmBoundary.setMap(map);
        }

        // Create info window for the farm and its crops
        const infowindow = new google.maps.InfoWindow({
            content: generateInfoWindowContent(farm),
        });

        // Add click listener for the marker to show the info window
        farmMarker.addListener("click", () => {
            infowindow.open(map, farmMarker);
        });

        // Push marker to markers array for filtering
        markers.push({ marker: farmMarker, farm });
    });

    // Function to generate info window content for farms and associated crops with harvest details
    function generateInfoWindowContent(farm) {
        let content = `<strong>${farm.farmName}</strong><br>`;
        farm.crops.forEach((crop) => {
            content += 
                `<div class="card mt-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <img src="../${crop.cropDetails.Picture1}" alt="Crop Image" width="100">
                            </div>
                            <div class="col-md-8">
                                <strong>${crop.cropName}</strong><br>
                                <ul class="list-unstyled">
                                    <li><strong title="Type">Type:</strong> ${crop.cropDetails.Type}</li>
                                    <li><strong title="Harvested Date">Harvested Date:</strong> ${crop.cropDetails.HarvestedDate}</li>
                                    <li><strong title="Amount of Harvest">Amount of Harvest:</strong> ${crop.cropDetails.AmountOfHarvest}</li>
                                    <li><strong title="Paid">Paid:</strong> ${crop.cropDetails.Paid ? 'Yes' : 'No'}</li>
                                    <li><strong title="Description">Description:</strong> ${crop.cropDetails.Description}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>`;
        });
        return content;
    }

    // Function to filter markers based on search input
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchQuery = this.value.toLowerCase().trim();
        markers.forEach((item) => {
            const { marker, farm } = item;
            const farmName = farm.farmName.toLowerCase();
            let found = false;

            farm.crops.forEach((crop) => {
                const cropName = crop.cropName.toLowerCase();
                const cropType = crop.cropDetails.Type.toLowerCase();
                const harvestedDate = crop.cropDetails.HarvestedDate.toLowerCase();

                if (cropName.includes(searchQuery) || cropType.includes(searchQuery) || harvestedDate.includes(searchQuery)) {
                    found = true;
                }
            });

            if (farmName.includes(searchQuery) || found) {
                marker.setVisible(true); // Show marker if matches search
            } else {
                marker.setVisible(false); // Hide marker if does not match search
            }
        });
    });
}

    </script>


    <script>
        // Load the map
        window.onload = initMap;
    </script>

<script>
        // Toggle action menu visibility
        function toggleActionMenu(element) {
            const actionMenu = element.querySelector('.action-menu');
            const allActionMenus = document.querySelectorAll('.action-menu');
            
            // Hide other action menus
            allActionMenus.forEach(menu => {
                if (menu !== actionMenu) {
                    menu.style.display = 'none';
                }
            });

            // Toggle the clicked action menu
            actionMenu.style.display = actionMenu.style.display === 'block' ? 'none' : 'block';
        }

        // Example edit functionality
        function editItem(event, activityId) {
            event.stopPropagation(); // Prevent action menu from closing on click
            alert('Edit activity: ' + activityId);
            // Perform the edit action here
        }

        // Example delete functionality
        function deleteItem(event, activityId) {
            event.stopPropagation(); // Prevent action menu from closing on click
            if (confirm('Are you sure you want to delete this activity?')) {
                // Perform the delete action here (e.g., via AJAX)
                alert('Deleted activity: ' + activityId);
            }
        }

        // Hide action menu when clicking outside
        document.addEventListener('click', function (event) {
            const actionMenus = document.querySelectorAll('.action-menu');
            actionMenus.forEach(menu => {
                if (!event.target.closest('.timeline-item')) {
                    menu.style.display = 'none';
                }
            });
        });

    </script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js"></script>

<!-- Container for the QR code -->

<script>
$(document).ready(function () {
    const cropId = '<?= $crop_id ?>';
    const qrUrl = 'https://agronetnafa.website/?page=result/crop_info&id=' + cropId;
    const qrCanvas = document.getElementById('qrCanvas');
    const logoUrl = '../uploads/nafalogo.png'; // Replace with your logo image path

    // Generate the QR code on page load
    generateQR(qrUrl, logoUrl);

    // Download QR code
    $('#downloadQR').click(function () {
        downloadQR();
    });

    // Function to generate the QR code
    function generateQR(url, logoUrl) {
        // Get the container size
        const container = document.querySelector('.qr-container');
        const containerWidth = container.offsetWidth;
        const containerHeight = containerWidth; // Keep it square

        // Set the canvas size based on the container
        qrCanvas.width = containerWidth;
        qrCanvas.height = containerHeight;

        const qrCode = qrcode(0, 'L');
        qrCode.addData(url);
        qrCode.make();

        const ctx = qrCanvas.getContext('2d');
        ctx.clearRect(0, 0, qrCanvas.width, qrCanvas.height);

        const tileW = qrCanvas.width / qrCode.getModuleCount();
        const tileH = qrCanvas.height / qrCode.getModuleCount();

        // Apply the gradient background
        const gradient = ctx.createLinearGradient(0, 0, qrCanvas.width, qrCanvas.height);
        gradient.addColorStop(0, '#9CDC78');
        gradient.addColorStop(1, '#74DCB0');
        ctx.fillStyle = gradient;

        // Draw the QR code using the gradient
        for (let r = 0; r < qrCode.getModuleCount(); r++) {
            for (let c = 0; c < qrCode.getModuleCount(); c++) {
                if (qrCode.isDark(r, c)) {
                    ctx.fillRect(c * tileW, r * tileH, tileW, tileH);
                }
            }
        }

        // Add the logo image at the center of the QR code
        const logo = new Image();
        logo.src = logoUrl;
        logo.onload = function () {
            const logoSize = qrCanvas.width / 4; // Adjust the size of the logo
            const logoX = (qrCanvas.width - logoSize) / 2;
            const logoY = (qrCanvas.height - logoSize) / 2;
            ctx.drawImage(logo, logoX, logoY, logoSize, logoSize);
        };
    }

    // Function to download the QR code as an image
    function downloadQR() {
        const link = document.createElement('a');
        link.href = qrCanvas.toDataURL('image/png');
        link.download = 'qrcode.png';
        link.click();
    }

    // Resize QR code when the window is resized
    $(window).resize(function() {
        generateQR(qrUrl, logoUrl);
    });
});


</script>
<script>
        function ghostHorizontalScroll() {
            const container = document.querySelector('.activity-timeline');
            let scrollDirection = 1; // 1 for right, -1 for left
            let scrollInterval;

            function startScrolling() {
                // Start the scrolling interval
                scrollInterval = setInterval(() => {
                    // Scroll by a small amount in the current direction
                    container.scrollLeft += scrollDirection;

                    // Reverse direction if the right or left end is reached
                    if (container.scrollLeft + container.clientWidth >= container.scrollWidth) {
                        scrollDirection = -1; // Switch to scrolling left
                    } else if (container.scrollLeft <= 0) {
                        scrollDirection = 1; // Switch to scrolling right
                    }
                }, 10); // Adjust the interval for different scroll speeds
            }

            function stopScrolling() {
                // Clear the scrolling interval
                clearInterval(scrollInterval);
            }

            // Start the ghostly horizontal scroll effect
            startScrolling();

            // Add event listeners to stop scrolling on hover
            container.addEventListener('mouseover', stopScrolling);
            container.addEventListener('mouseout', startScrolling);
        }

        // Initialize the ghostly scroll effect when the page loads
        window.onload = ghostHorizontalScroll;
    </script>
    <script>
        // Toggle action menu visibility
function toggleActionMenu(podCard) {
    const actionMenu = podCard.querySelector('.action-menu-pod');
    const allActionMenus = document.querySelectorAll('.action-menu-pod');
    
    // Hide other action menus
    allActionMenus.forEach(menu => {
        if (menu !== actionMenu) {
            menu.style.display = 'none';
        }
    });

    // Toggle the clicked action menu
    actionMenu.style.display = actionMenu.style.display === 'block' ? 'none' : 'block';
}

// Click outside to close action menu
document.addEventListener('click', (event) => {
    if (!event.target.closest('.pod-card')) {
        document.querySelectorAll('.action-menu-pod').forEach(menu => {
            menu.style.display = 'none';
        });
    }
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
       // Toggle action menu visibility
       function toggleActionMenu(element) {
            const actionMenu = element.querySelector('.action-menu');
            const allActionMenus = document.querySelectorAll('.action-menu');
            
            // Hide other action menus
            allActionMenus.forEach(menu => {
                if (menu !== actionMenu) {
                    menu.style.display = 'none';
                }
            });

            // Toggle the clicked action menu
            actionMenu.style.display = actionMenu.style.display === 'block' ? 'none' : 'block';
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
        $('#response').html(`
    <div class="body-load">
        <div class="e-loadholder">
            <div class="m-loader">
                <span class="e-text">Analyzing</span>
            </div>
        </div>
        <div id="particleCanvas-Blue"></div>
        <div id="particleCanvas-White"></div>
    </div>
`);
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
<div id="aiResponseModal" class="modal2">
    <div class="modal-content">
        <div class="head-ai"><img src="crops/chatbot.png" alt=""></div>
        <span class="close">&times;</span>
        <div class="response-container">
        <div id="response"></div>
        </div>
        <input type="hidden" id="currentPestId" value="">
        <button  style="background: linear-gradient(to bottom right, #9CDC78, #74DCB0) !important;" id="takeNoteBtn" class="btn " >Take a Note</button>
    </div>
</div>
<style>
@keyframes outerRotate1 {
    0% {
        transform: translate(-50%, -50%) rotate(0);
    }
    100% {
        transform: translate(-50%, -50%) rotate(360deg);
    }
}

@keyframes outerRotate2 {
    0% {
        transform: translate(-50%, -50%) rotate(0);
    }
    100% {
        transform: translate(-50%, -50%) rotate(-360deg);
    }
}

@keyframes textColour {
    0% {
        color: #fff;
    }
    100% {
        color: #3bb2d0;
    }
}


.body-load {
    margin: 0;
    padding: 0;
    font-family: "Open Sans", sans-serif;
    width: 100vw;
    height: 100vh;
    background: #ffffff;
}

.e-loadholder {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 120px; /* Reduced size */
    height: 120px; /* Reduced size */
    border: 3px solid #1b5f70;
    border-radius: 60px;
    box-sizing: border-box;
}

.e-loadholder:after {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translateY(-50%);
    content: " ";
    display: block;
    background:  #fff;
    transform-origin: center;
    z-index: 0;
    width: 50px;
    height: 200%;
    animation: outerRotate2 30s infinite linear;
}

.m-loader {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100px; /* Reduced size */
    height: 100px; /* Reduced size */
    color: #888;
    text-align: center;
    border: 3px solid #23828e;
    border-radius: 50px;
    box-sizing: border-box;
    z-index: 20;
    text-transform: uppercase;
}

.m-loader:after {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translateY(-50%);
    content: " ";
    display: block;
    background:  #fff;
    transform-origin: center;
    z-index: -1;
    width: 50px;
    height: 106%;
    animation: outerRotate1 15s infinite linear;
}

.e-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 1.0rem; /* Reduced font size */
    line-height: 10px;
    animation: textColour 1s alternate linear infinite;
    display: flex;
    width: 70px; /* Reduced size */
    height: 70px; /* Reduced size */
    text-align: center;
    border: 3px solid #2ba3b8;
    border-radius: 35px;
    box-sizing: border-box;
    z-index: 20;
    align-items: center;
    justify-content: center;
}

.e-text:before,
.e-text:after {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translateY(-50%);
    content: " ";
    display: block;
    background:  #fff;
    transform-origin: center;
    z-index: -1;
}

.e-text:before {
    width: 110%;
    height: 40px;
    animation: outerRotate2 3.5s infinite linear;
}

.e-text:after {
    width: 40px;
    height: 110%;
    animation: outerRotate1 8s infinite linear;
}

#particleCanvas-White {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    height: 50%;
    opacity: 0.1;
}

#particleCanvas-Blue {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 150px; /* Reduced size */
    height: 150px; /* Reduced size */
}
.m-loader img{
    width: 100%;
    height: 100%;
}
.head-ai {
    border-radius: 50%;
    width: 100px;
    height: 100px;
    padding: 15px;
    top: -57px;
    left: 7%;
    position: absolute;
    background-color: white;
}
    .head-ai img{
        border-radius: 50%;
        width: 100%;
        height: 100%;
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

<script>
    particlesJS("particleCanvas-Blue", {
	particles: {
		number: {
			value: 100,
			density: {
				enable: true,
				value_area: 800
			}
		},
		color: {
			value: "#1B5F70"
		},
		shape: {
			type: "circle",
			stroke: {
				width: 0,
				color: "#000000"
			},
			polygon: {
				nb_sides: 3
			},
			image: {
				src: "img/github.svg",
				width: 100,
				height: 100
			}
		},
		opacity: {
			value: 0.5,
			random: false,
			anim: {
				enable: true,
				speed: 1,
				opacity_min: 0.1,
				sync: false
			}
		},
		size: {
			value: 10,
			random: true,
			anim: {
				enable: false,
				speed: 10,
				size_min: 0.1,
				sync: false
			}
		},
		line_linked: {
			enable: false,
			distance: 150,
			color: "#ffffff",
			opacity: 0.4,
			width: 1
		},
		move: {
			enable: true,
			speed: 0.5,
			direction: "none",
			random: true,
			straight: false,
			out_mode: "bounce",
			bounce: false,
			attract: {
				enable: false,
				rotateX: 394.57382081613633,
				rotateY: 157.82952832645452
			}
		}
	},
	interactivity: {
		detect_on: "canvas",
		events: {
			onhover: {
				enable: true,
				mode: "grab"
			},
			onclick: {
				enable: false,
				mode: "push"
			},
			resize: true
		},
		modes: {
			grab: {
				distance: 200,
				line_linked: {
					opacity: 0.2
				}
			},
			bubble: {
				distance: 1500,
				size: 40,
				duration: 7.272727272727273,
				opacity: 0.3676323676323676,
				speed: 3
			},
			repulse: {
				distance: 50,
				duration: 0.4
			},
			push: {
				particles_nb: 4
			},
			remove: {
				particles_nb: 2
			}
		}
	},
	retina_detect: true
});

particlesJS("particleCanvas-White", {
	particles: {
		number: {
			value: 250,
			density: {
				enable: true,
				value_area: 800
			}
		},
		color: {
			value: "#ffffff"
		},
		shape: {
			type: "circle",
			stroke: {
				width: 0,
				color: "#000000"
			},
			polygon: {
				nb_sides: 3
			},
			image: {
				src: "img/github.svg",
				width: 100,
				height: 100
			}
		},
		opacity: {
			value: 0.5,
			random: true,
			anim: {
				enable: false,
				speed: 0.2,
				opacity_min: 0,
				sync: false
			}
		},
		size: {
			value: 15,
			random: true,
			anim: {
				enable: true,
				speed: 10,
				size_min: 0.1,
				sync: false
			}
		},
		line_linked: {
			enable: false,
			distance: 150,
			color: "#ffffff",
			opacity: 0.4,
			width: 1
		},
		move: {
			enable: true,
			speed: 0.5,
			direction: "none",
			random: true,
			straight: false,
			out_mode: "bounce",
			bounce: false,
			attract: {
				enable: true,
				rotateX: 3945.7382081613637,
				rotateY: 157.82952832645452
			}
		}
	},
	interactivity: {
		detect_on: "canvas",
		events: {
			onhover: {
				enable: false,
				mode: "grab"
			},
			onclick: {
				enable: false,
				mode: "push"
			},
			resize: true
		},
		modes: {
			grab: {
				distance: 200,
				line_linked: {
					opacity: 0.2
				}
			},
			bubble: {
				distance: 1500,
				size: 40,
				duration: 7.272727272727273,
				opacity: 0.3676323676323676,
				speed: 3
			},
			repulse: {
				distance: 50,
				duration: 0.4
			},
			push: {
				particles_nb: 4
			},
			remove: {
				particles_nb: 2
			}
		}
	},
	retina_detect: true
});
</script>