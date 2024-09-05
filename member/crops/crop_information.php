
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

</head>

<body>
  <header class="bg-primary text-white text-center py-4">
    <h1>Crop Information</h1>
  </header>
  <main class="container py-4">
    <section id="crop-details">
      <div class="section-heading mb-4">
        <h2 class="text-primary">Crop Details</h2>
      </div>
      <div class="row">
        <!-- Display crop pictures (replace src with actual URLs from the database) -->
        <div class="col-md-4 mb-4">
          <img src="alt.jpg" alt="Crop Picture 1" class="img-fluid">
        </div>
        <div class="col-md-4 mb-4">
          <img src="alt.jpg" alt="Crop Picture 2" class="img-fluid">
        </div>
        <div class="col-md-4 mb-4">
          <img src="alt.jpg" alt="Crop Picture 3" class="img-fluid">
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <!-- Display crop details -->
          <p><strong>Name:</strong> <?php echo $cropName; ?></p>
          <p><strong>Type:</strong> <?php echo $cropType; ?></p>
          <p><strong>Planned Planting Date:</strong> <?php echo $plannedPlantingDate; ?></p>
          <p><strong>Date Planted:</strong> <?php echo $datePlanted; ?></p>
          <p><strong>Size of Plantation:</strong> <?php echo $sizeOfPlantation; ?></p>
        </div>
        <div class="col-md-6">
          <!-- Display additional crop details -->
          <p><strong>Description:</strong> Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
          <p><strong>Status:</strong> Alive</p>
        </div>
      </div>
    </section>
    <section id="harvesting-information">
      <div class="section-heading">
        <h2>Harvesting Information</h2>
      </div>
      <div class="harvest-info">
        <!-- Display harvesting information -->
        <p><strong>Harvested Date:</strong> <?php echo $harvestedDate; ?></p>
        <p><strong>Amount of Harvest:</strong> <?php echo $amountOfHarvest; ?></p>
      </div>
    </section>
    <header class="bg-primary text-white text-center py-4">
      <h1>Flash Recommended Activity</h1>
    </header>
    <main class="container py-4">
      <section id="recommended-activity">
        <div class="section-heading mb-4">
          <h2 class="text-primary">Recommended Activity</h2>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div id="flash-message" class="alert alert-info" role="alert">
              <!-- Flash recommended activity content -->
              <?php echo $recommendedActivity; ?>
            </div>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-md-12">
            <p id="explanation" class="text-muted">Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quod fugiat, a voluptatem ex, minus sequi et, accusamus atque ullam reprehenderit consequatur expedita? Nulla deleniti quos at. Quis odio soluta, accusamus quos pariatur voluptatum quo molestiae? Dolore eveniet nesciunt minus
            repellat!<!-- Explanation for the recommended activity will appear here --></p>
          </div>
        </div>
      </section>
      <section id="pest-disease" class="mt-5">
        <div class="section-heading mb-4">
          <h2 class="text-primary">Pest and Disease</h2>
        </div>
        <div class="row">
          <div class="col-md-4 mb-4">
            <div class="card">
              <img src="alt.jpg" class="card-img-top" alt="Pest 1">
              <div class="card-body">
                <h5 class="card-title">Pest 1</h5>
                <!-- Display pest information -->
                <p class="card-text">CropID: 1</p>
                <p class="card-text">RefId: 1</p>
                <p class="card-text">Size of Area affected: Small</p>
                <p class="card-text">Status: Existing</p>
                <a href="#" class="btn btn-primary mb-2">Report to Association</a>
                <button class="btn btn-info" data-toggle="modal" data-target="#pestDiseaseModal">Reference Pest Disease</button>
                <p class="text-muted">Recommended Products:</p>
                <ul>
                  <!-- Display recommended products -->
                  <li><a href="#">Fungicide A</a></li>
                  <li><a href="#">Insecticide B</a></li>
                  <!-- Add more recommended products -->
                </ul>
              </div>
            </div>
          </div>
          <!-- Add more cards as needed -->
        </div>
      </section>
    </main>
    <div class="modal fade" id="pestDiseaseModal" tabindex="-1" role="dialog" aria-labelledby="pestDiseaseModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="pestDiseaseModalLabel">Reference Pest Disease Information</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <!-- Add content for Pest Disease reference information here -->
            <p><strong>Management Information:</strong> Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            <p><strong>Symptoms:</strong> Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            <p><strong>Preventive Measures:</strong> Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            <p><strong>Curative Measures:</strong> Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
      // Flash the recommended activity message
      $("#flash-message").text("<?php echo $recommendedActivity; ?>").fadeIn(1000).fadeOut(3000);
    </script>
  </body>
</html>
