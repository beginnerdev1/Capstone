<?php   

namespace App\Controllers;

use App\Models\BillingModel;
use App\Models\UserInformationModel;
use App\Models\PaymentsModel;
use App\Models\UsersModel;

class Users extends BaseController
{
    // Show users index page
public function index()
{
    $userId = session()->get('user_id');
    $userModel = new \App\Models\UsersModel();

    $user = $userModel->find($userId);
    $data['profile_complete'] = $user ? $user['profile_complete'] : 0;

    // Compute total paid by this user (include 'paid' and 'partial' statuses)
    try {
        $paymentsModel = new PaymentsModel();
        $totalRow = $paymentsModel
            ->selectSum('amount', 'total_paid')
            ->where('user_id', $userId)
            ->whereIn('status', ['paid', 'partial'])
            ->where('deleted_at', null)
            ->first();

        $data['totalPaidAmount'] = isset($totalRow['total_paid']) ? $totalRow['total_paid'] : 0;
    } catch (\Exception $e) {
        // In case of any issue, default to zero and log the error
        log_message('error', 'Failed to compute totalPaidAmount: ' . $e->getMessage());
        $data['totalPaidAmount'] = 0;
    }

    return view('users/index', $data);
}



        // Show billing history page - Done
        public function history()
        {
            $session = session();
            $userId = $session->get('user_id'); // Get logged-in user's ID

            if (!$userId) {
                // Redirect to login if user is not logged in
                return redirect()->to('/login');
            }

            $paymentsModel = new PaymentsModel();

            // Fetch all payments for this user, latest first
            // Fetch payments for this user (include partial payments explicitly)
            $data['payments'] = $paymentsModel
                ->select('payments.*, payments.payment_intent_id, billings.bill_no, billings.due_date, billings.amount_due as bill_amount, billings.balance as bill_balance')
                ->join('billings', 'billings.id = payments.billing_id', 'left')
                ->where('payments.user_id', $userId)
                // include common statuses and ensure 'partial' is present
                ->whereIn('payments.status', ['paid', 'partial', 'pending', 'awaiting_payment', 'initiated', 'failed', 'cancelled', 'expired'])
                ->orderBy('payments.created_at', 'DESC')
                ->findAll();

            // Ensure the view's `reference_number` shows the payment_intent_id for gateway payments
            $data['payments'] = array_map(function($p) {
                $method = strtolower($p['method'] ?? '');
                $reference = '-';

                if ($method === 'gateway' && !empty($p['payment_intent_id'])) {
                    $reference = $p['payment_intent_id'];
                } elseif (!empty($p['reference_number'])) {
                    $reference = $p['reference_number'];
                }

                // normalize bill amount for the view (prefer balance, then amount_due)
                $p['bill_amount'] = isset($p['bill_amount']) && $p['bill_amount'] !== null
                    ? $p['bill_amount']
                    : (isset($p['bill_balance']) ? $p['bill_balance'] : ($p['amount_due'] ?? 0));

                // Overwrite reference_number so existing views keep working
                $p['reference_number'] = $reference;

                return $p;
            }, $data['payments']);

            // Load the history view (file: app/Views/Users/history.php)
            return view('users/history', $data);
        }




//Check The Account Status
public function getAccountStatus()
{
    $userId = session()->get('user_id');
    $userModel = new \App\Models\UsersModel();
    $user = $userModel->find($userId);

    if (!$user) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'User not found']);
    }

    return $this->response->setJSON([
        'status' => 'success',
        'account_status' => $user['status'] // 'pending', 'approved', etc.
    ]);
}




    // Show profile page - Done
    public function profile()
    {
        $session = session();
        if (!$session->get('user_id')) {
            return redirect()->to(base_url('login'));
        }
        return view('users/profile');
    }

 
    // Update or insert profile info AJAX - Done
    public function updateProfile()
    {
        try {
            $userId = session()->get('user_id');
            $userModel = new \App\Models\UsersModel();

            if (!$userModel->find($userId)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'User does not exist'
                ]);
            }

            $data = [
                'first_name'    => $this->request->getPost('first_name'),
                'last_name'     => $this->request->getPost('last_name'),
                'phone'         => $this->request->getPost('phone'),
                'gender'        => $this->request->getPost('gender'),
                'age'           => $this->request->getPost('age'),
                'family_number' => $this->request->getPost('family_number'),
                'purok'         => $this->request->getPost('purok'),
                'barangay'      => $this->request->getPost('barangay'),
                'municipality'  => $this->request->getPost('municipality'),
                'province'      => $this->request->getPost('province'),
                'zipcode'       => $this->request->getPost('zipcode'),
            ];

            $file = $this->request->getFile('profile_picture');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $maxSize = 5 * 1024 * 1024;
                $uploadDir = 'uploads/profile_pictures';

                if (!in_array($file->getMimeType(), $allowedMimes)) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Invalid file type.'
                    ]);
                }

                if ($file->getSize() > $maxSize) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'File size exceeds 5MB.'
                    ]);
                }

                $extension = strtolower($file->getClientExtension());
                $validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (!in_array($extension, $validExtensions)) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Invalid file extension.'
                    ]);
                }

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $newName = time() . '_' . uniqid() . '.' . $extension;
                $file->move($uploadDir, $newName);
                $data['profile_picture'] = $newName;
            }

            $userInfoModel = model('UserInformationModel');
            $result = $userInfoModel->saveUserInfo($userId, $data);

            if (isset($result['success']) && $result['success'] === true) {
                // Mark profile as complete. Do NOT change account status to pending when user edits their profile.
                // Previously the code set 'status' => 'pending' here which forced the account back to pending
                // after any profile edit. Commenting that out to preserve admin-set/approved status.
                $userModel->update($userId, [
                    'profile_complete' => 1,
                    // 'status' => 'pending' // intentionally disabled
                ]);

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Profile updated successfully'
                ]);
            }

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update profile'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'UpdateProfile Exception: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An unexpected error occurred'
            ]);
        }
    }


    // Return profile info via AJAX - Done
