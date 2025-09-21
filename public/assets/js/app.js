
// Handle Payment Modal
$(document).ready(function () {
  // Open Payment Modal
  $("#openPaymentBtn").on("click", function () {
    let invoiceId = $(this).data("id");

    // Show modal
    $("#paymentModal").modal("show");

    // Load content via AJAX
    $.ajax({
      url: base_url + "users/payments/" + invoiceId, // your controller route
      type: "GET",
      success: function (response) {
        $("#paymentModalBody").html(response);
      },
      error: function () {
        $("#paymentModalBody").html("<p class='text-danger'>Failed to load payment form.</p>");
      },
    });
  });
});


// Handle Edit Personal Info Form Submission
$(document).ready(function() {
    $('#editPersonalInfoForm').on('submit', function(e) {
        e.preventDefault();

        let form = $(this);
        let url = form.attr('action');
        let formData = form.serialize();

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#editPersonalInfoModal').modal('hide');
                    alert(response.message || 'Profile updated successfully');

                    // Update values on profile card
                    $('#phone').text($('input[name="phone"]').val());
                    $('#email').text($('input[name="email"]').val());
                    $('#street').text($('input[name="street"]').val());
                    $('#address').text($('textarea[name="address"]').val());
                } else {
                    alert(response.message || 'Something went wrong');
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('Error: Could not update profile.');
            }
        });
    });
});
