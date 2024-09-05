<?php
$category_ids = isset($_GET['cids']) ? $_GET['cids'] : 'all';
$swhere = "";

if (!empty($category_ids)) {
    if ($category_ids != 'all') {
        $swhere = " and p.category_id in ({$category_ids}) ";
    }
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $swhere .= " and (p.name LIKE '%{$_GET['search']}%' or p.description LIKE '%{$_GET['search']}%' or c.name LIKE '%{$_GET['search']}%' or v.shop_name LIKE '%{$_GET['search']}%') ";
    }
}

$products = $conn->query("SELECT p.*, v.shop_name as vendor, c.name as `category`, AVG(r.rating) as mean_rating, p.longitude, p.latitude
                          FROM `product_list` p 
                          INNER JOIN vendor_list v ON p.vendor_id = v.id 
                          INNER JOIN category_list c ON p.category_id = c.id 
                          LEFT JOIN review r ON p.id = r.product_id 
                          WHERE p.delete_flag = 0 AND p.`status` = 1 {$swhere} 
                          GROUP BY p.id 
                          ORDER BY RAND()");
$productData = [];
while ($row = $products->fetch_assoc()) {
    $mean_rating = ($row['mean_rating']) ? round($row['mean_rating'], 1) : 0;
    $row['mean_rating'] = $mean_rating;
    $productData[] = $row;
}
?><!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product Locations Map</title>
  <style>
body {
    margin: 0;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    position: relative;
}

.category-container {
    position: absolute;
    top: 20px;
    left: 20px;
    background-color: #21ffaa28;
    border-radius: 20px;
    padding: 20px;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 1; /* Ensure it's above the map */
}

.category-container b {
    display: block;
    margin-bottom: 10px;
}

.category-item {
    margin-bottom: 10px;
}

#map-container {
    flex: 1; /* Grow to fill remaining space */
    display: flex;
    position: relative;
}

#map {
    flex: 1; /* Allow map to fill available space */
    width: 100%; /* Full width of the container */
    height: 100vh; /* Full height of the viewport */
}

.search-container {
    position: absolute;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 2;
    display: flex;
}

.search-input {
    background-color: rgba(255, 255, 255, 0.7);
    border-radius: 20px;
    border: none;
    padding: 10px 20px;
    width: 300px;
    outline: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding-right: 50px;
}

.search-button {
    background-color: rgba(255, 255, 255, 0.7);
    border: none;
    border-radius: 50%;
    padding: 10px;
    margin-left: -40px;
    cursor: pointer;
    outline: none;
}

.search-button i {
    color: #555;
}

.infowindow-content {
    display: flex;
    width: 300px;
    padding: 10px;
    background-color: white;
    border-radius: 5px;
    cursor: pointer;
}

.image-container {
    width: 120px;
    height: 120px;
    border-radius: 2px;
    background-color: gold;
    background-repeat: no-repeat;
    background-size: cover;
    flex-shrink: 0;
}

