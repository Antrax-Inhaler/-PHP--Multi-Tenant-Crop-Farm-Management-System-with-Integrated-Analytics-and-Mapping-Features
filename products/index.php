<div class="full-screen-content">
<?php 
$category_ids = isset($_GET['cids']) ? $_GET['cids'] : 'all';
?>
<STYle>
        .content{
        background-color: white;
    }
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
            width: 235px;
            height: 350px;
            border-radius:  20px;
            margin-bottom: 20px;
            border:  solid 1px  hsl(0, 0%, 93%);
            overflow: hidden;
            cursor: pointer;
            position: relative;
            background-color: white;
            box-shadow: 10px 10px 15px rgba(0, 0, 0, 0.1); /* Bottom right shadow */

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
        .product_card:hover {
            box-shadow: 0 0 10px hsla(0, 0%, 0%, 0.1);
                }
                @media (max-width: 1024px) {
                    .product_card{
                        width: 180px;
                        height: 290px;
                        
                    }
                    .product_card_parent{
                        width: 180px;
                    }
                    .product_section{
                        padding-right: 2px;
                    }
                    .card-body{
                        padding: 5px;
                    }
                    .product-profile {

height: 140px;

}
.product_section {
    display: block;
    
}
                }
                
        
        .product-photo-container {
            display: flex;
            justify-content: center;
            align-items: center;
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
.category-container {
    position: absolute;
    top: 50px;
    left: 20px;
    background-color: rgba(255, 255, 255, 0.9);
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
    position: fixed;
    top: 80px;
    display: flex;
    justify-content: center;
    width: 100%;
    padding: 10px;
    background-color: transparent;
    z-index: 1000;
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

.menu-icon {
    position: fixed;
    bottom: 20px;
    left: 20px;
    background-color: rgba(255, 255, 255, 0.9);
    border-radius: 20px;
    padding: 10px;
    cursor: pointer;
    z-index: 3;
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
}
.category-container {
    position: absolute;
    top: 40px;
    left: 20px;
    background-color: #2ddc99ad;
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
.ad-banner {
            width: 100%;
            max-width: 728px;
            height: 90px;
            border: 1px solid #ccc;
            margin: 20px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f9f9f9;
            position: sticky;
    top: 50px;
        }

        .ad-banner .alt-content {
            display: none;
            align-items: center;
            justify-content: center;
            height: 100%;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
            padding: 5px;
        }

        .ad-banner.no-ad .alt-content {
            display: flex;
        }

        .ad-banner.no-ad iframe {
            display: none;
        }
</STYle>
<br>
<div class="ad-banner">
    <div class="alt-content">
        <img src="./uploads/1633049820_download (2).jpg" alt="Alternative Content" style="max-width: 100%; max-height: 100%;">
    </div>
    <!-- AdSense code -->
    <iframe src="https://www.google.com/adsense/new/ads/iframe" width="728" height="90" frameborder="0" scrolling="no"></iframe>
</div>

<div class="custom-search-container">
    <form action="" id="search-frm" class="custom-search-form">
        <div class="custom-input-group">
            <input type="search" id="search" class="custom-search-field" placeholder="Search...">
            <button type="button" class="custom-clear-button" onclick="clearSearchField()"></button>
            <button type="submit" class="custom-search-button"><i class="fa fa-search"></i></button>
        </div>
    </form>
</div>
<div class="menu-icon" id="menu-icon">
    <i class="fas fa-bars"></i>
    <span><b>Category</b></span>
</div>
<div class="category-container" id="category-container">
    <div><b>Category</b></div>
    <div class="category-accordion-menu">
        <div class="category-item">
            <input class="cat_all" type="checkbox" id="cat_all">
            <label for="cat_all">All</label>
        </div>
    </div>
    <?php 
    $categories = $conn->query("SELECT * FROM `category_list` WHERE delete_flag = 0 AND status = 1 ORDER BY `name` ASC");
    while($row = $categories->fetch_assoc()):
    ?>
    <div class="category-item">
        <input class="cat_item" type="checkbox" id="cat_item<?= $row['id'] ?>" value="<?= $row['id'] ?>">
        <label for="cat_item<?= $row['id'] ?>"><?= $row['name'] ?></label>
    </div>
    <?php endwhile; ?>
</div>
<div class="card-body">
    <div class="container-fluid">
        <div class="row" id="product_list">
            <!-- Products will be dynamically loaded here -->
        </div>
    </div>
</div>
<script>
  $(document).ready(function() {
    function fetchProducts() {
        var query = $('#search').val();
        var categories = [];
        
        $('.cat_item:checked').each(function() {
            categories.push($(this).val());
        });

        var category_ids = categories.join(',');

        $.ajax({
            url: 'products/fetch_products.php', // Your PHP script to fetch products
            method: 'GET',
            data: {
                search: query,
                category_ids: category_ids
            },
            success: function(data) {
                $('#product_list').html(data);
            }
        });
    }

    // Fetch all products on page load
    fetchProducts();

    $('#search-frm').submit(function(e) {
        e.preventDefault();
        fetchProducts();
    });

    $('#search').on('input', function() {
        fetchProducts();
    });

    $('.cat_item, #cat_all').change(function() {
        fetchProducts();
    });

    $('#menu-icon').click(function() {
        $('#category-container').toggle();
    });

    if ($('#cat_all').is(':checked')) {
        $('.cat_item').prop('checked', true);
    }

    $('.cat_item').change(function() {
        if ($('.cat_item:checked').length === $('.cat_item').length) {
            $('#cat_all').prop('checked', true);
        } else {
            $('#cat_all').prop('checked', false);
        }
    });

    $('#cat_all').change(function() {
        if ($(this).is(':checked')) {
            $('.cat_item').prop('checked', true);
        } else {
            $('.cat_item').prop('checked', false);
        }
        fetchProducts();
    });
});

function clearSearchField() {
    $('#search').val('');
    fetchProducts();
}

</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var adBanner = document.querySelector('.ad-banner');
        var adIframe = adBanner.querySelector('iframe');

        // Check if the ad iframe has loaded
        adIframe.onload = function() {
            if (!adIframe.contentDocument || adIframe.contentDocument.body.childElementCount === 0) {
                adBanner.classList.add('no-ad');
            }
        };

        // Fallback if the ad iframe doesn't load within a certain time
        setTimeout(function() {
            if (!adIframe.contentDocument || adIframe.contentDocument.body.childElementCount === 0) {
                adBanner.classList.add('no-ad');
            }
        }, 5000); // 5 seconds timeout
    });
</script>
