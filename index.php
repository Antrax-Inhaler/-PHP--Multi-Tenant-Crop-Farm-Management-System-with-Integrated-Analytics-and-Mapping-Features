<?php require_once('./config.php');
// Get the current month in 'YYYY-MM' format
// Fetch all vendors
$vendors = $conn->query("SELECT id FROM vendor_list WHERE delete_flag = 0");

while($vendor = $vendors->fetch_assoc()) {
    $vendor_id = $vendor['id'];

    // Fetch all distinct months from order_list for the vendor
    $months_query = $conn->query("SELECT DISTINCT DATE_FORMAT(date_created, '%Y-%m') as month FROM order_list WHERE vendor_id = '{$vendor_id}'");

    while($month_row = $months_query->fetch_assoc()) {
        $month = $month_row['month'];

        // Calculate total sales for the month
        $sales_query = $conn->query("SELECT SUM(total_amount) as total_sales FROM order_list WHERE vendor_id = '{$vendor_id}' AND DATE_FORMAT(date_created, '%Y-%m') = '{$month}'")->fetch_assoc();
        $total_sales = $sales_query['total_sales'] ? $sales_query['total_sales'] : 0;

        // Fetch commission rate for the vendor's user
        $vendor_user_query = $conn->query("SELECT user_id FROM vendor_list WHERE id = '{$vendor_id}'")->fetch_assoc();
        $user_id = $vendor_user_query['user_id'];
        $commission_rate_query = $conn->query("SELECT commission FROM users WHERE id = '{$user_id}'")->fetch_assoc();
        $commission_rate = $commission_rate_query['commission'];
        $total_commission = $total_sales * $commission_rate;

        // Check if entry already exists
        $existing_entry = $conn->query("SELECT id FROM vendor_commissions WHERE vendor_id = '{$vendor_id}' AND month = '{$month}'")->fetch_assoc();

        if ($existing_entry) {
            // Update existing entry
            $conn->query("UPDATE vendor_commissions SET total_sales = '{$total_sales}', total_commission = '{$total_commission}' WHERE id = '{$existing_entry['id']}'");
        } else {
            // Insert new entry
            $conn->query("INSERT INTO vendor_commissions (vendor_id, month, total_sales, total_commission) VALUES ('{$vendor_id}', '{$month}', '{$total_sales}', '{$total_commission}')");
        }
    }
}
?>
<?php 

$apiKey = "2f745fa85d563da5adb87b6cd4b81caf";

// Get the current date
$currentDate = date('Y-m-d');

// Fetch all farms
$farms = $conn->query("SELECT Id, Latitude, Longitude FROM farm WHERE delete_flag = 0");

while($farm = $farms->fetch_assoc()) {
    $farmId = $farm['Id'];
    $latitude = $farm['Latitude'];
    $longitude = $farm['Longitude'];

    // Check if weather data already exists for this farm for today
    $existing_weather = $conn->query("SELECT Id FROM weather WHERE FarmId = '{$farmId}' AND DATE(RecordedAt) = '{$currentDate}' AND (Sunrise IS NOT NULL OR Sunset IS NOT NULL)")->fetch_assoc();

    if (!$existing_weather) {
        // Fetch weather data using the OpenWeatherMap API
        $url = "https://api.openweathermap.org/data/2.5/weather?lat={$latitude}&lon={$longitude}&appid={$apiKey}&units=metric";
        $weatherData = file_get_contents($url);
        $weatherData = json_decode($weatherData, true);

        if (isset($weatherData['main'])) {
            // Prepare data for insertion
            $temperature = $weatherData['main']['temp'];
            $minTemperature = $weatherData['main']['temp_min'];
            $maxTemperature = $weatherData['main']['temp_max'];
            $feelsLikeTemperature = $weatherData['main']['feels_like'];
            $humidity = $weatherData['main']['humidity'];
            $cloudiness = $weatherData['clouds']['all'];
            $windSpeed = $weatherData['wind']['speed'];
            $rainVolume = isset($weatherData['rain']['1h']) ? $weatherData['rain']['1h'] : 0;
            $weatherDescription = $weatherData['weather'][0]['description'];
            $sunrise = date('Y-m-d H:i:s', $weatherData['sys']['sunrise']);
            $sunset = date('Y-m-d H:i:s', $weatherData['sys']['sunset']);
            $recordedAt = date('Y-m-d H:i:s');

            // Insert weather data into the database
            $conn->query("INSERT INTO weather (FarmId, Temperature, MinTemperature, MaxTemperature, FeelsLikeTemperature, Humidity, RainVolume, Cloudiness, WindSpeed, WeatherDescription, Sunrise, Sunset, RecordedAt) 
                VALUES ('{$farmId}', '{$temperature}', '{$minTemperature}', '{$maxTemperature}', '{$feelsLikeTemperature}', '{$humidity}', '{$rainVolume}', '{$cloudiness}', '{$windSpeed}', '{$weatherDescription}', '{$sunrise}', '{$sunset}', '{$recordedAt}')");
        }
    }
}
?>

<?php
// Assuming you have a database connection established
// Fetch the latest order from the order_list table
$query = "SELECT * FROM order_list ORDER BY id DESC LIMIT 1";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $latestOrder = mysqli_fetch_assoc($result);
    // Now $latestOrder contains the data of the latest order
} else {
    $latestOrder = null; // No orders found
}
?>
<!-- Place this button where you want it to appear in your HTML -->

<!-- Include this script in your home.php or in a separate JS file -->
<script>
    function sendNotification() {
        // Check if the browser supports notifications
        if ('Notification' in window) {
            Notification.requestPermission().then(function(permission) {
                if (permission === 'granted') {
                    // Construct notification message using the latestOrder data
                    const notificationTitle = 'Latest Order';
                    const notificationOptions = {
                        body: 'New order: ' + <?php echo json_encode($latestOrder['code']); ?>,
                        icon: 'icon.png', // You can use a custom icon
                    };

                    // Show the notification
                    new Notification(notificationTitle, notificationOptions);
                } else {
                    console.error('Notification permission denied');
                }
            });
        } else {
            console.error('Notifications not supported by this browser.');
        }
    }
</script>

 <!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
