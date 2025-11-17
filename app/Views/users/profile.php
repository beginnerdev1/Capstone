<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile - Aqua Bill</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

  <!-- AOS Animation -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

  <!-- Your Local CSS -->
  <link href="<?= base_url('assets/Users/css/main.css?v=' . time()) ?>" rel="stylesheet">
  <link href="<?= base_url('assets/Users/css/navbar.css?v=' . time()) ?>" rel="stylesheet">

<style>
  :root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    --danger-gradient: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
    --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    --card-shadow-hover: 0 20px 60px rgba(0, 0, 0, 0.15);
    --border-radius: 20px;
  }

  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    padding-top: 90px;
  }

  /* Page Hero */
  .page-hero {
    background: var(--primary-gradient);
    padding: 60px 0 40px 0;
    position: relative;
    overflow: hidden;
    margin-bottom: 30px;
  }

  .page-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><defs><radialGradient id="a" cx="50%" cy="0%" fx="50%" fy="0%"><stop offset="0%" stop-color="%23ffffff" stop-opacity="0.1"/><stop offset="100%" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><circle cx="25" cy="10" r="8" fill="url(%23a)"/><circle cx="75" cy="10" r="8" fill="url(%23a)"/></svg>') repeat;
    opacity: 0.1;
  }

  .page-title {
    color: white;
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 10px;
    background: linear-gradient(45deg, #ffffff, #e3f2fd);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .page-subtitle {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.1rem;
    font-weight: 400;
  }

  /* Main Section */
  section {
    min-height: auto;
    display: block;
    padding: 0 0 60px 0;
  }

  .container {
    max-width: 1200px;
    margin-top: -20px;
    position: relative;
    z-index: 10;
  }

  /* Enhanced Profile Cards */
  .profile-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--card-shadow);
    overflow: hidden;
    border: none;
    transition: all 0.3s ease;
    height: 100%;
  }

  .profile-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--card-shadow-hover);
  }

  /* Profile Picture Section */
  .profile-picture-section {
    background: var(--primary-gradient);
    padding: 40px 30px;
    text-align: center;
    position: relative;
  }

  .profile-picture-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    animation: shimmer 3s infinite;
  }

  @keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
  }

  .profile-pic {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 50%;
    border: 5px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    margin-bottom: 20px;
    transition: all 0.3s ease;
    position: relative;
    z-index: 2;
  }

  .profile-pic:hover {
    transform: scale(1.05);
    border-color: rgba(255, 255, 255, 0.6);
  }

  .profile-name {
    color: white !important;
    font-weight: 800;
    font-size: 1.5rem;
    margin-bottom: 5px;
    position: relative;
    z-index: 2;
  }

  .profile-role {
    color: rgba(255, 255, 255, 0.9) !important;
    font-size: 1rem;
    font-weight: 500;
    margin-bottom: 25px;
    position: relative;
    z-index: 2;
  }

  /* Action Buttons */
  .action-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
    position: relative;
    z-index: 2;
  }

  .action-btn {
    padding: 12px 20px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .action-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.6);
    transform: translateY(-2px);
    color: white;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
  }

  /* Account Info Section */
  .account-info {
    padding: 30px;
    background: linear-gradient(135deg, #f8f9fa, #ffffff);
  }

  .section-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f2f5;
  }

  .section-icon {
    width: 50px;
    height: 50px;
    border-radius: 15px;
    background: var(--primary-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
  }

  .section-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #2d3748;
    margin: 0;
  }

  /* Info Items */
  .info-item {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    margin-bottom: 12px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    border-left: 4px solid var(--primary-gradient);
    transition: all 0.3s ease;
  }

  .info-item:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  }

  .info-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-right: 15px;
    font-size: 16px;
  }

  .info-content {
    flex: 1;
  }

  .info-label {
    font-size: 0.85rem;
    color: #6b7280;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 3px;
  }

  .info-value {
    font-size: 1rem;
    font-weight: 700;
    color: #1f2937;
  }

  /* Personal Information Grid */
  .personal-info {
    padding: 30px;
    background: white;
  }

  .info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
  }

  /* Enhanced Modals */
  .modal-content {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.15);
    overflow: hidden;
  }

  .modal-header {
    background: var(--primary-gradient);
    color: white;
    border: none;
    padding: 25px 30px;
    position: relative;
  }

  .modal-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    animation: shimmer 3s infinite;
  }

  .modal-title {
    font-weight: 700;
    font-size: 1.3rem;
    display: flex;
    align-items: center;
    gap: 10px;
    position: relative;
    z-index: 2;
  }

  .modal-body {
    padding: 35px 30px;
    background: linear-gradient(135deg, #f8f9fa, #ffffff);
  }

  /* Enhanced Form Controls */
  .form-label {
    font-weight: 700;
    color: #374151;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .form-control, .form-select {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 12px 16px;
    font-weight: 500;
    transition: all 0.3s ease;
    background: white;
  }

  .form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    background: #fafbfc;
  }

  /* Input Groups */
  .input-group .form-control {
    border-right: none;
  }

  .input-group-text {
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    border-left: none;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .input-group-text:hover {
    background: #f3f4f6;
    color: #667eea;
  }

  /* Password Validation */
  #passwordHint {
    background: #f8fafc;
    padding: 15px;
    border-radius: 10px;
    border-left: 4px solid #e5e7eb;
  }

  #passwordHint span.valid {
    color: #10b981;
  }

  #passwordHint span.invalid {
    color: #ef4444;
  }

  .password-error {
    color: #ef4444;
    font-size: 0.9rem;
    margin-top: 5px;
    font-weight: 500;
  }

  /* Modal Footer */
  .modal-footer {
    background: #f8f9fa;
    border: none;
    padding: 25px 30px;
    gap: 15px;
  }

  .btn-modal {
    padding: 12px 30px;
    border-radius: 50px;
    font-weight: 600;
    border: none;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }

  .btn-modal::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.6s;
  }

  .btn-modal:hover::before {
    left: 100%;
  }

  .btn-secondary-modal {
    background: #6b7280;
    color: white;
  }

  .btn-secondary-modal:hover {
    background: #4b5563;
    transform: translateY(-2px);
    color: white;
  }

  .btn-primary-modal {
    background: var(--primary-gradient);
    color: white;
  }

  .btn-primary-modal:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    color: white;
  }

  /* Status Badges */
  .status-badge {
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .status-active {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
  }

  .status-pending {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
  }

  /* Responsive Design */
  @media (max-width: 991px) {
    body {
      overflow-y: auto;
      padding-top: 70px;
    }
    
    .page-title {
      font-size: 2rem;
    }
    
    .container {
      margin-top: -10px;
    }
    
    .profile-picture-section {
      padding: 30px 20px;
    }
    
    .profile-pic {
      width: 100px;
      height: 100px;
    }
    
    .action-buttons {
      flex-direction: column;
      align-items: center;
    }
    
    .action-btn {
      width: 100%;
      max-width: 200px;
      justify-content: center;
    }
    
    .info-grid {
      grid-template-columns: 1fr;
      gap: 15px;
    }
  }

  @media (max-width: 576px) {
    .modal-dialog {
      max-width: 100%;
      margin: 0;
      height: 100vh;
    }
    
    .modal-content {
      height: 100%;
      border-radius: 0;
    }
    
    .info-item {
      flex-direction: column;
      text-align: center;
      gap: 10px;
    }
    
    .info-icon {
      margin-right: 0;
      margin-bottom: 5px;
    }
  }

  /* Loading State */
  .loading-shimmer {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
  }

  @keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
  }

  /* Custom Scrollbar */
  ::-webkit-scrollbar {
    width: 8px;
  }

  ::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
  }

  ::-webkit-scrollbar-thumb {
    background: var(--primary-gradient);
    border-radius: 10px;
  }

  ::-webkit-scrollbar-thumb:hover {
    background: #667eea;
  }
