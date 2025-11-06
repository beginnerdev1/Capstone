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
        $userId = session()->get('id'); // get the logged-in user's ID

        $billingModel = new \App\Models\BillingModel();

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














    // Show payments page Null pa
    public function payments()
    {
        return view('users/payments'); 
    }

    
// Create PayMongo checkout session
public function createCheckout()
{
    $secretKey = env('STRIPE_SECRET_KEY'); // Set this in .env
    $userId    = session()->get('user_id');
    $amount    = 5900; // ₱59.00 in centavos

    $payload = [
        "data" => [
            "attributes" => [
                "line_items" => [[
                    "amount"   => $amount,
                    "currency" => "PHP",
                    "name"     => "Water Bill Payment",
                    "quantity" => 1
                ]],
                "payment_method_types" => ["gcash", "card"],
                "success_url" => base_url('users?payment=success'),
                "cancel_url"  => base_url('users?payment=cancel')
            ]
        ]
    ];

    $ch = curl_init('https://api.paymongo.com/v1/checkout_sessions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($secretKey . ':')
        ]
    ]);

    $response  = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);

    log_message('debug', 'Checkout response: ' . json_encode($result));

    if ($httpCode === 200 && isset($result['data']['id'])) {
        $checkoutUrl = $result['data']['attributes']['checkout_url'];

        // ✅ Get the Payment Intent ID (important!)
        $paymentIntentId = $result['data']['attributes']['payment_intent']['id'] ?? null;

        if ($paymentIntentId) {
            $paymentsModel = new \App\Models\PaymentsModel();
            $paymentsModel->insert([
                'billing_id'        => $this->request->getPost('billing_id') ?? null,
                'payment_intent_id' => $paymentIntentId,
                'amount'            => $amount,
                'currency'          => 'PHP',
                'status'            => 'awaiting_payment',
                'user_id'           => $userId,
                'created_at'        => date('Y-m-d H:i:s'),
            ]);
        } else {
            log_message('error', '⚠️ Missing payment_intent_id in PayMongo response.');
        }

        return redirect()->to($checkoutUrl);
    }

    return $this->response->setJSON([
        'error'   => 'Unable to create checkout session',
        'details' => $result
    ]);
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
        session()->setFlashdata('payment_status', 'failed');
        return redirect()->to(base_url('users/index'));
    }

    //payment proof   
    public function paymentProof()
    {
        return view('users/payment_proof'); // your file: app/Views/payment_proof.php
    }


    //upload Proof
public function uploadProof()
{
    $paymentModel = new PaymentsModel();

    $validation = \Config\Services::validation();
    $validation->setRules([
        'referenceNumber' => 'required|min_length[5]',
        'screenshot'      => 'uploaded[screenshot]|is_image[screenshot]|max_size[screenshot,2048]',
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
    if ($file && $file->isValid() && !$file->hasMoved()) {
        $safeReference = preg_replace('/[^A-Za-z0-9_\-]/', '_', $reference);
        $timestamp     = date('Ymd_His');
        $newName       = $safeReference . '_' . $timestamp . '.' . $file->getExtension();

        $file->move(FCPATH . 'uploads/receipts', $newName);

        $paymentModel->insert([
            'billing_id'       => $this->request->getPost('billing_id') ?? null,
            'payment_intent_id' => uniqid('manual_'),
            'payment_method_id' => null,
            'method'            => 'manual',
            'reference_number'  => $reference,
            'admin_reference'   => null,
            'receipt_image'     => 'uploads/receipts/' . $newName,
            'amount'            => 0,
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
        // Or: return redirect()->route('home')->with('success', 'Payment proof submitted successfully!');
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



}


