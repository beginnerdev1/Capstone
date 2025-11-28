<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Config\Database;

class DemoUsersBillingSeeder extends Seeder
{
    public function run()
    {
        $db = Database::connect();
        // Use a seeded timestamp generator so multiple inserts don't share the same second
        $seedTime = time();
        $nextTs = function() use (&$seedTime) {
            return date('Y-m-d H:i:s', $seedTime++);
        };
        $usersTable = $db->table('users');
        $infoTable = $db->table('user_information');
        $billingsTable = $db->table('billings');
        $paymentsTable = $db->table('payments');

        $year = date('Y');
        // Use the specific months requested: September, October, November
        $months = [
            sprintf('%s-09-01', $year),
            sprintf('%s-10-01', $year),
            sprintf('%s-11-01', $year),
        ];

        // Generate 2 users per purok (1..7) => 14 users
        for ($p = 1; $p <= 7; $p++) {
            for ($i = 1; $i <= 2; $i++) {
                $unique = sprintf('p%su%s', $p, $i);
                $email = "demo+{$unique}@example.local";
                $password = password_hash('Password123!', PASSWORD_DEFAULT);

                $userData = [
                    'email' => $email,
                    'password' => $password,
                    'status' => 'approved',
                    'is_verified' => 1,
                    'active' => 1,
                    'created_at' => $nextTs(),
                    'updated_at' => $nextTs(),
                ];

                $usersTable->insert($userData);
                $userId = (int)$db->insertID();

                // Create user information
                $first = 'Demo' . strtoupper(substr($unique, 0, 3));
                $last = 'User' . $p . $i;
                $phone = str_pad(rand(90000000000, 99999999999), 11, '0', STR_PAD_LEFT);
                $age = rand(20, 65);
                $family = rand(1, 6);
                $lineNumber = sprintf('%02d-%02d', $p, $i);

                $infoData = [
                    'user_id' => $userId,
                    'first_name' => $first,
                    'last_name' => $last,
                    'gender' => (rand(0,1) ? 'Male' : 'Female'),
                    'line_number' => $lineNumber,
                    'age' => $age,
                    'family_number' => $family,
                    'phone' => (string)$phone,
                    'purok' => $p,
                    'barangay' => 'Borlongan',
                    'municipality' => 'Dipaculao',
                    'province' => 'Aurora',
                    'zipcode' => '3203',
                    'created_at' => $nextTs(),
                    'updated_at' => $nextTs(),
                ];

                $infoTable->insert($infoData);

                // Keep track of previous unpaid amount to carryover
                $carryover = 0.00;

                foreach ($months as $mIndex => $mStart) {
                    // billing_month is first of month
                    $billingMonth = date('Y-m-d', strtotime($mStart));
                    // Due date roughly mid-month
                    $dueDate = date('Y-m-d', strtotime($billingMonth . ' +14 days'));

                    // amount between 100 and 300
                    $amount = round(rand(10000, 30000) / 100, 2); // two decimals

                    // Determine status: bias so some are paid, some unpaid, some overdue
                    $r = rand(1, 100);
                    if ($r <= 55) {
                        $status = 'Paid';
                    } elseif ($r <= 75) {
                        $status = 'Partial';
                    } elseif ($r <= 90) {
                        $status = 'Pending';
                    } else {
                        $status = 'Overdue';
                    }

                    // If previous carryover exists, include it
                    $effectiveCarryover = round($carryover, 2);

                    // Balance: if Paid => 0; Partial => some remaining; Pending/Overdue => full amount
                    if ($status === 'Paid') {
                        $balance = 0.00;
                    } elseif ($status === 'Partial') {
                        // paid some portion
                        $paidPortion = round($amount * (rand(20, 70) / 100), 2);
                        $balance = round($amount - $paidPortion, 2);
                    } else {
                        $balance = $amount;
                    }

                    // If previous month's unpaid -> carryover flows into this month
                    if ($effectiveCarryover > 0) {
                        // add carryover to this billing record
                        // record carryover separately; outstanding will be carryover + balance
                    }

                    $billNo = 'B' . strtoupper(substr(md5($userId . $billingMonth . rand()), 0, 10));

                    $billingData = [
                        'user_id' => $userId,
                        'bill_no' => $billNo,
                        'amount_due' => number_format($amount, 2, '.', ''),
                        'balance' => number_format($balance, 2, '.', ''),
                        'carryover' => number_format($effectiveCarryover, 2, '.', ''),
                        'status' => $status,
                        'billing_month' => $billingMonth,
                        'due_date' => $dueDate,
                        'paid_date' => $status === 'Paid' ? date('Y-m-d H:i:s', strtotime($billingMonth . ' +6 days')) : null,
                        'remarks' => null,
                        'created_at' => $nextTs(),
                        'updated_at' => $nextTs(),
                    ];

                    $billingsTable->insert($billingData);
                    $billingId = (int)$db->insertID();

                    // Randomly insert payments depending on status
                    if ($status === 'Paid') {
                        // create a payment that covers carryover + amount
                        $payAmount = round($effectiveCarryover + $amount, 2);
                        $paymentsTable->insert([
                            'user_id' => $userId,
                            'billing_id' => $billingId,
                            'payment_intent_id' => 'pi_' . bin2hex(random_bytes(6)),
                            'method' => (rand(0,1) ? 'gateway' : 'gcash'),
                            'reference_number' => 'REF' . rand(100000, 999999),
                            'amount' => number_format($payAmount, 2, '.', ''),
                            'currency' => 'PHP',
                            'status' => 'paid',
                            'paid_at' => $nextTs(),
                            'created_at' => $nextTs(),
                        ]);
                        // reset carryover
                        $carryover = 0.00;
                    } elseif ($status === 'Partial') {
                        // partial payment created and remaining carryover set
                        $paid = round(($amount - $balance) + ($effectiveCarryover > 0 ? min($effectiveCarryover, rand(0, (int)($effectiveCarryover*100))/100) : 0), 2);
                        if ($paid > 0) {
                            $paymentsTable->insert([
                                'user_id' => $userId,
                                'billing_id' => $billingId,
                                'payment_intent_id' => 'pi_' . bin2hex(random_bytes(6)),
                                'method' => 'gateway',
                                'reference_number' => 'REF' . rand(100000, 999999),
                                'amount' => number_format($paid, 2, '.', ''),
                                'currency' => 'PHP',
                                'status' => 'paid',
                                'paid_at' => $nextTs(),
                                'created_at' => $nextTs(),
                            ]);
                        }
                        // carryover persists into next month (unpaid portion + previous carryover)
                        $carryover = round($effectiveCarryover + $balance, 2);
                    } else {
                        // Pending or Overdue: no successful payment, maybe create failed attempts
                        // Chance to add a failed gateway transaction
                        if (rand(1, 100) <= 30) {
                            $paymentsTable->insert([
                                'user_id' => $userId,
                                'billing_id' => $billingId,
                                'payment_intent_id' => 'pi_' . bin2hex(random_bytes(6)),
                                'method' => 'gateway',
                                'reference_number' => 'REF' . rand(100000, 999999),
                                'amount' => number_format(round(rand(1000, (int)($amount*100))/100, 2), 2, '.', ''),
                                'currency' => 'PHP',
                                'status' => 'failed',
                                'paid_at' => null,
                                'created_at' => $nextTs(),
                            ]);
                        }
                        // carryover accumulates
                        $carryover = round($effectiveCarryover + $balance, 2);
                    }

                    // Small chance to add a random old failed transaction unrelated to current bill
                    if (rand(1, 100) <= 10) {
                        $paymentsTable->insert([
                            'user_id' => $userId,
                            'billing_id' => null,
                            'payment_intent_id' => 'pi_' . bin2hex(random_bytes(6)),
                            'method' => 'gateway',
                            'reference_number' => 'REF' . rand(100000, 999999),
                            'amount' => number_format(round(rand(1000, 5000)/100, 2), 2, '.', ''),
                            'currency' => 'PHP',
                            'status' => 'failed',
                            'paid_at' => null,
                            'created_at' => $nextTs(),
                        ]);
                    }
                } // end months
            }
        }

        // Done
        echo "Demo users and billings seeded.\n";
    }
}
