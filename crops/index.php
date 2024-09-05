<!-- member\crops\index.php -->
<!DOCTYPE html>
<html lang="en" style="height: 100%;">

<?php require_once('../crops/inc/header.php') ?>
<style>

</style>
<link rel="stylesheet" href="../assets/css/styles.css">
<?php require_once('../crops/inc/header2.php') ?>

<body>jasjHAJSJabhbj
    <!-- Include the top bar navigation specific to the crops section -->
    <?php require_once('../crops/inc/topBarNav.php'); ?>

    <!-- Main content section -->
    <div class="container">
        <!-- Content specific to the crops section goes here -->
        <h1>Crops Section</h1>
        <!-- Add more content as needed -->

        <!-- Include the content based on the navigation selection -->
        <?php 
                            $page = isset($_GET['page']) ? $_GET['page'] : 'home';  
                            if($_settings->chk_flashdata('success')):
                        ?>
                        <script>
                            alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
                        </script>
                        <?php endif;?>
                        <?php 
                            if(!file_exists($page.".php") && !is_dir($page)){
                                include '404.html';
                            } else {
                                if(is_dir($page))
                                    include $page.'/index.php';
                                else
                                    include $page.'.php';
                            }
                        ?>
    </div>

    <!-- Include the footer -->
    <?php require_once('../crops/inc/footer.php'); ?>
</body>
</html>
nbhasdabshjbj