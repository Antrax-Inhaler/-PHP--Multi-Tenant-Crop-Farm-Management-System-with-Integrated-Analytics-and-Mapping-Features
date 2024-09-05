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

    .main-header2 {
        background-color: hsl(154, 100%, 40%);
        width: 100%;
        height: 35px; /* Adjust as needed */
        text-align: center;
        display: flex;
        justify-content: center; /* Center horizontally */
        align-items: center; /* Center vertically */
        padding-top: 15px;
    }

    .navbar-nav-custom {
        color: white;
        margin-right: 1rem;
        text-decoration: none;
        display: flex;
        text-align: center;
        list-style: none; /* Remove list points */
        padding: 0; /* Remove default padding */
    }

    .nav-item-custom {
        margin-left: 0.5rem;
        text-decoration: none;
    }

    .nav-link {
        color: white;
        text-decoration: none;
        display: inline-block; /* Display as inline-block for alignment */
        vertical-align: middle; /* Align vertically within the line */
    }

    .nav-link:hover,
    .nav-link:focus {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
    }

    .nav-icon {
        margin-right: 0.5rem;
    }
    @media (max-width: 1000px) {
  /* Hide desktop menu on mobile screens */
  .nav-link span {
    display: none;
  }

  /* Add a new element for the mobile menu icon */
  .mobile-menu-icon2 {
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    font-size: 1.5rem; /* Adjust font size as needed */
  }
}
.active{
    color: black;
}
</style>
<!-- Navbar -->
<div class="main-header2">
    <!-- Navbar Links -->
    <ul class="navbar-nav-custom">
        <li class="nav-item-custom">
            <a href="<?php echo base_url ?>vendor/?e-home" class="nav-link nav-home <?= $page == 'e-home' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item-custom">
            <a href="<?php echo base_url ?>vendor/?page=products" class="nav-link nav-products <?= $page == 'products' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-box"></i>
                <span>Product List</span>
            </a>
        </li>
        <li class="nav-item-custom">
            <a href="<?php echo base_url ?>vendor/?page=orders" class="nav-link nav-orders <?= $page == 'orders' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-list"></i>
                <span>Order List</span>
            </a>
        </li>
        <li class="nav-item-custom">
            <a href="<?php echo base_url ?>vendor/?page=reports/order_reports" class="nav-link nav-reports/order_reports <?= $page == 'reports/order_reports' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-file-alt"></i>
                <span>Monthly Order Report</span>
            </a>
        </li>
        <li class="nav-item-custom">
            <a href="<?php echo base_url ?>vendor/?page=categories" class="nav-link nav-categories <?= $page == 'categories' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-th-list"></i>
                <span>Category List</span>
            </a>
        </li>
    </ul>
</div>
<script>
    $(document).ready(function(){
      var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
      var s = '<?php echo isset($_GET['s']) ? $_GET['s'] : '' ?>';
      page = page.replace('/',"_");
      if(s!='')
        page = page+'_'+s;

      if($('.nav-link.nav-'+page).length > 0){
             $('.nav-link.nav-'+page).addClass('active')
        if($('.nav-link.nav-'+page).hasClass('tree-item') == true){
            $('.nav-link.nav-'+page).closest('.nav-treeview').siblings('a').addClass('active')
          $('.nav-link.nav-'+page).closest('.nav-treeview').parent().addClass('menu-open')
        }
        if($('.nav-link.nav-'+page).hasClass('nav-is-tree') == true){
          $('.nav-link.nav-'+page).parent().addClass('menu-open')
        }

      }
     
    })
  </script>