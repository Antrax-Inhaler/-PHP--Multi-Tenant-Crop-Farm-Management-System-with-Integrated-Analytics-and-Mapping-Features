<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
<style>
        /* Set up a flex container for the layout */
        #main-container {
            display: flex;
            flex-direction: row;
            height: 100vh;
            margin: 0;
        }

        /* Calendar section on the left side */
        #calendar-container {
            flex: 3; /* 75% of the width */
            padding: 20px;
            border-right: 2px solid #ddd;
        }

        /* Scheduler form on the right side */
        #scheduler-container {
            flex: 1; /* 25% of the width */
            padding: 20px;
        }

        /* Form and map styling */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        .form-group button {
            padding: 10px 15px;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        #response, #generatedMessage {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            min-height: 100px;
            max-height: 400px;
            overflow-y: auto;
        }

        #map {
            height: 300px; /* Set a fixed height for the map */
            width: 100%;
        }

        /* Ensure the calendar takes up its container's space */
        #calendar {
            height: 100%;
        }

        /* Media query for smaller screens */
        @media (max-width: 768px) {
            #main-container {
                flex-direction: column; /* Stack elements vertically */
                height: auto; /* Let height adjust based on content */
            }

            #calendar-container, #scheduler-container {
                flex: 1; /* Equal height for both containers on small screens */
                width: 100%; /* Make both containers take full width */
                padding: 10px;
                border-right: none; /* Remove border when stacked */
            }

            #calendar-container {
                order: 2; /* Show the calendar after the form on small screens */
            }

            #scheduler-container {
                order: 1; /* Show the scheduler form first on small screens */
            }
        }

        /* Further media query for very small screens */
        @media (max-width: 480px) {
            .form-group button {
                padding: 8px 10px;
                font-size: 14px;
            }

            #response, #generatedMessage {
                font-size: 14px;
            }

            h1 {
                font-size: 20px;
            }
        }
        .centerer{
        width: 100% !important;
    }
    .loading {
        position: relative;
        height: 100px; /* Adjust as needed */
        line-height: 100px;
        text-align: center;
        font-size: 18px;
        color: #000;
        background: #f8f9fa;
    }

    .loading::before {
        content: 'Analyzing...';
        position: absolute;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to right, transparent 0%, rgba(0, 0, 0, 0.2) 50%, transparent 100%);
        background-size: 200% 100%;
        animation: shine 1.5s infinite;
        z-index: 1;
    }

    .loading span {
        position: relative;
        z-index: 2;
    }

    @keyframes shine {
        0% {
            background-position: -200% 0;
        }
        100% {
            background-position: 200% 0;
        }
    }
.form-row {
    display: flex;
    gap: 15px;
    flex-direction: row;
}

