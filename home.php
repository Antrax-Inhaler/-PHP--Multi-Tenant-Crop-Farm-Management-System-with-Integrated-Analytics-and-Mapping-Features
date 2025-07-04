
<style>
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
        .welcome-content{
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .readmore{
            border: white solid 3px ;
            border-radius: 10px;
            width: 150px;
            height: 70px;
            background-color: rgba(255, 255, 255, 0.356);
        }
        .layer2{
            background-color: #404346;
            padding: 30px;
        }
        @media (max-width: 1000px) {
            .layer2{
                font-size: 10px;
            }
            .pictures{
                padding: 50px;
            }
  }
</style>
<audio id="silent-sound" src="sounds/notification.wav" preload="auto" muted></audio>
    <audio id="notification-sound" src="sounds/enchance.mp3" preload="auto"></audio>

    <script>
        window.onload = function() {
            var silentSound = document.getElementById('silent-sound');
            silentSound.play().then(function() {
                silentSound.muted = false; // Unmute after playing the silent sound
                setTimeout(function() {
                    document.getElementById('notification-sound').play();
                }, 5000); // 5000 milliseconds = 5 seconds
            }).catch(function(error) {
                console.log('Silent sound playback failed:', error);
            });
        };
    </script>
<div class="layer2">
                    <h3 style="color: #2ddc9a; padding-top: 50px; " class="text-center">Welcome</h3>
                    <div class="welcome-content" style="padding-bottom: 50px;" >
                        <?php include("welcome.html") ?>
                        <div >
                            <button class="readmore">
                                Read More
                            </button>
                        </div>
                    </div>
                    </div><style>
    .rectangle1 {
      width: 100%;
      height: 400px;
      display: flex;
      justify-content: space-between;
      background-color: white;
      
    }
    .rectangle2 {
      width: 100%;
      height: 400px;
      background-color: white;
      display: flex;
      justify-content: space-between;
      
    }
    img{
        height: 100%;

    }
    .strip{
      width: 100%;
      height: 10px;
      background: linear-gradient(to bottom, rgb(20, 20, 20) 10%, #0eefffd7);
    }
    .statement{
      color: #2ddc9a;
    }
    .bl{
      color: black;
      text-align: right;
    }
    .lefty{
      padding: 20px;

    }
    .join {
      display: inline-block;
      padding: 10px 20px;
      border-radius: 20px;
      background-color: #2ddc9a; /* Base color */
      color: white;
      text-decoration: none;
      text-transform: uppercase;
      font-weight: bold;
      position: relative; /* Required for animation */
      overflow: hidden; /* Prevents box-shadow from overflowing */
      box-shadow: 0 5px 15px rgba(45, 220, 154, 0.4); /* Soft shadow */
      animation: bounceAnimation 1s ease-in-out infinite; /* Bouncing animation */
    }

    @keyframes bounceAnimation {
      0%, 100% {
        transform: translateY(0);
      }
      50% {
        transform: translateY(-10px); /* Adjust bounce height as needed */
      }
    }

    .join::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.4), rgba(255, 255, 255, 0));
      border-radius: inherit;
      opacity: 0;
      transition: opacity 0.3s ease-in-out;
    }

    .join:hover::before {
      opacity: 1; /* Show glossy effect on hover */
    }

    .join:hover {
      transform: translateY(0); /* Reset transform on hover */
      animation: none; /* Stop bouncing animation on hover */
    }
    .pictures img{
        width: 100%;
        z-index: -1;
    }
  </style>
  <div class="strip"></div>
  <div class="rectangle1">
    <div class="lefty">
    <div class="statement">
         <h1>Explore Agronet!</h1> A revolutionary platform that transforms agriculture management. Seamlessly connect with local farmers and gain access to fresh produce. Our tools enable efficient crop management, financial transparency, and location mapping, promoting sustainable farming practices. Join us in revolutionizing agriculture for a brighter, greener future!
         <br>
         <a class="join" href="./vendor">Join Now!</a>
      </div>
    </div>
    <div class="pictures" >
      <img src="uploads/design/seedtosale.jpg" alt="">
    </div>
  </div>
  <div class="strip"></div>
  <div class="rectangle2">
    <div  class="pictures"><img src="uploads/ateprutas.png" alt=""></div>
    <div>
      <div class="statement bl"> <h1>From Seed to Sale</h1> Our platform empowers farmers at every step, offering tools for efficient crop management, transparent financial tracking, and seamless access to a broad customer base through our integrated ecommerce marketplace. Maximize your harvest and profitability with AgroNet!
        <div class="clear-fix mb-2"></div>
        <div class="text-center">
            <a href="./?page=products" class="btn btn-large btn-primary rounded-pill col-lg-3 col-md-5 col-sm-12">Explore More Products</a>
        </div>
      </div>
    </div>
  </div>
  </div>
  <div class="strip"></div>
  <div class="rectangle1">
    <div class="lefty"><div class="statement"><h1>Support Farmers</h1>Every purchase directly supports local farmers, sustainable agriculture practices, and community growth. Shop now to make a difference in farming communities!</div></div>
    <div  class="pictures"><img src="uploads/basketman.png" alt=""></div>
  </div>
  <div class="strip"></div>
  <style>
        #notify-btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
<body>
    <button id="notify-btn">Enable Notifications</button>

    <script>
        // Check for Notification permission
        function requestNotificationPermission() {
            if (Notification.permission !== 'granted') {
                Notification.requestPermission().then(permission => {
                    if (permission === 'granted') {
                        alert('You will receive notifications.');
                    } else {
                        alert('Notifications are blocked. Enable them in your browser settings.');
                    }
                });
            }
        }

        // Fetch the latest order and notify the user
        function fetchLatestOrder() {
            fetch('fetch_latest_order.php')
                .then(response => response.json())
                .then(data => {
                    if (data.id) {
                        new Notification('New Order Update', {
                            body: `Order ID: ${data.id}, Updated at: ${data.date_updated}`
                        });
                    }
                })
                .catch(error => console.error('Error fetching order:', error));
        }

        document.getElementById('notify-btn').addEventListener('click', () => {
            requestNotificationPermission();
        });

        // Poll for the latest order every 10 seconds
        setInterval(() => {
            if (Notification.permission === 'granted') {
                fetchLatestOrder();
            }
        }, 10000);

        // Optionally fetch immediately on page load
        if (Notification.permission === 'granted') {
            fetchLatestOrder();
        }
    </script>

<?php include './products/products.php'?>