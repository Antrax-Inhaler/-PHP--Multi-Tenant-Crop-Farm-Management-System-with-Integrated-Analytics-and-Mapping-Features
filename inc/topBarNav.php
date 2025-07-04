<style>
    .user-img {
      position: absolute;
      height: 27px;
      width: 27px;
      object-fit: cover;
      left: -7%;
      top: -12%;
    }

    .btn-rounded {
      border-radius: 50px;
    }

    #login-nav {
      position: fixed !important;
      top: 0 !important;
      z-index: 1038;
      padding: 0.3em 2.5em !important;
    }

    #top-Nav {
      top: 2.65em;
    }

    .text-sm .layout-navbar-fixed .wrapper .main-header ~ .content-wrapper,
    .layout-navbar-fixed .wrapper .main-header.text-sm ~ .content-wrapper {
      margin-top: calc(3.6) !important;
      padding-top: calc(3.2em) !important;
    }

    .btn-transparent {
      background-color: transparent;
      border: 1px solid #fff;
      color: inherit;
      padding: 0.375rem 0.75rem;
    }

    .btn-transparent:hover {
      background-color: #2ddc9a;
      color: #fff;
    }

    .dropdown-menu {
      min-width: 100%;
    }

    .dropdown-item {
      color: #000;
    }

    .dropdown-item:hover {
      background-color: #2ddc9a;
      color: #fff;
    }

    .dropdown-item i {
      margin-right: 0.5rem;
      vertical-align: middle;
    }

    .bg-color-custom {
      background-color: #102419 !important;
    }

    .social-icons {
      display: flex;
      align-items: center;
    }

    .social-icon {
      display: flex;
      width: 26px;
      height: 26px;
      background-color: #fff;
      border-radius: 10px;
      margin-left: 5px;
      margin-right: 5px;
      justify-content: center;
      text-align: center;
      transition: background-color 0.3s ease;
      align-items: center;
    }

    .social-icon i {
      font-size: 18px;
      color: #000;
    }

    .social-icon:hover {
      background-color: #2ddc9a;
    }

    .right_icons {
      display: flex;
      justify-content: flex-start;
    }

    .glow-green {
      box-shadow: 0 0 10px rgba(45, 220, 154, 0.8);
    }

    .navbar-nav .nav-item .nav-link.active {
      color: green !important;
    }

    .dropdown {
      position: relative;
      display: inline-block;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      background-color: #f9f9f9;
      min-width: 160px;
      box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
      z-index: 1;
    }

    .dropdown-content a {
      color: black;
      padding: 12px 16px;
      text-decoration: none;
      display: block;
    }

    .dropdown-content a:hover {
      background-color: #2ddc9a;
      color: #fff;
    }

    .dropdown-toggle {
      cursor: pointer;
    }

    .show {
      display: block;
    }
    .dropdown-menu {
    display: none;
    position: absolute;
    right: 0;
    z-index: 1000;
  }

  .dropdown-menu.show {
    display: block;
  }
  .truncate-1{
    color: white !important;
  }
  </style>
      <nav class="w-100 px-2 py-1 position-fixed top-0 bg-dark text-light" id="login-nav">
        <div class="d-flex justify-content-between w-100">
          <div>
            <p class="m-0 truncate-1"><small><?= $_settings->info('name') ?></small></p>
          </div>
          <div>
          <?php if($_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 3): ?>
            <div class="dropdown">
  <a href="javascript:void(0)" class="dropdown-toggle text-reset text-decoration-none" id="userDropdownToggle" aria-haspopup="true" aria-expanded="false">
    <span class="mx-2"><img src="<?= validate_image($_settings->userdata('avatar')) ?>" class="img-thumbnail rounded-circle" alt="User Avatar" id="client-img-avatar"></span>
    <span class="mx-2"><?= !empty($_settings->userdata('username')) ? $_settings->userdata('username') : $_settings->userdata('email') ?></span>
  </a>
  <div class="dropdown-menu dropdown-menu-right" id="userDropdownMenu" aria-labelledby="userDropdownToggle">
    <a class="dropdown-item" href="./?page=manage_account">Manage Account</a>
    <a class="dropdown-item" href="<?= base_url.'classes/Login.php?f=logout_client' ?>">Logout</a>
  </div>
