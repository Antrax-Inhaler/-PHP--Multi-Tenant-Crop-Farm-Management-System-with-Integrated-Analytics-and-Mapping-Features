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
</style>
<!-- Navbar -->
<!-- Navbar -->
<div class="main-header2">
    <!-- Navbar Links -->
    <ul class="navbar-nav-custom">
        <li class="nav-item-custom">
            <a href="<?php echo base_url ?>nafa/?page=crops" class="nav-link nav-home">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item-custom">
    <a href="<?php echo base_url ?>nafa/?page=suggestions" class="nav-link nav-products">
        <i class="nav-icon fas fa-lightbulb"></i>
        <span>Farming Activity Suggestions</span>
    </a>
</li>
<li class="nav-item-custom">
    <a href="<?php echo base_url ?>nafa/?page=archive" class="nav-link nav-orders">
        <i class="nav-icon fas fa-book"></i>
        <span>Pest and Disease Archive</span>
    </a>
</li>

    </ul>
</div>