<style>
  *{
    font-family: Poppins, Arial, "Helvetica Neue", Helvetica, sans-serif;
  }
  #header{
    height:70vh;
    width:calc(100%);
    position:relative;
    top:-1em;
  }
  #header:before{
    content:"";
    position:absolute;
    height:calc(100%);
    width:calc(100%);
    background-image:url(<?= validate_image($_settings->info("cover")) ?>);
    background-size:cover;
    background-repeat:no-repeat;
    background-position: center center;
  }
  #header>div{
    position:absolute;
    height:calc(100%);
    width:calc(100%);
    z-index:2;
  }

  #top-Nav a.nav-link.active {
      color: #343a40;
      font-weight: 900;
      position: relative;
  }
  #top-Nav a.nav-link.active:before {
    content: "";
    position: absolute;
    border-bottom: 2px solid #343a40;
    width: 33.33%;
    left: 33.33%;
    bottom: 0;
  }
  @media (max-width:760px){
    #top-Nav a.nav-link.active {
      background: #343a40db;
      color: #fff;
    }
    #top-Nav a.nav-link.active:before {
      content: "";
      position: absolute;
      border-bottom: 2px solid #343a40;
      width: 100%;
      left: 0;
      bottom: 0;
    }
    h1.w-100.text-center.site-title.px-5{
      font-size:2.5em !important;
    }
    
  }
    /* Floating arrow button styles */
  #floating-arrow {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000; /* Ensure it's above other content */
    cursor: pointer;
  }

  .arrow-icon {
    width: 60px;
    height: 60px;
    background-color: #2ddc9a; /* Button color */
    border-radius: 50%;
    box-shadow: 0 0 20px rgba(45, 220, 154, 0.3); /* Initial box shadow */
    display: flex;
    justify-content: center;
    align-items: center;
    transition: box-shadow 0.3s ease-in-out; /* Smooth transition for box shadow */
  }

  .arrow-icon:before {
    content: "";
    width: 32px; /* Width of the arrow image */
    height: 32px; /* Height of the arrow image */
    background-image: url('uploads/arrow_down.png'); /* Path to your custom arrow image */
    background-size: cover;
    display: block;
    filter: invert(100%)
  }

  /* Glowing effect */
  .glow {
    box-shadow: 0 0 20px 10px rgba(45, 220, 154, 0.9); /* Green glowing effect */
  }
</style>
<style>
      body{
        background-color: #fff;
      font-family: Poppins, Arial, "Helvetica Neue", Helvetica, sans-serif;

    }
    .container{
      background-color: white;
      font-family: Poppins, Arial, "Helvetica Neue", Helvetica, sans-serif;

    }
    .wrapper{
      background-color: #404346;
      font-family: Poppins, Arial, "Helvetica Neue", Helvetica, sans-serif;

    }
</style>

<style>
    .recovery-container {
        background: linear-gradient(to bottom right, #9CDC78, #74DCB0);
        border-radius: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        width: 400px;
        padding: 20px;
        text-align: center;
    }

    .recovery-header {
        padding: 10px;
    }

    .recovery-header h1 {
        margin: 0;
        font-size: 2em;
        color: white;
    }

    .recovery-body {
        padding: 20px;
    }

    .recovery-body p {
        font-size: 1em;
        color: #fff;
        margin-bottom: 20px;
    }

    .recovery-body a {
        text-decoration: none;
        color: #00796b;
        font-size: 1em;
    }

    .recovery-button {
        padding: 10px 20px;
        background: #00bfa5;
        color: #fff;
        border: none;
        border-radius: 20px;
        cursor: pointer;
        font-size: 1em;
    }

    .recovery-button:hover {
        background: #00796b;
    }
</style>
<?php require_once('inc/header.php') ?>
  <body class="layout-top-nav layout-fixed layout-navbar-fixed" style="height: auto; background-color: #404346;" >

    <div class="wrapper" style="padding: 0;" >
     <?php $page = isset($_GET['page']) ? $_GET['page'] : 'home';  ?>
     <?php require_once('inc/topBarNav.php') ?>
     <?php if($_settings->chk_flashdata('success')): ?>
      <script>
        alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
      </script>
      <?php endif;?>    
       <!-- Ad Blocking Recovery Message Modal -->
       <div class="modal fade" id="adBlockModal" tabindex="-1" role="dialog" aria-labelledby="adBlockModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content recovery-container">
            <div class="modal-header recovery-header">
                <h1 class="modal-title" id="adBlockModalLabel">Ad Blocking Recovery Message</h1>
            </div>
            <div class="modal-body recovery-body">
                <p>Your content is being blocked by an ad blocker. Please disable your ad blocker to view the content.</p>
                <a href="https://example.com/instructions" target="_blank">Click here for instructions</a>
            </div>
        </div>
    </div>
</div>
      <!-- Content Wrapper. Contains page content -->
      <div class="" style="background-color:  rgb(255, 255, 255);  margin-top: 90px;">
        <?php if($page == "home" || $page == "about"): ?>
          <style>
        /* Your existing CSS styles */
        .container1 {
            font-family: Poppins, Arial, "Helvetica Neue", Helvetica, sans-serif;
            margin-top: 95px;
            padding: 0;
            background-color: white;
        }
        .grid-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            justify-content: center;
            align-items: center;
            padding: 20px;
            text-align: left;
        }
        /* Add keyframes animation */
        @keyframes slideIn {
            0% {
                transform: translateX(-160%);
            }
            100% {
                transform: translateX(0);
            }
        }
        .text2 {
            padding: 20px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .design-container {
            text-align: center;
        }
        .leaf {
            width: 30px;
            height: 30px;
            margin-bottom: 40px;
        }
        .bag{
          width: 100px;
            height: 100px;
            margin-left: 12px;

        }
        .text_l {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 10px;
        }
        .agronet {
            font-size: 80px;
            margin-right: 10px;
        }
        .shopnow {
            font-size: 80%;
            color: #102419;
            font-weight: bold;
        }
        .design {
            max-width: 80%;
            height: auto;
        }
        .statement {
            color: #2ddc9a;
            text-align: left;
            margin-left: 100px;
            z-index: 12;
            text-align: justify;
        }
        /* Media query for smaller screens */
        @media screen and (max-width: 600px) {
            .grid-container {
                grid-template-columns: 1fr;
            }
            .statement {
                margin-left: 0px;
            }
        }
        /* Media query for larger screens */
        @media screen and (min-width: 601px) {
            .grid-container {
                grid-template-columns: 1fr 1fr;
            }
        }
        .shopnow {
            font-size: 100px;
        }
        .shopt{
          font-weight:bolder; 
          font-size: 100px;
          color: #102419;
        }
        .bot_container {
            margin-top: 20px;
            width: 400px;
            border-radius: 30px;
            display: flex;
            justify-content: right;
            align-items: center;
            background: white;
            color: white;
            font-size: 16px;
            padding: 0 1px;
            box-shadow: 4px 4px 6px 1px rgba(0, 0, 0, 0.1);
            padding: 3px;
            color: gray;
        }
        .buybtn {
            height: 50px;
            width: 150px;
            padding: 2px;
            border-radius: 30px;
            margin-left: 10px;
            position: relative;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            border: none;
            cursor: pointer;
            transition: background-position 0.5s, transform 0.2s;
            background-image: linear-gradient(to right, #12da19, rgb(70, 255, 79));
            color: #fff;
        }
        .buybtn:hover {
            background-position: 200% center;
            transform: scale(1.05);
        }
        .buybtn img {
            width: 25%;
            margin-left: 4px;
        }
        /* Add animation to buybtn */
        .buybtn.animate {
            animation: slideIn 2s forwards;
        }
        .moving-rectangle {
        position: relative;
        width: 100%;
        height: 40px;
        overflow: hidden;
        background: linear-gradient(to right, #ff5733, #ffa700, #ffef00, #aaff00, #33ff57, #00ffa7, #00d4ff, #0055ff, #6600ff, #d200ff, #ff00aa, #ff0044);
        background-size: 200% auto;
        animation: gradientShift 10s linear infinite;
    }

    @keyframes gradientShift {
        0% {
            background-position: 0 0;
        }
        100% {
            background-position: 200% 0;
        }
    }

    .moving-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 18px;
        font-weight: bold;
        white-space: nowrap;
    }
    </style>
    <style>
    #notifyBtn{
        background-color: white;
        width: 600px;
        height: 100px;
        border: white;
    }
    #notifyBtn :hover{
        background-color: white;
        width: 600px;
        height: 100px;
        border: none
    }
    @media (max-width: 1000px) {
  .shopnow {
    font-size: 40px;
  }
    .agronet{
      font-size: 40px;
    }
    .shopt{
      font-size: 40px;
    }
    .statement{
      margin-left: 0;
      font-size: 10px;
      padding: 20px;

    }
    .bag{
      width: 40px;
      height: 40px;
    }
    .bot_container {
        font-size: 12px;
            width: 80%;
        }
        .text2{
          padding: 10px;
        }

  }

