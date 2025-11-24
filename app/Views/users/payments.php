<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap');

  /* CSS Variables */
  :root {
    --primary-color: #667eea;
    --primary-dark: #5a67d8;
    --success-color: #48bb78;
    --success-dark: #38a169;
    --warning-color: #ed8936;
    --danger-color: #f56565;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    --border-radius: 0.75rem;
    --transition: all 0.2s ease;
  }

  * {
    font-family: 'Inter', sans-serif;
    box-sizing: border-box;
  }

  /* Payment Container */
  .payment-container {
    max-width: 1200px;
    margin: 0 auto;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--shadow-xl);
  }

  /* Payment Header */
  .payment-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
    padding: 2rem;
    position: relative;
    overflow: hidden;
  }

  .payment-header-content {
    position: relative;
    z-index: 2;
    text-align: center;
  }

  .payment-title {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
    background: linear-gradient(45deg, #ffffff, #e2e8f0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .payment-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    font-weight: 400;
  }

  /* Main Content Grid */
  .payment-content {
    display: grid;
    grid-template-columns: 1fr 1.5fr;
    gap: 0;
    min-height: 600px;
  }

  /* Bill Summary Panel */
  .bill-summary {
    background: linear-gradient(135deg, var(--gray-50), #ffffff);
    padding: 2rem;
    border-right: 1px solid var(--gray-200);
  }

  .summary-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--shadow-md);
    margin-bottom: 1.5rem;
    border: 1px solid var(--gray-100);
    position: relative;
  }

  .amount-display {
    text-align: center;
    margin-bottom: 1.5rem;
  }

  .amount-label {
    font-size: 0.875rem;
    color: var(--gray-500);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.5rem;
  }

  .amount-value {
    font-size: 3rem;
    font-weight: 800;
    color: var(--success-color);
    font-family: 'Poppins', sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
  }

  .currency-icon {
    font-size: 2.5rem;
    opacity: 0.8;
  }

  /* Single Peso Badge */
  .peso-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: #fff;
    width: 40px;
    height: 40px;
    border-radius: 999px;
    font-weight: 800;
    font-family: 'Poppins', sans-serif;
    margin-right: 0.5rem;
    box-shadow: var(--shadow-sm);
  }

  .peso-badge.small {
    width: 28px;
    height: 28px;
    font-size: 0.85rem;
  }

  /* Bill Selection Styles */
  .bill-selection {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--gray-100);
  }

  .selection-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--gray-200);
  }

  .selection-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--gray-800);
    margin: 0;
  }

  .pay-bill-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border: 2px solid var(--gray-200);
    border-radius: var(--border-radius);
    margin-bottom: 0.75rem;
    cursor: pointer;
  }

  .pay-bill-item.selected {
    border-color: var(--primary-color);
    background: rgba(102, 126, 234, 0.1);
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
  }

  .bill-radio {
    width: 20px;
    height: 20px;
    accent-color: var(--primary-color);
    cursor: pointer;
  }

  .bill-details {
    flex: 1;
  }

  .bill-month {
    font-weight: 700;
    color: var(--gray-800);
    font-size: 1rem;
    margin-bottom: 0.25rem;
  }

  .bill-info {
    font-size: 0.875rem;
    color: var(--gray-600);
    display: flex;
    gap: 1rem;
  }

  .bill-amount {
    font-weight: 700;
    color: var(--success-color);
    font-size: 1.1rem;
    font-family: 'Poppins', sans-serif;
  }

  .due-date {
    font-size: 0.8rem;
    color: var(--warning-color);
    font-weight: 600;
  }

  .overdue {
    color: var(--danger-color);
  }

  /* Bill Breakdown */
  .bill-breakdown {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--gray-100);
  }

  .breakdown-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--gray-100);
  }

  .breakdown-item:last-child {
    border-bottom: none;
    padding-top: 1rem;
    margin-top: 0.5rem;
    border-top: 2px solid var(--gray-200);
    font-weight: 700;
    font-size: 1.1rem;
  }

  .breakdown-label {
    color: var(--gray-600);
    font-weight: 500;
  }

  .breakdown-value {
    font-weight: 700;
    color: var(--gray-800);
  }

  /* Invoice Details */
  .invoice-details {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--gray-100);
  }

  .detail-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid var(--gray-100);
  }

  .detail-item:last-child {
    border-bottom: none;
  }

  .detail-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
  }

  .detail-content {
    flex: 1;
  }

  .detail-label {
    font-size: 0.875rem;
    color: var(--gray-500);
    font-weight: 600;
    margin-bottom: 0.25rem;
  }

  .detail-value {
    font-weight: 700;
    color: var(--gray-800);
  }

  /* No Bills Overlay */
  .no-bills-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(2px);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: var(--border-radius);
    z-index: 10;
  }

  .no-bills-icon {
    font-size: 3rem;
    color: var(--success-color);
    margin-bottom: 1rem;
  }

  .no-bills-text h4 {
    color: var(--gray-800);
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-align: center;
  }

  .no-bills-text p {
    color: var(--gray-600);
    margin: 0;
    text-align: center;
    font-size: 0.875rem;
  }

  /* Disabled State Styles */
  .disabled-content {
    opacity: 0.6;
    pointer-events: none;
    filter: grayscale(20%);
  }

  /* Payment Methods Panel */
  .payment-methods {
    background: white;
    padding: 2rem;
  }

  .methods-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--gray-100);
  }

  .methods-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-800);
    margin: 0;
  }

  .security-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, var(--success-color), var(--success-dark));
    color: white;
    border-radius: 25px;
    font-size: 0.875rem;
    font-weight: 600;
  }

  /* Enhanced Tabs */
  .payment-tabs {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .tab-btn {
    flex: 1;
    background: var(--gray-50);
    border: 2px solid var(--gray-200);
    border-radius: var(--border-radius);
    padding: 1rem 1.5rem;
    text-decoration: none;
    color: var(--gray-600);
    font-weight: 600;
    text-align: center;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    cursor: pointer;
  }

  .tab-btn.active {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    border-color: var(--primary-color);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
  }

  .tab-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  /* Form Styles */
  .payment-form {
    transition: var(--transition);
  }

  .payment-form.hidden {
    display: none;
  }

  /* GCash Gateway Styles */
  .gcash-gateway {
    text-align: center;
    padding: 2rem 1rem;
  }

  .gateway-header {
    margin-bottom: 2rem;
  }

  .gcash-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
  }

  .gcash-logo i {
    width: 50px;
    height: 50px;
    background: #007bff;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
  }

  .gcash-logo span {
    font-size: 1.5rem;
    font-weight: 700;
    color: #007bff;
  }

  .gateway-header p {
    color: var(--gray-600);
    margin: 0;
    font-size: 1rem;
  }

  .payment-amount {
    margin: 2rem 0;
    padding: 1.5rem;
    background: linear-gradient(135deg, rgba(0, 123, 255, 0.1) 0%, rgba(0, 123, 255, 0.05) 100%);
    border-radius: var(--border-radius);
    border: 1px solid rgba(0, 123, 255, 0.2);
  }

  .payment-steps {
    margin: 2rem 0;
    text-align: left;
    max-width: 300px;
    margin-left: auto;
    margin-right: auto;
  }

  .step-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--gray-200);
  }

  .step-item:last-child {
    border-bottom: none;
  }

  .step-icon {
    width: 24px;
    height: 24px;
    background: #007bff;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    flex-shrink: 0;
  }

  .step-item span:not(.step-icon) {
    color: var(--gray-700);
    font-size: 0.9rem;
  }

  .security-note {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    color: var(--success-color);
    font-size: 0.875rem;
    font-weight: 600;
    margin-top: 1.5rem;
  }

  .gcash-pay-btn {
    width: 100%;
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    border: none;
    padding: 1.25rem 2rem;
    border-radius: var(--border-radius);
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    margin-top: 2rem;
  }

  .gcash-pay-btn:disabled {
    background: var(--gray-400);
    cursor: not-allowed;
  }

 
  .manual-payment {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 1.5rem;
    gap: 1rem;
    width: 100%;
    max-width: 420px;
    margin: 0 auto;
  }

  .gcash-info {
    background: linear-gradient(135deg, var(--gray-50), white);
    border: 2px solid var(--gray-200);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    margin-bottom: 0.75rem;
    width: 100%;
    max-width: 360px;
    margin-left: auto;
    margin-right: auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px; /* space between number, QR and buttons */
    text-align: center;
  }

  /* ---------- Polished GCash UI (cards + buttons) ---------- */

  /* Container for number card */
  .gcash-number-card {
    max-width: 360px;
    width: 100%;
    margin: 0 auto 14px;
    padding: 1.25rem;
    background: linear-gradient(180deg, #ffffff, #fbfdff);
    border: 1px solid rgba(15,23,42,0.06);
    border-radius: 14px;
    box-shadow: 0 6px 18px rgba(16,24,40,0.06);
    text-align: center;
    box-sizing: border-box;
  }

  /* Title above number */
  .gcash-number-card .gcash-title {
    margin: 0 0 6px;
    font-size: 1rem;
    color: var(--gray-700);
    font-weight: 700;
  }

  /* Prominent number */
  .gcash-number-card .gcash-number {
    display: inline-block;
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--primary-color);
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(90deg, rgba(102,126,234,0.06), rgba(72,187,120,0.04));
    padding: 0.5rem 0.9rem;
    border-radius: 10px;
    margin-top: 6px;
    letter-spacing: 0.6px;
  }

  /* QR card that sits below the number */
  .gcash-qr-card {
    max-width: 360px;
    margin: 0 auto 14px;
    padding: 1rem;
    background: linear-gradient(180deg, #ffffff, #fbfdff);
    border: 1px solid rgba(15, 23, 42, 0.06);
    border-radius: 14px;
    box-shadow: 0 6px 18px rgba(16,24,40,0.06);
    text-align: center;
    box-sizing: border-box;
  }

  /* Larger, framed QR placeholder */
  .gcash-qr-card .qr-placeholder {
    width: 100%;
    max-width: 360px;
    height: auto;
    min-height: 170px;
    padding: 14px;
    margin: 0 auto 14px;
    display:flex;
    align-items:center;
    justify-content:center;
    background: linear-gradient(180deg, #fbfdff, #ffffff);
    border-radius: 12px;
    border: 2px dashed rgba(99,102,241,0.08);
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.6);
    overflow: hidden;
    box-sizing: border-box;
  }

  /* If QR is an image, make it fit nicely */
  .gcash-qr-card .qr-placeholder img,
  .gcash-qr-card img.gcash-qr-img {
    display: block;
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 6px 18px rgba(31,59,131,0.06);
  }

  /* Buttons row beneath QR */
  .gcash-qr-card .action-buttons {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin-top: 6px;
  }

  /* Primary and secondary button styles */
  .gcash-qr-card .action-btn {
    flex: 1 1 50%;
    padding: 10px 14px;
    border-radius: 10px;
    border: 1px solid rgba(15,23,42,0.06);
    background: #fff;
    color: var(--gray-700);
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    box-shadow: 0 6px 16px rgba(17,24,39,0.03);
    transition: transform .12s ease, box-shadow .12s ease, background .12s ease;
    text-decoration: none;
  }

  /* Filled primary for Download, outline for Copy */
  .gcash-qr-card .action-btn.primary {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: #fff;
    border-color: transparent;
  }
  .gcash-qr-card .action-btn.secondary {
    background: #fff;
    color: var(--gray-700);
  }

  /* Hover */
  .gcash-qr-card .action-btn:hover:not(:disabled) {
    transform: translateY(-3px);
    box-shadow: 0 16px 36px rgba(16,24,40,0.08);
  }

  /* Disabled */
  .gcash-qr-card .action-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
  }

  /* Create Transaction button - make it full width and prominent */
  .transaction-btn {
    display: block;
    width: 100%;
    max-width: 360px;
    margin: 0.6rem auto 0;
    padding: 0.95rem 1rem;
    border-radius: 12px;
    border: none;
    background: linear-gradient(135deg, var(--warning-color), #dd6b20);
    color: #fff;
    font-weight: 800;
    font-size: 1.02rem;
    box-shadow: 0 12px 36px rgba(221,107,32,0.12);
    transition: transform .12s ease, box-shadow .12s ease;
  }

  /* Create hover */
  .transaction-btn:hover:not(:disabled) {
    transform: translateY(-3px);
    box-shadow: 0 24px 48px rgba(221,107,32,0.16);
  }

  /* Focus outlines (accessible) */
  .gcash-qr-card .action-btn:focus,
  .transaction-btn:focus,
  .gcash-number-card .gcash-number:focus {
    outline: none;
    box-shadow: 0 0 0 6px rgba(99,102,241,0.06);
  }

  /* Small screens: stack buttons */
  @media (max-width: 480px) {
    .gcash-qr-card .action-buttons { flex-direction: column; gap: 8px; }
    .gcash-qr-card .action-btn { width: 100%; min-width: 0; }
    .transaction-btn { width: 100% !important; }
  }

  /* Responsive Design */
  @media (max-width: 768px) {
    .payment-content {
      grid-template-columns: 1fr;
    }

    .bill-summary {
      border-right: none;
      border-bottom: 1px solid var(--gray-200);
    }

    .payment-tabs {
      flex-direction: column;
    }

    .action-buttons {
      flex-direction: column;
      align-items: center;
    }

    .payment-title {
      font-size: 1.5rem;
    }

    .amount-value {
      font-size: 2.5rem;
    }

    .bill-info {
      flex-direction: column;
      gap: 0.25rem;
    }

    .manual-payment {
      padding: 1rem;
      max-width: 100%;
    }

    .gcash-info {
      padding: 1rem;
    }

    .qr-placeholder {
      width: 120px;
      height: 120px;
    }

    .transaction-btn {
      max-width: 100%;
    }
  }

  /* ===== Responsive Layout: Laptop / Tablet / Mobile =====
     Breakpoints:
     - Desktop: 1200px and above (default styles)
     - Laptop: 992px - 1199px
     - Tablet: 768px - 991px
     - Mobile: <768px
  */

  /* Laptops: keep two columns but reduce paddings and font sizes slightly */
  @media (min-width: 992px) and (max-width: 1199px) {
    .payment-container { max-width: 1100px; padding: 0 12px; }
    .payment-header { padding: 1.6rem; }
    .payment-title { font-size: 1.6rem; }
    .payment-content { grid-template-columns: 1fr 1.25fr; min-height: 560px; }
    .bill-summary { padding: 1.5rem; }
    .payment-methods { padding: 1.5rem; }
    .amount-value { font-size: 2.4rem; }
    .gcash-qr-card, .gcash-number-card, .gcash-qr-wrapper { max-width: 300px; }
    .gcash-actions, .btn-create, .transaction-btn { max-width: 300px; }
    .pay-bill-item { padding: 0.9rem; gap: 0.75rem; }
    .bill-month { font-size: 0.95rem; }
    .bill-amount { font-size: 1rem; }
  }

  /* Tablet: switch to single column stacking and breathe vertically */
  @media (min-width: 768px) and (max-width: 991px) {
    .payment-container { max-width: 760px; margin: 0 12px; }
    .payment-header { padding: 1.25rem; }
    .payment-title { font-size: 1.5rem; }
    .payment-content { grid-template-columns: 1fr; min-height: auto; }
    .bill-summary { border-right: none; border-bottom: 1px solid var(--gray-200); padding: 1rem; }
    .payment-methods { padding: 1rem; }
    .summary-card, .bill-breakdown, .invoice-details, .gcash-qr-card, .gcash-number-card {
      max-width: 100%;
      margin-left: 0;
      margin-right: 0;
    }

    /* Stack bill items vertically and make radio area larger */
    .pay-bill-item {
      flex-direction: row;
      align-items: center;
      gap: 0.75rem;
      padding: 0.85rem;
    }
    .bill-amount { font-size: 1rem; }

    /* QR and action buttons sit centered and full-width but constrained */
    .gcash-qr-wrapper { max-width: 280px; min-height: 150px; padding: 14px; }
    .gcash-actions { max-width: 280px; gap:10px; }
    .gcash-actions .btn { padding: 10px; font-size: 0.95rem; }
    .btn-create, .transaction-btn { max-width: 280px; margin-top: 14px; }

    /* Make gateway pay button comfortable on tablet */
    .gcash-pay-btn { padding: 1rem; font-size: 1rem; }
  }

  /* Mobile phones: stack everything, full-width controls */
  @media (max-width: 767px) {
    .payment-container { border-radius: 0.5rem; margin: 0 8px; box-shadow: none; }
    .payment-header { padding: 1rem; }
    .payment-title { font-size: 1.25rem; }
    .payment-subtitle { font-size: 0.95rem; }
    .payment-content { grid-template-columns: 1fr; gap: 10px; padding-bottom: 20px; }

    /* Make panels full-width and remove side margins */
    .bill-summary, .payment-methods {
      padding: 12px;
      border-right: none;
      border-bottom: none;
    }

    /* Bill item becomes stacked row with amount aligned right */
    .pay-bill-item {
      display: flex;
      flex-direction: row;
      align-items: flex-start;
      gap: 0.6rem;
      padding: 0.75rem;
      border-radius: 12px;
    }
    .bill-radio { width: 18px; height: 18px; margin-top: 6px; }
    .bill-details { flex: 1; min-width: 0; }
    .bill-month { font-size: 0.95rem; }
    .bill-info { font-size: 0.8rem; flex-direction: column; gap: 6px; }
    .bill-amount { font-size: 0.98rem; margin-left: 8px; white-space: nowrap; }

    /* Amount display reduction */
    .amount-value { font-size: 2rem; }

    /* Make QR area smaller and centered */
    .gcash-qr-wrapper { max-width: 220px; min-height: 140px; padding: 12px; }
    .gcash-qr-wrapper img { max-width: 100%; max-height: 100%; }

    /* Actions become stacked full-width buttons */
    .gcash-actions {
      flex-direction: column;
      align-items: stretch;
      gap: 8px;
      width: 100%;
      max-width: 100%;
      margin-top: 8px;
      padding: 0;
    }
    .gcash-actions .btn { width: 100%; justify-content: center; padding: 10px; font-size: 0.95rem; }

    /* Create button stretch full width */
    .btn-create, .transaction-btn, .gcash-pay-btn {
      width: 100% !important;
      max-width: none !important;
      padding: 12px 14px;
      font-size: 1rem;
    }

    /* Ensure pills / card elements have roomy gaps */
    .summary-card, .bill-breakdown, .gcash-number-card, .gcash-qr-card {
      padding: 12px;
      margin-bottom: 12px;
    }

    /* Reduce excessive shadows on mobile for performance/readability */
    .summary-card, .gcash-qr-card, .gcash-number-card {
      box-shadow: 0 6px 18px rgba(16,24,40,0.04);
    }

    /* Improve tap targets */
    .tab-btn, .gcash-pay-btn, .action-btn, .btn-create { min-height: 44px; }

    /* Avoid horizontal overflow */
    .payment-container, .payment-content, .bill-summary, .payment-methods {
      overflow: visible;
    }
  }

  /* Small enhancement: make sure very large screens get comfortable max-width */
  @media (min-width: 1400px) {
    .payment-container { max-width: 1280px; }
  }

  /* Accessibility tweak: increase hit area for small radios on touch devices */
  @media (hover: none) and (pointer: coarse) {
    .bill-radio { width: 22px; height: 22px; }
    .pay-bill-item { padding: 12px; }
  }

  /* -------------------------
     Restore Polished Manual Payment UI
     (Append at end of payments.php <style>)
     ------------------------- */

  /* Container card - restore original visual */
  .payments-panel.gcash-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 8px 28px rgba(17,24,39,0.06);
    padding: 18px;
    margin-bottom: 18px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    box-sizing: border-box;
  }

  /* Number card (prominent badge) */
  .gcash-number-card {
    max-width: 360px;
    width: 100%;
    margin: 0 auto 14px;
    padding: 1.25rem;
    background: linear-gradient(180deg, #ffffff, #fbfdff);
    border: 1px solid rgba(15,23,42,0.06);
    border-radius: 14px;
    box-shadow: 0 6px 18px rgba(16,24,40,0.06);
    text-align: center;
    box-sizing: border-box;
  }

  .gcash-number-card .gcash-number {
    display: inline-block;
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--primary-color);
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(90deg, rgba(102,126,234,0.06), rgba(72,187,120,0.04));
    padding: 0.5rem 0.9rem;
    border-radius: 10px;
    margin-top: 6px;
    letter-spacing: 0.6px;
  }

  /* QR wrapper (framed) */
  .gcash-qr-wrapper,
  .gcash-qr-card .qr-placeholder {
    width: 100%;
    max-width: 360px;
    height: auto;
    min-height: 170px;
    padding: 14px;
    margin: 0 auto 14px;
    display:flex;
    align-items:center;
    justify-content:center;
    background: linear-gradient(180deg, #fbfdff, #ffffff);
    border-radius: 12px;
    border: 2px dashed rgba(99,102,241,0.08);
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.6);
    overflow: hidden;
    box-sizing: border-box;
  }

  /* ensure QR image fits neatly */
  .gcash-qr-wrapper img,
  .gcash-qr-card .qr-placeholder img,
  img.gcash-qr-img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 6px 18px rgba(31,59,131,0.06);
  }

  /* Actions row — keep two equal buttons on wide screens */
  .gcash-actions,
  .gcash-qr-card .action-buttons {
    display:flex;
    gap:12px;
    justify-content:center;
    align-items:center;
    width:100%;
    max-width:360px;
    margin-top:10px;
    box-sizing: border-box;
  }

  /* Buttons styling - keep original look */
  .gcash-actions .btn,
  .gcash-qr-card .action-btn {
    flex: 1 1 50%;
    padding: 10px 14px;
    border-radius: 10px;
    font-weight: 700;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap: 8px;
    cursor: pointer;
    box-shadow: 0 6px 16px rgba(17,24,39,0.03);
    text-decoration: none;
  }

  /* download = primary, copy = secondary */
  .gcash-actions .btn-copy,
  .gcash-qr-card .action-btn.secondary {
    background: #fff;
    border: 1px solid rgba(15,23,42,0.06);
    color: var(--gray-700);
  }

  .gcash-actions .btn-download,
  .gcash-qr-card .action-btn.primary {
    background: linear-gradient(90deg,#5663f1,#6d7dff);
    color:#fff;
    border: none;
    box-shadow: 0 8px 24px rgba(86,99,241,0.14);
  }

  /* Create Transaction button — prominent + consistent */
  .btn-create,
  #btnCreateTransaction,
  .transaction-btn {
    background: linear-gradient(90deg,#f07a2e,#ff8f3a);
    color:#fff;
    padding: 12px 20px;
    border-radius: 12px;
    width:100%;
    max-width:360px;
    border:none;
    box-shadow: 0 10px 30px rgba(240,122,46,0.16);
    margin-top: 16px;
    font-weight:700;
    cursor:pointer;
    display:block;
    box-sizing: border-box;
  }

  /* Disabled look */
  .btn-create[disabled],
  .gcash-actions .btn[disabled],
  .gcash-qr-card .action-btn:disabled,
  .gcash-actions .btn[aria-disabled="true"],
  #btnDownloadQr[aria-disabled="true"] {
    opacity: 0.55;
    cursor: not-allowed;
    box-shadow: none;
    pointer-events: none;
  }

  /* Small screens: stack buttons but keep visuals */
  @media (max-width: 767px) {
    .gcash-qr-wrapper,
    .gcash-qr-card .qr-placeholder { max-width: 260px; min-height: 140px; padding: 12px; }

    .gcash-actions,
    .gcash-qr-card .action-buttons {
      flex-direction: column;
      gap: 10px;
      align-items: stretch;
      width: 100%;
      max-width: 100%;
    }

    .gcash-actions .btn,
    .gcash-qr-card .action-btn {
      width: 100%;
      flex: none;
      padding: 10px;
    }

    .btn-create,
    #btnCreateTransaction,
    .transaction-btn {
      max-width: 100%;
      padding: 12px;
      font-size: 1rem;
    }
  }

  /* Small defensive rule: ensure our manual card CSS wins over any earlier generic overrides */
  .payments-panel.gcash-card,
  .gcash-number-card,
  .gcash-qr-wrapper,
  .gcash-actions,
  .btn-create,
  .gcash-qr-card {
    transition: none !important;
  }
</style>

<div id="paymentsRoot" class="payment-container">
  <!-- Payment Header -->
  <div class="payment-header">
    <div class="payment-header-content">
      <h2 class="payment-title">💳 Complete Your Payment</h2>
      <p class="payment-subtitle">Select a bill and choose payment method</p>
    </div>
  </div>

  <!-- Main Payment Content -->
  <div class="payment-content">
    <!-- Left Panel: Bill Summary -->
    <div class="bill-summary">
      <!-- Bill Selection -->
      <div class="bill-selection">
        <div class="selection-header">
          <h3 class="selection-title">Select Bill to Pay</h3>
        </div>
        <div id="billListContainer">
          <!-- Bills will be loaded here via AJAX -->
          <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading bills...</span>
            </div>
            <p class="mt-2 text-muted">Loading your bills...</p>
          </div>
        </div>
      </div>

      <!-- Amount Display -->
      <div class="summary-card">
        <?php if (empty($bills)): ?>
          <div class="no-bills-overlay">
            <div class="no-bills-icon">
              <i class="fas fa-check-circle"></i>
            </div>
            <div class="no-bills-text">
              <h4>No Pending Bills</h4>
              <p>All bills are up to date!</p>
            </div>
          </div>
        <?php endif; ?>
        
        <div class="amount-display <?= empty($bills) ? 'disabled-content' : '' ?>">
          <div class="amount-label">Total Amount Due</div>
          <div class="amount-value">
            <span class="peso-badge" aria-hidden="true">₱</span>
            <span id="totalAmount">0.00</span>
          </div>
        </div>
      </div>

      <!-- Bill Breakdown -->
      <div class="bill-breakdown <?= empty($bills) ? 'disabled-content' : '' ?>">
        <div id="selectedBillsBreakdown">
          <div class="breakdown-item">
            <span class="breakdown-label">Carryover from Previous Bills</span>
            <span class="breakdown-value"><span id="carryoverAmount">0.00</span></span>
          </div>
          <div class="breakdown-item">
            <span class="breakdown-label">Current Charges</span>
            <span class="breakdown-value"><span id="currentAmount">0.00</span></span>
          </div>
          <div class="breakdown-item">
            <span class="breakdown-label">Payments Made</span>
            <span class="breakdown-value">-<span id="paymentsAmount">0.00</span></span>
          </div>
          <div class="breakdown-item">
            <span class="breakdown-label">Net Due</span>
            <span class="breakdown-value"><span id="netDueAmount">0.00</span></span>
          </div>
        </div>
        <div class="breakdown-item">
          <span class="breakdown-label">Service Fee</span>
          <span class="breakdown-value">₱<span id="serviceFee">0.00</span></span>
        </div>
        <div class="breakdown-item">
          <span class="breakdown-label">Total Amount</span>
          <span class="breakdown-value">₱<span id="finalTotal">0.00</span></span>
        </div>
      </div>
    </div>

    <!-- Right Panel: Payment Methods -->
    <div class="payment-methods">
      <!-- Methods Header -->
      <div class="methods-header">
        <h3 class="methods-title">Payment Methods</h3>
        <div class="security-badge">
          <i class="fas fa-shield-alt"></i>
          <span>Secure</span>
        </div>
      </div>

      <!-- Payment Tabs -->
      <div class="payment-tabs">
        <button type="button" class="tab-btn active" id="creditTab" disabled>
          <i class="fas fa-credit-card me-2"></i>
          GCash Gateway
        </button>
        <button type="button" class="tab-btn" id="mobileTab" disabled>
          <i class="fas fa-mobile-alt me-2"></i>
          Manual Payment
        </button>
      </div>

      <!-- GCash Gateway Form -->
      <div id="creditContent" class="payment-form">
        <form id="payForm" action="<?= site_url('users/createCheckout') ?>" method="post">
          <input type="hidden" name="total_amount" id="hiddenTotalAmount" value="0">
          <input type="hidden" name="bill_ids" id="hiddenBillIds" value="">
          <input type="hidden" name="billing_id" id="billing_id" value="">
          
          <!-- Minimal GCash Gateway -->
          <div class="gcash-gateway">
            <div class="gateway-header">
              <div class="gcash-logo">
                <i class="fas fa-mobile-alt"></i>
                <span>GCash</span>
              </div>
              <p>Secure payment via GCash</p>
            </div>

                <div class="payment-amount">
                  <span class="amount-label">You will pay</span>
                  <div class="amount-display">
                    <span id="gatewayAmount">0.00</span>
                  </div>
                </div>

            <div class="payment-steps">
              <div class="step-item">
                <span class="step-icon">1</span>
                <span>Select a bill to pay</span>
              </div>
              <div class="step-item">
                <span class="step-icon">2</span>
                <span>Click "Pay with GCash"</span>
              </div>
              <div class="step-item">
                <span class="step-icon">3</span>
                <span>Complete payment in GCash</span>
              </div>
            </div>

            <div class="security-note">
              <i class="fas fa-shield-check"></i>
              <span>SSL encrypted and secure</span>
            </div>
          </div>

          <button type="submit" class="gcash-pay-btn" id="gatewayPayBtn" disabled>
            <i class="fas fa-mobile-alt"></i>
            <span>Pay with GCash</span>
            <i class="fas fa-arrow-right"></i>
          </button>
        </form>
      </div>

      <!-- Manual Payment -->
      <div id="mobileContent" class="payment-form hidden">
        <!-- GCash Manual Payment Card -->
        <div class="payments-panel gcash-card" aria-label="Manual Payment via GCash">
          <div class="card-title">Pay via GCash</div>

          <div id="gcashNumber" class="gcash-number" aria-live="polite">—</div>

          <div id="gcashQrWrapper" class="gcash-qr-wrapper" role="img" aria-label="GCash QR code">
            <div class="gcash-qr-empty">QR not uploaded</div>
          </div>

          <div class="gcash-actions" role="group" aria-label="GCash actions">
            <button type="button" class="btn btn-copy" id="btnCopyNumber" disabled>Copy Number</button>
            <a id="btnDownloadQr" class="btn btn-download" href="#" download="gcash-qr.png" aria-disabled="true">Download QR</a>
          </div>

          <button type="button" class="btn-create" id="btnCreateTransaction" disabled>Create Transaction</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
(function initPaymentsView() {
  const root = document.getElementById('paymentsRoot') || document;
  const serviceFeeRate = 1.99; // Fixed service fee
  // Toggle whether service fee should apply (manual payments waive it)
  let applyServiceFee = true;

  // Client-side state
  let billsData = [];
  let selectedBillId = null;

  // Prevent double-initialization if reloaded
  if (root.dataset.initialized === '1') return;
  root.dataset.initialized = '1';

  console.log('Payment interface with single bill selection initialized');

  // Persistent DOM elements (static parts)
  const totalAmountElement = root.querySelector('#totalAmount');
  const serviceFeeElement = root.querySelector('#serviceFee');
  const finalTotalElement = root.querySelector('#finalTotal');
  const gatewayAmountElement = root.querySelector('#gatewayAmount');
  const hiddenTotalAmount = root.querySelector('#hiddenTotalAmount');
  const hiddenBillIds = root.querySelector('#hiddenBillIds');
  const gatewayPayBtn = root.querySelector('#gatewayPayBtn');
  const createTransactionBtn = root.querySelector('#btnCreateTransaction');
  const copyNumberBtn = root.querySelector('#copyNumber') || root.querySelector('#btnCopyNumber');
  const downloadQRBtn = root.querySelector('#downloadQR') || root.querySelector('#btnDownloadQr');
  const creditTab = root.querySelector('#creditTab');
  const mobileTab = root.querySelector('#mobileTab');
  const creditContent = root.querySelector('#creditContent');
  const mobileContent = root.querySelector('#mobileContent');

  // Calculate totals based on selected bill
  function updateTotals() {
    const selectedRadio = root.querySelector('.bill-radio:checked');

    if (!selectedRadio) {
      // Reset displays (numbers only; single peso badge shown near total)
      document.getElementById('carryoverAmount').textContent = `0.00`;
      document.getElementById('currentAmount').textContent = `0.00`;
      document.getElementById('paymentsAmount').textContent = `-0.00`;
      document.getElementById('netDueAmount').textContent = `0.00`;
      if (totalAmountElement) totalAmountElement.textContent = '0.00';
      if (gatewayAmountElement) gatewayAmountElement.textContent = '0.00';
      if (hiddenTotalAmount) hiddenTotalAmount.value = '0';
      if (hiddenBillIds) hiddenBillIds.value = '';
      const hiddenBillingId = root.querySelector('#billing_id');
      if (hiddenBillingId) hiddenBillingId.value = '';
      if (gatewayPayBtn) gatewayPayBtn.disabled = true;
      if (createTransactionBtn) createTransactionBtn.disabled = true;
      updateManualPaymentActions();
      return;
    }

    const billId = selectedRadio.value;
    selectedBillId = billId;
    const bill = billsData.find(b => String(b.id) === String(billId)) || null;

    // Compute safe numeric values
    const carryover = bill ? parseFloat(bill.carryover || 0) : 0.0;
    const current = bill ? parseFloat(bill.amount_due || 0) : 0.0;
    const payments = bill ? parseFloat(bill.paymentsMade || 0) : 0.0;
    let netDue = carryover + current - payments;
    if (isNaN(netDue) || netDue < 0) netDue = Math.max(0, netDue || 0);

    const serviceFee = parseFloat((applyServiceFee ? serviceFeeRate : 0) || 0);
    const total = parseFloat((netDue + serviceFee).toFixed(2));

    // Update elements (numbers only; single peso badge shown near total)
    document.getElementById('carryoverAmount').textContent = `${carryover.toFixed(2)}`;
    document.getElementById('currentAmount').textContent = `${current.toFixed(2)}`;
    document.getElementById('paymentsAmount').textContent = `-${payments.toFixed(2)}`;
    document.getElementById('netDueAmount').textContent = `${netDue.toFixed(2)}`;

    if (totalAmountElement) totalAmountElement.textContent = total.toFixed(2);
    if (serviceFeeElement) serviceFeeElement.textContent = serviceFee.toFixed(2);
    if (finalTotalElement) finalTotalElement.textContent = total.toFixed(2);
    if (gatewayAmountElement) gatewayAmountElement.textContent = total.toFixed(2);

    // Update hidden form fields
    if (hiddenTotalAmount) hiddenTotalAmount.value = total.toFixed(2);
    if (hiddenBillIds) hiddenBillIds.value = String(selectedBillId);
    const hiddenBillingId = root.querySelector('#billing_id');
    if (hiddenBillingId) hiddenBillingId.value = String(selectedBillId);

    // Enable/disable payment buttons and tabs
    const hasSelection = !!selectedRadio;
    if (gatewayPayBtn) gatewayPayBtn.disabled = !hasSelection;
    if (createTransactionBtn) createTransactionBtn.disabled = !hasSelection;
    updateManualPaymentActions();
    if (creditTab) creditTab.disabled = !hasSelection;
    if (mobileTab) mobileTab.disabled = !hasSelection;

    console.log('Totals updated:', { netDue, serviceFee, total, selectedBill: selectedBillId });
  }

  // Update manual payment actions (copy/download) based on both bill selection AND GCash data
  function updateManualPaymentActions() {
    const selectedRadio = root.querySelector('.bill-radio:checked');
    const hasSelection = !!selectedRadio;

    const gcashNumberEl = document.getElementById('gcashNumber');
    const qrImg = document.querySelector('#gcashQrWrapper img');

    const hasGcashNumber = gcashNumberEl && gcashNumberEl.textContent.trim() !== '—' && gcashNumberEl.textContent.trim() !== '';
    const hasQrCode = qrImg && qrImg.src;

    // Copy button
    if (copyNumberBtn) {
      copyNumberBtn.disabled = !(hasSelection && hasGcashNumber);
      copyNumberBtn.style.opacity = copyNumberBtn.disabled ? '0.5' : '1';
      copyNumberBtn.style.cursor = copyNumberBtn.disabled ? 'not-allowed' : 'pointer';
    }

    // Download button
    if (downloadQRBtn) {
      downloadQRBtn.disabled = !(hasSelection && hasQrCode);
      downloadQRBtn.style.pointerEvents = (hasSelection && hasQrCode) ? 'auto' : 'none';
      downloadQRBtn.style.opacity = downloadQRBtn.disabled ? '0.5' : '1';
      downloadQRBtn.style.cursor = downloadQRBtn.disabled ? 'not-allowed' : 'pointer';
      if (!downloadQRBtn.disabled && qrImg) {
        downloadQRBtn.href = qrImg.src;
        downloadQRBtn.setAttribute('download', 'gcash-qr.png');
      }
    }

    // Create Transaction button
    if (createTransactionBtn) {
      createTransactionBtn.disabled = !hasSelection;
      createTransactionBtn.style.opacity = createTransactionBtn.disabled ? '0.5' : '1';
      createTransactionBtn.style.cursor = createTransactionBtn.disabled ? 'not-allowed' : 'pointer';
    }
  }

  // Attach event listeners to bill items (called after AJAX load)
  function attachBillEventListeners() {
    const radioButtons = document.querySelectorAll('.bill-radio');
    const billItems = document.querySelectorAll('.pay-bill-item');

    radioButtons.forEach(radio => {
      radio.addEventListener('change', function() {
        billItems.forEach(item => item.classList.remove('selected'));
        const billItem = this.closest('.pay-bill-item');
        if (billItem && this.checked) {
          billItem.classList.add('selected');
        }
        updateTotals();
      });
    });

    billItems.forEach(item => {
      item.addEventListener('click', function(e) {
        if (e.target.type === 'radio') return;
        const radio = this.querySelector('.bill-radio');
        if (radio) {
          radio.checked = true;
          radio.dispatchEvent(new Event('change'));
        }
      });
    });
  }

  // Tab switching
  function showCreditTab() {
    if (creditTab && creditTab.disabled) return;
    // Credit (gateway) payments include service fee
    applyServiceFee = true;
    if (creditTab) creditTab.classList.add('active');
    if (mobileTab) mobileTab.classList.remove('active');
    if (creditContent) creditContent.classList.remove('hidden');
    if (mobileContent) mobileContent.classList.add('hidden');
    try { localStorage.setItem('activePaymentTab', 'credit'); } catch (e) {}
    updateTotals();
  }

  function showMobileTab() {
    if (mobileTab && mobileTab.disabled) return;
    // Manual payments do not apply service fee
    applyServiceFee = false;
    if (mobileTab) mobileTab.classList.add('active');
    if (creditTab) creditTab.classList.remove('active');
    if (mobileContent) mobileContent.classList.remove('hidden');
    if (creditContent) creditContent.classList.add('hidden');
    try { localStorage.setItem('activePaymentTab', 'mobile'); } catch (e) {}
    updateTotals();
  }

  if (creditTab) creditTab.addEventListener('click', function(e) { e.preventDefault(); showCreditTab(); });
  if (mobileTab) mobileTab.addEventListener('click', function(e) { e.preventDefault(); showMobileTab(); });

  // Create Transaction button click handler
  if (createTransactionBtn) {
    createTransactionBtn.addEventListener('click', function(e) {
      e.preventDefault();

      if (this.disabled) return;

      const selectedRadio = root.querySelector('.bill-radio:checked');
      if (!selectedRadio) {
        notify('Please select a bill to pay', 'error');
        return;
      }

      // Use data-net-due if present, otherwise fall back to the hidden total
      const subtotal = parseFloat(selectedRadio.dataset.netDue || hiddenTotalAmount.value || '0') || 0;
      const billId = selectedRadio.value;

      window.location.href = `<?= base_url('users/manualTransaction') ?>?bill_id=${billId}&amount=${subtotal.toFixed(2)}`;
    });
  }

  // Copy GCash number
  if (copyNumberBtn) {
    copyNumberBtn.addEventListener('click', function(e) {
      e.preventDefault();
      if (this.disabled) return;
      const numberEl = document.getElementById('gcashNumber');
      const text = numberEl ? numberEl.textContent.trim() : '';
      if (!text || text === '—') return;

      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
          const originalText = this.textContent;
          this.textContent = '✓ Copied!';
          setTimeout(() => { this.textContent = originalText; }, 1500);
        }).catch(err => { console.error('Copy failed:', err); alert('Failed to copy number'); });
      } else {
        const ta = document.createElement('textarea');
        ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0'; document.body.appendChild(ta); ta.select();
        try { document.execCommand('copy'); const originalText = this.textContent; this.textContent = '✓ Copied!'; setTimeout(() => { this.textContent = originalText; }, 1500); }
        catch (err) { console.error('Copy failed:', err); alert('Failed to copy number'); }
        document.body.removeChild(ta);
      }
    });
  }

  // Download QR
  if (downloadQRBtn) {
    downloadQRBtn.addEventListener('click', function(e) {
      if (this.disabled) { e.preventDefault(); return false; }
      const qrImg = document.querySelector('#gcashQrWrapper img');
      if (!qrImg || !qrImg.src) { e.preventDefault(); return false; }
      this.href = qrImg.src; this.setAttribute('download', 'gcash-qr.png');
    });
  }

  // Form submit handling
  const payForm = root.querySelector('#payForm');
  if (payForm) {
    payForm.addEventListener('submit', function(e) {
      const selectedBill = root.querySelector('.bill-radio:checked');
      if (!selectedBill) { e.preventDefault(); notify('Please select a bill to pay', 'error'); return; }

      const btn = this.querySelector('.gcash-pay-btn');
      if (btn && !btn.disabled) {
        const original = btn.innerHTML;
        btn.innerHTML = '<span class="spinner me-2"></span>Processing...';
        btn.disabled = true;
        setTimeout(() => { btn.innerHTML = original; btn.disabled = false; }, 10000);
      }
    });
  }

  // Utility notification function
  function notify(message, type) {
    const el = document.createElement('div');
    el.style.cssText = `position: fixed; top: 20px; right: 20px; z-index: 9999; color: #fff; padding: 1rem 1.25rem; border-radius: 12px; box-shadow: 0 10px 20px rgba(0,0,0,0.12); transition: all .3s ease; background: ${type === 'success' ? '#48bb78' : type === 'error' ? '#f56565' : '#667eea'}; opacity: 0; transform: translateX(100%); font-weight: 600;`;
    el.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>${message}`;
    document.body.appendChild(el);
    requestAnimationFrame(() => { el.style.opacity = '1'; el.style.transform = 'translateX(0)'; });
    setTimeout(() => { el.style.opacity = '0'; el.style.transform = 'translateX(100%)'; setTimeout(() => el.remove(), 300); }, 3000);
  }

  // Load GCash settings
  async function loadGcashSettings() {
    try {
      const res = await fetch('<?= base_url('users/getGcashSettings') ?>', { credentials: 'same-origin' });
      const data = await res.json();
      if (!data) return;

      const numEl = document.getElementById('gcashNumber');
      const qrWrapper = document.getElementById('gcashQrWrapper');
      if (numEl) numEl.textContent = data.gcash_number ? data.gcash_number : '—';
      if (qrWrapper) {
        qrWrapper.innerHTML = '';
        if (data.qr_code_url) {
          const img = document.createElement('img'); img.src = data.qr_code_url; img.alt = 'GCash QR'; img.className = 'gcash-qr-img'; img.style.maxWidth = '100%'; img.style.height = 'auto'; qrWrapper.appendChild(img);
        } else {
          qrWrapper.innerHTML = '<div class="gcash-qr-empty"><i class="fas fa-qr-code"></i><p class="mb-0">No QR code set</p></div>';
        }
      }
      updateManualPaymentActions();
    } catch (err) { console.error('Failed to load GCash settings', err); }
  }

  // Load bills via AJAX (latest only)
  async function loadBills() {
    try {
      const res = await fetch('<?= base_url('users/getPaymentBillsAjax') ?>', { credentials: 'same-origin' });
      const data = await res.json();
      if (!data || data.status !== 'success') throw new Error('Failed to load bills');
      billsData = data.bills || [];
      const container = document.getElementById('billListContainer');

      // Filter to only pending or partial bills (normalize several variants)
      const pendingList = (billsData || []).filter(b => {
        const s = (b.status || '').toString().toLowerCase().trim();
        return s === 'pending' || s === 'partial' || s === 'partially_paid' || s === 'partial payment' || s === 'partially paid';
      });

      // If no pending/partial bills, show the no-pending UI
      if (!pendingList || pendingList.length === 0) {
        container.innerHTML = `<div class="text-center py-4"><i class="fas fa-check-circle fa-3x text-success mb-3"></i><h5>No Pending Bills</h5><p class="text-muted">All bills are up to date!</p></div>`;
        document.querySelectorAll('.disabled-content').forEach(el => el.classList.add('disabled-content'));
        // Clear billsData since nothing pending
        billsData = [];
        updateTotals();
        return;
      }

      // Use only the first pending/partial bill (the "only pending bill")
      const bill = pendingList[0];
      // Keep billsData limited to pendingList so other logic (if any) refers to pending bills
      billsData = pendingList;
      const netDueVal = parseFloat(bill.netDue || ((parseFloat(bill.carryover||0) + parseFloat(bill.amount_due||0)) - parseFloat(bill.paymentsMade||0))) || 0;
      const netDue = netDueVal.toFixed(2);

      const billHtml = `
        <div class="pay-bill-item" data-bill-id="${bill.id}" data-net-due="${netDue}" data-carryover="${bill.carryover||0}" data-current="${bill.amount_due||0}" data-payments="${bill.paymentsMade||0}">
          <input type="radio" class="bill-radio" name="selected_bill" id="bill_${bill.id}" value="${bill.id}" data-net-due="${netDue}">
          <div class="bill-details">
            <div class="bill-month">${new Date(bill.due_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long' })}</div>
            <div class="bill-info"><span>Bill #${bill.bill_no}</span>
              <span class="due-date ${new Date(bill.due_date) < new Date() ? 'overdue' : ''}">Due: ${new Date(bill.due_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })} ${new Date(bill.due_date) < new Date() ? '(Overdue)' : ''}</span>
            </div>
          </div>
          <div class="bill-amount">${parseFloat(netDue).toFixed(2)}</div>
        </div>
      `;

      container.innerHTML = billHtml;
      attachBillEventListeners();

      // Auto-select
      const firstRadio = container.querySelector('.bill-radio');
      if (firstRadio) { firstRadio.checked = true; firstRadio.dispatchEvent(new Event('change')); }

      console.log('Loaded latest bill successfully');
    } catch (err) {
      console.error('Failed to load bills:', err);
      document.getElementById('billListContainer').innerHTML = `<div class="text-center py-4"><i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i><h5>Failed to Load Bills</h5><p class="text-muted">Please refresh the page or contact support.</p><button class="btn btn-primary btn-sm" onclick="loadBills()">Retry</button></div>`;
    }
  }

  // Initialize
  updateTotals();
  loadGcashSettings();
  loadBills();

  // Restore tab selection
  const savedTab = (function() { try { return localStorage.getItem('activePaymentTab'); } catch (e) { return null; } })();
  if (savedTab === 'mobile') showMobileTab(); else showCreditTab();
})();
</script>