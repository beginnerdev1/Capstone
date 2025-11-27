<style>
.profile-badges {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
  grid-column: 2;
  grid-row: 2;
}
:root {
  --primary: #3b82f6;
  --primary-dark: #1d4ed8;
  --primary-light: #60a5fa;
  --secondary: #0ea5e9;
  --success: #10b981;
  --warning: #f59e0b;
  --danger: #ef4444;
  --border: #e5e7eb;
  --dark: #1f2937;
  --light: #f9fafb;
  --muted: #6b7280;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
  min-height: 100vh;
}

.profile-wrapper {
  padding: 2rem;
  max-width: 1000px;
  margin: 0 auto;
}

.profile-header {
  background: linear-gradient(135deg, #3b82f6 0%, #0ea5e9 100%);
  color: white;
  padding: 3.5rem;
  border-radius: 28px;
  margin-bottom: 2rem;
  box-shadow: 0 25px 60px rgba(59, 130, 246, 0.35);
  position: relative;
  overflow: hidden;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.profile-header::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -10%;
  width: 600px;
  height: 600px;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, transparent 70%);
  border-radius: 50%;
}

.profile-header::after {
  content: '';
  position: absolute;
  bottom: -20%;
  left: -5%;
  width: 400px;
  height: 400px;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, transparent 70%);
  border-radius: 50%;
}

.profile-header-content {
  position: relative;
  z-index: 1;
  display: grid;
  grid-template-columns: auto 1fr;
  grid-template-rows: auto;
  align-items: center;
  gap: 3rem;
}

.profile-avatar-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.2rem;
  flex-shrink: 0;
  grid-row: 1 / 3;
}

.profile-avatar {
  width: 140px;
  height: 140px;
  border-radius: 24px;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0.15) 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 4rem;
  border: 3px solid rgba(255, 255, 255, 0.4);
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.3);
  overflow: hidden;
  backdrop-filter: blur(10px);
  transition: all 0.3s ease;
}

.profile-avatar:hover {
  border-color: rgba(255, 255, 255, 0.6);
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.4);
  transform: scale(1.02);
}

.profile-avatar img {
  width: 100%;
  height: 100%;
  border-radius: 21px;
  object-fit: cover;
}

.avatar-upload-button {
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  border: 2.5px solid rgba(255, 255, 255, 0.4);
  color: white;
  padding: 1rem 1.8rem;
  border-radius: 16px;
  font-size: 0.95rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.8rem;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  letter-spacing: 0.3px;
  min-width: 180px;
}

.avatar-upload-button:hover {
  background: rgba(255, 255, 255, 0.3);
  border-color: rgba(255, 255, 255, 0.6);
  transform: translateY(-3px);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
}

.avatar-upload-button:active {
  transform: translateY(-1px);
}

.avatar-upload-input {
  display: none;
}

.button-icon {
  font-size: 1.2rem;
}

.profile-info {
  flex: 1;
}

.profile-title {
  font-size: 2.8rem;
  font-weight: 900;
  margin-bottom: 0.8rem;
  letter-spacing: -1px;
  line-height: 1.1;
}

.profile-email {
  font-size: 1.1rem;
  opacity: 0.95;
  margin-bottom: 1.8rem;
  font-weight: 500;
  letter-spacing: 0.3px;
}

.profile-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.8rem;
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  padding: 0.85rem 1.5rem;
  border-radius: 18px;
  font-size: 1rem;
  font-weight: 700;
  border: 2.5px solid rgba(255, 255, 255, 0.35);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
  transition: all 0.3s ease;
}

.profile-badge:hover {
  background: rgba(255, 255, 255, 0.3);
  border-color: rgba(255, 255, 255, 0.5);
  transform: translateY(-2px);
}

.badge-icon {
  font-size: 1.3rem;
}

@media (max-width: 1024px) {
  .profile-header {
    padding: 2.5rem;
  }

  .profile-header-content {
    gap: 2rem;
  }

  .profile-avatar {
    width: 130px;
    height: 130px;
    font-size: 3.5rem;
    border-radius: 22px;
  }

  .avatar-upload-button {
    padding: 0.9rem 1.6rem;
    font-size: 0.9rem;
    min-width: 160px;
  }

  .profile-title {
    font-size: 2.3rem;
  }

  .profile-email {
    font-size: 1rem;
  }
}

