<!-- 
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap');

:root{
    --bg: #f6f8fb;
    --card: #ffffff;
    --muted: #6b7280;
    --primary: #2563eb;
    --primary-600: #1e40af;
    --success: #10b981;
    --danger: #ef4444;
    --border: #e6eef8;
    --glass: rgba(255,255,255,0.6);
    --shadow: 0 10px 30px rgba(23,42,77,0.06);
    --radius: 12px;
    --max-width: 1100px;
    --font-sans: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial;
}

* { box-sizing: border-box; }
html,body { height:100%; margin:0; font-family: var(--font-sans); background: linear-gradient(180deg,#f7fbff 0%, #f2f6fb 100%); color:#111827; -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale; }
.container-fluid { max-width: var(--max-width); margin: 28px auto; padding: 20px; }

/* Header */
.header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:1rem;
    margin-bottom: 16px;
    flex-wrap:wrap;
}
.header h1 { margin:0; font-size:1.2rem; font-weight:700; color:#0f172a; }
.header .sub { color:var(--muted); font-size:0.9rem; }

/* Card */
.card { background: var(--card); border-radius: var(--radius); box-shadow: var(--shadow); overflow:hidden; border: 1px solid var(--border); }
.card-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:1rem;
    padding: 18px 20px;
    border-bottom: 1px solid var(--border);
    background: linear-gradient(180deg, rgba(255,255,255,0.5), var(--card));
}
.card-header .left { display:flex; gap:12px; align-items:center; }
.controls { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }

/* Search and simple count pill */
.search {
    background: #f3f6fb;
    padding:8px 10px;
    border-radius: 10px;
    display:flex;
    gap:8px;
    align-items:center;
    border: 1px solid transparent;
}
.search input { border: none; outline:none; background:transparent; width: 220px; font-size:0.95rem; color:#0f172a; font-family: var(--font-sans); }
.count-pill {
    background: linear-gradient(90deg,#eef2ff 0%, #eefcfa 100%);
    padding:6px 10px;
    border-radius:999px;
    font-weight:600;
    color:var(--primary-600);
    font-size:0.85rem;
    border:1px solid rgba(37,99,235,0.08);
}

/* Buttons */
.btn { display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius: 10px; font-weight:600; cursor:pointer; border:none; transition:all .12s ease; font-size:0.9rem; font-family: var(--font-sans); }
.btn:active { transform:translateY(1px); }
.btn-primary { background: linear-gradient(90deg,var(--primary) 0%, var(--primary-600) 100%); color:white; box-shadow: 0 6px 18px rgba(37,99,235,0.14); }
.btn-ghost { background: transparent; color:var(--primary-600); border: 1px solid rgba(37,99,235,0.08); }
.btn-sm { padding:6px 10px; border-radius:8px; font-size:0.85rem; }

/* Table */
.table-wrap { overflow:auto; }
.table { width:100%; border-collapse: collapse; font-size:0.95rem; min-width:720px; font-family: var(--font-sans); }
.table thead th { text-align:left; padding:12px 16px; font-weight:700; font-size:0.8rem; color:var(--muted); text-transform:uppercase; letter-spacing:0.6px; background:transparent; position:sticky; top:0; backdrop-filter:saturate(120%); z-index:1; }
.table tbody td { padding:14px 16px; border-top:1px solid var(--border); vertical-align:middle; color:#111827; }
.row-empty { text-align:center; color:var(--muted); padding:36px 0; }

/* Table rows */
.user-cell { display:flex; align-items:center; gap:12px; }
.avatar {
    width:44px; height:44px; border-radius:10px; object-fit:cover; flex:0 0 44px; box-shadow: 0 6px 14px rgba(2,6,23,0.06);
    border:2px solid #fff;
}
.meta { display:flex; flex-direction:column; }
.meta .name { font-weight:700; color:#0f172a; }
.meta .email { font-size:0.85rem; color:var(--muted); }

/* Action cell */
.actions { display:flex; gap:8px; justify-content:flex-end; }
.action-btn { padding:8px 10px; border-radius:8px; font-weight:600; border:none; cursor:pointer; }
.view-btn { background:linear-gradient(90deg,#eef2ff,#fff); color:var(--primary-600); border:1px solid rgba(37,99,235,0.08); }
.icon { width:16px; height:16px; display:inline-block; vertical-align:middle; }

/* Modal */
.modal { display:none; position:fixed; inset:0; align-items:center; justify-content:center; background: rgba(4,6,15,0.45); z-index:1000; padding:20px; }
.modal.active { display:flex; }
.modal-content { width:100%; max-width:720px; background:var(--card); border-radius:14px; overflow:hidden; box-shadow: 0 30px 60px rgba(2,6,23,0.18); }
.modal-header { padding:20px; background: linear-gradient(90deg,var(--primary), var(--primary-600)); color:white; font-weight:700; font-size:1.05rem; display:flex; align-items:center; justify-content:space-between; }
.modal-body { padding:22px; display:grid; gap:16px; grid-template-columns: 160px 1fr; align-items:start; }
.modal-left { text-align:center; padding-right:8px; }
.modal-left .profile-picture { width:120px; height:120px; border-radius:12px; object-fit:cover; border:4px solid rgba(255,255,255,0.08); box-shadow: 0 12px 30px rgba(16,24,40,0.08); }
.modal-right { padding-left:12px; }
.info-grid { display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-top:6px; }
.info-item { background:#f8fafc; padding:12px; border-radius:10px; border:1px solid rgba(15,23,42,0.03); }
.info-label { font-size:0.75rem; color:var(--muted); font-weight:700; text-transform:uppercase; letter-spacing:0.6px; }
.info-value { font-weight:700; margin-top:6px; color:#0f172a; }

/* Modal footer */
.modal-footer { display:flex; gap:12px; padding:16px 20px; background:#fbfdff; border-top:1px solid var(--border); justify-content:flex-end; }
.btn-success { background: linear-gradient(90deg,var(--success), #059669); color:white; }
.btn-danger { background: linear-gradient(90deg, var(--danger), #dc2626); color:white; }

/* Small helpers */
.text-muted { color:var(--muted); }
.center { text-align:center; }

/* Responsive: transform table into stacked cards on narrow viewports */
@media (max-width: 860px) {
    .search input { width: 140px; }
    .table { min-width: 0; border: none; }
    .table thead { display:none; }
    .table tbody, .table tr, .table td { display:block; width:100%; }
    .table tbody tr { margin: 0 0 12px; background: var(--card); border-radius:10px; padding: 12px; box-shadow: var(--shadow); border:1px solid var(--border); }
    .table tbody td { padding: 8px 10px; border-top: none; display:flex; justify-content:space-between; align-items:center; }
    .table tbody td[data-label]::before {
        content: attr(data-label);
        display:block;
        font-weight:700;
        color:var(--muted);
        margin-right:8px;
        flex: 0 0 35%;
        text-align:left;
        font-size:0.85rem;
    }
    .user-cell { gap:10px; align-items:center; }
    .avatar { width:48px; height:48px; border-radius:8px; flex:0 0 48px; }
    .meta .name { font-size:0.98rem; }
    .meta .email { font-size:0.83rem; }
    .actions { justify-content:flex-end; gap:8px; margin-left:8px; }
    .view-btn { padding:6px 10px; font-size:0.85rem; }
    .card-body .table-wrap { padding:12px; }
}

/* Extra small tweaks */
@media (max-width: 420px) {
    .card-header { padding: 12px; }
    .header { gap:8px; }
    .search input { width: 110px; }
    .table tbody td[data-label]::before { flex: 0 0 40%; font-size:0.82rem; }
}
</style>

<div class="container-fluid">
    <div class="header">
        <div>
            <h1>Pending Accounts</h1>
            <div class="sub">Review new user applications awaiting approval</div>
        </div>

        <div class="controls">
            <div class="search" role="search" aria-label="Search pending accounts">
                <svg class="icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 21l-4.35-4.35" stroke="#6b7280" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="11" cy="11" r="6" stroke="#6b7280" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <input id="pendingSearch" placeholder="Search name or email" />
            </div>

            <div class="count-pill" id="pendingCount">— Pending</div>

            <button id="refreshPendingBtn" class="btn btn-primary btn-sm" title="Refresh list">
                <svg class="icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 12a9 9 0 10-2.59 6.01L21 21" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg> Refresh
            </button>
        </div>
    </div>

    <div class="card" role="region" aria-labelledby="pendingHeader">
        <div class="card-header" id="pendingHeader">
            <strong>Pending Users</strong>
            <div style="display:flex; gap:10px; align-items:center;">
                <span id="pendingStatusText" class="text-muted" style="font-size:0.9rem;">Idle</span>
            </div>
        </div>

        <div class="card-body" style="padding:0;">
            <div class="table-wrap">
                <table class="table" aria-describedby="pendingHeader">
                    <thead>
                        <tr>
                            <th style="width:45%;">Full Name</th>
                            <th style="width:25%;">Email</th>
                            <th style="width:10%;">Purok</th>
                            <th style="width:10%;">Barangay</th>
                            <th style="width:10%; text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="pendingAccountsBody">
                        <tr>
                            <td colspan="5" class="row-empty">
                                <div>
                                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" style="margin-bottom:8px;">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M20 21v-1a4 4 0 00-4-4H8a4 4 0 00-4 4v1" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <div class="text-muted">Loading...</div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

 User Modal 
<div class="modal" id="userModal" role="dialog" aria-modal="true" aria-labelledby="userModalTitle">
    <div class="modal-content" role="document">
        <div class="modal-header">
            <div id="userModalTitle">User Application Review</div>
            <button onclick="closeModal()" class="btn btn-ghost btn-sm" aria-label="Close">Close</button>
        </div>

        <div class="modal-body">
            <div class="modal-left">
                <img id="modalProfilePicture" class="profile-picture" src="" alt="Profile Picture">
                <div style="margin-top:12px;">
                    <div class="meta" style="align-items:center;">
                        <div class="name" id="modalFullName">-</div>
                        <div class="email text-muted" id="modalEmail">-</div>
                    </div>
                </div>
            </div>

            <div class="modal-right">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Gender</div>
                        <div class="info-value" id="modalGender">-</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Age</div>
                        <div class="info-value" id="modalAge">-</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Phone</div>
                        <div class="info-value" id="modalPhone">-</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Family Number</div>
                        <div class="info-value" id="modalFamilyNumber">-</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Purok</div>
                        <div class="info-value" id="modalPurok">-</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Barangay</div>
                        <div class="info-value" id="modalBarangay">-</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Municipality</div>
                        <div class="info-value" id="modalMunicipality">-</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Province</div>
                        <div class="info-value" id="modalProvince">-</div>
                    </div>
                </div>

                <div style="margin-top:12px;">
                    <div class="info-item" style="background:transparent; border:none; padding:0;">
                        <div class="info-label">Zipcode</div>
                        <div class="info-value" id="modalZipcode">-</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-danger" id="rejectBtn">Reject</button>
            <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button>
            <button type="button" class="btn btn-success" id="approveBtn">Approve</button>
        </div>
    </div>
</div>

<script>
var csrfName = '<?= csrf_token() ?>';
var csrfHash = '<?= csrf_hash() ?>';
window.currentUserId = window.currentUserId || null;

function setCountLabel(count) {
    const el = document.getElementById('pendingCount');
    if (!el) return;
    el.textContent = (count > 0 ? count + ' Pending' : 'No Pending');
}

function renderLoadingState() {
    const tbody = document.getElementById('pendingAccountsBody');
    if (!tbody) return;
    tbody.innerHTML = '<tr><td colspan="5" class="row-empty"><div><svg width="32" height="32" viewBox="0 0 24 24" fill="none" style="margin-bottom:8px;"><path d="M12 2v4" stroke="#9ca3af" stroke-width="1.6" stroke-linecap="round"/><path d="M12 18v4" stroke="#9ca3af" stroke-width="1.6" stroke-linecap="round"/><path d="M4.9 4.9l2.8 2.8" stroke="#9ca3af" stroke-width="1.6" stroke-linecap="round"/><path d="M16.3 16.3l2.8 2.8" stroke="#9ca3af" stroke-width="1.6" stroke-linecap="round"/></svg><div class="text-muted">Loading...</div></div></td></tr>';
}

function fetchPendingAccounts() {
    const tbody = document.getElementById('pendingAccountsBody');
    if (!tbody) { stopPendingInterval(); return; }
    renderLoadingState();
    fetch('<?= base_url("admin/pendingAccounts") ?>?ajax=1')
        .then(res => res.json())
        .then(users => {
            if (!tbody) { stopPendingInterval(); return; }
            while (tbody.firstChild) tbody.removeChild(tbody.firstChild);

            if (!users || users.length === 0) {
                const tr = document.createElement('tr');
                const td = document.createElement('td');
                td.colSpan = 5;
                td.className = 'row-empty';
                td.innerHTML = '<div><svg width="36" height="36" viewBox="0 0 24 24" fill="none" style="margin-bottom:8px;"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M20 21v-1a4 4 0 00-4-4H8a4 4 0 00-4 4v1" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg><div class="text-muted">No pending accounts.</div></div>';
                tr.appendChild(td);
                tbody.appendChild(tr);
                updatePendingStatus('Idle');
                setCountLabel(0);
                return;
            }

            users.forEach(user => {
                const tr = document.createElement('tr');

                const tdName = document.createElement('td');
                tdName.setAttribute('data-label', 'Full Name');
                const nameBox = document.createElement('div');
                nameBox.className = 'user-cell';
                const img = document.createElement('img');
                img.className = 'avatar';
                img.src = user.profile_picture || '<?= base_url("assets/default-profile.png") ?>';
                img.alt = (user.first_name || '') + ' avatar';
                const meta = document.createElement('div');
                meta.className = 'meta';
                const name = document.createElement('div');
                name.className = 'name';
                name.textContent = `${user.first_name || ''} ${user.last_name || ''}`.trim();
                const email = document.createElement('div');
                email.className = 'email text-muted';
                email.textContent = user.email || '';
                meta.appendChild(name);
                meta.appendChild(email);
                nameBox.appendChild(img);
                nameBox.appendChild(meta);
                tdName.appendChild(nameBox);

                const tdEmail = document.createElement('td');
                tdEmail.setAttribute('data-label', 'Email');
                tdEmail.textContent = user.email || '';

                const tdPurok = document.createElement('td');
                tdPurok.setAttribute('data-label', 'Purok');
                tdPurok.textContent = user.purok || '';

                const tdBarangay = document.createElement('td');
                tdBarangay.setAttribute('data-label', 'Barangay');
                tdBarangay.textContent = user.barangay || '';

                const tdAction = document.createElement('td');
                tdAction.setAttribute('data-label', 'Actions');
                tdAction.style.textAlign = 'right';
                const btn = document.createElement('button');
                btn.className = 'btn view-btn btn-sm view-user-btn';
                btn.dataset.userId = user.id;
                btn.title = 'View application';
                btn.innerHTML = '<svg class="icon" viewBox="0 0 24 24" fill="none" style="vertical-align:middle;"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z" stroke="#1e40af" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="#1e40af" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg> View';
                tdAction.appendChild(btn);

                tr.appendChild(tdName);
                tr.appendChild(tdEmail);
                tr.appendChild(tdPurok);
                tr.appendChild(tdBarangay);
                tr.appendChild(tdAction);

                tbody.appendChild(tr);
            });

            attachViewEvents();
            updatePendingStatus('Idle');
            setCountLabel(users.length);
        })
        .catch(err => {
            console.error('AJAX Error:', err);
            updatePendingStatus('Error');
            const tbody = document.getElementById('pendingAccountsBody');
            if (!tbody) return;
            while (tbody.firstChild) tbody.removeChild(tbody.firstChild);
            const tr = document.createElement('tr');
            const td = document.createElement('td');
            td.colSpan = 5;
            td.className = 'row-empty';
            td.innerHTML = '<div class="text-muted">Unable to load data. Click refresh to retry.</div>';
            tr.appendChild(td);
            tbody.appendChild(tr);
            setCountLabel(0);
        });
}

function attachViewEvents() {
    document.querySelectorAll('.view-user-btn').forEach(btn => {
        btn.onclick = function() {
            window.currentUserId = this.dataset.userId;
            fetch(`<?= base_url("admin/getUser") ?>/${window.currentUserId}`)
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
                    document.getElementById('userModal').classList.add('active');
                })
                .catch(err => console.error('Modal fetch error:', err));
        };
    });
}

function closeModal() {
    document.getElementById('userModal').classList.remove('active');
}

function _createSpinnerSvg() {
    return '<svg width="16" height="16" viewBox="0 0 50 50" style="vertical-align:middle; margin-right:8px;"><path fill="#ffffff" d="M43.935,25.145c0-10.318-8.364-18.682-18.682-18.682c-10.318,0-18.682,8.364-18.682,18.682h4.068 c0-8.066,6.548-14.614,14.614-14.614c8.066,0,14.614,6.548,14.614,14.614H43.935z"><animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.8s" repeatCount="indefinite"/></path></svg>';
}

function showButtonLoader(btn, text = 'Processing...') {
    if (!btn) return;
    btn.dataset.origHtml = btn.dataset.origHtml || btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = _createSpinnerSvg() + `<span style="vertical-align:middle;">${text}</span>`;
}

function restoreButton(btn) {
    if (!btn) return;
    btn.disabled = false;
    if (btn.dataset.origHtml) {
        btn.innerHTML = btn.dataset.origHtml;
    }
}

async function postAction(url) {
    try {
        const formData = new URLSearchParams();
        formData.append(csrfName, csrfHash);

        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });

        const newCsrfHash = res.headers.get('X-CSRF-Token');
        if (newCsrfHash) csrfHash = newCsrfHash;

        let data = null;
        try { data = await res.json(); } catch (e) { /* no JSON */ }

        fetchPendingAccounts();
        closeModal();

        return { ok: res.ok, status: res.status, data };
    } catch (err) {
        console.error('Action error:', err);
        return { ok: false, error: err };
    }
}

document.getElementById('approveBtn').onclick = async () => {
    if (!window.currentUserId) return;
    const btn = document.getElementById('approveBtn');
    showButtonLoader(btn, 'Approving...');
    updatePendingStatus('Approving...');

    const result = await postAction('<?= base_url("admin/approve/") ?>' + window.currentUserId);

    restoreButton(btn);

    if (result && result.ok) {
        updatePendingStatus('Approved');
    } else {
        updatePendingStatus('Error');
        console.error('Approve failed:', result);
        alert('Failed to approve account. Check console for details.');
    }
};
document.getElementById('rejectBtn').onclick = () => {
    if (!window.currentUserId) return;
    showButtonLoader(document.getElementById('rejectBtn'));
    postAction('<?= base_url("admin/reject/") ?>' + window.currentUserId);
};

document.getElementById('userModal').addEventListener('click', e => {
    if (e.target === e.currentTarget) closeModal();
});

function updatePendingStatus(text) {
    const el = document.getElementById('pendingStatusText');
    if (el) el.textContent = text;
}

function startPendingInterval() {
    if (window.__pendingAccountsInterval) {
        clearInterval(window.__pendingAccountsInterval);
        window.__pendingAccountsInterval = null;
    }
    window.__pendingAccountsInterval = setInterval(() => {
        const tbody = document.getElementById('pendingAccountsBody');
        if (!tbody) { stopPendingInterval(); return; }
        if (document.visibilityState === 'visible') {
            updatePendingStatus('Refreshing...');
            fetchPendingAccounts();
        }
    }, 30000);
    window.__pendingAccountsStop = stopPendingInterval;
}

function stopPendingInterval() {
    if (window.__pendingAccountsInterval) {
        clearInterval(window.__pendingAccountsInterval);
        window.__pendingAccountsInterval = null;
    }
}

document.getElementById('refreshPendingBtn').addEventListener('click', () => {
    updatePendingStatus('Refreshing...');
    fetchPendingAccounts();
});

document.getElementById('pendingSearch').addEventListener('input', function() {
    const q = this.value.trim().toLowerCase();
    const rows = document.querySelectorAll('#pendingAccountsBody tr');
    if (!q) {
        rows.forEach(r => r.style.display = '');
        return;
    }
    rows.forEach(r => {
        const name = (r.querySelector('.name') || { textContent:'' }).textContent.toLowerCase();
        const email = (r.querySelector('.email') || { textContent:'' }).textContent.toLowerCase();
        const match = name.includes(q) || email.includes(q);
        r.style.display = match ? '' : 'none';
    });
});

document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
        updatePendingStatus('Refreshing...');
        fetchPendingAccounts();
    }
});

// Initial fetch then start interval
updatePendingStatus('Loading...');
fetchPendingAccounts();
startPendingInterval();
window.addEventListener('beforeunload', stopPendingInterval);
</script> -->