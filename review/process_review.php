<!-- Add Review Modal -->
<div class="modal fade" id="addReviewModal" tabindex="-1" role="dialog" aria-labelledby="addReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addReviewModalLabel">Add Review</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Add review form -->
                <form id="addReviewForm">
                    <div class="form-group">
                        <label for="rating">Rating:</label>
                        <input type="number" class="form-control" id="rating" name="rating" min="1" max="5" required>
                    </div>
                    <div class="form-group">
                        <label for="comment">Comment:</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                    </div>
                    <input type="hidden" id="orderId" name="orderId" value="">
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to handle displaying the add review modal
    function displayAddReviewModal(orderId) {
        $('#orderId').val(orderId); // Set the orderId in the hidden input field
        $('#addReviewModal').modal('show'); // Show the modal
    }

    // jQuery code to handle form submission
    $(document).ready(function() {
        $('#addReviewForm').submit(function(e) {
            e.preventDefault(); // Prevent form submission
            // You can use AJAX here to submit the review data
            // Example AJAX code:
            /*
            $.ajax({
                url: 'add_review.php', // Specify the URL to submit the review
                method: 'POST',
                data: $(this).serialize(), // Serialize form data
                success: function(response) {
                    // Handle success response
                    console.log(response);
                    // You can close the modal or show a success message here
                    $('#addReviewModal').modal('hide');
                },
                error: function(err) {
                    // Handle error response
                    console.error(err);
                    // You can show an error message here
                }
            });
            */
            // For now, let's just close the modal (remove this line when using AJAX)
            $('#addReviewModal').modal('hide');
        });
    });
</script>
