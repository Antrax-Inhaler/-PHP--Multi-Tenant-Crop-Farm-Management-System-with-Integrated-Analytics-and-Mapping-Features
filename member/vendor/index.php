<body>
    <!-- Table layout -->
    <table style="width: 100%; height: 100%;">
        <!-- Top Bar -->
        <tr>
        <?php require_once('./vendor/inc/header.php') ?>

        </tr>
        <tr>
            <td>
                <?php require_once('./vendor/inc/topBarNav.php') ?>
            </td>
        </tr>
        <!-- Content -->
        <tr>
                          <section class="content">
                    <div class="container-fluid">
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
                </section>
        </tr>
    </table>
</body>
        <!-- /.content -->
  <!-- <div class="modal fade" id="uni_modal" role='dialog'>
    <div class="modal-dialog modal-md modal-dialog-centered rounded-0" role="document">
      <div class="modal-content rounded-0">
        <div class="modal-header rounded-0">
        <h5 class="modal-title"></h5>
      </div>
      <div class="modal-body rounded-0">
      </div>
      <div class="modal-footer rounded-0">
        <button type="button" class="btn btn-sm btn-flat btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
        <button type="button" class="btn btn-sm btn-flat btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal_second" role='dialog'>
    <div class="modal-dialog modal-md modal-dialog-centered rounded-0" role="document">
      <div class="modal-content rounded-0">
        <div class="modal-header rounded-0">
        <h5 class="modal-title"></h5>
      </div>
      <div class="modal-body rounded-0">
      </div>
      <div class="modal-footer rounded-0">
        <button type="button" class="btn btn-sm btn-flat btn-primary" id='submit' onclick="$('#uni_modal_second form').submit()">Save</button>
        <button type="button" class="btn btn-sm btn-flat btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal_right" role='dialog'>
    <div class="modal-dialog modal-full-height  modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header rounded-0">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="fa fa-arrow-right"></span>
        </button>
      </div>
      <div class="modal-body rounded-0">
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="viewer_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content rounded-0">
              <button type="button" class="btn-close" data-dismiss="modal"><span class="fa fa-times"></span></button>
              <img src="" alt="">
      </div>
    </div>
  </div>
  <div class="modal fade" id="confirm_modal" role='dialog'>
    <div class="modal-dialog modal-md modal-dialog-centered rounded-0" role="document">
      <div class="modal-content">
        <div class="modal-header rounded-0">
        <h5 class="modal-title">Confirmation</h5>
      </div>
      <div class="modal-body rounded-0">
        <div id="delete_content"></div>
      </div>
      <div class="modal-footer rounded-0">
        <button type="button" class="btn btn-sm btn-flat btn-primary" id='confirm' onclick="">Continue</button>
        <button type="button" class="btn btn-sm btn-flat btn-secondary" data-dismiss="modal">Close</button>
      </div>
      </div>
    </div>
  </div> -->
      </div>
      <!-- /.content-wrapper -->
   
  </body>
