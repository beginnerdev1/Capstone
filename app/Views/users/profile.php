<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile</title>

  <!-- Google Fonts: Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

  <link href="<?= base_url('assets/Users/css/main.css?v=' . time()) ?>" rel="stylesheet">
  <style>
    html, body { height: 100%; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; overflow-y: hidden; }
    .profile-pic { max-width: 150px; border-radius: 50%; }
    .error { color: red; font-size: 0.9em; }
    section { min-height: calc(100vh - 80px); display: flex; align-items: flex-start; justify-content: center; background-color: #eee; padding: 80px 0 20px 0; }
    .container { max-width: 1200px; }
    @media (max-width: 991px) { body { overflow-y: auto; } section { min-height: auto; display: block; padding: 40px 0; } }
  </style>
</head>
<body>
  
<?= $this->include('Users/header') ?>

<section>
  <div class="container py-3">
    <div class="row">
      <!-- Left Column -->
      <div class="col-lg-4">
        <div class="card mb-4 bg-dark text-light">
          <div class="card-body text-center">
            <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3.webp" alt="avatar" class="rounded-circle img-fluid profile-pic" id="profilePic">
            <h5 class="my-3 profile-name">Loading...</h5>
            <p class="text-muted mb-1">Member</p>
            <div class="d-flex justify-content-center mb-2">
              <button class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#editPersonalInfoModal">Edit Personal Info</button>
              <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change Password</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column -->
      <div class="col-lg-8">
        <div class="card mb-4 bg-dark text-light">
          <div class="card-body">
            <div class="row"><div class="col-sm-3"><p class="mb-0">First Name</p></div><div class="col-sm-9"><p class="text-white mb-0 profile-first-name"></p></div></div><hr>
            <div class="row"><div class="col-sm-3"><p class="mb-0">Last Name</p></div><div class="col-sm-9"><p class="text-white mb-0 profile-last-name"></p></div></div><hr>
            <div class="row"><div class="col-sm-3"><p class="mb-0">Gender</p></div><div class="col-sm-9"><p class="text-white mb-0 profile-gender"></p></div></div><hr>
            <div class="row"><div class="col-sm-3"><p class="mb-0">Email</p></div><div class="col-sm-9"><p class="text-white mb-0 profile-email"></p></div></div><hr>
            <div class="row"><div class="col-sm-3"><p class="mb-0">Contact Number</p></div><div class="col-sm-9"><p class="text-white mb-0 profile-phone"></p></div></div><hr>
            <div class="row"><div class="col-sm-3"><p class="mb-0">Purok</p></div><div class="col-sm-9"><p class="text-white mb-0 profile-purok"></p></div></div><hr>
            <div class="row"><div class="col-sm-3"><p class="mb-0">Barangay</p></div><div class="col-sm-9"><p class="text-white mb-0 profile-barangay"></p></div></div><hr>
            <div class="row"><div class="col-sm-3"><p class="mb-0">Municipality</p></div><div class="col-sm-9"><p class="text-white mb-0 profile-municipality"></p></div></div><hr>
            <div class="row"><div class="col-sm-3"><p class="mb-0">Province</p></div><div class="col-sm-9"><p class="text-white mb-0 profile-province"></p></div></div>
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
      <div class="modal-header">
        <h5 class="modal-title" id="editPersonalInfoLabel">Edit Personal Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editPersonalInfoForm" method="post" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">First Name</label><input type="text" class="form-control" name="first_name" id="firstName" required></div>
          <div class="mb-3"><label class="form-label">Last Name</label><input type="text" class="form-control" name="last_name" id="lastName" required></div>
          <div class="mb-3"><label class="form-label">Gender</label>
            <select class="form-control" name="gender" id="gender" required>
              <option value="">Select Gender</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" id="email" required></div>
          <div class="mb-3"><label class="form-label">Contact Number</label><input type="text" class="form-control" name="phone" id="phone" required></div>
          <div class="mb-3"><label class="form-label">Purok</label><input type="text" class="form-control" name="purok" id="purok"></div>
          <div class="mb-3"><label class="form-label">Barangay</label><input type="text" class="form-control" name="barangay" id="barangay"></div>
          <div class="mb-3"><label class="form-label">Municipality</label><input type="text" class="form-control" name="municipality" id="municipality"></div>
          <div class="mb-3"><label class="form-label">Province</label><input type="text" class="form-control" name="province" id="province"></div>
          <div class="mb-3"><label class="form-label">Profile Picture</label><input type="file" class="form-control" name="profile_picture" id="profilePicture" accept="image/*"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="changePasswordLabel">Change Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="changePasswordForm" method="post">
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Current Password</label><input type="password" class="form-control" name="current_password" id="currentPassword" required></div>
          <div class="mb-3"><label class="form-label">New Password</label><input type="password" class="form-control" name="new_password" id="newPassword" required></div>
          <div class="mb-3"><label class="form-label">Confirm New Password</label><input type="password" class="form-control" name="confirm_password" id="confirmPassword" required><div class="error" id="passwordError"></div></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Change Password</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {
    function loadProfile() {
        $.ajax({
            url: "<?= site_url('users/getProfileInfo') ?>",
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response.status === "success" && response.data) {
                    const data = response.data;

                    $(".profile-name").text(data.first_name + ' ' + data.last_name);
                    $(".profile-first-name").text(data.first_name);
                    $(".profile-last-name").text(data.last_name);
                    $(".profile-gender").text(data.gender || "-");
                    $(".profile-email").text(data.email || "-");
                    $(".profile-phone").text(data.phone || "-");
                    $(".profile-purok").text(data.purok || "-");
                    $(".profile-barangay").text(data.barangay || "-");
                    $(".profile-municipality").text(data.municipality || "-");
                    $(".profile-province").text(data.province || "-");

                    $("#profilePic").attr("src", data.profile_picture);

                    $("#firstName").val(data.first_name);
                    $("#lastName").val(data.last_name);
                    $("#gender").val(data.gender);
                    $("#email").val(data.email);
                    $("#phone").val(data.phone);
                    $("#purok").val(data.purok);
                    $("#barangay").val(data.barangay);
                    $("#municipality").val(data.municipality);
                    $("#province").val(data.province);
                } else {
                    console.error("Failed to load profile info:", response.message || "No data");
                }
            },
            error: function(xhr, status, error) { console.error("AJAX error:", status, error); }
        });
    }

    loadProfile();

    $("#editPersonalInfoForm").on("submit", function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: "<?= site_url('users/updateProfile') ?>",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    loadProfile();
                    $("#editPersonalInfoModal").modal("hide");
                } else {
                    alert(response.message || "Failed to update profile");
                }
            }
        });
    });

    $("#changePasswordForm").on("submit", function (e) {
        e.preventDefault();
        var newPass = $("#newPassword").val();
        var confirmPass = $("#confirmPassword").val();
        if (newPass !== confirmPass) { $("#passwordError").text("Passwords do not match."); return; }
        $("#passwordError").text("");
        $.ajax({
            url: "<?= site_url('users/changePassword') ?>",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    $("#changePasswordModal").modal("hide");
                    alert("Password changed successfully!");
                } else {
                    $("#passwordError").text(response.message || "Failed to change password");
                }
            }
        });
    });
});
</script>
</body>
</html>