public function getProfileInfo()
{
    $user_id = session()->get('user_id');
    if (!$user_id) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'No session user_id'
        ]);
    }

    $userModel = new \App\Models\UsersModel();
    $userInfoModel = new \App\Models\UserInformationModel();

    // Get account info
    $account = $userModel->select('email, created_at, status')->find($user_id);

    // Default account info
    if (!$account) {
        $account = [
            'email' => '',
            'created_at' => null,
            'status' => 'pending'
        ];
    }

    // Get profile info
    $profile = $userInfoModel->where('user_id', $user_id)->first();

    // Default profile if none found
    $defaultProfile = [
        'first_name' => '',
        'last_name' => '',
        'gender' => '',
        'age' => '',
        'family_number' => '',
        'phone' => '',
        'purok' => '',
        'barangay' => 'Borlongan',
        'municipality' => 'Dipaculao',
        'province' => 'Aurora',
        'zipcode' => '3203',
        'profile_picture' => null
    ];

    $profile = $profile ? $profile : $defaultProfile;

    // Merge account info
    $profile['email'] = $account['email'];
    $profile['created_at'] = $account['created_at'];
    $profile['account_status'] = $account['status'] ?? 'pending'; // <-- Show actual status

    return $this->response->setJSON([
        'status' => 'success',
        'data' => $profile
    ]);
}



    //Profile controller - Done?
    public function getProfilePicture($filename)
    {
        $filePath = FCPATH . 'uploads/' . $filename;

        if (file_exists($filePath)) {
            return $this->response->download($filePath, null, true); // ✅ display inline
        } else {
            return $this->response->setStatusCode(404)->setBody('File not found');
        }
    }

    //Change email - Done
    public function changeEmail()
    {
        $userId = session()->get('user_id');
        log_message('debug', 'User ID from session: ' . $userId);

        if (!$userId) {
            log_message('error', 'No user is logged in.');
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not logged in.'
            ]);
        }

        $currentEmail = $this->request->getPost('current_email');
        $newEmail = $this->request->getPost('new_email');
        $confirmEmail = $this->request->getPost('confirm_email');
        $password = $this->request->getPost('password');

        log_message('debug', 'POST data: ' . json_encode([
            'currentEmail' => $currentEmail,
            'newEmail' => $newEmail,
            'confirmEmail' => $confirmEmail
        ]));

        if ($newEmail !== $confirmEmail) {
            log_message('error', 'New emails do not match.');
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'New emails do not match.'
            ]);
        }

        $userModel = new \App\Models\UsersModel();
        $user = $userModel->find($userId);

        if (!$user) {
            log_message('error', 'User not found in database.');
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not found.'
            ]);
        }

        if (!password_verify($password, $user['password'])) {
            log_message('error', 'Incorrect password.');
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Incorrect password.'
            ]);
        }

        if ($user['email'] === $newEmail) {
            log_message('error', 'New email is the same as current.');
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'New email is the same as the current email.'
            ]);
        }

        $userModel->update($userId, ['email' => $newEmail]);
        log_message('debug', 'Email updated for user ID: ' . $userId);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Email updated successfully.'
        ]);
    }

