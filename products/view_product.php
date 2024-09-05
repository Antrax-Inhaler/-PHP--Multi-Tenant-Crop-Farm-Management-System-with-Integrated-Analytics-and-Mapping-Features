<!-- Include Bootstrap CSS and JS -->
<!-- Include Bootstrap CSS and JS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<style>
.carousel {
    position: relative;
    width: 100%;
    max-width: 100%; /* Ensure it doesn't overflow parent */
    height: 400px; /* Adjust height as needed */
    overflow: hidden;
}

.carousel-inner {
    position: relative;
    width: 100%;
    height: 100%;
}

.carousel-item {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: none;
}

.carousel-item.active {
    display: block;
}

.carousel-image {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Ensure images cover the entire carousel */
}

.carousel-control-prev,
.carousel-control-next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0, 0, 0, 0.3); /* Semi-transparent background */
    color: #fff; /* Text color */
    border: none;
    cursor: pointer;
    width: 50px; /* Adjust width as needed */
    height: 50px; /* Adjust height as needed */
    text-align: center;
    line-height: 50px; /* Center the icon vertically */
    font-size: 24px; /* Icon size */
}

.carousel-control-prev {
    left: 10px; /* Adjust left position */
}

.carousel-control-next {
    right: 10px; /* Adjust right position */
}

.carousel-control-icon {
    display: inline-block;
}

</style>

<?php
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT  p.*, v.shop_name as vendor, c.name as `category` FROM `product_list` p inner join vendor_list v on p.vendor_id = v.id inner join category_list c on p.category_id = c.id where p.delete_flag = 0 and p.id = '{$_GET['id']}'");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
        // Fetch reviews for the selected product
        $qry_reviews = $conn->query("SELECT r.*, cl.firstname,  cl.lastname FROM review r INNER JOIN client_list cl ON r.client_id = cl.id WHERE r.product_id = '{$id}' ORDER BY r.date_created DESC");
        
        // Calculate mean ratings
        $total_ratings = 0;
        $num_reviews = $qry_reviews->num_rows;
        while ($review = $qry_reviews->fetch_assoc()) {
            $total_ratings += $review['rating'];
        }
        $mean_rating = ($num_reviews > 0) ? round($total_ratings / $num_reviews, 2) : 0;

        // Reset the pointer of $qry_reviews to the beginning
        $qry_reviews->data_seek(0);
    }else{
        echo "<script> alert('Unknown Product ID.'); location.replace('./?page=products') </script>";
        exit;
    }
}else{
    echo "<script> alert('Product ID is required.'); location.replace('./?page=products') </script>";
    exit;
}
?>

   <!-- Include Font Awesome for star icons -->
   <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .star-rating {
            color: #FFD700; /* Default star color */
        }
        .star-rating.empty {
            color: #ccc; /* Empty star color */
        }
    #prod-img-holder {
        height: 45vh !important;
        width: calc(100%);
        overflow: hidden;
    }

    #prod-img {
        object-fit: scale-down;
        height: calc(100%);
        width: calc(100%);
        transition: transform .3s ease-in;
    }
    #prod-img-holder:hover #prod-img{
        transform:scale(1.2);
    }
</style>
    <div class="content py-3">
        <div class="card card-outline card-primary rounded-0 shadow">
            <div class="card-header">
                <h5 class="card-title"><b>Product Details</b></h5>
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <div id="msg"></div>
                    <div class="row">
                        <div class="col-lg-4 col-md-5 col-sm-12 text-center">
                        <?php
// Assuming $image_path, $image_path_2, $image_path_3, $image_path_4, $image_path_5 are fetched from the database
$image_paths = array_filter([$image_path, $image_path_2, $image_path_3, $image_path_4, $image_path_5]);
?>

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


                        </div>
                        <div class="col-lg-8 col-md-7 col-sm-12">
                            <h3><b><?= $name ?></b></h3>
                            <div class="container" style="margin-left: -8px;" >
                            <h5>Ratings:
                                    <?php
                                    // Calculate star rating based on mean_rating
                                    $full_stars = floor($mean_rating); // Number of full stars
                                    $empty_stars = 5 - $full_stars; // Number of empty stars

                                    // Display full stars
                                    for ($i = 0; $i < $full_stars; $i++) {
                                        echo '<i class="fas fa-star star-rating"></i>';
                                    }
                                    // Display empty stars
                                    for ($i = 0; $i < $empty_stars; $i++) {
                                        echo '<i class="fas fa-star star-rating empty"></i>';
                                    }
                                    ?>
                                    (<?= $mean_rating ?>/5)
                                </h5>
                            </div>
                        <div class="d-flex w-100">
                            <div class="col-auto px-0"><small class="text-muted">Vendor: </small></div>
                            <div class="col-auto px-0 flex-shrink-1 flex-grow-1"><p class="m-0"><small class="text-muted"><?= $vendor ?></small></p></div>
                        </div>
                        <div class="d-flex">
                            <div class="col-auto px-0"><small class="text-muted">Category: </small></div>
                            <div class="col-auto px-0 flex-shrink-1 flex-grow-1"><p class="m-0"><small class="text-muted"><?= $category ?></small></p></div>
                        </div>
                        <div class="d-flex">
                            <div class="col-auto px-0"><small class="text-muted">Price: </small></div>
                            <div class="col-auto px-0 flex-shrink-1 flex-grow-1"><p class="m-0 pl-3"><small class="text-primary"><?= format_num($price) ?></small></p></div>
                        </div>
                        <div class="row align-items-end">
                            <div class="col-md-3 form-group">
                                <input type="number" min = "1" id= 'qty' value="1" class="form-control rounded-0 text-center">
                            </div>
                            <div class="col-md-3 form-group">
                                <button class="btn btn-primary btn-flat" type="button" id="add_to_cart"><i class="fa fa-cart-plus"></i> Add to Cart</button>
                            </div>
                        </div>
                        <div class="w-100"><?= html_entity_decode($description) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Code to display customer reviews -->
