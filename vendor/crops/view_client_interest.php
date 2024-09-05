<?php
require_once('./../../config.php');

// Fetch interested client ID from URL
$interested_client_id = $_GET['id'] ?? null;

// Initialize variables
$client_details = [];
$interest_details = null;
$client_id = null;
$crop_details = null; // Variable to store crop details

// Check if interested client ID is set and retrieve the client's details and interest status
if (isset($interested_client_id) && $interested_client_id > 0) {
    // Fetch interest details from interested_clients table
    $interest_qry = $conn->query("SELECT * FROM `interested_clients` WHERE id = '{$interested_client_id}'");
    if ($interest_qry->num_rows > 0) {
        $interest_details = $interest_qry->fetch_assoc();
        $client_id = $interest_details['client_id'];
        $crop_id = $interest_details['crop_id']; // Assuming there's a crop_id in interested_clients table

        // Fetch client details from client_list table
        $client_qry = $conn->query("SELECT * FROM `client_list` WHERE id = '{$client_id}' AND delete_flag = 0");
        if ($client_qry->num_rows > 0) {
            $client_details = $client_qry->fetch_assoc();
        } else {
            echo '<center>Unknown Client</center>';
            echo '<style>#uni_modal .modal-footer{display:none}</style>';
            echo '<div class="text-right"><button class="btn btn-default bg-gradient-dark btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button></div>';
            exit;
        }

        // Fetch crop details from crop table
        if (isset($crop_id)) {
            $crop_qry = $conn->query("SELECT * FROM `crop` WHERE id = '{$crop_id}'");
            if ($crop_qry->num_rows > 0) {
                $crop_details = $crop_qry->fetch_assoc();
            } else {
                echo '<center>Unknown Crop</center>';
                echo '<style>#uni_modal .modal-footer{display:none}</style>';
                echo '<div class="text-right"><button class="btn btn-default bg-gradient-dark btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button></div>';
                exit;
            }
        } else {
            echo '<center>Crop ID not found in interest details</center>';
            exit;
        }
    } else {
        echo '<center>Unknown Interested Client</center>';
        echo '<style>#uni_modal .modal-footer{display:none}</style>';
        echo '<div class="text-right"><button class="btn btn-default bg-gradient-dark btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button></div>';
        exit;
    }
} else {
    echo '<center>Interested Client ID not provided</center>';
    exit;
}
?>

<div class="container-fluid">
    <!-- Display client information -->
    <div class="form-group">
        <label for="client_name" class="control-label">Client Name:</label>
        <p id="client_name"><?php echo "{$client_details['firstname']} {$client_details['middlename']} {$client_details['lastname']}"; ?></p>
    </div>

    <div class="form-group">
        <label for="client_contact" class="control-label">Client Contact:</label>
        <p id="client_contact"><?php echo $client_details['contact']; ?></p>
    </div>

    <div class="form-group">
        <label for="client_email" class="control-label">Client Email:</label>
        <p id="client_email"><?php echo $client_details['email']; ?></p>
    </div>

    <!-- Interest details form -->
    <form action="" id="update-interest-form">
        <input type="hidden" name="id" value="<?php echo $interested_client_id; ?>">

        <div class="form-group">
            <label for="InterestStatus" class="control-label">Interest Status</label>
            <select name="status" id="InterestStatus" class="custom-select" required>
                <option value="pending" <?php echo isset($interest_details['status']) && $interest_details['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="approved" <?php echo isset($interest_details['status']) && $interest_details['status'] == 'approved' ? 'selected' : '' ?>>Approved</option>
                <option value="denied" <?php echo isset($interest_details['status']) && $interest_details['status'] == 'denied' ? 'selected' : '' ?>>Denied</option>
            </select>
        </div>

        <div class="form-group">
            <label for="message" class="control-label">Message</label>
            <textarea name="message" id="message" class="form-control" readonly><?php echo $interest_details['message'] ?? ''; ?></textarea>
        </div>

        <!-- Toggle switch for hiding the crop -->
        <div class="form-group">
            <label for="hide_crop" class="control-label">Hide Crop</label>
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="hide_crop" name="hide_crop" <?php echo $crop_details['hide'] ? 'checked' : ''; ?>>
                <label class="custom-control-label" for="hide_crop">Toggle visibility</label>
            </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function(){
        $('#update-interest-form').submit(function(e){
            e.preventDefault();
            var _this = $(this);
            $('.err-msg').remove();
            if (_this[0].checkValidity() == false) {
                _this[0].reportValidity();
                return false;
            }
            var el = $('<div>');
            el.addClass("alert err-msg alert-danger");
            el.hide();
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=update_client_interest_status",
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
                success: function(resp){
                    if (typeof resp == 'object' && resp.status == 'success') {
                        location.reload();
                    } else if (resp.status == 'failed' && !!resp.msg) {
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
