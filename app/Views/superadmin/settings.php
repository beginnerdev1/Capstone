<div class="container-fluid px-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0">Settings</h4>
  </div>
          <hr>
          <h6>Restore From Backup</h6>
          <form id="restoreForm" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="mb-2">
              <label class="form-label">Upload Backup ZIP</label>
              <input type="file" id="restoreFile" name="restore_zip" accept=".zip,.enc" class="form-control form-control-sm" required>
              <div class="form-text">Upload a previously generated backup ZIP to restore. Encrypted backups (server-side AES-256-GCM) are accepted as well. This action can modify the database.</div>
              <?php if (getenv('BACKUP_ENCRYPTION_KEY')): ?>
                <div class="form-text small text-success">Backups are encrypted on this server (server-managed key).</div>
              <?php else: ?>
                <div class="form-text small text-muted">Backups on this server are not encrypted. To enable server-side encryption, set the environment variable <code>BACKUP_ENCRYPTION_KEY</code> (base64 or hex 32-byte key).</div>
              <?php endif; ?>
            </div>
            <div class="d-flex gap-2">
              <button id="btnUploadRestore" type="button" class="btn btn-warning btn-sm">Upload for Restore</button>
              <button id="btnApplyRestore" type="button" class="btn btn-danger btn-sm" disabled>Apply Restore (Danger)</button>
            </div>
            <div id="restoreMessage" class="small text-muted mt-2"></div>
          </form>

  <div class="row g-3">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">Backups</div>
        <div class="card-body">
          <form id="backupForm" method="post" action="<?= site_url('superadmin/backup') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
              <label class="form-label">Create Full Backup</label>
              <div class="form-text">This will create a ZIP archive containing JSON and CSV exports of all tables.</div>
              <?php if (getenv('BACKUP_ENCRYPTION_KEY')): ?>
                <div class="form-text small text-success">Backups will be encrypted on creation using the server key.</div>
              <?php else: ?>
                <div class="form-text small text-muted">Backups will be stored unencrypted unless <code>BACKUP_ENCRYPTION_KEY</code> is configured.</div>
              <?php endif; ?>
            </div>
            <button id="btnCreateBackup" type="submit" class="btn btn-primary">Create Backup Now</button>
          </form>

          <hr>
          <h6>Existing Backups</h6>
          <div id="backupsList" class="list-group small text-muted">Loading backups...</div>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card">
        <div class="card-header">System & Database</div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm">
              <tbody>
                <tr><th>PHP Version</th><td id="sysPhp">—</td></tr>
                <tr><th>OS</th><td id="sysOs">—</td></tr>
                <tr><th>DB Version</th><td id="sysDb">—</td></tr>
                <tr><th>DB Size</th><td id="sysDbSize">—</td></tr>
                <tr><th>Disk Free</th><td id="sysDisk">—</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  function humanBytes(n){ if(!n) return '0'; const sizes=['B','KB','MB','GB','TB']; let i=0; while(n>=1024 && i<sizes.length-1){ n/=1024; i++; } return n.toFixed(1)+' '+sizes[i]; }

  window.loadSystem = function(){
    $.getJSON('<?= site_url('superadmin/systemInfo') ?>').done(function(res){
      if(!res || res.status !== 'success') return;
      const info = res.info || {};
      $('#sysPhp').text(info.php_version || '');
      $('#sysOs').text(info.os || '');
      $('#sysDb').text(info.db_version || '');
      $('#sysDbSize').text(humanBytes(info.db_bytes || 0));
      $('#sysDisk').text(humanBytes(info.disk_free || 0));

      const backups = res.backups || [];
      const $list = $('#backupsList'); $list.empty();
      if(backups.length === 0){ $list.append('<div class="list-group-item">No backups found</div>'); return; }
      backups.forEach(function(b){
        const line = `<div class="d-flex justify-content-between align-items-center list-group-item"><div><div>${b.name}</div><div class="small text-muted">${new Date(b.mtime).toLocaleString()} • ${humanBytes(b.size)}</div></div><div><a href="<?= site_url('superadmin/downloadBackup') ?>?file=${encodeURIComponent(b.name)}" class="btn btn-sm btn-outline-primary">Download</a></div></div>`;
        $list.append(line);
      });
    });
  }

  // Submit backup via AJAX, then open the downloaded ZIP via downloadBackup
  $(document).on('submit', '#backupForm', function(e){
    e.preventDefault();
    const btn = $('#btnCreateBackup');
    btn.prop('disabled', true).text('Creating...');
    $.ajax({
      url: $(this).attr('action'),
      method: 'POST',
      data: $(this).serialize(),
      dataType: 'json'
    }).done(function(res){
      if(res && res.status === 'success' && res.filename){
        const url = '<?= site_url('superadmin/downloadBackup') ?>?file=' + encodeURIComponent(res.filename);
        window.open(url, '_blank');
        loadSystem();
      } else {
        alert('Backup failed: ' + (res && res.message ? res.message : 'Unknown'));
      }
    }).fail(function(jqXHR, textStatus, errorThrown){
      var msg = 'Backup request failed';
      try {
        if (jqXHR && jqXHR.responseJSON && jqXHR.responseJSON.message) {
          msg = jqXHR.responseJSON.message;
          if (jqXHR.responseJSON.debug) msg += '\n' + JSON.stringify(jqXHR.responseJSON.debug);
        } else if (jqXHR && jqXHR.responseText) {
          // show a short excerpt of responseText
          var txt = jqXHR.responseText.replace(/\s+/g,' ').trim();
          msg = txt.length > 500 ? txt.slice(0,500) + '...' : txt;
        }
      } catch(e){}
      alert(msg);
    }).always(function(){ btn.prop('disabled', false).text('Create Backup Now'); });
  });

  loadSystem();
})();
</script>
<script>
(function(){
  // Restore upload & apply handlers
  let uploadedRestoreFile = null;

  // Prefer click handler on the upload button (more reliable than submit interception)
  $(document).on('click', '#btnUploadRestore', function(e){
    e.preventDefault();
    const fileEl = document.getElementById('restoreFile');
    if (!fileEl || !fileEl.files || !fileEl.files[0]) { alert('Please select a ZIP file to upload'); return; }
    const f = fileEl.files[0];
    const fd = new FormData();
    fd.append('restore_zip', f);
    // include CSRF token from the form if present
    const csrf = $('#restoreForm').find("input[name^='csrf_']").first();
    if (csrf && csrf.length) fd.append(csrf.attr('name'), csrf.val());

    $('#btnUploadRestore').prop('disabled', true).text('Uploading...');
    $('#restoreMessage').text('Uploading backup...');

    // Use fetch as a robust fallback and provide console logs for debugging
    fetch('<?= site_url('superadmin/uploadRestore') ?>', {
      method: 'POST',
      body: fd,
      credentials: 'same-origin',
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(function(res){
      return res.json().catch(function(){ return { status: 'error', message: 'Invalid JSON response' }; });
    }).then(function(res){
      if (res && res.status === 'success' && res.filename) {
        uploadedRestoreFile = res.filename;
        $('#restoreMessage').text('Uploaded: ' + res.filename + '. Tables found: ' + (res.tables || []).join(', '));
        $('#btnApplyRestore').prop('disabled', false);
        loadSystem();
      } else {
        $('#restoreMessage').text('Upload failed: ' + (res && res.message ? res.message : 'Unknown'));
      }
    }).catch(function(err){
      console.error('uploadRestore failed', err);
      $('#restoreMessage').text('Upload request failed. See console for details.');
    }).finally(function(){
      $('#btnUploadRestore').prop('disabled', false).text('Upload for Restore');
    });
  });

  // Keep original form submit as a fallback in case the button type is changed elsewhere
  $(document).on('submit', '#restoreForm', function(e){
    e.preventDefault();
    $('#btnUploadRestore').trigger('click');
  });

  $(document).on('click', '#btnApplyRestore', function(e){
    if (!uploadedRestoreFile) { alert('No uploaded backup selected.'); return; }
    const confirmVal = prompt('Type RESTORE to confirm you understand this is destructive:');
    if (confirmVal !== 'RESTORE') { alert('Confirmation not provided. Restore cancelled.'); return; }

    $(this).prop('disabled', true).text('Applying...');
    $('#restoreMessage').text('Applying restore (this may take a while)...');

    const fd = new FormData();
    fd.append('filename', uploadedRestoreFile);
    // include CSRF token (pull from page)
    const csrf = $("#restoreForm").find("input[name^='csrf_']").first();
    if (csrf && csrf.length) fd.append(csrf.attr('name'), csrf.val());
    fd.append('confirm', 'RESTORE');

    fetch('<?= site_url('superadmin/applyRestore') ?>', {
      method: 'POST',
      body: fd,
      credentials: 'same-origin',
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(function(res){
      return res.json().catch(function(){ return { status: 'error', message: 'Invalid response' }; });
    }).then(function(res){
      if (res && res.status === 'success') {
        $('#restoreMessage').text('Restore applied successfully: ' + (res.message || 'Completed'));
        uploadedRestoreFile = null;
        $('#btnApplyRestore').prop('disabled', true);
        loadSystem();
      } else {
        $('#restoreMessage').text('Restore failed: ' + (res && res.message ? res.message : 'Unknown'));
      }
    }).catch(function(err){
      console.error('applyRestore failed', err);
      $('#restoreMessage').text('Restore request failed. See console for details.');
    }).finally(function(){
      $('#btnApplyRestore').prop('disabled', false).text('Apply Restore (Danger)');
    });
  });
})();
</script>
