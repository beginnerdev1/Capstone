<?php   

namespace App\Controllers;

use App\Models\BillingModel;
use App\Models\UserInformationModel;
use App\Models\PaymentsModel;

class Users extends BaseController
{
    // Show users index page
    public function index()
    {
        return view('users/index');
    }

    // Show billing history page with sample data
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
                            ->where('user_id', $userId)
                            ->orderBy('created_at', 'DESC')
                            ->findAll();

    // Load the history view (file: app/Views/Users/history.php)
    return view('Users/history', $data);
}

    // Show profile page
    public function profile()
    {
        return view('users/profile');
    }

    // Show change password page
    public function changePassword()
    {
        $session = session();
        $userId = $session->get('user_id');

        if (!$userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not logged in']);
        }

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found']);
        }

        $currentPassword = trim($this->request->getPost('current_password'));
        $newPassword     = trim($this->request->getPost('new_password'));
        $confirmPassword = trim($this->request->getPost('confirm_password'));

        // 🔒 Verify current password
        if (!password_verify($currentPassword, $user['password'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Incorrect Current Password']);
        }

        // 🔒 Confirm password match
        if ($newPassword !== $confirmPassword) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Incorrect Confirm Password']);
        }

        // 🔒 Password strength validation
        if (
            strlen($newPassword) < 8 ||
            !preg_match('/[A-Z]/', $newPassword) ||
            !preg_match('/[a-z]/', $newPassword) ||
            !preg_match('/[0-9]/', $newPassword)
        ) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Password must include at least 8 characters, 1 uppercase, 1 lowercase, and 1 number.']);
        }

        // ✅ Hash new password and update
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $userModel->update($userId, ['password' => $hashedPassword]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Password changed successfully']);
    }


    // Update user profile info via AJAX
      // Update or insert profile info
public function updateProfile()
{
    $request = $this->request;
    $user_id = session()->get('user_id');

    if (!$user_id) {
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'User not logged in'
        ]);
    }

    $userModel = new \App\Models\UserInformationModel();
    $existing  = $userModel->find($user_id);

    $data = [
        'first_name'   => $request->getPost('first_name') ?? null,
        'last_name'    => $request->getPost('last_name') ?? null,
        'gender'       => $request->getPost('gender') ?? null,
        'phone'        => $request->getPost('phone') ?? null,
        'email'        => $request->getPost('email') ?? null,
        'purok'        => $request->getPost('purok') ?? null,
        'street'       => $request->getPost('street') ?? null,
        'barangay'     => $request->getPost('barangay') ?? null,
        'municipality' => $request->getPost('municipality') ?? null,
        'province'     => $request->getPost('province') ?? null,
    ];

    // ✅ Save profile picture to /public/uploads/
    $file = $request->getFile('profile_picture');
    if ($file && $file->isValid() && !$file->hasMoved()) {
        $uploadPath = FCPATH . 'uploads/'; // <--- changed from WRITEPATH
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $newName = $file->getRandomName();
        $file->move($uploadPath, $newName);
        $data['profile_picture'] = $newName;

        // Delete old file if it exists
        if ($existing && !empty($existing['profile_picture']) && file_exists($uploadPath . $existing['profile_picture'])) {
            unlink($uploadPath . $existing['profile_picture']);
        }
    }

    // Save to database
    if ($existing) {
        $updated = $userModel->update($user_id, $data);
    } else {
        $data['user_id'] = $user_id;
        $updated = $userModel->insert($data);
    }

    return $this->response->setJSON([
        'status'  => $updated ? 'success' : 'error',
        'message' => $updated ? 'Profile saved successfully' : 'Failed to save profile'
    ]);
}



    // Return billing data via AJAX
    public function getBillingsAjax()
    {
        $limit = (int) ($this->request->getGet('limit') ?? 10);

        // Mock billing data
        $billings = [
            ['id'=>302, 'amount'=>950.00,  'due_date'=>'2025-07-01', 'status'=>'paid'],
            ['id'=>303, 'amount'=>1100.75, 'due_date'=>'2025-06-01', 'status'=>'paid'],
            ['id'=>304, 'amount'=>1300.00, 'due_date'=>'2025-05-01', 'status'=>'paid'],
            ['id'=>305, 'amount'=>1250.25, 'due_date'=>'2025-04-01', 'status'=>'paid'],
            ['id'=>306, 'amount'=>1400.00, 'due_date'=>'2025-03-01', 'status'=>'paid'],
            ['id'=>307, 'amount'=>1150.80, 'due_date'=>'2025-02-01', 'status'=>'paid'],
            ['id'=>308, 'amount'=>1600.00, 'due_date'=>'2025-01-01', 'status'=>'paid'],
            ['id'=>309, 'amount'=>1700.60, 'due_date'=>'2024-12-01', 'status'=>'paid'],
            ['id'=>310, 'amount'=>1800.90, 'due_date'=>'2024-11-01', 'status'=>'paid'],
            ['id'=>311, 'amount'=>1900.00, 'due_date'=>'2024-10-01', 'status'=>'paid'],
            ['id'=>312, 'amount'=>2000.75, 'due_date'=>'2024-09-01', 'status'=>'paid'],
        ];

        $data = array_slice($billings, 0, $limit);

        return $this->response->setJSON($data);
    }

    // Return profile info via AJAX
public function getProfileInfo()
{
    $user_id = session()->get('user_id');

    if (!$user_id) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'No session user_id'
        ]);
    }

    $userModel = new UserInformationModel();

    $user = $userModel
        ->select('user_information.first_name, user_information.last_name, user_information.gender, user_information.phone, user_information.email, user_information.purok, user_information.barangay, user_information.municipality, user_information.province, user_information.profile_picture')
        ->where('user_information.user_id', $user_id)
        ->first();

    if (!$user) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'User not found'
        ]);
    }

    return $this->response->setJSON([
        'status' => 'success',
        'data' => $user
    ]);
}

//Profile controller
    public function getProfilePicture($filename)
    {
        $filePath = FCPATH . 'uploads/' . $filename;

        if (file_exists($filePath)) {
            return $this->response->download($filePath, null, true); // ✅ display inline
        } else {
            return $this->response->setStatusCode(404)->setBody('File not found');
        }
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
    public function paymentSuccess()
    {
        session()->setFlashdata('payment_status', 'success');
        return redirect()->to(base_url('users/index'));
    }

    public function paymentFailed()
    {
        session()->setFlashdata('payment_status', 'failed');
        return redirect()->to(base_url('users/index'));
    }



}


