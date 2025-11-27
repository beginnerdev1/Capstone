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
        
        }
        .ubill-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem; 
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
        /* Make billing tables visually consistent with dashboard stat-cards */
        .ubill-table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
        .ubill-table th { 
            padding: 0.9rem 1.25rem; 
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
            padding: 1rem 1.25rem; 
            font-size: 0.95rem; 
            vertical-align: middle;
            background: #ffffff;
            border-bottom: none;
        } 
        .ubill-table tbody tr td:first-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
        .ubill-table tbody tr td:last-child { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
        .ubill-table tbody tr { transition: transform 0.18s ease, box-shadow 0.18s ease; }
        .ubill-table tbody tr td { box-shadow: none; }
        .ubill-table tbody tr:hover td { box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04); transform: translateY(-4px); }

        /* container adjustments */
        .ubill-table-container { padding: 1rem; border-radius: 12px; box-shadow: 0 12px 30px rgba(15,23,42,0.04); border: 1px solid #e5e7eb; background: white; }
        .ubill-table-header { padding: 1rem 1.25rem; }
        .ubill-table-wrapper { padding: 0.5rem 1rem 1rem 1rem; }
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
                        <!-- Fix the input field -->
<input type="month" id="ubill-billingMonth" class="ubill-form-input" 
    style="font-weight: 600; color: var(--ubill-primary); background: white;"> 
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
                                <th>Balance</th>
                                <th>Status</th>
                                <th>Billing Month</th>
                                <th>Due Date</th>
                            </tr>
                        </thead>
                        <tbody id="ubill-billingTable">
                            <tr>
                                <td colspan="8" class="ubill-no-data">
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
                <div class="ubill-form-title">✍️ Create Manual Billing</div>
                <div class="ubill-form-subtitle">
                    Create custom billing for specific users and months. Manual bills will use current date billing logic with automatic due date calculation (7 days after creation).
                </div>
                <div class="ubill-success-message" id="ubill-manualSuccess"></div>

                <div class="ubill-form-grid">
                    <div class="ubill-form-group">
                        <label class="ubill-form-label">🏘️ Select Purok First</label>
                        <select id="manual-purok-select" class="ubill-form-select" required>
                            <option value="">Choose a Purok...</option>
                        </select>
                    </div>
                    <div class="ubill-form-group">
                        <label class="ubill-form-label">👤 Select User</label>
                        <select id="manual-user-select" class="ubill-form-select" required disabled>
                            <option value="">First select a Purok...</option>
                        </select>
                    </div>
                    <div class="ubill-form-group">
                        <label class="ubill-form-label">📅 Billing Month</label>
                        <input type="month" id="manual-billing-month" class="ubill-form-input" 
                               style="font-weight: 600; color: var(--ubill-primary); background: white;" required>
                    </div>
                    <div class="ubill-form-group">
                        <label class="ubill-form-label">💰 Amount (₱)</label>
                        <input type="number" id="manual-billing-amount" class="ubill-form-input" placeholder="e.g., 60.00" step="0.01" min="1" required readonly aria-readonly="true" title="Auto-calculated amount (read-only)">
                    </div>
                </div>

                <div class="ubill-form-actions">
                    <button onclick="ubillProcessManualBill()" class="ubill-btn ubill-btn-success" disabled id="manual-submit-btn">
                        <span>✅</span>
                        <span>Create Manual Billing</span>
                    </button>
                </div>

                <!-- Manual Billing Preview Section -->
                <div id="manual-billing-preview" class="ubill-content-card" style="display: none; margin-top: 1rem; background: rgba(59,130,246,0.05);">
                    <div class="ubill-form-title" style="color: var(--ubill-primary);">📋 Billing Preview</div>
                    <div id="manual-preview-content"></div>
                </div>
            </div>
        </div>
    </div>
<script>
// Avoid redeclaring globals on AJAX reloads
window.APP_BASE_URL = window.APP_BASE_URL || "<?= base_url() ?>";
// Avoid redeclaring the billing table body when this script is injected multiple times
window.ubillBillingTableBody = window.ubillBillingTableBody || document.getElementById('ubill-billingTable');
var billingTableBody = window.ubillBillingTableBody;

// === Global Variables ===
let ubillUsers = [];
let currentMonth = null;

// === Utility Functions ===
function showLoading() {
    if (billingTableBody) {
        billingTableBody.innerHTML = `
            <tr>
                <td colspan="7" style="text-align: center; padding: 2rem;">
                    <div>⏳ Loading billing data...</div>
                </td>
            </tr>
        `;
    }
}

function ubillShowMessage(elementId, message, isSuccess = true) {
    const el = document.getElementById(elementId);
    if (!el) return;
    
    el.textContent = message;
    el.classList.remove('show', 'danger');
    if (message) {
        el.classList.add('show');
        if (!isSuccess) el.classList.add('danger');
        setTimeout(() => el.classList.remove('show'), 12000);
    }
}

function ubillFormatMonth(monthStr) {
    if (!monthStr) return 'N/A';
    
    try {
        // Handle both YYYY-MM and YYYY-MM-DD formats
        let date;
        if (monthStr.length === 7) {
            // YYYY-MM format
            date = new Date(monthStr + '-01');
        } else if (monthStr.length === 10) {
            // YYYY-MM-DD format, extract just the year-month
            const yearMonth = monthStr.substring(0, 7);
            date = new Date(yearMonth + '-01');
        } else {
            // Try parsing as-is
            date = new Date(monthStr);
        }
        
        // Check if date is valid
        if (isNaN(date.getTime())) {
            console.warn('Invalid date format:', monthStr);
            return monthStr; // Return original if can't parse
        }
        
        return date.toLocaleString('en-US', { year: 'numeric', month: 'long' });
    } catch (error) {
        console.error('Date parsing error:', error, monthStr);
        return monthStr; // Return original if error
    }
}

// === Load Billing Statistics ===
function loadBillingStatistics(month = null) {
    const targetMonth = month || currentMonth;
    
    fetch(`<?= site_url('admin/getBillingStatistics') ?>?month=${targetMonth}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            updateStatisticsDisplay(data.statistics);
        } else {
            console.error('Failed to load statistics:', data.message);
            // Set to zero if failed
            updateStatisticsDisplay({
                solo: 0,
                senior: 0,
                family: 0,
                total: 0
            });
        }
    })
    .catch(err => {
        console.error('Statistics fetch error:', err);
        // Set to zero if error
        updateStatisticsDisplay({
            solo: 0,
            senior: 0,
            family: 0,
            total: 0
        });
    });
}

function updateStatisticsDisplay(stats) {
    // Update the summary cards with actual data
    const soloElement = document.getElementById('ubill-summarySolo');
    const seniorElement = document.getElementById('ubill-summarySenior');
    const familyElement = document.getElementById('ubill-summaryFamily');
    const totalElement = document.getElementById('ubill-summaryTotal');
    
    if (soloElement) soloElement.textContent = stats.solo || 0;
    if (seniorElement) seniorElement.textContent = stats.senior || 0;
    if (familyElement) familyElement.textContent = stats.family || 0;
    if (totalElement) totalElement.textContent = stats.total || 0;
}

// === Load Billings (Main Function) ===
function loadBillings(filters = {}) {
    showLoading();
    
    const month = filters.month !== undefined ? filters.month : (document.getElementById('filter-month')?.value || currentMonth);
    const purok = filters.purok || (document.getElementById('filter-purok')?.value || '');
    const search = filters.search || (document.getElementById('search-term')?.value || '');
    const page = filters.page || 1;

    const url = `<?= site_url('admin/getAllBillings') ?>?month=${month}&purok=${encodeURIComponent(purok)}&search=${encodeURIComponent(search)}&page=${page}`;

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderBillingTable(data.billings || []);
                // Load statistics for the filtered month
                loadBillingStatistics(month);
            } else {
                ubillShowMessage('ubill-billingSuccess', `❌ ${data.message}`, false);
                renderBillingTable([]);
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            ubillShowMessage('ubill-billingSuccess', '❌ Failed to load billings. Check console.', false);
            renderBillingTable([]);
        });
}

// === Billing Table Rendering ===
function renderBillingTable(data) {
    if (!billingTableBody) return;
    
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
                <td colspan="8" style="text-align: center; padding: 3rem;">
                    <div class="ubill-no-data-icon">📋</div>
                    <div>No billing records found for ${filterText}</div>
                </td>
            </tr>
        `;
        return;
    }


    data.forEach(item => {
        const row = document.createElement('tr');
        
        // Format due date and add status styling
        const dueDate = item.due_date ? new Date(item.due_date) : null;
        const today = new Date();
        let dueDateDisplay = 'N/A';
        let dueDateClass = '';
        
        if (dueDate) {
            dueDateDisplay = dueDate.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });
            
            // Add styling based on due date status
            if (dueDate < today && item.status === 'Pending') {
                dueDateClass = 'style="color: #dc2626; font-weight: 600;"'; // Red for overdue
            } else if (dueDate <= new Date(today.getTime() + 3 * 24 * 60 * 60 * 1000) && item.status === 'Pending') {
                dueDateClass = 'style="color: #d97706; font-weight: 600;"'; // Orange for due soon
            } else {
                dueDateClass = 'style="color: #059669;"'; // Green for normal
            }
        }
        
        row.innerHTML = `
            <td>${item.bill_no || '-'}</td>
            <td>${item.user_name || '-'}</td>
            <td>${item.email || '-'}</td>
            <td>₱${parseFloat(item.amount_due || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
            <td>₱${parseFloat(item.balance || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
            <td>${renderStatusBadge(item.status)}</td>
            <td>${ubillFormatMonth(item.billing_month)}</td>
            <td ${dueDateClass}>${dueDateDisplay}</td>
        `;
        billingTableBody.appendChild(row);
    });
}

