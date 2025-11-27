CLI remediation tool — Pseudocode

File: tools/fix_failed_payments.php
Purpose: Interactive script to find and optionally mark awaiting gateway payments as rejected (for missed updates).

Pseudocode (CLI):

function main():
    printHeader()
    prompt = ask("Enter user ID or billing ID to target, or ALL to scan all users:")
    if prompt is empty: exit

    candidates = findCandidateRows(prompt)
    if candidates.count === 0:
        print("No candidate rows found.")
        exit

    printCandidates(candidates)
    confirm = ask("Type YES to apply updates:")
    if confirm !== 'YES':
        print("Aborted - no changes made.")
        exit

    beginTransaction()
    updated = 0
    for row in candidates:
        if row.status === 'awaiting_payment' and meetsCriteria(row):
            update payments set status='rejected', admin_reference = 'cli_remediation: superseded', updated_at = now where id = row.id
            updated += 1
    commitTransaction()

    print("Completed. Rows updated: " + updated)

function findCandidateRows(target):
    if target === 'ALL':
        select * from payments where method='gateway' and status='awaiting_payment' and expires_at < now() - someGracePeriod
    else if looksLikeBillingId(target):
        select * from payments where billing_id = target and method='gateway' and status='awaiting_payment'
    else:
        select * from payments where user_id = target and method='gateway' and status='awaiting_payment'

Notes and safety
- The script must require explicit confirmation (literal YES) before any DB updates.
- Always run inside a DB transaction to avoid partial updates.
- Provide a dry-run mode (print summary, do not change) and a --yes flag for automation if desired.
- Log actions with timestamp and operator (if available) for auditability.
