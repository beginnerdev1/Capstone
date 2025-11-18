<div class="container-fluid mt-4">
  <div class="row">
    <div class="col-md-3">
      <div class="card">
        <div class="card-header">Conversations</div>
        <div style="max-height:520px; overflow:auto;">
          <ul id="conversation-list" class="list-group list-group-flush"></ul>
        </div>
      </div>
      <div class="mt-3">
        <h6>Admins</h6>
        <div id="admin-contacts"></div>
      </div>
    </div>
    <div class="col-md-9">
      <div class="card">
        <div class="card-header">Live Chat (Admins)</div>
        <div id="chat-messages" style="height:400px; overflow:auto; padding:12px;"></div>
        <div class="card-body">
          <form id="chat-form">
            <div class="input-group">
              <input type="text" id="chat-input" class="form-control" placeholder="Type message..." aria-label="Message">
              <button class="btn btn-primary" type="submit">Send</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/2.4.0/purify.min.js"></script>
<script src="<?= base_url('assets/js/safe-html.js') ?>"></script>
<script>
$(function(){
  var adminMap = {}, selectedUserId = null;
  // CSRF for AJAX posts
  const csrfName = '<?= csrf_token() ?>';
  let csrfHash = '<?= csrf_hash() ?>';

  function loadAdmins(){
    $.get('<?= base_url('admin/chat/getAdmins') ?>', function(data){
      $('#admin-contacts').empty();
      adminMap = {};
      data.forEach(function(a){
        adminMap[a.id] = a.name || a.email || 'Admin';
        var contact = '<div class="p-2 border mb-2"><strong>'+ (a.name||a.email) +'</strong><br>' +
          (a.email?'<a href="mailto:'+a.email+'">'+a.email+'</a><br>':'') +
          (a.phone?'<a href="tel:'+a.phone+'">'+a.phone+'</a>':'') +
          '</div>';
        $('#admin-contacts').append(contact);
      });
    });
  }

  function loadConversations(){
    $.get('<?= base_url('admin/chat/getConversations') ?>', function(data){
      $('#conversation-list').empty();
      data.forEach(function(c){
        var li = $('<li class="list-group-item d-flex justify-content-between align-items-start"></li>');
        function formatDateToMDY(d){
          if(!d) return '';
          <div class="container-fluid mt-4">
            <div class="row">
              <div class="col-md-3">
                <div class="card">
                  <div class="card-header">Conversations</div>
                  <div style="max-height:520px; overflow:auto;">
                    <ul id="conversation-list" class="list-group list-group-flush"></ul>
                  </div>
                </div>
                <div class="mt-3">
                  <h6>Admins</h6>
                  <div id="admin-contacts"></div>
                </div>
              </div>
              <div class="col-md-9">
                <div class="card">
                  <div class="card-header">Live Chat (Admins)</div>
                  <div id="chat-messages" style="height:400px; overflow:auto; padding:12px;"></div>
                  <div class="card-body">
                    <form id="chat-form">
                      <div class="input-group">
                        <input type="text" id="chat-input" class="form-control" placeholder="Type message..." aria-label="Message">
                        <button class="btn btn-primary" type="submit">Send</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/2.4.0/purify.min.js"></script>
          <script src="<?= base_url('assets/js/safe-html.js') ?>"></script>
          <script>
          $(function(){
            var adminMap = {}, selectedUserId = null;
            // CSRF for AJAX posts
            const csrfName = '<?= csrf_token() ?>';
            let csrfHash = '<?= csrf_hash() ?>';

            function loadAdmins(){
              $.get('<?= base_url('admin/chat/getAdmins') ?>', function(data){
                $('#admin-contacts').empty();
                adminMap = {};
                data.forEach(function(a){
                  adminMap[a.id] = a.name || a.email || 'Admin';
                  var contact = '<div class="p-2 border mb-2"><strong>'+ (a.name||a.email) +'</strong><br>' +
                    (a.email?'<a href="mailto:'+a.email+'">'+a.email+'</a><br>':'') +
                    (a.phone?'<a href="tel:'+a.phone+'">'+a.phone+'</a>':'') +
                    '</div>';
                  $('#admin-contacts').append(contact);
                });
              });
            }

            function loadConversations(){
              $.get('<?= base_url('admin/chat/getConversations') ?>', function(data){
                $('#conversation-list').empty();
                data.forEach(function(c){
                  var li = $('<li class="list-group-item d-flex justify-content-between align-items-start"></li>');
                  function formatDateToMDY(d){
                    if(!d) return '';
                    var dt = new Date(d);
                    if (isNaN(dt)) {
                      // try fallback parse
                      dt = new Date(d.replace(' ', 'T'));
                      if (isNaN(dt)) return d;
                    }
                    var mm = String(dt.getMonth()+1).padStart(2,'0');
                    var dd = String(dt.getDate()).padStart(2,'0');
                    var yyyy = dt.getFullYear();
                    return mm + '/' + dd + '/' + yyyy;
                  }

                  var displayLabel = (c.display && String(c.display).trim()) ? String(c.display).trim() : 'User';
                  var title = $('<div></div>').text(displayLabel + (c.last_at ? ' — ' + formatDateToMDY(c.last_at) : ''));
                  var badge = $('<span class="badge bg-danger rounded-pill ms-2"></span>').text(c.unread||0);
                  if (!c.unread) badge.hide();
                  li.append(title).append(badge);
                  li.attr('data-user', c.user_id);
                  li.css('cursor','pointer');
                  li.on('click', function(){
                      $('#conversation-list .active').removeClass('active');
                      li.addClass('active');
                      selectedUserId = c.user_id;
                      lastTimestamp = null; // reset for full load
                      loadConversationMessages(selectedUserId, false);
                      startMessagePolling();
                    });
                  $('#conversation-list').append(li);
                });
              });
            }

            var lastTimestamp = null;
            var messagePollId = null;
            var messagePollInterval = 3000; // 3s when active

            function appendMessagesToList(messages){
              messages.forEach(function(m){
                var el = document.createElement('div');
                el.className = 'mb-2';
                var authorName = 'System';
                if (m.sender === 'admin') {
                  // keep admin anonymous
                  authorName = 'Admin';
                } else if (m.sender === 'user') {
                  // show user's provided name if available
                  authorName = (m.user_name && String(m.user_name).trim()) ? String(m.user_name).trim() : 'User';
                }
                var header = '<div class="small text-muted">'+authorName+' <span class="ms-2">'+(m.created_at||'')+'</span></div>';
                var safe = (window.DOMPurify && typeof DOMPurify.sanitize === 'function') ? DOMPurify.sanitize(m.message||'') : (m.message||'');
                var wrapper = document.createElement('div');
                wrapper.innerHTML = '<div class="p-2 bg-light rounded">'+header+'<div>'+safe+'</div></div>';
                $('#chat-messages').append(wrapper);
                // update lastTimestamp
                if (m.created_at) lastTimestamp = m.created_at;
              });
              var cm = $('#chat-messages')[0]; if (cm) cm.scrollTop = cm.scrollHeight;
            }

            function loadConversationMessages(userId, incremental){
              if (!userId) return;
              var url = '<?= base_url('admin/chat/getMessages') ?>/' + encodeURIComponent(userId);
              if (incremental && lastTimestamp) {
                url += '?since=' + encodeURIComponent(lastTimestamp);
              }
              $.get(url, function(data){
                if (!incremental) $('#chat-messages').empty();
                appendMessagesToList(data || []);

                // mark read after loading (only if there were messages)
                if (!incremental || (data && data.length)) {
                  var markPayload = {};
                  markPayload[csrfName] = csrfHash;
                  $.post('<?= base_url('admin/chat/markRead') ?>/' + encodeURIComponent(userId), markPayload).always(function(){
                    loadConversations();
                  });
                }
              });
            }

            function startMessagePolling(){
              stopMessagePolling();
              messagePollId = setInterval(function(){
                if (!selectedUserId) return;
                if (document.hidden) return; // don't poll while tab hidden
                loadConversationMessages(selectedUserId, true);
              }, messagePollInterval);
            }

            function stopMessagePolling(){
              if (messagePollId) { clearInterval(messagePollId); messagePollId = null; }
            }

            $('#chat-form').on('submit', function(e){
              e.preventDefault();
              var msg = $('#chat-input').val().trim();
              if(!msg) return;
              if (!selectedUserId) { alert('Select a conversation (user) to reply to.'); return; }
              var payload = { message: msg, user_id: selectedUserId };
              payload[csrfName] = csrfHash;
              $.post('<?= base_url('admin/chat/postMessage') ?>', payload, function(res){
                $('#chat-input').val('');
                loadConversationMessages(selectedUserId);
              });
            });

            // initial load
            loadAdmins();
            loadConversations();
            // poll conversations every 8s to keep unread badges up-to-date
            setInterval(loadConversations, 8000);

            // stop message polling when navigating away via ajax links
            $(document).on('click', '.ajax-link', function(){ stopMessagePolling(); });
          });
          </script>
