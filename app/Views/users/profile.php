<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile</title>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

  <link href="<?= base_url('assets/Users/css/main.css?v=' . time()) ?>" rel="stylesheet">
  <link href="<?= base_url('assets/Users/css/navbar.css?v=' . time()) ?>" rel="stylesheet">

<style>
  html, body {
    height: 100%;
    margin: 0;
    padding: 0;
    font-family: 'Poppins', sans-serif;
    overflow-y: hidden;
    background-color: #eee;
  }
  section { min-height: calc(100vh - 80px); display: flex; align-items: flex-start; justify-content: center; padding: 80px 0 20px 0; }
  .container { max-width: 1200px; }
  .card.bg-dark { background-color: #1e1e1e !important; border-radius: 15px; }
  .profile-pic { width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 0 10px rgba(0,0,0,0.3); margin-bottom: 10px; }
  .profile-name { color: #fff !important; font-weight: 600; }
  .text-muted { color: #aaa !important; }
  .btn-warning { background-color: #ffc107; color: #000; font-weight: 600; border: none; }
  .btn-danger { background-color: #dc3545; font-weight: 600; border: none; }
  .btn:hover { opacity: 0.9; }
  .error { color: red; font-size: 0.9em; }
  .password-hint { font-size: 0.9em; color: #555; }
  /* Password validation colors */
  #passwordHint span.invalid { color: red; }
  #passwordHint span.valid { color: green; }

  @media (max-width: 991px) {
    body { overflow-y: auto; }
    section { min-height: auto; display: block; padding: 40px 0; }
  }
</style>



</head>

<body>
<?= $this->include('Users/header') ?>
<section>
  <div class="container py-5">
    <div class="row g-4 align-items-start">

      <!-- LEFT SIDE -->
      <div class="col-lg-4 d-flex flex-column gap-3">

        <!-- SMALLER PROFILE CARD -->
      <div class="card bg-dark text-light text-center p-3 shadow-sm rounded-4">
        <img id="profilePic"
            src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3.webp"
            alt="Avatar"
            class="mx-auto mb-2 rounded-circle border border-2 border-warning"
            style="width: 100px; height: 100px; object-fit: cover;">

        <h6 class="mb-1 fw-semibold profile-name">Loading...</h6>
        <p class="text-muted small mb-2">Member</p>

        <div class="d-flex justify-content-center flex-wrap gap-2">
          <button class="btn btn-warning btn-sm px-3" data-bs-toggle="modal" data-bs-target="#editPersonalInfoModal">
            <i class="bi bi-pencil-square me-1"></i>Profile
          </button>
          <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#changeEmailModal">
            <i class="bi bi-envelope me-1"></i>Email
          </button>
          <button class="btn btn-danger btn-sm px-3" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
            <i class="bi bi-lock me-1"></i>Password
          </button>
        </div>
      </div>

        <!-- ACCOUNT INFO CARD -->
        <div class="card bg-dark text-light p-3 shadow-sm rounded-4">
          <h6 class="border-bottom pb-2 mb-3 text-warning">
            <i class="bi bi-person-badge me-2"></i>Account Details
          </h6>
          <div class="row gy-2 small">
            <div class="col-12 d-flex align-items-center">
              <i class="bi bi-calendar3 text-warning me-2"></i>
              <span><strong>Created:</strong> <span class="account-created">—</span></span>
            </div>
            <div class="col-12 d-flex align-items-center">
              <i class="bi bi-shield-check text-warning me-2"></i>
              <span><strong>Status:</strong> <span class="account-status">Active</span></span>
            </div>
          </div>
        </div>


      </div>

      <!-- RIGHT SIDE -->
      <div class="col-lg-8">
        <div class="card bg-dark text-light p-4 shadow-sm rounded-4">
          <h5 class="border-bottom pb-2 mb-3 text-warning">
            <i class="bi bi-info-circle me-2"></i>Personal Information
          </h5>

          <div class="row gy-3">
            <div class="col-md-6"><p class="text-secondary mb-1 small fw-semibold">First Name</p><p class="text-white mb-0 profile-first-name">—</p></div>
            <div class="col-md-6"><p class="text-secondary mb-1 small fw-semibold">Last Name</p><p class="text-white mb-0 profile-last-name">—</p></div>
            <div class="col-md-6"><p class="text-secondary mb-1 small fw-semibold">Gender</p><p class="text-white mb-0 profile-gender">—</p></div>
            <div class="col-md-6"><p class="text-secondary mb-1 small fw-semibold">Age</p><p class="text-white mb-0 profile-age">—</p></div>
            <div class="col-md-6"><p class="text-secondary mb-1 small fw-semibold">Family Number</p><p class="text-white mb-0 profile-family-number">—</p></div>
            <div class="col-md-6"><p class="text-secondary mb-1 small fw-semibold">Contact Number</p><p class="text-white mb-0 profile-phone">—</p></div>
            <div class="col-md-6"><p class="text-secondary mb-1 small fw-semibold">Purok</p><p class="text-white mb-0 profile-purok">—</p></div>
            <div class="col-md-6"><p class="text-secondary mb-1 small fw-semibold">Barangay</p><p class="text-white mb-0 profile-barangay">—</p></div>
            <div class="col-md-6"><p class="text-secondary mb-1 small fw-semibold">Municipality</p><p class="text-white mb-0 profile-municipality">—</p></div>
            <div class="col-md-6"><p class="text-secondary mb-1 small fw-semibold">Province</p><p class="text-white mb-0 profile-province">—</p></div>
            <div class="col-md-6"><p class="text-secondary mb-1 small fw-semibold">Zip Code</p><p class="text-white mb-0 profile-zipcode">—</p></div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>


<!-- Edit Personal Info Modal -->
<!-- Redesigned Edit Personal Info Modal -->
<div class="modal fade" id="editPersonalInfoModal" tabindex="-1" aria-labelledby="editPersonalInfoLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header bg-dark text-light rounded-top-4">
        <h5 class="modal-title fw-semibold" id="editPersonalInfoLabel">
          <i class="bi bi-pencil-square me-2"></i>Edit Personal Information
        </h5>
       
      </div>

      <form id="editPersonalInfoForm" method="post" action="<?= base_url('users/updateProfile') ?>" enctype="multipart/form-data">
        <div class="modal-body bg-light">
          <div class="row g-3">

            <div class="col-md-6">
              <label class="form-label fw-semibold">First Name</label>
              <input type="text" class="form-control shadow-sm" name="first_name" id="firstName" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Last Name</label>
              <input type="text" class="form-control shadow-sm" name="last_name" id="lastName" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Gender</label>
              <select class="form-select shadow-sm" name="gender" id="gender" required>
                <option value="">Select Gender</option>
                <option>Male</option>
                <option>Female</option>
                <option>Other</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Age</label>
              <input type="number" class="form-control shadow-sm" name="age" id="age" min="1" max="120" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Family Number</label>
              <input type="number" class="form-control shadow-sm" name="family_number" id="familyNumber" min="1" max="20" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Contact Number</label>
              <input type="text" class="form-control shadow-sm" name="phone" id="phone" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Purok</label>
              <select class="form-select shadow-sm" name="purok" id="purok" required>
                <option value="">Select Purok</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Barangay</label>
              <input type="text" class="form-control shadow-sm" name="barangay" id="barangay" value="Borlongan" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Municipality</label>
              <input type="text" class="form-control shadow-sm" name="municipality" id="municipality" value="Dipaculao" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Province</label>
              <input type="text" class="form-control shadow-sm" name="province" id="province" value="Aurora" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Zip Code</label>
              <input type="text" class="form-control shadow-sm" name="zipcode" id="zipcode" value="3203" readonly>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">Profile Picture</label>
              <input type="file" class="form-control shadow-sm" name="profile_picture" id="profilePicture" accept="image/*">
            </div>

          </div>
        </div>

        <div class="modal-footer bg-light border-top-0">
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-dark px-4" id="saveProfileBtn">
              <i class="bi bi-save me-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!--Change Email Modal -->
<div class="modal fade" id="changeEmailModal" tabindex="-1" aria-labelledby="changeEmailLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header bg-dark text-light rounded-top-4">
        <h5 class="modal-title fw-semibold" id="changeEmailLabel">
          <i class="bi bi-envelope-at me-2"></i>Change Email
        </h5>
       
      </div>

      <form id="changeEmailForm" method="post" action="<?= base_url('users/changeEmail') ?>">
        <div class="modal-body bg-light">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Current Email</label>
              <input type="email" class="form-control shadow-sm" id="currentEmail" name="current_email" readonly>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">New Email</label>
              <input type="email" class="form-control shadow-sm" id="newEmail" name="new_email" required>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">Confirm New Email</label>
              <input type="email" class="form-control shadow-sm" id="confirmEmail" name="confirm_email" required>
            </div>

            <div class="col-12 position-relative">
              <label class="form-label fw-semibold">Password</label>
              <div class="input-group">
                <input type="password" class="form-control shadow-sm" id="emailPassword" name="password" required>
                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="emailPassword">
                  <i class="bi bi-eye-slash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer bg-light border-top-0">
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-dark px-4" id="saveEmailBtn">
            <i class="bi bi-save me-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header bg-dark text-light rounded-top-4">
        <h5 class="modal-title fw-semibold" id="changePasswordLabel">
          <i class="bi bi-shield-lock me-2"></i>Change Password
        </h5>
      </div>

      <form id="changePasswordForm" method="post" action="<?= base_url('users/changePassword') ?>">
        <div class="modal-body bg-light">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Current Password</label>
              <div class="input-group">
                <input type="password" class="form-control shadow-sm" name="current_password" id="currentPassword" required>
                <span class="input-group-text bg-transparent border-start-0 toggle-password" data-target="currentPassword">
                  <i class="bi bi-eye-slash"></i>
                </span>
              </div>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">New Password</label>
              <div class="input-group">
                <input type="password" class="form-control shadow-sm" name="new_password" id="newPassword" required>
                <span class="input-group-text bg-transparent border-start-0 toggle-password" data-target="newPassword">
                  <i class="bi bi-eye-slash"></i>
                </span>
              </div>

              <div id="passwordHint" class="mt-2 small">
                <strong>Password must include:</strong><br>
                <span id="length" class="invalid">• 8+ characters</span><br>
                <span id="upper" class="invalid">• 1 uppercase letter</span><br>
                <span id="lower" class="invalid">• 1 lowercase letter</span><br>
                <span id="number" class="invalid">• 1 number</span>
              </div>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">Confirm New Password</label>
              <div class="input-group">
                <input type="password" class="form-control shadow-sm" name="confirm_password" id="confirmPassword" required>
                <span class="input-group-text bg-transparent border-start-0 toggle-password" data-target="confirmPassword">
                  <i class="bi bi-eye-slash"></i>
                </span>
              </div>
              <div class="text-danger small mt-1" id="passwordError"></div>
            </div>
          </div>
        </div>

        <div class="modal-footer bg-light border-top-0">
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-dark px-4">
            <i class="bi bi-save me-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!--Nptif for suceess or fail -->
  <?php if (session()->getFlashdata('success')): ?>
  <script>
  Swal.fire({
    icon: 'success',
    title: 'Success',
    text: '<?= session()->getFlashdata('success') ?>',
    showConfirmButton: false,
    timer: 2000
  });
  </script>
  <?php endif; ?>

  <?php if (session()->getFlashdata('error')): ?>
  <script>
  Swal.fire({
    icon: 'error',
    title: 'Error',
    text: '<?= session()->getFlashdata('error') ?>',
    showConfirmButton: true
  });
  </script>
  <?php endif; ?>

  <?php if (session()->getFlashdata('profile_alert')): ?>
<script>
Swal.fire({
  icon: 'info',
  title: 'Profile Incomplete',
  text: 'Your account is still pending. Some features may be restricted.',
  confirmButtonText: 'Ok'
});
</script>
<?php endif; ?>

<script>
$(document).ready(function () {

    //Load Profile 
    function loadProfile() {
        $.ajax({
            url: "<?= site_url('users/getProfileInfo') ?>",
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response.status === "success" && response.data) {
                    const data = response.data;

                    // Profile display
                    $(".profile-name").text(data.first_name + ' ' + data.last_name);
                    $(".profile-first-name").text(data.first_name);
                    $(".profile-last-name").text(data.last_name);
                    $(".profile-gender").text(data.gender || "-");
                    $(".profile-age").text(data.age || "-");
                    $(".profile-family-number").text(data.family_number || "-");
                    $(".profile-email").text(data.email || "-");
                    $(".profile-phone").text(data.phone || "-");
                    $(".profile-purok").text(data.purok || "-");
                    $(".profile-barangay").text(data.barangay || "-");
                    $(".profile-municipality").text(data.municipality || "-");
                    $(".profile-province").text(data.province || "-");
                    $(".profile-zipcode").text(data.zipcode || "-");

                   
                    // Account info card
                    $(".account-created").text(data.created_at || "—");
                    $(".account-status").text(data.account_status || "Pending");


                    // Profile picture
                    if (data.profile_picture) {
                        $("#profilePic").attr("src", "<?= site_url('uploads/profile_pictures/') ?>" + data.profile_picture);
                    } else {
                        $("#profilePic").attr("src", "https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3.webp");
                    }

                    // Edit personal info form
                    $("#firstName").val(data.first_name || "");
                    $("#lastName").val(data.last_name || "");
                    $("#gender").val(data.gender || "");
                    $("#age").val(data.age || "");
                    $("#familyNumber").val(data.family_number || "");
                    $("#phone").val(data.phone || "");
                    $("#purok").val(data.purok || "");
                    $("#barangay").val(data.barangay || "");
                    $("#municipality").val(data.municipality || "");
                    $("#province").val(data.province || "");
                    $("#zipcode").val(data.zipcode || "");

                    // ✅ Update current email in Change Email modal
                    $("#currentEmail").val(data.email || "");
                }
            },
            error: function () {
                console.error("Failed to load profile data.");
            }
        });
    }

    loadProfile()

    //Update Profile
    $("#editPersonalInfoForm").on("submit", function (e) {
        e.preventDefault()
        var formData = new FormData(this)

        $.ajax({
            url: "<?= site_url('users/updateProfile') ?>",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            beforeSend: function () {
                $("#saveProfileBtn").prop("disabled", true).text("Saving...")
            },
            success: function (response) {
                if (response.status === "success") {
                    $("#editPersonalInfoModal").modal("hide")
                    Swal.fire({
                        icon: 'success',
                        title: 'Profile updated successfully!',
                        text: 'Your changes have been saved.',
                        showConfirmButton: false,
                        timer: 1200
                    })
                    setTimeout(loadProfile, 1300) // reload data automatically
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || "Failed to update profile"
                    })
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.'
                })
            },
            complete: function () {
                $("#saveProfileBtn").prop("disabled", false).text("Save Changes")
            }
        })
    })



    // Change Email
    $("#changeEmailForm").on("submit", function (e) {
        e.preventDefault();

        $.ajax({
            url: "<?= site_url('users/changeEmail') ?>",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            beforeSend: function () {
                $("#saveEmailBtn").prop("disabled", true).text("Saving...");
            },
            success: function (response) {
                if (response.status === "success") {
                    // Clear input fields
                    $("#newEmail").val("");
                    $("#confirmEmail").val("");
                    $("#emailPassword").val("");

                    // Close modal
                    $("#changeEmailModal").modal("hide");

                    // Show success message
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });

                    // Reload profile data to get updated email
                    loadProfile();
                } else {
                    // Show error without closing modal
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: response.message,
                        showConfirmButton: true
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "An unexpected error occurred. Please try again.",
                    showConfirmButton: true
                });
            },
            complete: function () {
                $("#saveEmailBtn").prop("disabled", false).text("Save Changes");
            }
        });
    });









    //Change Password
