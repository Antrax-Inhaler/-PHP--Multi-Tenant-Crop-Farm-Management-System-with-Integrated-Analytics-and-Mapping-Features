<style>
  /* styles.css */

body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
}

header {
  background-color: #014D64;
  color: #fff;
  padding: 20px;
  text-align: center;
}

main {
  padding: 20px;
}

h2 {
  margin-top: 0;
}

#crop-list {
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-start;
  gap: 20px;
  padding-right: 20px;
}

.crop-card {
  width: 220px;
            height: 315px;
            background-color: white;
            border-radius:  20px;
            margin-bottom: 20px;

}

.crop-card:hover {
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.267);
}

.crop-photo-container {
  width: 100%;
            height: 210px;
            background-color: #45a0496e;
            background-size: cover;
            background-position: center;
            border-radius:  50px;
            cursor: pointer;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.267);
}

.crop-profile {
  width: 100%;
  height: 100%;
  background-size: cover;
  background-position: center;
  border-radius:  20px;

}

.crop-info {
  padding: 10px;
}

.crop-info h3 {
  margin-top: 0;
}

.crop-info p {
  margin: 5px 0;
}

</style>
<body>
  <header>
    <h1>Crop Management</h1>
  </header>
  <main>
    <section id="crop-list">
      <button class="crop-card" onclick="viewCrop('Crop Name 1', 'Rice', 'January 1, 2024')">
        <div class="crop-photo-container">
          <div class="crop-profile" style="background-image: url('alt.jpg');"></div>
        </div>
        <div class="crop-info">
          <h3>Crop Name 1</h3>
          <p>Type: Rice</p>
          <p>Planting Date: January 1, 2024</p>
        </div>
      </button>
      <button class="crop-card" onclick="viewCrop('Crop Name 1', 'Rice', 'January 1, 2024')">
        <div class="crop-photo-container">
          <div class="crop-profile" style="background-image: url('alt.jpg');"></div>
        </div>
        <div class="crop-info">
          <h3>Crop Name 1</h3>
          <p>Type: Rice</p>
          <p>Planting Date: January 1, 2024</p>
        </div>
      </button>
      <button class="crop-card" onclick="viewCrop('Crop Name 2', 'Corn', 'February 15, 2024')">
        <div class="crop-photo-container">
          <div class="crop-profile" style="background-image: url('alt.jpg');"></div>
        </div>
        <div class="crop-info">
          <h3>Crop Name 2</h3>
          <p>Type: Corn</p>
          <p>Planting Date: February 15, 2024</p>
        </div>
      </button>
      <!-- Add more crop cards here -->
    </section>
  </main>
</body>
</html>
