<?php require_once('./inc/topBarNav.php') ?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<div class="content py-3">
    <div class="card card-primary rounded-0 shadow">
        <div class="card-header">
            <h5 class="card-title"><b>Products to Review</b></h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php 
                    $i = 1;
                    $client_id = $_settings->userdata('id');
                    $products = $conn->query("
                        SELECT ol.date_created AS date_delivered, ol.code AS order_code, p.name AS product_name, p.id AS product_id, ol.id AS order_id, p.image_path AS product_image
                        FROM `order_list` ol 
                        JOIN `order_items` oi ON ol.id = oi.order_id 
                        JOIN `product_list` p ON oi.product_id = p.id 
                        LEFT JOIN `review` r ON ol.id = r.order_id AND oi.product_id = r.product_id AND ol.client_id = r.client_id
                        WHERE ol.client_id = '{$client_id}' 
                        AND ol.status = 4 
                        AND r.id IS NULL
                        ORDER BY unix_timestamp(ol.date_created) DESC 
                    ");
                    while($row = $products->fetch_assoc()):
                ?>
                <div class="col-12 mb-3">
                    <div class="card shadow-sm h-100" style="background: white; border-radius: 20px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);">
                        <div class="row no-gutters">
                            <div class="col-md-4">
                                <div class="img-container" style="overflow: hidden; border-radius: 20px 0 0 20px;">
                                    <img src="<?= $row['product_image'] ?>" class="card-img" style="height: 100%; width: 100%; object-fit: cover;">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="card-body" style="">
                                    <h5 class="card-title"><?= $row['product_name'] ?></h5>
                                    <p class="card-text">Order Code: <?= $row['order_code'] ?></p>
                                    <p class="card-text">Date Delivered: <?= date("Y-m-d H:i", strtotime($row['date_delivered'])) ?></p>
                                    <button type="button" class="btn btn-primary review_product" data-product_id="<?= $row['product_id'] ?>" data-order_id="<?= $row['order_id'] ?>" data-product_name="<?= $row['product_name'] ?>">Write Review</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(function(){
        $('.review_product').click(function(){
            uni_modal("Write Review for - <b>"+($(this).attr('data-product_name'))+"</b>","review/manage_review.php?product_id="+$(this).attr('data-product_id')+"&order_id="+($(this).attr('data-order_id')),'mid-large')
        });
    });
</script>

<style>

    .card-body {
        overflow-x: auto;
    }

    .card-title {
        font-size: 1.25rem;
    }

    .card-text {
        font-size: 1rem;
    }

    .btn-primary {
        background: #00bfa5;
        border-color: #00bfa5;
        border-radius: 15px;
    }

    .btn-primary:hover {
        background: #00796b;
        border-color: #00796b;
    }

    .img-container {
        position: relative;
        padding-bottom: 56.25%;
        height: 0;
    }

    .img-container img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
