<div class="container-fluid">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0">Inactive Users</h4>
    <div>
      <button id="refreshInactive" class="btn btn-outline-secondary btn-sm">Refresh</button>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <form id="inactiveFilters" class="row g-2">
        <div class="col-sm-4 col-md-3">
          <input type="text" class="form-control" name="search" placeholder="Search name or email" />
        </div>
        <div class="col-sm-3 col-md-2">
          <input type="number" class="form-control" name="purok" placeholder="Purok" min="1" />
        </div>
        <div class="col-sm-3 col-md-2">
          <input type="date" class="form-control" name="from" />
        </div>
        <div class="col-sm-3 col-md-2">
          <input type="date" class="form-control" name="to" />
        </div>
        <div class="col-sm-12 col-md-3">
          <button class="btn btn-primary" id="applyInactiveFilters" type="submit">Apply</button>
          <button class="btn btn-outline-secondary" id="resetInactiveFilters" type="button">Reset</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover mb-0" id="inactiveUsersTable">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Purok</th>
            <th>Inactivated</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

<!-- Archived Bills Modal -->
<div class="modal fade" id="archivedBillsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Archived Bills</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-sm table-striped" id="archivedBillsTable">
            <thead>
              <tr>
                <th>Bill #</th>
                <th>Billing Month</th>
                <th>Due Date</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Paid Date</th>
                <th>Archived At</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Reactivate Modal -->
<div class="modal fade" id="reactivateModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Reactivate User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to reactivate this user?</p>
        <input type="hidden" id="reactivateUserId" />
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" id="confirmReactivate">Yes, Reactivate</button>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const INACTIVE_API = "<?= site_url('admin/getInactiveUsers') ?>";
  const ARCHIVED_API_BASE = "<?= site_url('admin/archivedBills') ?>";
  const REACTIVATE_API_BASE = "<?= site_url('admin/reactivateUser') ?>";
  const $tableBody = $('#inactiveUsersTable tbody');

  function buildQuery() {
    const params = new URLSearchParams(new FormData(document.getElementById('inactiveFilters')));
    return params.toString();
  }

  function loadInactive() {
    const qs = buildQuery();
    const url = `${INACTIVE_API}${qs ? '?' + qs : ''}`;
    $.getJSON(url).done(function(rows){
      $tableBody.empty();
      if(!rows || rows.length === 0){
        $tableBody.append('<tr><td colspan="7" class="text-center text-muted py-4">No inactive users found</td></tr>');
        return;
      }
      rows.forEach((r, idx) => {
        const name = `${r.first_name || ''} ${r.last_name || ''}`.trim();
        const tr = $(
          `<tr>
            <td>${idx+1}</td>
            <td>${name}</td>
            <td>${r.email || ''}</td>
            <td>${r.phone || ''}</td>
            <td>${r.purok || ''}</td>
            <td>${r.inactivated_at || ''}</td>
            <td>
              <button class="btn btn-sm btn-outline-info me-1 view-archived" data-id="${r.inactive_id}">Archived Bills</button>
              <button class="btn btn-sm btn-success reactivate" data-user-id="${r.user_id}">Reactivate</button>
            </td>
          </tr>`);
        $tableBody.append(tr);
      });
    });
  }

  $(document).on('submit', '#inactiveFilters', function(e){ e.preventDefault(); loadInactive(); });
  $(document).on('click', '#resetInactiveFilters', function(){
    $('#inactiveFilters')[0].reset();
    loadInactive();
  });
  $(document).on('click', '#refreshInactive', function(){ loadInactive(); });

  // Archived bills modal
  $(document).on('click', '.view-archived', function(){
    const id = $(this).data('id');
    const url = `${ARCHIVED_API_BASE}/${id}`;
    $.getJSON(url).done(function(bills){
      const $tb = $('#archivedBillsTable tbody');
      $tb.empty();
      if(!bills || bills.length === 0){
        $tb.append('<tr><td colspan="7" class="text-center text-muted">No archived bills</td></tr>');
      } else {
        bills.forEach(b => {
          $tb.append(`
            <tr>
              <td>${b.bill_no || ''}</td>
              <td>${b.billing_month || ''}</td>
              <td>${b.due_date || ''}</td>
              <td>${(b.amount_due ?? 0).toFixed(2)}</td>
              <td>${b.status || ''}</td>
              <td>${b.paid_date || ''}</td>
              <td>${b.archived_at || ''}</td>
            </tr>`);
        });
      }
      const modal = new bootstrap.Modal(document.getElementById('archivedBillsModal'));
      modal.show();
    });
  });

  // Reactivate
  $(document).on('click', '.reactivate', function(){
    const userId = $(this).data('user-id');
    $('#reactivateUserId').val(userId);
    const modal = new bootstrap.Modal(document.getElementById('reactivateModal'));
    modal.show();
  });
  $(document).on('click', '#confirmReactivate', function(){
    const userId = $('#reactivateUserId').val();
    const url = `${REACTIVATE_API_BASE}/${userId}`;
    $.post(url, {}).done(function(res){
      if(res && res.success){
        loadInactive();
        bootstrap.Modal.getInstance(document.getElementById('reactivateModal')).hide();
      } else {
        alert(res && res.message ? res.message : 'Failed to reactivate');
      }
    }).fail(function(){ alert('Request failed'); });
  });

  // Initial load
  loadInactive();
})();
</script>