// Render a simple status badge HTML for table
function renderStatusBadge(status) {
    if (!status) return '-';
    const s = status.toString();
    let cls = 'ubill-status-badge';
    let text = s;
    if (s === 'Paid') cls += ' ubill-status-active';
    if (s === 'Partial') cls += ' ubill-status-warning';
    if (s === 'Pending') cls += ' ubill-status-pending';
    return `<span class="${cls}">${text}</span>`;
}

// === Initialize Functions ===
function populateMonthFilter() {
    const monthSelect = document.getElementById('filter-month');
    if (!monthSelect) return;

    monthSelect.innerHTML = '<option value="">All Months</option>';

    const today = new Date();
    const months = [];

    // Add past 24 months
    for (let i = 24; i >= 0; i--) {
        const date = new Date(today.getFullYear(), today.getMonth() - i, 1);
        const monthValue = date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0');
        const monthLabel = date.toLocaleString('en-US', { year: 'numeric', month: 'long' });
        months.push({ value: monthValue, label: monthLabel });
    }

    // Add future 6 months
    for (let i = 1; i <= 6; i++) {
        const date = new Date(today.getFullYear(), today.getMonth() + i, 1);
        const monthValue = date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0');
        const monthLabel = date.toLocaleString('en-US', { year: 'numeric', month: 'long' });
        months.push({ value: monthValue, label: monthLabel });
    }

    months.forEach(month => {
        const option = document.createElement('option');
        option.value = month.value;
        option.textContent = month.label;

        if (month.value === currentMonth) {
            option.textContent += ' (Current Month)';
            option.style.fontWeight = 'bold';
            option.style.backgroundColor = '#e0f2fe';
        }

        monthSelect.appendChild(option);
    });
}