// Show change password page
public function changePassword()
{
    $userId = session()->get('user_id');
    
    $currentPassword = $this->request->getPost('current_password');
    $newPassword = $this->request->getPost('new_password');
    $confirmPassword = $this->request->getPost('confirm_password');

    // Check if new passwords match
    if ($newPassword !== $confirmPassword) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'New passwords do not match.'
        ]);
    }

    // Password complexity validation
    $uppercase = preg_match('@[A-Z]@', $newPassword);
    $lowercase = preg_match('@[a-z]@', $newPassword);
    $number    = preg_match('@[0-9]@', $newPassword);
    $length    = strlen($newPassword) >= 8;

    if (!$uppercase || !$lowercase || !$number || !$length) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Password must be at least 8 characters and include 1 uppercase letter, 1 lowercase letter, and 1 number.'
        ]);
    }

    // Load the model
    $usersModel = new \App\Models\UsersModel();

    // Get user data
    $user = $usersModel->find($userId);

    // Check current password
    if (!$user || !password_verify($currentPassword, $user['password'])) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Current password is incorrect.'
        ]);
    }

    // Update password
    $usersModel->update($userId, [
        'password' => password_hash($newPassword, PASSWORD_DEFAULT)
    ]);

    return $this->response->setJSON([
        'status' => 'success',
        'message' => 'Password changed successfully.'
    ]);
}




    // Return billing data via AJAX
public function getBillingsAjax()
{
    $limit = (int) ($this->request->getGet('limit') ?? 10);
    $userId = session()->get('user_id');

    if (empty($userId)) {
        return $this->response->setStatusCode(401)->setJSON([
            'status' => 'error',
            'message' => 'Unauthenticated'
        ]);
    }

    $billingModel = new BillingModel();

    // 1) Fetch recent bills
    try {
        $billings = $billingModel->getBillsByUser($userId, $limit);
    } catch (\Throwable $e) {
        $billings = [];
        log_message('error', 'getBillingsAjax: failed to fetch bills - ' . $e->getMessage());
    }

    $latestBill = !empty($billings) ? $billings[0] : null;

    if ($latestBill) {
        $amountDue = isset($latestBill['amount_due']) ? (float)$latestBill['amount_due'] : 0.0;
        $carryover = isset($latestBill['carryover']) ? (float)$latestBill['carryover'] : 0.0;
        $balance = ($latestBill['balance'] !== null) ? (float)$latestBill['balance'] : $amountDue;

        $totalOutstanding = $carryover + $balance;
    } else {
        $amountDue = 0;
        $carryover = 0;
        $balance = 0;
        $totalOutstanding = 0;
    }

    // Compute totalPaid from payments (include 'paid' and 'partial')
    $totalPaid = 0.0;
    try {
        $paymentsModel = new \App\Models\PaymentsModel();
        $row = $paymentsModel->selectSum('amount', 'total_paid')
            ->where('user_id', $userId)
            ->whereIn('status', ['paid', 'partial'])
            ->where('deleted_at', null)
            ->first();

        $totalPaid = isset($row['total_paid']) ? (float)$row['total_paid'] : 0.0;
    } catch (\Throwable $e) {
        log_message('error', 'getBillingsAjax: failed to compute totalPaid - ' . $e->getMessage());
        $totalPaid = 0.0;
    }

    $response = [
        'status' => 'success',
        'bills' => $billings,
        'totals' => [
            'carryover' => $carryover,
            'currentBill' => $amountDue,
            'totalOutstanding' => $totalOutstanding,
            'totalPaid' => $totalPaid
        ],

        // legacy keys for backward compatibility
        'carryover' => $carryover,
        'currentBill' => $amountDue,
        'totalOutstanding' => $totalOutstanding,
        'totalPaid' => $totalPaid,
    ];

    return $this->response->setJSON($response);
}



// Return specific bill details via AJAX for the manual transaction

// Return specific bill details via AJAX for the manual transaction
public function getBillDetails()
{
    $billId = $this->request->getGet('bill_id');
    log_message('debug', 'getBillDetails: billId received: ' . $billId); // Debug: Check bill_id

    $billingModel = new \App\Models\BillingModel();
    $bill = $billingModel->find($billId);
    log_message('debug', 'getBillDetails: bill array: ' . json_encode($bill)); // Debug: Inspect the full array

    if ($bill) {
        log_message('debug', 'getBillDetails: Bill found, processing fields'); // Debug: Confirm branch
        return $this->response->setJSON([
            'bill_no' => isset($bill['bill_no']) ? $bill['bill_no'] : '',
            'billing_month' => isset($bill['billing_month']) ? $bill['billing_month'] : '',
            'due_date' => isset($bill['due_date']) ? $bill['due_date'] : '',
            'amount_due' => isset($bill['amount_due']) ? (float)$bill['amount_due'] : 0.0,
            'balance' => isset($bill['balance']) ? (float)$bill['balance'] : 0.0,
            'carryover' => isset($bill['carryover']) ? (float)$bill['carryover'] : 0.0,
        ]);
    } else {
        log_message('debug', 'getBillDetails: No bill found for billId: ' . $billId); // Debug: If null
        return $this->response->setJSON([]);
    }
}










    // Show payments 
