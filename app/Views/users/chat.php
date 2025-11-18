<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Admins - Aqua Bill</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= base_url('assets/Users/css/main.css?v=' . time()) ?>" rel="stylesheet">
  <link href="<?= base_url('assets/Users/css/navbar.css?v=' . time()) ?>" rel="stylesheet">
</head>
<body>
  <?php include APPPATH . 'Views/users/header.php'; ?>

  <main class="container mt-5">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">Contact Admins — Live Chat</div>
          <div class="card-body">
            <div id="chat-messages" style="height:400px; overflow:auto; padding:12px;"></div>
            <form id="chat-form" class="mt-3">
              <div class="input-group">
                <input type="text" id="chat-input" class="form-control" placeholder="Type your message...">
                <button class="btn btn-primary" type="submit">Send</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php include APPPATH . 'Views/users/footer.php'; ?>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/2.4.0/purify.min.js"></script>
  <script src="<?= base_url('assets/js/safe-html.js') ?>"></script>
  <script>
  $(function(){
    // CSRF for AJAX posts
    const csrfName = '<?= csrf_token() ?>';
    let csrfHash = '<?= csrf_hash() ?>';

    function loadMessages(){
      $.get('<?= base_url('users/chat/getMessages') ?>', function(data){
        $('#chat-messages').empty();
        data.forEach(function(m){
          var container = document.createElement('div');
          container.className = 'd-flex mb-2';

          var isAdmin = (m.sender === 'admin');
          var authorName = 'System';
          if (isAdmin) {
            authorName = 'Admin';
          } else if (m.sender === 'user') {
            authorName = (m.author && String(m.author).trim()) ? String(m.author).trim() : ((m.user_name && String(m.user_name).trim()) ? String(m.user_name).trim() : 'You');
          }

          var safe = (window.DOMPurify && typeof DOMPurify.sanitize === 'function') ? DOMPurify.sanitize(m.message||'') : (m.message||'');
          var bubble = document.createElement('div');
          bubble.className = 'chat-bubble p-2 text-white';
          bubble.style.maxWidth = '80%';
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
        });
        $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
      });
    }

    $('#chat-form').on('submit', function(e){
      e.preventDefault();
      var msg = $('#chat-input').val().trim();
      if(!msg) return;
      var payload = { message: msg };
      payload[csrfName] = csrfHash;
      $.post('<?= base_url('users/chat/postMessage') ?>', payload, function(res){
        $('#chat-input').val('');
        loadMessages();
      }).fail(function(xhr){
        if(xhr.status === 401) {
          window.location = '<?= base_url('login') ?>';
        }
      });
    });

    loadMessages();
    setInterval(loadMessages, 3000);
  });
  </script>
</body>
</html>
