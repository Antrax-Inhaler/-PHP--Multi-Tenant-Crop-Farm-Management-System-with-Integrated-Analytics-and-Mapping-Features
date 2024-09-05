
<style>
    
:root {

/**
 * colors
 */

--spanish-gray: hsl(0, 0%, 60%);
--sonic-silver: hsl(0, 0%, 47%);
--eerie-black: hsl(0, 0%, 13%);
--salmon-pink: hsl(154, 100%, 40%);
--sandy-brown: hsl(29, 90%, 65%);
--bittersweet: hsl(0, 100%, 70%);
--ocean-green: hsl(152, 51%, 52%);
--davys-gray: hsl(0, 0%, 33%);
--cultured: hsl(0, 0%, 93%);
--white: hsl(0, 100%, 100%);
--onyx: hsl(0, 0%, 27%);

/**
 * typography
 */

--fs-1: 1.563rem;
--fs-2: 1.375rem;
--fs-3: 1.25rem;
--fs-4: 1.125rem;
--fs-5: 1rem;
--fs-6: 0.938rem;
--fs-7: 0.875rem;
--fs-8: 0.813rem;
--fs-9: 0.75rem;
--fs-10: 0.688rem;
--fs-11: 0.625rem;

--weight-300: 300;
--weight-400: 400;
--weight-500: 500;
--weight-600: 600;
--weight-700: 700;

/**
 * border-radius
 */

--border-radius-md: 10px;
--border-radius-sm: 5px;

/**
 * transition 
 */

--transition-timing: 0.2s ease;

}
body{
    font-family: Poppins;
}
a { text-decoration: none; }