.form-group {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.form-group label {
    margin-bottom: 5px;
}
.fc-event {
            cursor: pointer;
        }
        .fc-event .avatar {
            border-radius: 50%;
            width: 30px;
            height: 30px;
            object-fit: cover;
        }
        .modal {
    position: fixed;
    z-index: 999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
    display: none;
}

.modal-content {
    position: relative;
    width: 100%;
    max-width: 600px;
    margin: auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 5px;
}

.close {
    position: absolute;
    top: 10px;
    right: 15px;
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
button {
  position: relative;
  padding: 10px 20px;
  font-size: 16px;
  cursor: pointer;
  border: 2px solid #3498db;
  background-color: #3498db;
  color: white;
  border-radius: 5px;
  transition: background-color 0.3s ease;
}

button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.checkmark {
  display: none;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

button.animate-checkmark .checkmark {
  display: block;
}

.checkmark svg {
  width: 24px;
  height: 24px;
}

input {
  padding: 10px;
  width: 100%;
  border: 2px solid #ccc;
  border-radius: 5px;
  font-size: 16px;
}
.modal-backdrop {

    z-index: 992 !important;

}
    </style>
</head>
<body>
    <div id="main-container">
        <div id="calendar-container">
    <h1>Crop Planting Calendar</h1>

    <!-- Add crop filter dropdown -->
    <label for="cropFilterSelect">Filter by Crop:</label>
    <select id="cropFilterSelect" onchange="filterCalendarByCrop();">
        <option value="">All Crops</option>
        <!-- Dynamically populate options from the database -->
    </select>

    <div id="calendar"></div>
</div>


        <!-- Right Side: Planting Date Scheduler -->
        <div id="scheduler-container">
            <h3>Planting Date Scheduler</h3>
            <form id="plantingScheduler">
                <div class="form-group">
                    <label for="cropSelect">Select Crop:</label>
                    <select id="cropSelect" name="crop">
                        <option value="Rice">Rice</option>
                        <option value="corn">Corn</option>
                        <option value="wheat">Wheat</option>
                        <!-- Add more crops as needed -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="map">Select Your Planting Location</label>
                    <div id="map"></div>
                    <input type="hidden" id="latitude" name="latitude" placeholder="Latitude">
                    <input type="hidden" id="longitude" name="longitude" placeholder="Longitude">
                </div>
                <div class="form-row">
    <div class="form-group">
        <label for="startDate">Start Date (Optional):</label>
        <input type="date" id="startDate" name="startDate">
    </div>
    <div class="form-group">
        <label for="endDate">End Date (Optional):</label>
        <input type="date" id="endDate" name="endDate">
    </div>
</div>

                <div class="form-group">
                <button type="button" id="generateButton" onclick="generateSchedule(); triggerCheckmark();">Generate Schedule</button>
                </div>
            </form>

            <div class="form-group">
                <input type="hidden" id="messageInput" rows="4" readonly></input>
            </div>

            <div class="form-group">
                <button  style="background: linear-gradient(to bottom right, #9CDC78, #74DCB0) !important; type="button" onclick="submitToAI()">Submit to AI</button>
            </div>
            <button id="create_new">Add Crop</button>
<!-- Add this to your HTML where appropriate -->
<div id="response">
</div>
        </div>
    </div>

    <div id="cropModal" class="modal">
    <div class="card modal-content">
        <div class="card-body">
            <span class="close">&times;</span>
            <div class="row">
                <div class="">
                    <img id="modalAvatar" alt="Avatar" width="100">
                </div>
                <div class="col-md-8">
                    <strong id="modalTitle"></strong><br>
                    <ul class="list-unstyled">
                        <li><strong>Planned Planting Date:</strong> <span id="modalDate"></span></li>
                        <li>
                            <strong>Contact:</strong>
                            <a id="modalContact" style="color: blue; text-decoration: underline;">
                                <i class="fa fa-phone"></i> <span id="contactText"></span>
                            </a>
                        </li>
                        <li>
                            <a id="modalFacebook" class="btn btn-sm btn-info">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </a>
                        </li>
                        <li><strong>Username:</strong> <span id="modalUsername"></span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


    <p id="cropInfo">
    <script>
function generateSchedule() {
    let crop = $('#cropSelect').val();
    let startDate = $('#startDate').val();
    let endDate = $('#endDate').val();

    let generatedMessage = `I want to plant ${crop}. Can you recommend me a date when I can plant my crop.`;

    if (startDate || endDate) {
        generatedMessage += ` I'm considering planting between ${startDate || 'unknown'} and ${endDate || 'unknown'}.`;
    }

    $.ajax({
        url: 'crops/fetch_neighboring_crops.php',
        method: 'GET',
        dataType: 'json',
        data: {
            crop: crop,
            latitude: $('#latitude').val(),
            longitude: $('#longitude').val()
        },
        success: function(response) {
            if (response.success) {
                if (response.neighboringCrops.length > 0 && (startDate || endDate)) {
                    let neighborInfo = " I want to synchronize my planting schedule with neighboring farms planting the same crop.  Note: Some neighboring farms have planting dates outside of your specified range. Please provide advice on how to adjust my schedule or offer recommendations. Here are the details of neighboring crops:";
                    let outOfRange = false;

                    response.neighboringCrops.forEach(crop => {
                        neighborInfo += ` ${crop.Name} plans to plant on ${crop.PlannedPlantingDate}.`;
                        if (startDate && endDate && (crop.PlannedPlantingDate < startDate || crop.PlannedPlantingDate > endDate)) {
                            outOfRange = true;
                        }
                    });

                    if (outOfRange) {
                        neighborInfo += " Note: Some neighboring farms have planting dates outside of your specified range. Please provide advice on how to adjust my schedule or offer recommendations.";
                    }

                    generatedMessage += neighborInfo;
                } else if (response.neighboringCrops.length === 0) {
                    generatedMessage += ` The provided details do not mention any plans for your farm to plant ${crop}.`;
                }
            } else {
                generatedMessage += "";
            }

            $('#messageInput').val(generatedMessage);
        },
        error: function() {
            $('#messageInput').val(" There was an error fetching neighboring crops.");
        }
    });
}

function triggerCheckmark() {
    const input = document.getElementById('messageInput');
    const button = document.getElementById('generateButton');

    // Check if the input has content
    if (input.value.trim() !== "") {
        // Add the checkmark animation
        button.classList.add('animate-checkmark');

        // Disable the button
        button.disabled = true;

        // Add a checkmark inside the button
        const checkmarkHTML = `
            <div class="checkmark">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
        `;
        button.innerHTML = checkmarkHTML;
    } else {
        alert("Please fill in the input before generating the schedule.");
    }
}



function submitToAI() {
    let generatedMessage = $('#messageInput').val();
    if (!generatedMessage) {
        alert('Please generate a schedule first.');
        return;
    }

    // Show loading indicator
    $('#response').html('<div class="loading"><span>Analyzing...</span></div>');

    $.ajax({
        url: 'crops/ai.php',
        method: 'POST',
        dataType: 'json',
        data: { message: generatedMessage },
        success: function(response) {
            var content = response.content;

            content = content.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');
            content = content.replace(/\*/g, '<br><br>');
            $('#response').html('<h6>' + content + '</h6>');
        },
        error: function(xhr, status, error) {
            $('#response').html('<h6>Oops! Something went wrong.</h6>');
        }
    });
}



        var map;

function initMap() {
    var defaultLocation = { lat: 13.293002, lng: 121.193405 }; // Default coordinates
    map = new google.maps.Map(document.getElementById('map'), {
        center: defaultLocation,
        zoom: 13
    });

    marker = new google.maps.Marker({
        position: defaultLocation,
        map: map,
        draggable: true,
        icon: {
            url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png', // Custom marker image
            scaledSize: new google.maps.Size(32, 32) // Marker size
        }
    });

    google.maps.event.addListener(marker, 'dragend', function() {
        updateMarkerPosition(marker.getPosition());
    });

    // Fetch crop data and add markers and circles to the map
    fetch('crops/fetch_plantation_data.php') // Adjust the path to your PHP file
        .then(response => response.json())
        .then(data => {
            data.forEach(crop => {
                addCropMarkerAndCircle(crop);
            });
        });

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                var userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                map.setCenter(userLocation);
                marker.setPosition(userLocation);
                updateMarkerPosition(userLocation);
            },
            function() {
                handleLocationError(true);
            }
        );
    } else {
        handleLocationError(false);
    }
}
// Fetch crop names from the database and populate the dropdown
function populateCropFilter() {
    $.ajax({
        url: 'crops/fetch_crops.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                let cropFilterSelect = $('#cropFilterSelect');
                cropFilterSelect.empty();
                response.crops.forEach(crop => {
                    cropFilterSelect.append(`<option value="${crop.Name}">${crop.Name}</option>`);
                });
            } else {
                alert('Failed to load crop names');
            }
        },
        error: function() {
            alert('Error loading crop names');
        }
    });
}


