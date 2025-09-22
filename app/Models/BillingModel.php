<?php 

namespace App\Models;

use CodeIgniter\Model;

class BillingModel extends Model
{
    protected $table      = 'billings';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'amount', 'due_date', 'status', 'paid_date'];

    // 🔹 Custom method for unpaid bills admin side
    public function getUnpaidBills()
    {
        return $this->select('billings.id, users.username, billings.amount, billings.due_date, billings.status')
                    ->join('users', 'billings.user_id = users.id')
                    ->where('billings.status', 'unpaid')
                    ->orderBy('billings.due_date', 'ASC')
                    ->findAll();
    }

    // 🔹 Example: custom method for paid bills in last 12 months admin side
    public function getPaidBills()
    {
        return $this->select('billings.id, users.username, billings.amount, billings.due_date, billings.paid_date, billings.status')
                    ->join('users', 'billings.user_id = users.id')
                    ->where('billings.status', 'paid')
                    ->where('billings.paid_date >=', date('Y-m-d H:i:s', strtotime('-12 months')))
                    ->orderBy('billings.paid_date', 'DESC')
                    ->findAll();
    }

    // custom method for fecthing userUnpaidBills
    public function getUserUnpaidBills($user_id){
        return $this->where('user_id', $user_id)
                    ->where('status', 'unpaid')
                    ->orderBy('due_date', 'ASC')
                    ->findAll();
    }
    // custom method for fecthing userPaidBills
    public function getUserPaidBills($user_id, $months = 12 , $limit = 10){
        return $this->where('user_id', $user_id)
                    ->where('status', 'paid')
                    ->where('billings.paid_date >=', date('Y-m-d H:i:s', strtotime("-$months months")))
                    ->orderBy('paid_date', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
    //method for fecthing Monthly Expenses for charts
    public function getMonthlyExpenses($user_id){
        return $this->select("MONTH(due_date) as month, SUM(amount) as total")
                    ->where('user_id', $user_id)
                    ->where('status', 'paid')
                    ->groupBy('MONTH(due_date)')
                    ->orderBy('month', 'ASC')
                    ->findAll();
    }
    //method for fetching Yearly Expenses for charts
    public function getYearlyExpenses($user_id){
        return $this->select("YEAR(due_date) as year, SUM(amount) as total")
                    ->where('user_id', $user_id)
                    ->where('status', 'paid')
                    ->groupBy('YEAR(due_date)')
                    ->orderBy('year', 'ASC')
                    ->findAll();
    }
}
?>
