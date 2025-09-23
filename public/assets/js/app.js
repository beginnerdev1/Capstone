
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



// Handle Edit Personal Info Modal
$(document).ready(function(){

    // function to refresh profile info
    function fetchProfileInfo(){
        $.ajax({
            url: base_url + "users/getProfileInfo",
            type: "GET",
            dataType: "json",
            success: function(user){
                $(".profile-name").text(user.full_name ?? '');
                $(".profile-email").text(user.email ?? '');
                $(".profile-phone").text(user.phone ?? '');
                $(".profile-street").text(user.street ?? '');
                $(".profile-address").text(user.address ?? '');

                // preload values in modal form
                $("#phone").val(user.phone ?? '');
                $("#email").val(user.email ?? '');
                $("#street").val(user.street ?? '');
                $("#address").val(user.address ?? '');
            }
        });
    }

    // handle submit
    $("#editPersonalInfoForm").submit(function(e){
        e.preventDefault();

        $.ajax({
            url: $(this).attr("action"),
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function(res){
                if(res.status === "success"){
                    // ✅ Close modal
                    let modal = bootstrap.Modal.getInstance(document.getElementById("editPersonalInfoModal"));
                    modal.hide();

                    // ✅ Refresh profile info
                    fetchProfileInfo();
                } else {
                    alert("Failed to update profile.");
                }
            },
            error: function(){
                alert("Error updating profile.");
            }
        });
    });

    // load info on page ready
    fetchProfileInfo();
});
