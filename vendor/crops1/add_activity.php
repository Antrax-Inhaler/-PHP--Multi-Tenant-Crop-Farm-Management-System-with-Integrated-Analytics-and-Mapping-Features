<?php
require_once('./../../config.php');

$crop_id = $_GET['id'] ?? null;
if (!$crop_id) {
    die("Crop ID is required.");
}

// Fetch crop details
$qry = $conn->query("SELECT * FROM `crop` WHERE Id = '{$crop_id}'");
if ($qry->num_rows > 0) {
    $crop = $qry->fetch_assoc();
} else {
    die("Unknown Crop");
}

// Fetch crop activity suggestions
$suggestions = $conn->query("SELECT * FROM `crop_activity_suggestions` ORDER BY `date_created` DESC");
$activity_suggestions = [];
while ($row = $suggestions->fetch_assoc()) {
    $activity_suggestions[] = $row;
}
?>

<div class="container-fluid">
    <form id="add-activity-form">
        <input type="hidden" name="crop_id" value="<?= $crop_id ?>">

        <div class="form-group">
            <label for="activity_date" class="control-label">Activity Date</label>
            <input type="date" name="activity_date" id="activity_date" class="form-control form-control-sm form-control-border" required>
        </div>

        <div class="form-group">
    <label for="activity_type" class="control-label">Activity Type</label>
    <input type="text" name="activity_type" id="activity_type" class="form-control form-control-sm form-control-border" required>
    <div id="activity-suggestions" class="list-group" style="max-height: 200px; overflow-y: auto;"></div>
</div>


        <div class="form-group">
            <label for="description" class="control-label">Description</label>
            <textarea name="description" id="description" class="form-control form-control-sm form-control-border"></textarea>
        </div>

    </form>
</div>

<script>
$(document).ready(function(){
    const activitySuggestions = <?= json_encode($activity_suggestions) ?>;
    const suggestionsContainer = $('#activity-suggestions');
    const activityTypeInput = $('#activity_type');
    const descriptionInput = $('#description');

    // Function to populate activity suggestions
    function populateActivitySuggestions(data) {
        suggestionsContainer.empty();
        data.forEach(activity => {
            const button = $('<button>').text(activity.title).addClass('list-group-item list-group-item-action').attr('type', 'button');
            button.on('click', function() {
                activityTypeInput.val(activity.title);
                descriptionInput.val(activity.description);
                suggestionsContainer.empty(); // Clear suggestions after selection
            });
            suggestionsContainer.append(button);
        });
    }

    // Initial population of activity suggestions
    populateActivitySuggestions(activitySuggestions);

    // Input event handler for filtering suggestions
    activityTypeInput.on('input', function() {
        const query = $(this).val().toLowerCase();
        if (query.length === 0) {
            populateActivitySuggestions(activitySuggestions); // Show all suggestions if input is empty
            return;
        }
        const filtered = activitySuggestions.filter(activity => activity.title.toLowerCase().includes(query));
        populateActivitySuggestions(filtered);
    });

    // Form submission
    $('#add-activity-form').submit(function(e){
        e.preventDefault();
        var _this = $(this);
        $('.err-msg').remove();
        if (_this[0].checkValidity() === false) {
            _this[0].reportValidity();
            return false;
        }
        var el = $('<div>');
        el.addClass("alert err-msg alert-danger");
        el.hide();
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=save_activity",
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            dataType: 'json',
            error: function(xhr, status, error) {
                console.error(error);
                el.text("An error occurred");
                $('#uni_modal .modal-body').prepend(el);
                el.show();
                end_loader();
            },
            success: function(resp) {
                if (typeof resp === 'object' && resp.status === 'success') {
                    location.reload();
                } else if (resp.status === 'failed' && !!resp.msg) {
                    el.text(resp.msg);
                    $('#uni_modal .modal-body').prepend(el);
                    el.show();
                } else {
                    el.text("An error occurred");
                    console.error(resp);
                }
                $("html, body").scrollTop(0);
                end_loader();
            }
        });
    });
});
</script>
