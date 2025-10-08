<div class="container bg-light d-md-flex align-items-start p-4 rounded shadow">
  <!-- LEFT BOX -->
  <div class="card box1 shadow-sm p-md-5 p-4 me-md-4 mb-4 mb-md-0 flex-fill">
    <div class="fw-bolder mb-4 fs-3">
      <i class="fas fa-dollar-sign"></i>
      <span class="ps-1">599.00</span>
    </div>
    <div class="d-flex flex-column">
      <div class="d-flex align-items-center justify-content-between text">
        <span>Commission</span>
        <span>₱1.99</span>
      </div>
      <div class="d-flex align-items-center justify-content-between text mb-4">
        <span>Total</span>
        <span>₱600.99</span>
      </div>
      <hr class="mb-4" />
      <div class="mb-4">
        <span><i class="far fa-file-alt"></i><span class="ps-2">Invoice ID:</span></span>
        <div class="ps-3">SN8478042099</div>
      </div>
      <div class="mb-5">
        <span><i class="far fa-calendar-alt"></i><span class="ps-2">Next payment:</span></span>
        <div class="ps-3">22 July, 2018</div>
      </div>
      <div class="d-flex align-items-center justify-content-between text mt-5">
        <div>
          <div>Customer Support:</div>
          <div>Online chat 24/7</div>
        </div>
        <button type="button" class="btn btn-primary rounded-circle">
          <i class="fas fa-comment-alt"></i>
        </button>
      </div>
    </div>
  </div>

  <!-- RIGHT BOX -->
  <div class="card box2 shadow-sm flex-fill">
    <div class="d-flex align-items-center justify-content-between p-md-5 p-4">
      <h5 class="fw-bold m-0">Payment methods</h5>
      <button type="button" class="btn btn-primary bar"><i class="fas fa-bars"></i></button>
    </div>
    <ul class="nav nav-tabs mb-3 px-md-4 px-2">
      <li class="nav-item">
        <a class="nav-link px-2 active" href="#">Credit Card</a>
      </li>
      <li class="nav-item">
        <a class="nav-link px-2" href="#">Mobile Payment</a>
      </li>
      <li class="nav-item ms-auto">
        <a class="nav-link px-2" href="#">+ More</a>
      </li>
    </ul>
    <div class="px-md-5 px-4 mb-4 d-flex align-items-center">
      <button type="button" class="btn btn-success me-4"><i class="fas fa-plus"></i></button>
      <div class="btn-group" role="group">
        <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked />
        <label class="btn btn-outline-primary" for="btnradio1"><span class="pe-1">+</span>5949</label>

        <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off" />
        <label class="btn btn-outline-primary" for="btnradio2"><span class="pe-1">+</span>3894</label>
      </div>
    </div>

    <!-- FORM -->
    <form id="payForm" action="<?= site_url('users/createCheckout') ?>" method="post">
      <div class="row">
        <div class="col-12">
          <div class="d-flex flex-column px-md-5 px-4 mb-4">
            <label for="cardNumber">Credit Card</label>
            <div class="inputWithIcon position-relative">
              <input id="cardNumber" class="form-control" type="text" value="5136 1845 5468 3894" />
              <span class="position-absolute end-0 me-3">
                <img src="https://www.freepnglogos.com/uploads/mastercard-png/mastercard-logo-logok-15.png" alt="Mastercard" width="40" />
              </span>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="d-flex flex-column ps-md-5 px-md-0 px-4 mb-4">
            <label for="expiry">Expiration Date</label>
            <div class="inputWithIcon position-relative">
              <input id="expiry" type="text" class="form-control" value="05/20" />
              <i class="fas fa-calendar-alt position-absolute end-0 me-3"></i>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="d-flex flex-column pe-md-5 px-md-0 px-4 mb-4">
            <label for="cvv">Code CVV</label>
            <div class="inputWithIcon position-relative">
              <input id="cvv" type="password" class="form-control" value="123" />
              <i class="fas fa-lock position-absolute end-0 me-3"></i>
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="d-flex flex-column px-md-5 px-4 mb-4">
            <label for="cardName">Name</label>
            <div class="inputWithIcon position-relative">
              <input id="cardName" class="form-control text-uppercase" type="text" value="Valdimir Berezovkiy" />
              <i class="far fa-user position-absolute end-0 me-3"></i>
            </div>
          </div>
        </div>

        <div class="col-12 px-md-5 px-4 mt-3">
          <button type="submit" class="btn btn-primary w-100">Pay ₱599.00</button>
        </div>
      </div>
    </form>
  </div>
</div>