li { list-style: none; }
        .index_ui{
            font-family: Poppins, Arial, "Helvetica Neue", Helvetica, sans-serif;
            padding: 0pc;
        }
        .head1{
            padding-left: 100px;
            padding-right: 100px;
            border-bottom: solid .01px rgb(175, 175, 175);
            display: flex;
            align-items: center;
             margin-left: auto;
             justify-content: space-between;
        }
        .head2{

            border-bottom: solid .01px rgb(175, 175, 175);
            display: flex;
             align-items: center;
             justify-content: space-between;
        }
        @media (max-width: 1000px) {
  /* Hide desktop menu on mobile screens */
  .head2 {
    padding-left: 20px;
    padding-right: 4px;
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
@media (min-width: 1000px) {
  /* Hide desktop menu on mobile screens */
  .mobile-bottom-navigation {
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
        .head3{
            padding-left: 100px;
            padding-right: 100px;
            position: relative;
            border-bottom: solid .01px rgb(175, 175, 175);
            align-items: center;
            gap: 30px;
        }
        .left-content {
    /* You can add styles for the left content here */
        }

        .right-content {
        display: flex;
        align-items: center;
        }
        
        .language_select{
            border: solid white;
        }
        language_div{
            display: flex;
        align-items: center;
        }
        .img_verified{
            width: 15px;
            margin-bottom: 10px;
        }
        .login-profile-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 8px;
}

.login-profile {
    width: 50px;
    height: 50px;
    background-color: #45a0496e;
    border-radius: 50%;
    border: dashed;
    background-size: cover;
    background-position: center;
    cursor: pointer;
}

.login-profile img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}
.header-social-container .social-link {
    padding: 5px;
    background: hsl(0, 0%, 95%);
    border-radius: var(--border-radius-sm);
    color: var(--sonic-silver);
    transition: var(--transition-timing);
  }

  .header-social-container .social-link:hover {
    background: var(--salmon-pink);
    color: var(--white);
  }
  .header-social-container {
    display: flex;
    align-items: center;
    gap: 5px;
  }

  .desktop-menu-category-list {
    position: relative;
    display: flex;
    justify-content: space-around;
    align-items: center;
    gap: 30px;
  }
  .menu-category .accordion-menu {
  width: 100%;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
      -ms-flex-pack: justify;
          justify-content: space-between;
  -webkit-box-align: center;
  -webkit-align-items: center;
      -ms-flex-align: center;
          align-items: center;
}
.dropdown-list {
    position: absolute;
    top: 100%;
    left: 0;
    width: 200px;
    background: var(--white);
    padding: 20px 0;
    border-radius: var(--border-radius-md);
    border: 1px solid var(--cultured);
    box-shadow: 0 3px 5px hsla(0, 0%, 0%, 0.1);
    transform: translateY(50px);
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: var(--transition-timing);
    z-index: 5;
  }
  .mobile-bottom-navigation { display: none; }


  .desktop-menu-category-list { gap: 45px; }

  .dropdown-list .dropdown-item a {
    color: var(--sonic-silver);
    font-size: var(--fs-7);
    text-transform: capitalize;
    padding: 4px 20px;
    transition: var(--transition-timing);
  }

  .dropdown-list .dropdown-item a:hover { color: var(--salmon-pink); }
  .desktop-menu-category-list .menu-category:not(:nth-child(2)) { position: relative; }


  /* .desktop-menu-category-list .menu-category > .menu-title:hover { color: var(--salmon-pink); } */
  .desktop-menu-category-list .menu-category > .menu-title:hover,
.desktop-menu-category-list .menu-category > .menu-title.active {
    color: var(--salmon-pink);
}



  .menu-bottom .menu-category { border-bottom: none; }
/* registration form */
.registration {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #ffffff;
    font-family: roboto;
}

.registration-container {
    width: 380px;
    padding: 20px;
    border-radius: 35px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.5);
    background: linear-gradient(to bottom right, #9CDC78, #9CDC78, #93DC86, #8BDC92, #74DCB0);
    padding-right: 70px;
    padding-left: 50px;
    padding-bottom: 50px;
    /* make the paading for left right buttom equal */
}

.form-input {
    padding: 12px; 
    margin-bottom: 10px;
    border: 1px solid #ffffff;
    border-radius: 14px;
    width: 100%;
    box-shadow: 0 2px 16px rgba(66, 66, 66, 0.2);
    margin-top: 10px;
    margin-right: 30px;
    font-size: 16px;
}
.short-input {
    padding: 12px; 
    margin-bottom: 10px;
    border: 1px solid #ffffff;
    border-radius: 14px;
    width: 41%;
    box-shadow: 0 2px 16px rgba(66, 66, 66, 0.2);
    margin-top: 10px;
    margin-right: 6px;
    font-size: 16px;
}

.green-button {
    width: 110px;
    padding: 10px;
    background-color: #ffffff;
    color: #22A18E;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    font-size: large;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}
.blue-button{
    width: 110px;
    padding: 10px;
    background-color: #014D64;
    color: #ffffff;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    font-size: large;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}
.green-button:hover {
    background-color: #45a049;
    color: white;
}
.blue-button:hover {
    background-color: #03A4CE;
    color: white;
}
.form-a{
    color: black;
    padding-right: 25px;
    font-size: 23px;
}
.form-input::placeholder{
    color: #000000;
}
.short-input::placeholder{
    color: #000000;
}

.login-profile-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 8px;
}

.login-profile {
    width: 90px;
    height: 90px;
    background-color: #45a0496e;
    border-radius: 50%;
    border: dashed;
    background-size: cover;
    background-position: center;
    cursor: pointer;
}

.login-profile img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

input[type="file"] {
    display: none;
}

.brgy-select{
padding: 12px; 
margin-bottom: 10px;
border: 1px solid #ffffff;
border-radius: 14px;
width: 100%;
box-shadow: 0 2px 16px rgba(66, 66, 66, 0.2);
margin-top: 10px;
font-size: 16px;
}
.role-select{
padding: 12px; 
margin-bottom: 10px;
border: 1px solid #ffffff;
border-radius: 14px;
width: 100%;
box-shadow: 0 2px 16px rgba(66, 66, 66, 0.2);
margin-top: 10px;
font-size: 16px;
}
.unchecked{
width: 13px; 
height: 13px; 
background-color: #ffffff; 
border-radius: 50%;
display: inline-block;
}
.checked{
    width: 13px; 
    height: 13px; 
    background-color: #00790c; 
    border-radius: 50%;
    display: inline-block;
}

/* crop management */
.crop_management{
    font-family: Poppins;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
.crop_management_section {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-start;
    gap: 20px;
    padding: 20px;
}
.btn_addcrop{
    width: 500px;
    height: 850px;
    border: dashed rgb(78, 78, 78) 2px;
    border-radius: 40px;
    background-color: white;
    display: flex;
    justify-content: center;
    align-items: center;
}
.crop_management_card{
    width: 500px;
    height: 850px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 40px;
    text-align: center;
    padding-left: 70px;
    padding-right: 70px;
    align-items: center;
    padding-top: 30px;
}
.carousel-inner {
    border-radius: 20px;
    overflow: hidden;
    max-height: 200px;
}
.carousel-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.carousel-control-prev, .carousel-control-next {
    background: transparent;
    border: none;
    color: black;
    font-size: 24px;
    width: 5%;
}
.add_symbol{
    font-size: 150px;
    margin-bottom: 0;
}
.crop_type_and_name{
    display: flex;
    font-size: 20px;
}
.crop_manager_button{
    display: flex;
    gap: 18px;
    text-align: center;
    margin-top: 100px;
}
.manage_yield_btn, .manage_pest_btn, .manage_harvest_btn, .manage_sell_btn{
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background-color: #00FF00; /* Green color */
    border: none;
}
.reference_btn{
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #ff8800; /* Green color */
    border: none;
    padding: 10px;
}
.btn_icon{
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}
.btn_icon2{
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    
}
.manage_pest_btn {
    background-color: #FF0000; /* Red color */
}
.manage_harvest_btn {
    background-color: #FFFF00; /* Yellow color */
}
.manage_sell_btn {
    background-color: #0099CC; /* Blue color */
}
.add_crop_image {
width: 300px;
height: 200px;
padding: 10px; 
margin: 0;
border: dashed 2px;
border-radius: 7%;
box-sizing: border-box;
background-color: transparent; 
color: black;
font-size: 16px; 
}
.crop_info_table{
border: 1px black;
width: 100%;
}
.crop_column_1{
width: 50px;
}
.crop_info_table td{
text-align: left;
}
.crop_column_3{
width: 50px;
}

/* community */
.feed{
    max-width: 700px;
    height: 100%;
    background-color: rgb(233, 238, 241);
    padding: 10px;
    border-radius: 10px;
}
.create_discussion_container{
    min-height: 230px;
    background-color: white;
    padding: 10px;
    border-radius: 10px;
    margin-bottom: 10px;
    box-shadow: 0 2px 8px rgba(66, 66, 66, 0.2);
}
.login-profile-container_community {
display: flex;
align-items: center;
margin: 8px;
}
.img_verified{
        width: 15px;
        margin-bottom: 10px;
        padding-top: 15px;
    }
.login-profile_community {
width: 75px;
height: 75px;
background-color: #45a0496e;
border-radius: 50%;
background-size: cover;
background-position: center;
cursor: pointer;
border: none;
}
.create_discussion_title{
width: 85%;
border: solid rgb(145, 142, 142) 1px;
border-radius: 10px;
padding: 5px;
font-size: 15px;
}
.create_discussion_content{
width: 85%;
border: solid rgb(145, 142, 142) 1px;
border-radius: 15px;
padding: 5px;
font-size: 15px;
}
.role_box{
height: 30px;
background-color: green;
color: white;
padding: 2px;
font-size: 15px;
border: none;
padding-left: 3px;
padding-right: 3px;
}
.post-button {
margin-top: 12px;
    padding: 8px 16px;
    background-color: #014D64;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

.post-button:hover {
    background-color: #22A18E;
}
.create_discussion_image{
    width: 80%;
    height: 20px;
    margin-right: 30px;
}
.post_date{
    font-size: 13px;
    color: #2b2ff0;
    
}
.card_community_content{
    color: #1a1919;
    font-size: 15px;
}
.reply-button{
    padding: 5px 12px;
    background-color: #014D64;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
}
.vote-container {
display: flex;
flex-direction: column;
align-items: center;
justify-content: center;
font-size: 18px;
}
.reply_box{
width: 85%;
border: solid rgb(145, 142, 142) 1px;
border-radius: 15px;
padding: 5px;
font-size: 15px;
}
.post_content_picture_container{
width: 70% ;
height: 70%;
background-color: #45a0496e;
border-radius: 5px;
background-size: cover;
background-position: center;
border: none;
}
.lower_table_com_card{
display: flex;
}
.community_card_organizer{
width: 100%;
}
.upvote-button,
.downvote-button {
background-color: transparent;
border: none;
border-radius: 10px;
cursor: pointer;
padding: 5px 10px;
font-size: 30px;
}
.upvote-button:active{
color: green;
}
.downvote-button:active{
color: red;
}

.vote-count {
padding: 10px 0;
}

/* Styling for hover effects */
.upvote-button:hover,
.downvote-button:hover {
background-color: #f0f0f0;
border-radius: 50%;
}
.modal__input,
.modal__textarea {
    padding: 12px; 
    margin-bottom: 10px;
    border: 1px solid #ffffff;
    border-radius: 14px;
    width: 100%;
    box-shadow: 0 2px 16px rgba(66, 66, 66, 0.2);
    margin-top: 10px;
    margin-right: 30px;
    font-size: 16px;
}

.modal__input:focus,
.modal__textarea:focus {
    outline: none;
    border-color: #22A18E;
}
.active_list{
    width: 350px;
background-color: white;
box-shadow: 0 2px 16px rgba(66, 66, 66, 0.2);
border-radius: 10px;
padding: 10px;
}

.online_indicator {
    position: absolute;
bottom: 0;
right: 0;
width: 20px;
height: 20px;
border-radius: 50%;
background-color: rgb(0, 255, 0);
margin-right: 6px;
top: 1; /* Adjust as needed */
margin-bottom: 4px;

    right: 0;
    z-index: 2;
}
.login-profile-community-container {
position: relative;
padding: 5px;
z-index: 1; 
}
.active_container{
display: flex;
flex-wrap: wrap;
    justify-content: flex-start;
}
.mobile-menu-icon{
    display: none;
}
@media (max-width: 768px) {
  /* Hide desktop menu on mobile screens */
  .head3 {
    display: none;
  }

  /* Add a new element for the mobile menu icon */
 
.mobile-bottom-navigation {
  background: var(--white);
  position: fixed;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 100%;
  max-width: 500px;
  margin: auto;
  display: flex;
  justify-content: space-around;
  align-items: center;
  padding: 5px 0;
  box-shadow: 0 0 10px hsla(0, 0%, 0%, 0.25);
  z-index: 5;
}

.mobile-bottom-navigation .action-btn {
  position: relative;
  font-size: 26px;
  color: var(--eerie-black);
  padding: 10px;
}

.mobile-bottom-navigation .count {
  background: var(--bittersweet);
  color: var(--white);
  position: absolute;
  top: 0;
  right: 0;
  font-size: 12px;
  font-weight: var(--weight-500);
  line-height: 1;
  padding: 2px 4px;
  border-radius: 20px;
}
.mobile-bottom-navigation {
    border-top-left-radius: var(--border-radius-md);
    border-top-right-radius: var(--border-radius-md);
  }
  .mobile-bottom-navigation { display: on; }
  .mobile-bottom-navigation .action-btn {
  position: relative;
  font-size: 26px;
  color: var(--eerie-black);
  padding: 10px;
}
  }

.header-user-actions .action-btn {
    position: relative;
    font-size: 35px;
    color: var(--onyx);
    padding: 5px;
  }
  .action-btn{
    border: none;
    background-color: transparent;
  }


  .mobile-navigation-menu {
  background: var(--white);
  position: fixed;
  top: 0;
  left: -100%;
  width: 100%;
  max-width: 320px;
  height: 100%;
  padding: 20px;
  box-shadow: 0 0 10px hsla(0, 0%, 0%, 0.1);
  overflow-y: scroll;
  overscroll-behavior: contain;
  visibility: hidden;
  transition: 0.5s ease;
  z-index: 20;
}

.mobile-navigation-menu.active {
  left: 0;
  visibility: visible;
}
.menu-top {
  padding-bottom: 15px;
  margin-bottom: 10px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 2px solid var(--cultured);
}

.menu-top .menu-title {
  color: var(--salmon-pink);
  font-size: var(--fs-4);
  font-weight: var(--weight-600);
}
.mobile-menu-category-list .menu-title {
  color: var(--onyx);
  font-size: var(--fs-6);
  font-weight: var(--weight-500);
  padding: 12px 0;
}
.menu-bottom .menu-title {
  font-size: var(--fs-6);
  font-weight: var(--weight-500);
  color: var(--eerie-black);
  padding: 12px 0;
}
.sidebar .menu-title {
  font-size: var(--fs-5);
  color: var(--sonic-silver);
  font-weight: var(--weight-500);
}

  .menu-category .accordion-menu {
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.accordion-menu > div { font-size: 14px; }

.accordion-menu ion-icon {
  color: var(--onyx);
  --ionicon-stroke-width: 90px;
}
.accordion-menu.active .caret-back { transform: rotate(-0.25turn); }

.accordion-menu.active .add-icon,
.accordion-menu .remove-icon { display: none; }

.accordion-menu .add-icon,
.accordion-menu.active .remove-icon { display: block; }
.accordion-menu.active .caret-back { transform: rotate(-0.25turn); }

.menu-category .accordion-menu {
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.mobile-menu-category-list .menu-category { border-bottom: 1px solid var(--cultured); }
.menu-bottom .submenu-category:not(:last-child) { border-bottom: 1px solid var(--cultured); }
.accordion-menu.active .caret-back { -webkit-transform: rotate(-0.25turn); -ms-transform: rotate(-0.25turn); transform: rotate(-0.25turn); }
button {
  background: none;
  font: inherit;
  border: none;
  cursor: pointer;
}

img, ion-icon, button, a { display: block; }
a { text-decoration: none; }
.menu-category .submenu-category-list { margin-left: 10px; }
.submenu-category-list {
  max-height: 0;
  overflow: hidden;
  visibility: hidden;
  -webkit-transition: 0.5s ease-in-out;
  -o-transition: 0.5s ease-in-out;
  transition: 0.5s ease-in-out;
}

.submenu-category-list.active {
  max-height: 148px;
  visibility: visible;
}
.submenu-title {
  padding: 6px 0;
  font-size: var(--fs-6);
  color: var(--sonic-silver);
  font-weight: var(--weight-300);
}

.submenu-title:hover { color: var(--davys-gray); }
.overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: hsla(0, 0%, 0%, 0.5);
  opacity: 0;
  pointer-events: none;
  z-index: 15;
  transition: 0.5s ease;
}

.overlay.active {
  opacity: 1;
  pointer-events: all;
}
.menu-social-container .social-link {
  background: var(--cultured);
  color: var(--eerie-black);
  font-size: 20px;
  padding: 10px;
  -webkit-border-radius: var(--border-radius-md);
          border-radius: var(--border-radius-md);
}
.footer-nav .social-link {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: start;
  -webkit-justify-content: flex-start;
      -ms-flex-pack: start;
          justify-content: flex-start;
  -webkit-box-align: center;
  -webkit-align-items: center;
      -ms-flex-align: center;
          align-items: center;
  gap: 10px;
}

.social-link .footer-nav-link { font-size: 25px; }
.header-social-container .social-link {
    padding: 5px;
    background: hsl(0, 0%, 95%);
    -webkit-border-radius: var(--border-radius-sm);
            border-radius: var(--border-radius-sm);
    color: var(--sonic-silver);
    -webkit-transition: var(--transition-timing);
    -o-transition: var(--transition-timing);
    transition: var(--transition-timing);
  }

  .header-social-container .social-link:hover {
    background: var(--salmon-pink);
    color: var(--white);
  }
  .menu-social-container .social-link {
  background: var(--cultured);
  color: var(--eerie-black);
  font-size: 20px;
  padding: 10px;
  -webkit-border-radius: var(--border-radius-md);
          border-radius: var(--border-radius-md);
}
@media (max-width: 1000px) {
  /* Hide desktop menu on mobile screens */
  .head1 {
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
.menu-social-container {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: center;
  -webkit-justify-content: center;
      -ms-flex-pack: center;
          justify-content: center;
  -webkit-box-align: center;
  -webkit-align-items: center;
      -ms-flex-align: center;
          align-items: center;
  gap: 10px;
}
</style>
<div class="overlay" data-overlay></div>

<div class="index_ui">
    <div class="head1">
        <ul class="header-social-container">
            <li>
                <a href="https://web.facebook.com/profile.php?id=100054330589589" class="social-link">
                <ion-icon name="logo-facebook"></ion-icon>
                </a>
            </li>
          
            <li>
                <a href="https://twitter.com/" class="social-link">
                <ion-icon name="logo-twitter"></ion-icon>
                </a>
            </li>
          
            <li>
                <a href="https://www.instagram.com/" class="social-link">
                <ion-icon name="logo-instagram"></ion-icon>
                </a>
            </li>
        </ul>
        <style>

          .language_div{
            display: flex;
            flex-direction: row;
          }
          .scanner-btn{
            display: flex;
            flex-direction: row;
            align-items: center;
          }
        </style>
        <div class="language_div">
        <button class="action-btn scanner-btn" data-toggle="modal" data-target="#scannerModal">
  <ion-icon name="qr-code-outline"></ion-icon> <br> &nbsp;
  <span> QR Code Scanner</span>
</button>
        <select class="language_select" name="" id="">
            <option value="">
                FILIPINO
            </option>
            <option value="">
                ENGLISH
            </option>
        </select>
    </div>
    </div>
    <div class="head2">
        <div class="left-content">
            <h1>AgroNet</h1>
        </div>
        <div class="right-content">
        <span>
        <?php echo ucwords($_settings->userdata('username')) ?>
        </span>
        <?php
$is_verified = ($_settings->userdata('is_verified') == 0);
?>
 <?php if ($is_verified): ?>
            <img class="img_verified" src="../uploads/verified.png" alt="Verified">
        <?php endif; ?>
        <label for="profile-picture-input" class="login-profile-container">
    <button class="login-profile" style="background-image: url('<?php echo validate_image($_settings->userdata('avatar')) ?>');" type="button" data-toggle="dropdown">
        <button style="display: none;" type="button" class="btn btn-rounded badge badge-light dropdown-toggle dropdown-icon">
            <span><img src="" class="img-circle elevation-2 user-img" alt="User Image"></span>
            <span class="ml-3"><?php echo ucwords($_settings->userdata('username')) ?></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div class="dropdown-menu" role="menu">
            <a class="dropdown-item" href="<?php echo base_url.'vendor/?page=user' ?>"><span class="fa fa-user"></span> My Account</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="<?php echo base_url.'/classes/Login.php?f=logout_vendor' ?>"><span class="fas fa-sign-out-alt"></span> Logout</a>
        </div>
    </button>
</label>


        
    </div>
</div>

        <div class="head3">
            <div class="mobile-menu-icon">  <ion-icon name="menu-outline"></ion-icon>
            </div>
    <ul class="desktop-menu-category-list">
        <li class="menu-category">
            <a href="<?php echo base_url ?>vendor/" class="menu-title">Home</a>
            <ul class="dropdown-list">
                <li class="dropdown-item">
                    <a href="#"></a>
                </li>
            </ul>
        </li>

        <li class="menu-category">
        <a href="<?php echo base_url ?>vendor/?page=crops/index2"  class="menu-title <?= isset($page) && $page == 'home' ? "active" : "crops/index2" ?>" >Crop Management</a>
        
        </li>

        <li class="menu-category">
            <a href="<?php echo base_url ?>vendor/?page=financial/index" class="menu-title">Financial Transparency</a>
            <ul class="dropdown-list">
                <li class="dropdown-item">
                    <a href="#"></a>
                </li>
            </ul>
        </li>

        <li class="menu-category">
            <a href="<?php echo base_url ?>vendor/?e-home" class="menu-title">Marketplace</a>
            <ul class="dropdown-list">
                <li class="dropdown-item">
                    <a href="#"></a>
                </li>
            </ul>
        </li>

        <li class="menu-category">
            <a href="<?php echo base_url ?>vendor/?page=map/map" class="menu-title">Farm Map</a>
            <ul class="dropdown-list">
                <li class="dropdown-item">
                    <a href="#"></a>
                </li>
            </ul>
        </li>

        <li class="menu-category">
            <a href="<?php echo base_url ?>vendor/?page=commission" class="menu-title">Commission</a>
        </li>
        <li class="menu-category">
            <a href="<?php echo base_url ?>vendor/?page=crops/feeder" class="menu-title">Feeder</a>
        </li>
    </ul>
</div>
</div>
<nav class="mobile-navigation-menu " data-mobile-menu>

<div class="menu-top">
  <h2 class="menu-title">Menu</h2>

  <button class="menu-close-btn" data-mobile-menu-close-btn>
    <ion-icon name="close-outline"></ion-icon>
  </button>
</div>

<ul class="mobile-menu-category-list">

  <li class="menu-category">
    <a href="<?php echo base_url ?>vendor/" class="menu-title">Home</a>
    
  </li>

  <!-- <li class="menu-category">

    <button class="accordion-menu" data-accordion-btn>
      <p class="menu-title">Men's</p>

      <div>
        <ion-icon name="add-outline" class="add-icon"></ion-icon>
        <ion-icon name="remove-outline" class="remove-icon"></ion-icon>
      </div>
    </button>

    <ul class="submenu-category-list" data-accordion>

      <li class="submenu-category">
        <a href="#" class="submenu-title">Shirt</a>
      </li>

      <li class="submenu-category">
        <a href="#" class="submenu-title">Shorts & Jeans</a>
      </li>

      <li class="submenu-category">
        <a href="#" class="submenu-title">Safety Shoes</a>
      </li>

      <li class="submenu-category">
        <a href="#" class="submenu-title">Wallet</a>
      </li>

    </ul>

  </li> -->

  <!-- <li class="menu-category">

    <button class="accordion-menu" data-accordion-btn>
      <p class="menu-title">Women's</p>

      <div>
        <ion-icon name="add-outline" class="add-icon"></ion-icon>
        <ion-icon name="remove-outline" class="remove-icon"></ion-icon>
      </div>
    </button>

    <ul class="submenu-category-list" data-accordion>

      <li class="submenu-category">
        <a href="#" class="submenu-title">Dress & Frock</a>
      </li>

      <li class="submenu-category">
        <a href="#" class="submenu-title">Earrings</a>
      </li>

      <li class="submenu-category">
        <a href="#" class="submenu-title">Necklace</a>
      </li>

      <li class="submenu-category">
        <a href="#" class="submenu-title">Makeup Kit</a>
      </li>

    </ul>

  </li> -->

  <!-- <li class="menu-category">

    <button class="accordion-menu" data-accordion-btn>
      <p class="menu-title">Jewelry</p>

      <div>
        <ion-icon name="add-outline" class="add-icon"></ion-icon>
        <ion-icon name="remove-outline" class="remove-icon"></ion-icon>
      </div>
    </button>

    <ul class="submenu-category-list" data-accordion>

      <li class="submenu-category">
        <a href="#" class="submenu-title">Earrings</a>
      </li>

      <li class="submenu-category">
        <a href="#" class="submenu-title">Couple Rings</a>
      </li>

      <li class="submenu-category">
        <a href="#" class="submenu-title">Necklace</a>
      </li>

      <li class="submenu-category">
        <a href="#" class="submenu-title">Bracelets</a>
      </li>

    </ul>

  </li> -->

  <!-- <li class="menu-category">

    <button class="accordion-menu" data-accordion-btn>
      <p class="menu-title">Perfume</p>

      <div>
        <ion-icon name="add-outline" class="add-icon"></ion-icon>
        <ion-icon name="remove-outline" class="remove-icon"></ion-icon>
      </div>
    </button>

    <ul class="submenu-category-list" data-accordion>

      <li class="submenu-category">
        <a href="#" class="submenu-title">Clothes Perfume</a>
      </li>

      <li class="submenu-category">
        <a href="#" class="submenu-title">Deodorant</a>
      </li>

      <li class="submenu-category">
        <a href="#" class="submenu-title">Flower Fragrance</a>
      </li>

      <li class="submenu-category">
        <a href="#" class="submenu-title">Air Freshener</a>
      </li>

    </ul>

  </li> -->

  <li class="menu-category">
  <a href="<?php echo base_url ?>vendor/?page=crops/index2"  class="menu-title <?= isset($page) && $page == 'home' ? "active" : "crops/index2" ?>" >Crop Management</a>
  </li>

  <li class="menu-category">
  <a href="<?php echo base_url ?>vendor/?page=financial/index" class="menu-title">Financial Transparency</a>
  </li>
  <li class="menu-category">
  <a href="<?php echo base_url ?>vendor/?e-home" class="menu-title">Marketplace</a>
  </li>
  <li class="menu-category">
  <a href="<?php echo base_url ?>vendor/?page=map/map" class="menu-title">Farm Map</a>
  </li>
  <li class="menu-category">
  <a href="<?php echo base_url ?>vendor/?page=commission" class="menu-title">Commission</a>
  </li>

</ul>

<div class="menu-bottom">

  <ul class="menu-category-list">

    <li class="menu-category">

      <button class="accordion-menu" data-accordion-btn>
        <p class="menu-title">Language</p>

        <ion-icon name="caret-back-outline" class="caret-back"></ion-icon>
      </button>

      <ul class="submenu-category-list" data-accordion>

        <li class="submenu-category">
          <a href="#" class="submenu-title">English</a>
        </li>

        <li class="submenu-category">
          <a href="#" class="submenu-title">Espa&ntilde;ol</a>
        </li>

        <li class="submenu-category">
          <a href="#" class="submenu-title">Fren&ccedil;h</a>
        </li>

      </ul>

    </li>

    <li class="menu-category">
      <button class="accordion-menu" data-accordion-btn>
        <p class="menu-title">Currency</p>
        <ion-icon name="caret-back-outline" class="caret-back"></ion-icon>
      </button>

      <ul class="submenu-category-list" data-accordion>
        <li class="submenu-category">
          <a href="#" class="submenu-title">USD &dollar;</a>
        </li>

        <li class="submenu-category">
          <a href="#" class="submenu-title">EUR &euro;</a>
        </li>
      </ul>
    </li>

  </ul>

  <ul class="menu-social-container">

    <li>
      <a href="https://web.facebook.com/profile.php?id=100054330589589" class="social-link">
        <ion-icon name="logo-facebook"></ion-icon>
      </a>
    </li>

    <li>
      <a href="https://twitter.com/" class="social-link">
        <ion-icon name="logo-twitter"></ion-icon>
      </a>
    </li>

    <li>
      <a href="https://www.instagram.com/" class="social-link">
        <ion-icon name="logo-instagram"></ion-icon>
      </a>
    </li>

    <li>
      <a href="https://linkedin.com/" class="social-link">
        <ion-icon name="logo-linkedin"></ion-icon>
      </a>
    </li>

  </ul>

</div>

</nav>
<?php
// Assuming you have a session variable for the vendor ID or some other identifier
// Fetch crop count for the vendor
$crop_count_query = $conn->query("SELECT COUNT(id) as total FROM crop WHERE VendorId = '{$_settings->userdata('id')}'");
$crop_count = $crop_count_query->fetch_assoc()['total'];

$crop_order_query = $conn->query("SELECT COUNT(id) as total_order FROM order_list WHERE vendor_id = '{$_settings->userdata('id')}' AND status = '1'");
$crop_order = $crop_order_query->fetch_assoc()['total_order'];

?>

<div class="mobile-bottom-navigation">

<button class="action-btn" data-mobile-menu-open-btn>
  <ion-icon name="menu-outline"></ion-icon>
</button>

<button class="action-btn" onclick="redirectToCrops()">
  <ion-icon name="leaf-outline"></ion-icon>
  <span class="count"><?php echo $crop_count; ?></span>
</button>

<button class="action-btn" onclick="redirectToHome()">
  <ion-icon name="home-outline"></ion-icon>
</button>

<button class="action-btn" onclick="redirectToMarket()">
<ion-icon name="bag-handle-outline"></ion-icon>
  <span class="count"><?php echo $crop_order; ?></span>
</button>

<button class="action-btn" data-mobile-menu-open-btn onclick="redirectToMap()">
  <ion-icon name="map-outline"></ion-icon>
</button>
<button class="action-btn" data-toggle="modal" data-target="#scannerModal">
  <ion-icon name="qr-code-outline"></ion-icon>
</button>

</div>
<script>
  function redirectToCrops() {
    window.location.href = "<?php echo base_url ?>vendor/?page=crops/index2";
  }
  function redirectToMarket() {
    window.location.href = "<?php echo base_url ?>vendor/?e-home";
  }
  function redirectToMap() {
    window.location.href = "<?php echo base_url ?>vendor/?page=map/map";
  }
  function redirectToHome() {
    window.location.href = "<?php echo base_url ?>vendor/";
  }
</script>

<script>
    const mobileMenuOpenBtn = document.querySelectorAll('[data-mobile-menu-open-btn]');
const mobileMenu = document.querySelectorAll('[data-mobile-menu]');
const mobileMenuCloseBtn = document.querySelectorAll('[data-mobile-menu-close-btn]');
const overlay = document.querySelector('[data-overlay]');

for (let i = 0; i < mobileMenuOpenBtn.length; i++) {

  // mobile menu function
  const mobileMenuCloseFunc = function () {
    mobileMenu[i].classList.remove('active');
    overlay.classList.remove('active');
  }

  mobileMenuOpenBtn[i].addEventListener('click', function () {
    mobileMenu[i].classList.add('active');
    overlay.classList.add('active');
  });

  mobileMenuCloseBtn[i].addEventListener('click', mobileMenuCloseFunc);
  overlay.addEventListener('click', mobileMenuCloseFunc);

}
</script>
<div class="modal fade" id="scannerModal" tabindex="-1" role="dialog" aria-labelledby="scannerModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="scannerModalLabel">Scanner</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container1">
                        <h1>Scan QR Codes</h1>
                        <div class="section1">
                            <div id="my-qr-reader"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<style>
        .modal-body canvas {
            display: block;
            margin: 0 auto;
        }
    </style>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  <script src="https://unpkg.com/html5-qrcode"></script>

  <script>
                $('#scannerModal').on('shown.bs.modal', function () {
                html5QrCode = new Html5Qrcode("my-qr-reader");
                html5QrCode.start(
                    { facingMode: "environment" },
                    {
                        fps: 10, // Optional, frame per seconds for qr code scanning
                        qrbox: { width: 250, height: 250 } // Optional, if you want bounded box UI
                    },
                    qrCodeMessage => {
                        // Redirect to the scanned QR code URL
                        window.location.href = qrCodeMessage;
                    },
                    errorMessage => {
                        // Parse error, ignore it
                    })
                .catch(err => {
                    // Start failed, handle it.
                    console.error(`Unable to start scanning, error: ${err}`);
                });
            });

            $('#scannerModal').on('hidden.bs.modal', function () {
                if (html5QrCode) {
                    html5QrCode.stop().then(ignore => {
                        // QR Code scanning stopped
                    }).catch(err => {
                        console.error(`Unable to stop scanning, error: ${err}`);
                    });
                }
            });
  </script>