</style>

</head>

<body>
<?= $this->include('Users/header') ?>

<!-- Page Hero -->
<section class="page-hero">
  <div class="container">
    <div class="row justify-content-center text-center" data-aos="fade-up">
      <div class="col-lg-8">
        <h1 class="page-title">My Profile</h1>
        <p class="page-subtitle">Manage your account information and preferences</p>
      </div>
    </div>
  </div>
</section>

<section>
  <div class="container py-0">
    <div class="row g-4 align-items-start">

      <!-- LEFT SIDE - Profile Card -->
      <div class="col-lg-4 d-flex flex-column gap-4">
        
        <!-- Profile Picture & Actions -->
        <div class="profile-card" data-aos="fade-up" data-aos-delay="100">
          <div class="profile-picture-section">
            <img id="profilePic"
                src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3.webp"
                alt="Profile Picture"
                class="profile-pic">

            <h3 class="profile-name">Loading...</h3>
            <p class="profile-role">Aqua Bill Member</p>

            <div class="action-buttons">
              <button class="action-btn" data-bs-toggle="modal" data-bs-target="#editPersonalInfoModal">
                <i class="bi bi-person-gear"></i>
                Edit Profile
              </button>
              <button class="action-btn" data-bs-toggle="modal" data-bs-target="#changeEmailModal">
                <i class="bi bi-envelope-at"></i>
                Change Email
              </button>
              <button class="action-btn" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                <i class="bi bi-shield-lock"></i>
                Security
              </button>
            </div>
          </div>
        </div>

        <!-- Account Information -->
        <div class="profile-card" data-aos="fade-up" data-aos-delay="200">
          <div class="account-info">
            <div class="section-header">
              <div class="section-icon">
                <i class="bi bi-person-badge"></i>
              </div>
              <h5 class="section-title">Account Status</h5>
            </div>
            
            <div class="info-item">
              <div class="info-icon">
                <i class="bi bi-calendar-check"></i>
              </div>
              <div class="info-content">
                <div class="info-label">Member Since</div>
                <div class="info-value account-created">—</div>
              </div>
            </div>
            
            <div class="info-item">
              <div class="info-icon">
                <i class="bi bi-shield-check"></i>
              </div>
              <div class="info-content">
                <div class="info-label">Account Status</div>
                <div class="info-value">
                  <span class="status-badge status-active account-status">Active</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- RIGHT SIDE - Personal Information -->
      <div class="col-lg-8">
        <div class="profile-card" data-aos="fade-up" data-aos-delay="300">
          <div class="personal-info">
            <div class="section-header">
              <div class="section-icon">
                <i class="bi bi-person-lines-fill"></i>
              </div>
              <h5 class="section-title">Personal Information</h5>
            </div>

            <div class="info-grid">
              <div class="info-item">
                <div class="info-icon">
                  <i class="bi bi-person"></i>
                </div>
                <div class="info-content">
                  <div class="info-label">First Name</div>
                  <div class="info-value profile-first-name">—</div>
                </div>
              </div>

              <div class="info-item">
                <div class="info-icon">
                  <i class="bi bi-person"></i>
                </div>
                <div class="info-content">
                  <div class="info-label">Last Name</div>
                  <div class="info-value profile-last-name">—</div>
                </div>
              </div>

              <div class="info-item">
                <div class="info-icon">
                  <i class="bi bi-gender-ambiguous"></i>
                </div>
                <div class="info-content">
                  <div class="info-label">Gender</div>
                  <div class="info-value profile-gender">—</div>
                </div>
              </div>

              <div class="info-item">
                <div class="info-icon">
                  <i class="bi bi-calendar3"></i>
                </div>
                <div class="info-content">
                  <div class="info-label">Age</div>
                  <div class="info-value profile-age">—</div>
                </div>
              </div>

              <div class="info-item">
                <div class="info-icon">
                  <i class="bi bi-house"></i>
                </div>
                <div class="info-content">
                  <div class="info-label">Family Number</div>
                  <div class="info-value profile-family-number">—</div>
                </div>
              </div>

              <div class="info-item">
                <div class="info-icon">
                  <i class="bi bi-telephone"></i>
                </div>
                <div class="info-content">
                  <div class="info-label">Contact Number</div>
                  <div class="info-value profile-phone">—</div>
                </div>
              </div>

              <div class="info-item">
                <div class="info-icon">
                  <i class="bi bi-geo-alt"></i>
                </div>
                <div class="info-content">
                  <div class="info-label">Purok</div>
                  <div class="info-value profile-purok">—</div>
                </div>
              </div>

              <div class="info-item">
                <div class="info-icon">
                  <i class="bi bi-building"></i>
                </div>
                <div class="info-content">
                  <div class="info-label">Barangay</div>
                  <div class="info-value profile-barangay">—</div>
                </div>
              </div>

              <div class="info-item">
                <div class="info-icon">
                  <i class="bi bi-pin-map"></i>
                </div>
                <div class="info-content">
                  <div class="info-label">Municipality</div>
                  <div class="info-value profile-municipality">—</div>
                </div>
              </div>

              <div class="info-item">
                <div class="info-icon">
                  <i class="bi bi-map"></i>
                </div>
                <div class="info-content">
                  <div class="info-label">Province</div>
                  <div class="info-value profile-province">—</div>
                </div>
              </div>

              <div class="info-item">
                <div class="info-icon">
                  <i class="bi bi-mailbox"></i>
                </div>
                <div class="info-content">
                  <div class="info-label">Zip Code</div>
                  <div class="info-value profile-zipcode">—</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Enhanced Edit Personal Info Modal -->