.product-details {
    margin-left: 10px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.product_name {
    font-size: 14px;
    font-weight: bold;
}

.card-text.store {
    font-size: 12px;
}

.product-ratings {
    font-size: 12px;
    color: #FFD700;
}

.card-text.price {
    font-size: 14px;
    font-weight: bold;
}

.directions-link {
    color: blue;
    text-decoration: underline;
    cursor: pointer;
}

.add_cart {
    border: none;
    border-radius: 5px;
    background-color: #00CC73;
    color: white;
    padding: 4px;
    margin: -5px;
    box-shadow: #00CC73;
}

.add_cart {
    box-shadow: #00CC73;
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

.menu-icon {
    position: absolute;
    top: 20px;
    left: 20px;
    background-color: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    padding: 10px;
    cursor: pointer;
    z-index: 3;
    display: none;
}

.menu-icon i {
    font-size: 24px;
}

@media (max-width: 768px) {
    .category-container {
        display: none;
    }
    .menu-icon {
        display: block;
    }
    .image-container {
    width: 50px;
    height: 50px;
    border-radius: 2px;
    background-color: gold;
    background-repeat: no-repeat;
    background-size: cover;
    flex-shrink: 0;
}
}
element.style {
    padding-top: 0px;
    min-width: 549px !important;
    max-width: 549px !important;

    max-height: 536px;
}
</style>
</head>
<body>
<style>
    /* Set map container size */
    #map {
      height: calc(100vh - 50px); /* Adjust map height */
      width: 100%; /* Full width */
    }

    /* Navigation header styles */
    #nav-header {
      height: 50px; /* Fixed height for header */
      width: 100%; /* Full width */
      background-color: #333; /* Header background color */
      color: #fff; /* Header text color */
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 20px;
      box-sizing: border-box;
    }

    /* Navigation link styles */
    .nav-link {
      text-decoration: none;
      color: #fff; /* Link text color */
      font-size: 16px;
      display: flex;
      align-items: center;
    }

    /* Navigation link hover effect */
    .nav-link:hover {
      color: #ddd; /* Hover text color */
    }

    /* Icon styles */
    .icon {
      margin-right: 5px;
      font-size: 24px;
    }

    /* Search input style */
    #searchInput {
      width: 300px; /* Adjust width as needed */
      padding: 8px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    .flex{
      display: flex;
      justify-content: flex-start;
    }
    @media (max-width: 768px) {
  /* Hide desktop menu on mobile screens */
  .labelmap {
    display: none;
  }
  #searchInput{
    width: 100%;
  }
}
.active{

  color: #2ddc9a;}
  .container-fluid{
    padding-right: 0px;
    padding-left: 0px;
  }
  .centerer{
        width: 100% !important;
    }
    #nav-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px;
}

.filter-container {
  position: relative;
}

#filterToggleBtn {
  background-color: #4CAF50;
  color: white;
  padding: 8px 16px;
  border: none;
  cursor: pointer;
  border-radius: 5px;
}

.filter-dropdown {
  position: absolute;
  top: 40px;
  right: 0;
  background-color: white;
  border: 1px solid #ccc;
  border-radius: 5px;
  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
  padding: 15px;
  width: 300px;
  z-index: 1000;
}

.filter-dropdown .close-btn {
  position: absolute;
  top: 5px;
  right: 5px;
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
}

.hidden {
  display: none;
}

#map {
  height: 100vh; /* Ensure the map takes up full height */
  width: 100%;
}

  </style>
<div id="nav-header">
<div class="flex">
<a href="<?php echo base_url ?>./?page=map" class="nav-link active">
        <div class="icon"><i class="fas fa-shopping-cart"></i></div> <!-- Icon representing ecommerce -->
        <div class="labelmap">Ecommerce Map</div>
    </a>
    <a href="<?php echo base_url ?>./?page=map/farm-map" class="nav-link">
        <div class="icon"><i class="fas fa-seedling"></i></div> <!-- Icon representing farming/agriculture -->
        <div class="labelmap">Farm Map</div>
    </a>

</div>



  <div style="display: flex; justify-content: center;" >
  <input type="text" id="searchInput" placeholder="Search by crop name, type, planting date, etc.">

  <div class="filter-container">
    <button id="filterToggleBtn">Filters</button>

    <div id="filterDropdown" class="filter-dropdown hidden">
      <button id="closeFilterBtn" class="close-btn">&times;</button>
      <select id="cropNameSelect">
        <option value="">Select Crop Name</option>
        <?php
          $cropNameSql = "SELECT DISTINCT Name FROM crop WHERE delete_flag = 0 AND is_deleted = 0";
          $cropNameResult = $conn->query($cropNameSql);
          if ($cropNameResult->num_rows > 0) {
            while($nameRow = $cropNameResult->fetch_assoc()) {
              echo "<option value='{$nameRow['Name']}'>{$nameRow['Name']}</option>";
            }
          }
        ?>
      </select>

      <select id="cropTypeSelect" disabled>
        <option value="">Select Crop Type</option>
      </select>

      <div>
        <label for="plantingDateFrom">Planting Date From:</label>
        <input type="date" id="plantingDateFrom">
      </div>

      <div>
        <label for="plantingDateTo">Planting Date To:</label>
        <input type="date" id="plantingDateTo">
      </div>

      <div>
        <label for="datePlantedFrom">Date Planted From:</label>
        <input type="date" id="datePlantedFrom">
      </div>

      <div>
        <label for="datePlantedTo">Date Planted To:</label>
        <input type="date" id="datePlantedTo">
      </div>

      <input type="number" id="sizeOfPlantationFrom" placeholder="Size Of Plantation From" step="any">
      <input type="number" id="sizeOfPlantationTo" placeholder="Size Of Plantation To" step="any">
      <button id="filterButton">Filter</button>
    </div>
  </div>

  </div>