//Change Password
$("#changePasswordForm").on("submit", function (e) {
    e.preventDefault();
    var newPass = $("#newPassword").val();
    var confirmPass = $("#confirmPassword").val();

    if (newPass !== confirmPass) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "New password and confirm password do not match.",
            showConfirmButton: true
        });
        return;
    }

    $.ajax({
        url: "<?= site_url('users/changePassword') ?>",
        type: "POST",
        data: $(this).serialize(),
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                $("#changePasswordModal").modal("hide"); // close modal
                Swal.fire({                              // success popup
                    icon: 'success',
                    title: 'Password changed successfully!',
                    showConfirmButton: false,
                    timer: 1500
                });
                $("#currentPassword, #newPassword, #confirmPassword").val(""); // clear fields
            } else {
                // Show specific error from server
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: response.message || "Failed to change password",
                    showConfirmButton: true
                });
            }
        },
        error: function (jqXHR) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "An unexpected error occurred: " + jqXHR.responseText,
                showConfirmButton: true
            });
        }
    });
});



    //Password Show And indicator
    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', function () {
            const target = document.getElementById(this.dataset.target)
            const iconElement = this.querySelector('i')
            if (target.type === 'password') {
                target.type = 'text'
                iconElement.classList.replace('bi-eye-slash', 'bi-eye')
            } else {
                target.type = 'password'
                iconElement.classList.replace('bi-eye', 'bi-eye-slash')
            }
        })
    })

    const newPassword = document.getElementById("newPassword")
    const confirmPassword = document.getElementById("confirmPassword")
    const passwordError = document.getElementById("passwordError")
    const lengthReq = document.getElementById("length")
    const upperReq = document.getElementById("upper")
    const lowerReq = document.getElementById("lower")
    const numberReq = document.getElementById("number")

    newPassword.addEventListener("input", function () {
        const value = this.value
        lengthReq.classList.toggle("valid", value.length >= 8)
        upperReq.classList.toggle("valid", /[A-Z]/.test(value))
        lowerReq.classList.toggle("valid", /[a-z]/.test(value))
        numberReq.classList.toggle("valid", /\d/.test(value))
    })

    confirmPassword.addEventListener("input", function () {
        passwordError.textContent = confirmPassword.value !== newPassword.value ? "Passwords do not match." : ""
    })
})
</script>






</body>
</html>
