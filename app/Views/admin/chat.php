<style>
	/* Small responsive tweaks for admin chat */
	.chat-side { border-right:1px solid rgba(0,0,0,0.04); }
	#chat-messages { min-height:320px; max-height:60vh; overflow:auto; padding:14px; }
	.chat-bubble{ padding:10px 14px; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.04); }
	@media (max-width:767px){
		/* hide side column; toggle with button */
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
		<!-- Side panel: conversations + admins -->
		<div id="side-panel" class="col-md-3 chat-side desktop-only">
			<div class="card">
				<div class="card-header d-flex justify-content-between align-items-center">
					<div>Conversations</div>
				</div>
				<div style="max-height:520px; overflow:auto;">
					<ul id="conversation-list" class="list-group list-group-flush"></ul>
				</div>
			</div>
			<div class="mt-3">
				<h6>Admins</h6>
				<div id="admin-contacts"></div>
			</div>
		</div>

		<!-- Main panel -->
		<div class="col-md-9" id="main-panel">
			<div class="d-flex mb-2 d-md-none">
				<button id="toggle-conversations" class="btn btn-outline-secondary btn-sm me-2">Conversations</button>
			</div>

			<div class="card">
				<div class="card-header">Live Chat (Admins)</div>
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
			<div class="card-header">Conversations</div>
			<div style="max-height:64vh; overflow:auto;"><ul id="conversation-list-mobile" class="list-group list-group-flush"></ul></div>
		</div>
		<div class="mt-3">
			<h6>Admins</h6>
			<div id="admin-contacts-mobile"></div>
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

	// Determine chat route base: use superadmin routes when served under /superadmin
	const isSuper = (window.location.pathname.indexOf('/superadmin') !== -1);
	const chatBase = isSuper
		? '<?= base_url('superadmin/chat') ?>'
		: '<?= base_url('admin/chat') ?>';

	function loadAdmins(){
		$.get(chatBase + '/getAdmins', function(data){
			$('#admin-contacts, #admin-contacts-mobile').empty();
			adminMap = {};
			data.forEach(function(a){
				adminMap[a.id] = a.name || a.email || 'Admin';
				var contact = $('<div class="p-2 border mb-2"><strong></strong><br></div>');
				contact.find('strong').text(a.name || a.email || 'Admin');
				if (a.email) contact.append($('<div>').html('<a href="mailto:'+a.email+'">'+a.email+'</a>'));
				if (a.phone) contact.append($('<div>').html('<a href="tel:'+a.phone+'">'+a.phone+'</a>'));
				$('#admin-contacts, #admin-contacts-mobile').append(contact.clone());
			});
		});
	}

	function formatDateToMDY(d){
		if(!d) return '';
		var dt = new Date(d);
		if (isNaN(dt)) {
			dt = new Date(d.replace(' ', 'T'));
			if (isNaN(dt)) return d;
		}
		var mm = String(dt.getMonth()+1).padStart(2,'0');
		var dd = String(dt.getDate()).padStart(2,'0');
		var yyyy = dt.getFullYear();
		return mm + '/' + dd + '/' + yyyy;
	}

	function loadConversations(){
		// For superadmin view we do NOT load user conversations — only show admins
		if (isSuper) return;

		$.get(chatBase + '/getConversations', function(data){
			$('#conversation-list, #conversation-list-mobile').empty();
			data.forEach(function(c){
				var li = $('<li class="list-group-item d-flex justify-content-between align-items-start"></li>');
				var displayLabel = (c.display && String(c.display).trim()) ? String(c.display).trim() : 'User';
				var title = $('<div></div>').text(displayLabel + (c.last_at ? ' — ' + formatDateToMDY(c.last_at) : ''));
				var badge = $('<span class="badge bg-danger rounded-pill ms-2"></span>').text(c.unread||0);
				if (!c.unread) badge.hide();
				li.append(title).append(badge);
				li.attr('data-user', c.user_id);
				li.css('cursor','pointer');
				li.on('click', function(){
						$('#conversation-list .active, #conversation-list-mobile .active').removeClass('active');
						li.addClass('active');
						selectedUserId = c.user_id;
						lastTimestamp = null; // reset for full load
						loadConversationMessages(selectedUserId, false);
						startMessagePolling();
						// hide mobile side panel if open
						$('#side-panel').removeClass('show');
					});
				$('#conversation-list, #conversation-list-mobile').append(li.clone(true));
			});
		});
	}

	var lastTimestamp = null;
	var messagePollId = null;
	var messagePollInterval = 3000; // 3s when active

	function appendMessagesToList(messages){
		messages.forEach(function(m){
			var container = document.createElement('div');
			container.className = 'd-flex mb-2';

			var isAdmin = (m.sender === 'admin');
			var authorName = 'System';
			if (isAdmin) {
				authorName = 'Admin';
			} else if (m.sender === 'user') {
				authorName = (m.author && String(m.author).trim()) ? String(m.author).trim() : ((m.user_name && String(m.user_name).trim()) ? String(m.user_name).trim() : 'User');
			}

			var safe = (window.DOMPurify && typeof DOMPurify.sanitize === 'function') ? DOMPurify.sanitize(m.message||'') : (m.message||'');
			var bubble = document.createElement('div');
			bubble.className = 'chat-bubble p-2 text-white';
			bubble.style.maxWidth = '75%';
			bubble.style.borderRadius = '12px';

			if (isAdmin) {
				container.classList.add('justify-content-start');
				bubble.classList.add('bg-secondary');
				bubble.innerHTML = '<div class="small text-white-50">' + authorName + ' <span class="ms-2 small">' + (m.created_at||'') + '</span></div><div>' + safe + '</div>';
			} else {
				container.classList.add('justify-content-end');
				bubble.classList.add('bg-primary');
				bubble.innerHTML = '<div class="small text-white-50 text-end">' + authorName + ' <span class="ms-2 small">' + (m.created_at||'') + '</span></div><div>' + safe + '</div>';
			}

			container.appendChild(bubble);
			$('#chat-messages').append(container);
			if (m.created_at) lastTimestamp = m.created_at;
		});
		var cm = $('#chat-messages')[0]; if (cm) cm.scrollTop = cm.scrollHeight;
	}

	function loadConversationMessages(userId, incremental){
		if (!userId) return;
		var url = chatBase + '/getMessages/' + encodeURIComponent(userId);
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
				$.post(chatBase + '/markRead/' + encodeURIComponent(userId), markPayload).always(function(){
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
		$.post(chatBase + '/postMessage', payload, function(res){
			$('#chat-input').val('');
			loadConversationMessages(selectedUserId);
		});
	});

	// mobile side panel toggle
	$('#toggle-conversations').on('click', function(){ $('#side-panel').addClass('show'); });
	$(document).on('click', '#side-panel .close-side', function(){ $('#side-panel').removeClass('show'); });

	// initial load
	loadAdmins();
	if (!isSuper) {
		loadConversations();
		// poll conversations every 8s to keep unread badges up-to-date
		setInterval(loadConversations, 8000);
	} else {
		// If superadmin, hide user conversation UI and message form — show admins only
		$('#side-panel, #side-panel-mobile, #conversation-list, #conversation-list-mobile').hide();
		$('#chat-messages, #chat-form').hide();
		$('.card-header').first().text('Admins');
	}

	// stop message polling when navigating away via ajax links
	$(document).on('click', '.ajax-link', function(){ stopMessagePolling(); });

});
</script>

