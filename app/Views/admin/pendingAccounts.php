<style>
:root {
    --primary: #667eea;
    --primary-dark: #5568d3;
    --success: #10b981;
    --danger: #ef4444;
    --light: #f3f4f6;
    --border: #e5e7eb;
    --dark: #1f2937;
    --muted: #6b7280;
}
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
    background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
}
.container-fluid { padding: 2rem 1rem; }
.card { background: white; border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid var(--border); overflow: hidden; }
.card-header { padding: 1.5rem; border-bottom: 1px solid var(--border); background: var(--light); }
.card-body { padding: 1.5rem; }
.table { width: 100%; margin-bottom: 0; border-collapse: collapse; font-size: 0.95rem; }
.table th, .table td { padding: 1rem; text-align: center; border-bottom: 1px solid var(--border); }
.table th { font-weight: 600; text-transform: uppercase; background: var(--light); }
.btn { padding: 0.5rem 1rem; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; }
.btn-primary { background: var(--primary); color: white; }
.btn-success { background: var(--success); color: white; }
.btn-danger { background: var(--danger); color: white; }
.btn-sm { padding: 0.35rem 0.7rem; font-size: 0.85rem; }
.modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px); padding: 1rem; overflow: auto; }
.modal.active { display: flex; }
.modal-content { background: white; border-radius: 16px; width: 90%; max-width: 600px; box-shadow: 0 25px 50px rgba(0,0,0,0.15); overflow: hidden; animation: slideUp 0.3s ease-out; margin: auto; }
@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.modal-header { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white; padding: 2rem; text-align: center; font-size: 1.3rem; font-weight: 600; }
.modal-body { padding: 2rem; max-height: 65vh; overflow-y: auto; }
.modal-body::-webkit-scrollbar {
    width: 6px;
}
.modal-body::-webkit-scrollbar-track {
    background: var(--light);
}
.modal-body::-webkit-scrollbar-thumb {
    background: var(--primary);
    border-radius: 3px;
}
.profile-section { text-align: center; margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 1px solid var(--border); }
.profile-picture { width: 120px; height: 120px; border-radius: 12px; object-fit: cover; border: 4px solid var(--primary); margin: 0 auto 1rem; box-shadow: 0 10px 25px rgba(102, 126, 234, 0.15); }
.profile-name { margin: 0; color: var(--dark); font-size: 1.2rem; }
.profile-email { margin: 0.5rem 0 0; color: var(--muted); font-size: 0.9rem; }
.user-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem; }
.user-info-item { display: flex; flex-direction: column; }
.user-info-label { font-size: 0.75rem; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem; }
.user-info-value { font-size: 0.95rem; color: var(--dark); font-weight: 500; }
.user-info-grid.full-width { grid-template-columns: 1fr; }
.modal-footer { display: flex; gap: 1rem; padding: 1.5rem 2rem; background: var(--light); justify-content: flex-end; border-top: 1px solid var(--border); }
.modal-footer .btn { padding: 0.75rem 1.5rem; font-weight: 600; transition: all 0.3s ease; }
.modal-footer .btn-success { background: linear-gradient(135deg, var(--success) 0%, #059669 100%); flex: 1; }
.modal-footer .btn-success:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3); }
.modal-footer .btn-danger { background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%); flex: 1; }
.modal-footer .btn-danger:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(239, 68, 68, 0.3); }
.modal-footer .btn-close { background: white; color: var(--dark); border: 1px solid var(--border); flex: 1; }
.modal-footer .btn-close:hover { background: var(--border); }
.text-center { text-align: center; }
.text-muted { color: var(--muted); }
@media (max-width: 1024px) {
    .container-fluid { padding: 1.5rem 1rem; }
    .card-header { padding: 1.25rem; }
    .card-body { padding: 1.25rem; }
    .table th, .table td { padding: 0.75rem; }
    .table { font-size: 0.9rem; }
}

@media (max-width: 768px) {
    .container-fluid { padding: 1rem; }
    .card-header { padding: 1rem; }
    .card-body { padding: 1rem; }
    .table th, .table td { padding: 0.6rem; }
    .table { font-size: 0.85rem; }
    .btn-sm { padding: 0.3rem 0.6rem; font-size: 0.8rem; }
    .modal-content { width: 95%; max-width: 95%; }
    .modal-header { padding: 1.5rem; font-size: 1.1rem; }
    .modal-body { padding: 1.5rem; max-height: 75vh; }
    .modal-footer { padding: 1rem; gap: 0.75rem; }
    .profile-picture { width: 100px; height: 100px; }
    .profile-name { font-size: 1.1rem; }
    .user-info-grid { gap: 1rem; }
    .user-info-label { font-size: 0.7rem; }
    .user-info-value { font-size: 0.9rem; }
}

