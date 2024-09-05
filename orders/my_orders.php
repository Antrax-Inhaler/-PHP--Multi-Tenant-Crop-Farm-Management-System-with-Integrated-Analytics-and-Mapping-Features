<style>
    .order-card {
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
        box-shadow: 10px 10px 15px rgba(0, 0, 0, 0.3); /* Bottom right shadow */
    }
    .order-card img {
        max-width: 150px;
        max-height: 150px;
        border-radius: 15px;
        object-fit: cover;
        overflow: hidden;
    }
    .order-details {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        margin-left: 20px;
        flex: 1;
    }
    .order-ref-code {
        font-size: 0.8rem;
        color: #777;
        margin-bottom: 5px;
    }
    .order-title-price {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .order-title {
        font-size: 1rem;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 60%;
    }
    .order-price {
        font-size: 1.5rem;
        font-weight: 700;
        color: #333;
    }
    .order-description, .order-address {
        font-size: 0.9rem;
        color: #555;
    }
    .order-status {
        font-size: 1rem;
        display: inline-block;
        font-weight:bold;
    }
    .badge2 {
        padding: 10px 15px;
        border-radius: 20px;
        margin-right: 5px;
    }
    .order-action {
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }
    .order-action .btn {
        background: #00f;
        color: #fff;
        padding: 10px 15px;
        border: none;
        border-radius: 20px;
        cursor: pointer;
        margin-right: 5px;
    }
    .order-action .btn.view {
        background:#2ddc9a;
        font-weight:bold;
    }
    .content{
        background-color: #01E37F !;
    }
    .card{
        background-color: transparent;
    }
    .card-title2{
        text-align: center;
    }
    @media (max-width: 500px) {
            .order-card {
                flex-direction: column;
                align-items: center;
            }
            .order-card img {
                max-width: 100%;
                margin-bottom: 15px;
            }
            .order-details {
                margin-left: 0;
                align-items: center;
                text-align: center;
            }
            .order-title-price {
                flex-direction: column;
                align-items: center;
            }
            .order-title {
                max-width: 100%;
            }
        }
</style>

<div class="content py-3">
    <div class="card card-primary rounded-0">
        <div class="card-header">
            <h5 class="card-title2"><i class="fas fa-shopping-bag"></i> <b>My Orders</b></h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php 
                $orders = $conn->query("SELECT ol.*, p.name AS product_name, p.image_path, ol.total_amount, ol.date_created, ol.code, ol.status, ol.delivery_address
                FROM `order_list` ol 
                LEFT JOIN `order_items` oi ON ol.id = oi.order_id
                LEFT JOIN `product_list` p ON oi.product_id = p.id 
                WHERE ol.client_id = '{$_settings->userdata('id')}' 
                ORDER BY ol.status ASC, UNIX_TIMESTAMP(ol.date_created) DESC");

                if($orders->num_rows > 0):
                    while($row = $orders->fetch_assoc()): ?>
                        <div class="col-md-4">
                            <div class="order-card">
                                <img src="<?= $row['image_path'] ?>" alt="<?= $row['product_name'] ?>" class="img-fluid">
                                <div class="order-details">
                                    <div class="order-ref-code">Ref. Code: <?= $row['code'] ?></div>
                                    <div class="order-title-price">
                                        <div class="order-title"><?= $row['product_name'] ?></div>
                                        <div class="order-price">₱<?= format_num($row['total_amount']) ?></div>
                                    </div>
                                    <div class="order-description"><?= date("Y-m-d H:i", strtotime($row['date_created'])) ?></div>
                                    <div class="order-address"><?= $row['delivery_address'] ?></div>
                                    <div class="order-action">
                                        <div class="order-status">
                                            <?php 
                                            switch($row['status']){
                                                case 0:
                                                    echo '<span class="badge2 badge-secondary">Pending</span>';
                                                    break;
                                                case 1:
                                                    echo '<span class="badge2 badge-primary">Confirmed</span>';
                                                    break;
                                                case 2:
                                                    echo '<span class="badge2 badge-info">Packed</span>';
                                                    break;
                                                case 3:
                                                    echo '<span class="badge2 badge-warning">Out for Delivery</span>';
                                                    break;
                                                case 4:
                                                    echo '<span class="badge2 badge-success">Delivered</span>';
                                                    break;
                                                case 5:
                                                    echo '<span class="badge2 badge-danger">Cancelled</span>';
                                                    break;
                                                default:
                                                    echo '<span class="badge2 badge-light border">N/A</span>';
                                                    break;
                                            }
                                            ?>
                                        </div>
                                        <button type="button" class="btn view" data-id="<?= $row['id'] ?>" data-code="<?= $row['code'] ?>"><i class="fas fa-eye"> </i> View</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile;
                else: ?>
                    <div class="col-12 text-center">
                        <img src="uploads/no_orders_yet.jpg" alt="No Orders Yet" class="img-fluid" style="max-width: 300px; margin-bottom: 20px; box-shadow: 10px 10px 15px rgba(0, 0, 0, 0.3);">
                        <h2>No Orders Yet</h2>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(function(){
        $('.view').click(function(){
            uni_modal("View Order Details - <b>"+($(this).attr('data-code'))+"</b>","orders/view_order.php?id="+$(this).attr('data-id'),'mid-large')
        });
    });
</script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<div class="full-screen-content">
<?php 
$category_ids = isset($_GET['cids']) ? $_GET['cids'] : 'all';
?>
<STYle>
       .product_section{
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            gap: 20px;
            padding-right: 20px;
            
        }
        .product_card_parent{
            width: 235px;
            border-radius:  20px;
            margin-bottom: 20px;
            cursor: pointer;
            position: relative;
        }
        .product_card{
            width: 240px;
            height: 350px;
            border-radius:  20px;
            margin-bottom: 20px;
            border:  solid 1px  hsl(0, 0%, 93%);
            overflow: hidden;
            cursor: pointer;
            position: relative;
            background-color: white;
            box-shadow: 10px 10px 15px rgba(0, 0, 0, 0.3); /* Bottom right shadow */
        }
        .product_card:hover {
            box-shadow: 0 0 10px hsla(0, 0%, 0%, 0.1);
                }
                @media (max-width: 1024px) {
                    .product_card{
                        width: 295px;
                    }
                    
                }
        .product-photo-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .product-profile {
            margin: 10px;
            width: 90%;
            height: 210px;
            background-color: #45a0496e;
            background-size: cover;
            background-position: center;
            border-radius:  15px;
            cursor: pointer;
            background-image: url(uploads/12345.png);
        }

        .product-profile img {
            width: 90%;
            height: 90%;
            object-fit: cover;
        }
        .product_card_data{
            margin-top: -6px;
            margin-left: 6px;
            margin-right: 6px;
            padding: 0;
            width: 100%;
        }
        .product_card_data *{
            margin: 0%;
        }
        .card_cart_button{
            background-color: rgba(255, 255, 255, 0.836);
            width: 35px;
            height: 35px;
            border-radius: 50%;
            border: solid orange 2px;
            margin-top: 15px;
        }
        .btn_icon{
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        td{
            border: none;
        }
        .card_stock_data{
            font-size: 15px;
            color: rgb(153, 153, 153);
        }
        .card_product_vendor{
            font-size: 15px;
            font-weight: 500;
            padding-top: 4px;
            color: #00CC73;
        }
        .card_product_vendor i{
            color: #00CC73;
            font-size: 12px;
        }
        p{
            color: black;
        }
        tr{
            overflow: hidden;

        }
        td{
            overflow: hidden;

        }
        .price{
            font-size: 18px;
            font-weight: 600;
            padding-top: 4px;
        }
        .showcase-badge.angle {

    -webkit-transform: rotate(-45deg);
    -ms-transform: rotate(-45deg);
    transform: rotate(-45deg);
    text-transform: uppercase;
    font-size: 11px;
    padding: 5px 50px;
    margin-top: 18px;
    margin-left: -28px;
        }
        .showcase-badge {
    position: absolute;
    background: var(--ocean-green);
    font-size: 8;
    font-weight: 600;
    color: white;
    padding: 0 8px;
    -webkit-border-radius: var(--border-radius-sm);
    border-radius: var(--border-radius-sm);
    background-color: #00CC73;
}
.product-grid .showcase {
  border: 1px solid var(--cultured);
  -webkit-border-radius: var(--border-radius-md);
          border-radius: var(--border-radius-md);
  -webkit-transition: var(--transition-timing);
  -o-transition: var(--transition-timing);
  transition: var(--transition-timing);
}
:root {
    --white: #ffffff;
    --cultured: #f5f5f5;
    --border-radius-md: 8px;
}

.category {
    background: var(--white);
    position: fixed;
    top: 0;
    left: -100%;
    bottom: 0;
    min-width: 320px;
    padding: 30px;
    visibility: hidden;
    -webkit-transition: 0.5s ease;
    -o-transition: 0.5s ease;
    transition: 0.5s ease;
    z-index: 20;
}

.category-accordion-menu {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 7px 0;
}

.category-item {
    padding: 10px 0;
    border-bottom: 1px solid var(--cultured);
    font-size: 15px;
    font-weight: 500;
}

@media (min-width: 1024px) {
    .category {
        padding: 20px;
        margin-bottom: 30px;
        border: 1px solid var(--cultured);
        border-radius: var(--border-radius-md);
        left: 0;
        position: relative;
        visibility: visible;
        max-width: none;
        width: auto;
    }
}
/* General Reset and Box Sizing */
*, *::before, *::after {
    margin: 0;
    padding: 0;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
}

/* Custom Search Form Styles */
.custom-search-form {
    position: sticky;
    top: 0;
    display: flex;
    justify-content: center;
    width: 100%;
    padding: 10px;
    background-color: white;
}

/* Custom Input Group Styles */
.custom-input-group {
    position: relative;
    width: 100%;
    max-width: 600px; /* Fixed width for desktop */
    z-index: 1099;
    position: sticky;
    
    
}

.custom-search-field {
    color: hsl(0, 0%, 27%); /* onyx */
    padding: 30px 15px;
    border: 1px solid hsl(0, 0%, 93%); /* cultured */
    -webkit-border-radius: 10px; /* border-radius-md */
    border-radius: 10px; /* border-radius-md */
    width: 100%;
    padding-right: 90px; /* Space for the buttons */
    height: 40px;
}

.custom-clear-button,
.custom-search-button {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1rem; /* Adjust size as needed */
    color: hsl(0, 0%, 27%); /* onyx */
}

.custom-clear-button {
    right: 40px; /* Position of the clear button */
}

.custom-search-button {
    right: 30px; /* Position of the search button */
}

.custom-clear-button:focus,
.custom-search-button:focus {
    outline: none;
}

input[type="search"] {
    appearance: auto;
    box-sizing: border-box;
    padding-block: 1px;
    padding-inline: 2px;
}

/* Responsive Adjustments */
@media (min-width: 1024px) {
    .custom-search-field {
        font-size: 2.625rem; /* fs-1 */
    }
}

@media (min-width: 768px) {
    .custom-search-field {
        font-size: 2.375rem; /* fs-1 */
    }
}

@media (max-width: 600px) {
    .custom-input-group {
        max-width: 100%;
    }

    .custom-search-field {
        font-size: 1.875rem; /* fs-1 */
    }
}

.sidebar {
        width: 500px; /* Adjust width as needed */
        position: sticky;
        top: 20px; /* Adjust top spacing */
        height: calc(100vh - 40px); /* Adjust height to fit screen */
    }
    .sidebar-container {
        display: flex;
        gap: 20px;
    }


    .ribbon {
    position: absolute;
    top: -4px; /* Adjust to have a bit of excess at the top */
    right: 16;
    overflow: visible;
    position: absolute;
    z-index: 99;
    filter: drop-shadow(2px 3px 2px rgba(0, 0, 0, 0.5));
  
}

.ribbon .content {
    color: white;
    text-align: center;
    font-weight: 400;
    background: var(--color, #2ca7d8) linear-gradient(45deg, rgba(0, 0, 0, 0) 0%, rgba(255, 255, 255, 0.25) 100%);
    padding: 8px 2px 4px;
    clip-path: polygon(0 0, 100% 0, 100% 100%, 50% 100%, 0 100%);
    width: var(--width, 36px);
    min-height: var(--height, 49px);
        overflow: visible;
        filter: drop-shadow(2px 3px 2px rgba(0, 0, 0, 0.5));

}

.ribbon.check .content {
    clip-path: polygon(0 0, 100% 0, 100% calc(100% - 20px), 40% 100%, 0 calc(100% - 12px));

}

.sample {
    padding: 20px;
}
.ribnon_container{
    filter: drop-shadow(4px 6px 4px rgba(black, 1));
}
.header_banner {
            width: 100%;
            height: 30px;
            line-height: 30px; /* Vertically center text */
            text-align: center;
            font-size: 18px; /* Adjust as needed */
            color: white;
            background: linear-gradient(45deg, #ff4b2b, #ff416c, #ff4b2b);
            background-size: 400% 400%;
            animation: gradient 5s ease infinite, glow 1s ease-in-out infinite alternate;
            box-shadow: 0 0 15px rgba(255, 65, 108, 0.5);
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        @keyframes glow {
            0% {
                box-shadow: 0 0 5px rgba(255, 65, 108, 0.5), 0 0 10px rgba(255, 65, 108, 0.5), 0 0 15px rgba(255, 65, 108, 0.5), 0 0 20px rgba(255, 65, 108, 0.5);
            }
            100% {
                box-shadow: 0 0 10px rgba(255, 65, 108, 0.8), 0 0 20px rgba(255, 65, 108, 0.8), 0 0 30px rgba(255, 65, 108, 0.8), 0 0 40px rgba(255, 65, 108, 0.8);
            }
        }
</STYle>
<header class="header_banner" >Browse More Farmer's Products</header>

<div class="sidebar-container">
        <div class="card-body">
            <div class="container-fluid">
                <div class="row" id="product_list">
                <?php
$swhere = "";
if (!empty($category_ids)) {
    if ($category_ids != 'all') {
        $swhere = " and p.category_id in ({$category_ids}) ";
    }
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $swhere .= " and (p.name LIKE '%{$_GET['search']}%' or p.description LIKE '%{$_GET['search']}%' or c.name LIKE '%{$_GET['search']}%' or v.shop_name LIKE '%{$_GET['search']}%') ";
    }

    $products = $conn->query("SELECT p.*, v.shop_name as vendor, c.name as `category`, AVG(r.rating) as mean_rating
                        FROM `product_list` p 
                        INNER JOIN vendor_list v ON p.vendor_id = v.id 
                        INNER JOIN category_list c ON p.category_id = c.id 
                        LEFT JOIN review r ON p.id = r.product_id 
                        WHERE p.delete_flag = 0 AND p.`status` = 1 {$swhere} 
                        GROUP BY p.id 
                        ORDER BY RAND()");

    while ($row = $products->fetch_assoc()) {
        // Calculate mean rating for each product
        $mean_rating = ($row['mean_rating']) ? round($row['mean_rating'], 1) : 0;

        // Calculate if the product is new (within 10 days)
        $date_created = new DateTime($row['date_created']);
        $current_date = new DateTime();
        $interval = $date_created->diff($current_date);
        $is_new = $interval->days <= 10;

        // Determine if the product is from Agronet crop management
        $is_from_agronet = $row['from_agronet'] == 1 ? true : false;
?>
                            <div class="product_section">
                            <div class="product_card_parent">
                            <?php if ($is_from_agronet) : ?>
                    <div class="ribbon_container">
                        <div class="ribbon check" style="--color: #57c443;">
                            <div class="content">
                            <i class="fa fa-medal" aria-hidden="true"></i>
                            <svg width="24px" height="24px" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check" class="svg-inline--fa fa-check fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                    <path fill="currentColor" d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                                <div class="product_card">

                                    <?php if ($is_new): ?>
                                        <p class="showcase-badge angle black">New</p>
                                    <?php endif; ?>
                                    <div style="display: flex; justify-content: space-around;" >

                                    </div>

                                    <a href="./?page=products/view_product&id=<?= $row['id'] ?>">
                                        <div class="product-photo-container">
                                            <div class="product-profile" style="background-image: url('<?= validate_image($row['image_path']) ?>');"></div>
                                        </div>
                                        <table class="product_card_data">
                                            <tr>
                                                <td colspan="2"> 
                                                    <div class="card_product_vendor">
                                                        <p class="card-text stock"><i class="fas fa-store"></i> <?= $row['vendor'] ?></p>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="mean-rating">
                                                        <?php
                                                        $full_stars = floor($mean_rating); // Number of full stars
                                                        $half_star = ($mean_rating - $full_stars) >= 0.5 ? true : false; // Check for half star

                                                        // Full stars
                                                        for ($i = 0; $i < $full_stars; $i++) {
                                                            echo '<i class="fas fa-star text-warning" title=" ' . number_format($mean_rating, 1) . ' "></i>';
                                                        }

                                                        // Half star if applicable
                                                        if ($half_star) {
                                                            echo '<i class="fas fa-star-half-alt text-warning" title=" ' . number_format($mean_rating, 1) . ' " > </i>';
                                                        }
                                                        ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="card_stock_data">
                                                        <p><?= $row['name'] ?></p>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <div>
                                                        <p class="card-text price">₱<?= format_num($row['price']) ?>.00</p>
                                                    </div>                                                
                                                </td>
                                            </tr>
                                        </table>
                                    </a>
                                </div>
                            </div>
                            </div>

                        <?php } ?>
                    <?php } else { ?>
                        <div class="col-12 text-center">
                            Please select at least 1 product category.
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
                    </div>

<script>
    $(function(){
        if($('#cat_all').is(':checked') == true){
            $('.cat_item').prop('checked',true)
        }
        if($('.cat_item:checked').length == $('.cat_item').length){
            $('#cat_all').prop('checked',true)
        }
        $('.cat_item').change(function(){
            var ids = [];
            $('.cat_item:checked').each(function(){
                ids.push($(this).val())
            })
            location.href="./?page=products&cids="+(ids.join(","))
        })
        $('#cat_all').change(function(){
            if($(this).is(':checked') == true){
                $('.cat_item').prop('checked',true)
            }else{
                $('.cat_item').prop('checked',false)
            }
            $('.cat_item').trigger('change')
        })
        $('#search-frm').submit(function(e){
            e.preventDefault()
            var q = "search="+$('#search').val()
            if('<?= !empty($category_ids) && $category_ids !='all' ?>' == 1){
                q += "&cids=<?= $category_ids ?>"
            }
            location.href="./?page=products&"+q;

        })
    })
</script>
<script>
function clearSearchField() {
    document.getElementById('search').value = '';
}
document.getElementById('search-frm').addEventListener('submit', function(event) {
    event.preventDefault();
    var query = document.getElementById('search').value;
    if (query) {
        window.location.href = `search_results_page_url?search=${encodeURIComponent(query)}`;
    }
});
</script>
