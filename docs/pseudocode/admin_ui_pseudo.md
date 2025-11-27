Admin UI — Pseudocode

Purpose: Describe client-side behavior (polling, sanitizers) and AJAX flows used by admin pages (users, billing, failed transactions).

Client polling + refresh
- The UI implements a lightweight fingerprint polling: periodically request a small endpoint that returns a fingerprint or "last changed" token for tables.
- If fingerprint differs from last seen, call AJAX to refresh the affected tables.

Pseudocode (polling):
setInterval(function(){
    token = localFingerprint
    $.get('/admin/fingerprint', {resource:'users, payments'})
      .done(function(res){
          if (res.token !== token) {
              reloadTables(res.affected)
              localFingerprint = res.token
          }
      })
}, POLL_INTERVAL)

Loading users (AJAX)
function loadUsers(search='', purok='', status=''):
    showLoading()
    $.get('/admin/filterUsers', {search, purok, status}, function(users){
        renderUsersTable(users)
        hideLoading()
    }).fail(showError)

Sanitizers (already implemented client-side):
- name inputs: on input/paste, strip anything not in [A-Za-z. ]
- phone inputs: on input/paste, strip anything not in [0-9NnAa/]

Add User flow (client)
- on open: populate purok select
- on submit: perform HTML pattern validation + custom checks (e.g. line_number alphanumeric)
- if valid: send POST /admin/addUser with serialized form
- on success: close modal, reload users
- on error: show validation messages returned from server

Edit User flow (client)
- open modal -> GET /admin/getUser/{id} -> fill fields
- on submit: run same validations as Add, POST /admin/updateUser/{id}

UI Rendering rules
- Ensure accessible labels and aria attributes for modals and forms
- Provide clear invalid-feedback blocks on inputs
- For long admin tables on small screens, convert rows to card-like blocks (CSS rules exist to render table as cards)

Best practices
- Keep client-side sanitizer and HTML patterns for UX but always validate again on server.
- Use descriptive `admin_reference` notes when admin actions change DB rows so UI can show context.