@media (max-width: 640px) {
    .container-fluid { padding: 0.75rem; }
    h1.h3 { font-size: 1.3rem; margin-bottom: 1rem; }
    .card-header { padding: 0.75rem; }
    .card-body { padding: 0.75rem; }
    .table th, .table td { padding: 0.5rem; font-size: 0.75rem; }
    .table th { text-align: left; }
    .table td:first-child, .table th:first-child { text-align: left; }
    .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.75rem; }
    .modal-content { width: 98%; max-width: 100%; }
    .modal-header { padding: 1.25rem 1rem; font-size: 1rem; }
    .modal-body { padding: 1.25rem 1rem; max-height: 80vh; }
    .modal-footer { padding: 0.75rem; flex-direction: column; justify-content: stretch; }
    .modal-footer form { width: 100%; }
    .modal-footer .btn { width: 100%; padding: 0.65rem; font-size: 0.9rem; }
    .profile-section { margin-bottom: 1.5rem; padding-bottom: 1.5rem; }
    .profile-picture { width: 90px; height: 90px; }
    .profile-name { font-size: 1rem; }
    .profile-email { font-size: 0.85rem; }
    .user-info-grid { grid-template-columns: 1fr; gap: 0.75rem; margin-bottom: 1rem; }
    .user-info-label { font-size: 0.65rem; }
    .user-info-value { font-size: 0.85rem; }
}

@media (max-width: 480px) {
    .container-fluid { padding: 0.5rem; }
    h1.h3 { font-size: 1.1rem; margin-bottom: 0.75rem; }
    .card-header { padding: 0.5rem; }
    .card-body { padding: 0.5rem; }
    .table th, .table td { padding: 0.35rem; font-size: 0.7rem; }
    .btn-sm { padding: 0.2rem 0.4rem; font-size: 0.65rem; }
    .modal { padding: 0.5rem; }
    .modal-content { width: 100%; max-width: 100%; margin: 0; }
    .modal-header { padding: 1rem; font-size: 0.95rem; }
    .modal-body { padding: 1rem; max-height: 75vh; }
    .modal-footer { padding: 0.75rem; gap: 0.5rem; flex-direction: column; display: grid; grid-template-columns: 1fr 1fr; grid-template-rows: auto auto; }
    .modal-footer form { width: 100%; }
    .modal-footer #rejectForm { grid-column: 1; grid-row: 1; }
    .modal-footer .btn-close { grid-column: 2; grid-row: 1; }
    .modal-footer #approveForm { grid-column: 1 / -1; grid-row: 2; }
    .modal-footer .btn { width: 100%; padding: 0.65rem; }
    .profile-picture { width: 80px; height: 80px; border: 3px solid var(--primary); }
    .profile-name { font-size: 0.95rem; }
    .profile-email { font-size: 0.75rem; }
    .user-info-grid { grid-template-columns: 1fr; gap: 0.5rem; margin-bottom: 0.75rem; }
    .user-info-label { font-size: 0.6rem; }
    .user-info-value { font-size: 0.8rem; }
}
</style>

<div class="container-fluid">
    <h1 class="h3 text-dark mb-4">Pending Accounts</h1>

    <div class="card mb-4">
        <div class="card-header"><strong>Pending Users</strong></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Purok</th>
                            <th>Barangay</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="pendingAccountsBody">
                        <tr>
                            <td colspan="5" class="text-center text-muted">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- User Modal -->
