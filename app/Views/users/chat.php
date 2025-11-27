<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Admins - Aqua Bill</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= base_url('assets/Users/css/main.css?v=' . time()) ?>" rel="stylesheet">
  <link href="<?= base_url('assets/Users/css/navbar.css?v=' . time()) ?>" rel="stylesheet">

  <style>
    :root {
      --primary: #0066cc;
      --primary-dark: #0052a3;
      --secondary: #6c757d;
      --success: #28a745;
      --bg-main: #f0f4f8;
      --card-bg: #ffffff;
      --border-color: #e1e8ed;
      --text-primary: #1a202c;
      --text-secondary: #718096;
      --text-muted: #a0aec0;
      --shadow-sm: 0 2px 4px rgba(0,0,0,0.04);
      --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
      --shadow-lg: 0 10px 40px rgba(0,0,0,0.12);
    }

    body {
      /* Poppins added as the primary font */
      font-family: 'Poppins', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
      background: var(--bg-main);
      color: var(--text-primary);
      line-height: 1.6;
    }

    /* Main Container */
    .chat-wrapper {
      max-width: 1000px;
      margin: 0 auto;
      padding: 0 20px;
      padding-top: calc(var(--site-header-height, 70px) + 10px);
      padding-bottom: 40px;
    }

    /* Page Header */
    .page-header {
      text-align: center;
      margin-bottom: 30px;
      padding-top: 20px;
    }

    .page-header h1 {
      font-size: 2rem;
      font-weight: 700;
      color: var(--text-primary);
      margin-bottom: 8px;
    }

    .page-header p {
      color: var(--text-secondary);
      font-size: 1rem;
    }

    /* Chat Card */
    .chat-card {
      border: 1px solid var(--border-color);
      border-radius: 16px;
      box-shadow: var(--shadow-lg);
      overflow: hidden;
      background: var(--card-bg);
      transition: box-shadow 0.3s ease;
    }

    /* Chat Header */
    .chat-header {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      padding: 24px 28px;
      display: flex;
      align-items: center;
      gap: 16px;
      border-bottom: 3px solid rgba(255,255,255,0.1);
    }

    .chat-header-content {
      flex: 1;
    }

    .chat-header .title {
      font-weight: 700;
      font-size: 1.25rem;
      color: #ffffff;
      margin-bottom: 4px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .chat-header .subtitle {
      font-size: 0.875rem;
      color: rgba(255,255,255,0.85);
      margin: 0;
    }

    .connection-status {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 8px 16px;
      background: rgba(255,255,255,0.15);
      border-radius: 20px;
      font-size: 0.875rem;
      color: #ffffff;
      font-weight: 500;
      backdrop-filter: blur(10px);
    }

    .status-dot {
      width: 8px;
      height: 8px;
      background: var(--success);
      border-radius: 50%;
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.5; }
    }

    .avatar {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 1.25rem;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .avatar-admin {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: #ffffff;
    }

    /* Chat Area */
    .chat-area {
      display: flex;
      flex-direction: column;
      height: 500px;
    }

    #chat-messages {
      flex: 1;
      padding: 24px 28px;
      overflow-y: auto;
      background: linear-gradient(to bottom, #fafbfc 0%, #ffffff 100%);
      scroll-behavior: smooth;
    }

    /* Empty State */
    .empty-state {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100%;
      text-align: center;
      color: var(--text-muted);
    }

    .empty-state i {
      font-size: 3rem;
      margin-bottom: 16px;
      opacity: 0.5;
    }

    .empty-state p {
      font-size: 1rem;
      margin: 0;
    }

    /* Chat Bubbles */
    .chat-row {
      display: flex;
      margin-bottom: 20px;
      animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .chat-row.admin {
      justify-content: flex-start;
    }

    .chat-row.me {
      justify-content: flex-end;
    }

    .chat-bubble {
      max-width: 70%;
      padding: 14px 18px;
      border-radius: 16px;
      box-shadow: var(--shadow-sm);
      word-wrap: break-word;
      word-break: break-word;
      position: relative;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .chat-bubble:hover {
      transform: translateY(-1px);
      box-shadow: var(--shadow-md);
    }

    .chat-row.admin .chat-bubble {
      background: #f7fafc;
      border: 1px solid #e2e8f0;
      border-bottom-left-radius: 4px;
    }

    .chat-row.me .chat-bubble {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      color: #ffffff;
      border-bottom-right-radius: 4px;
    }

    .meta {
      font-size: 0.75rem;
      font-weight: 600;
      margin-bottom: 6px;
      display: flex;
      align-items: center;
      gap: 8px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .chat-row.admin .meta {
      color: var(--text-secondary);
    }

    .chat-row.me .meta {
      color: rgba(255,255,255,0.9);
    }

    .message-time {
      font-size: 0.7rem;
      opacity: 0.7;
      font-weight: 400;
      text-transform: none;
      letter-spacing: normal;
    }

    .message-content {
      font-size: 0.95rem;
      line-height: 1.5;
    }

    /* Composer */
    .composer {
      padding: 20px 28px;
      border-top: 1px solid var(--border-color);
      background: #ffffff;
      display: flex;
      gap: 12px;
      align-items: flex-end;
    }

    .composer-input-wrapper {
      flex: 1;
      position: relative;
    }

    .composer textarea {
      resize: none;
      min-height: 52px;
      max-height: 120px;
      border-radius: 12px;
      padding: 14px 16px;
      border: 2px solid var(--border-color);
      font-size: 0.95rem;
      transition: all 0.2s ease;
      line-height: 1.5;
    }

    .composer textarea:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 4px rgba(0, 102, 204, 0.1);
      outline: none;
    }

    .composer textarea::placeholder {
      color: var(--text-muted);
    }

    .btn-send {
      min-width: 100px;
      height: 52px;
      border-radius: 12px;
      font-weight: 600;
      font-size: 0.95rem;
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      border: none;
      color: #ffffff;
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .btn-send:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(0, 102, 204, 0.3);
      background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
    }

    .btn-send:active {
      transform: translateY(0);
    }

    .btn-send:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none;
    }

    /* Scrollbar */
    #chat-messages::-webkit-scrollbar {
      width: 8px;
    }

    #chat-messages::-webkit-scrollbar-track {
      background: transparent;
    }

    #chat-messages::-webkit-scrollbar-thumb {
      background: #cbd5e0;
      border-radius: 4px;
    }

    #chat-messages::-webkit-scrollbar-thumb:hover {
      background: #a0aec0;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .chat-wrapper {
        padding: calc(var(--site-header-height, 64px) + 6px) 12px 20px;
        box-sizing: border-box;
        width: 100%;
      }

      .page-header {
        margin-bottom: 20px;
        padding-top: 10px;
      }

      .page-header h1 {
        font-size: 1.5rem;
      }

      .page-header p {
        font-size: 0.875rem;
      }

      .chat-header {
        padding: 18px 16px;
        flex-wrap: wrap;
      }

      .chat-header .title {
        font-size: 1.1rem;
      }

      .chat-header .subtitle {
        font-size: 0.8rem;
      }

      .connection-status {
        width: 100%;
        justify-content: center;
        margin-top: 8px;
      }

      .avatar {
        width: 42px;
        height: 42px;
        font-size: 1.1rem;
      }

      .chat-area {
        height: 400px;
      }

      #chat-messages {
        padding: 16px;
      }

      .chat-bubble {
        max-width: 85%;
        padding: 12px 14px;
      }

      .composer {
        padding: 14px 16px;
        gap: 8px;
      }

      .composer textarea {
        min-height: 48px;
        padding: 12px 14px;
        font-size: 0.9rem;
      }

      .btn-send {
        min-width: 80px;
        height: 48px;
        font-size: 0.875rem;
      }
    }

    @media (max-width: 480px) {
      .chat-wrapper {
        margin: 12px auto;
        padding: calc(var(--site-header-height, 58px) + 8px) 8px 12px;
      }

      .page-header h1 {
        font-size: 1.25rem;
      }

      .chat-header {
        padding: 14px 12px;
      }

      .chat-area {
        height: 350px;
      }

      #chat-messages {
        padding: 12px;
      }

      .chat-bubble {
        max-width: 90%;
        font-size: 0.9rem;
      }

      .btn-send span {
        display: none;
      }

      .btn-send {
        min-width: 52px;
        padding: 0 12px;
      }
    }

    /* Loading indicator */
    .typing-indicator {
      display: flex;
      gap: 4px;
      padding: 12px 16px;
    }

    .typing-indicator span {
      width: 8px;
      height: 8px;
      background: var(--text-muted);
      border-radius: 50%;
      animation: typing 1.4s infinite;
    }

    .typing-indicator span:nth-child(2) {
      animation-delay: 0.2s;
    }

    .typing-indicator span:nth-child(3) {
      animation-delay: 0.4s;
    }

    @keyframes typing {
      0%, 60%, 100% {
        transform: translateY(0);
        opacity: 0.7;
      }
      30% {
        transform: translateY(-10px);
        opacity: 1;
      }
    }
  </style>
