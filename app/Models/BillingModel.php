<?php 

namespace App\Models;

use CodeIgniter\Model;

class BillingModel extends Model
{
    protected $table      = 'billings';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'amount', 'due_date', 'status', 'paid_date'];

    // 🔹 Custom method for unpaid bills
    public function getUnpaidBills()
    {
        return $this->select('billings.id, users.username, billings.amount, billings.due_date, billings.status')
                    ->join('users', 'billings.user_id = users.id')
                    ->where('billings.status', 'unpaid')
                    ->orderBy('billings.due_date', 'ASC')
                    ->findAll();
    }

    // 🔹 Example: custom method for paid bills in last 12 months
    public function getPaidBills()
    {
        return $this->select('billings.id, users.username, billings.amount, billings.due_date, billings.paid_date, billings.status')
                    ->join('users', 'billings.user_id = users.id')
                    ->where('billings.status', 'paid')
                    ->where('billings.paid_date >=', date('Y-m-d H:i:s', strtotime('-12 months')))
                    ->orderBy('billings.paid_date', 'DESC')
                    ->findAll();
    }
}
?>