function populatePurokFilter() {
    const purokSelect = document.getElementById('filter-purok');
    if (!purokSelect) return;
    
    purokSelect.innerHTML = '<option value="">All Puroks</option>';
    
    for (let i = 1; i <= 7; i++) {
        const option = document.createElement('option');
        option.value = i.toString();
        option.textContent = `Purok ${i}`;
        purokSelect.appendChild(option);
    }
}

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
        option.textContent = `${user.first_name} ${user.last_name} (Purok ${user.purok})`;
        select.appendChild(option);
    });
}

// === Filter Functions ===
function ubillRenderFilteredTable() {
    const month = document.getElementById('filter-month')?.value || '';
    const purok = document.getElementById('filter-purok')?.value || '';
    const search = document.getElementById('search-term')?.value || '';
    
    loadBillings({ month: month, purok: purok, search: search });
}

function ubillResetFilters() {
    document.getElementById('filter-month').value = currentMonth;
    document.getElementById('filter-purok').value = '';
    document.getElementById('search-term').value = '';
    
    loadBillings({ month: currentMonth });
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
        loadBillings({ month: currentMonth });
    }
}

// === Synchronize Billings ===
function ubillSetupAllBillings() {
    const monthInput = document.getElementById('ubill-billingMonth');
    const month = monthInput ? monthInput.value : currentMonth;
    
    if (!month) {
        ubillShowMessage('ubill-billingSuccess', '❌ Please select a billing month', false);
        return;
    }
    
    if (!confirm(`Are you sure you want to synchronize billings for ${ubillFormatMonth(month)}?`)) {
        return;
    }
    
    const button = event.target;
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<span>⏳</span><span>Synchronizing...</span>';
    
    fetch(`<?= site_url('admin/synchronizeBillings') ?>`, {
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
            loadBillings({ month: month });
            // Refresh statistics after synchronization
            loadBillingStatistics(month);
        } else {
            ubillShowMessage('ubill-billingSuccess', `❌ ${data.message}`, false);
        }
    })
    .catch(err => {
        console.error('Synchronization error:', err);
        ubillShowMessage('ubill-billingSuccess', '❌ Failed to synchronize billings.', false);
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = originalText;
    });
}