</style>
</head>
<div class="container1">
    <div class="grid-container">
        <div class="text2">
            <div class="shopnow">Explore Now on</div>
            <div class="text_l">
                <div class="agronet">Agronet</div>
                <img class="leaf" src="uploads/leaf.png" alt="">
                <div class="shopt">Shop</div>
                <div><img class="bag" src="uploads/bag.png" alt=""></div>
            </div>
            <div class="statement">
               Your Ultimate Agricultural Companion! Dive into a seamless farming experience where innovation meets simplicity. Explore powerful tools for crop management, financial transparency, and effortless trade in our bustling marketplace. Join us in revolutionizing agriculture with AgroNet!
            </div>
            <div class="bot_container">
                <span>Explore farmers product </span>
                <br>
                <br>
                <div>
                <button class="buybtn animate" onclick="redirectToProducts()">
  Buy Now <img src="uploads/shopping-cart_5549291.png" alt="">
</button>                </div>
            </div>
        </div>
        <div class="design-container">
            <img class="design" src="uploads/design.png" alt="">
        </div>
    </div>
    <div class="moving-rectangle">
        <div class="moving-text">Shop Now on Agronet!</div>
    </div>
    <script>

      
  function redirectToProducts() {
    window.location.href = './?page=products';
  }
</script>
<script async src="https://fundingchoicesmessages.google.com/i/pub-5217286547377656?ers=1" nonce="ofkEgn6_vfgZzIg-KLeBhQ"></script>
<script nonce="ofkEgn6_vfgZzIg-KLeBhQ">
    (function() {
        function signalGooglefcPresent() {
            if (!window.frames['googlefcPresent']) {
                if (document.body) {
                    const iframe = document.createElement('iframe');
                    iframe.style = 'width: 0; height: 0; border: none; z-index: -1000; left: -1000px; top: -1000px;';
                    iframe.style.display = 'none';
                    iframe.name = 'googlefcPresent';
                    document.body.appendChild(iframe);
                } else {
                    setTimeout(signalGooglefcPresent, 0);
                }
            }
        }
        signalGooglefcPresent();
    })();
