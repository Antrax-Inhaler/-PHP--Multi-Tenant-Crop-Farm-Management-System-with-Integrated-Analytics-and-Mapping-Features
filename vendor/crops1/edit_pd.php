<?php
require_once('./../../config.php');

// Check if ID is provided in the URL
if(isset($_GET['id']) && $_GET['id'] > 0) {
    $pestID = $_GET['id'];
    
    // Fetch pest/disease data from database based on ID
    $qry = $conn->query("SELECT * FROM `croppestdisease` WHERE Id = '$pestID'");
    
    if($qry->num_rows > 0) {
        $pest = $qry->fetch_assoc();
        $Name = $pest['Name'];
        $SizeOfAreaAffected = $pest['SizeOfAreaAffected'];
        $Status = $pest['Status'];
        // You can fetch other fields as needed
        
    } else {
        // If no pest/disease found with that ID, show error message and close modal
        ?>
        <div class="container-fluid">
            <center>Unknown Pest or Disease</center>
            <style>
                #uni_modal .modal-footer {
                    display:none
                }
            </style>
            <div class="text-right">
                <button class="btn btn-default bg-gradient-dark btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
        <?php
        exit;
    }
} else {
    // If no ID provided in URL, show error message and close modal
    ?>
    <div class="container-fluid">
        <center>No ID specified for editing</center>
        <style>
            #uni_modal .modal-footer {
                display:none
            }
        </style>
        <div class="text-right">
            <button class="btn btn-default bg-gradient-dark btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
        </div>
    </div>
    <?php
    exit;
}
$pest_disease_archive = $conn->query("SELECT pda.id, pda.name, pda.management, pda.symptoms, pda.preventive_measures, pda.curative_measures, pdi.image_path 
                                      FROM pest_disease_archive pda
                                      LEFT JOIN pest_disease_images pdi ON pda.id = pdi.pest_disease_id");
// Assuming you have a list of pest/diseases for reference
$pest_diseases = [];
while ($row = $pest_disease_archive->fetch_assoc()) {
    $pest_diseases[] = $row;
}
?>

<!-- Modal Body -->
<div class="container-fluid">
    <form action="" id="pest-disease-form" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="Id" value="<?php echo $pestID; ?>">
        <!-- Other hidden fields and form inputs as needed -->
        
        <div class="form-group">
            <label for="Name" class="control-label">Name of the Pest/Disease</label>
            <input name="Name" id="Name" type="text" class="form-control form-control-sm form-control-border" value="<?php echo htmlspecialchars($Name); ?>" required>
        </div>

        <div class="form-group">
            <label for="SizeOfAreaAffected" class="control-label">Size of Area Affected</label>
            <input type="number" name="SizeOfAreaAffected" id="SizeOfAreaAffected" step="any" class="form-control form-control-sm form-control-border" value="<?php echo htmlspecialchars($SizeOfAreaAffected); ?>" required>
        </div>

        <div class="form-group">
            <label for="Status" class="control-label">Status</label>
            <select name="Status" id="Status" class="custom-select" required>
                <option value="Existing" <?php if ($Status == 'Existing') echo 'selected'; ?>>Existing</option>
                <option value="Fixed" <?php if ($Status == 'Fixed') echo 'selected'; ?>>Fixed</option>
                <option value="Worsened" <?php if ($Status == 'Worsened') echo 'selected'; ?>>Worsened</option>
            </select>
        </div>

    </form>
</div>
<script>

        $(document).ready(function(){
    $('#pest-disease-form').submit(function(e){
        e.preventDefault();
        var _this = $(this);
        $('.err-msg').remove();
        if(_this[0].checkValidity() == false){
            _this[0].reportValidity();
            return false;
        }
        var el = $('<div>');
        el.addClass("alert err-msg alert-danger");
        el.hide();
        start_loader();
        $.ajax({
            url: _base_url_+"classes/Master.php?f=save_pd",
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
                if(typeof resp == 'object' && resp.status == 'success'){
                    var cropPestDiseaseId = resp.cropPestDiseaseId;
                    savePestDiseaseImages(cropPestDiseaseId);
                } else if(resp.status == 'failed' && !!resp.msg){
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
