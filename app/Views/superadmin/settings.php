<div class="container-fluid px-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0">Settings</h4>
  </div>

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

<script>
(function(){
  function humanBytes(n){ if(!n) return '0'; const sizes=['B','KB','MB','GB','TB']; let i=0; while(n>=1024 && i<sizes.length-1){ n/=1024; i++; } return n.toFixed(1)+' '+sizes[i]; }

  function loadSystem(){
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