// === Print Function ===

function ubillPrintTable() {
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
                // Format due date for printing
                const dueDate = item.due_date ? new Date(item.due_date) : null;
                const dueDateDisplay = dueDate ? 
                    dueDate.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : 
                    'N/A';
                
                rows += `
                    <tr>
                        <td style="border:1px solid #333; padding:8px;">${item.bill_no || '-'}</td>
                        <td style="border:1px solid #333; padding:8px;">${item.user_name || '-'}</td>
                        <td style="border:1px solid #333; padding:8px;">${item.email || '-'}</td>
                        <td style="border:1px solid #333; padding:8px;">₱${parseFloat(item.amount_due || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        <td style="border:1px solid #333; padding:8px;">₱${parseFloat(item.balance || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        <td style="border:1px solid #333; padding:8px;">${item.status || '-'}</td>
                        <td style="border:1px solid #333; padding:8px;">${ubillFormatMonth(item.billing_month)}</td>
                        <td style="border:1px solid #333; padding:8px;">${dueDateDisplay}</td>
                    </tr>
                `;
            });

            const today = new Date().toLocaleString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', hour12: true });
            
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
                    <h2>Billing Management Report${filterText}</h2>
                    <p>Generated on: ${today}</p>
                </div>
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="border:1px solid #333; padding:8px; background:#f3f3f3;">Bill No</th>
                            <th style="border:1px solid #333; padding:8px; background:#f3f3f3;">User Name</th>
                            <th style="border:1px solid #333; padding:8px; background:#f3f3f3;">Email</th>
                                            <th style="border:1px solid #333; padding:8px; background:#f3f3f3;">Amount Due</th>
                                            <th style="border:1px solid #333; padding:8px; background:#f3f3f3;">Balance</th>
                                            <th style="border:1px solid #333; padding:8px; background:#f3f3f3;">Status</th>
                                            <th style="border:1px solid #333; padding:8px; background:#f3f3f3;">Billing Month</th>
                                            <th style="border:1px solid #333; padding:8px; background:#f3f3f3;">Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${rows || '<tr><td colspan="7" style="text-align:center; padding:20px;">No billing records found.</td></tr>'}
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
        });
}

// === MAIN INITIALIZATION ===
function initializePage() {
    // Set current month first
    const now = new Date();
    currentMonth = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0');
    
    // Initialize all components
    populateMonthFilter();
    populatePurokFilter();
    populateManualPurokSelect(); // Add this line
    ubillSetCurrentBillingMonth();
    
    // Set current month as default
    const monthFilter = document.getElementById('filter-month');
    if (monthFilter) monthFilter.value = currentMonth;
    
    // Setup auto-apply filters
    setupFilterAutoApply();
    setupManualBillingEventListeners(); // Add this line
    
    // Load current month data AND statistics
    loadBillings({ month: currentMonth });
    loadBillingStatistics(currentMonth);
}

function setupFilterAutoApply() {
    const monthFilter = document.getElementById('filter-month');
    const purokFilter = document.getElementById('filter-purok');
    const searchFilter = document.getElementById('search-term');
    
    if (monthFilter) {
        monthFilter.addEventListener('change', ubillRenderFilteredTable);
    }
    
    if (purokFilter) {
        purokFilter.addEventListener('change', ubillRenderFilteredTable);
    }
    
    if (searchFilter) {
        let searchTimeout;
        searchFilter.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(ubillRenderFilteredTable, 500);
        });
        
        searchFilter.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') ubillRenderFilteredTable();
        });
    }
}

// === EXECUTE INITIALIZATION ===
initializePage();

