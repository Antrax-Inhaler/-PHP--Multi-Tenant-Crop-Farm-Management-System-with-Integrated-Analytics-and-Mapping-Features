<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pest and Disease Reports</title>
<style>
    /* Custom Dropdown Styles */
    .dropdown-unique {
        position: relative;
        display: inline-block;
    }
    .dropdown-content-unique {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
        border-radius: 10px;
    }
    .dropdown-content-unique a {
        color: black;
        padding: 10px 16px;
        text-decoration: none;
        display: block;
        border-radius: 10px;
        margin: 2px 10px;
    }
    .dropdown-content-unique a:hover {
        background-color: #f1f1f1;
    }
    .dropdown-unique:hover .dropdown-content-unique {
        display: block;
    }
    .dropdown-unique:hover .dropdown-button-unique {
        background-color: #3e8e41;
    }

    /* Minimalistic Page Layout and Design */
    h3.title-unique {
        color: #333;
        text-align: center;
        margin-bottom: 20px;
    }

    .table-unique {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .table-unique th, .table-unique td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .table-unique th {
        background-color: #9CDC78;
        color: white;
    }

    .table-unique td:last-child {
        text-align: center;
    }

    .avatar-unique img {
        width: 45px;
        height: 45px;
        object-fit: cover;
        object-position: center center;
        border-radius: 50%;
    }

    .dropdown-button-unique {
        padding: 5px 10px;
        background-color: #00bfa5;
        color: #fff;
        border: none;
        border-radius: 10px;
        cursor: pointer;
    }

    .dropdown-button-unique:hover {
        background-color: #00796b;
    }

    @media (max-width: 600px) {
        .table-unique th, .table-unique td {
            padding: 10px;
        }
    }

    /* Status Capsules */
    .status-capsule {
        padding: 5px 10px;
        border-radius: 20px;
        color: white;
        font-weight: bold;
        text-align: center;
    }
    .status-pending {
        background-color: red;
    }
    .status-processing {
        background-color: orange;
    }
    .status-visited {
        background-color: yellow;
    }
    .status-resolved {
        background-color: green;
    }

    /* Image Viewer */
    .image-viewer {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 9000;
        justify-content: center;
        align-items: center;
    }
    .image-viewer img {
        max-width: 80%;
        max-height: 80%;
    }
    .image-viewer .close, .image-viewer .prev, .image-viewer .next {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: #fff;
        border: none;
        padding: 10px;
        cursor: pointer;
        border-radius: 50%;
    }
    .image-viewer .close {
        right: 20px;
        top: 20px;
        transform: none;
    }
    .image-viewer .prev {
        left: 20px;
    }
    .image-viewer .next {
        right: 20px;
    }
</style>
</head>
<body>
    <h3 class="title-unique">Pest and Disease Reports</h3>

    <table class="table-unique">
        <colgroup>
            <col width="5%">
            <col width="15%">
            <col width="15%">
            <col width="15%">
            <col width="15%">
            <col width="10%">
            <col width="20%">
            <col width="15%">
            <col width="15%">
            <col width="10%">
            <col width="10%">
            <col width="10%">
        </colgroup>
        <thead>
            <tr>
                <th>#</th>
                <th>Member</th>
                <th>Crop Name</th>
                <th>Crop Type</th>
                <th>Farm Name</th>
                <th>Pest or Disease</th>
                <th>Description</th>
                <th>Size of Affected Area</th>
                <th>Get Directions</th>
                <th>Report Date</th>
                <th>Status</th>
                <th>Action</th>
                <th>View Images</th>
            </tr>
        </thead>
        <tbody>
        <?php
$user_id = $_settings->userdata('id');

$query = "
    SELECT
        pd.id AS report_id,
        v.user_id, v.shop_owner,
        c.Name AS crop_name,
        c.Type AS crop_variety,
        f.Name AS farm_name,
        p.CropID,
        p.SizeOfAreaAffected,
        p.Name AS pestordisease,
        pd.description,
        f.latitude,
        f.longitude,
        pd.created_at,
        pd.status,
        p.Image1,
        p.Image2,
        p.Image3,
        p.Image4,
        p.Image5
    FROM 
        pestanddiseasereport pd
     JOIN 
        croppestdisease p ON pd.pestordisease_id = p.Id
    JOIN 
        crop c ON p.CropID = c.Id
    JOIN 
        farm f ON c.FarmId = f.id
    JOIN 
        vendor_list v ON c.VendorId = v.id
    WHERE 
        v.user_id = '{$user_id}'
    ORDER BY 
        pd.created_at DESC";

$result = $conn->query($query);
$i = 1;
while ($row = $result->fetch_assoc()):
    // Convert status to text with appropriate CSS class
    $status_text = '';
    $status_class = '';
    switch ($row['status']) {
        case 0:
            $status_text = 'Pending';
            $status_class = 'status-pending';
            break;
        case 1:
            $status_text = 'Processing';
            $status_class = 'status-processing';
            break;
        case 2:
            $status_text = 'Visited';
            $status_class = 'status-visited';
            break;
        case 3:
            $status_text = 'Resolved';
            $status_class = 'status-resolved';
            break;
        default:
            $status_text = 'Unknown';
            $status_class = '';
            break;
    }

    // Gather images
    $images = array_filter([
        $row['Image1'],
        $row['Image2'],
        $row['Image3'],
        $row['Image4'],
        $row['Image5']
    ]);
?>

                <tr>
                    <td class="text-center"><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($row['shop_owner']); ?></td>
                    <td><?php echo htmlspecialchars($row['crop_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['crop_variety']); ?></td>
                    <td><?php echo htmlspecialchars($row['farm_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['pestordisease']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo htmlspecialchars($row['SizeOfAreaAffected']); ?> Hectars</td>
                    <td>
                        <a href="#" onclick="getDirections(event, '<?php echo $row['latitude']; ?>', '<?php echo $row['longitude']; ?>')">Get Directions</a>
                    </td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td><span class="status-capsule <?php echo $status_class; ?>"><?php echo htmlspecialchars($status_text); ?></span></td>
                    <td align="center">
                        <button class="dropdown-button-unique edit-report" data-report-id="<?= htmlspecialchars($row['report_id']) ?>">Update</button>
                    </td>
                    <td align="center">
                        <button class="view-images" data-images="<?= htmlspecialchars(json_encode($images)) ?>">View Images</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Image Viewer -->
    <div class="image-viewer" id="image-viewer">
        <button class="close" id="close-viewer">X</button>
        <button class="prev" id="prev-image">&lt;</button>
        <img src="" alt="Image Viewer" id="viewer-image">
        <button class="next" id="next-image">&gt;</button>
    </div>

    <script>
        // JavaScript function to open Google Maps with directions
        function getDirections(event, latitude, longitude) {
            event.preventDefault();
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    const userLat = position.coords.latitude;
                    const userLng = position.coords.longitude;
                    const directionsUrl = `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${latitude},${longitude}&travelmode=driving`;
                    window.open(directionsUrl, '_blank');
                }, function () {
                    alert('Error getting your location. Please try again.');
                });
            } else {
                alert('Geolocation is not supported by your browser.');
            }
        }

        // Image Viewer Logic
        document.addEventListener('DOMContentLoaded', function() {
            const viewer = document.getElementById('image-viewer');
            const viewerImage = document.getElementById('viewer-image');
            const closeViewer = document.getElementById('close-viewer');
            const prevImage = document.getElementById('prev-image');
            const nextImage = document.getElementById('next-image');

            let images = [];
            let currentIndex = 0;

            document.querySelectorAll('.view-images').forEach(button => {
                button.addEventListener('click', () => {
                    images = JSON.parse(button.getAttribute('data-images'));
                    currentIndex = 0;
                    if (images.length > 0) {
                        viewerImage.src = '../' + images[currentIndex];
                        viewer.style.display = 'flex';
                    }
                });
            });

            closeViewer.addEventListener('click', () => {
                viewer.style.display = 'none';
            });

            prevImage.addEventListener('click', () => {
                if (images.length > 0) {
                    currentIndex = (currentIndex - 1 + images.length) % images.length;
                    viewerImage.src = '../' + images[currentIndex];
                }
            });

            nextImage.addEventListener('click', () => {
                if (images.length > 0) {
                    currentIndex = (currentIndex + 1) % images.length;
                    viewerImage.src = '../' + images[currentIndex];
                }
            });
        });

        // jQuery for modal
        $(document).ready(function(){
            $('.edit-report').click(function(){
                var reportId = $(this).attr('data-report-id');
                uni_modal('Update Report', "crops/edit_report.php?id=" + reportId, 'small');
            });
        });
    </script>
</body>
</html>
