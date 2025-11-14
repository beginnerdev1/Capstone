<style>
/* === BASE STYLES AND VARIABLES === */
:root { --ubill-primary: #3b82f6; --ubill-primary-dark: #2563eb; --ubill-success: #10b981; --ubill-warning: #f59e0b; --ubill-danger: #ef4444; --ubill-info: #06b6d4; --ubill-border: #e5e7eb; --ubill-dark: #1f2937; --ubill-light: #f9fafb; --ubill-muted: #6b7280; --ubill-input-height: 2.8rem; }
.ubill- * { margin: 0; padding: 0; box-sizing: border-box; }
.ubill-body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%); min-height: 100vh; padding: 2rem 2rem; display: flex; justify-content: center; align-items: flex-start; }
.ubill-wrapper {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 2.5rem;
}
.ubill-content-card {
    max-width: 900px;
    margin: 2rem auto;
    padding: 2rem 2rem;
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 2px 12px rgba(59,130,246,0.08);
}
/* === NAVIGATION & HEADER === */
/* Removed .ubill-top-nav since only one section remains */
.ubill-section { display: none; }
.ubill-section.active { display: block; }
.ubill-header {
    max-width: 900px;
    margin: 2rem auto 1.5rem auto;
    padding: 1.5rem 2rem;
    border-radius: 15px;
    font-size: 1.5rem;
    font-weight: 600;
    box-shadow: 0 4px 16px rgba(59,130,246,0.10);
}
.ubill-header-title { font-size: 2rem; font-weight: 700; display: flex; align-items: center; gap: 1rem; }
/* === FORM STYLES (Kept for the recalculation button) === */
.ubill-form-container {
    padding: 1.5rem 1rem;
}
.ubill-form-title { font-size: 1.5rem; font-weight: 700; color: var(--ubill-dark); margin-bottom: 2rem; }
.ubill-form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem; }
.ubill-form-group { display: flex; flex-direction: column; }
.ubill-form-label { font-weight: 600; color: var(--ubill-dark); margin-bottom: 0.5rem; font-size: 0.9rem; }
.ubill-form-input, .ubill-form-select { padding: 0 0.85rem; height: var(--ubill-input-height); border: 1px solid var(--ubill-border); border-radius: 8px; font-size: 0.95rem; font-family: 'Poppins', sans-serif; background: var(--ubill-light); transition: all 0.3s; -webkit-appearance: none; -moz-appearance: none; appearance: none; }
.ubill-form-select { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%236b7280'%3E%3Cpath fill-rule='evenodd' d='M5.23 7.21a.75.75 0 011.06.02L10 10.97l3.71-3.74a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.23 8.29a.75.75 0 01.02-1.06z' clip-rule='evenodd'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25rem; }
.ubill-form-input:focus, .ubill-form-select:focus { outline: none; border-color: var(--ubill-primary); box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2); background: white; }
.ubill-form-actions { display: flex; gap: 1rem; margin-top: 2rem; flex-wrap: wrap; }
/* === BUTTON STYLES === */
.ubill-btn { padding: 0.85rem 1.5rem; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s; font-size: 1rem; text-transform: uppercase; }
.ubill-btn-primary { background: linear-gradient(135deg, var(--ubill-primary) 0%, var(--ubill-primary-dark) 100%); color: white; }
.ubill-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }
.ubill-btn-success { background: linear-gradient(135deg, var(--ubill-success) 0%, #0d9488 100%); color: white; }
.ubill-btn-success:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); }
.ubill-btn-warning { background: linear-gradient(135deg, var(--ubill-warning) 0%, #d97706 100%); color: white; }
.ubill-btn-warning:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3); }
.ubill-btn-secondary { background: white; color: var(--ubill-dark); border: 1px solid var(--ubill-border); }
.ubill-btn-secondary:hover { background: var(--ubill-light); border-color: var(--ubill-primary); }
.ubill-btn:disabled { opacity: 0.5; cursor: not-allowed; }
/* === TABLE STYLES === */
.ubill-table-container { background: white; border-radius: 15px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); border: 1px solid var(--ubill-border); overflow: hidden; margin-bottom: 2rem; }
.ubill-table-header { padding: 1.5rem; background: var(--ubill-light); border-bottom: 1px solid var(--ubill-border); display: flex; justify-content: space-between; align-items: center; }
.ubill-table-title { font-weight: 700; color: var(--ubill-dark); font-size: 1.1rem; }
.ubill-table-wrapper { overflow-x: auto; }
.ubill-table { width: 100%; border-collapse: collapse; }
.ubill-table th { padding: 1rem 1.5rem; text-align: left; font-weight: 700; color: var(--ubill-dark); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid var(--ubill-border); background: var(--ubill-light); }
.ubill-table td { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--ubill-border); font-size: 0.9rem; } 
.ubill-table tbody tr:hover { background: rgba(59, 130, 246, 0.03); }
.ubill-avatar { width: 35px; height: 35px; border-radius: 50%; background: linear-gradient(135deg, var(--ubill-primary) 0%, var(--ubill-primary-dark) 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; }
.ubill-action-btns { display: flex; gap: 0.5rem; }
.ubill-action-btn { padding: 0.5rem 0.75rem; font-size: 0.8rem; border: none; border-radius: 8px; cursor: pointer; transition: all 0.3s; font-weight: 600; }
.ubill-btn-delete { background: rgba(239, 68, 68, 0.15); color: var(--ubill-danger); }
.ubill-btn-delete:hover { background: rgba(239, 68, 68, 0.25); }
.ubill-no-data { text-align: center; padding: 3rem 2rem; color: var(--ubill-muted); }
/* === BADGES & SUMMARY === */
.ubill-status-badge { display: inline-block; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
.ubill-status-active { background: rgba(16, 185, 129, 0.15); color: #059669; border: 1px solid rgba(16, 185, 129, 0.3); }
.ubill-billing-badge { padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.85rem; font-weight: 600; }
.ubill-billing-30 { background: rgba(59, 130, 246, 0.15); color: #1e40af; } 
.ubill-billing-48 { background: rgba(139, 92, 246, 0.15); color: #6d28d9; } 
.ubill-billing-60 { background: rgba(34, 197, 94, 0.15); color: #15803d; } 
.ubill-summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
.ubill-summary-card { background: linear-gradient(135deg, var(--ubill-light) 0%, white 100%); padding: 1.5rem; border-radius: 12px; text-align: center; border: 2px solid var(--ubill-border); transition: transform 0.3s, box-shadow 0.3s; }
.ubill-summary-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1); }
.ubill-summary-value { font-size: 2rem; font-weight: 700; color: var(--ubill-primary); }
.ubill-summary-label { font-size: 0.85rem; color: var(--ubill-muted); margin-top: 0.5rem; font-weight: 600; }
.ubill-success-message { background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #059669; padding: 1rem; border-radius: 10px; margin-bottom: 1rem; display: none; }
.ubill-success-message.show { display: block; animation: ubill-slideDown 0.3s ease; }
@keyframes ubill-slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
/* === MODAL STYLES (Removed modal logic but kept base classes for consistency) === */
.ubill-modal { display: none; } /* Ensuring modal is hidden */
/* === RESPONSIVENESS (Media Queries) === */
@media (max-width: 900px) {
    .ubill-wrapper {
        padding: 0 1rem;
    }
    .ubill-content-card {
        padding: 1rem 0;
        margin: 1rem 0; /* Responsive margin */
    }
    .ubill-table-wrapper { position: relative; }
    .ubill-table thead { display: none; }
    .ubill-table, .ubill-table tbody, .ubill-table tr, .ubill-table td { display: block; width: 100%; }
    .ubill-table tr { margin-bottom: 1rem; border-bottom: 1px solid var(--ubill-border); background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05); }
    .ubill-table td { text-align: right; padding: 0.75rem 1rem; position: relative; }
    .ubill-table td::before { content: attr(data-label); float: left; font-weight: 600; text-transform: uppercase; color: var(--ubill-muted); font-size: 0.75rem; }
    .ubill-table td:first-child { border-top-left-radius: 8px; border-top-right-radius: 8px; padding-top: 1.25rem; }
    .ubill-table td:last-child { border-bottom-left-radius: 8px; border-bottom-right-radius: 8px; padding-bottom: 1.25rem; }
    .ubill-action-btns { justify-content: flex-end; }
}
@media (max-width: 768px) {
    .ubill-form-grid { grid-template-columns: 1fr; }
    .ubill-summary-grid { grid-template-columns: 1fr 1fr; } 
    .ubill-action-btns { flex-direction: row; justify-content: space-between; }
    .ubill-btn { flex: 1; }
}
@media (max-width: 480px) {
    .ubill-summary-grid { grid-template-columns: 1fr; } 
    .ubill-header-title { font-size: 1.5rem; }
}
</style>
</head>
<body class="ubill-body">

<div class="ubill-wrapper">
    <div class="ubill-content-card">
        <!-- Removed TOP NAVIGATION -->

        <!-- ===== BILLING SUMMARY SECTION (Now the only active section) ===== -->
        <div id="ubill-billings" class="ubill-section active">
            <div class="ubill-header">
                <div class="ubill-header-title">⚙️ Billing and Summary Console</div>
            </div>

            <div class="ubill-form-container">
                <div class="ubill-form-title">🚀 Recalculate and Synchronize Billing</div>
                <p style="color: var(--ubill-muted); margin-bottom: 1.5rem; font-size: 0.95rem;">Use this function to ensure all pre-registered users have the correct, up-to-date monthly billing amounts based on their latest household classification rules.</p>
                <div class="ubill-success-message" id="ubill-billingSuccess"></div>
                <!-- Recalculate button remains active -->
                <button onclick="ubillSetupAllBillings()" class="ubill-btn ubill-btn-warning" style="font-size: 1rem; padding: 1rem 2rem;">⚡ SYNCHRONIZE BILLINGS NOW</button>
            </div>

            <div class="ubill-table-container">
                <div class="ubill-table-header">
                    <div class="ubill-table-title">💵 Billing Summary by Type</div>
                </div>
                <div style="padding: 2rem;">
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
                            <div class="ubill-summary-label">📋 Total Registered</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ubill-table-container">
                <div class="ubill-table-header">
                    <div class="ubill-table-title">🧾 Detailed Billing List</div>
                </div>
                <div class="ubill-table-wrapper">
                    <table class="ubill-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Household Type</th>
                                <th>Details</th>
                                <th>Billing Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="ubill-billingTable">
                            <!-- Data will be populated by JavaScript on load -->
                            <tr>
                                <td colspan="6" class="ubill-no-data">Calculating initial billing data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Removed DELETE CONFIRMATION MODAL -->

<script>
    // Mock user data to enable billing calculations after registration is removed
    let ubillUsers = [
        { id: 1, first_name: 'Juan', last_name: 'Dela Cruz', email: 'juan.cruz@example.com', phone: '0917-1234567', purok: '1', household_type: 'family', family_members: 4, billing_amount: 0, address: 'Blk 1 Lot 5', status: 'active' },
        { id: 2, first_name: 'Maria', last_name: 'Santos', email: 'maria.santos@example.com', phone: '0918-9876543', purok: '3', household_type: 'solo', family_members: 1, billing_amount: 0, address: 'Unit 3A', status: 'active' },
        { id: 3, first_name: 'Jose', last_name: 'Rizal', email: 'jose.rizal@example.com', phone: '0919-5551212', purok: '7', household_type: 'senior', family_members: 2, billing_amount: 0, address: 'House 12B', status: 'active' },
        { id: 4, first_name: 'Elena', last_name: 'Garcia', email: 'elena.garcia@example.com', phone: '0920-1112222', purok: '2', household_type: 'family', family_members: 1, billing_amount: 0, address: 'Lot 9', status: 'active' },
        { id: 5, first_name: 'Pedro', last_name: 'Reyes', email: 'pedro.reyes@example.com', phone: '0921-3334444', purok: '5', household_type: 'family', family_members: 7, billing_amount: 0, address: 'Blk 10', status: 'active' }
    ];

    /**
     * Calculates the fixed monthly billing amount.
     */
    function ubillCalculateBillingAmount(type, members = 1) {
        if (type === 'senior') return 48;
        if (type === 'solo') return 30;
        if (type === 'family') {
            // Family with 1 member is treated as solo living (₱30)
            return members >= 2 ? 60 : 30; 
        }
        return 0;
    }

    /**
     * Gets a user-friendly label for the household type.
     */
    function ubillGetHouseholdLabel(type, members = 1) {
        if (type === 'senior') return '👴 Senior';
        if (type === 'solo') return '🧑 Solo Living';
        if (type === 'family') {
            return members >= 2 ? `👨‍👩‍👧‍👦 Family (${members} members)` : `🧑 Solo (1 member)`;
        }
        return type;
    }

    /**
     * Recalculates and updates the billing amount for all users.
     * This is the core function for the remaining button.
     */
    function ubillSetupAllBillings() {
        if (ubillUsers.length === 0) {
            ubillShowSuccess('ubill-billingSuccess', '⚠️ No users to setup billing.', 'warning');
            return;
        }

        // Recalculate billing amounts for all mock users
        ubillUsers.forEach(user => {
            user.billing_amount = ubillCalculateBillingAmount(user.household_type, user.family_members);
        });

        ubillShowSuccess('ubill-billingSuccess', `✅ Billing Setup Complete! Updated ${ubillUsers.length} mock users.`);
        ubillUpdateBillingView();
    }

    /**
     * Calculates the counts for the billing summary.
     */
    function ubillCalculateSummary() {
        let familyCount = 0, soloCount = 0, seniorCount = 0;
        
        ubillUsers.forEach(user => {
            // Count based on calculated billing amount
            if (user.billing_amount === 60) {
                familyCount++;
            } else if (user.billing_amount === 30) {
                soloCount++;
            } else if (user.billing_amount === 48) {
                seniorCount++;
            }
        });
        return { familyCount, soloCount, seniorCount };
    }

    /**
     * Renders the billing summary cards and the Detailed Billing List table.
     */
    function ubillUpdateBillingView() {
        const billingTable = document.getElementById('ubill-billingTable');
        
        if (!ubillUsers.length) {
            billingTable.innerHTML = '<tr><td colspan="6" class="ubill-no-data">No users with billing amounts yet</td></tr>';
            ubillUpdateSummary();
            return;
        }

        billingTable.innerHTML = ubillUsers.map(user => {
            const fullName = `${user.first_name} ${user.last_name}`;
            const initials = `${user.first_name.charAt(0)}${user.last_name.charAt(0)}`;
            // Determine billing class based on the calculated amount
            const billingClass = user.billing_amount === 60 ? '60' : (user.billing_amount === 48 ? '48' : '30');
            
            return `
                <tr>
                    <td data-label="Name">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div class="ubill-avatar">${initials}</div>
                            <strong>${fullName}</strong>
                        </div>
                    </td>
                    <td data-label="Email">${user.email}</td>
                    <td data-label="Household Type">${ubillGetHouseholdLabel(user.household_type, user.family_members)}</td>
                    <td data-label="Details">Purok ${user.purok} · ${user.address}</td>
                    <td data-label="Billing Amount"><span class="ubill-billing-badge ubill-billing-${billingClass}">₱${user.billing_amount}</span></td>
                    <td data-label="Status"><span class="ubill-status-badge ubill-status-active">✅ Active</span></td>
                </tr>
            `;
        }).join('');

        ubillUpdateSummary();
    }

    /**
     * Updates the data in the Billing Summary cards.
     */
    function ubillUpdateSummary() {
        const summary = ubillCalculateSummary();

        document.getElementById('ubill-summaryFamily').textContent = summary.familyCount;
        document.getElementById('ubill-summarySolo').textContent = summary.soloCount;
        document.getElementById('ubill-summarySenior').textContent = summary.seniorCount;
        document.getElementById('ubill-summaryTotal').textContent = ubillUsers.length;
    }

    /**
     * Displays a success message banner.
     */
    function ubillShowSuccess(elementId, message) {
        const el = document.getElementById(elementId);
        if (message) {
            el.innerHTML = message.replace(/\n/g, '<br>'); 
        }
        el.classList.add('show');
        setTimeout(() => {
            el.classList.remove('show');
        }, 4000);
    }
    
    // Initial setup: Calculate billings and render the view on page load.
    window.onload = function() {
        ubillSetupAllBillings();
    };
</script>