function addCropMarkerAndCircle(crop) {
    // Create marker for the crop
    var cropMarker = new google.maps.Marker({
        position: { lat: parseFloat(crop.Latitude), lng: parseFloat(crop.Longitude) },
        map: map,
        title: crop.Name,
        icon: {
            url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png', // Green marker for crops
            scaledSize: new google.maps.Size(32, 32)
        }
    });

    var radiusInMeters = Math.sqrt((crop.SizeOfPlantation * 10000) / Math.PI);

    var cropCircle = new google.maps.Circle({
        strokeColor: '#FF0000',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: '#FF0000',
        fillOpacity: 0.35,
        map: map,
        center: { lat: parseFloat(crop.Latitude), lng: parseFloat(crop.Longitude) },
        radius: radiusInMeters 
    });
}

function updateMarkerPosition(latLng) {
    const lat = latLng.lat().toFixed(6);
    const lng = latLng.lng().toFixed(6);
    
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;
}
function handleLocationError(browserHasGeolocation) {
    var errorMessage = browserHasGeolocation ?
        'Error: The Geolocation service failed.' :
        'Error: Your browser doesn\'t support geolocation.';
    alert(errorMessage);
}

    </script>
<script>
    // Store original event data for filtering
let originalEvents = [];

// Function to filter calendar by crop
function filterCalendarByCrop() {
    let selectedCrop = $('#cropFilterSelect').val();
    
    if (selectedCrop === "") {
        // Show all events if no crop is selected
        calendar.removeAllEvents();
        calendar.addEventSource(originalEvents);
    } else {
        let filteredEvents = originalEvents.filter(event => event.title === selectedCrop);
        calendar.removeAllEvents();
        calendar.addEventSource(filteredEvents);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var closeBtn = document.querySelector('.modal .close');
    var modal = document.getElementById('cropModal');
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: 'crops/fetch_planting_schedule.php',
        eventContent: function(arg) {
            let customHtml = `
                <div style="display: flex; align-items: center;">
                    <img src="../${arg.event.extendedProps.ownerAvatar}" alt="Avatar" style="width:30px; height:30px; border-radius:50%; margin-right:10px;">
                    <span>${arg.event.title}</span>
                </div>
            `;
            return { html: customHtml };
        },
        eventClick: function(info) {
            // Set modal data
            document.getElementById('modalTitle').innerText = info.event.title;
            document.getElementById('modalDate').innerText = info.event.startStr;
            document.getElementById('modalAvatar').src = `../${info.event.extendedProps.avatar || ''}`;
            document.getElementById('modalContact').href = `tel:${info.event.extendedProps.contact}`;
            document.getElementById('contactText').innerText = info.event.extendedProps.contact;
            document.getElementById('modalFacebook').href = `https://www.facebook.com/${info.event.extendedProps.facebook}`;
            document.getElementById('modalFacebook').innerText = info.event.extendedProps.facebook;
            document.getElementById('modalUsername').innerText = info.event.extendedProps.username;

            // Show the modal
            document.getElementById('cropModal').style.display = 'flex';

            // Handle username click to trigger chat opening
            document.getElementById('modalUsername').onclick = function() {
                $('.chatbot-toggler').click();
                // Retrieve the vendor role and user ID from the event's extended properties
                let vendorRole = 'vendor'; // Predefined role is 'vendor'
                let userId = info.event.extendedProps.userId;

                // Now trigger the chat opening
                if (userId && vendorRole) {
                    fetchMessages(userId, vendorRole); // This opens the chat with the selected user
                }
            };
        }
    });

    calendar.render();

  
});


</script>
<script>
  // Get the modal element
  var modal = document.getElementById("cropModal");

  // Get the close button element
  var closeButton = document.getElementsByClassName("close")[0];

  // Function to open the modal
  function openModal() {
    modal.style.display = "flex"; // Set display to flex to align content to center
  }

  // Function to close the modal when the close button is clicked
  closeButton.onclick = function() {
    modal.style.display = "none";
  }

  // Function to close the modal when the user clicks outside the modal content
  window.onclick = function(event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  }
</script>

<script>
    $(document).ready(function(){
        $('#create_new').click(function(){
            // Show a modal with a form where the user can input the farm ID
            uni_modal('Add New Planting Schedule', 'crops/manage_sched.php', 'large');
        });
    });
</script>

<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCPgOaKhjwvksUVP6qBQpjdq3bTQa57NuQ&callback=initMap"
    async
  ></script> 