<style>
	/* Reuse admin chat styles but simpler for superadmin admin-only chat */
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
		<!-- Side panel: admins -->
		<div id="side-panel" class="col-md-3 chat-side desktop-only">
			<div class="card">
				<div class="card-header d-flex justify-content-between align-items-center">
					<div>Admins</div>
				</div>
				<div style="max-height:520px; overflow:auto;">
					<ul id="admin-list" class="list-group list-group-flush"></ul>
				</div>
			</div>
		</div>

		<!-- Main panel -->
		<div class="col-md-9" id="main-panel">
			<div class="d-flex mb-2 d-md-none">
				<button id="toggle-conversations" class="btn btn-outline-secondary btn-sm me-2">Admins</button>
			</div>

			<div class="card">
				<div class="card-header">Admin Chat (Admins only)</div>
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

<!-- small side-panel when on mobile -->
<div id="side-panel-mobile" style="display:none;">
	<div id="side-panel" aria-hidden="true">
		<button class="btn btn-sm btn-light close-side">Close</button>
		<div class="card mt-4">
			<div class="card-header">Admins</div>
			<div style="max-height:64vh; overflow:auto;"><ul id="admin-list-mobile" class="list-group list-group-flush"></ul></div>
		</div>
	</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/2.4.0/purify.min.js"></script>
<script src="<?= base_url('assets/js/safe-html.js') ?>"></script>
<script>
$(function(){
	var adminMap = {}, selectedAdminId = null;
	// current admin id (sender) from session — prefer superadmin when present
	const currentAdminId = <?= (session()->get('superadmin_id') ?? session()->get('admin_id') ?? 'null') ?>;
	const csrfName = '<?= csrf_token() ?>';
	let csrfHash = '<?= csrf_hash() ?>';
	const chatBase = '<?= base_url('superadmin/chat') ?>';

	function loadAdmins(){
		$.get(chatBase + '/getAdmins', function(data){
			console.log('superadmin: getAdmins response', data);
			$('#admin-list, #admin-list-mobile').empty();
			adminMap = {};
			if (!data || !data.length) {
				var emptyLi = $('<li class="list-group-item text-muted">No admins found.</li>');
				$('#admin-list').append(emptyLi.clone());
				$('#admin-list-mobile').append(emptyLi.clone());
				return;
			}
			data.forEach(function(a){
				adminMap[a.id] = a.name || a.email || 'Admin';
				var li = $('<li class="list-group-item d-flex justify-content-between align-items-start" style="cursor:pointer"></li>');
				li.text(a.name || a.email || 'Admin');
				li.attr('data-admin', a.id);
					li.on('click', function(){
						$('#admin-list .active, #admin-list-mobile .active').removeClass('active');
						li.addClass('active');
						selectedAdminId = a.id; // which admin we're viewing
						lastTimestamp = null; // reset for full load
						$('#chat-messages').empty();
						loadAdminMessages(false);
						startPolling();
						// hide mobile side panel if open
						$('#side-panel').removeClass('show');
					});
				$('#admin-list').append(li.clone(true));
				$('#admin-list-mobile').append(li.clone(true));
			});
		}).fail(function(xhr, status, err){
			console.error('superadmin: getAdmins failed', status, err, xhr.responseText);
			$('#admin-list, #admin-list-mobile').empty().append($('<li class="list-group-item text-danger">Failed to load admins</li>'));
		});
	}

	var lastTimestamp = null;
	var pollId = null;
	// Increase poll interval slightly to reduce chattiness
	var pollInterval = 5000;
	// Keep track of displayed message ids to avoid duplicate appends
	var seenMessageIds = new Set();

	function appendMessagesToList(messages){
		if (!messages || !messages.length) return;
		messages.forEach(function(m){

			// Only show admin-sent messages (admin-only chat)
			if (m.sender !== 'admin' && m.sender !== 'admin_internal') return;

			// Use the numeric DB id or external_id as a stable key
			var mid = m.id || m.external_id || null;
			if (mid && seenMessageIds.has(mid)) {
				// already rendered
				return;
			}

			var container = document.createElement('div');
			container.className = 'd-flex mb-2';

			var authorName = 'Admin';
			if (m.admin_id && adminMap[m.admin_id]) authorName = adminMap[m.admin_id];

			var safe = (window.DOMPurify && typeof DOMPurify.sanitize === 'function') ? DOMPurify.sanitize(m.message||'') : (m.message||'');
			var bubble = document.createElement('div');
			bubble.className = 'chat-bubble p-2 text-white';
			bubble.style.maxWidth = '75%';
			bubble.style.borderRadius = '12px';

			// Align based on sender: messages from current admin are right-aligned
			if (currentAdminId && parseInt(m.admin_id) === parseInt(currentAdminId)) {
				container.classList.add('justify-content-end');
			} else {
				container.classList.add('justify-content-start');
			}

			if (m.sender === 'admin_internal' || m.is_internal == 1) {
				bubble.classList.add('bg-dark');
				bubble.innerHTML = '<div class="small text-white-50">' + authorName + ' <span class="badge bg-warning text-dark ms-2">Internal</span> <span class="ms-2 small">' + (m.created_at||'') + '</span></div><div>' + safe + '</div>';
			} else {
				bubble.classList.add('bg-secondary');
				bubble.innerHTML = '<div class="small text-white-50">' + authorName + ' <span class="ms-2 small">' + (m.created_at||'') + '</span></div><div>' + safe + '</div>';
			}

			container.appendChild(bubble);
			$('#chat-messages').append(container);

			if (mid) seenMessageIds.add(mid);
			if (m.created_at) lastTimestamp = m.created_at;
		});
		var cm = $('#chat-messages')[0]; if (cm) cm.scrollTop = cm.scrollHeight;
	}

	function loadAdminMessages(incremental){
		if (!selectedAdminId) return;
		var url = chatBase + '/getMessagesForAdmin/' + encodeURIComponent(selectedAdminId);
		if (incremental && lastTimestamp) url += '?since=' + encodeURIComponent(lastTimestamp);
		$.get(url, function(data){
			if (!incremental) { $('#chat-messages').empty(); seenMessageIds.clear(); }
			appendMessagesToList(data || []);
		});
	}

	function startPolling(){
		stopPolling();
		pollId = setInterval(function(){
			if (document.hidden) return;
			loadAdminMessages(true);
		}, pollInterval);
	}

	function stopPolling(){ if (pollId) { clearInterval(pollId); pollId = null; } }

	$('#chat-form').on('submit', function(e){
		e.preventDefault();
		var msg = $('#chat-input').val().trim();
		if(!msg) return;
		if (!selectedAdminId) { alert('Select an admin to message.'); return; }
		var payload = { message: msg, recipient_admin_id: selectedAdminId };
		payload[csrfName] = csrfHash;
		$.post(chatBase + '/postAdminMessage', payload, function(res){
			$('#chat-input').val('');
			lastTimestamp = null;
			loadAdminMessages(false);
		}).fail(function(xhr){
			console.error('postAdminMessage failed', xhr.responseText);
			alert('Failed to send message');
		});
	});

	// mobile side panel toggle
	$('#toggle-conversations').on('click', function(){ $('#side-panel').addClass('show'); });
	$(document).on('click', '#side-panel .close-side', function(){ $('#side-panel').removeClass('show'); });

	// initial
	loadAdmins();
	loadAdminMessages();
	startPolling();

	// cleanup on navigation
	$(document).on('click', '.ajax-link', function(){ stopPolling(); });

});
</script>