<style>
	/* Reuse superadmin chat styles, keep admin toggle */
	.chat-side { border-right:1px solid rgba(0,0,0,0.04); }
	#chat-messages { min-height:320px; max-height:60vh; overflow:auto; padding:14px; }
	.chat-bubble{ padding:10px 14px; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.04); }
	@media (max-width:767px){
		#side-panel { position:fixed; left:0; top:0; bottom:0; width:86%; max-width:360px; background:#fff; z-index:1055; box-shadow:0 10px 40px rgba(2,6,23,0.1); transform:translateX(-110%); transition:transform .22s ease; }
		#side-panel.show { transform:translateX(0); }
		#side-panel .close-side { position:absolute; right:8px; top:8px; }
		#toggle-conversations { display:inline-block; }
		.col-md-3.desktop-only{ display:none; }
		.col-md-9 { padding-left:12px; padding-right:12px; }
	}
	@media (min-width:768px){ #toggle-conversations{ display:none; } #side-panel{ position:static; transform:none; box-shadow:none; } }
</style>

<div class="container-fluid mt-4">
	<div class="row">
		<!-- Side panel -->
		<div id="side-panel" class="col-md-3 chat-side desktop-only">
			<div class="card">
				<div class="card-header d-flex justify-content-between align-items-center">
					<div id="side-title">Conversations</div>
				</div>
				<div style="max-height:520px; overflow:auto;">
					<ul id="side-list" class="list-group list-group-flush"></ul>
				</div>
			</div>
		</div>

		<!-- Main panel -->
		<div class="col-md-9" id="main-panel">
			<div class="d-flex mb-2 d-md-none">
				<button id="toggle-conversations" class="btn btn-outline-secondary btn-sm me-2">Open</button>
			</div>

			<div class="card">
				<div class="card-header d-flex justify-content-between align-items-center">
					<div id="chat-mode-label">Live Chat</div>
					<div>
						<div class="btn-group btn-group-sm" role="group" aria-label="Chat mode">
							<button id="mode-users" type="button" class="btn btn-outline-secondary active">Users</button>
							<button id="mode-admins" type="button" class="btn btn-outline-secondary">Admins</button>
						</div>
					</div>
				</div>
				<div id="chat-messages"></div>
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

<!-- mobile side panel -->
<div id="side-panel-mobile" style="display:none;">
	<div id="side-panel" aria-hidden="true">
		<button class="btn btn-sm btn-light close-side">Close</button>
		<div class="card mt-4">
			<div class="card-header" id="side-title-mobile">Conversations</div>
			<div style="max-height:64vh; overflow:auto;"><ul id="side-list-mobile" class="list-group list-group-flush"></ul></div>
		</div>
	</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/2.4.0/purify.min.js"></script>
<script src="<?= base_url('assets/js/safe-html.js') ?>"></script>
<script>
$(function(){
	var adminMap = {}, selectedUserId = null, selectedAdminId = null;
	const csrfName = '<?= csrf_token() ?>';
	let csrfHash = '<?= csrf_hash() ?>';
	const chatBase = '<?= base_url('admin/chat') ?>';

	// UI mode: 'user' or 'admin'
	var viewMode = 'user';

	function renderSideList(items, type){
		// type = 'users' or 'admins'
		var $list = $('#side-list');
		var $listMobile = $('#side-list-mobile');
		$list.empty(); $listMobile.empty();
		if (!items || !items.length) {
			$list.append($('<li class="list-group-item text-muted">No items</li>'));
			$listMobile.append($('<li class="list-group-item text-muted">No items</li>'));
			return;
		}
		items.forEach(function(it){
			if (type === 'users') {
				var li = $('<li class="list-group-item d-flex justify-content-between align-items-start" style="cursor:pointer"></li>');
				var label = it.display || 'User';
				li.text(label + (it.last_at ? ' — ' + it.last_at : ''));
				if (it.unread) li.append($('<span class="badge bg-danger rounded-pill ms-2"></span>').text(it.unread));
				li.attr('data-user', it.user_id);
				li.on('click', function(){ $('#side-list .active, #side-list-mobile .active').removeClass('active'); li.addClass('active'); selectedAdminId = null; selectedUserId = it.user_id; lastTimestamp = null; $('#chat-messages').empty(); loadConversationMessages(it.user_id, false); startPolling(); $('#side-panel').removeClass('show'); });
				$list.append(li.clone(true));
				$listMobile.append(li.clone(true));
			} else {
				var li = $('<li class="list-group-item d-flex justify-content-between align-items-start" style="cursor:pointer"></li>');
				li.text(it.name || 'Admin');
				li.attr('data-admin', it.id);
				li.on('click', function(){ $('#side-list .active, #side-list-mobile .active').removeClass('active'); li.addClass('active'); selectedUserId = null; selectedAdminId = it.id; lastTimestamp = null; $('#chat-messages').empty(); loadAdminMessages(false); startPolling(); $('#side-panel').removeClass('show'); });
				$list.append(li.clone(true));
				$listMobile.append(li.clone(true));
			}
		});
	}

	function loadAdmins(){
		$.get(chatBase + '/getAdmins', function(data){
			adminMap = {};
			data.forEach(function(a){ adminMap[a.id] = a.name || 'Admin'; });
			if (viewMode === 'admin') renderSideList(data, 'admins');
		}).fail(function(){ renderSideList([], 'admins'); });
	}

	// (removed admin-conversation loader to restore previous admin behavior)

	function loadConversations(){
		$.get(chatBase + '/getConversations', function(data){ if (viewMode === 'user') renderSideList(data, 'users'); }).fail(function(){ renderSideList([], 'users'); });
	}

	var lastTimestamp = null;
	var pollId = null;
	var pollInterval = 5000;
	var seenMessageIds = new Set();

	function appendMessages(messages){
		if (!messages || !messages.length) return;
		messages.forEach(function(m){
			if (m.sender !== 'user' && m.sender !== 'admin' && m.sender !== 'admin_internal') return;

			var mid = m.id || m.external_id || null;
			if (mid && seenMessageIds.has(mid)) return;

			var container = document.createElement('div'); container.className = 'd-flex mb-2';
			var author = 'System';
			if (m.sender === 'admin' || m.sender === 'admin_internal') author = adminMap[m.admin_id] || 'Admin';
			if (m.sender === 'user') author = m.author || m.user_name || 'User';
			var safe = (window.DOMPurify && typeof DOMPurify.sanitize === 'function') ? DOMPurify.sanitize(m.message||'') : (m.message||'');
			var bubble = document.createElement('div'); bubble.className = 'chat-bubble p-2 text-white'; bubble.style.maxWidth = '75%';
			if (m.sender === 'admin_internal' || m.is_internal == 1) {
				bubble.classList.add('bg-dark');
				bubble.innerHTML = '<div class="small text-white-50">' + author + ' <span class="badge bg-warning text-dark ms-2">Internal</span> <span class="ms-2 small">' + (m.created_at||'') + '</span></div><div>' + safe + '</div>';
				container.classList.add('justify-content-start');
			} else if (m.sender === 'admin') {
				bubble.classList.add('bg-secondary'); bubble.innerHTML = '<div class="small text-white-50">' + author + ' <span class="ms-2 small">' + (m.created_at||'') + '</span></div><div>' + safe + '</div>'; container.classList.add('justify-content-start');
			} else {
				bubble.classList.add('bg-primary'); bubble.innerHTML = '<div class="small text-white-50 text-end">' + author + ' <span class="ms-2 small">' + (m.created_at||'') + '</span></div><div>' + safe + '</div>'; container.classList.add('justify-content-end');
			}
			container.appendChild(bubble); $('#chat-messages').append(container);
			if (mid) seenMessageIds.add(mid);
			if (m.created_at) lastTimestamp = m.created_at;
		});
		var cm = $('#chat-messages')[0]; if (cm) cm.scrollTop = cm.scrollHeight;
	}

	function loadAdminMessages(incremental){ if (!selectedAdminId) return; var url = chatBase + '/getMessagesForAdmin/' + encodeURIComponent(selectedAdminId); if (incremental && lastTimestamp) url += '?since=' + encodeURIComponent(lastTimestamp); $.get(url, function(data){ if (!incremental) { $('#chat-messages').empty(); seenMessageIds.clear(); } appendMessages(data || []); }); }

	function loadConversationMessages(userId, incremental){ if (!userId) return; var url = chatBase + '/getMessages/' + encodeURIComponent(userId); if (incremental && lastTimestamp) url += '?since=' + encodeURIComponent(lastTimestamp); $.get(url, function(data){ if (!incremental) { $('#chat-messages').empty(); seenMessageIds.clear(); } appendMessages(data || []); if (!incremental || (data && data.length)) { var markPayload = {}; markPayload[csrfName] = csrfHash; $.post(chatBase + '/markRead/' + encodeURIComponent(userId), markPayload).always(function(){ loadConversations(); }); } }); }

	function startPolling(){ stopPolling(); pollId = setInterval(function(){ if (document.hidden) return; if (viewMode === 'user' && selectedUserId) loadConversationMessages(selectedUserId, true); if (viewMode === 'admin' && selectedAdminId) loadAdminMessages(true); }, pollInterval); }
	function stopPolling(){ if (pollId) { clearInterval(pollId); pollId = null; } }

	$('#chat-form').on('submit', function(e){ e.preventDefault(); var msg = $('#chat-input').val().trim(); if(!msg) return; if (viewMode === 'user') { if (!selectedUserId) { alert('Select a conversation to reply to.'); return; } var payload = { message: msg, user_id: selectedUserId }; payload[csrfName] = csrfHash; $.post(chatBase + '/postMessage', payload, function(){ $('#chat-input').val(''); loadConversationMessages(selectedUserId, false); }).fail(function(xhr){ console.error('postMessage failed', xhr.responseText); alert('Failed to send message'); }); return; } else { if (!selectedAdminId) { alert('Select an admin to message.'); return; } var payload = { message: msg, recipient_admin_id: selectedAdminId }; payload[csrfName] = csrfHash; $.post(chatBase + '/postAdminMessage', payload, function(){ $('#chat-input').val(''); lastTimestamp = null; loadAdminMessages(false); }).fail(function(xhr){ console.error('postAdminMessage failed', xhr.responseText); alert('Failed to send admin message'); }); return; } });

	// toggle handlers
	$('#mode-users').on('click', function(){ viewMode = 'user'; $('#mode-users').addClass('active'); $('#mode-admins').removeClass('active'); $('#side-title').text('Conversations'); $('#side-title-mobile').text('Conversations'); loadConversations(); stopPolling(); startPolling(); });
	$('#mode-admins').on('click', function(){ viewMode = 'admin'; $('#mode-admins').addClass('active'); $('#mode-users').removeClass('active'); $('#side-title').text('Admins'); $('#side-title-mobile').text('Admins'); loadAdmins(); stopPolling(); startPolling(); });

	$('#toggle-conversations').on('click', function(){ $('#side-panel').addClass('show'); });
	$(document).on('click', '#side-panel .close-side', function(){ $('#side-panel').removeClass('show'); });

	// initial
	loadAdmins(); loadConversations(); startPolling(); setInterval(function(){ if (viewMode === 'user') loadConversations(); }, 8000);

	$(document).on('click', '.ajax-link', function(){ stopPolling(); });
});
</script>