@media (max-width: 1024px) {
  .profile-header {
    padding: 2.5rem;
  }

  .profile-header-content {
    gap: 2rem;
  }

  .profile-avatar {
    width: 120px;
    height: 120px;
    font-size: 3.5rem;
  }

  .profile-title {
    font-size: 2.2rem;
  }
}

.profile-container {
  background: white;
  border-radius: 20px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
  border: 1px solid var(--border);
  overflow: hidden;
}

.form-section {
  padding: 2.5rem;
  border-bottom: 1px solid var(--border);
}

.form-section:last-child {
  border-bottom: none;
}

.section-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--dark);
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.section-icon {
  font-size: 1.5rem;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-label {
  font-weight: 600;
  color: var(--dark);
  margin-bottom: 0.6rem;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.form-required {
  color: var(--danger);
  font-weight: 700;
}

.form-input,
.form-select,
.form-textarea {
  padding: 0.9rem 1.1rem;
  border: 2px solid var(--border);
  border-radius: 10px;
  font-size: 0.9rem;
  font-family: 'Poppins', sans-serif;
  background: var(--light);
  color: var(--dark);
  transition: all 0.3s ease;
}

.form-input::placeholder {
  color: var(--muted);
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
  outline: none;
  border-color: var(--primary);
  background: white;
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

.form-textarea {
  resize: vertical;
  min-height: 100px;
}

.form-hint {
  font-size: 0.8rem;
  color: var(--muted);
  margin-top: 0.4rem;
}

.form-actions {
  padding: 2rem 2.5rem;
  background: var(--light);
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  flex-wrap: wrap;
}

.btn {
  padding: 0.9rem 2rem;
  border: none;
  border-radius: 10px;
  font-weight: 700;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
}

.btn-primary {
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  color: white;
  box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
}

.btn-secondary {
  background: white;
  color: var(--dark);
  border: 2px solid var(--border);
}

.btn-secondary:hover {
  background: var(--light);
  border-color: var(--primary);
}

.btn-success {
  background: linear-gradient(135deg, var(--success) 0%, #0d9488 100%);
  color: white;
  box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

.btn-success:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
}

.btn-danger {
  background: linear-gradient(135deg, var(--danger) 0%, #991b1b 100%);
  color: white;
  box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
}

.btn-danger:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
}

.alert {
  padding: 1.25rem;
  border-radius: 12px;
  margin-bottom: 2rem;
  display: none;
  align-items: center;
  gap: 1rem;
  animation: slideDown 0.3s ease-out;
}

.alert.show {
  display: flex;
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

.alert-success {
  background: rgba(16, 185, 129, 0.1);
  border: 1.5px solid rgba(16, 185, 129, 0.3);
  color: var(--success);
}

.alert-error {
  background: rgba(239, 68, 68, 0.1);
  border: 1.5px solid rgba(239, 68, 68, 0.3);
  color: var(--danger);
}

.alert-icon {
  font-size: 1.3rem;
  flex-shrink: 0;
}

/* Password requirements styling */
.req-list { margin-top: 0.4rem; display: grid; grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); gap: 0.25rem 1rem; }
.req-item { font-size: 0.85rem; display: flex; align-items: center; gap: 0.4rem; color: var(--danger); }
.req-item .req-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--danger); display: inline-block; }
.req-item.valid { color: var(--success); }
.req-item.valid .req-dot { background: var(--success); }

.form-grid.full-width {
  grid-template-columns: 1fr;
}

@media (max-width: 768px) {
  .profile-wrapper {
    padding: 1rem;
  }

  .profile-header {
    padding: 2rem 1.5rem;
    border-radius: 24px;
  }

  .profile-header-content {
    grid-template-columns: 1fr;
    text-align: center;
    gap: 1.5rem;
  }

  .profile-avatar-section {
    grid-row: auto;
    align-items: center;
  }

  .profile-info {
    width: 100%;
  }

  .profile-title {
    font-size: 1.8rem;
    margin-bottom: 0.6rem;
  }

  .profile-email {
    font-size: 1rem;
    margin-bottom: 1.2rem;
  }

  .profile-avatar {
    width: 120px;
    height: 120px;
    font-size: 3rem;
    border-radius: 20px;
  }

  .avatar-upload-button {
    min-width: 160px;
    padding: 0.85rem 1.5rem;
    font-size: 0.9rem;
  }

  .profile-badges {
    justify-content: center;
  }

  .profile-badge {
    font-size: 0.9rem;
    padding: 0.7rem 1.2rem;
    border-radius: 16px;
  }

  .form-section {
    padding: 1.5rem;
  }

  .section-title {
    font-size: 1.1rem;
  }

  .form-actions {
    padding: 1.5rem;
    flex-direction: column;
  }

  .btn {
    width: 100%;
    justify-content: center;
  }

  .form-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
}

@media (max-width: 480px) {
  .profile-wrapper {
    padding: 0.75rem;
  }

  .profile-header {
    padding: 1.5rem 1rem;
    border-radius: 20px;
  }

  .profile-header-content {
    gap: 1rem;
  }

  .profile-title {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
  }

  .profile-email {
    font-size: 0.95rem;
    margin-bottom: 1rem;
  }

  .profile-avatar {
    width: 110px;
    height: 110px;
    font-size: 2.8rem;
    border-radius: 18px;
  }

  .avatar-upload-button {
    min-width: auto;
    width: 100%;
    padding: 0.75rem 1.2rem;
    font-size: 0.85rem;
  }

  .button-icon {
    font-size: 1rem;
  }

  .profile-badge {
    font-size: 0.85rem;
    padding: 0.6rem 1rem;
    border-radius: 14px;
  }

  .badge-icon {
    font-size: 1.1rem;
  }

  .form-section {
    padding: 1.25rem;
  }

  .section-title {
    font-size: 1rem;
    margin-bottom: 1rem;
  }

  .section-icon {
    font-size: 1.25rem;
  }

  .form-label {
    font-size: 0.85rem;
  }

  .form-input,
  .form-select,
  .form-textarea {
    padding: 0.75rem 1rem;
    font-size: 0.85rem;
  }

  .form-hint {
    font-size: 0.75rem;
  }

  .form-actions {
    padding: 1rem;
    gap: 0.75rem;
  }

  .btn {
    padding: 0.75rem 1.5rem;
    font-size: 0.85rem;
  }
}
</style>

<div class="profile-wrapper">
  <!-- Alert Messages -->
  <div id="successAlert" class="alert alert-success">
    <span class="alert-icon">✓</span>
    <span id="successMessage">Profile updated successfully!</span>
  </div>
  <div id="errorAlert" class="alert alert-error">
    <span class="alert-icon">✕</span>
    <span id="errorMessage">Something went wrong. Please try again.</span>
  </div>

      <div class="profile-header">
    <div class="profile-header-content">
      <div class="profile-avatar-section">
        <div class="profile-avatar" id="profileAvatarDisplay">
          <?php
            $profilePic = $admin['profile_picture'] ?? '';
            $profilePicSrc = '';
            if (!empty($profilePic) && $profilePic !== 'default.png') {
                // If it's already a full URL or already includes uploads/, use as-is (prefix with base_url if relative)
                if (preg_match('#^https?://#i', $profilePic)) {
                    $profilePicSrc = $profilePic;
                } elseif (strpos($profilePic, 'uploads/') === 0 || strpos($profilePic, '/uploads/') === 0) {
                    $profilePicSrc = base_url(ltrim($profilePic, '/'));
                } else {
                    $profilePicSrc = base_url('uploads/profile/' . $profilePic);
                }
            }
          ?>
          <?php if ($profilePicSrc): ?>
            <img src="<?= esc($profilePicSrc) ?>" alt="Profile">
          <?php else: ?>
            <span>👤</span>
          <?php endif; ?>
        </div>
        <label class="avatar-upload-button">
          <span class="button-icon">📷</span>
          <span>Change Photo</span>
          <input type="file" class="avatar-upload-input" id="avatarInput" accept="image/*">
        </label>
      </div>
      <div class="profile-info">
        <h1 class="profile-title" id="profileName"><?= esc($admin['first_name'] ?? '') ?> <?= esc($admin['last_name'] ?? '') ?></h1>
        <p class="profile-email" id="profileEmailDisplay"><?= esc($admin['email'] ?? '') ?></p>
      </div>
      <!-- Removed profile badges (Account Verified / Secure Profile) per request -->
    </div>
  </div>

  <!-- Edit Profile Form -->
  <div class="profile-container">
    <form id="editProfileForm">
      <?= csrf_field() ?>
      <!-- Personal Information -->
      <div class="form-section">
        <h2 class="section-title">
          <span class="section-icon">👤</span>
          Personal Information
        </h2>
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">
              First Name <span class="form-required">*</span>
            </label>
            <input type="text" class="form-input" name="first_name" placeholder="Enter first name" required>
          </div>
          <div class="form-group">
            <label class="form-label">
              Middle Name
            </label>
            <input type="text" class="form-input" name="middle_name" placeholder="Enter middle name">
          </div>
          <div class="form-group">
            <label class="form-label">
              Last Name <span class="form-required">*</span>
            </label>
            <input type="text" class="form-input" name="last_name" placeholder="Enter last name" required>
          </div>
          <div class="form-group">
            <label class="form-label">
              Position
            </label>
            <input type="text" class="form-input" name="position" placeholder="Enter position" readonly>
            <div class="form-hint">Contact admin to change position</div>
          </div>
        </div>
      </div>

      <!-- Contact Information -->
      <div class="form-section">
        <h2 class="section-title">
          <span class="section-icon">📧</span>
          Contact Information
        </h2>
        <div class="form-grid full-width">
          <div class="form-group">
            <label class="form-label">
              Email Address <span class="form-required">*</span>
            </label>
            <input type="email" class="form-input" name="email" placeholder="Enter email address" required>
            <div class="form-hint">We'll send confirmations to this email</div>
          </div>
        </div>
      </div>

      <!-- Change Password (OTP Secured) -->
      <style>
        /* Password change steps: render as a simple vertical list (not button-like) */
          .pwd-steps { list-style: decimal inside; padding-left: 1rem; margin: 0 0 12px 0; }
          .pwd-steps li { display: block; margin: 0.35rem 0; padding: 0; color: var(--muted); font-size: 0.95rem; }
          .pwd-steps li .step-desc { display:block; color: var(--muted); font-size:0.9rem; margin-top:4px; }
          .pwd-steps li.active { color: var(--dark); font-weight: 700; }
          .pwd-steps li.active::before { content: '✓ '; color: var(--success); font-weight:700; }
      </style>

      <div class="form-section" id="passwordChangeSection">
        <h2 class="section-title">
          <span class="section-icon">🔐</span>
          Change Password (OTP Secured)
        </h2>
        <ol class="pwd-steps" id="pwdSteps">
          <li id="step1">Enter current password</li>
          <li id="step2">Click Send OTP</li>
          <li id="step3">Input OTP from email</li>
          <li id="step4">Enter new password</li>
          <li id="step5">Click Change Password</li>
        </ol>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Current Password <span class="form-required">*</span></label>
            <input type="password" class="form-input" id="currentPassword" placeholder="Enter current password" autocomplete="current-password">
            <div class="form-hint">Required to request an OTP</div>
          </div>
          <div class="form-group">
            <label class="form-label">New Password <span class="form-required">*</span></label>
            <input type="password" class="form-input" id="newPassword" placeholder="New password" disabled autocomplete="new-password">
            <div class="form-hint">Min 6 chars, capital letter, number & special character</div>
            <div class="req-list" id="pwdReqList">
              <div class="req-item" id="reqLength"><span class="req-dot"></span>At least 6 characters</div>
              <div class="req-item" id="reqUpper"><span class="req-dot"></span>At least one capital letter (A-Z)</div>
              <div class="req-item" id="reqNumber"><span class="req-dot"></span>At least one number (0-9)</div>
              <div class="req-item" id="reqSpecial"><span class="req-dot"></span>At least one special character (!@#$…)</div>
              <div class="req-item" id="reqMatch"><span class="req-dot"></span>Passwords match</div>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Confirm Password <span class="form-required">*</span></label>
            <input type="password" class="form-input" id="confirmPassword" placeholder="Confirm password" disabled autocomplete="new-password">
          </div>
          <div class="form-group">
            <label class="form-label">OTP Code <span class="form-required">*</span></label>
            <input type="text" class="form-input" id="otpCode" placeholder="6-digit OTP" disabled maxlength="6">
            <div class="form-hint" id="otpStatus">Request an OTP to begin</div>
          </div>
        </div>
        <div class="form-actions" style="justify-content:flex-start; background:transparent; padding:1rem 0 0;">
          <button type="button" class="btn btn-secondary" id="sendOtpBtn">📩 Send OTP</button>
          <button type="button" class="btn btn-success" id="changePasswordBtn" disabled>✅ Change Password</button>
        </div>
        <div id="passwordChangeMessages" style="margin-top:1rem;"></div>
      </div>

      <!-- Form Actions -->
      <div class="form-actions">
        <button type="reset" class="btn btn-secondary">
          🔄 Clear
        </button>
        <button type="submit" class="btn btn-primary" id="submitBtn">
          💾 Save Changes
        </button>
      </div>
    </form>
  </div>
</div>

<script>
const form = document.getElementById('editProfileForm');
const avatarInput = document.getElementById('avatarInput');
const avatarDisplay = document.getElementById('profileAvatarDisplay');
const successAlert = document.getElementById('successAlert');
const errorAlert = document.getElementById('errorAlert');
const submitBtn = document.getElementById('submitBtn');

// Handle avatar upload
avatarInput.addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = function(event) {
    avatarDisplay.innerHTML = `<img src="${event.target.result}" alt="Profile">`;
  };
  reader.readAsDataURL(file);
});

// Show alert
function showAlert(message, type) {
  const alert = type === 'success' ? successAlert : errorAlert;
  const messageEl = type === 'success' ? 
    document.getElementById('successMessage') : 
    document.getElementById('errorMessage');
  
  messageEl.textContent = message;
  alert.classList.add('show');
  
  setTimeout(() => {
    alert.classList.remove('show');
  }, 4000);
}

// Form submission
form.addEventListener('submit', async (e) => {
  e.preventDefault();
  
  submitBtn.disabled = true;
  submitBtn.innerHTML = '⏳ Saving...';

  const formData = new FormData(form);
  if (avatarInput.files[0]) {
    formData.append('profile_picture', avatarInput.files[0]);
  }

  try {
    const response = await fetch('<?= base_url('admin/updateProfile') ?>', {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      body: formData
    });

    const data = await response.json();

    if (data.success) {
      showAlert('Profile updated successfully!', 'success');
      // Update header with new name and email
      document.getElementById('profileName').textContent = 
        formData.get('first_name') + ' ' + formData.get('last_name');
      document.getElementById('profileEmailDisplay').textContent = formData.get('email');
      // If server indicates a redirect (e.g. password was changed), follow it
      if (data.redirect) {
        setTimeout(function(){ window.location.href = data.redirect; }, 800);
        return;
      }
    } else {
      showAlert(data.message || 'Failed to update profile', 'error');
    }
  } catch (error) {
    console.error('Error:', error);
    showAlert('An error occurred. Please try again.', 'error');
  } finally {
    submitBtn.disabled = false;
    submitBtn.innerHTML = '💾 Save Changes';
  }
});

  // Load profile data from PHP (safe handling for profile picture paths)
  document.addEventListener('DOMContentLoaded', () => {
    console.log('edit_profile script loaded at', new Date().toISOString());
    const adminData = <?= json_encode($admin ?? []) ?>;
    const baseUrl = '<?= rtrim(base_url(), "\\/") ?>';

    if (adminData) {
      // Populate form fields
      if (form.first_name) form.first_name.value = adminData.first_name || '';
      if (form.middle_name) form.middle_name.value = adminData.middle_name || '';
      if (form.last_name) form.last_name.value = adminData.last_name || '';
      if (form.email) form.email.value = adminData.email || '';
      if (form.position && adminData.position) form.position.value = adminData.position || '';

      // Update profile picture if exists — avoid double-prefixing uploads/
      const pic = adminData.profile_picture || '';
      if (pic && pic !== 'default.png') {
        let src = '';
        // Absolute URL
        if (/^https?:\/\//i.test(pic)) {
          src = pic;
        } else if (/^\//.test(pic)) {
          // Leading slash path like '/uploads/...'
          src = baseUrl + pic;
        } else if (/^uploads\//.test(pic)) {
          // Already includes uploads/ prefix
          src = baseUrl + '/' + pic;
        } else {
          // Assume filename only, use uploads/profile/ folder
          src = baseUrl + '/uploads/profile/' + pic;
        }

        avatarDisplay.innerHTML = `<img src="${src}" alt="Profile">`;
      }
    }
  });

// ================= PASSWORD CHANGE WITH OTP =================
const sendOtpBtn = document.getElementById('sendOtpBtn');
const changePasswordBtn = document.getElementById('changePasswordBtn');
const currentPasswordInput = document.getElementById('currentPassword');
const newPasswordInput = document.getElementById('newPassword');
const confirmPasswordInput = document.getElementById('confirmPassword');
const otpInput = document.getElementById('otpCode');
const otpStatus = document.getElementById('otpStatus');
const passwordChangeMessages = document.getElementById('passwordChangeMessages');

function getCsrfField() {
  const hidden = document.querySelector('#editProfileForm input[type="hidden"]');
  return hidden ? { name: hidden.getAttribute('name'), value: hidden.value } : null;
}

function showPwdMessage(msg, type='info') {
  passwordChangeMessages.innerHTML = `<div class="alert ${type === 'error' ? 'alert-error show' : 'alert-success show'}"><span class="alert-icon">${type==='error'?'✕':'✓'}</span><span>${msg}</span></div>`;
  setTimeout(()=>{ passwordChangeMessages.innerHTML=''; }, 5000);
}

sendOtpBtn.addEventListener('click', async () => {
  const currentPwd = currentPasswordInput.value.trim();
  if (!currentPwd) { showPwdMessage('Current password is required.', 'error'); return; }
  sendOtpBtn.disabled = true; sendOtpBtn.textContent = '⏳ Sending...';
  const csrf = getCsrfField();
  const fd = new FormData();
  if (csrf) fd.append(csrf.name, csrf.value);
  fd.append('current_password', currentPwd);
  try {
    const res = await fetch('<?= base_url('admin/requestPasswordOtp') ?>', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, body: fd });
    // Log response status for debugging
    console.log('sendOtp fetch response', { status: res.status, statusText: res.statusText, redirected: res.redirected, url: res.url });
    let data = null;
    try {
      data = await res.json();
    } catch (parseErr) {
      console.error('sendOtp: failed to parse JSON', parseErr);
      // fallback: read text for inspection
      try { const t = await res.text(); console.log('sendOtp response text:', t); } catch (tErr) { console.error('sendOtp: failed to read text', tErr); }
      data = null;
    }
    if (data.success) {
      otpStatus.textContent = 'OTP sent. Check your delivery channel.';
      newPasswordInput.disabled = false;
      confirmPasswordInput.disabled = false;
      otpInput.disabled = false;
      window.otpRequested = true;
      // Do not enable change button yet; wait for requirements to pass
      updatePasswordUI();
      showPwdMessage(data.message, 'success');
    } else {
      window.otpRequested = false;
      updateSteps();
      showPwdMessage(data.message || 'Failed to send OTP.', 'error');
    }
  } catch (e) {
    console.error('sendOtp error', e);
    showPwdMessage('Network error sending OTP.', 'error');
  } finally {
    sendOtpBtn.disabled = false; sendOtpBtn.textContent = '📩 Send OTP';
  }
});

