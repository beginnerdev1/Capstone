<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class OverdueWarning extends BaseCommand
{
    protected $group = 'Payments';
    protected $name = 'overdue:warning';
    protected $description = 'Send warning emails to users approaching suspension due to overdue bills.';

    public function run(array $params = [])
    {
        // Determine mode: prefer --mode option, fallback to first positional param, default to 'months'
        $mode = CLI::getOption('mode'); // months or consecutive
        if (! $mode && ! empty($params) && in_array(strtolower($params[0]), ['months', 'consecutive'])) {
            $mode = strtolower($params[0]);
        }
        $mode = $mode ?: 'months';
        $dry = CLI::getOption('dry-run') !== null;

        // Configurable thresholds (defaults to 3 as requested)
        $monthsThreshold = (int)(CLI::getOption('months') ?: 3);
        $consecutiveThreshold = (int)(CLI::getOption('consecutive') ?: 3);

        CLI::write("Mode: {$mode}");
        CLI::write($dry ? 'Dry run: ON' : 'Dry run: OFF');
        CLI::write("Months threshold: {$monthsThreshold}, Consecutive threshold: {$consecutiveThreshold}");

        // Initialize DB and models early so debug/sample blocks can use them
        $db = \Config\Database::connect();
        $billingModel = new \App\Models\BillingModel();
        $usersModel = new \App\Models\UsersModel();
        $userInfoModel = new \App\Models\UserInformationModel();

        $showSamples = CLI::getOption('show-samples') !== null;
        // Robust CLI filtering: allow --only-email=email or --only-email email; accept positional fallback
        $onlyEmail = CLI::getOption('only-email');
        if (! $onlyEmail && ! empty($params)) {
            foreach ($params as $p) {
                if (filter_var($p, FILTER_VALIDATE_EMAIL)) {
                    $onlyEmail = $p;
                    break;
                }
            }
        }
        $onlyEmail = $onlyEmail ?: null;
        $onlyUserId = CLI::getOption('user-id') ? (int)CLI::getOption('user-id') : null;
        // Duplicate-send safeguard: skip sends if a SENT exists within last N days (default 7). Set 0 to disable.
        $dedupeDays = (int)(CLI::getOption('dedupe-days') ?: 7);
        if ($showSamples) {
            CLI::write('--- Sample unpaid billings (up to 10 rows) ---');
            $samples = $billingModel
                ->select('id, user_id, billing_month, due_date, status, amount_due, balance, carryover')
                ->where('status <>', 'Paid')
                ->orderBy('due_date', 'ASC')
                ->limit(10)
                ->findAll();

            if (empty($samples)) {
                CLI::write('(no unpaid billings found)');
            } else {
                foreach ($samples as $s) {
                    CLI::write(sprintf("id=%s user_id=%s billing_month=%s due_date=%s status=%s amount_due=%s balance=%s carryover=%s",
                        $s['id'] ?? '', $s['user_id'] ?? '', $s['billing_month'] ?? '', $s['due_date'] ?? '', $s['status'] ?? '', $s['amount_due'] ?? '', $s['balance'] ?? '', $s['carryover'] ?? ''
                    ));
                }
            }
            CLI::write('--- end samples ---');
        }

        
        $candidates = [];

        if ($mode === 'months') {
            // Find billings with billing_month older than N months and still pending/unpaid
            // Use billing_month (month of the bill) rather than due_date for the "N months overdue" semantics
            $thresholdMonth = date('Y-m-01', strtotime("-{$monthsThreshold} months"));
            CLI::write("Months threshold month start: {$thresholdMonth}");

            $builder = $billingModel->select('user_id, COUNT(*) as cnt, GROUP_CONCAT(id) as bill_ids')
                ->where('DATE(billing_month) <=', $thresholdMonth)
                ->groupBy('user_id');

            // Include rows where status is NULL or not 'Paid', or any positive outstanding
            $builder->groupStart()
                ->where('status IS NULL')
                ->orWhere('status <>', 'Paid')
                ->orWhere('balance >', 0)
                ->orWhere('amount_due >', 0)
            ->groupEnd();

            $rows = $builder->get()->getResultArray();

            foreach ($rows as $r) {
                $candidates[] = (int)$r['user_id'];
            }
        } else {
            $today = date('Y-m-d');
            CLI::write("Today: {$today}");
            // consecutive: build candidate users from billing rows that are past-due and have outstanding amounts
            $today = date('Y-m-d');

            $usersRows = $billingModel->distinct()->select('user_id')->groupStart()
                    ->where('balance >', 0)
                    ->orWhere('amount_due >', 0)
                ->groupEnd()
                ->where('DATE(due_date) <', $today)
                ->get()->getResultArray();

            $candidateUserIds = array_map(function ($r) { return (int)$r['user_id']; }, $usersRows ?: []);

            foreach ($candidateUserIds as $uid) {
                // Fetch latest billing rows for this user
                $rows = $billingModel
                    ->select('billing_month, status, due_date, amount_due, balance, carryover')
                    ->where('user_id', $uid)
                    ->orderBy('billing_month', 'DESC')
                    ->findAll(36);

                $consecutive = 0;
                $prevMonth = null;

                if ($showSamples) {
                    CLI::write("-- Evaluating user {$uid} (" . count($rows) . " bill rows)");
                }

                foreach ($rows as $r) {
                    $bm = substr(($r['billing_month'] ?? ''), 0, 7);
                    $dueDate = $r['due_date'] ?? null;
                    $outstanding = (float)($r['balance'] ?? $r['amount_due'] ?? 0) + (float)($r['carryover'] ?? 0);
                    $isPastDue = $dueDate ? (strtotime($dueDate) < strtotime($today)) : true;
                    $isUnpaid = (strtolower(trim($r['status'] ?? '')) !== 'paid') || ($outstanding > 0);

                    if ($showSamples) {
                        CLI::write(sprintf("  row: billing_month=%s due_date=%s outstanding=%s pastDue=%s unpaid=%s status=%s",
                            $r['billing_month'] ?? '', $dueDate ?? '', number_format($outstanding,2), $isPastDue ? 'Y' : 'N', $isUnpaid ? 'Y' : 'N', $r['status'] ?? 'NULL'
                        ));
                    }

                    if (! $isPastDue || ! $isUnpaid) {
                        break; // consecutive run stops
                    }

                    if ($consecutive === 0) {
                        $consecutive = 1;
                        $prevMonth = $bm;
                    } else {
                        $dt = \DateTime::createFromFormat('Y-m', $prevMonth);
                        if (! $dt) break;
                        $dt->modify('-1 month');
                        $expected = $dt->format('Y-m');
                        if ($bm === $expected) {
                            $consecutive++;
                            $prevMonth = $bm;
                        } else {
                            break; // gap
                        }
                    }

                    if ($consecutive >= $consecutiveThreshold) break;
                }

                if ($consecutive >= $consecutiveThreshold) {
                    $candidates[] = $uid;
                }
            }
        }

        $candidates = array_unique($candidates);

        CLI::write('Found ' . count($candidates) . ' candidate(s) for warning.');

        // Apply --only-email or --user-id filters if provided to limit recipients
        if ($onlyEmail) {
            CLI::write("Filtering to only email: {$onlyEmail}");
            $filtered = [];
            foreach ($candidates as $uid) {
                $u = $usersModel->find($uid);
                if ($u && isset($u['email']) && strtolower(trim($u['email'])) === strtolower(trim($onlyEmail))) {
                    $filtered[] = $uid;
                }
            }
            $candidates = $filtered;
        }

        if ($onlyUserId) {
            CLI::write("Filtering to only user-id: {$onlyUserId}");
            $candidates = array_values(array_intersect($candidates, [$onlyUserId]));
        }

        // Prepare DB logging
        $logTable = 'overdue_warning_logs';
        // Prepare fallback file logging path
        $logPath = WRITEPATH . 'logs/overdue_warning.log';

        if (empty($candidates)) {
            return;
        }

        // Prepare Brevo configuration
        $apiKey = getenv('BREVO_API_KEY') ?: null;
        if (!$apiKey) {
            CLI::warning('BREVO_API_KEY not configured; emails will not be sent.');
        }

        foreach ($candidates as $uid) {
            $user = $usersModel->find($uid);
            if (!$user) continue;

            $info = $userInfoModel->getByUserId($uid) ?? [];
            $recipientName = trim((($info['first_name'] ?? '') . ' ' . ($info['last_name'] ?? '')) ?: $user['email']);
            $toEmail = $user['email'] ?? null;

            $siteUrl = rtrim(base_url(), '/');
            $supportUrl = $siteUrl . '/contact';

            $subject = 'Warning: Account at risk of suspension';
            $now = date('Y-m-d H:i:s');
            $html = view('emails/user_suspension_warning', [
                'recipientName' => $recipientName,
                'email' => $toEmail,
                'detectedAt' => $now,
                'supportUrl' => $supportUrl,
                'mode' => $mode,
                'unpaidBills' => [],
            ]);

            // Attach unpaid/past-due billing rows to the email so recipients see details
            try {
                $unpaidRows = $billingModel->select('billing_month, due_date, status, amount_due, balance, carryover')
                    ->where('user_id', $uid)
                    ->groupStart()
                        ->where('balance >', 0)
                        ->orWhere('amount_due >', 0)
                        ->orWhere('status IS NULL')
                        ->orWhere('status <>', 'Paid')
                    ->groupEnd()
                    ->orderBy('billing_month', 'ASC')
                    ->findAll();

                $unpaidBills = [];
                foreach ($unpaidRows as $r) {
                    $unpaidBills[] = [
                        'billing_month' => $r['billing_month'] ?? null,
                        'due_date' => $r['due_date'] ?? null,
                        'status' => $r['status'] ?? null,
                        'amount_due' => $r['amount_due'] ?? 0,
                        'balance' => $r['balance'] ?? 0,
                        'carryover' => $r['carryover'] ?? 0,
                    ];
                }

                $html = view('emails/user_suspension_warning', [
                    'recipientName' => $recipientName,
                    'email' => $toEmail,
                    'detectedAt' => $now,
                    'supportUrl' => $supportUrl,
                    'mode' => $mode,
                    'unpaidBills' => $unpaidBills,
                ]);
            } catch (\Throwable $e) {
                // keep original html if billing fetch fails
            }

            if ($dry) {
                CLI::write("[DRY] Would send warning to user_id={$uid}, email={$toEmail}");
                // Log dry-run to DB, fallback to file
                try {
                    $db->table($logTable)->insert([
                        'user_id' => $uid ?: null,
                        'email' => $toEmail,
                        'status' => 'DRY',
                        'message' => null,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                } catch (\Throwable $e) {
                    @file_put_contents($logPath, date('Y-m-d H:i:s') . " [DRY] user_id={$uid} email={$toEmail}\n", FILE_APPEND);
                }
                continue;
            }

            // Duplicate-send safeguard: if dedupeDays > 0, check recent SENT
            if ($dedupeDays > 0 && ! empty($toEmail)) {
                try {
                    $cutoff = date('Y-m-d H:i:s', strtotime("-{$dedupeDays} days"));
                    $exists = $db->table($logTable)
                        ->where('email', $toEmail)
                        ->where('status', 'SENT')
                        ->where('created_at >=', $cutoff)
                        ->get()->getRowArray();
                    if ($exists) {
                        CLI::write("Skipping send to {$toEmail} — recent SENT found within {$dedupeDays} days.");
                        try {
                            $db->table($logTable)->insert([
                                'user_id' => $uid ?: null,
                                'email' => $toEmail,
                                'status' => 'SKIP_DUPLICATE',
                                'message' => "Recent SENT within {$dedupeDays} days",
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);
                        } catch (\Throwable $e) {
                            @file_put_contents($logPath, date('Y-m-d H:i:s') . " SKIP_DUPLICATE user_id={$uid} email={$toEmail}\n", FILE_APPEND);
                        }
                        continue;
                    }
                } catch (\Throwable $e) {
                    // ignore DB errors here and proceed to send; fallback will log if send fails
                }
            }

            if ($apiKey && !empty($toEmail)) {
                try {
                    $config = \Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $apiKey);
                    $apiInstance = new \Brevo\Client\Api\TransactionalEmailsApi(new \GuzzleHttp\Client(), $config);

                    $fromEmail = getenv('SMTP_FROM') ?: getenv('MAIL_FROM') ?: (getenv('SMTP_USER') ?: 'no-reply@localhost');
                    $fromName  = getenv('MAIL_FROM_NAME') ?: getenv('SMTP_FROM_NAME') ?: 'Support';

                    $payload = [
                        'subject' => $subject,
                        'sender' => ['name' => $fromName, 'email' => $fromEmail],
                        'to' => [['email' => $toEmail, 'name' => $recipientName]],
                        'htmlContent' => $html,
                        'textContent' => strip_tags($html),
                    ];

                    $emailObj = new \Brevo\Client\Model\SendSmtpEmail($payload);
                    $apiInstance->sendTransacEmail($emailObj);
                    CLI::write("Warning sent to {$toEmail}");
                    try {
                        $db->table($logTable)->insert([
                            'user_id' => $uid ?: null,
                            'email' => $toEmail,
                            'status' => 'SENT',
                            'message' => null,
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                    } catch (\Throwable $e) {
                        @file_put_contents($logPath, date('Y-m-d H:i:s') . " SENT user_id={$uid} email={$toEmail}\n", FILE_APPEND);
                    }
                } catch (\Throwable $e) {
                    CLI::error("Failed to send email to {$toEmail}: " . $e->getMessage());
                    try {
                        $db->table($logTable)->insert([
                            'user_id' => $uid ?: null,
                            'email' => $toEmail,
                            'status' => 'ERROR',
                            'message' => $e->getMessage(),
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                    } catch (\Throwable $e2) {
                        @file_put_contents($logPath, date('Y-m-d H:i:s') . " ERROR user_id={$uid} email={$toEmail} msg=" . $e->getMessage() . "\n", FILE_APPEND);
                    }
                }
            } else {
                CLI::warning("Skipping send for user_id={$uid} (no email or Brevo not configured)");
                try {
                    $db->table($logTable)->insert([
                        'user_id' => $uid ?: null,
                        'email' => $toEmail,
                        'status' => 'SKIP',
                        'message' => 'No email or Brevo not configured',
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                } catch (\Throwable $e) {
                    @file_put_contents($logPath, date('Y-m-d H:i:s') . " SKIP user_id={$uid} email={$toEmail}\n", FILE_APPEND);
                }
            }
        }
    }
}