</script>
<script>(function(){'use strict';function aa(a){var b=0;return function(){return b<a.length?{done:!1,value:a[b++]}:{done:!0}}}var ba="function"==typeof Object.defineProperties?Object.defineProperty:function(a,b,c){if(a==Array.prototype||a==Object.prototype)return a;a[b]=c.value;return a};
function ca(a){a=["object"==typeof globalThis&&globalThis,a,"object"==typeof window&&window,"object"==typeof self&&self,"object"==typeof global&&global];for(var b=0;b<a.length;++b){var c=a[b];if(c&&c.Math==Math)return c}throw Error("Cannot find global object");}var da=ca(this);function k(a,b){if(b)a:{var c=da;a=a.split(".");for(var d=0;d<a.length-1;d++){var e=a[d];if(!(e in c))break a;c=c[e]}a=a[a.length-1];d=c[a];b=b(d);b!=d&&null!=b&&ba(c,a,{configurable:!0,writable:!0,value:b})}}
function ea(a){return a.raw=a}function m(a){var b="undefined"!=typeof Symbol&&Symbol.iterator&&a[Symbol.iterator];if(b)return b.call(a);if("number"==typeof a.length)return{next:aa(a)};throw Error(String(a)+" is not an iterable or ArrayLike");}function fa(a){for(var b,c=[];!(b=a.next()).done;)c.push(b.value);return c}var ha="function"==typeof Object.create?Object.create:function(a){function b(){}b.prototype=a;return new b},n;
if("function"==typeof Object.setPrototypeOf)n=Object.setPrototypeOf;else{var q;a:{var ia={a:!0},ja={};try{ja.__proto__=ia;q=ja.a;break a}catch(a){}q=!1}n=q?function(a,b){a.__proto__=b;if(a.__proto__!==b)throw new TypeError(a+" is not extensible");return a}:null}var ka=n;
function r(a,b){a.prototype=ha(b.prototype);a.prototype.constructor=a;if(ka)ka(a,b);else for(var c in b)if("prototype"!=c)if(Object.defineProperties){var d=Object.getOwnPropertyDescriptor(b,c);d&&Object.defineProperty(a,c,d)}else a[c]=b[c];a.A=b.prototype}function la(){for(var a=Number(this),b=[],c=a;c<arguments.length;c++)b[c-a]=arguments[c];return b}k("Number.MAX_SAFE_INTEGER",function(){return 9007199254740991});
k("Number.isFinite",function(a){return a?a:function(b){return"number"!==typeof b?!1:!isNaN(b)&&Infinity!==b&&-Infinity!==b}});k("Number.isInteger",function(a){return a?a:function(b){return Number.isFinite(b)?b===Math.floor(b):!1}});k("Number.isSafeInteger",function(a){return a?a:function(b){return Number.isInteger(b)&&Math.abs(b)<=Number.MAX_SAFE_INTEGER}});
k("Math.trunc",function(a){return a?a:function(b){b=Number(b);if(isNaN(b)||Infinity===b||-Infinity===b||0===b)return b;var c=Math.floor(Math.abs(b));return 0>b?-c:c}});k("Object.is",function(a){return a?a:function(b,c){return b===c?0!==b||1/b===1/c:b!==b&&c!==c}});k("Array.prototype.includes",function(a){return a?a:function(b,c){var d=this;d instanceof String&&(d=String(d));var e=d.length;c=c||0;for(0>c&&(c=Math.max(c+e,0));c<e;c++){var f=d[c];if(f===b||Object.is(f,b))return!0}return!1}});
k("String.prototype.includes",function(a){return a?a:function(b,c){if(null==this)throw new TypeError("The 'this' value for String.prototype.includes must not be null or undefined");if(b instanceof RegExp)throw new TypeError("First argument to String.prototype.includes must not be a regular expression");return-1!==this.indexOf(b,c||0)}});/*

 Copyright The Closure Library Authors.
 SPDX-License-Identifier: Apache-2.0
*/
var t=this||self;function v(a){return a};var w,x;a:{for(var ma=["CLOSURE_FLAGS"],y=t,z=0;z<ma.length;z++)if(y=y[ma[z]],null==y){x=null;break a}x=y}var na=x&&x[610401301];w=null!=na?na:!1;var A,oa=t.navigator;A=oa?oa.userAgentData||null:null;function B(a){return w?A?A.brands.some(function(b){return(b=b.brand)&&-1!=b.indexOf(a)}):!1:!1}function C(a){var b;a:{if(b=t.navigator)if(b=b.userAgent)break a;b=""}return-1!=b.indexOf(a)};function D(){return w?!!A&&0<A.brands.length:!1}function E(){return D()?B("Chromium"):(C("Chrome")||C("CriOS"))&&!(D()?0:C("Edge"))||C("Silk")};var pa=D()?!1:C("Trident")||C("MSIE");!C("Android")||E();E();C("Safari")&&(E()||(D()?0:C("Coast"))||(D()?0:C("Opera"))||(D()?0:C("Edge"))||(D()?B("Microsoft Edge"):C("Edg/"))||D()&&B("Opera"));var qa={},F=null;var ra="undefined"!==typeof Uint8Array,sa=!pa&&"function"===typeof btoa;function G(){return"function"===typeof BigInt};var H=0,I=0;function ta(a){var b=0>a;a=Math.abs(a);var c=a>>>0;a=Math.floor((a-c)/4294967296);b&&(c=m(ua(c,a)),b=c.next().value,a=c.next().value,c=b);H=c>>>0;I=a>>>0}function va(a,b){b>>>=0;a>>>=0;if(2097151>=b)var c=""+(4294967296*b+a);else G()?c=""+(BigInt(b)<<BigInt(32)|BigInt(a)):(c=(a>>>24|b<<8)&16777215,b=b>>16&65535,a=(a&16777215)+6777216*c+6710656*b,c+=8147497*b,b*=2,1E7<=a&&(c+=Math.floor(a/1E7),a%=1E7),1E7<=c&&(b+=Math.floor(c/1E7),c%=1E7),c=b+wa(c)+wa(a));return c}
function wa(a){a=String(a);return"0000000".slice(a.length)+a}function ua(a,b){b=~b;a?a=~a+1:b+=1;return[a,b]};var J;J="function"===typeof Symbol&&"symbol"===typeof Symbol()?Symbol():void 0;var xa=J?function(a,b){a[J]|=b}:function(a,b){void 0!==a.g?a.g|=b:Object.defineProperties(a,{g:{value:b,configurable:!0,writable:!0,enumerable:!1}})},K=J?function(a){return a[J]|0}:function(a){return a.g|0},L=J?function(a){return a[J]}:function(a){return a.g},M=J?function(a,b){a[J]=b;return a}:function(a,b){void 0!==a.g?a.g=b:Object.defineProperties(a,{g:{value:b,configurable:!0,writable:!0,enumerable:!1}});return a};function ya(a,b){M(b,(a|0)&-14591)}function za(a,b){M(b,(a|34)&-14557)}
function Aa(a){a=a>>14&1023;return 0===a?536870912:a};var N={},Ba={};function Ca(a){return!(!a||"object"!==typeof a||a.g!==Ba)}function Da(a){return null!==a&&"object"===typeof a&&!Array.isArray(a)&&a.constructor===Object}function P(a,b,c){if(!Array.isArray(a)||a.length)return!1;var d=K(a);if(d&1)return!0;if(!(b&&(Array.isArray(b)?b.includes(c):b.has(c))))return!1;M(a,d|1);return!0}Object.freeze(new function(){});Object.freeze(new function(){});var Ea=/^-?([1-9][0-9]*|0)(\.[0-9]+)?$/;var Q;function Fa(a,b){Q=b;a=new a(b);Q=void 0;return a}
function R(a,b,c){null==a&&(a=Q);Q=void 0;if(null==a){var d=96;c?(a=[c],d|=512):a=[];b&&(d=d&-16760833|(b&1023)<<14)}else{if(!Array.isArray(a))throw Error();d=K(a);if(d&64)return a;d|=64;if(c&&(d|=512,c!==a[0]))throw Error();a:{c=a;var e=c.length;if(e){var f=e-1;if(Da(c[f])){d|=256;b=f-(+!!(d&512)-1);if(1024<=b)throw Error();d=d&-16760833|(b&1023)<<14;break a}}if(b){b=Math.max(b,e-(+!!(d&512)-1));if(1024<b)throw Error();d=d&-16760833|(b&1023)<<14}}}M(a,d);return a};function Ga(a){switch(typeof a){case "number":return isFinite(a)?a:String(a);case "boolean":return a?1:0;case "object":if(a)if(Array.isArray(a)){if(P(a,void 0,0))return}else if(ra&&null!=a&&a instanceof Uint8Array){if(sa){for(var b="",c=0,d=a.length-10240;c<d;)b+=String.fromCharCode.apply(null,a.subarray(c,c+=10240));b+=String.fromCharCode.apply(null,c?a.subarray(c):a);a=btoa(b)}else{void 0===b&&(b=0);if(!F){F={};c="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789".split("");d=["+/=",
"+/","-_=","-_.","-_"];for(var e=0;5>e;e++){var f=c.concat(d[e].split(""));qa[e]=f;for(var g=0;g<f.length;g++){var h=f[g];void 0===F[h]&&(F[h]=g)}}}b=qa[b];c=Array(Math.floor(a.length/3));d=b[64]||"";for(e=f=0;f<a.length-2;f+=3){var l=a[f],p=a[f+1];h=a[f+2];g=b[l>>2];l=b[(l&3)<<4|p>>4];p=b[(p&15)<<2|h>>6];h=b[h&63];c[e++]=g+l+p+h}g=0;h=d;switch(a.length-f){case 2:g=a[f+1],h=b[(g&15)<<2]||d;case 1:a=a[f],c[e]=b[a>>2]+b[(a&3)<<4|g>>4]+h+d}a=c.join("")}return a}}return a};function Ha(a,b,c){a=Array.prototype.slice.call(a);var d=a.length,e=b&256?a[d-1]:void 0;d+=e?-1:0;for(b=b&512?1:0;b<d;b++)a[b]=c(a[b]);if(e){b=a[b]={};for(var f in e)Object.prototype.hasOwnProperty.call(e,f)&&(b[f]=c(e[f]))}return a}function Ia(a,b,c,d,e){if(null!=a){if(Array.isArray(a))a=P(a,void 0,0)?void 0:e&&K(a)&2?a:Ja(a,b,c,void 0!==d,e);else if(Da(a)){var f={},g;for(g in a)Object.prototype.hasOwnProperty.call(a,g)&&(f[g]=Ia(a[g],b,c,d,e));a=f}else a=b(a,d);return a}}
function Ja(a,b,c,d,e){var f=d||c?K(a):0;d=d?!!(f&32):void 0;a=Array.prototype.slice.call(a);for(var g=0;g<a.length;g++)a[g]=Ia(a[g],b,c,d,e);c&&c(f,a);return a}function Ka(a){return a.s===N?a.toJSON():Ga(a)};function La(a,b,c){c=void 0===c?za:c;if(null!=a){if(ra&&a instanceof Uint8Array)return b?a:new Uint8Array(a);if(Array.isArray(a)){var d=K(a);if(d&2)return a;b&&(b=0===d||!!(d&32)&&!(d&64||!(d&16)));return b?M(a,(d|34)&-12293):Ja(a,La,d&4?za:c,!0,!0)}a.s===N&&(c=a.h,d=L(c),a=d&2?a:Fa(a.constructor,Ma(c,d,!0)));return a}}function Ma(a,b,c){var d=c||b&2?za:ya,e=!!(b&32);a=Ha(a,b,function(f){return La(f,e,d)});xa(a,32|(c?2:0));return a};function Na(a,b){a=a.h;return Oa(a,L(a),b)}function Oa(a,b,c,d){if(-1===c)return null;if(c>=Aa(b)){if(b&256)return a[a.length-1][c]}else{var e=a.length;if(d&&b&256&&(d=a[e-1][c],null!=d))return d;b=c+(+!!(b&512)-1);if(b<e)return a[b]}}function Pa(a,b,c,d,e){var f=Aa(b);if(c>=f||e){var g=b;if(b&256)e=a[a.length-1];else{if(null==d)return;e=a[f+(+!!(b&512)-1)]={};g|=256}e[c]=d;c<f&&(a[c+(+!!(b&512)-1)]=void 0);g!==b&&M(a,g)}else a[c+(+!!(b&512)-1)]=d,b&256&&(a=a[a.length-1],c in a&&delete a[c])}
function Qa(a,b){var c=Ra;var d=void 0===d?!1:d;var e=a.h;var f=L(e),g=Oa(e,f,b,d);if(null!=g&&"object"===typeof g&&g.s===N)c=g;else if(Array.isArray(g)){var h=K(g),l=h;0===l&&(l|=f&32);l|=f&2;l!==h&&M(g,l);c=new c(g)}else c=void 0;c!==g&&null!=c&&Pa(e,f,b,c,d);e=c;if(null==e)return e;a=a.h;f=L(a);f&2||(g=e,c=g.h,h=L(c),g=h&2?Fa(g.constructor,Ma(c,h,!1)):g,g!==e&&(e=g,Pa(a,f,b,e,d)));return e}function Sa(a,b){a=Na(a,b);return null==a||"string"===typeof a?a:void 0}
function Ta(a,b){var c=void 0===c?0:c;a=Na(a,b);if(null!=a)if(b=typeof a,"number"===b?Number.isFinite(a):"string"!==b?0:Ea.test(a))if("number"===typeof a){if(a=Math.trunc(a),!Number.isSafeInteger(a)){ta(a);b=H;var d=I;if(a=d&2147483648)b=~b+1>>>0,d=~d>>>0,0==b&&(d=d+1>>>0);b=4294967296*d+(b>>>0);a=a?-b:b}}else if(b=Math.trunc(Number(a)),Number.isSafeInteger(b))a=String(b);else{if(b=a.indexOf("."),-1!==b&&(a=a.substring(0,b)),!("-"===a[0]?20>a.length||20===a.length&&-922337<Number(a.substring(0,7)):
19>a.length||19===a.length&&922337>Number(a.substring(0,6)))){if(16>a.length)ta(Number(a));else if(G())a=BigInt(a),H=Number(a&BigInt(4294967295))>>>0,I=Number(a>>BigInt(32)&BigInt(4294967295));else{b=+("-"===a[0]);I=H=0;d=a.length;for(var e=b,f=(d-b)%6+b;f<=d;e=f,f+=6)e=Number(a.slice(e,f)),I*=1E6,H=1E6*H+e,4294967296<=H&&(I+=Math.trunc(H/4294967296),I>>>=0,H>>>=0);b&&(b=m(ua(H,I)),a=b.next().value,b=b.next().value,H=a,I=b)}a=H;b=I;b&2147483648?G()?a=""+(BigInt(b|0)<<BigInt(32)|BigInt(a>>>0)):(b=
m(ua(a,b)),a=b.next().value,b=b.next().value,a="-"+va(a,b)):a=va(a,b)}}else a=void 0;return null!=a?a:c}function S(a,b){a=Sa(a,b);return null!=a?a:""};function T(a,b,c){this.h=R(a,b,c)}T.prototype.toJSON=function(){return Ua(this,Ja(this.h,Ka,void 0,void 0,!1),!0)};T.prototype.s=N;T.prototype.toString=function(){return Ua(this,this.h,!1).toString()};
function Ua(a,b,c){var d=a.constructor.v,e=L(c?a.h:b);a=b.length;if(!a)return b;var f;if(Da(c=b[a-1])){a:{var g=c;var h={},l=!1,p;for(p in g)if(Object.prototype.hasOwnProperty.call(g,p)){var u=g[p];if(Array.isArray(u)){var jb=u;if(P(u,d,+p)||Ca(u)&&0===u.size)u=null;u!=jb&&(l=!0)}null!=u?h[p]=u:l=!0}if(l){for(var O in h){g=h;break a}g=null}}g!=c&&(f=!0);a--}for(p=+!!(e&512)-1;0<a;a--){O=a-1;c=b[O];O-=p;if(!(null==c||P(c,d,O)||Ca(c)&&0===c.size))break;var kb=!0}if(!f&&!kb)return b;b=Array.prototype.slice.call(b,
0,a);g&&b.push(g);return b};function Va(a){return function(b){if(null==b||""==b)b=new a;else{b=JSON.parse(b);if(!Array.isArray(b))throw Error(void 0);xa(b,32);b=Fa(a,b)}return b}};function Wa(a){this.h=R(a)}r(Wa,T);var Xa=Va(Wa);var U;function V(a){this.g=a}V.prototype.toString=function(){return this.g+""};var Ya={};function Za(a){if(void 0===U){var b=null;var c=t.trustedTypes;if(c&&c.createPolicy){try{b=c.createPolicy("goog#html",{createHTML:v,createScript:v,createScriptURL:v})}catch(d){t.console&&t.console.error(d.message)}U=b}else U=b}a=(b=U)?b.createScriptURL(a):a;return new V(a,Ya)};function $a(){return Math.floor(2147483648*Math.random()).toString(36)+Math.abs(Math.floor(2147483648*Math.random())^Date.now()).toString(36)};function ab(a,b){b=String(b);"application/xhtml+xml"===a.contentType&&(b=b.toLowerCase());return a.createElement(b)}function bb(a){this.g=a||t.document||document};/*

 SPDX-License-Identifier: Apache-2.0
*/
function cb(a,b){a.src=b instanceof V&&b.constructor===V?b.g:"type_error:TrustedResourceUrl";var c,d;(c=(b=null==(d=(c=(a.ownerDocument&&a.ownerDocument.defaultView||window).document).querySelector)?void 0:d.call(c,"script[nonce]"))?b.nonce||b.getAttribute("nonce")||"":"")&&a.setAttribute("nonce",c)};function db(a){a=void 0===a?document:a;return a.createElement("script")};function eb(a,b,c,d,e,f){try{var g=a.g,h=db(g);h.async=!0;cb(h,b);g.head.appendChild(h);h.addEventListener("load",function(){e();d&&g.head.removeChild(h)});h.addEventListener("error",function(){0<c?eb(a,b,c-1,d,e,f):(d&&g.head.removeChild(h),f())})}catch(l){f()}};var fb=t.atob("aHR0cHM6Ly93d3cuZ3N0YXRpYy5jb20vaW1hZ2VzL2ljb25zL21hdGVyaWFsL3N5c3RlbS8xeC93YXJuaW5nX2FtYmVyXzI0ZHAucG5n"),gb=t.atob("WW91IGFyZSBzZWVpbmcgdGhpcyBtZXNzYWdlIGJlY2F1c2UgYWQgb3Igc2NyaXB0IGJsb2NraW5nIHNvZnR3YXJlIGlzIGludGVyZmVyaW5nIHdpdGggdGhpcyBwYWdlLg=="),hb=t.atob("RGlzYWJsZSBhbnkgYWQgb3Igc2NyaXB0IGJsb2NraW5nIHNvZnR3YXJlLCB0aGVuIHJlbG9hZCB0aGlzIHBhZ2Uu");function ib(a,b,c){this.i=a;this.u=b;this.o=c;this.g=null;this.j=[];this.m=!1;this.l=new bb(this.i)}
function lb(a){if(a.i.body&&!a.m){var b=function(){mb(a);t.setTimeout(function(){nb(a,3)},50)};eb(a.l,a.u,2,!0,function(){t[a.o]||b()},b);a.m=!0}}
function mb(a){for(var b=W(1,5),c=0;c<b;c++){var d=X(a);a.i.body.appendChild(d);a.j.push(d)}b=X(a);b.style.bottom="0";b.style.left="0";b.style.position="fixed";b.style.width=W(100,110).toString()+"%";b.style.zIndex=W(2147483544,2147483644).toString();b.style.backgroundColor=ob(249,259,242,252,219,229);b.style.boxShadow="0 0 12px #888";b.style.color=ob(0,10,0,10,0,10);b.style.display="flex";b.style.justifyContent="center";b.style.fontFamily="Roboto, Arial";c=X(a);c.style.width=W(80,85).toString()+
"%";c.style.maxWidth=W(750,775).toString()+"px";c.style.margin="24px";c.style.display="flex";c.style.alignItems="flex-start";c.style.justifyContent="center";d=ab(a.l.g,"IMG");d.className=$a();d.src=fb;d.alt="Warning icon";d.style.height="24px";d.style.width="24px";d.style.paddingRight="16px";var e=X(a),f=X(a);f.style.fontWeight="bold";f.textContent=gb;var g=X(a);g.textContent=hb;Y(a,e,f);Y(a,e,g);Y(a,c,d);Y(a,c,e);Y(a,b,c);a.g=b;a.i.body.appendChild(a.g);b=W(1,5);for(c=0;c<b;c++)d=X(a),a.i.body.appendChild(d),
a.j.push(d)}function Y(a,b,c){for(var d=W(1,5),e=0;e<d;e++){var f=X(a);b.appendChild(f)}b.appendChild(c);c=W(1,5);for(d=0;d<c;d++)e=X(a),b.appendChild(e)}function W(a,b){return Math.floor(a+Math.random()*(b-a))}function ob(a,b,c,d,e,f){return"rgb("+W(Math.max(a,0),Math.min(b,255)).toString()+","+W(Math.max(c,0),Math.min(d,255)).toString()+","+W(Math.max(e,0),Math.min(f,255)).toString()+")"}function X(a){a=ab(a.l.g,"DIV");a.className=$a();return a}
function nb(a,b){0>=b||null!=a.g&&0!==a.g.offsetHeight&&0!==a.g.offsetWidth||(pb(a),mb(a),t.setTimeout(function(){nb(a,b-1)},50))}function pb(a){for(var b=m(a.j),c=b.next();!c.done;c=b.next())(c=c.value)&&c.parentNode&&c.parentNode.removeChild(c);a.j=[];(b=a.g)&&b.parentNode&&b.parentNode.removeChild(b);a.g=null};function qb(a,b,c,d,e){function f(l){document.body?g(document.body):0<l?t.setTimeout(function(){f(l-1)},e):b()}function g(l){l.appendChild(h);t.setTimeout(function(){h?(0!==h.offsetHeight&&0!==h.offsetWidth?b():a(),h.parentNode&&h.parentNode.removeChild(h)):a()},d)}var h=rb(c);f(3)}function rb(a){var b=document.createElement("div");b.className=a;b.style.width="1px";b.style.height="1px";b.style.position="absolute";b.style.left="-10000px";b.style.top="-10000px";b.style.zIndex="-10000";return b};function Ra(a){this.h=R(a)}r(Ra,T);function sb(a){this.h=R(a)}r(sb,T);var tb=Va(sb);function ub(a){var b=la.apply(1,arguments);if(0===b.length)return Za(a[0]);for(var c=a[0],d=0;d<b.length;d++)c+=encodeURIComponent(b[d])+a[d+1];return Za(c)};function vb(a){if(!a)return null;a=Sa(a,4);var b;null===a||void 0===a?b=null:b=Za(a);return b};var wb=ea([""]),xb=ea([""]);function yb(a,b){this.m=a;this.o=new bb(a.document);this.g=b;this.j=S(this.g,1);this.u=vb(Qa(this.g,2))||ub(wb);this.i=!1;b=vb(Qa(this.g,13))||ub(xb);this.l=new ib(a.document,b,S(this.g,12))}yb.prototype.start=function(){zb(this)};
function zb(a){Ab(a);eb(a.o,a.u,3,!1,function(){a:{var b=a.j;var c=t.btoa(b);if(c=t[c]){try{var d=Xa(t.atob(c))}catch(e){b=!1;break a}b=b===Sa(d,1)}else b=!1}b?Z(a,S(a.g,14)):(Z(a,S(a.g,8)),lb(a.l))},function(){qb(function(){Z(a,S(a.g,7));lb(a.l)},function(){return Z(a,S(a.g,6))},S(a.g,9),Ta(a.g,10),Ta(a.g,11))})}function Z(a,b){a.i||(a.i=!0,a=new a.m.XMLHttpRequest,a.open("GET",b,!0),a.send())}function Ab(a){var b=t.btoa(a.j);a.m[b]&&Z(a,S(a.g,5))};(function(a,b){t[a]=function(){var c=la.apply(0,arguments);t[a]=function(){};b.call.apply(b,[null].concat(c instanceof Array?c:fa(m(c))))}})("__h82AlnkH6D91__",function(a){"function"===typeof window.atob&&(new yb(window,tb(window.atob(a)))).start()});}).call(this);

