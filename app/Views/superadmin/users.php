<br>
<br>
<div class="container-fluid px-4">
  <div class="row">
    <div class="col">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
          <h3 class="mb-0">Create User Account</h3>
        </div>

        <!-- ✅ User Input Form -->
        <div class="card-body">
          <form id="createUserForm" method="post" action="<?= base_url('superadmin/createUser') ?>">
            <div class="row g-3">
              <div class="col-md-3">
                <input type="text" name="name" class="form-control" placeholder="Full Name" required>
              </div>
              <div class="col-md-3">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
              </div>
              <div class="col-md-3">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
              </div>
              <div class="col-md-3">
                <select name="position" class="form-select" required>
                  <option value="">President</option>
                  <option value="Active">Vice President</option>
                  <option value="Pending">Secretaty</option>
                  <option value="Inactive">Treasurer</option>
                </select>
              </div>
            </div>
            <div class="mt-3 text-end">
              <button type="submit" class="btn btn-success">Create Account</button>
            </div>
          </form>
        </div>

        <!-- User Table -->
        <div class="table-responsive px-3">
        <table class="table table-striped table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="user-table-body">
                <!-- Users will be injected here -->
            </tbody>
        </table>
        </div>

      
        <!-- ✅ Pagination
        <div class="card-footer py-3">
          <nav aria-label="Table pagination">
            <ul class="pagination justify-content-end mb-0" id="pagination-links">
            </ul>
          </nav>
        </div>  -->
      </div>
    </div>
  </div>
</div>

<script>

  // Handle form submission via AJAX
$(document).ready(function () {
    $("#createUserForm").on("submit", function (e) {
        e.preventDefault(); // stop default form submit

        $.ajax({
            url: $(this).attr("action"),
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    alert(response.message);
                    $("#createUserForm")[0].reset(); // clear the form
                } else {
                    alert(response.message);
                }
            },
            error: function () {
                alert("Something went wrong. Please try again.");
            }
        });
    });
});


// Load users via AJAX
$(document).ready(function () {
    function loadUsers() {
        $.ajax({
            url: "<?= base_url('superadmin/getUsers') ?>",
            type: 'GET',
            dataType: 'json',
            success: function(users) {
                const tbody = $('#user-table-body');
                tbody.empty();

                users.forEach((user, index) => {
                    const statusBadge = user.is_verified == 1
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-secondary">Inactive</span>';

                    tbody.append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td>${user.username}</td>
                            <td>${statusBadge}</td>
                        </tr>
                    `);
                });
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                alert("Failed to load users.");
            }
        });
    }

    // Initial load
    loadUsers();
});

</script>


