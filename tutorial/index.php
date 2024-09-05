<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmers Member/Seller Portal Tutorial</title>
    <link href="https://fonts.googleapis.com/css2?family=roboto:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
        }
        .tutorial-body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f4f4f4;
        }
        .tutorial-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            max-width: 1000px;
            width: 90%;
            margin: 20px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section img {
            max-width: 100%;
            border-radius: 10px;
            margin-top: 10px;
        }
        .header-h1 {
            text-align: center;
            color: #333;
            font-weight: 600;
        }
        .header-h2 {
            color: #555;
            font-weight: 600;
            transition: transform 0.3s ease-in-out;
        }
        .paragraph {
            color: #666;
            line-height: 1.6;
        }
        @media (max-width: 600px) {
            .tutorial-container {
                padding: 15px;
            }
            .header-h1 {
                font-size: 1.5em;
            }
            .header-h2 {
                font-size: 1.2em;
            }
            .section img {
                width: 100%;
            }
        }
        .toc {
            margin-bottom: 20px;
        }
        .toc ul {
            list-style-type: none;
            padding: 0;
        }
        .toc li {
            margin-bottom: 10px;
        }
        .toc a {
            color: #0066cc;
            text-decoration: none;
        }
        .toc a:hover {
            text-decoration: underline;
        }
        .pulse {
            animation: pulse-animation 0.6s ease-in-out;
        }
        @keyframes pulse-animation {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }
    </style>
</head>
<body>
    <div class="tutorial-body">
        <div class="tutorial-container">
            <h1 class="header-h1">Farmers Member/Seller Portal Tutorial</h1>

            <div class="toc">
                <h2 class="header-h2">Table of Contents</h2>
                <ul>
                    <li><a href="#crop-management">Crop Management</a></li>
                    <li><a href="#financial-transparency">Financial Transparency</a></li>
                    <li><a href="#marketplace">Marketplace</a></li>
                    <li><a href="#farm-map">Farm Map</a></li>
                    <li><a href="#commission-overview">Commission Overview</a></li>
                </ul>
            </div>

            <div class="section" id="crop-management">
                <h2 class="header-h2">Crop Management</h2>
                <p class="paragraph">The Crop Management section allows you to create digital representations of your farms, pinpoint crop locations, and track harvest quantities. You can also report pest and disease sightings, which will notify admins for immediate action.</p>
                <img src="path-to-image/crop-management.png" alt="Crop Management">
            </div>

            <div class="section" id="financial-transparency">
                <h2 class="header-h2">Financial Transparency</h2>
                <p class="paragraph">This section ensures transparency in financial dealings. Members can view receipts, budget allocations, and monitor remaining funds. This feature helps build trust within the community.</p>
                <img src="path-to-image/financial-transparency.png" alt="Financial Transparency">
            </div>

            <div class="section" id="marketplace">
                <h2 class="header-h2">Marketplace</h2>
                <p class="paragraph">Manage your sales and commissions here. Get insights into total products sold, overall sales figures, and pending orders. Track commissions owed to the association for accurate financial management.</p>
                <img src="path-to-image/marketplace.png" alt="Marketplace">
            </div>

            <div class="section" id="farm-map">
                <h2 class="header-h2">Farm Map</h2>
                <p class="paragraph">The Farm Map visually represents crop locations and helps in identifying areas affected by pests. It also highlights available produce for sale, making it easier for buyers to find fresh products.</p>
                <img src="path-to-image/farm-map.png" alt="Farm Map">
            </div>

            <div class="section" id="commission-overview">
                <h2 class="header-h2">Commission Overview</h2>
                <p class="paragraph">View a monthly overview of commissions, including detailed status information. Update statuses with ease and ensure accurate record-keeping for timely payments.</p>
                <img src="path-to-image/commission-overview.png" alt="Commission Overview">
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.toc a').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.header-h2').forEach(header => {
                    header.classList.remove('pulse');
                });
                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId).querySelector('.header-h2');
                const headerOffset = 100; // adjust this value according to your navigation bar height
                const elementPosition = targetElement.getBoundingClientRect().top + window.pageYOffset;
                const offsetPosition = elementPosition - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });

                setTimeout(() => {
                    targetElement.classList.add('pulse');
                }, 700); // Delay for 0.7 seconds before adding the pulse class
            });
        });
    </script>
</body>
</html>