</div>

<div id="map-container">
    <div class="menu-icon" id="menu-icon">
        <i class="fas fa-bars"></i>
    </div>
    <div class="search-container">
        <input type="text" class="search-input" placeholder="Search...">
        <button class="search-button"><i class="fas fa-search"></i></button>
    </div>
    <div class="category-container" id="category-container">
        <div><b>Category</b></div>
        <div class="category-accordion-menu">
            <div class="category-item">
                <input class="cat_all" type="checkbox" id="cat_all" <?= !is_array($category_ids) && $category_ids == 'all' ? "checked" : "" ?>>
                <label for="cat_all">All</label>
            </div>
        </div>
        <?php 
        $categories = $conn->query("SELECT * FROM `category_list` where delete_flag = 0 and status = 1 order by `name` asc ");
        while($row = $categories->fetch_assoc()):
        ?>
        <div class="category-item">
            <input class="cat_item" type="checkbox" id="cat_item<?= $row['id'] ?>" <?= in_array($row['id'],explode(',',$category_ids)) ? "checked" : '' ?> value="<?= $row['id'] ?>">
            <label for="cat_item<?= $row['id'] ?>"><?= $row['name'] ?></label>
        </div>
        <?php endwhile; ?>
    </div>
    <div id="map"></div>
</div>

<script>
const products = <?php echo json_encode($productData); ?>;

function initMap() {
    const map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 13.232900, lng: 121.156900 }, // Default center coordinates (adjust as needed)
        zoom: 16, // Adjust the zoom level as needed
        gestureHandling: 'greedy'
    });

    products.forEach(product => {
        const marker = new google.maps.Marker({
            position: { lat: parseFloat(product.latitude), lng: parseFloat(product.longitude) },
            map: map,
            title: product.name, // Use product name as marker title
            icon: {
                url: product.image_path,
                scaledSize: new google.maps.Size(50, 50), // Adjust size as needed
            },
        });

        const infoWindowContent = `
            <div class="infowindow-content">
                <div class="image-container" style="background-image: url('${product.image_path}');"></div>
                <div class="product-details">
                    <div class="card-text store"><i class="fas fa-store"></i> ${product.vendor}</div>
                    <div class="product-ratings">${getStars(product.mean_rating)}</div>
                    <div class="product_name">${product.name}</div>
                    <div class="card-text price">₱${product.price}.00</div>
                    <div style="display: flex; justify-content: space-around; gap: 10px;">
                        <a href="#" class="directions-link" data-lat="${product.latitude}" data-lng="${product.longitude}" onclick="getDirections(event, ${product.latitude}, ${product.longitude})">Get Directions</a>
                        <button onclick="window.location.href='./?page=products/view_product&id=${product.id}'" class="add_cart"><i class="fas fa-cart-arrow-down"></i></button>
                    </div>
                </div>
            </div>`;

        const infoWindow = new google.maps.InfoWindow({
            content: infoWindowContent
        });

        // Open the info window immediately after adding the marker
        infoWindow.open(map, marker);

        // Optionally, you can also add a click listener to the marker to toggle the info window
        marker.addListener("click", () => {
            infoWindow.open(map, marker);
        });
    });
}

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

function getStars(rating) {
    if (rating === 0 || rating === null) {
        return 'No reviews yet';
    }

    let stars = '';
    for (let i = 0; i < Math.floor(rating); i++) {
        stars += '<i class="fas fa-star text-warning"></i>';
    }
    if (rating - Math.floor(rating) >= 0.5) {
        stars += '<i class="fas fa-star-half-alt text-warning"></i>';
    }
    return stars;
}

// Menu toggle
document.getElementById('menu-icon').addEventListener('click', function() {
    const categoryContainer = document.getElementById('category-container');
    if (categoryContainer.style.display === 'none' || categoryContainer.style.display === '') {
        categoryContainer.style.display = 'block';
    } else {
        categoryContainer.style.display = 'none';
    }
});
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCPgOaKhjwvksUVP6qBQpjdq3bTQa57NuQ&callback=initMap" async></script>
</body>
</html>