<div class="modal fade" id="editPersonalInfoModal" tabindex="-1" aria-labelledby="editPersonalInfoLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editPersonalInfoLabel">
          <i class="bi bi-person-gear"></i>
          Edit Personal Information
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="editPersonalInfoForm" method="post" action="<?= base_url('users/updateProfile') ?>" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="row g-4">
            
            <div class="col-12 text-center mb-3">
              <label class="form-label">
                <i class="bi bi-camera"></i>
                Profile Picture
              </label>
              <input type="file" class="form-control" name="profile_picture" id="profilePicture" accept="image/*">
              <small class="text-muted">Choose a new profile picture (optional)</small>
            </div>

            <div class="col-md-6">
              <label class="form-label">
                <i class="bi bi-person"></i>
                First Name
              </label>
              <input type="text" class="form-control" name="first_name" id="firstName" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">
                <i class="bi bi-person"></i>
                Last Name
              </label>
              <input type="text" class="form-control" name="last_name" id="lastName" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">
                <i class="bi bi-gender-ambiguous"></i>
                Gender
              </label>
              <select class="form-select" name="gender" id="gender" required>
                <option value="">Select Gender</option>
                <option>Male</option>
                <option>Female</option>
                <option>Other</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">
                <i class="bi bi-calendar3"></i>
                Age
              </label>
              <input type="number" class="form-control" name="age" id="age" min="1" max="120" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">
                <i class="bi bi-house"></i>
                Family Number
              </label>
              <input type="number" class="form-control" name="family_number" id="familyNumber" min="1" max="20" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">
                <i class="bi bi-telephone"></i>
                Contact Number
              </label>
              <input type="text" class="form-control" name="phone" id="phone" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">
                <i class="bi bi-geo-alt"></i>
                Purok
              </label>
              <select class="form-select" name="purok" id="purok" required>
                <option value="">Select Purok</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">
                <i class="bi bi-building"></i>
                Barangay
              </label>
              <input type="text" class="form-control" name="barangay" id="barangay" value="Borlongan" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label">
                <i class="bi bi-pin-map"></i>
                Municipality
              </label>
              <input type="text" class="form-control" name="municipality" id="municipality" value="Dipaculao" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label">
                <i class="bi bi-map"></i>
                Province
              </label>
              <input type="text" class="form-control" name="province" id="province" value="Aurora" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label">
                <i class="bi bi-mailbox"></i>
                Zip Code
              </label>
              <input type="text" class="form-control" name="zipcode" id="zipcode" value="3203" readonly>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-modal btn-secondary-modal" data-bs-dismiss="modal">
            <i class="bi bi-x me-2"></i>Cancel
          </button>
          <button type="submit" class="btn btn-modal btn-primary-modal" id="saveProfileBtn">
            <i class="bi bi-save me-2"></i>Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Enhanced Change Email Modal -->
