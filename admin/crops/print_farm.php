<?php
$user_id = $_SESSION['userdata']['id'];

// Handle filtering by month
$filter_month = isset($_GET['month']) ? $_GET['month'] : '';

// SQL query for fetching farm data
$sql = "SELECT f.*, v.shop_owner AS Owner, GROUP_CONCAT(c.Name SEPARATOR ', ') AS Crops, COUNT(h.Id) AS HarvestCount
        FROM farm f
        LEFT JOIN crop c ON f.Id = c.FarmId
        LEFT JOIN harvest h ON c.Id = h.CropId
        LEFT JOIN vendor_list v ON f.VendorListId = v.id
        WHERE f.VendorListId IN (SELECT id FROM vendor_list WHERE user_id = '{$user_id}' AND delete_flag = 0)";

// Apply filter by month if specified
if (!empty($filter_month)) {
    $sql .= " AND MONTH(f.CreatedAt) = '$filter_month'";
}

$sql .= " GROUP BY f.Id
          ORDER BY f.Name ASC";

$qry = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }
        .img-farm {
            width: 100px;
            height: 100px;
            object-fit: cover;
            object-position: center center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>Farm List</h2>
    <?php if (!empty($filter_month)): ?>
        <h3>Filtered by Month: <?php echo date('F', mktime(0, 0, 0, $filter_month, 1)); ?></h3>
    <?php endif; ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Image</th>
                <th>Name</th>
                <th>Owner</th>
                <th>Description</th>
                <th>Crops</th>
                <th>Harvest Count</th>
                <th>Direction</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 1;
            while($row = $qry->fetch_assoc()):
            ?>
                <tr>
                    <td class="text-center"><?php echo $i++; ?></td>
                    <td class="text-center"><img src="<?php echo validate_image($row['Image']) ?>" class="img-farm img-thumbnail" alt="farm_image"></td>
                    <td><?php echo ucwords($row['Name']) ?></td>
                    <td><?php echo ucwords($row['Owner']) ?></td>
                    <td><?php echo $row['Description'] ?></td>
                    <td><?php echo $row['Crops'] ?></td>
                    <td><?php echo $row['HarvestCount'] ?></td>
                    <td>
                        <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo $row['Latitude'] ?>,<?php echo $row['Longitude'] ?>" target="_blank">Get Directions</a>
                    </td>
                    <td><?php echo $row['CreatedAt'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
