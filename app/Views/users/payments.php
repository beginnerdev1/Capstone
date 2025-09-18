<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Payment</title>

    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />

    <!-- Font Awesome -->
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
      rel="stylesheet"
    />

    <!-- Your Custom CSS -->
    <link href="<?= base_url('assets/Users/css/payment.css?v=' . time()) ?>" rel="stylesheet">
  </head>
  <body>
    <div class="container py-5">
      <div class="container bg-light d-md-flex align-items-center p-4 rounded shadow">
        <!-- LEFT BOX -->
        <div class="card box1 shadow-sm p-md-5 p-4 me-md-4 mb-4 mb-md-0">
          <div class="fw-bolder mb-4 fs-3">
            <span class="fas fa-dollar-sign"></span>
            <span class="ps-1">599.00</span>
          </div>
          <div class="d-flex flex-column">
            <div class="d-flex align-items-center justify-content-between text">
              <span>Commission</span>
              <span>$1.99</span>
            </div>
            <div class="d-flex align-items-center justify-content-between text mb-4">
              <span>Total</span>
              <span>$600.99</span>
            </div>
            <div class="border-bottom mb-4"></div>
            <div class="d-flex flex-column mb-4">
              <span><i class="far fa-file-alt"></i><span class="ps-2">Invoice ID:</span></span>
              <span class="ps-3">SN8478042099</span>
            </div>
            <div class="d-flex flex-column mb-5">
              <span><i class="far fa-calendar-alt"></i><span class="ps-2">Next payment:</span></span>
              <span class="ps-3">22 July, 2018</span>
            </div>
            <div class="d-flex align-items-center justify-content-between text mt-5">
              <div class="d-flex flex-column text">
                <span>Customer Support:</span>
                <span>Online chat 24/7</span>
              </div>
              <div class="btn btn-primary rounded-circle">
                <span class="fas fa-comment-alt"></span>
              </div>
            </div>
          </div>
        </div>

        <!-- RIGHT BOX -->
        <div class="card box2 shadow-sm">
          <div class="d-flex align-items-center justify-content-between p-md-5 p-4">
            <span class="h5 fw-bold m-0">Payment methods</span>
            <div class="btn btn-primary bar"><span class="fas fa-bars"></span></div>
          </div>
          <ul class="nav nav-tabs mb-3 px-md-4 px-2">
            <li class="nav-item">
              <a class="nav-link px-2 active" aria-current="page" href="#">Credit Card</a>
            </li>
            <li class="nav-item">
              <a class="nav-link px-2" href="#">Mobile Payment</a>
            </li>
            <li class="nav-item ms-auto">
              <a class="nav-link px-2" href="#">+ More</a>
            </li>
          </ul>
          <div class="px-md-5 px-4 mb-4 d-flex align-items-center">
            <div class="btn btn-success me-4"><span class="fas fa-plus"></span></div>
            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
              <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked />
              <label class="btn btn-outline-primary" for="btnradio1"><span class="pe-1">+</span>5949</label>

              <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off" />
              <label class="btn btn-outline-primary" for="btnradio2"><span class="pe-1">+</span>3894</label>
            </div>
          </div>

          <!-- FORM -->
          <form action="">
            <div class="row">
              <div class="col-12">
                <div class="d-flex flex-column px-md-5 px-4 mb-4">
                  <span>Credit Card</span>
                  <div class="inputWithIcon position-relative">
                    <input class="form-control" type="text" value="5136 1845 5468 3894" />
                    <span class="position-absolute end-0 me-3">
                      <img
                        src="https://www.freepnglogos.com/uploads/mastercard-png/mastercard-logo-logok-15.png"
                        alt="Mastercard"
                        width="40"
                      />
                    </span>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="d-flex flex-column ps-md-5 px-md-0 px-4 mb-4">
                  <span>Expiration Date</span>
                  <div class="inputWithIcon position-relative">
                    <input type="text" class="form-control" value="05/20" />
                    <span class="fas fa-calendar-alt position-absolute end-0 me-3"></span>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="d-flex flex-column pe-md-5 px-md-0 px-4 mb-4">
                  <span>Code CVV</span>
                  <div class="inputWithIcon position-relative">
                    <input type="password" class="form-control" value="123" />
                    <span class="fas fa-lock position-absolute end-0 me-3"></span>
                  </div>
                </div>
              </div>

              <div class="col-12">
                <div class="d-flex flex-column px-md-5 px-4 mb-4">
                  <span>Name</span>
                  <div class="inputWithIcon position-relative">
                    <input class="form-control text-uppercase" type="text" value="Valdimir Berezovkiy" />
                    <span class="far fa-user position-absolute end-0 me-3"></span>
                  </div>
                </div>
              </div>

              <div class="col-12 px-md-5 px-4 mt-3">
                <button type="submit" class="btn btn-primary w-100">Pay $599.00</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Bootstrap Bundle (JS + Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