public function payments()
{
    $userId = session()->get('user_id');

    if (!$userId) {
        return redirect()->to('/login');
    }

    $billingModel  = new BillingModel();
    $paymentsModel = new PaymentsModel();

    // 1) Get the latest pending bill for the user
    $latestBill = $billingModel
        ->where('user_id', $userId)
        ->where('status', 'Pending')
        ->orderBy('billing_month', 'DESC')
        ->first();

    if ($latestBill) {
        // 2) Get carryover for this bill (or calculate total carryover if needed)
        $carryover = isset($latestBill['carryover']) ? (float)$latestBill['carryover'] : 0.0;

        // 3) Get current month bill amount
        $currentAmount = isset($latestBill['amount_due']) ? (float)$latestBill['amount_due'] : 0.0;

        // 4) Subtract payments already made for this bill
        $paymentsMade = $paymentsModel
            ->selectSum('amount')
            ->where('user_id', $userId)
            ->where('billing_id', $latestBill['id'])
            ->whereIn('status', ['paid', 'Paid']) // Adjust statuses as needed
            ->get()
            ->getRowArray()['amount'] ?? 0.0;

        // 5) Calculate net total due for latest bill
        $netDue = $carryover + $currentAmount - $paymentsMade;
        $netDue = max(0, $netDue); // Ensure not negative

        // 6) Add service fee
        $serviceFee = 1.99;
        $finalTotal = $netDue + $serviceFee;

        $data = [
            'bills' => [$latestBill], // Only show latest bill
            'totalAmount' => $netDue,
            'serviceFee' => $serviceFee,
            'finalTotal' => $finalTotal,
            'latestBill' => $latestBill,
            'carryover' => $carryover,
            'currentAmount' => $currentAmount,
            'paymentsMade' => $paymentsMade,
        ];
    } else {
        // No pending bills: Show total outstanding as fallback
        $totalOutstanding = $billingModel->getOutstandingForUser($userId);
        $serviceFee = 1.99;
        $finalTotal = $totalOutstanding + $serviceFee;

        $data = [
            'bills' => [],
            'totalAmount' => $totalOutstanding,
            'serviceFee' => $serviceFee,
            'finalTotal' => $finalTotal,
            'latestBill' => null,
            'carryover' => 0,
            'currentAmount' => 0,
            'paymentsMade' => 0,
        ];
    }

    return view('users/payments', $data);
}
    
    // Check for existing pending transaction for a bill
public function hasPendingTransaction($billId)
    {
        $userId = session()->get('user_id');
        $paymentsModel = new \App\Models\PaymentsModel();

        // Check for pending or awaiting_payment status for this bill and user
        $pending = $paymentsModel
            ->where('user_id', $userId)
            ->where('billing_id', $billId)
            ->whereIn('status', ['pending', 'awaiting_payment'])
            ->where('deleted_at', null)
            ->first();

        return $pending ? true : false;
    }


    // Create PayMongo checkout session