changePasswordBtn.addEventListener('click', async () => {
  // Visible debug: add small banner so we can confirm the handler ran without relying on console
  try {
    let dbg = document.getElementById('ep-debug-banner');
    if (!dbg) {
      dbg = document.createElement('div'); dbg.id = 'ep-debug-banner';
      dbg.style.position = 'fixed'; dbg.style.right = '12px'; dbg.style.bottom = '12px'; dbg.style.background = 'rgba(0,0,0,0.7)'; dbg.style.color = 'white'; dbg.style.padding = '8px 10px'; dbg.style.borderRadius = '6px'; dbg.style.zIndex = '99999'; dbg.style.fontSize = '12px';
      document.body.appendChild(dbg);
    }
    dbg.textContent = 'changePassword clicked at ' + new Date().toLocaleTimeString();
  } catch (dbgErr) { console.warn('ep-debug-banner failed', dbgErr); }

  console.log('changePassword: handler invoked at', new Date().toISOString());
  const otp = otpInput.value.trim();
  const newPwd = newPasswordInput.value.trim();
  const confirmPwd = confirmPasswordInput.value.trim();
  if (!otp || !newPwd || !confirmPwd) { showPwdMessage('All password fields & OTP are required.', 'error'); return; }
  const csrf = getCsrfField();
  const fd = new FormData();
  if (csrf) fd.append(csrf.name, csrf.value);
  fd.append('otp_code', otp);
  fd.append('new_password', newPwd);
  fd.append('confirm_password', confirmPwd);
  changePasswordBtn.disabled = true; changePasswordBtn.textContent = '⏳ Changing...';
  try {
    const res = await fetch('<?= base_url('admin/changePassword') ?>', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, body: fd });
    // Read response text first to avoid json() parse errors when server returns HTML/redirects
    console.log('changePassword: fetch completed', { status: res.status, statusText: res.statusText, redirected: res.redirected, url: res.url });
    const text = await res.text();
    console.log('changePassword: response text (truncated 200 chars):', (text || '').substring(0,200));
    let data = null;
    try {
      data = text ? JSON.parse(text) : null;
    } catch (parseErr) {
      // Not JSON — likely an HTML page or redirect. Log for debugging and navigate if appropriate.
      console.error('changePassword: JSON parse error', parseErr, { status: res.status, textSample: (text||'').substring(0,200) });
      if (res.redirected || /<\/?html|<!doctype/i.test(text || '')) {
        console.log('changePassword: non-JSON HTML/redirect response detected, navigating to', res.url || '<?= base_url('admin') ?>');
        window.location.href = res.url || '<?= base_url('admin') ?>';
        return;
      }
      showPwdMessage('Unexpected server response.', 'error');
      return;
    }

    if (!res.ok) {
      console.warn('changePassword: response not ok', { status: res.status, statusText: res.statusText, data });
      showPwdMessage((data && data.message) ? data.message : ('Server error: ' + (res.statusText || res.status)), 'error');
      return;
    }

    if (data && data.success) {
      showPwdMessage('Password changed successfully.', 'success');
      currentPasswordInput.value='';
      newPasswordInput.value='';
      confirmPasswordInput.value='';
      otpInput.value='';
      newPasswordInput.disabled = true;
      confirmPasswordInput.disabled = true;
      otpInput.disabled = true;
      changePasswordBtn.disabled = true;
      otpStatus.textContent = 'Request a new OTP to change again';
      window.otpRequested = false;
      updateSteps();
      // If server returned a redirect, navigate there so the admin is taken to the dashboard.
      if (data.redirect) {
        setTimeout(function(){ window.location.href = data.redirect; }, 800);
        return;
      }
    } else {
      console.warn('changePassword: change failed', data);
      showPwdMessage((data && data.message) ? data.message : 'Failed to change password.', 'error');
    }
  } catch (e) {
    console.error('changePassword: caught error', e);
    showPwdMessage('Network error changing password.', 'error');
  } finally {
    changePasswordBtn.disabled = false; changePasswordBtn.textContent = '✅ Change Password';
  }
});

