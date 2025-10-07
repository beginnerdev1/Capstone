<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Billings - SB Admin</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
     <link href="<?php echo base_url('assets/admin/css/styles.css') ?>" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <?php require_once(APPPATH . 'Views/admin/navbar.php'); ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Billings</h1>
                <form id="createBillingForm">
                <div class="mb-3">
                    <label>User ID</label>
                    <input type="number" name="user_id" id="user_id" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Name</label>
                    <input type="text" id="username" class="form-control" placeholder="Enter username (optional)">
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" id="email" class="form-control" readonly>
                </div>

                <div class="mb-3">
                    <label>Address</label>
                    <input type="text" id="address" class="form-control" readonly>
                </div>

                <div class="mb-3">
                    <label>Billing Amount (₱)</label>
                    <input type="number" name="amount" class="form-control" required step="0.01">
                </div>

                <div class="mb-3"> <label>Description</label>
                    <input type="text" name="description" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary">Create Bill</button>
                </form>

                <div id="responseMsg" class="mt-3"></div>
                
                <div class="card mb-4 mt-4">
                    <div class="card-header">
                        <i class="fas fa-file-invoice-dollar me-1"></i>
                        Billings Table
                    </div>
                    <div class="card-body">
                        <table id="billingsTable" class="table table-hover table-striped align-middle shadow-sm">
                            <thead class="table-primary">
                                <tr class="table-primary">
                                    <th>Bill ID</th>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                </tbody>
                        </table>
                    </div>
                </div>
                </div>
        </main>
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; Your Website 2023</div>
                    <div>
                        <a href="#">Privacy Policy</a>
                        &middot;
                        <a href="#">Terms &amp; Conditions</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="<?= base_url('assets/admin/js/scripts.js') ?>"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    
    <script>
        // Global variable to hold the DataTable instance
        let billingsDataTable;

        /**
         * Fetches billing data and initializes/re-initializes the DataTable.
         * Assumes the CodeIgniter endpoint 'admin/getBillings' returns { data: [...] }
         */
        function fetchAndRenderBillings() {
            fetch("<?= base_url('admin/getBillings') ?>", {
                credentials: 'include' 
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                const billData = data.data || [];
                const $tbody = $("#billingsTable tbody");
                
                // 1. Destroy old DataTable instance if it exists before manipulating the DOM
                if ($.fn.DataTable.isDataTable('#billingsTable')) {
                    billingsDataTable.destroy();
                }
                
                // 2. Clear and populate the tbody with new data
                $tbody.empty();

                if (billData.length) {
                    billData.forEach(bill => {
                        $tbody.append(`
                            <tr>
                                <td>${bill.id}</td>
                                <td>${bill.username}</td>
                                <td><strong>₱${parseFloat(bill.amount).toFixed(2)}</strong></td>
                                <td>${bill.due_date}</td>
                            </tr>
                        `);
                    });
                }
                
                // 3. Re-initialize the DataTable
                billingsDataTable = $('#billingsTable').DataTable({
                    "order": [[0, "desc"]], // Sort by Bill ID descending
                    "paging": true,
                    "searching": true,
                    "info": true
                });

            })
            .catch(error => {
                console.error("Fetch Error:", error);
                $('#responseMsg').html('<div class="alert alert-danger">Error loading bills: ' + error.message + '</div>');
            });
        }

        document.addEventListener("DOMContentLoaded", function() {
            // Load the initial data and initialize the table on page load
            fetchAndRenderBillings();

            // --- User Info Auto-fill Logic (Enhanced) ---
            $('#user_id, #username').on('change keyup', function() {
                let userId = $('#user_id').val();
                let username = $('#username').val();
                
                // Clear fields if both inputs are empty
                if (!userId && !username) {
                    $('#email').val('');
                    $('#address').val('');
                    return;
                }

                $.ajax({
                    url: "<?= base_url('admin/getUserInfo') ?>",
                    method: "GET",
                    data: { user_id: userId, username: username },
                    dataType: "json",
                    success: function(response) {
                        if (response.status === 'success' && response.data) {
                            $('#email').val(response.data.email);
                            $('#address').val(response.data.address);
                            $('#username').val(response.data.username);
                            $('#user_id').val(response.data.id);
                        } else {
                            // Clear fields if user not found (Error handling enhanced)
                            $('#email').val('');
                            $('#address').val('');
                            $('#user_id').val(userId); // Keep the ID/Name the user typed
                            $('#username').val(username);
                        }
                    },
                    error: function() {
                        console.error("Error fetching user info");
                        $('#email').val('');
                        $('#address').val('');
                    }
                });
            });

            // --- Bill Creation Form Submission Logic (NEW) ---
            $('#createBillingForm').on('submit', function(e) {
                e.preventDefault(); // Stop default form submission

                const formData = $(this).serialize(); // Get form data
                const $responseMsg = $('#responseMsg');

                // Basic validation check
                if (!$('#user_id').val() || !$('input[name="amount"]').val()) {
                    $responseMsg.html('<div class="alert alert-warning">User ID and Billing Amount are required.</div>');
                    return;
                }

                // AJAX call to create the bill
                $.ajax({
                    url: "<?= base_url('admin/createBilling') ?>", // Assumed CodeIgniter endpoint
                    method: "POST",
                    data: formData,
                    dataType: "json",
                    beforeSend: function() {
                        $responseMsg.html('<div class="alert alert-info">Creating bill...</div>');
                        $('button[type="submit"]').prop('disabled', true); // Disable button
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            $responseMsg.html('<div class="alert alert-success">Bill created successfully! Bill ID: ' + response.bill_id + '</div>');
                            
                            // Reset and clear form fields
                            $('#createBillingForm')[0].reset(); 
                            $('#email').val(''); 
                            $('#address').val('');
                            
                            fetchAndRenderBillings(); // Refresh the table
                        } else {
                            $responseMsg.html('<div class="alert alert-danger">Error creating bill: ' + (response.message || 'Unknown error') + '</div>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", error);
                        $responseMsg.html('<div class="alert alert-danger">An error occurred during bill creation. Please check server logs.</div>');
                    },
                    complete: function() {
                        $('button[type="submit"]').prop('disabled', false); // Re-enable button
                    }
                });
            });

        });
    </script>
</body>
</html>