public function createCheckout()
{
    try {
        $secretKey = env('PAYMONGO_SECRET_KEY');
        $userId = session()->get('user_id');

        if (!$userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Authentication required'
            ]);
        }

        $billIds = trim($this->request->getPost('bill_ids') ?? '');
        if (empty($billIds)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No bills selected for payment'
            ]);
        }

        $billIdArray = array_filter(explode(',', $billIds));
        $billingModel = new BillingModel();
        $paymentsModel = new PaymentsModel();

        // Get selected bills (only Pending status, ignore partial)
        $userBills = $billingModel->whereIn('id', $billIdArray)
                                  ->where('user_id', $userId)
                                  ->where('status', 'Pending')
                                  ->findAll();

        if (count($userBills) !== count($billIdArray)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid bill selection'
            ]);
        }

        // Total of selected bills (exact balances)
        $calculatedTotal = array_sum(array_column($userBills, 'balance'));

        // Include carryover from latest bill
        $latestBill = $billingModel->where('user_id', $userId)
                                   ->orderBy('billing_month', 'DESC')
                                   ->first();
        $carryover = $latestBill ? (float)($latestBill['carryover'] ?? 0) : 0.0;

        $totalAmount = $calculatedTotal + $carryover;

        // Validate payment amount
        $inputAmount = filter_var($this->request->getPost('total_amount'), FILTER_VALIDATE_FLOAT);
        if ($inputAmount === false || abs($inputAmount - $totalAmount) > 0.01) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Payment amount must exactly match the total due of selected bills including carryover'
            ]);
        }

        // Server-side service fee (fixed) — prevents client tampering
        $serviceFee = 1.99;

        // Gateway amount = net due + service fee (this is the amount we'll charge via PayMongo)
        $gatewayAmount = round($totalAmount + $serviceFee, 2);

        // Persist a pending payment record so we can reference it later (and store service fee)
        $paymentData = [
            'billing_id'     => $billIds,
            'user_id'        => $userId,
            'method'         => 'gateway',
            'amount'         => $totalAmount,
            'gateway_amount' => $gatewayAmount,
            'service_fee'    => $serviceFee,
            'status'         => 'initiated',
            'currency'       => 'PHP',
            'created_at'     => date('Y-m-d H:i:s'),
        ];

        $insertedPaymentId = $paymentsModel->insert($paymentData);
        if (!$insertedPaymentId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to create payment record'
            ]);
        }

        // Convert gateway amount to centavos
        $amount = (int) round($gatewayAmount * 100);

        // Create PayMongo checkout session
        $payload = [
            "data" => [
                "attributes" => [
                    "line_items" => [[
                        "amount" => $amount,
                        "currency" => "PHP",
                        "name" => "Water Bill Payment - " . count($userBills) . " bill(s)",
                        "quantity" => 1
                    ]],
                    "payment_method_types" => ["gcash"],
                    "success_url" => base_url('users/payment-success'),
                    "cancel_url" => base_url('users/payment-cancel'),
                    "description" => "Water Bill Payment for User ID: " . $userId
                ]
            ]
        ];

        $ch = curl_init('https://api.paymongo.com/v1/checkout_sessions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($secretKey . ':')
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            // mark payment as failed
            $paymentsModel->update($insertedPaymentId, ['status' => 'failed', 'updated_at' => date('Y-m-d H:i:s')]);
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Payment gateway connection failed'
            ]);
        }

        $result = json_decode($response, true);
        if ($httpCode === 200 && isset($result['data']['id'])) {
            $checkoutUrl = $result['data']['attributes']['checkout_url'] ?? null;
            $paymentIntentId = $result['data']['attributes']['payment_intent']['id'] ?? null;

            // update payment record with gateway references
            $updateData = [
                'payment_intent_id' => $paymentIntentId,
                'checkout_url' => $checkoutUrl,
                'status' => 'awaiting_payment',
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $paymentsModel->update($insertedPaymentId, $updateData);

            if ($checkoutUrl) {
                return redirect()->to($checkoutUrl);
            }
        }

        // If we reach here something went wrong with the gateway
        $paymentsModel->update($insertedPaymentId, ['status' => 'failed', 'updated_at' => date('Y-m-d H:i:s')]);
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Payment gateway error'
        ]);

    } catch (\Exception $e) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'An unexpected error occurred: ' . $e->getMessage()
        ]);
    }
}


// Helper method to expire old pending payments
private function expireOldPayments($userId)

    {
                $paymentsModel = new PaymentsModel();
                
                // Mark payments as expired if they're older than 30 minutes and still awaiting_payment
                $expiredCount = $paymentsModel
                    ->where('user_id', $userId)
                    ->where('status', 'awaiting_payment')
                    ->where('created_at <', date('Y-m-d H:i:s', strtotime('-30 minutes')))
                    ->set(['status' => 'expired', 'updated_at' => date('Y-m-d H:i:s')])
                    ->update();
                
                if ($expiredCount > 0) {
                    log_message('info', 'Expired ' . $expiredCount . ' old pending payments for user: ' . $userId);
                }
                
                return $expiredCount;
    }

        


    // AJAX endpoint to apply a payment immediately to a single bill (used by client-side quick-pay)
    public function applyPaymentAjax()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthenticated'])->setStatusCode(401);
        }

        $billId = (int) $this->request->getPost('billing_id');
        $amount = floatval($this->request->getPost('amount'));

        if ($billId <= 0 || $amount <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid input']);
        }

        $paymentsModel = new \App\Models\PaymentsModel();
        $billingModel  = new BillingModel();

        // Verify the bill belongs to the user
        $bill = $billingModel->where('id', $billId)->where('user_id', $userId)->first();
        if (!$bill) {
            return $this->response->setJSON(['success' => false, 'message' => 'Bill not found']);
        }

        // Create a payment record (manual immediate capture)
        $paymentData = [
            'user_id' => $userId,
            'billing_id' => $billId,
            'method' => 'manual',
            'amount' => $amount,
            'currency' => 'PHP',
            'status' => 'paid',
            'paid_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $insertedId = $paymentsModel->insert($paymentData);
        if (!$insertedId) {
            log_message('error', 'applyPaymentAjax failed to insert payment: ' . json_encode($paymentData));
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to record payment']);
        }

        // Apply the payment to the billing atomically
        try {
            $res = $billingModel->applyPaymentToBill($billId, $amount);
            if ($res === false) {
                log_message('error', 'applyPaymentAjax: applyPaymentToBill returned false for bill ' . $billId);
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to apply payment']);
            }

            return $this->response->setJSON([
                'success' => true,
                'billing_id' => $billId,
                'applied_amount' => $amount,
                'new_balance' => isset($res['new_balance']) ? (float)$res['new_balance'] : null,
                'status' => $res['status'] ?? null
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'applyPaymentAjax exception: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Server error applying payment'])->setStatusCode(500);
        }
    }