<div class="modal fade" id="changeEmailModal" tabindex="-1" aria-labelledby="changeEmailLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="changeEmailLabel">
          <i class="bi bi-envelope-at"></i>
          Change Email Address
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="changeEmailForm" method="post" action="<?= base_url('users/changeEmail') ?>">
        <div class="modal-body">
          <div class="row g-4">
            <div class="col-12">
              <label class="form-label">
                <i class="bi bi-envelope"></i>
                Current Email
              </label>
              <input type="email" class="form-control" id="currentEmail" name="current_email" readonly>
            </div>

            <div class="col-12">
              <label class="form-label">
                <i class="bi bi-envelope-plus"></i>
                New Email Address
              </label>
              <input type="email" class="form-control" id="newEmail" name="new_email" required>
            </div>

            <div class="col-12">
              <label class="form-label">
                <i class="bi bi-envelope-check"></i>
                Confirm New Email
              </label>
              <input type="email" class="form-control" id="confirmEmail" name="confirm_email" required>
            </div>

            <div class="col-12">
              <label class="form-label">
                <i class="bi bi-lock"></i>
                Current Password
              </label>
              <div class="input-group">
                <input type="password" class="form-control" id="emailPassword" name="password" required>
                <span class="input-group-text toggle-password" data-target="emailPassword">
                  <i class="bi bi-eye-slash"></i>
                </span>
              </div>
              <small class="text-muted">Enter your current password to confirm this change</small>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-modal btn-secondary-modal" data-bs-dismiss="modal">
            <i class="bi bi-x me-2"></i>Cancel
          </button>
          <button type="submit" class="btn btn-modal btn-primary-modal" id="saveEmailBtn">
            <i class="bi bi-check-circle me-2"></i>Update Email
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Enhanced Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="changePasswordLabel">
          <i class="bi bi-shield-lock"></i>
          Change Password
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="changePasswordForm" method="post" action="<?= base_url('users/changePassword') ?>">
        <div class="modal-body">
          <div class="row g-4">
            <div class="col-12">
              <label class="form-label">
                <i class="bi bi-lock"></i>
                Current Password
              </label>
              <div class="input-group">
                <input type="password" class="form-control" name="current_password" id="currentPassword" required>
                <span class="input-group-text toggle-password" data-target="currentPassword">
                  <i class="bi bi-eye-slash"></i>
                </span>
              </div>
            </div>

            <div class="col-12">
              <label class="form-label">
                <i class="bi bi-lock-fill"></i>
                New Password
              </label>
              <div class="input-group">
                <input type="password" class="form-control" name="new_password" id="newPassword" required>
                <span class="input-group-text toggle-password" data-target="newPassword">
                  <i class="bi bi-eye-slash"></i>
                </span>
              </div>

              <div id="passwordHint" class="mt-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <i class="bi bi-info-circle text-primary"></i>
                  <strong>Password Requirements:</strong>
                </div>
                <div class="row g-2">
                  <div class="col-6">
                    <span id="length" class="invalid d-flex align-items-center gap-1">
                      <i class="bi bi-x-circle"></i>8+ characters
                    </span>
                  </div>
                  <div class="col-6">
                    <span id="upper" class="invalid d-flex align-items-center gap-1">
                      <i class="bi bi-x-circle"></i>Uppercase letter
                    </span>
                  </div>
                  <div class="col-6">
                    <span id="lower" class="invalid d-flex align-items-center gap-1">
                      <i class="bi bi-x-circle"></i>Lowercase letter
                    </span>
                  </div>
                  <div class="col-6">
                    <span id="number" class="invalid d-flex align-items-center gap-1">
                      <i class="bi bi-x-circle"></i>Number
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-12">
              <label class="form-label">
                <i class="bi bi-shield-check"></i>
                Confirm New Password
              </label>
              <div class="input-group">
                <input type="password" class="form-control" name="confirm_password" id="confirmPassword" required>
                <span class="input-group-text toggle-password" data-target="confirmPassword">
                  <i class="bi bi-eye-slash"></i>
                </span>
              </div>
              <div class="password-error" id="passwordError"></div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-modal btn-secondary-modal" data-bs-dismiss="modal">
            <i class="bi bi-x me-2"></i>Cancel
          </button>
          <button type="submit" class="btn btn-modal btn-primary-modal">
            <i class="bi bi-shield-check me-2"></i>Update Password
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Flash Messages (maintaining original functionality) -->
<?php if (session()->getFlashdata('success')): ?>
<script>
Swal.fire({
  icon: 'success',
  title: 'Success',
  text: '<?= session()->getFlashdata('success') ?>',
  showConfirmButton: false,
  timer: 2000,
  customClass: {
    popup: 'rounded-4',
  },
  backdrop: 'rgba(0,0,0,0.4)'
});
</script>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
<script>
Swal.fire({
  icon: 'error',
  title: 'Error',
  text: '<?= session()->getFlashdata('error') ?>',
  showConfirmButton: true,
  customClass: {
    popup: 'rounded-4',
    confirmButton: 'btn btn-danger rounded-pill px-4'
  },
  buttonsStyling: false
});
</script>
<?php endif; ?>

