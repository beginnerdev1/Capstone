<?php
// Partial Billings view
// Controller may pass this data as either `$partialBills` or `$partialPayments`.
// Normalize to `$items` so the template renders regardless of the naming used by the controller.
$items = $partialBills ?? $partialPayments ?? [];
$filterStart = $filterStart ?? '';
$filterEnd = $filterEnd ?? '';
$search = $search ?? '';
?>

<style>
/* Lightweight admin styles scoped to this view */
.partial-wrapper { padding: 1.25rem; }
.page-header { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:1rem; }
.page-title { font-size:1.25rem; font-weight:700; }
.page-header .page-title { color: #0f1724; }
.page-header .small { color: #374151; font-weight:500; }
.filters { display:flex; gap:0.75rem; flex-wrap:wrap; margin-bottom:1rem; }
.form-control { padding:0.5rem 0.75rem; border:1px solid #e6e9ef; border-radius:6px; }
.btn { padding:0.5rem 0.75rem; border-radius:6px; border:1px solid transparent; background:#4e73df; color:#fff; cursor:pointer; }
.btn-ghost { background:transparent; color:#4e73df; border:1px solid #dbe3ff; }
.table { width:100%; border-collapse:collapse; background:#fff; border:1px solid #eef2ff; border-radius:6px; overflow:hidden; }
.table th, .table td { padding:0.75rem 0.85rem; border-bottom:1px solid #f3f6ff; text-align:left; font-size:0.95rem; }
.table thead th { background:#fbfcff; color:#253049; font-weight:700; }
.table tbody tr:hover { background:#fbfbff; }
.empty { padding:1.25rem; color:#6b7280; }
.actions { display:flex; gap:0.5rem; }
.modal { position:fixed; inset:0; display:none; align-items:center; justify-content:center; background: rgba(10, 12, 18, 0.45); z-index: 9999; }
.modal.open { display:flex; }
.modal-content { background:#fff; border-radius:8px; width:720px; max-width:96%; padding:1rem; box-shadow:0 8px 30px rgba(15,23,42,0.15); }
.modal-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem; }
.form-row { display:flex; gap:0.75rem; margin-bottom:0.5rem; }
.form-row .col { flex:1; }
.small { font-size:0.85rem; color:#6b7280; }
</style>

<div class="partial-wrapper">
  <div class="page-header">
    <div>
      <div class="page-title">Partial Billings</div>
      <div class="small">Manage households that have partial payments and record partial receipts.</div>
    </div>
    <div class="actions">
      <!-- Actions removed as requested -->
    </div>
  </div>

  <div class="filters">
    <input id="search" class="form-control" type="search" placeholder="Search by name, account or reference" value="<?= esc($search ?? '') ?>">
    <input id="startDate" class="form-control" type="date" value="<?= esc($filterStart ?? '') ?>">
    <input id="endDate" class="form-control" type="date" value="<?= esc($filterEnd ?? '') ?>">
    <button id="applyFilters" class="btn btn-ghost">Apply</button>
    <button id="resetFilters" class="btn btn-ghost">Reset</button>
  </div>

  <?php if (empty($items) || count($items) === 0): ?>
    <div class="card empty">No partial payments found for the selected criteria.</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table" id="partialTable">
        <thead>
            <tr>
              <th>Bill No</th>
              <th>User</th>
              <th>Balance</th>
              <th>Updated</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $p): ?>
              <tr>
                <td><?= esc($p['bill_no'] ?? $p['id']) ?></td>
                <td><?= esc($p['name'] ?? $p['household_name'] ?? 'Unknown') ?></td>
                <td>₱<?= number_format($p['balance'] ?? 0, 2) ?></td>
                <td><?= esc($p['updated_at'] ?? '') ?></td>
              </tr>
            <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<!-- Modal: Partial Payment Form -->
<div id="partialModal" class="modal" role="dialog" aria-modal="true" aria-hidden="true">
  <div class="modal-content" role="document">
    <div class="modal-header">
      <h3 id="modalTitle">New Partial Payment</h3>
      <button id="modalClose" class="btn btn-ghost">Close</button>
    </div>

    <form id="partialForm" method="post" action="<?= site_url('admin/savePartialPayment') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="id" id="formId" value="">

      <div class="form-row">
        <div class="col">
          <label class="small">Household / Name</label>
          <input class="form-control" type="text" name="household_name" id="formHousehold" required>
        </div>
        <div class="col">
          <label class="small">Account #</label>
          <input class="form-control" type="text" name="account_no" id="formAccount">
        </div>
      </div>

      <div class="form-row">
        <div class="col">
          <label class="small">Amount Due (₱)</label>
          <input class="form-control" type="number" step="0.01" name="amount_due" id="formAmountDue" required>
        </div>
        <div class="col">
          <label class="small">Amount Paid (₱)</label>
          <input class="form-control" type="number" step="0.01" name="amount_paid" id="formAmountPaid" required>
        </div>
      </div>

      <div class="form-row">
        <div class="col">
          <label class="small">Payment Date</label>
          <input class="form-control" type="date" name="paid_at" id="formPaidAt" value="<?= date('Y-m-d') ?>">
        </div>
        <div class="col">
          <label class="small">Reference / Notes</label>
          <input class="form-control" type="text" name="reference" id="formReference">
        </div>
      </div>

      <div style="display:flex; gap:.5rem; justify-content:flex-end; margin-top:0.75rem;">
        <button type="button" id="formCancel" class="btn btn-ghost">Cancel</button>
        <button type="submit" class="btn">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
(function(){
  const modal = document.getElementById('partialModal');
  const btnNew = document.getElementById('btnNewPartial');
  const modalClose = document.getElementById('modalClose');
  const formCancel = document.getElementById('formCancel');
  const form = document.getElementById('partialForm');

  function openModal() { modal.classList.add('open'); modal.setAttribute('aria-hidden','false'); }
  function closeModal() { modal.classList.remove('open'); modal.setAttribute('aria-hidden','true'); clearForm(); }

  function clearForm() {
    document.getElementById('formId').value = '';
    document.getElementById('formHousehold').value = '';
    document.getElementById('formAccount').value = '';
    document.getElementById('formAmountDue').value = '';
    document.getElementById('formAmountPaid').value = '';
    document.getElementById('formPaidAt').value = '<?= date('Y-m-d') ?>';
    document.getElementById('formReference').value = '';
    document.getElementById('modalTitle').textContent = 'New Partial Payment';
  }

  btnNew && btnNew.addEventListener('click', openModal);
  modalClose && modalClose.addEventListener('click', closeModal);
  formCancel && formCancel.addEventListener('click', closeModal);

  // Edit / Add payment buttons (delegation)
  document.addEventListener('click', function(e){
    if (e.target.closest('.btn-edit')) {
      const id = e.target.closest('tr').dataset.id;
      const tr = document.querySelector(`tr[data-id="${id}"]`);
      if (tr) {
        const ds = tr.dataset;
        document.getElementById('formId').value = id;
        document.getElementById('formHousehold').value = ds.household || '';
        document.getElementById('formAccount').value = ds.account || '';
        document.getElementById('formAmountDue').value = (parseFloat(ds.amountDue || '0') || 0).toFixed(2);
        document.getElementById('formAmountPaid').value = (parseFloat(ds.amountPaid || '0') || 0).toFixed(2);
        document.getElementById('formPaidAt').value = ds.lastPayment ? (ds.lastPayment.substring(0,10)) : '<?= date('Y-m-d') ?>';
        document.getElementById('modalTitle').textContent = 'Edit Partial Payment';
      }
      openModal();
    }

    if (e.target.closest('.btn-add-payment')) {
      const id = e.target.closest('tr').dataset.id;
      const tr = document.querySelector(`tr[data-id="${id}"]`);
      if (tr) {
        const ds = tr.dataset;
        document.getElementById('formId').value = id;
        document.getElementById('formHousehold').value = ds.household || '';
        document.getElementById('formAccount').value = ds.account || '';
        document.getElementById('formAmountDue').value = (parseFloat(ds.amountDue || '0') || 0).toFixed(2);
        const due = parseFloat(ds.amountDue || '0') || 0;
        const paid = parseFloat(ds.amountPaid || '0') || 0;
        document.getElementById('formAmountPaid').value = (paid).toFixed(2);
        document.getElementById('formPaidAt').value = '<?= date('Y-m-d') ?>';
        document.getElementById('modalTitle').textContent = 'Add Payment to Partial Billing';
      }
      openModal();
    }
  });

  // Filters
  document.getElementById('applyFilters') && document.getElementById('applyFilters').addEventListener('click', function(){
    const qs = new URLSearchParams();
    const s = document.getElementById('search').value.trim();
    const st = document.getElementById('startDate').value;
    const en = document.getElementById('endDate').value;
    if (s) qs.set('search', s);
    if (st) qs.set('start', st);
    if (en) qs.set('end', en);
      qs.set('status', 'Partial');
      const url = '<?= site_url('admin/partialBillings') ?>' + (qs.toString() ? ('?' + qs.toString()) : '');
    if (typeof loadAjaxPage === 'function') loadAjaxPage(url); else window.location.href = url;
  });

  document.getElementById('resetFilters') && document.getElementById('resetFilters').addEventListener('click', function(){
    document.getElementById('search').value = '';
    document.getElementById('startDate').value = '';
    document.getElementById('endDate').value = '';
      const url = '<?= site_url('admin/partialBillings') ?>?status=Partial';
    if (typeof loadAjaxPage === 'function') loadAjaxPage(url); else window.location.href = url;
  });

  // Form submit: allow normal POST as fallback; if fetch-based API is preferred, uncomment and adapt
  /*
  form.addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(form);
    fetch(form.action, { method:'POST', body: formData, credentials:'same-origin' })
      .then(r=>r.json())
      .then(json => {
        if (json.success) {
          closeModal();
          // reload list
          if (typeof loadAjaxPage === 'function') loadAjaxPage('<?= site_url('admin/partialBillings') ?>'); else window.location.reload();
        } else {
          alert(json.message || 'Failed to save');
        }
      }).catch(err => { console.error(err); alert('Save failed'); });
  });
  */
})();
</script>