public function paymentFailed()
    {
        // Redirect to the new cancel handler for consistency
        return $this->paymentCancel();
    }

    //payment proof   
    public function manualTransaction()
    {
        return view('users/manual_transaction'); // your file: app/Views/payment_proof.php
    }


    //upload Proof

//upload Proof
public function uploadProof()
{
    $paymentModel = new PaymentsModel();
    $billingModel = new BillingModel();

    $validation = \Config\Services::validation();
    $validation->setRules([
        'referenceNumber' => 'required|min_length[5]',
        'screenshot'      => 'uploaded[screenshot]|is_image[screenshot]|max_size[screenshot,2048]',
        'amount'          => 'required|decimal|greater_than[0]',
        'billing_id'      => 'permit_empty|integer'
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        return redirect()->back()->with('error', $validation->listErrors());
    }

    $reference  = trim($this->request->getPost('referenceNumber'));
    $billingId  = $this->request->getPost('billing_id') ? (int) $this->request->getPost('billing_id') : null;
    $userId     = session()->get('user_id');

    // Duplicate reference guard
    if ($paymentModel->where('reference_number', $reference)->first()) {
        return redirect()->back()->with('error', 'This reference number is already in use.');
    }

    // Validate the selected bill belongs to user
    if ($billingId) {
        $bill = $billingModel->where('id', $billingId)
                             ->where('user_id', $userId)
                             ->first();

        if (!$bill) {
            return redirect()->back()->with('error', 'Invalid bill selected.');
        }
    }

    $file = $this->request->getFile('screenshot');
    $amountPaid = floatval($this->request->getPost('amount'));

    if (!$file || !$file->isValid() || $file->hasMoved()) {
        return redirect()->back()->with('error', 'File upload failed or file is invalid.');
    }

    // Save uploaded file
    $safeReference = preg_replace('/[^A-Za-z0-9_\-]/', '_', $reference);
    $timestamp     = date('Ymd_His');
    $extension     = $file->getClientExtension() ?: $file->getExtension();
    $newName       = $safeReference . '_' . $timestamp . '.' . $extension;
    $targetPath    = FCPATH . 'uploads/receipts';

    if (!is_dir($targetPath)) {
        mkdir($targetPath, 0755, true);
    }

    $file->move($targetPath, $newName);

    $receiptPath = 'uploads/receipts/' . $newName;

    // Create payment record
    $paymentData = [
        'billing_id'        => $billingId,
        'payment_intent_id' => uniqid('manual_'),
        'method'            => 'manual',
        'reference_number'  => $reference,
        'receipt_image'     => $receiptPath,
        'amount'            => $amountPaid,
        'currency'          => 'PHP',
        'status'            => 'pending', // admin must confirm
        'user_id'           => $userId,
        'paid_at'           => null,
        'created_at'        => date('Y-m-d H:i:s'),
    ];

    $insertedId = $paymentModel->insert($paymentData);

    if (!$insertedId) {
        return redirect()->back()->with('error', 'Failed to save payment record.');
    }

    // Only mark the bill as pending, no calculations yet
    if ($billingId) {
        $billingModel->update($billingId, ['status' => 'Pending']);
    }

    return redirect()->to(base_url('users'))
                     ->with('success', 'Payment proof submitted. Waiting for admin confirmation.');
}


    //check reference
public function checkReference()
        {
            $reference = $this->request->getPost('referenceNumber');
            $paymentModel = new PaymentsModel();

            $exists = $paymentModel->where('reference_number', $reference)->first();

            return $this->response->setJSON(['exists' => $exists ? true : false]);
        }

    // Handle payment success
public function paymentSuccess()
{
    $userId = session()->get('user_id');
    
    if (!$userId) {
        return redirect()->to('/login');
    }

    // Find the most recent payment (could be paid by webhook already)
    $paymentsModel = new PaymentsModel();
    $payment = $paymentsModel
        ->where('user_id', $userId)
        ->whereIn('status', ['awaiting_payment', 'paid']) // Check both statuses
        ->orderBy('created_at', 'DESC')
        ->first();

    if ($payment) {
            if ($payment['status'] === 'awaiting_payment') {
                // Webhook hasn't processed yet, do fallback update
                $paymentsModel->update($payment['id'], [
                    'status' => 'paid',
                    'paid_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                // Update billing records by allocating the payment amount across referenced bills (oldest-first)
                if (!empty($payment['billing_id'])) {
                    $billingModel = new BillingModel();
                    $billIds = array_filter(array_map('trim', explode(',', $payment['billing_id'])));
                    $amountRemaining = isset($payment['amount']) ? (float)$payment['amount'] : 0.0;

                    foreach ($billIds as $billId) {
                        if ($amountRemaining <= 0) break;
                        $bill = $billingModel->find((int)$billId);
                        if (!$bill) continue;

                        $outstanding = isset($bill['balance']) && $bill['balance'] > 0 ? (float)$bill['balance'] : (float)$bill['amount_due'];
                        $toApply = min($amountRemaining, $outstanding);

                        if ($toApply <= 0) continue;

                        $res = $billingModel->applyPaymentToBill((int)$billId, $toApply);
                        if ($res === false) {
                            log_message('error', 'paymentSuccess: failed to apply ' . $toApply . ' to bill ' . $billId);
                        }

                        $amountRemaining = round($amountRemaining - $toApply, 2);
                    }
                }

                log_message('info', 'Payment updated via redirect (webhook fallback) for user: ' . $userId);
        } else {
            log_message('info', 'Payment already processed by webhook for user: ' . $userId);
        }
    }

    session()->setFlashdata('payment_status', 'success');
    session()->setFlashdata('message', 'Payment completed successfully!');
    
    return redirect()->to(base_url('users'));
}

// Handle payment cancellation

public function paymentCancel()
    {
        $userId = session()->get('user_id');
        
        if ($userId) {
            // Mark the most recent payment as cancelled
            $paymentsModel = new PaymentsModel();
            $payment = $paymentsModel
                ->where('user_id', $userId)
                ->where('status', 'awaiting_payment')
                ->orderBy('created_at', 'DESC')
                ->first();

            if ($payment) {
                $paymentsModel->update($payment['id'], [
                    'status' => 'cancelled',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                log_message('info', 'Payment cancelled for user: ' . $userId . ', Payment ID: ' . $payment['id']);
            }
        }
        
        session()->setFlashdata('payment_status', 'cancelled');
        session()->setFlashdata('message', 'Payment was cancelled.');
        
        return redirect()->to(base_url('users'));
    }

// Manual cleanup method (can be called via URL for testing)
public function cleanupPayments()
    {
        // Only allow this in development or for testing
        if (ENVIRONMENT !== 'development') {
            return $this->response->setJSON(['error' => 'Not allowed in production']);
        }
        
        $paymentsModel = new PaymentsModel();
        
        // 1. Expire old awaiting payments (older than 30 minutes)
        $expiredCount = $paymentsModel
            ->where('status', 'awaiting_payment')
            ->where('created_at <', date('Y-m-d H:i:s', strtotime('-30 minutes')))
            ->set([
                'status' => 'expired', 
                'updated_at' => date('Y-m-d H:i:s')
            ])
            ->update();
        
        // 2. Cancel very old expired payments (older than 24 hours)
        $cancelledCount = $paymentsModel
            ->where('status', 'expired')
            ->where('created_at <', date('Y-m-d H:i:s', strtotime('-24 hours')))
            ->set([
                'status' => 'cancelled', 
                'updated_at' => date('Y-m-d H:i:s')
            ])
            ->update();
        
        // 3. Get current statistics
        $totalPending = $paymentsModel
            ->where('status', 'awaiting_payment')
            ->countAllResults();
            
        $totalExpired = $paymentsModel
            ->where('status', 'expired')
            ->countAllResults();
        
        // Log the cleanup activity
        log_message('info', 'Manual payment cleanup completed: ' . $expiredCount . ' expired, ' . $cancelledCount . ' cancelled');
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Payment cleanup completed successfully',
            'results' => [
                'expired_payments' => $expiredCount,
                'cancelled_payments' => $cancelledCount,
                'still_pending' => $totalPending,
                'total_expired' => $totalExpired
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }


//Fetch GCash settings
public function getGcashSettings()
{
    // If the GcashSettingsModel or the settings table does not exist yet,
    // return a safe response instead of throwing a fatal error.
    if (!class_exists('\App\Models\GcashSettingsModel')) {
        log_message('warning', 'GcashSettingsModel not found when calling getGcashSettings');
        return $this->response->setJSON([
            'success' => false,
            'message' => 'GCash settings not configured',
            'gcash_number' => null,
            'qr_code_url' => null
        ]);
    }

    try {
        $model = new \App\Models\GcashSettingsModel();
        $settings = $model->find(1) ?: $model->orderBy('id', 'DESC')->first();
        if (! $settings) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No GCash settings found',
                'gcash_number' => null,
                'qr_code_url' => null
            ]);
        }

        $qrUrl = !empty($settings['qr_code_path']) ? base_url($settings['qr_code_path']) : null;
        return $this->response->setJSON([
            'success' => true,
            'gcash_number' => $settings['gcash_number'] ?? null,
            'qr_code_url' => $qrUrl
        ]);
    } catch (\Exception $e) {
        log_message('error', 'getGcashSettings exception: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error fetching GCash settings',
            'gcash_number' => null,
            'qr_code_url' => null
        ])->setStatusCode(500);
    }
}

// Check disconnection status
public function getDisconnectionStatus()
{
    if (! session()->get('user_id')) {
        return $this->response->setJSON(['error' => 'Unauthenticated'])->setStatusCode(401);
    }

    $userId = session()->get('user_id');
    $billingModel = new \App\Models\BillingModel();

    // server timezone considerations: compare dates using YYYY-MM-DD
    $thresholdDate = date('Y-m-d', strtotime('+1 day'));

    $urgent = $billingModel
        ->select('id, bill_no, due_date, amount_due, COALESCE(balance,0) as balance, status, billing_month')
        ->where('user_id', $userId)
        ->where('status', 'pending')
        ->where('due_date <=', $thresholdDate)
        ->findAll();

    return $this->response->setJSON([
        'count' => count($urgent),
        'bills' => $urgent
    ]);
}

// Fetch payment bills via AJAX for Payments page
public function getPaymentBillsAjax()
{
    $limit = 1; // only fetch latest

    // Allow debug user_id via GET only in development
    $debugUserId = $this->request->getGet('user_id');
    if (ENVIRONMENT === 'development' && !empty($debugUserId)) {
        $userId = (int) $debugUserId;
    } else {
        $userId = session()->get('user_id');
    }

    if (empty($userId)) {
        return $this->response->setStatusCode(401)->setJSON([
            'status' => 'error',
            'message' => 'Unauthenticated',
            'bills' => [],
            'totals' => [
                'carryover' => 0.0,
                'currentBill' => 0.0,
                'totalOutstanding' => 0.0,
                'totalPaid' => 0.0
            ]
        ]);
    }

    $billingModel = new BillingModel();

    try {
        $billings = $billingModel->getBillsByUser($userId, $limit);
    } catch (\Throwable $e) {
        log_message('error', 'getPaymentBillsAjax: failed to fetch latest bill - ' . $e->getMessage());
        $billings = [];
    }

    $latestBill = !empty($billings) ? $billings[0] : null;

    if ($latestBill) {
        $amountDue = isset($latestBill['amount_due']) ? (float)$latestBill['amount_due'] : 0.0;
        $carryover = isset($latestBill['carryover']) ? (float)$latestBill['carryover'] : 0.0;
        $balance = ($latestBill['balance'] !== null) ? (float)$latestBill['balance'] : $amountDue;
        $totalOutstanding = $carryover + $balance;
        $responseBills = [$latestBill];
    } else {
        $amountDue = 0.0;
        $carryover = 0.0;
        $balance = 0.0;
        $totalOutstanding = 0.0;
        $responseBills = [];
    }

    $response = [
        'status' => 'success',
        'bills' => $responseBills,
        'totals' => [
            'carryover' => $carryover,
            'currentBill' => $amountDue,
            'totalOutstanding' => $totalOutstanding,
            'totalPaid' => 0.0
        ],
        // legacy keys for backward compatibility
        'carryover' => $carryover,
        'currentBill' => $amountDue,
        'totalOutstanding' => $totalOutstanding,
        'totalPaid' => 0.0,
    ];

    return $this->response->setJSON($response);
}

}