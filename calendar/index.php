<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
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
    <div id="main-container">
        <div id="calendar-container">
            <h1>Crop Planting Calendar</h1>

            <!-- Filters Section -->
           

            <div id="calendar"></div>
        </div>
        <!-- <div id="filters">
                <div class="form-group">
                    <label for="cropName">Crop Name:</label>
                    <input type="text" id="cropName" placeholder="Enter crop name">
                </div>
                <div class="form-group">
                    <label for="cropType">Crop Type:</label>
                    <input type="text" id="cropType" placeholder="Enter crop type">
                </div>
                <div class="form-group">
                    <label for="plantingDateFrom">Planting Date From:</label>
                    <input type="date" id="plantingDateFrom">
                </div>
                <div class="form-group">
                    <label for="plantingDateTo">Planting Date To:</label>
                    <input type="date" id="plantingDateTo">
                </div>
                <div class="form-group">
                    <label for="plantationSize">Plantation Size Range (e.g., 10-50):</label>
                    <input type="text" id="plantationSize" placeholder="Enter range (e.g., 10-50)">
                </div>
                <button id="applyFilters" onclick="filterCalendarByCrop()">Apply Filters</button>
            </div> -->
    </div>
    <div id="cropModal" class="modal">
    <div class="card modal-content">
        <div class="card-body">
            <!-- Close Button -->
            <span class="close" onclick="closeModal()">&times;</span>
            <div class="row">
                <div class="">
                    <img id="modalAvatar" alt="Avatar" width="100">
                </div>
                <div class="col-md-8">
                    <strong id="modalTitle"></strong><br>
                    <ul class="list-unstyled">
                        <li><strong>Type:</strong> <span id="modalType"></span></li>
                        <li><strong>Planned Planting Date:</strong> <span id="modalPlannedDate"></span></li>
                        <li><strong>Date Planted:</strong> <span id="modalDatePlanted"></span></li>
                        <li><strong>Size of Plantation:</strong> <span id="modalSize"></span> hectares</li>
                        <li><strong>Description:</strong> <span id="modalDescription"></span></li>
                        <li><strong>Pictures:</strong>
                            <div id="modalPictures" style="display: flex; gap: 10px;">
                                <!-- Pictures will be dynamically appended -->
                            </div>
                        </li>
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
                        <li>
    <a id="viewMoreDetails" class="btn btn-sm btn-primary" href="#" target="_blank">
        View More Details
    </a>
</li>

                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    // Function to close the modal
    function closeModal() {
        document.getElementById('cropModal').style.display = 'none';
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
        const modal = document.getElementById('cropModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    };
</script>

    <script>
        let calendar;

        // Function to load filtered events into the calendar
        function filterCalendarByCrop() {
            const cropName = document.getElementById('cropName').value;
            const cropType = document.getElementById('cropType').value;
            const plantingDateFrom = document.getElementById('plantingDateFrom').value;
            const plantingDateTo = document.getElementById('plantingDateTo').value;
            const plantationSize = document.getElementById('plantationSize').value;

            // Construct filters to send to the backend
            const filters = {
                cropName,
                cropType,
                plantingDateFrom,
                plantingDateTo,
                plantationSize,
            };

            // Fetch filtered events and refresh the calendar
            fetch('calendar/fetch_planting_schedule.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(filters),
            })
                .then((response) => response.json())
                .then((events) => {
                    // Remove all existing events and render the filtered ones
                    calendar.removeAllEvents();
                    calendar.addEventSource(events);
                })
                .catch((error) => {
                    console.error('Error fetching filtered events:', error);
                });
        }

        // Initialize the calendar
        document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            start: 'title', // Display the title
            center: 'prevYear,prev,next,nextYear', // Add year navigation
            end: 'today'
        },
        buttonText: {
            today: 'Today',
            month: 'Month',
        },
        events: 'calendar/fetch_planting_schedule.php',
        eventClick: function (info) {
            const eventData = info.event.extendedProps;

            document.getElementById('modalTitle').innerText = info.event.title;
            document.getElementById('modalType').innerText = eventData.type || 'N/A';
            document.getElementById('modalPlannedDate').innerText = info.event.startStr;
            document.getElementById('modalDatePlanted').innerText = eventData.datePlanted || 'N/A';
            document.getElementById('modalSize').innerText = eventData.sizeOfPlantation || 'N/A';
            document.getElementById('modalDescription').innerText = eventData.description || 'N/A';
            document.getElementById('modalAvatar').src = eventData.avatar || 'default-avatar.png';
            document.getElementById('contactText').innerText = eventData.contact || 'N/A';
            document.getElementById('modalContact').href = `tel:${eventData.contact}`;
            document.getElementById('modalFacebook').href = eventData.facebook || '#';
            document.getElementById('modalUsername').innerText = eventData.username || 'N/A';

            const viewMoreDetails = document.getElementById('viewMoreDetails');
            viewMoreDetails.href = `./?page=map/crop-preview&id=${info.event.id}`;

            const modalPictures = document.getElementById('modalPictures');
            modalPictures.innerHTML = '';
            (eventData.pictures || []).forEach((picture) => {
                if (picture) {
                    const img = document.createElement('img');
                    img.src = picture;
                    img.alt = 'Crop Picture';
                    img.style.width = '60px';
                    img.style.height = '60px';
                    img.style.objectFit = 'cover';
                    modalPictures.appendChild(img);
                }
            });

            document.getElementById('cropModal').style.display = 'flex';
        },
    });

    calendar.render();

    // Close modal functionality
    window.addEventListener('click', function (event) {
        if (event.target === document.getElementById('cropModal')) {
            document.getElementById('cropModal').style.display = 'none';
        }
    });
});


    </script>