window.__h82AlnkH6D91__("WyJwdWItNTIxNzI4NjU0NzM3NzY1NiIsW251bGwsbnVsbCxudWxsLCJodHRwczovL2Z1bmRpbmdjaG9pY2VzbWVzc2FnZXMuZ29vZ2xlLmNvbS9iL3B1Yi01MjE3Mjg2NTQ3Mzc3NjU2Il0sbnVsbCxudWxsLCJodHRwczovL2Z1bmRpbmdjaG9pY2VzbWVzc2FnZXMuZ29vZ2xlLmNvbS9lbC9BR1NLV3hWdlpyM1JueDMxV3FvdU1LS1I3WlJScDFLRFVYLVN4eUVGZEt2a2g3VlVsckllQXZmb0FRSmU4Ui1PSFRaYmZ1Y1pBclZ5OG9UMngzcXB4THg5RkZTeVBnXHUwMDNkXHUwMDNkP3RlXHUwMDNkVE9LRU5fRVhQT1NFRCIsImh0dHBzOi8vZnVuZGluZ2Nob2ljZXNtZXNzYWdlcy5nb29nbGUuY29tL2VsL0FHU0tXeFVQQW1PWHI4MGp5SUhCMVNhWFpRcUR2dTZjdUR3Yk9ZZE9hZkx5NHozUTBXczdjZWtHaU93cThtazB4VUhEVUdWMEZ1aGRDQ2ZwZkQwMGFmYW01MGN3dmdcdTAwM2RcdTAwM2Q/YWJcdTAwM2QxXHUwMDI2c2JmXHUwMDNkMSIsImh0dHBzOi8vZnVuZGluZ2Nob2ljZXNtZXNzYWdlcy5nb29nbGUuY29tL2VsL0FHU0tXeFVBemtKQlZ3bzN1cS0wN2I2Uk5IVkJ3VlpGZlVUUDUyaEdwbTJaYkx1dnpYUHlGUklGdGZUcWRRMWNuNWtpTGhDQlNpa0lCR0NaLThWZ0JidFM5OE1KZ0FcdTAwM2RcdTAwM2Q/YWJcdTAwM2QyXHUwMDI2c2JmXHUwMDNkMSIsImh0dHBzOi8vZnVuZGluZ2Nob2ljZXNtZXNzYWdlcy5nb29nbGUuY29tL2VsL0FHU0tXeFZJQU52S1JDYXFrUVFBbzAxcy1XbjBEalh3RHc5SjBCb3QzVEl6dld1d2ppemZEbDI2ZExRc0FEVDZGemgzRnpjVThqbTczd2llb1pRRFBqSmIyZjJaTXdcdTAwM2RcdTAwM2Q/c2JmXHUwMDNkMiIsImRpdi1ncHQtYWQiLDIwLDEwMCwiY0hWaUxUVXlNVGN5T0RZMU5EY3pOemMyTlRZXHUwMDNkIixbbnVsbCxudWxsLG51bGwsImh0dHBzOi8vd3d3LmdzdGF0aWMuY29tLzBlbW4vZi9wL3B1Yi01MjE3Mjg2NTQ3Mzc3NjU2LmpzP3VzcXBcdTAwM2RDQW8iXSwiaHR0cHM6Ly9mdW5kaW5nY2hvaWNlc21lc3NhZ2VzLmdvb2dsZS5jb20vZWwvQUdTS1d4VlFkQ2MzZGQybDVHbEQzdDZPR1BnWkRWZnMxT0l4OVVONXhoODdfTVRpRW1qZWlSQjZJOHdJZXVYYjhLQWdVUTBlUXFqVHNIMEZpZ1NNS1ZfM3lIdy1uZ1x1MDAzZFx1MDAzZCJd");</script>

    <script>
        // Wait for the page to load
        document.addEventListener("DOMContentLoaded", function() {
            // Get the buybtn element
            const buyBtn = document.querySelector(".buybtn");

            // Add event listener to remove animation class after animation completes
            function removeAnimationClass() {
                buyBtn.classList.remove("animate");
                // Remove the event listener to prevent re-triggering
                buyBtn.removeEventListener("animationend", removeAnimationClass);
            }

            // Add the animation class to trigger animation on load
            buyBtn.classList.add("animate");

            // Add event listener to remove animation class after animation completes
            buyBtn.addEventListener("animationend", removeAnimationClass);
        });
    </script>
