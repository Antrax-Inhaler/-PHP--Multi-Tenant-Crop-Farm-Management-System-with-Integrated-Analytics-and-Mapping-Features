<?php
require_once('./../config.php');

// Fetch all products without filters for initial display
$all_products = $conn->query("SELECT p.*, v.shop_name as vendor, c.name as `category`, AVG(r.rating) as mean_rating
                            FROM `product_list` p 
                            INNER JOIN vendor_list v ON p.vendor_id = v.id 
                            INNER JOIN category_list c ON p.category_id = c.id 
                            LEFT JOIN review r ON p.id = r.product_id 
                            WHERE p.delete_flag = 0 AND p.status = 1
                            GROUP BY p.id 
                            ORDER BY RAND()");

// Display all products initially
while ($row = $all_products->fetch_assoc()) {
    display_product($row);
}

// Now apply search and category filters if specified
$search = $_GET['search'] ?? '';
$category_ids = $_GET['category_ids'] ?? '';

$swhere = "WHERE p.delete_flag = 0 AND p.status = 1";

if ($category_ids !== '' && $category_ids !== 'all') {
    $swhere .= " AND p.category_id IN ($category_ids)";
}

if ($search !== '') {
    $swhere .= " AND (p.name LIKE '%$search%' OR p.description LIKE '%$search%' OR c.name LIKE '%$search%' OR v.shop_name LIKE '%$search%')";
}

$filtered_products = $conn->query("SELECT p.*, v.shop_name as vendor, c.name as `category`, AVG(r.rating) as mean_rating
                                FROM `product_list` p 
                                INNER JOIN vendor_list v ON p.vendor_id = v.id 
                                INNER JOIN category_list c ON p.category_id = c.id 
                                LEFT JOIN review r ON p.id = r.product_id 
                                $swhere
                                GROUP BY p.id 
                                ORDER BY RAND()");

if ($filtered_products->num_rows > 0) {
    // Clear the previous display if there are filtered results
    echo '<script>document.querySelectorAll(".product_section").forEach(el => el.remove());</script>';

    // Display filtered products
    while ($row = $filtered_products->fetch_assoc()) {
        display_product($row);
    }
} else {
    echo "<p>No products found matching your criteria.</p>";
}

// Function to display a product
function display_product($row) {
    $mean_rating = ($row['mean_rating']) ? round($row['mean_rating'], 1) : 0;
    $date_created = new DateTime($row['date_created']);
    $current_date = new DateTime();
    $interval = $date_created->diff($current_date);
    $is_new = $interval->days <= 10;
    $is_from_agronet = $row['from_agronet'] == 1 ? true : false;

    echo '<div class="product_section">';
    echo '<div class="product_card_parent">';
    if ($is_from_agronet) {
        echo '<div class="ribbon_container">';
        echo '<div class="ribbon check" style="--color: #57c443;">';
        echo '<div class="content">';
        echo '<i class="fa fa-medal" aria-hidden="true"></i>';
        echo '<svg width="24px" height="24px" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check" class="svg-inline--fa fa-check fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">';
        echo '<path fill="currentColor" d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z"></path>';
        echo '</svg>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    echo '<div class="product_card">';
    if ($is_new) {
        echo '<p class="showcase-badge angle black">New</p>';
    }
    echo '<a href="./?page=products/view_product&id=' . $row['id'] . '">';
    echo '<div class="product-photo-container">';
    echo '<div class="product-profile" style="background-image: url(' . validate_image($row['image_path']) . ');"></div>';
    echo '</div>';
    echo '<table class="product_card_data">';
    echo '<tr><td colspan="2"><div class="card_product_vendor"><p class="card-text stock"><i class="fas fa-store"></i> ' . $row['vendor'] . '</p></div></td></tr>';
    echo '<tr><td><div class="mean-rating">';
    $full_stars = floor($mean_rating);
    $half_star = ($mean_rating - $full_stars) >= 0.5;
    for ($i = 0; $i < $full_stars; $i++) {
        echo '<i class="fas fa-star text-warning" title=" ' . number_format($mean_rating, 1) . ' "></i>';
    }
    if ($half_star) {
        echo '<i class="fas fa-star-half-alt text-warning" title=" ' . number_format($mean_rating, 1) . ' "></i>';
    }
    echo '</div></td></tr>';
    echo '<tr><td><div class="card_stock_data"><p>' . $row['name'] . '</p></div></td></tr>';
    echo '<tr><td colspan="2"><div><p class="card-text price">₱' . format_num($row['price']) . '.00</p></div></td></tr>';
    echo '<tr><td><div class="card_stock_data"><p>Stock:' . $row['stock_amount'] . '</p></div></td></tr>';
    echo '</table>';
    echo '</a>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}
?>
