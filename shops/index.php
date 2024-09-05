<style>
.shop-card {
    display: flex;
    flex-direction: row;
    width: 100%;
    max-width: 600px;
    border: 1px solid #ddd;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin: 10px;
    padding: 20px;
    background-color: #fff;
}
.shop-card img {
    max-width: 150px;
    max-height: 150px;
    border-radius: 15px;
    object-fit: cover;
    overflow: hidden;
}
.shop-details {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    margin-left: 20px;
    flex: 1;
}
.shop-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #333;
}
.shop-title i{
    font-size: 1.2rem;
}
.shop-type {
    font-size: 1rem;
    color: #555;
}
.shop-contact {
    font-size: 1rem;
    color: blue;
}
.shop-rating {
    font-size: 1rem;
    font-weight: bold;
    color: #00f;
}
.shop-action {
    display: flex;
    justify-content: flex-end;
    margin-top: 10px;
}
.btn-visit {
    background: #00bfa5;
    color: #fff;
    padding: 10px 15px;
    border: none;
    border-radius: 20px;
    cursor: pointer;
}
.btn-visit:hover {
    background: #00796b;
}
.star-rating {
    color: #FFD700; /* Default star color */
}
.star-rating.empty {
    color: #ccc; /* Empty star color */
}
@media (max-width: 1024px) {
    .shop-card img {
    max-width: 50px;
    max-height: 50px;
    border-radius: 15px;
    object-fit: cover;
    overflow: hidden;
}
.shop-title {
    font-size: 1rem;
    font-weight: 600;
    color: #333;
}
}
.search-container {
    position: absolute;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: center;
}

.search-input {
    background-color: rgba(255, 255, 255, 0.7);
    border-radius: 20px;
    border: none;
    padding: 10px 20px;
    width: 300px;
    outline: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding-right: 90px; /* Adjusted for button width */
    position: relative;
}

.clear-button, .search-button {
    position: absolute;
    right: 10px;
    border: none;
    background: transparent;
    padding: 10px;
    cursor: pointer;
    outline: none;
    margin-top: 3px;
}

.clear-button {
    right: 40px; /* Adjust to place clear button before search button */
}

.search-button i, .clear-button i {
    color: #555;
}

</style>
<?php
// PHP code to handle the search and retrieve the data
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$sql = "SELECT 
            vl.shop_name AS vendor_name,
            vl.id as shop_id,
            vl.contact,
            vl.avatar,
            stl.name AS shop_type_name,
            COALESCE(AVG(r.rating), 0) AS overall_rating
        FROM 
            vendor_list vl
        LEFT JOIN 
            shop_type_list stl ON vl.shop_type_id = stl.id
        LEFT JOIN 
            product_list pl ON vl.id = pl.vendor_id
        LEFT JOIN 
            review r ON pl.id = r.product_id
        WHERE 
            vl.shop_name LIKE '%$search%' OR
            vl.contact LIKE '%$search%' OR
            stl.name LIKE '%$search%'
        GROUP BY 
            vl.id, vl.shop_owner, vl.contact, vl.avatar, stl.name";

$result = $conn->query($sql);
?><div class="search-container">
<form action="" id="search-frm" class="custom-search-form">
    <div class="custom-input-group">
        <input type="search" id="search" class="search-input" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" placeholder="Search...">
        <button type="button" class="clear-button" onclick="clearSearchField()"><i class="fas fa-times"></i></button>
        <button type="submit" class="search-button"><i class="fa fa-search"></i></button>
    </div>
</form>
</div>
<div class="content py-3">
<div class="card card-primary rounded-0">
    <div class="card-header">
        <h5 class="card-title2"><i class="fas fa-shopping-bag"></i> <b>Shops List</b></h5>
    </div>
    <div class="card-body">
        <div class="row">
            <?php 
            if ($result->num_rows > 0):
                while($row = $result->fetch_assoc()): 
                    $overall_rating = number_format($row['overall_rating'], 2);
                    $full_stars = floor($overall_rating);
                    $empty_stars = 5 - $full_stars;
                ?>
                    <div class="col-md-4">
                        <div class="shop-card">
                            <img src="<?= htmlspecialchars($row['avatar']) ?>" alt="Avatar" class="img-fluid">
                            <div class="shop-details">
                                <div class="shop-title"><i class="fas fa-store"></i> <?= htmlspecialchars($row['vendor_name']) ?></div>
                                <div class="shop-type"><?= htmlspecialchars($row['shop_type_name']) ?></div>
                                <div class="shop-contact"><?= htmlspecialchars($row['contact']) ?></div>
                                <div class="container" style="margin-left: -12px;">
                                    <h5 class="shop-type">Ratings:
                                        <?php
                                        // Display full stars
                                        for ($i = 0; $i < $full_stars; $i++) {
                                            echo '<i class="fas fa-star star-rating"></i>';
                                        }
                                        // Display empty stars
                                        for ($i = 0; $i < $empty_stars; $i++) {
                                            echo '<i class="fas fa-star star-rating empty"></i>';
                                        }
                                        ?>
                                        (<?= $overall_rating ?>/5)
                                    </h5>
                                </div>
                                <div class="shop-action">
                                    <button class="btn-visit" data-shop-id="<?= htmlspecialchars($row['shop_id']) ?>">Visit Shop</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile;
            else: ?>
                <div class="col-12 text-center">
                    <img src="uploads/no_shops_yet.jpg" alt="No Shops Yet" class="img-fluid" style="max-width: 300px; margin-bottom: 20px; box-shadow: 10px 10px 15px rgba(0, 0, 0, 0.3);">
                    <h2>No Shops Yet</h2>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</div>
<script>
    function clearSearchField() {
        document.getElementById('search').value = '';
    }

    $(function() {
        $('#search-frm').submit(function(e) {
            e.preventDefault();
            var q = "search=" + $('#search').val();
            location.href = "./?page=shops&" + q;
        });
    });
    document.querySelectorAll('.btn-visit').forEach(card => {
        card.addEventListener('click', function() {
            const shopId = this.getAttribute('data-shop-id');
            location.href = '<?php echo base_url ?>shops/?page=shops_info&id=' + shopId;
        });
    });
</script>