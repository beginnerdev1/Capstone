<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

  <link href="<?= base_url('assets/Users/css/main.css?v=' . time()) ?>" rel="stylesheet">
</head>
<body>
  
<?= $this->include('Users/header') ?>
<br><br>

<section style="background-color: #eee;">
  <div class="container py-5">
    <div class="row">
      <!-- Left Column -->
      <div class="col-lg-4">
        <div class="card mb-4 bg-dark text-light">
          <div class="card-body text-center">
            <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3.webp"
              alt="avatar" class="rounded-circle img-fluid" style="width: 150px;">
            <h5 class="my-3 profile-name">Loading...</h5>
            <p class="text-muted mb-1">Member</p>
            <p class="text-muted mb-4 profile-address">Loading...</p>
            <div class="d-flex justify-content-center mb-2">
              <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editPersonalInfoModal">
                Edit Personal Info
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column -->
      <div class="col-lg-8">
          <div class="card mb-4 bg-dark text-light">
            <div class="card-body">
              <div class="row">
                <div class="col-sm-3"><p class="mb-0">Full Name</p></div>
                <div class="col-sm-9"><p class="text-white mb-0 profile-name"></p></div>
              </div>
              <hr>
              <div class="row">
                <div class="col-sm-3"><p class="mb-0">Email</p></div>
                <div class="col-sm-9"><p class="text-white mb-0 profile-email"></p></div>
              </div>
              <hr>
              <div class="row">
                <div class="col-sm-3"><p class="mb-0">Phone</p></div>
                <div class="col-sm-9"><p class="text-white mb-0 profile-phone"></p></div>
              </div>
              <hr>
              <div class="row">
                <div class="col-sm-3"><p class="mb-0">Street</p></div>
                <div class="col-sm-9"><p class="text-white mb-0 profile-street"></p></div>
              </div>
              <hr>
              <div class="row">
                <div class="col-sm-3"><p class="mb-0">Address</p></div>
                <div class="col-sm-9"><p class="text-white mb-0 profile-address"></p></div>
              </div>
            </div>
          </div>
        </div>

    </div>
  </div>
</section>


<!-- Edit Personal Info Modal -->
<div class="modal fade" id="editPersonalInfoModal" tabindex="-1" aria-labelledby="editPersonalInfoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      
      <!-- Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="editPersonalInfoLabel">Edit Personal Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Form -->
      <form id="editPersonalInfoForm" method="post" action="<?= site_url('users/updateProfile') ?>">
        <div class="modal-body">
          <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control" name="phone" id="phone">
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="email">
          </div>
          <div class="mb-3">
            <label for="street" class="form-label">Street</label>
            <input type="text" class="form-control" name="street" id="street">
          </div>
          <div class="mb-3">
            <label for="address" class="form-label">Full Address</label>
            <textarea class="form-control" name="address" id="address" rows="2"></textarea>
          </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
 $(document).ready(function () {
  function loadProfile() {
    $.ajax({
      url: "<?= site_url('users/getProfileInfo') ?>",
      type: "GET",
      dataType: "json",
      success: function (data) {
        if (data) {
          $(".profile-name").text(data.username);
          $(".profile-email").text(data.email);
          $(".profile-phone").text(data.phone);
          $(".profile-street").text(data.street);
          $(".profile-address").text(data.address);

          $("#phone").val(data.phone);
          $("#email").val(data.email);
          $("#street").val(data.street);
          $("#address").val(data.address);
        }
      }
    });
  }

  loadProfile();

  $("#editPersonalInfoForm").on("submit", function (e) {
    e.preventDefault();

    $.ajax({
      url: $(this).attr("action"),
      type: "POST",
      data: $(this).serialize(),
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          loadProfile();
          $("#editPersonalInfoModal").modal("hide");
        }
      }
    });
  });
});

</script>


</body>
</html>