// Live password requirement checks
function hasSpecial(s){
  return /[!@#$%^&*()_\-+=\[\]{};:'",.<>\/?\\|`~]/.test(s);
}

function setReq(id, ok){
  const el = document.getElementById(id);
  if(!el) return;
  if(ok) el.classList.add('valid'); else el.classList.remove('valid');
}

function updatePasswordUI(){
  const pwd = newPasswordInput.value || '';
  const confirm = confirmPasswordInput.value || '';
  const otp = otpInput.value || '';
  const lenOK = pwd.length >= 6;
  const upperOK = /[A-Z]/.test(pwd);
  const numOK = /\d/.test(pwd);
  const specialOK = hasSpecial(pwd);
  const matchOK = confirm.length > 0 && pwd === confirm;

  setReq('reqLength', lenOK);
  setReq('reqUpper', upperOK);
  setReq('reqNumber', numOK);
  setReq('reqSpecial', specialOK);
  setReq('reqMatch', matchOK);

  const allOK = lenOK && upperOK && numOK && specialOK && matchOK;
  const otpReady = !otpInput.disabled && /^\d{6}$/.test(otp);
  changePasswordBtn.disabled = !(allOK && otpReady);
}

newPasswordInput.addEventListener('input', updatePasswordUI);
confirmPasswordInput.addEventListener('input', updatePasswordUI);
otpInput.addEventListener('input', updatePasswordUI);
</script>
<!-- Force-password-change modal -->
<div class="modal fade" id="forceChangeModal" tabindex="-1" aria-labelledby="forceChangeLabel" aria-hidden="true" style="display:none;">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="forceChangeLabel">Default password detected</h5>
      </div>
      <div class="modal-body">
        <p>Your account appears to be using the default password. For security, please change your password now.</p>
        <ol>
          <li>Enter your current password (if it's the default one: <code>123456</code>).</li>
          <li>Click <strong>Send OTP</strong>.</li>
          <li>Input the OTP sent to your email.</li>
          <li>Enter your new password (follow the requirements).</li>
          <li>Click <strong>Change Password</strong>.</li>
        </ol>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<script>
  // Show modal when server indicates forced password change
  document.addEventListener('DOMContentLoaded', function(){
    var forceChange = <?= (session()->getFlashdata('force_password_change') || session()->get('force_password_change')) ? 'true' : 'false' ?>;
    if (forceChange) {
      try { var m = new bootstrap.Modal(document.getElementById('forceChangeModal')); m.show(); } catch (e) { console.warn('Bootstrap modal failed to show', e); }
    }

    // Step UI logic
    window.otpRequested = false;
    // Expose updateSteps globally so other handlers can call it
    window.updateSteps = function(){
      var step1 = document.getElementById('step1');
      var step2 = document.getElementById('step2');
      var step3 = document.getElementById('step3');
      var step4 = document.getElementById('step4');
      var step5 = document.getElementById('step5');
      if (!step1) return;
      step1.classList.toggle('active', (currentPasswordInput.value || '').length > 0);
      step2.classList.toggle('active', window.otpRequested === true);
      step3.classList.toggle('active', (otpInput.value || '').length >= 6);
      // password valid check mirrors updatePasswordUI's criteria
      var pwd = newPasswordInput.value || '';
      var confirm = confirmPasswordInput.value || '';
      var lenOK = pwd.length >= 6;
      var upperOK = /[A-Z]/.test(pwd);
      var numOK = /\d/.test(pwd);
      var specialOK = /[!@#$%^&*()_\-+=\[\]{};:'",.<>\/\\?\\|`~]/.test(pwd);
      var matchOK = confirm.length > 0 && pwd === confirm;
      step4.classList.toggle('active', lenOK && upperOK && numOK && specialOK && matchOK);
      step5.classList.toggle('active', !changePasswordBtn.disabled);
    }

    // Hook into existing events
    currentPasswordInput && currentPasswordInput.addEventListener('input', updateSteps);
    otpInput && otpInput.addEventListener('input', updateSteps);
    newPasswordInput && newPasswordInput.addEventListener('input', updateSteps);
    confirmPasswordInput && confirmPasswordInput.addEventListener('input', updateSteps);

    // When OTP is successfully requested set flag and update steps
    var origSendHandler = sendOtpBtn && sendOtpBtn.onclick;
    // Patch sendOtpBtn click handler by listening for clicks — existing async handler will still run
    sendOtpBtn && sendOtpBtn.addEventListener('click', function(){ setTimeout(function(){ updateSteps(); }, 200); });

    // Monitor change button enable/disable
    setInterval(updateSteps, 500);
  });
</script>