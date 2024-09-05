<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">Write Review</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="review-form">
                    <input type="hidden" id="product_id" name="product_id">
                    <input type="hidden" id="order_id" name="order_id">
                    <input type="hidden" id="client_id" name="client_id" value="<?php echo $_settings->userdata('id'); ?>">
                    <div class="form-group">
                        <label for="rating">Rating:</label>
                        <select class="form-control" id="rating" name="rating" required>
                            <option value="5">5 Stars - Excellent</option>
                            <option value="4">4 Stars - Very Good</option>
                            <option value="3">3 Stars - Good</option>
                            <option value="2">2 Stars - Fair</option>
                            <option value="1">1 Star - Poor</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="comment">Comment:</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="response-message"></div>

<script>
 $(document).ready(function(){
    $('#reviewModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var productId = button.data('product-id');
        var orderId = button.data('order-id');
        var clientId = button.data('client-id');
        $('#product_id').val(productId);
        $('#order_id').val(orderId);
        $('#client_id').val(clientId);
    });

    $('#review-form').submit(function(e){
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: 'classes/Master.php?f=save_review',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response){
                $('#response-message').html('<div class="alert alert-success" role="alert">' + response.msg + '</div>');
            },
            error: function(xhr, status, error){
                console.error('Error saving review:', xhr.responseText);
                $('#response-message').html('<div class="alert alert-danger" role="alert">An error occurred while saving the review.</div>');
            },
            complete: function(){
                $('#reviewModal').modal('hide'); // Close modal regardless of AJAX result
                $('#review-form')[0].reset(); // Clear form fields
            }
        });
    });
});

</script>
