<?php   

namespace App\Controllers;

use App\Models\BillingModel;
use App\Models\UserInformationModel;

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
        // Static monthly expenses
        $data['monthlyExpenses'] = [
            ['month' => 'January', 'total' => 1200.50],
            ['month' => 'February', 'total' => 950.00],
            ['month' => 'March', 'total' => 1100.75],
            ['month' => 'April', 'total' => 1300.00],
            ['month' => 'May', 'total' => 1250.25],
            ['month' => 'June', 'total' => 1400.00],
            ['month' => 'July', 'total' => 1150.80],
            ['month' => 'August', 'total' => 1600.00],
            ['month' => 'September', 'total' => 1700.60],
            ['month' => 'October', 'total' => 1800.90],
            ['month' => 'November', 'total' => 1900.00],
            ['month' => 'December', 'total' => 2000.75],
        ];

        // Calculate percentage change per month
        $monthlyExpensesWithChange = [];
        $prevTotal = null;
        foreach ($data['monthlyExpenses'] as $expense) {
            $change = 0;
            if ($prevTotal !== null && $prevTotal != 0) {
                $change = round((($expense['total'] - $prevTotal) / $prevTotal) * 100, 2);
            }
            $monthlyExpensesWithChange[] = array_merge($expense, ['percent_change' => $change]);
            $prevTotal = $expense['total'];
        }

        $data['monthlyExpensesWithChange'] = $monthlyExpensesWithChange;

        // Static yearly expenses
        $data['yearlyExpenses'] = [
            ['year' => 2022, 'total' => 15000.00],
            ['year' => 2023, 'total' => 18000.50],
            ['year' => 2024, 'total' => 20000.75],
        ];

        return view('users/history', $data);
    }

    // Show profile page
    public function profile()
    {
        return view('users/profile');
    }

    // Show change password page
    public function changePassword()
    {
        return view('users/changepassword');
    }

    // Update user profile info via AJAX
      // Update or insert profile info
    public function updateProfile()
    {
        $request = $this->request;
        $user_id = session()->get('user_id');
        $userModel = new UserInformationModel();

        $data = [
            'user_id' => $user_id,
            'phone'   => $request->getPost('phone'),
            'email'   => $request->getPost('email'),
            'street'  => $request->getPost('street'),
            'address' => $request->getPost('address'),
        ];

        // Check if record exists
        $existing = $userModel->find($user_id);

        if ($existing) {
            $updated = $userModel->update($user_id, $data);
        } else {
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
            return $this->response->setJSON(['error' => 'No session user_id']);
        }

        $userModel = new UserInformationModel();

        $user = $userModel
            ->select('users.username, user_information.phone, user_information.email, user_information.street, user_information.address')
            ->join('users', 'users.id = user_information.user_id', 'left')
            ->where('user_information.user_id', $user_id)
            ->first();

        return $this->response->setJSON($user ?? []);
    }

    public function payments()
    {
        return view('users/payments'); // create this view next
    }

public function createCheckout()
{
     $secretKey = env('STRIPE_SECRET_KEY');


    $payload = [
        "data" => [
            "attributes" => [
                "line_items" => [[
                    "amount" => 59900,
                    "currency" => "PHP",
                    "name" => "Water Bill Payment",
                    "quantity" => 1
                ]],
                "payment_method_types" => ["gcash", "card"],
                "success_url" => base_url('index.php?payment=success'),
                "cancel_url" => base_url('index.php?payment=cancel')
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
        ]
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($httpCode === 200 && isset($result['data']['attributes']['checkout_url'])) {
        // Redirect frontend to the checkout URL
        return redirect()->to($result['data']['attributes']['checkout_url']);
    }

    log_message('error', 'PayMongo Checkout Error: ' . print_r($result, true));

    return $this->response->setJSON([
        'error' => 'Unable to create checkout session',
        'details' => $result
    ]);
}

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


