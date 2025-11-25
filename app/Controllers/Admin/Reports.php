<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BillingModel;
use App\Models\UsersModel;

class Reports extends BaseController
{
    protected $billingModel;
    protected $usersModel;

    public function __construct()
    {
        $this->billingModel = new BillingModel();
        $this->usersModel = new UsersModel();
    }

    public function index()
    {
        // === Billing data ===
        $totalCollected = $this->billingModel
            ->whereIn('status', ['Paid', 'Over the Counter'])
            ->selectSum('amount_due')
            ->get()->getRow()->amount_due ?? 0;

        $unpaidCount = $this->billingModel
            ->where('status', 'Pending')
            ->countAllResults();

        $monthlyTotal = $this->billingModel
            ->whereIn('status', ['Paid', 'Over the Counter'])
            ->where('MONTH(billings.updated_at)', date('m'))
            ->where('YEAR(billings.updated_at)', date('Y'))
            ->selectSum('amount_due')
            ->get()->getRow()->amount_due ?? 0;

        // === User counts ===
        $active = $this->usersModel->where('status', 'approved')->countAllResults();
        $pending = $this->usersModel->where('status', 'pending')->countAllResults();
        $inactive = $this->usersModel->where('status', 'inactive')->countAllResults();
        $activeUsers = $active;

        // === Monthly paid bills ===
        $bills = $this->billingModel
            ->select("MONTHNAME(billings.updated_at) as month, SUM(amount_due) as total")
            ->whereIn('status', ['Paid', 'Over the Counter'])
            ->groupBy('MONTH(billings.updated_at)')
            ->orderBy('MONTH(billings.updated_at)', 'ASC')
            ->findAll();

        $months = [];
        $totals = [];

        foreach ($bills as $b) {
            $months[] = $b['month'];
            $totals[] = (float)$b['total'];
        }

        return view('admin/index', [
            'title' => 'Reports',
            'totalCollected' => $totalCollected,
            'unpaidCount' => $unpaidCount,
            'activeUsers' => $activeUsers,
            'monthlyTotal' => $monthlyTotal,
            'months' => json_encode($months),
            'totals' => json_encode($totals),
            'active' => $active,
            'pending' => $pending,
            'inactive' => $inactive
        ]);
    }
}