<div class="modal" id="userModal">
    <div class="modal-content">
        <div class="modal-header">User Application Review</div>
        <div class="modal-body">
            <div class="profile-section">
                <img id="modalProfilePicture" class="profile-picture" src="" alt="Profile Picture">
                <h3 class="profile-name" id="modalFullName">-</h3>
                <p class="profile-email" id="modalEmail">-</p>
            </div>

            <div class="user-info-grid">
                <div class="user-info-item">
                    <span class="user-info-label">Gender</span>
                    <span class="user-info-value" id="modalGender">-</span>
                </div>
                <div class="user-info-item">
                    <span class="user-info-label">Age</span>
                    <span class="user-info-value" id="modalAge">-</span>
                </div>
            </div>

            <div class="user-info-grid">
                <div class="user-info-item">
                    <span class="user-info-label">Phone</span>
                    <span class="user-info-value" id="modalPhone">-</span>
                </div>
                <div class="user-info-item">
                    <span class="user-info-label">Family Number</span>
                    <span class="user-info-value" id="modalFamilyNumber">-</span>
                </div>
            </div>

            <div class="user-info-grid">
                <div class="user-info-item">
                    <span class="user-info-label">Purok</span>
                    <span class="user-info-value" id="modalPurok">-</span>
                </div>
                <div class="user-info-item">
                    <span class="user-info-label">Barangay</span>
                    <span class="user-info-value" id="modalBarangay">-</span>
                </div>
            </div>

            <div class="user-info-grid">
                <div class="user-info-item">
                    <span class="user-info-label">Municipality</span>
                    <span class="user-info-value" id="modalMunicipality">-</span>
                </div>
                <div class="user-info-item">
                    <span class="user-info-label">Province</span>
                    <span class="user-info-value" id="modalProvince">-</span>
                </div>
            </div>

            <div class="user-info-grid full-width">
                <div class="user-info-item">
                    <span class="user-info-label">Zipcode</span>
                    <span class="user-info-value" id="modalZipcode">-</span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <form id="rejectForm" method="post" style="margin: 0; flex: 1;">
                <button type="submit" class="btn btn-danger" style="width: 100%;">Reject</button>
            </form>
            <button type="button" class="btn btn-close" onclick="closeModal()">Cancel</button>
            <form id="approveForm" method="post" style="margin: 0; flex: 1;">
                <button type="submit" class="btn btn-success" style="width: 100%;">Approve</button>
            </form>
        </div>
    </div>
</div>

<script>
function fetchPendingAccounts() {
    fetch('<?= base_url("admin/pendingAccounts") ?>?ajax=1') 
        .then(res => res.json())
        .then(users => {
            const tbody = document.getElementById('pendingAccountsBody');
            tbody.innerHTML = '';

            if (!users || users.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No pending accounts.</td></tr>';
                return;
            }

            users.forEach(user => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${user.first_name || ''} ${user.last_name || ''}</td>
                    <td>${user.email || ''}</td>
                    <td>${user.purok || ''}</td>
                    <td>${user.barangay || ''}</td>
                    <td>
                        <button class="btn btn-primary btn-sm view-user-btn" data-user-id="${user.id}">View</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            attachViewEvents();
        })
        .catch(err => console.error('AJAX Error:', err));
}

function attachViewEvents() {
    document.querySelectorAll('.view-user-btn').forEach(btn => {
        btn.onclick = function() {
            const userId = this.dataset.userId;
            fetch(`<?= base_url("admin/getUser") ?>/${userId}`)
                .then(res => res.json())
                .then(user => {
                    if (!user) return;

                    document.getElementById('modalFullName').textContent = (user.first_name || '') + ' ' + (user.last_name || '');
                    document.getElementById('modalEmail').textContent = user.email || '';
                    document.getElementById('modalGender').textContent = user.gender || '-';
                    document.getElementById('modalAge').textContent = user.age || '-';
                    document.getElementById('modalFamilyNumber').textContent = user.family_number || '-';
                    document.getElementById('modalPhone').textContent = user.phone || '-';
                    document.getElementById('modalPurok').textContent = user.purok || '-';
                    document.getElementById('modalBarangay').textContent = user.barangay || '-';
                    document.getElementById('modalMunicipality').textContent = user.municipality || '-';
                    document.getElementById('modalProvince').textContent = user.province || '-';
                    document.getElementById('modalZipcode').textContent = user.zipcode || '-';
                    document.getElementById('modalProfilePicture').src = user.profile_picture || '<?= base_url("assets/default-profile.png") ?>';

                    document.getElementById('approveForm').action = '<?= base_url("admin/approve/") ?>' + userId;
                    document.getElementById('rejectForm').action = '<?= base_url("admin/reject/") ?>' + userId;

                    document.getElementById('userModal').classList.add('active');
                })
                .catch(err => console.error('Modal fetch error:', err));
        };
    });
}

function closeModal() {
    document.getElementById('userModal').classList.remove('active');
}

function handleActionForm(formId) {
    document.getElementById(formId).onsubmit = function(e) {
        e.preventDefault();
        fetch(this.action, { method: 'POST' })
            .then(() => {
                closeModal();
                fetchPendingAccounts();
            })
            .catch(err => console.error('Action error:', err));
    };
}

handleActionForm('approveForm');
handleActionForm('rejectForm');

document.getElementById('userModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

fetchPendingAccounts();
setInterval(fetchPendingAccounts, 3000);
</script>