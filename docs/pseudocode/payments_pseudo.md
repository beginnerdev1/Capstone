Payments subsystem — Pseudocode

Purpose: Handle gateway sessions, manual payments, supersede/reject logic, and failed-transactions selection rules.

Key DB table: `payments` (columns include id, user_id, billing_id, method, status, amount, expires_at, created_at, paid_at, admin_reference)

High-level flows

1) Creating a gateway session (checkout)
Controller: Users->createCheckout(request)
- validate billing_id and user
- begin DB transaction
- // Supersede previous awaiting gateway sessions for same user
- PaymentsModel.rejectAwaitingForUser(user_id, note='superseded_by_new_gateway_payment')
- create new payment row with method='gateway', status='awaiting_payment', expires_at = now + TTL
- commit transaction
- return gateway/session info

Model: PaymentsModel.rejectAwaitingForUser(user_id, note)
- UPDATE payments
    SET status='rejected', admin_reference = note, updated_at = now
  WHERE user_id = user_id AND method='gateway' AND status = 'awaiting_payment' AND created_at between someRange
- (range may be used if intended to restrict to same-day; implement carefully using created_at between startOfDay and endOfDay when required)

2) Admin confirms manual payment
Controller: Payments->confirmManualPayment(payment_id, adminReference)
- find payment row
- if payment.method is manual and not paid:
    - mark payment.status = 'paid'
    - set paid_at = now
    - set admin_reference = adminReference
    - // Reject same-day awaiting gateway sessions for this user
    - PaymentsModel.rejectAwaitingForUserSameDay(user_id, note='rejected_due_to_manual_payment')
- return success

3) Rejecting same-day awaiting gateway rows (helper)
Model: rejectAwaitingForUserSameDay(user_id, note)
- determine day range = startOfDay(paid_at) .. endOfDay(paid_at)
- UPDATE payments
    SET status='rejected', admin_reference=note
  WHERE user_id = user_id AND method='gateway' AND status='awaiting_payment' AND created_at BETWEEN dayStart AND dayEnd

4) Failed Transactions view selection (Admin->getFailedPaymentsData)
- Goal: include rows considered failed or rejected, including those expired or cancelled

Pseudocode:
function getFailedPaymentsData(month, year):
    start = first day of month at 00:00:00
    end = last day of month at 23:59:59
    SELECT * FROM payments p
    WHERE (
        p.status IN ('failed', 'rejected')
        OR (p.expires_at IS NOT NULL AND p.expires_at < now() AND p.status != 'paid')
        OR p.status = 'cancelled'
    )
    AND (p.expires_at BETWEEN start AND end OR p.created_at BETWEEN start AND end OR p.paid_at BETWEEN start AND end)
    ORDER BY p.created_at DESC

Notes and best practices:
- Avoid using DATE(created_at) comparisons when precise ranges matter; use explicit BETWEEN start/end timestamps to avoid timezone/DB function differences and improve index use.
- Use DB transactions for multi-step state changes that must be atomic (e.g., marking paid + rejecting other rows).
- When marking rows as rejected/superseded, annotate `admin_reference` with a human-readable reason and optionally admin id + timestamp.
- Implement safeguards to avoid double-processing (e.g., check current status before update).
- Provide CLI remediation for missed or legacy rows (`tools/fix_failed_payments.php`).
