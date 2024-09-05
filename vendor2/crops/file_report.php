<?php
require_once('./../../config.php');

$pest_id = $_GET['id'] ?? null;
if (!$pest_id) {
    die("Pest/Disease ID is required.");
}

// Fetch pest and disease details
$qry = $conn->query("SELECT * FROM `croppestdisease` WHERE Id = '{$pest_id}'");
if ($qry->num_rows > 0) {
    $pest_disease = $qry->fetch_assoc();
} else {
    die("Unknown Pest/Disease");
}

// Function to fetch croppestordisease name
function getCropPestOrDiseaseName($pestOrDiseaseId, $conn) {
    $qry = $conn->query("SELECT Name FROM `croppestdisease` WHERE Id = '{$pestOrDiseaseId}'");
    if ($qry->num_rows > 0) {
        $row = $qry->fetch_assoc();
        return $row['Name'];
    }
    return "Unknown Pest/Disease";
}

?>

<div class="container-fluid">
    <form action="" id="pest-disease-report-form" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="pestordisease_id" value="<?= $pest_id ?>">
        
        <!-- Visible Description -->
        <div class="form-group">
            <label for="Description" class="control-label">Description</label>
            <textarea name="description" id="Description" rows="4" class="form-control form-control-sm form-control-border" required></textarea>
        </div>

        <!-- Status -->
        <div class="form-group">
            <label for="Status" class="control-label">Status</label>
            <select name="status" id="Status" class="custom-select" required>
                <option value="0">Pending</option>
                <option value="1">Resolved</option>
            </select>
        </div>
    </form>
</div>

<script>
     // Form submission
     $('#pest-disease-report-form').submit(function(e){
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
            url: _base_url_ + "classes/Master.php?f=save_pest_and_disease_report",
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
</script>
