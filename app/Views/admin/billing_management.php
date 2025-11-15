<style>
        /* === BASE STYLES AND VARIABLES === */
        :root { 
            --ubill-primary: #3b82f6; 
            --ubill-primary-dark: #2563eb; 
            --ubill-success: #10b981; 
            --ubill-warning: #f59e0b; 
            --ubill-danger: #ef4444; 
            --ubill-info: #06b6d4; 
            --ubill-border: #e5e7eb; 
            --ubill-dark: #1f2937; 
            --ubill-light: #f9fafb; 
            --ubill-muted: #6b7280; 
            --ubill-input-height: 2.8rem; 
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Poppins', sans-serif; 
            background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%); 
            min-height: 100vh; 
            padding: 2rem 1rem; 
        }
        .ubill-wrapper {
            max-width: 1200px;
            margin: 0 auto;
        }
        .ubill-header {
            background: linear-gradient(135deg, var(--ubill-primary) 0%, var(--ubill-primary-dark) 100%);
            color: white;
            padding: 2rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            box-shadow: 0 8px 24px rgba(59,130,246,0.2);
        }
        .ubill-header-title { 
            font-size: 2.5rem; 
            font-weight: 700; 
            display: flex; 
            align-items: center; 
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .ubill-nav-tabs {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 2px solid rgba(255,255,255,0.2);
        }
        .ubill-tab-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .ubill-tab-btn.active {
            background: white;
            color: var(--ubill-primary);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .ubill-tab-btn.inactive {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 2px solid rgba(255,255,255,0.3);
        }
        .ubill-tab-btn:hover {
            transform: translateY(-2px);
        }
        .ubill-content-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .ubill-section { display: none; }
        .ubill-section.active { display: block; animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        
        /* === FORM STYLES === */
        .ubill-form-title { 
            font-size: 1.5rem; 
            font-weight: 700; 
            color: var(--ubill-dark); 
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .ubill-form-subtitle {
            color: var(--ubill-muted);
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        .ubill-form-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
            gap: 1.5rem; 
            margin-bottom: 1.5rem; 
        }
        .ubill-form-group { display: flex; flex-direction: column; }
        .ubill-form-label { 
            font-weight: 600; 
            color: var(--ubill-dark); 
            margin-bottom: 0.6rem; 
            font-size: 0.9rem;
        }
        .ubill-form-input, .ubill-form-select { 
            padding: 0.75rem 1rem; 
            height: var(--ubill-input-height); 
            border: 2px solid var(--ubill-border); 
            border-radius: 10px; 
            font-size: 0.95rem; 
            font-family: 'Poppins', sans-serif; 
            background: var(--ubill-light); 
            transition: all 0.3s; 
            -webkit-appearance: none; 
            -moz-appearance: none; 
            appearance: none; 
        }
        .ubill-form-select { 
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%236b7280'%3E%3Cpath fill-rule='evenodd' d='M5.23 7.21a.75.75 0 011.06.02L10 10.97l3.71-3.74a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.23 8.29a.75.75 0 01.02-1.06z' clip-rule='evenodd'/%3E%3C/svg%3E"); 
            background-repeat: no-repeat; 
            background-position: right 1rem center; 
            background-size: 1.2rem;
            padding-right: 2.5rem;
        }
        .ubill-form-input:focus, .ubill-form-select:focus { 
            outline: none; 
            border-color: var(--ubill-primary); 
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); 
            background: white; 
        }
        .ubill-form-actions { 
            display: flex; 
            gap: 1rem; 
            margin-top: 2rem; 
            flex-wrap: wrap;
        }
        
        /* === BUTTON STYLES === */
        .ubill-btn { 
            padding: 0.9rem 1.8rem; 
            border: none; 
            border-radius: 10px; 
            font-weight: 600; 
            cursor: pointer; 
            transition: all 0.3s; 
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .ubill-btn-primary { 
            background: linear-gradient(135deg, var(--ubill-primary) 0%, var(--ubill-primary-dark) 100%); 
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        }
        .ubill-btn-primary:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.3); 
        }
        .ubill-btn-success { 
            background: linear-gradient(135deg, var(--ubill-success) 0%, #0d9488 100%); 
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }
        .ubill-btn-success:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3); 
        }
        .ubill-btn-warning { 
            background: linear-gradient(135deg, var(--ubill-warning) 0%, #d97706 100%); 
            color: white;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
        }
        .ubill-btn-warning:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 6px 16px rgba(245, 158, 11, 0.3); 
        }
        .ubill-btn-secondary { 
            background: white; 
            color: var(--ubill-dark); 
            border: 2px solid var(--ubill-border);
        }
        .ubill-btn-secondary:hover { 
            background: var(--ubill-light); 
            border-color: var(--ubill-primary);
            color: var(--ubill-primary);
        }
        
        /* === FILTER BAR === */
        .ubill-filter-bar {
            background: linear-gradient(135deg, rgba(59,130,246,0.05) 0%, rgba(59,130,246,0.02) 100%);
            border: 2px solid var(--ubill-border);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .ubill-filter-title {
            font-weight: 700;
            color: var(--ubill-dark);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.1rem;
        }
        .ubill-filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .ubill-filter-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        /* === TABLE STYLES === */
        .ubill-table-container { 
            background: white; 
            border-radius: 16px; 
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); 
            border: 2px solid var(--ubill-border); 
            overflow: hidden; 
            margin-bottom: 2rem; 
        }
        .ubill-table-header { 
            padding: 1.5rem; 
            background: linear-gradient(135deg, var(--ubill-light) 0%, white 100%);
            border-bottom: 2px solid var(--ubill-border); 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .ubill-table-title { 
            font-weight: 700; 
            color: var(--ubill-dark); 
            font-size: 1.1rem; 
        }
        .ubill-table-wrapper { overflow-x: auto; }
        .ubill-table { width: 100%; border-collapse: collapse; }
        .ubill-table th { 
            padding: 1.2rem 1.5rem; 
            text-align: left; 
            font-weight: 700; 
            color: white;
            font-size: 0.85rem; 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
            border-bottom: 2px solid var(--ubill-border); 
            background: linear-gradient(135deg, var(--ubill-primary) 0%, var(--ubill-primary-dark) 100%);
        }
        .ubill-table td { 
            padding: 1.2rem 1.5rem; 
            border-bottom: 1px solid var(--ubill-border); 
            font-size: 0.9rem; 
        } 
        .ubill-table tbody tr:hover { 
            background: rgba(59, 130, 246, 0.03); 
        }
        .ubill-avatar { 
            width: 38px; 
            height: 38px; 
            border-radius: 50%; 
            background: linear-gradient(135deg, var(--ubill-primary) 0%, var(--ubill-primary-dark) 100%); 
            color: white; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-weight: 700; 
            font-size: 0.9rem;
        }
        .ubill-no-data { 
            text-align: center; 
            padding: 4rem 2rem; 
            color: var(--ubill-muted); 
        }
        .ubill-no-data-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }
        
        /* === BADGES === */
        .ubill-status-badge { 
            display: inline-block; 
            padding: 0.5rem 1rem; 
            border-radius: 20px; 
            font-size: 0.8rem; 
            font-weight: 600;
        }
        .ubill-status-active { 
            background: rgba(16, 185, 129, 0.15); 
            color: #059669; 
            border: 1.5px solid rgba(16, 185, 129, 0.3); 
        }
        .ubill-billing-badge { 
            padding: 0.5rem 1rem; 
            border-radius: 8px; 
            font-size: 0.9rem; 
            font-weight: 700;
        }
        .ubill-billing-30 { background: rgba(59, 130, 246, 0.15); color: #1e40af; } 
        .ubill-billing-48 { background: rgba(139, 92, 246, 0.15); color: #6d28d9; } 
        .ubill-billing-60 { background: rgba(34, 197, 94, 0.15); color: #15803d; } 
        
        /* === SUMMARY CARDS === */
        .ubill-summary-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); 
            gap: 1.5rem; 
            margin-bottom: 2rem;
        }
        .ubill-summary-card { 
            background: linear-gradient(135deg, var(--ubill-light) 0%, white 100%); 
            padding: 1.5rem; 
            border-radius: 12px; 
            text-align: center; 
            border: 2px solid var(--ubill-border); 
            transition: all 0.3s; 
        }
        .ubill-summary-card:hover { 
            transform: translateY(-4px); 
            box-shadow: 0 12px 24px rgba(59, 130, 246, 0.1); 
            border-color: var(--ubill-primary);
        }
        .ubill-summary-value { 
            font-size: 2.5rem; 
            font-weight: 700; 
            color: var(--ubill-primary); 
            line-height: 1;
        }
        .ubill-summary-label { 
            font-size: 0.85rem; 
            color: var(--ubill-muted); 
            margin-top: 0.75rem; 
            font-weight: 600; 
        }
        
        /* === SUCCESS MESSAGE === */
        .ubill-success-message { 
            background: rgba(16, 185, 129, 0.1);
            border-left: 4px solid var(--ubill-success);
            color: #059669; 
            padding: 1rem 1.5rem; 
            border-radius: 8px; 
            margin-bottom: 1.5rem; 
            display: none;
            font-weight: 600;
        }
        .ubill-success-message.show { 
            display: block; 
            animation: ubill-slideDown 0.3s ease; 
        }
        .ubill-success-message.danger {
            background: rgba(239, 68, 68, 0.1);
            border-left-color: var(--ubill-danger);
            color: #b91c1c;
        }
        @keyframes ubill-slideDown { 
            from { opacity: 0; transform: translateY(-10px); } 
            to { opacity: 1; transform: translateY(0); } 
        }
        
        /* === RESPONSIVENESS === */
        @media (max-width: 768px) {
            .ubill-header-title { font-size: 1.8rem; }
            .ubill-content-card { padding: 1.5rem; }
            .ubill-form-grid { grid-template-columns: 1fr; }
            .ubill-filter-grid { grid-template-columns: 1fr; }
            .ubill-nav-tabs { gap: 0.75rem; }
            .ubill-tab-btn { padding: 0.6rem 1rem; font-size: 0.85rem; }
            .ubill-table th { padding: 0.75rem 1rem; }
            .ubill-table td { padding: 0.75rem 1rem; }
            .ubill-summary-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 480px) {
            body { padding: 1rem; }
            .ubill-header { padding: 1.5rem; }
            .ubill-header-title { font-size: 1.5rem; gap: 0.5rem; }
            .ubill-content-card { padding: 1rem; }
            .ubill-nav-tabs { margin-top: 1rem; }
            .ubill-summary-grid { grid-template-columns: 1fr; }
            .ubill-filter-grid { grid-template-columns: 1fr; }
            .ubill-filter-actions { flex-direction: column; }
            .ubill-btn { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>
    <div class="ubill-wrapper">
        <div class="ubill-header">
            <div class="ubill-header-title">
                <span>⚙️</span>
                <span>Billing Management Console</span>
            </div>
            <div class="ubill-nav-tabs">
                <button class="ubill-tab-btn active" onclick="ubillToggleView('ubill-billings')">
                    <span>📊</span>
                    <span>Billing Summary</span>
                </button>
                <button class="ubill-tab-btn inactive" onclick="ubillToggleView('ubill-manual-billing')">
                    <span>✍️</span>
                    <span>Manual Billing</span>
                </button>
            </div>
        </div>

        <div id="ubill-billings" class="ubill-section active">
            <div class="ubill-content-card">
                <div class="ubill-form-title">🚀 Synchronize Automatic Billings</div>
                <div class="ubill-form-subtitle">
                    Calculate and update automatic monthly billing amounts for all users based on their household type. Manual bills are preserved.
                </div>
                <div class="ubill-success-message" id="ubill-billingSuccess"></div>
                
                <div class="ubill-form-grid" style="grid-template-columns: 1fr auto;">
                    <div class="ubill-form-group">
                        <label class="ubill-form-label">Current Billing Month</label>
                        <input type="month" id="ubill-billingMonth" readonly class="ubill-form-input" style="background: var(--ubill-light); cursor: not-allowed;">
                    </div>
                </div>
                
                <div class="ubill-form-actions">
                    <button onclick="ubillSetupAllBillings()" class="ubill-btn ubill-btn-warning">
                        <span>⚡</span>
                        <span>Synchronize Now</span>
                    </button>
                </div>
            </div>

            <div class="ubill-content-card">
                <div class="ubill-form-title">📈 Billing Statistics (Latest Month)</div>
                <div class="ubill-summary-grid">
                    <div class="ubill-summary-card">
                        <div class="ubill-summary-value" id="ubill-summaryFamily">0</div>
                        <div class="ubill-summary-label">👨‍👩‍👧‍👦 Family (₱60)</div>
                    </div>
                    <div class="ubill-summary-card">
                        <div class="ubill-summary-value" id="ubill-summarySolo">0</div>
                        <div class="ubill-summary-label">🧑 Solo (₱30)</div>
                    </div>
                    <div class="ubill-summary-card">
                        <div class="ubill-summary-value" id="ubill-summarySenior">0</div>
                        <div class="ubill-summary-label">👴 Senior (₱48)</div>
                    </div>
                    <div class="ubill-summary-card">
                        <div class="ubill-summary-value" id="ubill-summaryTotal">0</div>
                        <div class="ubill-summary-label">📋 Total Users</div>
                    </div>
                </div>
            </div>

            <div class="ubill-filter-bar">
                <div class="ubill-filter-title">
                    <span>🔍</span>
                    <span>Filter & Search Billings</span>
                </div>
                <div class="ubill-filter-grid">
                    <div class="ubill-form-group">
                        <label class="ubill-form-label">📅 By Month</label>
                        <select id="filter-month" class="ubill-form-select">
                            <option value="">All Months</option>
                        </select>
                    </div>
                    <div class="ubill-form-group">
                        <label class="ubill-form-label">🏘️ By Purok</label>
                        <select id="filter-purok" class="ubill-form-select">
                            <option value="">All Puroks</option>
                        </select>
                    </div>
                    <div class="ubill-form-group">
                        <label class="ubill-form-label">🔎 Search</label>
                        <input type="text" id="search-term" class="ubill-form-input" placeholder="Name, email, address...">
                    </div>
                </div>
                <div class="ubill-filter-actions">
                    <button onclick="ubillRenderFilteredTable()" class="ubill-btn ubill-btn-primary">
                        <span>🔍</span>
                        <span>Apply Filter</span>
                    </button>
                    <button onclick="ubillResetFilters()" class="ubill-btn ubill-btn-secondary">
                        <span>↻</span>
                        <span>Reset</span>
                    </button>
                    <button onclick="ubillPrintTable()" class="ubill-btn ubill-btn-secondary">
                        <span>🖨️</span>
                        <span>Print</span>
                    </button>
                </div>
            </div>

            <div class="ubill-table-container">
                <div class="ubill-table-header">
                    <div class="ubill-table-title">💼 Detailed Billing Records</div>
                </div>
                <div class="ubill-table-wrapper">
                    <table class="ubill-table">
                        <thead>
                            <tr>
                                <th>Bill No</th>
                                <th>User Name</th>
                                <th>Email</th>
                                <th>Amount Due</th>
                                <th>Status</th>
                                <th>Billing Month</th>
                            </tr>
                        </thead>
                        <tbody id="ubill-billingTable">
                            <tr>
                                <td colspan="6" class="ubill-no-data">
                                    <div class="ubill-no-data-icon">📭</div>
                                    <div>Loading billing data...</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="ubill-manual-billing" class="ubill-section">
            <div class="ubill-content-card">
                <div class="ubill-form-title">✍️ Create or Override Billing</div>
                <div class="ubill-form-subtitle">
                    Manually set a billing amount for a specific user and month. This will override automatic billings if they exist.
                </div>
                <div class="ubill-success-message" id="ubill-manualSuccess"></div>

                <div class="ubill-form-grid">
                    <div class="ubill-form-group">
                        <label class="ubill-form-label">👤 Select User</label>
                        <select id="manual-user-select" class="ubill-form-select" required>
                            <option value="">Choose a user...</option>
                        </select>
                    </div>
                    <div class="ubill-form-group">
                        <label class="ubill-form-label">📅 Billing Month</label>
                        <input type="month" id="manual-billing-month" class="ubill-form-input" required>
                    </div>
                    <div class="ubill-form-group">
                        <label class="ubill-form-label">💰 Amount (₱)</label>
                        <input type="number" id="manual-billing-amount" class="ubill-form-input" placeholder="e.g., 500.00" step="0.01" min="1" required>
                    </div>
                </div>

                <div class="ubill-form-actions">
                    <button onclick="ubillProcessManualBill()" class="ubill-btn ubill-btn-success">
                        <span>✅</span>
                        <span>Process Manual Billing</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
<script>
// Avoid redeclaring globals on AJAX reloads
window.APP_BASE_URL = window.APP_BASE_URL || "<?= base_url() ?>";
const billingTableBody = document.getElementById('ubill-billingTable');

// === Global Variables ===
let ubillUsers = [];
let currentMonth = new Date().toISOString().slice(0, 7);

// === Initialize Billing Page ===
function initBillingPage() {
    // Initialize filter options
    populateMonthFilter();
    populatePurokFilter();
    
    // Set up other components
    ubillSetCurrentBillingMonth();
    ubillPopulateUserSelect();
    
    // Set current month as default and load billings
    document.getElementById('filter-month').value = currentMonth;
    loadBillings();
}

// === Utility Functions ===
function showLoading() {
    if (billingTableBody) {
        billingTableBody.innerHTML = `
            <tr>
                <td colspan="6" class="ubill-no-data">
                    <div class="ubill-no-data-icon">⏳</div>
                    <div>Loading billing data...</div>
                </td>
            </tr>
        `;
    }
}

function hideLoading() {
    // Loading handled by table content
}

function ubillShowMessage(elementId, message, isSuccess = true) {
    const el = document.getElementById(elementId);
    if (!el) return;
    
    el.textContent = message;
    el.classList.remove('show', 'danger');
    if (message) {
        el.classList.add('show');
        if (!isSuccess) el.classList.add('danger');
        setTimeout(() => el.classList.remove('show'), 5000);
    }
}

function ubillFormatMonth(monthStr) {
    if (!monthStr) return 'N/A';
    const date = new Date(monthStr + '-01');
    return date.toLocaleString('en-US', { year: 'numeric', month: 'long' });
}

// === Load Billings (Main Function) ===
function loadBillings(filters = {}) {
    showLoading();
    
    // Get filter values - default to current month
    const month = filters.month !== undefined ? filters.month : (document.getElementById('filter-month')?.value || currentMonth);
    const purok = filters.purok || (document.getElementById('filter-purok')?.value || '');
    const search = filters.search || (document.getElementById('search-term')?.value || '');
    const page = filters.page || 1;

    const url = `<?= site_url('admin/getAllBillings') ?>?month=${month}&purok=${encodeURIComponent(purok)}&search=${encodeURIComponent(search)}&page=${page}`;
    console.log('Loading billings from:', url);

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderBillingTable(data.billings || []);
                
                // Update table title
                const tableTitle = document.querySelector('.ubill-table-title');
                if (tableTitle) {
                    let titleText = '💼 Billing Records';
                    if (month) {
                        titleText += ` - ${ubillFormatMonth(month)}`;
                    }
                    if (purok) {
                        titleText += ` - Purok ${purok}`;
                    }
                    tableTitle.textContent = titleText;
                }
                
                // Update summary message
                let filterDescription = '';
                if (month) filterDescription += ubillFormatMonth(month);
                if (purok) filterDescription += (filterDescription ? ' in Purok ' + purok : 'Purok ' + purok);
                if (!filterDescription) filterDescription = 'all records';
                
                const recordCount = data.billings?.length || 0;
                if (recordCount > 0) {
                    ubillShowMessage('ubill-billingSuccess', `✅ Loaded ${recordCount} billing records for ${filterDescription}.`, true);
                }
            } else {
                console.error('Backend error:', data.message);
                ubillShowMessage('ubill-billingSuccess', 'Error: ' + (data.message || 'Failed to load billings'), false);
                renderBillingTable([]);
            }
            hideLoading();
        })
        .catch(err => {
            console.error('Fetch error:', err);
            ubillShowMessage('ubill-billingSuccess', '❌ Failed to load billings. Check console.', false);
            if (billingTableBody) {
                billingTableBody.innerHTML = `<tr><td colspan="6">❌ Failed to load billings. Check console.</td></tr>`;
            }
            hideLoading();
        });
}

// === Billing Table Rendering ===
function renderBillingTable(data) {
    console.log('renderBillingTable called with:', data);
    
    if (!billingTableBody) {
        console.error('billingTableBody not found');
        return;
    }
    
    billingTableBody.innerHTML = '';

    if (!data.length) {
        const selectedMonth = document.getElementById('filter-month')?.value;
        const selectedPurok = document.getElementById('filter-purok')?.value;
        
        let filterText = 'the selected filters';
        if (selectedMonth && selectedPurok) {
            filterText = `${ubillFormatMonth(selectedMonth)} in Purok ${selectedPurok}`;
        } else if (selectedMonth) {
            filterText = ubillFormatMonth(selectedMonth);
        } else if (selectedPurok) {
            filterText = `Purok ${selectedPurok}`;
        }
        
        billingTableBody.innerHTML = `
            <tr>
                <td colspan="6" class="ubill-no-data">
                    <div class="ubill-no-data-icon">📭</div>
                    <div>No billing records found for ${filterText}.</div>
                </td>
            </tr>
        `;
        return;
    }

    data.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.bill_no || '-'}</td>
            <td>${item.user_name || '-'}</td>
            <td>${item.email || '-'}</td>
            <td>₱${parseFloat(item.amount_due || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
            <td>${item.status || '-'}</td>
            <td>${ubillFormatMonth(item.billing_month)}</td>
        `;
        billingTableBody.appendChild(row);
    });
    
    console.log('Table rendered successfully with', data.length, 'records');
}

// === Populate Month Filter Options ===
function populateMonthFilter() {
    const monthSelect = document.getElementById('filter-month');
    if (!monthSelect) return;
    
    // Clear existing options
    monthSelect.innerHTML = '<option value="">All Months</option>';
    
    // Generate options for the past 24 months and future 6 months (extended range)
    const today = new Date();
    const months = [];
    
    // Add past 24 months
    for (let i = 24; i >= 0; i--) {
        const date = new Date(today.getFullYear(), today.getMonth() - i, 1);
        const monthValue = date.toISOString().slice(0, 7);
        const monthLabel = date.toLocaleString('en-US', { year: 'numeric', month: 'long' });
        months.push({ value: monthValue, label: monthLabel });
    }
    
    // Add future 6 months
    for (let i = 1; i <= 6; i++) {
        const date = new Date(today.getFullYear(), today.getMonth() + i, 1);
        const monthValue = date.toISOString().slice(0, 7);
        const monthLabel = date.toLocaleString('en-US', { year: 'numeric', month: 'long' });
        months.push({ value: monthValue, label: monthLabel });
    }
    
    // Add options to select
    months.forEach(month => {
        const option = document.createElement('option');
        option.value = month.value;
        option.textContent = month.label;
        
        // Mark current month with indicator and make it stand out
        if (month.value === currentMonth) {
            option.textContent += ' (Current Month)';
            option.style.fontWeight = 'bold';
            option.style.backgroundColor = '#e0f2fe';
        }
        
        monthSelect.appendChild(option);
    });
    
    console.log('Month filter populated with options from', months[0]?.label, 'to', months[months.length-1]?.label);
}

// === Populate Purok Filter Options ===
function populatePurokFilter() {
    const purokSelect = document.getElementById('filter-purok');
    if (!purokSelect) return;
    
    // Clear existing options
    purokSelect.innerHTML = '<option value="">All Puroks</option>';
    
    // Add purok options (1-7 as commonly used in Philippine barangays)
    for (let i = 1; i <= 7; i++) {
        const option = document.createElement('option');
        option.value = i.toString();
        option.textContent = `Purok ${i}`;
        purokSelect.appendChild(option);
    }
    
    console.log('Purok filter populated with options 1-7');
}

// === Filter & Search Handlers ===
function ubillRenderFilteredTable() {
    const month = document.getElementById('filter-month')?.value || '';
    const purok = document.getElementById('filter-purok')?.value || '';
    const search = document.getElementById('search-term')?.value || '';
    
    console.log('Applying filters - Month:', month, 'Purok:', purok, 'Search:', search);
    
    loadBillings({ month: month, purok: purok, search: search });
}

function ubillResetFilters() {
    console.log('Resetting filters to current month:', currentMonth);
    
    // Reset to current month and clear other filters
    document.getElementById('filter-month').value = currentMonth;
    document.getElementById('filter-purok').value = '';
    document.getElementById('search-term').value = '';
    
    // Load current month billings
    loadBillings({ month: currentMonth });
}

// === Auto-apply filters on change (optional enhancement) ===
function setupFilterAutoApply() {
    const monthFilter = document.getElementById('filter-month');
    const purokFilter = document.getElementById('filter-purok');
    const searchFilter = document.getElementById('search-term');
    
    // Auto-apply when month changes
    if (monthFilter) {
        monthFilter.addEventListener('change', function() {
            ubillRenderFilteredTable();
        });
    }
    
    // Auto-apply when purok changes
    if (purokFilter) {
        purokFilter.addEventListener('change', function() {
            ubillRenderFilteredTable();
        });
    }
    
    // Auto-apply search on input (with debounce)
    if (searchFilter) {
        let searchTimeout;
        searchFilter.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                ubillRenderFilteredTable();
            }, 500); // 500ms delay after user stops typing
        });
    }
}

// === Print Table Handler ===
function ubillPrintTable() {
    // Fetch all records for current filters
    const month = document.getElementById('filter-month')?.value || '';
    const purok = document.getElementById('filter-purok')?.value || '';
    const search = document.getElementById('search-term')?.value || '';
    
    const url = `<?= site_url('admin/getAllBillings') ?>?month=${month}&purok=${encodeURIComponent(purok)}&search=${encodeURIComponent(search)}&all=1`;

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.json())
        .then(data => {
            const billings = data.billings || [];
            let rows = '';
            billings.forEach(item => {
                rows += `
                    <tr>
                        <td>${item.bill_no || '-'}</td>
                        <td>${item.user_name || '-'}</td>
                        <td>${item.email || '-'}</td>
                        <td>₱${parseFloat(item.amount_due || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        <td>${item.status || '-'}</td>
                        <td>${ubillFormatMonth(item.billing_month)}</td>
                    </tr>
                `;
            });

            const today = new Date().toLocaleString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', hour12: true });
            
            // Build filter description for print header
            let filterText = '';
            if (month && purok) {
                filterText = ` - ${ubillFormatMonth(month)} (Purok ${purok})`;
            } else if (month) {
                filterText = ` - ${ubillFormatMonth(month)}`;
            } else if (purok) {
                filterText = ` - Purok ${purok}`;
            } else {
                filterText = ' - All Records';
            }
            
            const content = `
                <div style="text-align:center; margin-bottom:20px;">
                    <h2 style="margin:0; font-size:2rem;">Billing Report${filterText}</h2>
                    <div style="font-size:15px; color:#555; margin-top:8px;">Generated on: ${today}</div>
                    <hr style="width:100%; margin:18px 0 0 0;">
                </div>
                <table style="width:100%; border-collapse:collapse; margin-top:18px;">
                    <thead>
                        <tr style="background:#f3f3f3;">
                            <th style="padding:10px;border:1px solid #333;">Bill No</th>
                            <th style="padding:10px;border:1px solid #333;">User Name</th>
                            <th style="padding:10px;border:1px solid #333;">Email</th>
                            <th style="padding:10px;border:1px solid #333;">Amount Due</th>
                            <th style="padding:10px;border:1px solid #333;">Status</th>
                            <th style="padding:10px;border:1px solid #333;">Billing Month</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${rows || `<tr><td colspan="6" style="text-align:center; padding:20px;">No billing records found for selected filters.</td></tr>`}
                    </tbody>
                </table>
            `;
            
            const printWindow = window.open('', '', 'width=900,height=600');
            printWindow.document.write('<html><head><title>Billing Report</title>');
            printWindow.document.write('<style>body{font-family:Poppins,sans-serif;margin:20px;} table{width:100%;border-collapse:collapse;} th,td{border:1px solid #333;padding:8px;text-align:left;} th{background:#f3f3f3;} h2{margin-bottom:8px;} </style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write(content);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => { printWindow.print(); printWindow.close(); }, 200);
        })
        .catch(err => {
            console.error('Print error:', err);
            alert('❌ Failed to generate print report. Please try again.');
        });
}

// === Initialization ===
function ubillSetCurrentBillingMonth() {
    const billingMonthInput = document.getElementById('ubill-billingMonth');
    const manualMonthInput = document.getElementById('manual-billing-month');
    
    if (billingMonthInput) billingMonthInput.value = currentMonth;
    if (manualMonthInput) manualMonthInput.value = currentMonth;
}

function ubillPopulateUserSelect() {
    const select = document.getElementById('manual-user-select');
    if (!select) return;
    
    select.innerHTML = '<option value="">Choose a user...</option>';
    ubillUsers.forEach(user => {
        const option = document.createElement('option');
        option.value = user.id;
        option.textContent = `${user.first_name} ${user.last_name} (Purok ${user.purok}, ${user.household_type.charAt(0).toUpperCase() + user.household_type.slice(1)})`;
        select.appendChild(option);
    });
}

// === Tab Navigation ===
function ubillToggleView(viewId) {
    document.querySelectorAll('.ubill-section').forEach(section => {
        section.classList.remove('active');
    });
    document.getElementById(viewId).classList.add('active');

    document.querySelectorAll('.ubill-tab-btn').forEach(btn => {
        btn.classList.remove('active');
        btn.classList.add('inactive');
    });
    
    if (event && event.currentTarget) {
        event.currentTarget.classList.add('active');
        event.currentTarget.classList.remove('inactive');
    }

    if (viewId === 'ubill-billings') {
        console.log('Switching to billing tab - loading current month data');
        // Load current month data when switching to billing tab
        loadBillings({ month: currentMonth });
    }
    if (viewId === 'ubill-manual-billing') {
        ubillShowMessage('ubill-manualSuccess', '', false);
    }
}

// === Search on Enter Key ===
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-term');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                ubillRenderFilteredTable();
            }
        });
    }
    
    // Setup auto-apply filters
    setupFilterAutoApply();
});

// === Initialize Page ===
initBillingPage();

// === Synchronize All Billings ===
function ubillSetupAllBillings() {
    const monthInput = document.getElementById('ubill-billingMonth');
    const month = monthInput ? monthInput.value : currentMonth;
    
    if (!month) {
        ubillShowMessage('ubill-billingSuccess', '❌ Please select a billing month', false);
        return;
    }
    
    if (!confirm(`Are you sure you want to synchronize billings for ${ubillFormatMonth(month)}? This will create new billings for all users.`)) {
        return;
    }
    
    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<span>⏳</span><span>Synchronizing...</span>';
    
    const url = `<?= site_url('admin/synchronizeBillings') ?>`;
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ month: month })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            ubillShowMessage('ubill-billingSuccess', `✅ ${data.message}`, true);
            // Reload the billing table
            loadBillings({ month: month });
        } else {
            ubillShowMessage('ubill-billingSuccess', `❌ ${data.message}`, false);
        }
    })
    .catch(err => {
        console.error('Synchronization error:', err);
        ubillShowMessage('ubill-billingSuccess', '❌ Failed to synchronize billings. Check console.', false);
    })
    .finally(() => {
        // Restore button state
        button.disabled = false;
        button.innerHTML = originalText;
    });
}
</script>