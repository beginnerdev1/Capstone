<style>
  :root {
    --primary: #667eea;
    --primary-dark: #5568d3;
    --success: #10b981;
    --success-dark: #059669;
    --warning: #f59e0b;
    --danger: #ef4444;
    --border: #e5e7eb;
    --dark: #1f2937;
    --light: #f9fafb;
    --muted: #6b7280;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
  }

  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  .gcash-wrapper {
    min-height: 100%;
    padding: 2rem 1rem;
  }

  .gcash-container {
    max-width: 700px;
    margin: 0 auto;
    background: white;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    border: 1px solid var(--border);
    overflow: hidden;
  }

  .gcash-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    padding: 2.5rem 2rem;
    text-align: center;
    color: white;
  }

  .gcash-header-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    display: inline-block;
    opacity: 0.9;
  }

  .gcash-header h2 {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    letter-spacing: -0.5px;
  }

  .gcash-header p {
    font-size: 0.95rem;
    opacity: 0.95;
    margin: 0;
  }

  .gcash-content {
    padding: 2.5rem 2rem;
  }

  .gcash-alert {
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.2);
    color: var(--success-dark);
    padding: 1rem 1.25rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    display: none;
    align-items: center;
    gap: 0.75rem;
    animation: slideDown 0.3s ease-out;
  }

  .gcash-alert.show {
    display: flex;
  }

  .gcash-alert-icon {
    font-size: 1.25rem;
    flex-shrink: 0;
  }

  @keyframes slideDown {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .gcash-form-group {
    margin-bottom: 2rem;
  }

  .gcash-form-group:last-of-type {
    margin-bottom: 0;
  }

  .gcash-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 0.75rem;
    font-size: 0.95rem;
  }

  .gcash-label-icon {
    font-size: 1.1rem;
    color: var(--primary);
  }

  .gcash-required {
    color: var(--danger);
    font-weight: 700;
  }

  .gcash-input-wrapper {
    position: relative;
  }

  .gcash-form-group input[type="text"],
  .gcash-form-group input[type="file"] {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid var(--border);
    border-radius: 10px;
    font-size: 0.95rem;
    font-family: 'Poppins', sans-serif;
    background: var(--gray-50);
    transition: all 0.3s ease;
  }

  .gcash-form-group input[type="text"]::placeholder {
    color: var(--muted);
  }

  .gcash-form-group input[type="text"]:focus,
  .gcash-form-group input[type="file"]:focus {
    outline: none;
    border-color: var(--primary);
    background: white;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
  }

  .gcash-file-input-label {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 1.5rem;
    border: 2px dashed var(--border);
    border-radius: 10px;
    background: var(--gray-50);
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
  }

  .gcash-file-input-label:hover {
    border-color: var(--primary);
    background: rgba(102, 126, 234, 0.05);
  }

  .gcash-file-input-label.has-file {
    border-style: solid;
    border-color: var(--success);
    background: rgba(16, 185, 129, 0.05);
  }

  .gcash-file-input {
    display: none;
  }

  .gcash-file-icon {
    font-size: 1.5rem;
    color: var(--muted);
  }

  .gcash-file-input-label.has-file .gcash-file-icon {
    color: var(--success);
  }

  .gcash-file-text {
    font-weight: 500;
    color: var(--dark);
    font-size: 0.9rem;
  }

  .gcash-file-subtext {
    font-size: 0.8rem;
    color: var(--muted);
    display: block;
    margin-top: 0.25rem;
  }

  .gcash-preview {
    margin-top: 1.5rem;
    text-align: center;
    min-height: 200px;
    border: 2px dashed var(--border);
    border-radius: 10px;
    background: var(--gray-50);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    transition: all 0.3s ease;
  }

  .gcash-preview.has-image {
    border-style: solid;
    border-color: var(--success);
    background: white;
    padding: 1rem;
  }

  .gcash-preview img {
    max-width: 100%;
    max-height: 180px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .gcash-preview-placeholder {
    color: var(--muted);
    font-size: 3rem;
    opacity: 0.3;
  }

  .gcash-button-group {
    display: flex;
    gap: 1rem;
    margin-top: 2.5rem;
  }

  .gcash-btn {
    flex: 1;
    padding: 1rem;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
  }

  .gcash-btn-primary {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
  }

  .gcash-btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
  }

  .gcash-btn-primary:active:not(:disabled) {
    transform: translateY(0);
  }

  .gcash-btn-secondary {
    background: var(--gray-100);
    color: var(--dark);
    border: 1px solid var(--border);
  }

  .gcash-btn-secondary:hover:not(:disabled) {
    background: var(--border);
  }

  .gcash-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }

  .gcash-btn-icon {
    font-size: 1.1rem;
  }

  .gcash-helper-text {
    color: var(--muted);
    font-size: 0.85rem;
    margin-top: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .gcash-helper-text-icon {
    color: var(--primary);
  }

  .gcash-divider {
    height: 1px;
    background: var(--border);
    margin: 2rem 0;
  }

  .gcash-info-box {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(102, 126, 234, 0.02) 100%);
    border: 1px solid rgba(102, 126, 234, 0.1);
    border-radius: 12px;
    padding: 1.25rem;
    margin-top: 2rem;
  }

  .gcash-info-title {
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .gcash-info-text {
    color: var(--muted);
    font-size: 0.9rem;
    line-height: 1.6;
    margin: 0.5rem 0;
  }

  @media (max-width: 768px) {
    .gcash-wrapper {
      padding: 1rem;
    }

    .gcash-container {
      border-radius: 12px;
    }

    .gcash-header {
      padding: 2rem 1.5rem;
    }

    .gcash-header h2 {
      font-size: 1.5rem;
    }

    .gcash-header-icon {
      font-size: 2.5rem;
      margin-bottom: 0.75rem;
    }

    .gcash-content {
      padding: 1.5rem;
    }

    .gcash-button-group {
      flex-direction: column;
    }

    .gcash-btn {
      width: 100%;
    }
  }

  @media (max-width: 480px) {
    .gcash-header {
      padding: 1.5rem 1rem;
    }

    .gcash-header h2 {
      font-size: 1.25rem;
    }

    .gcash-header-icon {
      font-size: 2rem;
    }

    .gcash-content {
      padding: 1.25rem;
    }

    .gcash-form-group {
      margin-bottom: 1.5rem;
    }

    .gcash-label {
      font-size: 0.9rem;
    }

    .gcash-file-input-label {
      padding: 1.25rem;
      flex-direction: column;
    }
  }
</style>

<div class="gcash-wrapper">
  <div class="gcash-container">
    <!-- Header -->
    <div class="gcash-header">
      <div class="gcash-header-icon">💳</div>
      <h2>GCash Payment Setup</h2>
      <p>Configure your GCash details for manual payments</p>
    </div>

    <!-- Content -->
    <div class="gcash-content">
      <!-- Success Message -->
      <div class="gcash-alert" id="successMessage">
        <span class="gcash-alert-icon">✓</span>
        <span>Settings saved successfully!</span>
      </div>

      <!-- Form -->
      <form id="gcashForm" enctype="multipart/form-data">
        <!-- GCash Number -->
        <div class="gcash-form-group">
          <label class="gcash-label">
            <span class="gcash-label-icon">📱</span>
            GCash Number
            <span class="gcash-required">*</span>
          </label>
          <div class="gcash-input-wrapper">
            <input 
              type="text" 
              name="gcash_number" 
              placeholder="e.g., 09XX-XXX-XXXX" 
              required
              maxlength="20"
            >
          </div>
          <div class="gcash-helper-text">
            <span class="gcash-helper-text-icon">ℹ</span>
            Enter your active GCash number
          </div>
        </div>

        <!-- QR Code Upload -->
        <div class="gcash-form-group">
          <label class="gcash-label">
            <span class="gcash-label-icon">📸</span>
            QR Code
            <span class="gcash-required">*</span>
          </label>
          <label class="gcash-file-input-label" id="fileLabel">
            <span class="gcash-file-icon">📤</span>
            <span>
              <span class="gcash-file-text">Click to upload or drag</span>
              <span class="gcash-file-subtext">PNG, JPG up to 5MB</span>
            </span>
          </label>
          <input 
            type="file" 
            name="gcash_qr" 
            class="gcash-file-input"
            id="gcashQrInput"
            accept="image/*"
            required
          >
          <div class="gcash-preview" id="qrPreview">
            <div class="gcash-preview-placeholder">📷</div>
          </div>
        </div>

        <!-- Info Box -->
        <div class="gcash-info-box">
          <div class="gcash-info-title">
            <span>💡</span>
            Quick Tips
          </div>
          <div class="gcash-info-text">• Make sure your QR code is clear and readable</div>
          <div class="gcash-info-text">• Update your settings when you change accounts</div>
          <div class="gcash-info-text">• Customers will use this for manual payments</div>
        </div>

        <!-- Buttons -->
        <div class="gcash-button-group">
          <button type="reset" class="gcash-btn gcash-btn-secondary">
            <span>🔄</span>
            Clear
          </button>
          <button type="submit" class="gcash-btn gcash-btn-primary" id="submitBtn">
            <span>💾</span>
            Save Settings
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
(function() {
  const form = document.getElementById('gcashForm');
  const fileInput = document.getElementById('gcashQrInput');
  const fileLabel = document.getElementById('fileLabel');
  const preview = document.getElementById('qrPreview');
  const successMessage = document.getElementById('successMessage');
  const submitBtn = document.getElementById('submitBtn');

  // File input label click
  fileLabel.addEventListener('click', () => {
    fileInput.click();
  });

  // Drag and drop
  fileLabel.addEventListener('dragover', (e) => {
    e.preventDefault();
    fileLabel.style.borderColor = 'var(--primary)';
    fileLabel.style.background = 'rgba(102, 126, 234, 0.1)';
  });

  fileLabel.addEventListener('dragleave', () => {
    fileLabel.style.borderColor = 'var(--border)';
    fileLabel.style.background = 'var(--gray-50)';
  });

  fileLabel.addEventListener('drop', (e) => {
    e.preventDefault();
    fileLabel.style.borderColor = 'var(--border)';
    fileLabel.style.background = 'var(--gray-50)';
    const files = e.dataTransfer.files;
    if (files.length > 0) {
      fileInput.files = files;
      handleFileSelect();
    }
  });

  // File select handler
  fileInput.addEventListener('change', handleFileSelect);

  function handleFileSelect() {
    const file = fileInput.files[0];
    if (!file) return;

    fileLabel.classList.add('has-file');
    fileLabel.innerHTML = `
      <span class="gcash-file-icon">✓</span>
      <span>
        <span class="gcash-file-text">${file.name}</span>
        <span class="gcash-file-subtext">${(file.size / 1024).toFixed(2)} KB</span>
      </span>
    `;

    const reader = new FileReader();
    reader.onload = (event) => {
      preview.classList.add('has-image');
      preview.innerHTML = `<img src="${event.target.result}" alt="QR Preview">`;
    };
    reader.readAsDataURL(file);
  }

  // Form submission
  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span>⏳</span> Saving...';

    const formData = new FormData(form);

    try {
      const response = await fetch('<?= base_url('admin/saveGcashSettings') ?>', {
        method: 'POST',
        body: formData
      });

      const data = await response.json();

      if (data.success) {
        successMessage.classList.add('show');
        form.reset();
        fileLabel.classList.remove('has-file');
        fileLabel.innerHTML = `
          <span class="gcash-file-icon">📤</span>
          <span>
            <span class="gcash-file-text">Click to upload or drag</span>
            <span class="gcash-file-subtext">PNG, JPG up to 5MB</span>
          </span>
        `;
        preview.classList.remove('has-image');
        preview.innerHTML = '<div class="gcash-preview-placeholder">📷</div>';

        setTimeout(() => {
          successMessage.classList.remove('show');
        }, 4000);
      } else {
        alert('Error: ' + (data.message || 'Failed to save settings'));
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Failed to save settings');
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = '<span>💾</span> Save Settings';
    }
  });

  // Form reset handler
  form.addEventListener('reset', () => {
    fileLabel.classList.remove('has-file');
    fileLabel.innerHTML = `
      <span class="gcash-file-icon">📤</span>
      <span>
        <span class="gcash-file-text">Click to upload or drag</span>
        <span class="gcash-file-subtext">PNG, JPG up to 5MB</span>
      </span>
    `;
    preview.classList.remove('has-image');
    preview.innerHTML = '<div class="gcash-preview-placeholder">📷</div>';
  });
})();
</script>