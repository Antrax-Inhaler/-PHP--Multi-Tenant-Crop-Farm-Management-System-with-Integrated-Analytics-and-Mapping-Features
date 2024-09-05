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
        .product_card{
            width: 220px;
            height: 315px;
            background-color: white;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.267);
            border-radius:  20px;
            margin-bottom: 20px;
        }
        .product-photo-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .product-profile {
            width: 100%;
            height: 210px;
            background-color: #45a0496e;
            background-size: cover;
            background-position: center;
            border-radius:  15px;
            cursor: pointer;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.267);


        }

        .product-profile img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .product_card_data{
            margin-top: -6px;
            margin-left: 6px;
            margin-right: 6px;
            padding: 0;
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
            font-size: small;
            color: rgb(153, 153, 153);
        }
        .card_product_name{
            font-size: 15px;
            font-weight: 500;
            padding-top: 4px;
        }
        p{
            color: black;
        }
</STYle>
<table>
<tr>
    <td colspan="2" style="text-align: center;">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 col-sm-12">
                <form action="" id="search-frm">
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Search</span></div>
                        <input type="search" id="search" class="form-control" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
                        <div class="input-group-append"><span class="input-group-text"><i class="fa fa-search"></i></span></div>
                    </div>
                </form>
            </div>
        </div>
    </td>
</tr>

    <tr>
        <td>
                <div class="card-body">
                    <div class="list-group">
                        <div class="list-group-item list-group-item-action">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input custom-control-input-primary custom-control-input-outline cat_all" type="checkbox" id="cat_all" <?= !is_array($category_ids) && $category_ids =='all' ? "checked" : "" ?>>
                                <label for="cat_all" class="custom-control-label"> All</label>
                            </div>
                        </div>
                        <?php 
                        $categories = $conn->query("SELECT * FROM `category_list` where delete_flag = 0 and status = 1 order by `name` asc ");
                        while($row = $categories->fetch_assoc()):
                        ?>
                        <div class="list-group-item list-group-item-action">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input custom-control-input-primary custom-control-input-outline cat_item" type="checkbox" id="cat_item<?= $row['id'] ?>" <?= in_array($row['id'],explode(',',$category_ids)) ? "checked" : '' ?> value="<?= $row['id'] ?>">
                                <label for="cat_item<?= $row['id'] ?>" class="custom-control-label"> <?= $row['name'] ?></label>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            
        </td>
        <td>
        <div class="card-body">
                    <div class="container-fluid">
    
                            
              
                        <div class="row" id="product_list">
                            <?php 
                            $swhere = "";
                            if(!empty($category_ids)):
                            if($category_ids !='all'){
                                $swhere = " and p.category_id in ({$category_ids}) ";
                            }
                            if(isset($_GET['search']) && !empty($_GET['search'])){
                                $swhere .= " and (p.name LIKE '%{$_GET['search']}%' or p.description LIKE '%{$_GET['search']}%' or c.name LIKE '%{$_GET['search']}%' or v.shop_name LIKE '%{$_GET['search']}%') ";
                            }

                            $products = $conn->query("SELECT p.*, v.shop_name as vendor, c.name as `category` FROM `product_list` p inner join vendor_list v on p.vendor_id = v.id inner join category_list c on p.category_id = c.id where p.delete_flag = 0 and p.`status` =1 {$swhere} order by RAND()");
                            while($row = $products->fetch_assoc()):
                            ?>
                            <div class="product_section">
                            <div  class="product_card">
    <a href="./?page=products/view_product&id=<?= $row['id'] ?>">
        <div class="product-photo-container">
            <div class="product-profile" style="background-image: url('<?= validate_image($row['image_path']) ?>');"></div>
        </div>
        <table class="product_card_data">
            <tr>
                <td colspan="2">
                    <div class="card_product_name">
                        <p><?= $row['name'] ?></p>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: right;">
                    <div>
                   
                        <p class="card-text price">₱<?= format_num($row['price']) ?></p>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card_stock_data">
                        <p class="card-text stock">Vendor: <?= $row['vendor'] ?></p>
                    </div>
                </td>
                <td>
                    <div class="card_stock_data">
                        <p class="card-text stock">Category: <?= $row['category'] ?></p>
                    </div>
                </td>
            </tr>
        </table>
    </a>
</div>
</div>

                            <?php endwhile; ?>
                            <?php else: ?>
                                <div class="col-12 text-center">
                                    Pleas select atleast 1 product category
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>                
        </td>
    </tr>
</table>
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