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

        // 🔍 Debug logs
        log_message('debug', 'Webhook received: ' . $payload);
        log_message('debug', 'Paymongo-Signature header: ' . $signatureHeader);

        // 🧩 Verify signature
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

        // 🧠 Decode payload
        $data = json_decode($payload, true);
        if (!isset($data['data']['attributes']['data']['attributes'])) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                                  ->setBody('Invalid payload');
        }

        $attributes = $data['data']['attributes']['data']['attributes'];
        $paymentIntentId = $attributes['payment_intent_id'] ?? null;
        $status = $attributes['status'] ?? null;

        if ($paymentIntentId && $status) {
            $paymentModel = new PaymentsModel();

            // 🔄 Update payment record
            $paymentModel->where('payment_intent_id', $paymentIntentId)
                         ->set([
                             'status'     => $status,
                             'paid_at'    => ($status === 'paid' ? date('Y-m-d H:i:s') : null),
                             'updated_at' => date('Y-m-d H:i:s')
                         ])
                         ->update();

            log_message('debug', "Payment updated: {$paymentIntentId} → {$status}");
        }

        return $this->response->setStatusCode(ResponseInterface::HTTP_OK)
                              ->setBody('Webhook processed');
    }
}
