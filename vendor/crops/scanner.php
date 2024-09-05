
<style>
.container1 {
    width: 100%;
    max-width: 500px;
    margin: 5px;
}
 
.container1 h1 {
    color: #ffffff;
}
 
.section1 {
    padding: 50px 30px;
    border: 1.5px solid #b2b2b2;
    border-radius: 0.25em;
    box-shadow: 0 20px 25px rgba(0, 0, 0, 0.25);
}
 
#my-qr-reader {
    padding: 20px !important;
    border: 1.5px solid #b2b2b2 !important;
    border-radius: 8px;
}
 
#my-qr-reader img[alt="Info icon"] {
    display: none;
}
 
#my-qr-reader img[alt="Camera based scan"] {
    width: 100px !important;
    height: 100px !important;
}

#html5-qrcode-anchor-scan-type-change {
    text-decoration: none !important;
    color: #1d9bf0;
}
 
video {
    width: 100% !important;
    border: 1px solid #b2b2b2 !important;
    border-radius: 0.25em;
}
</style>
<body>
	<div class="container1">
		<h1>Scan QR Codes</h1>
		<div class="section1">
			<div id="my-qr-reader">
			</div>
		</div>
	</div>
	<script
		src="https://unpkg.com/html5-qrcode">
	</script>
	<script src="crops/script.js"></script>
</body>

</html>