<?php if (session()->getFlashdata('profile_alert')): ?>
<script>
Swal.fire({
  icon: 'info',
  title: 'Profile Incomplete',
  text: 'Your account is still pending. Some features may be restricted.',
  confirmButtonText: 'Understood',
  customClass: {
    popup: 'rounded-4',
    confirmButton: 'btn btn-primary rounded-pill px-4'
  },
  buttonsStyling: false
});
</script>
<?php endif; ?>

<script>
$(document).ready(function () {
    // Initialize AOS animations
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true
    });

    // Load Profile (maintaining original functionality)
    function loadProfile() {
        // Add loading shimmer effect
        $('.profile-name, .profile-first-name, .profile-last-name').addClass('loading-shimmer');
        
        $.ajax({
            url: "<?= site_url('users/getProfileInfo') ?>",
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response.status === "success" && response.data) {
                    const data = response.data;

                    // Remove loading effects
                    $('.loading-shimmer').removeClass('loading-shimmer');

                    // Profile display
                    $(".profile-name").text(data.first_name + ' ' + data.last_name);
                    $(".profile-first-name").text(data.first_name);
                    $(".profile-last-name").text(data.last_name);
                    $(".profile-gender").text(data.gender || "—");
                    $(".profile-age").text(data.age || "—");
                    $(".profile-family-number").text(data.family_number || "—");
                    $(".profile-email").text(data.email || "—");
                    $(".profile-phone").text(data.phone || "—");
                    $(".profile-purok").text(data.purok || "—");
                    $(".profile-barangay").text(data.barangay || "—");
                    $(".profile-municipality").text(data.municipality || "—");
                    $(".profile-province").text(data.province || "—");
                    $(".profile-zipcode").text(data.zipcode || "—");

                    // Account info card
                    if (data.created_at) {
                        const createdDate = new Date(data.created_at);
                        $(".account-created").text(createdDate.toLocaleDateString('en-US', { 
                            year: 'numeric', 
                            month: 'long', 
                            day: 'numeric' 
                        }));
                    } else {
                        $(".account-created").text("—");
                    }

                    // Update account status with appropriate styling
                    const status = data.account_status || "Pending";
                    $(".account-status").text(status)
                        .removeClass('status-active status-pending')
                        .addClass(status.toLowerCase() === 'active' || status.toLowerCase() === 'approved' ? 'status-active' : 'status-pending');

                    // Profile picture with loading animation
                    const profilePic = $("#profilePic");
                    if (data.profile_picture) {
                        profilePic.attr("src", "<?= site_url('uploads/profile_pictures/') ?>" + data.profile_picture);
                    } else {
                        profilePic.attr("src", "https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3.webp");
                    }

                    // Edit personal info form
                    $("#firstName").val(data.first_name || "");
                    $("#lastName").val(data.last_name || "");
                    $("#gender").val(data.gender || "");
                    $("#age").val(data.age || "");
                    $("#familyNumber").val(data.family_number || "");
                    $("#phone").val(data.phone || "");
                    $("#purok").val(data.purok || "");
                    $("#barangay").val(data.barangay || "");
                    $("#municipality").val(data.municipality || "");
                    $("#province").val(data.province || "");
                    $("#zipcode").val(data.zipcode || "");

                    // Update current email in Change Email modal
                    $("#currentEmail").val(data.email || "");
                }
            },
            error: function () {
                console.error("Failed to load profile data.");
                $('.loading-shimmer').removeClass('loading-shimmer');
                showErrorToast('Failed to load profile data');
            }
        });
    }

    loadProfile();

    // Update Profile (maintaining original functionality)
    $("#editPersonalInfoForm").on("submit", function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        const submitBtn = $("#saveProfileBtn");
        const originalText = submitBtn.html();

        $.ajax({
            url: "<?= site_url('users/updateProfile') ?>",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            beforeSend: function () {
                submitBtn.prop("disabled", true).html('<i class="bi bi-arrow-clockwise spin me-2"></i>Updating...');
            },
            success: function (response) {
                if (response.status === "success") {
                    $("#editPersonalInfoModal").modal("hide");
                    Swal.fire({
                        icon: 'success',
                        title: 'Profile Updated!',
                        text: 'Your changes have been saved successfully.',
                        showConfirmButton: false,
                        timer: 1500,
                        customClass: {
                            popup: 'rounded-4',
                        }
                    });
                    setTimeout(() => {
                        loadProfile();
                        // Reset form
                        $("#editPersonalInfoForm")[0].reset();
                    }, 1600);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: response.message || "Failed to update profile",
                        customClass: {
                            popup: 'rounded-4',
                            confirmButton: 'btn btn-danger rounded-pill px-4'
                        },
                        buttonsStyling: false
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.',
                    customClass: {
                        popup: 'rounded-4',
                        confirmButton: 'btn btn-danger rounded-pill px-4'
                    },
                    buttonsStyling: false
                });
            },
            complete: function () {
                submitBtn.prop("disabled", false).html(originalText);
            }
        });
    });

    // Change Email (maintaining original functionality)
    $("#changeEmailForm").on("submit", function (e) {
        e.preventDefault();
        const submitBtn = $("#saveEmailBtn");
        const originalText = submitBtn.html();

        // Client-side validation
        const newEmail = $("#newEmail").val();
        const confirmEmail = $("#confirmEmail").val();
        
        if (newEmail !== confirmEmail) {
            showErrorToast('Email addresses do not match');
            return;
        }

        $.ajax({
            url: "<?= site_url('users/changeEmail') ?>",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            beforeSend: function () {
                submitBtn.prop("disabled", true).html('<i class="bi bi-arrow-clockwise spin me-2"></i>Updating...');
            },
            success: function (response) {
                if (response.status === "success") {
                    // Clear input fields
                    $("#newEmail").val("");
                    $("#confirmEmail").val("");
                    $("#emailPassword").val("");

                    // Close modal
                    $("#changeEmailModal").modal("hide");

                    // Show success message
                    Swal.fire({
                        icon: "success",
                        title: "Email Updated!",
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500,
                        customClass: {
                            popup: 'rounded-4',
                        }
                    });

                    // Reload profile data to get updated email
                    setTimeout(() => loadProfile(), 1600);
                } else {
                    // Show error without closing modal
                    Swal.fire({
                        icon: "error",
                        title: "Update Failed",
                        text: response.message,
                        customClass: {
                            popup: 'rounded-4',
                            confirmButton: 'btn btn-danger rounded-pill px-4'
                        },
                        buttonsStyling: false
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "An unexpected error occurred. Please try again.",
                    customClass: {
                        popup: 'rounded-4',
                        confirmButton: 'btn btn-danger rounded-pill px-4'
                    },
                    buttonsStyling: false
                });
            },
            complete: function () {
                submitBtn.prop("disabled", false).html(originalText);
            }
        });
    });

    // Change Password (maintaining original functionality)
    $("#changePasswordForm").on("submit", function (e) {
        e.preventDefault();
        var newPass = $("#newPassword").val();
        var confirmPass = $("#confirmPassword").val();

        if (newPass !== confirmPass) {
            showErrorToast('New password and confirm password do not match');
            return;
        }

        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();

        $.ajax({
            url: "<?= site_url('users/changePassword') ?>",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            beforeSend: function () {
                submitBtn.prop("disabled", true).html('<i class="bi bi-arrow-clockwise spin me-2"></i>Updating...');
            },
            success: function (response) {
                if (response.status === "success") {
                    $("#changePasswordModal").modal("hide");
                    Swal.fire({
                        icon: 'success',
                        title: 'Password Changed!',
                        text: 'Your password has been updated successfully.',
                        showConfirmButton: false,
                        timer: 1500,
                        customClass: {
                            popup: 'rounded-4',
                        }
                    });
                    setTimeout(() => {
                        $("#currentPassword, #newPassword, #confirmPassword").val("");
                    }, 1600);
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Update Failed",
                        text: response.message || "Failed to change password",
                        customClass: {
                            popup: 'rounded-4',
                            confirmButton: 'btn btn-danger rounded-pill px-4'
                        },
                        buttonsStyling: false
                    });
                }
            },
            error: function (jqXHR) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "An unexpected error occurred. Please try again.",
                    customClass: {
                        popup: 'rounded-4',
                        confirmButton: 'btn btn-danger rounded-pill px-4'
                    },
                    buttonsStyling: false
                });
            },
            complete: function () {
                submitBtn.prop("disabled", false).html(originalText);
            }
        });
    });

    // Password Show/Hide Toggle (maintaining original functionality)
    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', function () {
            const target = document.getElementById(this.dataset.target);
            const iconElement = this.querySelector('i');
            if (target.type === 'password') {
                target.type = 'text';
                iconElement.classList.replace('bi-eye-slash', 'bi-eye');
            } else {
                target.type = 'password';
                iconElement.classList.replace('bi-eye', 'bi-eye-slash');
            }
        });
    });

    // Enhanced Password Validation (maintaining original functionality)
    const newPassword = document.getElementById("newPassword");
    const confirmPassword = document.getElementById("confirmPassword");
    const passwordError = document.getElementById("passwordError");
    const lengthReq = document.getElementById("length");
    const upperReq = document.getElementById("upper");
    const lowerReq = document.getElementById("lower");
    const numberReq = document.getElementById("number");

    newPassword.addEventListener("input", function () {
        const value = this.value;
        
        // Length validation
        if (value.length >= 8) {
            lengthReq.classList.remove('invalid');
            lengthReq.classList.add('valid');
            lengthReq.innerHTML = '<i class="bi bi-check-circle"></i>8+ characters';
        } else {
            lengthReq.classList.remove('valid');
            lengthReq.classList.add('invalid');
            lengthReq.innerHTML = '<i class="bi bi-x-circle"></i>8+ characters';
        }
        
        // Uppercase validation
        if (/[A-Z]/.test(value)) {
            upperReq.classList.remove('invalid');
            upperReq.classList.add('valid');
            upperReq.innerHTML = '<i class="bi bi-check-circle"></i>Uppercase letter';
        } else {
            upperReq.classList.remove('valid');
            upperReq.classList.add('invalid');
            upperReq.innerHTML = '<i class="bi bi-x-circle"></i>Uppercase letter';
        }
        
        // Lowercase validation
        if (/[a-z]/.test(value)) {
            lowerReq.classList.remove('invalid');
            lowerReq.classList.add('valid');
            lowerReq.innerHTML = '<i class="bi bi-check-circle"></i>Lowercase letter';
        } else {
            lowerReq.classList.remove('valid');
            lowerReq.classList.add('invalid');
            lowerReq.innerHTML = '<i class="bi bi-x-circle"></i>Lowercase letter';
        }
        
        // Number validation
        if (/\d/.test(value)) {
            numberReq.classList.remove('invalid');
            numberReq.classList.add('valid');
            numberReq.innerHTML = '<i class="bi bi-check-circle"></i>Number';
        } else {
            numberReq.classList.remove('valid');
            numberReq.classList.add('invalid');
            numberReq.innerHTML = '<i class="bi bi-x-circle"></i>Number';
        }
    });

    confirmPassword.addEventListener("input", function () {
        if (confirmPassword.value !== newPassword.value && confirmPassword.value !== '') {
            passwordError.textContent = "Passwords do not match.";
        } else {
            passwordError.textContent = "";
        }
    });

    // Utility functions
    function showErrorToast(message) {
        const toast = document.createElement('div');
        toast.className = 'position-fixed top-0 end-0 p-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <div class="toast show" role="alert">
                <div class="toast-body bg-danger text-white rounded-3 d-flex align-items-center">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    ${message}
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
});

// Add CSS for spin animation
const style = document.createElement('style');
style.textContent = `
    .spin {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);
</script>

</body>
</html>
