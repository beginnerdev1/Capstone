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
        $data['payments'] = $paymentsModel
        ->select('payments.*, billings.bill_no, billings.due_date')
        ->join('billings', 'billings.id = payments.billing_id', 'left')
        ->where('payments.user_id', $userId)
        ->orderBy('payments.created_at', 'DESC')
        ->findAll();

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
                // Mark user as pending with complete profile
                $userModel->update($userId, [
                    'profile_complete' => 1,
                    'status' => 'pending'
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
        $userId = session()->get('user_id'); // Fixed: was 'id', now 'user_id'

        $billingModel = new BillingModel();

        // Fetch bills belonging to the logged-in user
        $billings = $billingModel
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll($limit);

        // Optionally format the data for JSON (for cleaner output)
        $data = array_map(function ($bill) {
            return [
                'id'         => $bill['id'],
                'bill_no'    => $bill['bill_no'],
                'amount'     => (float) $bill['amount_due'],
                'due_date'   => $bill['due_date'],
                'status'     => ucfirst($bill['status']),
                'month'      => $bill['billing_month'] ?? '',
            ];
        }, $billings);

        return $this->response->setJSON($data);
    }


// Return specific bill details via AJAX for the manual transaction
public function getBillDetails()
{
    $billId = $this->request->getGet('bill_id');
    $billingModel = new \App\Models\BillingModel();
    $bill = $billingModel->find($billId);

    if ($bill) {
        return $this->response->setJSON([
            'bill_no' => $bill['bill_no'],
            'billing_month' => $bill['billing_month'],
            'due_date' => $bill['due_date'],
            'amount_due' => $bill['amount_due']
        ]);
    } else {
        return $this->response->setJSON([]);
    }
}










    // Show payments page Null pa
    public function payments()
    {
        $userId = session()->get('user_id');
        
        if (!$userId) {
            return redirect()->to('/login');
        }

        $billingModel = new BillingModel();
        
        // Get pending bills for the current user
        $pendingBills = $billingModel->getPendingBillingsByUserAndMonth($userId);
        
        // Calculate totals
        $totalAmount = 0;
        $serviceFee = 1.99; // Fixed service fee
        
        foreach ($pendingBills as $bill) {
            $totalAmount += $bill['amount_due'];
        }
        
        $finalTotal = $totalAmount + $serviceFee;
        
        // Get the latest bill for invoice details
        $latestBill = !empty($pendingBills) ? $pendingBills[0] : null;
        
        $data = [
            'bills' => $pendingBills,
            'totalAmount' => $totalAmount,
            'serviceFee' => $serviceFee,
            'finalTotal' => $finalTotal,
            'latestBill' => $latestBill,
            'invoiceId' => $latestBill ? $latestBill['bill_no'] : 'N/A',
            'dueDate' => $latestBill ? date('F j, Y', strtotime($latestBill['due_date'])) : 'N/A'
        ];
        
        return view('users/payments', $data);
    }
    
// Create PayMongo checkout session
public function createCheckout()
{
    try {
        $secretKey = env('PAYMONGO_SECRET_KEY'); // Fixed environment variable name
        $userId = session()->get('user_id');
        
        // Validate user session
        if (!$userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Authentication required'
            ]);
        }

        // Get and validate input
        $totalAmount = filter_var($this->request->getPost('total_amount'), FILTER_VALIDATE_FLOAT);
        $billIds = trim($this->request->getPost('bill_ids') ?? '');
        
        if ($totalAmount === false || $totalAmount <= 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid payment amount'
            ]);
        }

        if (empty($billIds)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No bills selected for payment'
            ]);
        }

        $paymentsModel = new PaymentsModel();
        
        // CHECK FOR EXISTING PENDING PAYMENTS - Prevent duplicates
        $existingPendingPayment = $paymentsModel
            ->where('user_id', $userId)
            ->where('billing_id', $billIds)
            ->whereIn('status', ['awaiting_payment', 'pending'])
            ->where('created_at >', date('Y-m-d H:i:s', strtotime('-30 minutes'))) // Only check recent payments
            ->first();

        if ($existingPendingPayment) {
            log_message('info', 'Duplicate payment attempt blocked for user: ' . $userId);
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'A payment for these bills is already in progress. Please wait a few minutes before trying again.'
            ]);
        }

        // CHECK DAILY PAYMENT ATTEMPTS LIMIT
        $todayAttempts = $paymentsModel
            ->where('user_id', $userId)
            ->where('method', 'gateway')
            ->where('DATE(created_at)', date('Y-m-d'))
            ->countAllResults();

        if ($todayAttempts >= 5) { // Maximum 5 attempts per day
            log_message('warning', 'Payment attempt limit exceeded for user: ' . $userId);
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'You have exceeded the daily payment attempt limit. Please try again tomorrow or contact support.'
            ]);
        }

        // EXPIRE OLD PENDING PAYMENTS before creating new one
        $this->expireOldPayments($userId);

        // Verify bill ownership and calculate total
        $billingModel = new BillingModel();
        $billIdArray = array_filter(explode(',', $billIds));
        
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

        // Calculate and verify amount
        $calculatedTotal = array_sum(array_column($userBills, 'amount_due'));
        $serviceFee = 1.99;
        $expectedTotal = $calculatedTotal + $serviceFee;

        if (abs($totalAmount - $expectedTotal) > 0.01) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Payment amount verification failed'
            ]);
        }

        // Convert to centavos for PayMongo
        $amount = (int)($totalAmount * 100);

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
            log_message('error', 'PayMongo API cURL error: ' . $curlError);
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Payment gateway connection failed'
            ]);
        }

        $result = json_decode($response, true);
        log_message('debug', 'PayMongo checkout response: ' . $response);

        if ($httpCode === 200 && isset($result['data']['id'])) {
            $checkoutUrl = $result['data']['attributes']['checkout_url'];
            $paymentIntentId = $result['data']['attributes']['payment_intent']['id'] ?? null;

            if ($paymentIntentId) {
                // Create payment record with tracking fields
                $paymentData = [
                    'user_id' => $userId,
                    'billing_id' => $billIds,
                    'payment_intent_id' => $paymentIntentId,
                    'method' => 'gateway',
                    'amount' => $totalAmount,
                    'currency' => 'PHP',
                    'status' => 'awaiting_payment',
                    'expires_at' => date('Y-m-d H:i:s', strtotime('+30 minutes')),
                    'attempt_number' => $todayAttempts + 1,
                    'created_at' => date('Y-m-d H:i:s')
                ];

                $paymentId = $paymentsModel->insert($paymentData);
                
                if (!$paymentId) {
                    log_message('error', 'Failed to create payment record for user: ' . $userId);
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Failed to initialize payment'
                    ]);
                }

                log_message('info', 'Payment session created for user: ' . $userId . ', Payment ID: ' . $paymentId);

                return redirect()->to($checkoutUrl);
            }
        }

        // Handle API errors
        $errorMessage = 'Payment gateway error';
        if (isset($result['errors']) && is_array($result['errors'])) {
            $errorDetails = array_map(function($error) {
                return $error['detail'] ?? 'Unknown error';
            }, $result['errors']);
            $errorMessage = implode('; ', $errorDetails);
        }

        log_message('error', 'PayMongo API error (HTTP ' . $httpCode . '): ' . $response);
        
        return $this->response->setJSON([
            'status' => 'error',
            'message' => $errorMessage
        ]);

    } catch (\Exception $e) {
        log_message('error', 'Exception in createCheckout: ' . $e->getMessage());
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'An unexpected error occurred while processing payment'
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

        
    // Handle payment success and failure redirects
public function paySuccess()
    {
        $session = session();
        $userId = $session->get('user_id');

        $paymentModel = new \App\Models\PaymentModel();
        $billingModel = new \App\Models\BillingModel();

        // 1️⃣ Find the latest pending bill of the user
        $latestBill = $billingModel
            ->where('user_id', $userId)
            ->where('status', 'Pending')
            ->orderBy('id', 'DESC')
            ->first();

        // 2️⃣ Create the payment record
        $paymentData = [
            'user_id' => $userId,
            'amount'  => $latestBill ? $latestBill['amount_due'] : 0,
            'status'  => 'Paid',
            'billing_id' => $latestBill ? $latestBill['id'] : null,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $paymentModel->insert($paymentData);

        // 3️⃣ Update the billing record to mark as paid
        if ($latestBill) {
            $billingModel->update($latestBill['id'], [
                'status' => 'Paid',
                'paid_date' => date('Y-m-d H:i:s'),
            ]);
        }

        // Redirect or load success page
        return redirect()->to('/user/payments')->with('success', 'Payment successful!');
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
public function uploadProof()
{
    $paymentModel = new PaymentsModel();

    $validation = \Config\Services::validation();
    $validation->setRules([
        'referenceNumber' => 'required|min_length[5]',
        'screenshot'      => 'uploaded[screenshot]|is_image[screenshot]|max_size[screenshot,2048]',
        'amount'          => 'required|decimal|greater_than[0]',
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        return redirect()->back()->with('error', $validation->listErrors());
    }

    $reference = trim($this->request->getPost('referenceNumber'));

    // Duplicate guard
    if ($paymentModel->where('reference_number', $reference)->first()) {
        return redirect()->back()->with('error', 'This reference number is already in use.');
    }

    $file = $this->request->getFile('screenshot');
    $amountPaid = floatval($this->request->getPost('amount')); // Get actual amount paid

    if ($file && $file->isValid() && !$file->hasMoved()) {
        $safeReference = preg_replace('/[^A-Za-z0-9_\-]/', '_', $reference);
        $timestamp     = date('Ymd_His');
        $newName       = $safeReference . '_' . $timestamp . '.' . $file->getExtension();

        $file->move(FCPATH . 'uploads/receipts', $newName);

        $paymentModel->insert([
            'billing_id'        => $this->request->getPost('billing_id') ?? null,
            'payment_intent_id' => uniqid('manual_'),
            'payment_method_id' => null,
            'method'            => 'manual',
            'reference_number'  => $reference,
            'admin_reference'   => null,
            'receipt_image'     => 'uploads/receipts/' . $newName,
            'amount'            => $amountPaid, // Use actual amount paid, no service fee
            'currency'          => 'PHP',
            'status'            => 'pending',
            'user_id'           => session()->get('user_id'),
            'paid_at'           => null,
        ]);
        
        $billingModel = new BillingModel();

        // Update all user's billings to 'Pending' upon new proof submission
        $billingModel->where('user_id', session()->get('user_id'))
             ->orderBy('id', 'DESC')
             ->set(['status' => 'Pending'])
             ->update();

        // Redirect to Users::index (homepage)
        return redirect()->to('users')->with('success', 'Payment proof submitted successfully!');
    }

    return redirect()->back()->with('error', 'File upload failed.');
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

            // Update billing records
            if (!empty($payment['billing_id'])) {
                $billingModel = new BillingModel();
                $billIds = explode(',', $payment['billing_id']);
                
                foreach ($billIds as $billId) {
                    $billingModel->update(trim($billId), [
                        'status' => 'Paid',
                        'paid_date' => date('Y-m-d H:i:s')
                    ]);
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
    $model = new \App\Models\GcashSettingsModel();
    $settings = $model->find(1) ?: $model->orderBy('id', 'DESC')->first();
    $qrUrl = $settings['qr_code_path'] ? base_url($settings['qr_code_path']) : null;
    return $this->response->setJSON([
        'success' => true,
        'gcash_number' => $settings['gcash_number'] ?? null,
        'qr_code_url' => $qrUrl
    ]);
}

}