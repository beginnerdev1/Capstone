<style>
/* FIXED CHAT STYLES:
    1. User Messages: Left, Subtle Blue (.bg-secondary)
    2. Admin Messages: Right, Prominent Blue (.bg-primary)
*/

    /* Reuse superadmin chat styles, keep admin toggle */
    .aqua-chat-side { border-right: 1px solid rgba(0, 114, 255, 0.1); background: linear-gradient(to bottom, #f8fbff, #ffffff); }
    #aqua-chat-messages { 
        min-height: 320px; 
        max-height: 60vh; 
        overflow: auto; 
        padding: 20px; 
        background: linear-gradient(to bottom, #fafcff, #ffffff);
        scroll-behavior: smooth;
    }
    
    /* Custom scrollbar */
    #aqua-chat-messages::-webkit-scrollbar { width: 8px; }
    #aqua-chat-messages::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
    #aqua-chat-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    #aqua-chat-messages::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    .aqua-chat-bubble { 
        padding: 12px 16px; 
        border-radius: 16px; 
        box-shadow: 0 2px 8px rgba(0, 114, 255, 0.08);
        font-size: 14px;
        line-height: 1.5;
        animation: aqua-fadeIn 0.3s ease;
    }

    @keyframes aqua-fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* 1. ADMIN MESSAGE (AGENT) - RIGHT ALIGNED */
    .aqua-chat-bubble.bg-primary {
        /* Prominent Blue for Admin (Right) */
        background: linear-gradient(135deg, #0072ff 0%, #004aad 100%) !important;
        color: white !important;
        /* Adjusted corner to point right (3rd value is bottom-left) */
        border-radius: 16px 16px 16px 4px; 
    }
    .aqua-chat-bubble.bg-primary .small,
    .aqua-chat-bubble.bg-primary .text-white-50 { 
        color: rgba(255, 255, 255, 0.7) !important; 
    }

    /* 2. USER MESSAGE (CUSTOMER) - LEFT ALIGNED */
    .aqua-chat-bubble.bg-secondary {
        /* Subtle Pale Blue/White for User (Left) */
        background: linear-gradient(135deg, #e6f4ff 0%, #dbeafe 100%) !important;
        color: #073763 !important;
        border-left: 3px solid #0072ff;
        /* Adjusted corner to point left (4th value is bottom-right) */
        border-radius: 16px 16px 4px 16px; 
    }
    .aqua-chat-bubble.bg-secondary .small,
    .aqua-chat-bubble.bg-secondary .text-white-50 { 
        color: rgba(7, 55, 99, 0.7) !important; 
    }

    /* 3. INTERNAL ADMIN MESSAGE - RIGHT ALIGNED (Dark Gray) */
    .aqua-chat-bubble.bg-dark {
        /* Darker gray for Internal Admin (Right) */
        background: linear-gradient(135deg, #64748b 0%, #475569 100%) !important;
        color: white !important;
        border-radius: 16px 16px 16px 4px;
    }
    .aqua-chat-bubble.bg-dark .small,
    .aqua-chat-bubble.bg-dark .text-white-50 { 
        color: rgba(255, 255, 255, 0.7) !important; 
    }

    /* Side panel styles */
    .aqua-side-panel-card {
        border: none;
        box-shadow: 0 2px 12px rgba(0, 114, 255, 0.08);
        border-radius: 16px;
        overflow: hidden;
    }

    .aqua-side-panel-header {
        background: linear-gradient(135deg, #0072ff 0%, #004aad 100%);
        color: white;
        padding: 16px 20px;
        font-weight: 600;
        font-size: 16px;
        border: none;
    }

    .aqua-conversation-item {
        border: none;
        border-bottom: 1px solid #f1f5f9;
        padding: 14px 20px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 14px;
    }

    .aqua-conversation-item:hover {
        background: linear-gradient(to right, #f0f7ff, #ffffff);
        border-left: 3px solid #0072ff;
        padding-left: 17px;
    }

    .aqua-conversation-item.active {
        background: linear-gradient(135deg, #e6f4ff 0%, #dbeafe 100%);
        border-left: 4px solid #0072ff;
        padding-left: 16px;
        font-weight: 600;
    }

    .aqua-unread-badge {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(239, 68, 68, 0.3);
    }

    /* Main chat card */
    .aqua-main-chat-card {
        border: none;
        box-shadow: 0 4px 20px rgba(0, 114, 255, 0.1);
        border-radius: 16px;
        overflow: hidden;
    }

    .aqua-chat-header {
        background: linear-gradient(135deg, #0072ff 0%, #004aad 100%);
        color: white;
        padding: 18px 24px;
        border: none;
    }

    .aqua-chat-label {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
    }

    /* Mode toggle buttons */
    .aqua-mode-group {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        padding: 4px;
        display: inline-flex;
    }

    .aqua-mode-btn {
        background: transparent;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .aqua-mode-btn:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .aqua-mode-btn.active {
        background: white;
        color: #0072ff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    /* Chat input */
    .aqua-chat-input-group {
        padding: 20px 24px;
        background: #fafcff;
        border-top: 1px solid rgba(0, 114, 255, 0.1);
    }

    .aqua-chat-input {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .aqua-chat-input:focus {
        outline: none;
        border-color: #0072ff;
        box-shadow: 0 0 0 4px rgba(0, 114, 255, 0.1);
    }

    .aqua-send-btn {
        background: linear-gradient(135deg, #0072ff 0%, #004aad 100%);
        border: none;
        border-radius: 12px;
        padding: 12px 28px;
        color: white;
        font-weight: 600;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(0, 114, 255, 0.3);
    }

    .aqua-send-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 114, 255, 0.4);
    }

    .aqua-send-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* Toggle conversations button (mobile) */
    .aqua-toggle-btn {
        background: linear-gradient(135deg, #0072ff 0%, #004aad 100%);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 10px 20px;
        font-size: 14px;
        font-weight: 500;
        box-shadow: 0 2px 8px rgba(0, 114, 255, 0.3);
        transition: all 0.2s ease;
    }

    .aqua-toggle-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 114, 255, 0.4);
    }

    /* Alert/Empty state */
    .aqua-empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #64748b;
    }

    .aqua-empty-icon {
        font-size: 48px;
        color: #cbd5e1;
        margin-bottom: 16px;
    }

    /* Internal badge */
    .aqua-internal-badge {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: #78350f;
        font-size: 10px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Timestamp styling */
    .aqua-timestamp {
        font-size: 11px;
        opacity: 0.8;
        font-weight: 400;
    }

    .aqua-sender-name {
        font-size: 12px;
        font-weight: 600;
        opacity: 0.9;
        margin-bottom: 4px;
    }

    /* Mobile responsive */
    @media (max-width: 767px) {
        #aqua-side-panel { 
            position: fixed; 
            left: 0; 
            top: 0; 
            bottom: 0; 
            width: 86%; 
            max-width: 360px; 
            background: #fff; 
            z-index: 1055; 
            box-shadow: 0 10px 40px rgba(0, 114, 255, 0.2); 
            transform: translateX(-110%); 
            transition: transform 0.3s ease;
        }
        
        #aqua-side-panel.show { 
            transform: translateX(0); 
        }
        
        #aqua-side-panel .aqua-close-side { 
            position: absolute; 
            right: 12px; 
            top: 12px;
            background: white;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 500;
            color: #0072ff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }
        
        #aqua-toggle-conversations { 
            display: inline-block; 
        }
        
        /* Keep the side panel element present on mobile but off-canvas; visibility controlled
           by #aqua-side-panel.show so the toggle button can open it. Setting display:block
           here prevents the element from being permanently hidden by a utility class. */
        .col-md-3.aqua-desktop-only { 
            display: block; 
        }
        
        .col-md-9 { 
            padding-left: 12px; 
            padding-right: 12px; 
        }

        .aqua-chat-header {
            padding: 14px 16px;
        }

        .aqua-chat-label {
            font-size: 16px;
        }

        .aqua-chat-input-group {
            padding: 16px;
        }

        #aqua-chat-messages {
            padding: 16px;
        }
    }
    
    @media (min-width: 768px) { 
        #aqua-toggle-conversations { 
            display: none; 
        } 
        
        #aqua-side-panel { 
            position: static; 
            transform: none; 
            box-shadow: none; 
        } 
        /* Ensure the close button is hidden on desktop */
        #aqua-side-panel .aqua-close-side {
            display: none !important;
        }
    }

    /* Overlay for mobile */
    .aqua-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1054;
    }

    .aqua-overlay.show {
        display: block;
    }
</style>

<div class="container-fluid mt-4">
    <div class="row">
        <div id="aqua-side-panel" class="col-md-3 aqua-chat-side aqua-desktop-only">
            
            <button class="btn aqua-close-side d-md-none">
                 <i class="fas fa-times me-1"></i>Close
            </button>

            <div class="card aqua-side-panel-card">
                <div class="card-header aqua-side-panel-header d-flex justify-content-between align-items-center">
                    <div id="aqua-side-title">Conversations</div>
                </div>
                <div style="max-height: 520px; overflow: auto;">
                    <ul id="aqua-side-list" class="list-group list-group-flush"></ul>
                </div>
            </div>
        </div>

        <div class="col-md-9" id="aqua-main-panel">
            <div class="d-flex mb-3 d-md-none">
                <button id="aqua-toggle-conversations" class="btn aqua-toggle-btn">
                    <i class="fas fa-comments me-2"></i>Conversations
                </button>
            </div>

     <!--        <div class="mb-2">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-2 d-md-none">
                        <a href="#" class="btn btn-outline-secondary btn-sm btn-back" onclick="goBackWithFallback('<?= base_url('admin') ?>'); return false;" aria-label="Go back">
                            <i class="fas fa-arrow-left"></i>
                            <span class="d-none d-sm-inline"> Back</span>
                        </a>
                    </div>
                    <div class="flex-shrink-0 d-none d-md-block">
                        <a href="<?= base_url('admin') ?>" class="btn btn-outline-secondary btn-sm" aria-label="Dashboard">
                            <i class="fas fa-home"></i>
                            <span class="d-none d-sm-inline"> Dashboard</span>
                        </a>
                    </div>
                    <div class="flex-grow-1"></div>
                </div>
            </div> -->

            <div class="card aqua-main-chat-card">
                <div class="card-header aqua-chat-header d-flex justify-content-between align-items-center">
                    <div id="aqua-chat-mode-label" class="aqua-chat-label">
                        <i class="fas fa-comment-dots me-2"></i>Live Chat
                    </div>
                    <div>
                        <div class="btn-group aqua-mode-group" role="group" aria-label="Chat mode">
                            <button id="aqua-mode-users" type="button" class="aqua-mode-btn active">
                                <i class="fas fa-users me-1"></i>Users
                            </button>
                            <button id="aqua-mode-admins" type="button" class="aqua-mode-btn">
                                <i class="fas fa-user-shield me-1"></i>Admins
                            </button>
                        </div>
                    </div>
                </div>
                <div id="aqua-chat-messages"></div>
                <div class="aqua-chat-input-group">
                    <form id="aqua-chat-form">
                        <div class="input-group">
                            <input type="text" id="aqua-chat-input" class="form-control aqua-chat-input" placeholder="Type your message..." aria-label="Message">
                            <button class="btn aqua-send-btn" type="submit">
                                <i class="fas fa-paper-plane me-2"></i>Send
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="aqua-overlay" id="aqua-overlay"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/2.4.0/purify.min.js"></script>
<script src="<?= base_url('assets/js/safe-html.js') ?>"></script>
<script>
        // Navigate back safely: prefer same-origin referrer, otherwise fallback to admin dashboard
        function goBackWithFallback(fallbackUrl) {
            try {
                var ref = document.referrer || '';
                if (ref && ref.indexOf(location.origin) === 0) {
                    history.back();
                    return;
                }
                if (history.length > 1) {
                    history.back();
                    return;
                }
            } catch (e) {
                // ignore and fall through to fallback
            }
            window.location.href = fallbackUrl || '/';
        }

$(function(){
    var adminMap = {}, selectedUserId = null, selectedAdminId = null;
    // current admin id (prefer admin session, fall back to superadmin if present)
    const currentAdminId = <?= (session()->get('admin_id') ?? session()->get('superadmin_id') ?? 'null') ?>;
    const csrfName = '<?= csrf_token() ?>';
    let csrfHash = '<?= csrf_hash() ?>';
    const chatBase = '<?= base_url('admin/chat') ?>';

    // UI mode: 'user' or 'admin'
    var viewMode = 'user';

    /**
     * Renders the conversation list for the side panel.
     * FIX: Removed all references to the obsolete mobile list element.
     */
    function renderSideList(items, type){
        var $list = $('#aqua-side-list');
        $list.empty();
        
        if (!items || !items.length) {
            var emptyHtml = '<li class="list-group-item aqua-empty-state"><div class="aqua-empty-icon"><i class="fas fa-inbox"></i></div><div>No ' + (type === 'users' ? 'conversations' : 'admins') + ' yet</div></li>';
            $list.append($(emptyHtml));
            return;
        }
        
        items.forEach(function(it){
            var li = $('<li class="list-group-item aqua-conversation-item d-flex justify-content-between align-items-start" style="cursor: pointer"></li>');
            
            if (type === 'users') {
                var label = it.display || 'User';
                var textContent = $('<div></div>');
                textContent.append($('<div class="fw-semibold"></div>').text(label));
                if (it.last_at) {
                    textContent.append($('<div class="small text-muted aqua-timestamp"></div>').text(it.last_at));
                }
                li.append(textContent);
                if (it.unread) li.append($('<span class="badge aqua-unread-badge"></span>').text(it.unread));
                li.attr('data-user', it.user_id);
                
                li.on('click', function(){ 
                    $('#aqua-side-list .active').removeClass('active'); 
                    li.addClass('active'); 
                    selectedAdminId = null; 
                    selectedUserId = it.user_id; 
                    lastTimestamp = null; 
                    $('#aqua-chat-messages').empty(); 
                    loadConversationMessages(it.user_id, false); 
                    startPolling(); 
                    // CLOSING LOGIC (Mobile only)
                    $('#aqua-side-panel').removeClass('show'); 
                    $('#aqua-overlay').removeClass('show');
                });
            } else { // type === 'admins'
                var adminName = $('<div class="fw-semibold"></div>').text(it.name || 'Admin');
                li.append(adminName);
                li.attr('data-admin', it.id);
                
                li.on('click', function(){ 
                    $('#aqua-side-list .active').removeClass('active'); 
                    li.addClass('active'); 
                    selectedUserId = null; 
                    selectedAdminId = it.id; 
                    lastTimestamp = null; 
                    $('#aqua-chat-messages').empty(); 
                    loadAdminMessages(false); 
                    startPolling(); 
                    // CLOSING LOGIC (Mobile only)
                    $('#aqua-side-panel').removeClass('show'); 
                    $('#aqua-overlay').removeClass('show');
                });
            }
            $list.append(li);
        });
    }

    function loadAdmins(){
        $.get(chatBase + '/getAdmins', function(data){
            adminMap = {};
            data.forEach(function(a){ adminMap[a.id] = a.name || 'Admin'; });
            if (viewMode === 'admin') renderSideList(data, 'admins');
        }).fail(function(){ renderSideList([], 'admins'); });
    }

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
            // Accept both legacy `chat_messages` and new `admin_chats` shapes
            var senderId = m.admin_id || m.sender_admin_id || null;
            var senderType = m.sender || null;
            var isInternal = (m.sender === 'admin_internal') || (m.is_internal == 1) || (m.is_broadcast == 1);

            if (senderType && senderType !== 'user' && senderType !== 'admin' && senderType !== 'admin_internal') return;

            var mid = m.id || m.external_id || null;
            if (mid && seenMessageIds.has(mid)) return;

            var container = document.createElement('div'); container.className = 'd-flex mb-3';
            var author = 'System';
            if (senderType === 'admin' || senderType === 'admin_internal') author = adminMap[senderId] || 'Admin';
            if (senderType === 'user') author = m.author || m.user_name || 'User';
            var safe = (window.DOMPurify && typeof DOMPurify.sanitize === 'function') ? DOMPurify.sanitize(m.message||'') : (m.message||'');
            var bubble = document.createElement('div'); bubble.className = 'aqua-chat-bubble p-2'; bubble.style.maxWidth = '75%';

            // --- MESSAGE ALIGNMENT LOGIC ---
            if (senderType === 'user') {
                // 1. User Message (Customer): LEFT ALIGNED
                bubble.classList.add('bg-secondary'); 
                bubble.innerHTML = '<div class="aqua-sender-name text-muted">' + author + ' <span class="ms-2 aqua-timestamp">' + (m.created_at||'') + '</span></div><div>' + safe + '</div>';
                container.classList.add('justify-content-start'); 
            } else {
                // 2. Admin/Internal Message: RIGHT ALIGNED
                bubble.classList.add('text-white'); 
                
                if (isInternal) {
                    // Internal Admin
                    bubble.classList.add('bg-dark');
                    bubble.innerHTML = '<div class="aqua-sender-name text-white-50 text-end"><span class="badge aqua-internal-badge me-2">Internal</span> ' + author + ' <span class="ms-2 aqua-timestamp">' + (m.created_at||'') + '</span></div><div>' + safe + '</div>';
                } else {
                    // Regular Admin (Agent)
                    bubble.classList.add('bg-primary');
                    bubble.innerHTML = '<div class="aqua-sender-name text-white-50 text-end">' + author + ' <span class="ms-2 aqua-timestamp">' + (m.created_at||'') + '</span></div><div>' + safe + '</div>';
                }

                container.classList.add('justify-content-end'); 
            }
            // --- END MESSAGE ALIGNMENT LOGIC ---

            container.appendChild(bubble); $('#aqua-chat-messages').append(container);
            if (mid) seenMessageIds.add(mid);
            if (m.created_at) lastTimestamp = m.created_at;
        });
        var cm = $('#aqua-chat-messages')[0]; if (cm) cm.scrollTop = cm.scrollHeight;
    }

    function loadAdminMessages(incremental){ if (!selectedAdminId) return; var url = chatBase + '/getMessagesForAdmin/' + encodeURIComponent(selectedAdminId); if (incremental && lastTimestamp) url += '?since=' + encodeURIComponent(lastTimestamp); $.get(url, function(data){ if (!incremental) { $('#aqua-chat-messages').empty(); seenMessageIds.clear(); } appendMessages(data || []); }); }

    function loadConversationMessages(userId, incremental){ if (!userId) return; var url = chatBase + '/getMessages/' + encodeURIComponent(userId); if (incremental && lastTimestamp) url += '?since=' + encodeURIComponent(lastTimestamp); $.get(url, function(data){ if (!incremental) { $('#aqua-chat-messages').empty(); seenMessageIds.clear(); } appendMessages(data || []); if (!incremental || (data && data.length)) { var markPayload = {}; markPayload[csrfName] = csrfHash; $.post(chatBase + '/markRead/' + encodeURIComponent(userId), markPayload).always(function(){ loadConversations(); }); } }); }

    function startPolling(){ stopPolling(); pollId = setInterval(function(){ if (document.hidden) return; if (viewMode === 'user' && selectedUserId) loadConversationMessages(selectedUserId, true); if (viewMode === 'admin' && selectedAdminId) loadAdminMessages(true); }, pollInterval); }
    function stopPolling(){ if (pollId) { clearInterval(pollId); pollId = null; } }

    // Prevent duplicate sends: generate client external_id and disable submit while request is in-flight
    var sending = false;
    function makeExternalId(){
        if (window.crypto && crypto.randomUUID) return crypto.randomUUID();
        return 'c' + Math.random().toString(36).slice(2,12);
    }

    $('#aqua-chat-form').on('submit', function(e){
        e.preventDefault();
        if (sending) return;
        var msg = $('#aqua-chat-input').val().trim(); if(!msg) return;
        sending = true; $('#aqua-chat-form button[type=submit]').prop('disabled', true);

        var ext = makeExternalId();

        if (viewMode === 'user') {
            if (!selectedUserId) { alert('Select a conversation to reply to.'); sending = false; $('#aqua-chat-form button[type=submit]').prop('disabled', false); return; }
            var payload = { message: msg, user_id: selectedUserId, external_id: ext };
            payload[csrfName] = csrfHash;
            $.post(chatBase + '/postMessage', payload, function(){ $('#aqua-chat-input').val(''); loadConversationMessages(selectedUserId, false); }).fail(function(xhr){ console.error('postMessage failed', xhr.responseText); alert('Failed to send message'); }).always(function(){ sending = false; $('#aqua-chat-form button[type=submit]').prop('disabled', false); });
            return;
        } else {
            if (!selectedAdminId) { alert('Select an admin to message.'); sending = false; $('#aqua-chat-form button[type=submit]').prop('disabled', false); return; }
            var payload = { message: msg, recipient_admin_id: selectedAdminId, external_id: ext };
            payload[csrfName] = csrfHash;
            $.post(chatBase + '/postAdminMessage', payload, function(){ $('#aqua-chat-input').val(''); lastTimestamp = null; loadAdminMessages(false); }).fail(function(xhr){ console.error('postAdminMessage failed', xhr.responseText); alert('Failed to send admin message'); }).always(function(){ sending = false; $('#aqua-chat-form button[type=submit]').prop('disabled', false); });
            return;
        }
    });

    // Toggle handlers
    $('#aqua-mode-users').on('click', function(){ viewMode = 'user'; $('#aqua-mode-users').addClass('active'); $('#aqua-mode-admins').removeClass('active'); $('#aqua-side-title').text('Conversations'); loadConversations(); stopPolling(); startPolling(); });
    $('#aqua-mode-admins').on('click', function(){ viewMode = 'admin'; $('#aqua-mode-admins').addClass('active'); $('#aqua-mode-users').removeClass('active'); $('#aqua-side-title').text('Admins'); loadAdmins(); stopPolling(); startPolling(); });

    // MOBILE TOGGLE HANDLERS (Fixed)
    $('#aqua-toggle-conversations').on('click', function(){ 
        $('#aqua-side-panel').addClass('show'); 
        $('#aqua-overlay').addClass('show');
    });
    
    $(document).on('click', '#aqua-side-panel .aqua-close-side', function(){ 
        $('#aqua-side-panel').removeClass('show'); 
        $('#aqua-overlay').removeClass('show');
    });

    $('#aqua-overlay').on('click', function(){
        $('#aqua-side-panel').removeClass('show');
        $(this).removeClass('show');
    });

    // initial
    loadAdmins(); loadConversations(); startPolling(); setInterval(function(){ if (viewMode === 'user') loadConversations(); }, 8000);

    $(document).on('click', '.ajax-link', function(){ stopPolling(); });
});
</script>