// === Manual Billing Functions ===
function populateManualPurokSelect() {
    const purokSelect = document.getElementById('manual-purok-select');
    if (!purokSelect) return;
    
    purokSelect.innerHTML = '<option value="">Choose a Purok...</option>';
    
    for (let i = 1; i <= 7; i++) {
        const option = document.createElement('option');
        option.value = i.toString();
        option.textContent = `Purok ${i}`;
        purokSelect.appendChild(option);
    }
}
function loadUsersByPurok(purok) {
    const userSelect = document.getElementById('manual-user-select');
    if (!userSelect) return;
    
    // Show loading state
    userSelect.innerHTML = '<option value="">Loading users...</option>';
    userSelect.disabled = true;
    
    if (!purok) {
        userSelect.innerHTML = '<option value="">First select a Purok...</option>';
        return;
    }
    
    // Get the selected month for filtering
    const selectedMonth = document.getElementById('manual-billing-month')?.value || currentMonth;
    
    // Use the new endpoint specifically for manual billing
    fetch(`<?= site_url('admin/getUsersByPurokForManualBilling') ?>/${purok}?month=${selectedMonth}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(users => {
        userSelect.innerHTML = '<option value="">Choose a user...</option>';
        
        if (!users || !users.length) {
            userSelect.innerHTML = '<option value="">No users without billing found in this Purok for selected month</option>';
            userSelect.disabled = true;
            return;
        }
        
        users.forEach(user => {
            const option = document.createElement('option');
            option.value = user.user_id;
            option.textContent = `${user.first_name} ${user.last_name}`;
            // ✅ Store the calculated amount in the option data
            option.dataset.calculatedAmount = user.calculated_amount || 30.00;
            userSelect.appendChild(option);
        });
        
        userSelect.disabled = false;
    })
    .catch(err => {
        console.error('Failed to fetch users:', err);
        userSelect.innerHTML = '<option value="">Error loading users</option>';
        ubillShowMessage('ubill-manualSuccess', '❌ Failed to load users for selected Purok', false);
    });
}

// === Manual Billing Event Listeners ===
function onUserSelectionChange() {
    const userSelect = document.getElementById('manual-user-select');
    const amountInput = document.getElementById('manual-billing-amount');
    
    if (!userSelect || !amountInput) return;
    
    const selectedOption = userSelect.selectedOptions[0];
    
    if (selectedOption && selectedOption.dataset.calculatedAmount) {
        // Auto-populate with calculated amount
        amountInput.value = parseFloat(selectedOption.dataset.calculatedAmount).toFixed(2);
        
        // Add visual indicator that amount was auto-calculated
        amountInput.style.background = 'rgba(16, 185, 129, 0.1)';
        amountInput.style.borderColor = 'var(--ubill-success)';
        
        // Optional: Add tooltip or placeholder text
        amountInput.title = 'Auto-calculated amount based on user profile (you can override)';
    } else {
        // Reset if no user selected
        amountInput.value = '';
        amountInput.style.background = '';
        amountInput.style.borderColor = '';
        amountInput.title = '';
    }
    
    // Trigger form validation
    validateManualBillingForm();
}

// === Event Listeners for Manual Billing ===
function setupManualBillingEventListeners() {
    const purokSelect = document.getElementById('manual-purok-select');
    const userSelect = document.getElementById('manual-user-select');
    const amountInput = document.getElementById('manual-billing-amount');
    const monthInput = document.getElementById('manual-billing-month');
    
    if (purokSelect) {
        purokSelect.addEventListener('change', function() {
            const purok = this.value;
            loadUsersByPurok(purok);
            validateManualBillingForm();
        });
    }
    
    if (userSelect) {
        // ✅ UPDATED: Add user selection change listener
        userSelect.addEventListener('change', function() {
            onUserSelectionChange(); // This will call validateManualBillingForm internally
        });
    }
    
    if (amountInput) {
        amountInput.addEventListener('input', function() {
            // ✅ Reset styling when user manually edits
            this.style.background = '';
            this.style.borderColor = '';
            this.title = '';
            validateManualBillingForm();
        });
    }
    
    // Add month change listener
    if (monthInput) {
        monthInput.addEventListener('change', onManualMonthChange);
    }
}



function onManualMonthChange() {
    // When month changes, reload users for the currently selected purok
    const purokSelect = document.getElementById('manual-purok-select');
    const currentPurok = purokSelect?.value;
    
    // Reset user selection
    document.getElementById('manual-user-select').innerHTML = '<option value="">First select a Purok...</option>';
    document.getElementById('manual-user-select').disabled = true;
    
    // Reload users if purok is selected
    if (currentPurok) {
        loadUsersByPurok(currentPurok);
    }
    
    // Revalidate form
    validateManualBillingForm();
}

function validateManualBillingForm() {
    const purok = document.getElementById('manual-purok-select')?.value;
    const user = document.getElementById('manual-user-select')?.value;
    const month = document.getElementById('manual-billing-month')?.value;
    const amount = document.getElementById('manual-billing-amount')?.value;
    
    const submitBtn = document.getElementById('manual-submit-btn');
    const isValid = purok && user && month && amount && parseFloat(amount) > 0;
    
    if (submitBtn) {
        submitBtn.disabled = !isValid;
    }
    
    // Show preview if all fields are filled
    if (isValid) {
        showManualBillingPreview();
    } else {
        hideManualBillingPreview();
    }
    
    return isValid;
}

function showManualBillingPreview() {
    const purok = document.getElementById('manual-purok-select')?.value;
    const userSelect = document.getElementById('manual-user-select');
    const month = document.getElementById('manual-billing-month')?.value;
    const amount = parseFloat(document.getElementById('manual-billing-amount')?.value || 0);
    
    const userName = userSelect?.selectedOptions[0]?.textContent || 'Unknown User';
    const today = new Date();
    const dueDate = new Date(today.getTime() + 7 * 24 * 60 * 60 * 1000); // 7 days from today
    
    const previewContent = `
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div><strong>User:</strong> ${userName} (Purok ${purok})</div>
            <div><strong>Month:</strong> ${ubillFormatMonth(month)}</div>
            <div><strong>Amount:</strong> ₱${amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</div>
            <div><strong>Due Date:</strong> ${dueDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</div>
        </div>
    `;
    
    document.getElementById('manual-preview-content').innerHTML = previewContent;
    document.getElementById('manual-billing-preview').style.display = 'block';
}

function hideManualBillingPreview() {
    document.getElementById('manual-billing-preview').style.display = 'none';
}

function ubillProcessManualBill() {
    const purok = document.getElementById('manual-purok-select')?.value;
    const userId = document.getElementById('manual-user-select')?.value;
    const month = document.getElementById('manual-billing-month')?.value;
    const amount = document.getElementById('manual-billing-amount')?.value;
    
    // Validate form
    if (!validateManualBillingForm()) {
        ubillShowMessage('ubill-manualSuccess', '❌ Please fill in all required fields correctly', false);
        return;
    }
    
    const userName = document.getElementById('manual-user-select').selectedOptions[0]?.textContent || 'Unknown User';
    
    if (!confirm(`Create manual billing for ${userName} (Purok ${purok})\nMonth: ${ubillFormatMonth(month)}\nAmount: ₱${parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2 })}?`)) {
        return;
    }
    
    const button = event.target;
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<span>⏳</span><span>Creating Billing...</span>';
    
    // Prepare data similar to synchronize billing
    const requestData = {
        user_id: userId,
        month: month,
        amount: parseFloat(amount),
        purok: purok
    };
    
    fetch(`<?= site_url('admin/createManualBilling') ?>`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(requestData)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            ubillShowMessage('ubill-manualSuccess', `✅ ${data.message}`, true);
            
            // Reset form
            resetManualBillingForm();
            
            // Refresh billing table if on billings tab
            loadBillings({ month: month });
            loadBillingStatistics(month);
        } else {
            ubillShowMessage('ubill-manualSuccess', `❌ ${data.message}`, false);
        }
    })
    .catch(err => {
        console.error('Manual billing error:', err);
        ubillShowMessage('ubill-manualSuccess', '❌ Failed to create manual billing.', false);
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = originalText;
    });
}

function resetManualBillingForm() {
    document.getElementById('manual-purok-select').value = '';
    document.getElementById('manual-user-select').innerHTML = '<option value="">First select a Purok...</option>';
    document.getElementById('manual-user-select').disabled = true;
    document.getElementById('manual-billing-month').value = currentMonth;
    document.getElementById('manual-billing-amount').value = '';
    document.getElementById('manual-submit-btn').disabled = true;
    hideManualBillingPreview();
}
</script>