<div class="container20" style="padding:30px" >
<h2>Customer Reviews</h2>
    <div class="row">
        <?php
        if ($qry_reviews->num_rows > 0) {
            while ($review = $qry_reviews->fetch_assoc()) {
                // Calculate star rating based on the review's rating
                $review_stars = $review['rating']; // Rating out of 5 stars

                // Fetch client avatar URL from client_list table
                $client_id = $review['client_id'];
                $qry_client = $conn->query("SELECT avatar FROM client_list WHERE id = '{$client_id}'");
                $client_avatar = ($qry_client->num_rows > 0) ? $qry_client->fetch_assoc()['avatar'] : '';

                // Display star icons based on the review's rating
                $full_stars = floor($review_stars); // Number of full stars
                $empty_stars = 5 - $full_stars; // Number of empty stars

                ?>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body d-flex align-items-center">
                            <?php if (!empty($client_avatar)) : ?>
                                <img src="<?= $client_avatar ?>" alt="Client Avatar" class="rounded-circle mr-3" style="width: 50px; height: 50px;">
                            <?php else : ?>
                                <div class="rounded-circle bg-secondary mr-3" style="width: 50px; height: 50px;"></div>
                            <?php endif; ?>
                            <div>
                                <h5 class="card-text"><?= $review['firstname'] ?> <?= $review['lastname'] ?></h5>
                                
                                <div class="d-flex align-items-center">
                                    <div class="mr-2">
                                        Rating:
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
                                    </div>
                                    <div class="text-muted">(<?= $review_stars ?>/5)</div>
                                </div>
                                <p class="card-text"><?= $review['comment'] ?></p>
                                <p class="card-text"><small class="text-muted">Posted on <?= date('F j, Y', strtotime($review['date_created'])) ?></small></p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<div class="col-12"><p>No reviews yet.</p></div>';
        }
        ?>
    </div>

    <div class="container text-center my-4">
    <ins class="adsbygoogle"
         style="display:block"
         data-ad-client="ca-pub-5217286547377656"
         data-ad-slot="your-ad-slot"
         data-ad-format="auto"></ins>
    <script>
        (adsbygoogle = window.adsbygoogle || []).push({});
    </script>
</div>

<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5217286547377656" crossorigin="anonymous"></script>



<script>
    function add_to_cart(){
        var pid = '<?= isset($id) ? $id : '' ?>';
        var qty = $('#qty').val();
        var el = $('<div>')
        el.addClass('alert alert-danger')
        el.hide()
        $('#msg').html('')
        start_loader()
        $.ajax({
            url:_base_url_+'classes/Master.php?f=add_to_cart',
            method:'POST',
            data:{product_id:pid,quantity:qty},
            dataType:'json',
            error:err=>{
                console.error(err)
                alert_toast('An error occurred.','error')
                end_loader()
            },
            success:function(resp){
                if(resp.status =='success'){
                    location.reload()
                }else if(!!resp.msg){
                    el.text(resp.msg)
                    $('#msg').append(el)
                    el.show('slow')
                    $('html, body').scrollTop(0)
                }else{
                    el.text("An error occurred. Please try to refresh this page.")
                    $('#msg').append(el)
                    el.show('slow')
                    $('html, body').scrollTop(0)
                }
                end_loader()
            }
        })
    }
    $(function(){
        $('#add_to_cart').click(function(){
            if('<?= $_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 3 ?>'){
                add_to_cart();
            }else{
                location.href = "./login.php"
            }
        })
    })
</script>
<script>
$(document).ready(function() {
    // Initialize Bootstrap Carousel
    $('#product-carousel').carousel({
        interval: 2000, // Set the interval for sliding (in milliseconds)
        wrap: true // Enable looping of carousel items
    });
});
</script>
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