</head>
<body>
  <?php include APPPATH . 'Views/users/header.php'; ?>

  <main class="chat-wrapper">
    <div class="page-header">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <h1 class="mb-0"><i class="bi bi-headset"></i> Contact Support</h1>
          <p class="mb-0">We're here to help! Chat with our admin team in real-time.</p>
        </div>
      </div>
    </div>

    <div class="card chat-card">
      <div class="chat-header">
        <div class="avatar avatar-admin">
          <i class="bi bi-shield-check"></i>
        </div>
        <div class="chat-header-content">
          <div class="title">
            <i class="bi bi-chat-dots-fill"></i>
            Live Support Chat
          </div>
          <p class="subtitle">Our team typically responds within minutes</p>
        </div>
        <div class="connection-status">
          <span class="status-dot"></span>
          <span>Connected</span>
        </div>
      </div>

      <div class="chat-area">
        <div id="chat-messages" aria-live="polite" aria-atomic="false">
          <div class="empty-state">
            <i class="bi bi-chat-text"></i>
            <p>Start a conversation with our support team</p>
          </div>
        </div>
      </div>

      <form id="chat-form" class="composer" aria-label="Send message to admins">
        <div class="composer-input-wrapper">
          <textarea 
            id="chat-input" 
            class="form-control" 
            placeholder="Type your message here..." 
            aria-label="Message input"
            rows="1"></textarea>
        </div>
        <button class="btn btn-primary btn-send" type="submit">
          <i class="bi bi-send-fill"></i>
          <span>Send</span>
        </button>
      </form>
    </div>
    <!-- Moved: Back button placed below chat to avoid overlap with offcanvas -->
    <div class="mt-3 text-start d-block">
      <a href="#" class="btn btn-outline-secondary btn-sm" aria-label="Go back" onclick="goBackWithFallback('<?= base_url() ?>'); return false;">
        <i class="bi bi-arrow-left"></i>
        <span class="ms-1">Back</span>
      </a>
    </div>
  </main>

  <?php include APPPATH . 'Views/users/footer.php'; ?>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/2.4.0/purify.min.js"></script>
  <script src="<?= base_url('assets/js/safe-html.js') ?>"></script>
  <script>
    // Navigate back safely: prefer same-origin referrer, otherwise fallback to site root
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
    // CSRF for AJAX posts
    const csrfName = '<?= csrf_token() ?>';
    let csrfHash = '<?= csrf_hash() ?>';

    function formatBubble(m){
      var container = document.createElement('div');
      container.className = 'chat-row';

      var isAdmin = (m.sender === 'admin');
      var authorName = 'System';
      if (isAdmin) {
        authorName = 'Admin';
        container.classList.add('admin');
      } else if (m.sender === 'user') {
        authorName = (m.author && String(m.author).trim()) ? String(m.author).trim() : ((m.user_name && String(m.user_name).trim()) ? String(m.user_name).trim() : 'You');
        container.classList.add('me');
      }

      var safe = (window.DOMPurify && typeof DOMPurify.sanitize === 'function') ? DOMPurify.sanitize(m.message||'') : (m.message||'');

      var bubble = document.createElement('div');
      bubble.className = 'chat-bubble';

      var metaHTML = '<div class="meta">' + authorName + ' <span class="message-time">' + (m.created_at||'') + '</span></div>';
      var contentHTML = '<div class="message-content">' + safe + '</div>';

      bubble.innerHTML = metaHTML + contentHTML;
      container.appendChild(bubble);
      
      return container;
    }

    // Keep track of last seen timestamp/message ids to avoid full re-renders
    var lastTimestamp = null;
    var seenMessageIds = new Set();

    function isAtBottom(el) {
      if (!el) return true;
      return (el.scrollHeight - (el.scrollTop + el.clientHeight)) < 48;
    }

    function loadMessages(incremental = true){
      var url = '<?= base_url('users/chat/getMessages') ?>';
      if (incremental && lastTimestamp) url += '?since=' + encodeURIComponent(lastTimestamp);

      $.get(url, function(data){
        var $container = $('#chat-messages');

        // If not incremental, clear and render full list
        if(!incremental) {
          $container.empty();
          seenMessageIds.clear();
        }

        if(data && data.length > 0) {
          var atBottomBefore = isAtBottom($container[0]);

          data.forEach(function(m){
            var mid = m.id || m.external_id || null;
            if (mid && seenMessageIds.has(mid)) return; // already shown

            $container.append(formatBubble(m));
            if (mid) seenMessageIds.add(mid);
            if (m.created_at) lastTimestamp = m.created_at;
          });

          // Only scroll if user was at or near bottom before new messages
          if (atBottomBefore) $container.scrollTop($container[0].scrollHeight);
        } else if (!incremental) {
          // initial empty state
          $container.html('<div class="empty-state"><i class="bi bi-chat-text"></i><p>Start a conversation with our support team</p></div>');
        }
      });
    }

    $('#chat-form').on('submit', function(e){
      e.preventDefault();
      var msg = $('#chat-input').val().trim();
      if(!msg) return;
      
      var $btn = $('.btn-send');
      $btn.prop('disabled', true);
      
      var payload = { message: msg };
      payload[csrfName] = csrfHash;
      
      $.post('<?= base_url('users/chat/postMessage') ?>', payload, function(res){
        $('#chat-input').val('');
        loadMessages();
        $btn.prop('disabled', false);
        $('#chat-input').focus();
      }).fail(function(xhr){
        $btn.prop('disabled', false);
        if(xhr.status === 401) {
          window.location = '<?= base_url('login') ?>';
        } else {
          alert('Failed to send message. Please try again.');
        }
      });
    });

    // Auto-resize textarea
    $('#chat-input').on('input', function() {
      this.style.height = 'auto';
      this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });

    // Submit on Ctrl+Enter, new line on Enter
    $('#chat-input').on('keydown', function(e) {
      if (e.key === 'Enter' && !e.shiftKey && !e.ctrlKey) {
        e.preventDefault();
        $('#chat-form').submit();
      }
    });

    // Focus on input
    $('#chat-input').focus();

    // Initial load and polling: do a full initial load, then incremental polling to avoid flicker
    loadMessages(false);
    setInterval(function(){ loadMessages(true); }, 3000);
  });
  </script>
</body>
</html>