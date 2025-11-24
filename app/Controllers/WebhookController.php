<?php

namespace App\Controllers;

use App\Models\PaymentsModel;
use CodeIgniter\HTTP\ResponseInterface;

class WebhookController extends BaseController
{
    public function webhook()
    {
        $webhookSecret = getenv('PAYMONGO_WEBHOOK_SECRET');
        $signatureHeader = $this->request->getHeaderLine('Paymongo-Signature');
        $payload = $this->request->getBody();

        log_message('debug', 'Webhook received: ' . $payload);
        log_message('debug', 'Paymongo-Signature header: ' . $signatureHeader);

        if (!$signatureHeader) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_FORBIDDEN)
                                  ->setBody('Missing signature');
        }

        $parts = explode(',', $signatureHeader);
        $time = explode('=', $parts[0])[1] ?? '';
        $signature = explode('=', $parts[1])[1] ?? '';

        $computed = hash_hmac('sha256', $time . '.' . $payload, $webhookSecret);

        if (!hash_equals($computed, $signature)) {
            log_message('error', 'Invalid signature');
            return $this->response->setStatusCode(ResponseInterface::HTTP_FORBIDDEN)
                                  ->setBody('Invalid signature');
        }

        $data = json_decode($payload, true);
        if (!isset($data['data']['attributes']['data']['attributes'])) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                                  ->setBody('Invalid payload');
        }

        $attributes = $data['data']['attributes']['data']['attributes'];
        $paymentIntentId = $attributes['payment_intent_id'] ?? null;
        $status = $attributes['status'] ?? null;
        $amountPaid = ($attributes['amount'] ?? 0) / 100; // PayMongo sends amount in centavos

        if ($paymentIntentId && $status) {
            $paymentModel = new PaymentsModel();
            $billingModel = new \App\Models\BillingModel();

            $currentPayment = $paymentModel->where('payment_intent_id', $paymentIntentId)->first();

            if ($currentPayment && $currentPayment['status'] !== $status) {

                // Update payment status
                $updateData = [
                    'status'     => $status,
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                if ($status === 'paid') {
                    $updateData['paid_at'] = date('Y-m-d H:i:s');
                }

                $paymentModel->where('payment_intent_id', $paymentIntentId)
                             ->set($updateData)
                             ->update();

                // 🔥 APPLY PAYMENT TO BILLING IF PAID
                if ($status === 'paid' && !empty($currentPayment['billing_id'])) {

                    $billIds = explode(',', $currentPayment['billing_id']);
                    $remainingPayment = $amountPaid;

                    foreach ($billIds as $billId) {
                        $billId = trim($billId);
                        $bill = $billingModel->find($billId);

                        if (!$bill || $remainingPayment <= 0) {
                            continue;
                        }

                        $carryover = (float)($bill['carryover'] ?? 0);
                        $balance   = (float)($bill['balance'] ?? 0);

                        // Deduct from carryover first
                        if ($carryover > 0) {
                            $deduct = min($remainingPayment, $carryover);
                            $carryover -= $deduct;
                            $remainingPayment -= $deduct;
                        }

                        // Deduct from balance next
                        if ($remainingPayment > 0 && $balance > 0) {
                            $deduct = min($remainingPayment, $balance);
                            $balance -= $deduct;
                            $remainingPayment -= $deduct;
                        }

                        // Mark bill as Paid only if zero
                        $newStatus = ($carryover <= 0 && $balance <= 0) ? 'Paid' : 'Partial';

                        $billingModel->update($billId, [
                            'carryover' => $carryover,
                            'balance'   => $balance,
                            'status'    => $newStatus,
                            'paid_date' => date('Y-m-d H:i:s')
                        ]);
                    }
                }

                log_message('info', "Payment updated via webhook: {$paymentIntentId} → {$status}");
            } else {
                log_message('debug', "Payment unchanged or not found: {$paymentIntentId}");
            }
        }

        return $this->response->setStatusCode(ResponseInterface::HTTP_OK)
                              ->setBody('Webhook processed');
    }
}