</div>

      <?php else: ?>
              <div class="dropdown">
            <button class="btn btn-transparent dropdown-toggle" style="padding: 3px;" type="button" id="loginDropdown">Login As</button>
            <div class="dropdown-content" id="loginDropdownContent">
              <a class="dropdown-item" href="./login.php">Customer</a>
              <a class="dropdown-item" href="./vendor">Member</a>
              <a class="dropdown-item" href="./admin">Association</a>
              <a class="dropdown-item" href="./nafa">NAFA</a>
            </div>
          </div>
            <?php endif; ?>
          </div>
        </div>
      </nav>
      <nav class="main-header navbar navbar-expand-md navbar-light border-0 text-sm shadow" id='top-Nav' style="background: linear-gradient(to bottom right, #9CDC78, #74DCB0) !important;" >
        
        <div class="container" style="background: linear-gradient(to bottom right, #9CDC78, #74DCB0) !important;">
          <a href="./" class="navbar-brand">
            <img src="<?php echo validate_image($_settings->info('logo'))?>" alt="Site Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span><?= $_settings->info('short_name') ?></span>
          </a>

         

          <div class="collapse navbar-collapse order-3" id="navbarCollapse">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a href="./" class="nav-link <?= isset($page) && $page == 'home' ? "active" : "" ?>"><i class="fas fa-home"></i> Home</a>
        </li>
        <li class="nav-item">
            <a href="./?page=products" class="nav-link <?= isset($page) && $page == 'products' ? "active" : "" ?>"><i class="fas fa-box"></i> Products</a>
        </li>
        <li class="nav-item">
            <a href="./?page=map" class="nav-link <?= isset($page) && $page == 'map' ? "active" : "" ?>"><i class="fas fa-map-marker-alt"></i> Map Shop</a>
        </li>
        <li class="nav-item">
    <a href="./?page=calendar" class="nav-link <?= isset($page) && $page == 'calendar' ? "active" : "" ?>">
        <i class="fas fa-calendar-alt"></i> Calendar
    </a>
</li>

        <?php if ($_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 3) : ?>
            <li class="nav-item">
                <?php
                $cart_count = $conn->query("SELECT sum(quantity) FROM `cart_list` where client_id = '{$_settings->userdata('id')}'")->fetch_array()[0];
                $cart_count = $cart_count > 0 ? $cart_count : 0;
                ?>
                <a href="./?page=orders/cart" class="nav-link <?= isset($page) && $page == 'orders/cart' ? "active" : "" ?>"><span class="badge badge-secondary rounded-cirlce"><?= format_num($cart_count) ?></span> Cart</a>
            </li>
            <li class="nav-item">
                <a href="./?page=orders/my_orders" class="nav-link <?= isset($page) && $page == 'orders/my_orders' ? "active" : "" ?>"><i class="fas fa-shopping-bag"></i> My Orders</a>
            </li>
            <li class="nav-item">
                <a href="./?page=review" class="nav-link <?= isset($page) && $page == 'review' ? "active" : "" ?>"><i class="fas fa-star"></i> Review Items</a>
            </li>
            <?php
            // Check if the client has any followed crops
            $followed_count = $conn->query("SELECT COUNT(*) FROM `interested_clients` WHERE client_id = '{$_settings->userdata('id')}'")->fetch_array()[0];
            if ($followed_count > 0) : ?>
                <li class="nav-item">
                    <a href="./?page=followed_crops" class="nav-link <?= isset($page) && $page == 'followed_crops' ? "active" : "" ?>"><i class="fas fa-seedling"></i> Followed Crops</a>
                </li>
            <?php endif; ?>
        <?php endif; ?>
        <!-- <li class="nav-item">
            <a href="./?page=about" class="nav-link <?= isset($page) && $page == 'about' ? "active" : "" ?>"><i class="fas fa-info-circle"></i> About Us</a>
        </li> -->
        <li class="nav-item">
            <a href="./?page=tutorial" class="nav-link <?= isset($page) && $page == 'tutorial' ? "active" : "" ?>"><i class="fas fa-question-circle"></i> Tutorial</a>
        </li>
    </ul>
</div>

          <!-- Right navbar links -->
          <div class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
            <button class="navbar-toggler order-1 border-0 text-sm" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
          </div>
        </div>
      </nav>
            <?php if ($_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 3) : ?>
        <?php include'chat3.php' ?>
        <?php endif; ?>
      <!-- /.navbar -->
      <script>
        $(function(){
          
        })
      </script>
      <script>
  document.addEventListener("DOMContentLoaded", function() {
    var loginDropdown = document.getElementById('loginDropdown');
    var loginDropdownContent = document.getElementById('loginDropdownContent');
    var userDropdown = document.getElementById('dropdownMenuButton');
    var userDropdownContent = document.getElementById('userDropdownContent');

    if (loginDropdown) {
      loginDropdown.addEventListener('click', function() {
        loginDropdownContent.classList.toggle('show');
      });
    }

    if (userDropdown) {
      userDropdown.addEventListener('click', function() {
        userDropdownContent.classList.toggle('show');
      });
    }

    window.addEventListener('click', function(event) {
      if (!event.target.matches('.dropdown-toggle')) {
        if (loginDropdownContent) loginDropdownContent.classList.remove('show');
        if (userDropdownContent) userDropdownContent.classList.remove('show');
      }
    });
  });
</script>

<!-- <script>
  document.addEventListener("DOMContentLoaded", function() {
    var userDropdownToggle = document.getElementById('userDropdownToggle');
    var userDropdownMenu = document.getElementById('userDropdownMenu');

    if (userDropdownToggle) {
      userDropdownToggle.addEventListener('click', function(event) {
        event.preventDefault();
        userDropdownMenu.classList.toggle('show');
      });
    }

    window.addEventListener('click', function(event) {
      if (!event.target.closest('.dropdown')) {
        userDropdownMenu.classList.remove('show');
      }
    });
  });
</script> -->
