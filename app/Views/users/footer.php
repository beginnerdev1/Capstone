<footer id="footer" class="footer bg-light border-top" role="contentinfo">
  <div class="container py-5">
    <div class="row gy-4">
      <div class="col-lg-4 col-md-6">
        <a href="<?= site_url() ?>" class="d-flex align-items-center mb-3 text-decoration-none">
          <img src="<?= base_url('assets/images/logo.png') ?>" alt="AquaBill" style="height:40px; object-fit:contain; margin-right:10px;" onerror="this.style.display='none'">
          <span class="fs-5 fw-semibold">AquaBill</span>
        </a>
        <p class="text-muted small">Smart billing, clearer insights. Contact our support anytime — we&apos;re here to help.</p>

        <div class="d-flex gap-2 mt-3" aria-label="Social links">
          <a class="btn btn-outline-secondary btn-sm rounded-circle" href="#" aria-label="Twitter"><i class="bi bi-twitter-x" aria-hidden="true"></i><span class="visually-hidden">Twitter</span></a>
          <a class="btn btn-outline-secondary btn-sm rounded-circle" href="#" aria-label="Facebook"><i class="bi bi-facebook" aria-hidden="true"></i><span class="visually-hidden">Facebook</span></a>
          <a class="btn btn-outline-secondary btn-sm rounded-circle" href="#" aria-label="Instagram"><i class="bi bi-instagram" aria-hidden="true"></i><span class="visually-hidden">Instagram</span></a>
          <a class="btn btn-outline-secondary btn-sm rounded-circle" href="#" aria-label="LinkedIn"><i class="bi bi-linkedin" aria-hidden="true"></i><span class="visually-hidden">LinkedIn</span></a>
        </div>
      </div>

      <div class="col-lg-2 col-md-6">
        <h6 class="text-dark fw-semibold">Product</h6>
        <ul class="list-unstyled small text-muted">
          <li><a href="#" class="text-muted text-decoration-none">Features</a></li>
          <li><a href="#" class="text-muted text-decoration-none">Pricing</a></li>
          <li><a href="#" class="text-muted text-decoration-none">Integrations</a></li>
        </ul>
      </div>

      <div class="col-lg-2 col-md-6">
        <h6 class="text-dark fw-semibold">Company</h6>
        <ul class="list-unstyled small text-muted">
          <li><a href="#" class="text-muted text-decoration-none">About</a></li>
          <li><a href="#" class="text-muted text-decoration-none">Careers</a></li>
          <li><a href="#" class="text-muted text-decoration-none">Contact</a></li>
        </ul>
      </div>

      <div class="col-lg-4 col-md-6">
        <h6 class="text-dark fw-semibold">Get Help</h6>
        <p class="small text-muted mb-1">Support via email: <a href="mailto:support@aquabill.example" class="text-decoration-none">support@aquabill.example</a></p>
        <p class="small text-muted">Or start a chat from the <a href="<?= base_url('users/chat') ?>" class="text-decoration-none">Contact Admins</a> page.</p>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-center small text-muted">
        <div>&copy; <?= date('Y') ?> AquaBill. All rights reserved.</div>
        <div>Designed by <a href="#" class="text-decoration-none">AquaBill 2025</a></div>
      </div>
    </div>
  </div>
</footer>