</div>
        <?php endif; ?>
        <!-- Main content -->
        <section class="content " >
       
            <?php 
              if(!file_exists($page.".php") && !is_dir($page)){
                  include '404.html';
              }else{
                if(is_dir($page))
                  include $page.'/index.php';
                else
                  include $page.'.php';

              }
            ?>
         
        </section>
        <style>
    .modal-content {
        border-radius: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    .modal-header {
        border-bottom: none;
        border-radius: 20px 20px 0 0;
        background: linear-gradient(to bottom right, #9CDC78, #74DCB0);
    }
    .modal-header h5 {
        color: #fff;
    }
    .modal-footer {
        border-top: none;
        border-radius: 0 0 20px 20px;
    }
    .btn-primary {
        background-color: #00bfa5;
        border-color: #00bfa5;
    }
    .btn-primary:hover {
        background-color: #00796b;
        border-color: #00796b;
    }
    .btn-secondary {
        background-color: #ccc;
        border-color: #ccc;
    }
    .btn-secondary:hover {
        background-color: #999;
        border-color: #999;
    }
    .btn-close {
        background: none;
        border: none;
        font-size: 1.5em;
    }
    .btn-close:hover {
        color: #999;
    }
</style>
<div class="modal fade rounded-0" id="uni_modal" role='dialog'>
    <div class="modal-dialog modal-md modal-dialog-centered rounded-0" role="document">
        <div class="modal-content rounded-0">
            <div class="modal-header rounded-0">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                    <span class="fa fa-times"></span>
                </button>
            </div>
            <div class="modal-body rounded-0"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade rounded-0" id="confirm_modal" role='dialog'>
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header rounded-0">
                <h5 class="modal-title">Confirmation</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                    <span class="fa fa-times"></span>
                </button>
            </div>
            <div class="modal-body rounded-0">
                <div id="delete_content"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id='confirm' onclick="">Continue</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade rounded-0" id="uni_modal_right" role='dialog'>
    <div class="modal-dialog modal-full-height modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header rounded-0">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                    <span class="fa fa-arrow-right"></span>
                </button>
            </div>
            <div class="modal-body rounded-0"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewer_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-dismiss="modal">
                <span class="fa fa-times"></span>
            </button>
            <img src="" alt="">
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal Title</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                This is the modal body content.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

    <!-- Bootstrap JS and dependencies -->
    <?php require_once('inc/footer.php') ?>
      <!-- /.content-wrapper -->  </body>
</html>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const arrowBtn = document.getElementById("floating-arrow");

    // Toggle glow effect on hover
    arrowBtn.addEventListener("mouseenter", function() {
      arrowBtn.querySelector(".arrow-icon").classList.add("glow");
    });

    arrowBtn.addEventListener("mouseleave", function() {
      arrowBtn.querySelector(".arrow-icon").classList.remove("glow");
    });
  });
</script>
