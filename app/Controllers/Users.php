<?php   

namespace App\Controllers;

use App\Models\BillingModel;

class Users extends BaseController
{
    public function index()
    {
        return view('users/index');
    }

    public function history()
    {  /*  // View user billing history

        //fetching user id from session
        $user_id = session()->get('user_id');

        //get limit from user input, default = 10
        //$limit = $this->request->getGet('limit') ?? 10;

        //loading billing model
       // $billingModel = new BillingModel();
        //$data['billings'] = $billingModel->getUserPaidBills($user_id, $limit);
        //$data['limit'] = $limit;
       



        $data['monthlyExpenses'] = $billingModel->getMonthlyExpenses($user_id);
        if(count($monthlyExpenses)>=2){
        $last = end($monthlyExpenses);
        $prev = prev($monthlyExpenses);
        $change = ($last['total'] - $prev['total'])/$prev['total']*100;
        $data['monthlyChange'] = round($change, 2);
        }else{
            $data['monthlyChange'] = null;
        }
        $data['yearlyExpenses'] = $billingModel->getYearlyExpenses($user_id);

        return view('users/history', $data);*/

       
      

    
    //static data for monthly expenses
    $data['monthlyExpenses'] = [
        ['month' => 'January'   , 'total' => 1200.50   ],
        ['month' => 'February'  , 'total' => 950.00    ],
        ['month' => 'March'     , 'total' => 1100.75   ],
        ['month' => 'April'     , 'total' => 1300.00   ],
        ['month' => 'May'       , 'total' => 1250.25   ],
        ['month' => 'June'      , 'total' => 1400.00   ],
        ['month' => 'July'      , 'total' => 1150.80   ],
        ['month' => 'August'    , 'total' => 1600.00   ],
        ['month' => 'September' , 'total' => 1700.60   ],
        ['month' => 'October'   , 'total' => 1800.90   ],
        ['month' => 'November'  , 'total' => 1900.00   ],
        ['month' => 'December'  , 'total' => 2000.75   ],
    ];

    // Calculate percent change month-over-month
    $monthlyExpensesWithChange = [];
    $prevTotal = null;
    foreach ($data['monthlyExpenses'] as $index => $expense) {
        $change = 0;
        if ($prevTotal !== null && $prevTotal != 0) {
            $change = round((($expense['total'] - $prevTotal) / $prevTotal) * 100, 2);
        }
        $monthlyExpensesWithChange[] = array_merge($expense, ['percent_change' => $change]);
        $prevTotal = $expense['total'];
    }
    //$data['monthlyExpenses'] = $monthlyExpenses;
    $data['monthlyExpensesWithChange'] = $monthlyExpensesWithChange;
    $data['yearlyExpenses'] = [
        ['year' => 2022, 'total' => 15000.00],
        ['year' => 2023, 'total' => 18000.50],
        ['year' => 2024, 'total' => 20000.75],
    ];
    return view('users/history', $data);
    }

    public function payments()
    {
        return view('users/payments');
    }

    public function pressure()
    {
        return view('users/pressure');
    }

    public function report()
    {
        return view('users/report');
    }

    public function profile()
    {
        return view('users/profile');
    }

    public function changePassword()
    {
        // Logic to change user password
        return view('users/changepassword');
    }


        public function updateProfile()
        {
            $request = $this->request;

            // Get POST data
            $phone   = $request->getPost('phone');
            $email   = $request->getPost('email');
            $street  = $request->getPost('street');
            $address = $request->getPost('address');

            // Load model
            $userModel = new \App\Models\UserInformationModel();

            // Assume user_id is stored in session
            $user_id = session()->get('user_id');

            // Update the database
            $updated = $userModel->update($user_id, [
                'phone'  => $phone,
                'email'  => $email,
                'street' => $street,
                'address'=> $address
            ]);

            // Return JSON response for AJAX
            return $this->response->setJSON([
                'status'  => $updated ? 'success' : 'error',
                'message' => $updated ? 'Profile updated successfully' : 'Failed to update profile'
            ]);
        }

    public function getBillingsAjax(){

        //For dynamic data from database
        /*  $limit   = (int) ($this->request->getGet('limit') ?? 10);
         $user_id = session()->get('user_id');

         $billingModel = new \App\Models\BillingModel();

           
         $billings = $billingModel->getUserPaidBills($user_id, 12, $limit);

         return $this->response->setJSON($billings);
 */



        $limit = (int) ($this->request->getGet('limit') ?? 10);

        log_message('debug', 'getBillingsAjax CALLED with limit = '.$limit);
        //mock static data 
        $billings =[
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
   
}