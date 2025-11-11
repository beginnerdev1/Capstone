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
          <span>👤</span>
        </div>
        <label class="avatar-upload-button">
          <span class="button-icon">📷</span>
          <span>Change Photo</span>
          <input type="file" class="avatar-upload-input" id="avatarInput" accept="image/*">
        </label>
      </div>
      <div class="profile-info">
        <h1 class="profile-title" id="profileName">John Doe</h1>
        <p class="profile-email" id="profileEmailDisplay">john@example.com</p>
      </div>
      <div class="profile-badges">
        <div class="profile-badge">
          <span class="badge-icon">✓</span>
          <span>Account Verified</span>
        </div>
        <div class="profile-badge">
          <span class="badge-icon">🔒</span>
          <span>Secure Profile</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Profile Form -->
  <div class="profile-container">
    <form id="editProfileForm">
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
              Gender
            </label>
            <select class="form-select" name="gender">
              <option value="">Select Gender</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">
              Age
            </label>
            <input type="number" class="form-input" name="age" placeholder="Enter age" min="1" max="120">
          </div>
          <div class="form-group">
            <label class="form-label">
              Phone Number <span class="form-required">*</span>
            </label>
            <input type="tel" class="form-input" name="phone" placeholder="Enter phone number" required>
            <div class="form-hint">Format: 09XXXXXXXXX</div>
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

      <!-- Address Information -->
      <div class="form-section">
        <h2 class="section-title">
          <span class="section-icon">🏠</span>
          Address Information
        </h2>
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">
              Purok <span class="form-required">*</span>
            </label>
            <input type="text" class="form-input" name="purok" placeholder="Enter purok" required>
          </div>
          <div class="form-group">
            <label class="form-label">
              Barangay <span class="form-required">*</span>
            </label>
            <input type="text" class="form-input" name="barangay" placeholder="Enter barangay" required>
          </div>
          <div class="form-group">
            <label class="form-label">
              Municipality <span class="form-required">*</span>
            </label>
            <input type="text" class="form-input" name="municipality" placeholder="Enter municipality" required>
          </div>
          <div class="form-group">
            <label class="form-label">
              Province <span class="form-required">*</span>
            </label>
            <input type="text" class="form-input" name="province" placeholder="Enter province" required>
          </div>
          <div class="form-group">
            <label class="form-label">
              Zipcode <span class="form-required">*</span>
            </label>
            <input type="text" class="form-input" name="zipcode" placeholder="Enter zipcode" required>
          </div>
          <div class="form-group">
            <label class="form-label">
              Family Number
            </label>
            <input type="text" class="form-input" name="family_number" placeholder="Enter family number">
          </div>
        </div>
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
      body: formData
    });

    const data = await response.json();

    if (data.success) {
      showAlert('Profile updated successfully!', 'success');
      // Update header with new name and email
      document.getElementById('profileName').textContent = 
        formData.get('first_name') + ' ' + formData.get('last_name');
      document.getElementById('profileEmailDisplay').textContent = formData.get('email');
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

// Load profile data (you can populate this with actual data)
document.addEventListener('DOMContentLoaded', () => {
  // TODO: Fetch and populate user data
  // fetch('<?= base_url('admin/getProfileInfo') ?>')
  //   .then(res => res.json())
  //   .then(data => {
  //     form.first_name.value = data.first_name;
  //     form.last_name.value = data.last_name;
  //     // ... populate other fields
  //   });
});
</script>