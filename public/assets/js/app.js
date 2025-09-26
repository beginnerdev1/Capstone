
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
