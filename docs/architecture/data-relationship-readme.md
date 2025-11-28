# Data Relationship Diagram — Capstone

This README documents the entities and relationships included in `data-relationship-diagram.mmd`.

## Overview
The system uses a relational DB (MySQL/MariaDB). The diagrams illustrate the core entities and their primary relationships.

## Entities & Keys (summary)
- `users` (PK: id)
  - stores auth and account-level fields
- `user_information` (PK: info_id, FK: user_id -> users.id)
  - one-to-one extended profile fields
- `billings` (PK: id, FK: user_id -> users.id)
  - periodic billing entries for a user
- `payments` (PK: id, FK: user_id -> users.id, FK: billing_id -> billings.id)
  - payment records; may be mapped to a billing entry
- `chat_messages` (PK: id, FK: user_id -> users.id, FK: admin_id -> admin.id)
  - stores inbound/outbound messages for the user chat and admin internal messages (with sender field to distinguish)
- `admin_chats` (PK: id, FK: sender_admin_id -> admin.id, FK: recipient_admin_id -> admin.id)
  - dedicated store for admin-to-admin messages and broadcasts
- `admin` (PK: id)
  - admin accounts (internal) for the admin dashboard
- `super_admin` (PK: id)
  - superadmin accounts (separate table) with elevated privileges
- `admin_activity_logs` (PK: id)
  - logs actor activity (actor_type, actor_id to reference admin or super_admin)

## Relationship notes
- `users` -> `user_information` is 1:1 (user can have one information record)
- `users` -> `billings` is 1:N (one user can have multiple monthly bills)
- `billings` -> `payments` is 1:N (a billing can have multiple payment attempts)
- `users` -> `payments` is 1:N (for record-keeping of payments placed by user)
- `chat_messages` contains either `user_id` or `admin_id` depending on the message sender. Some messages may be system broadcasts where neither field is set.
- `admin_chats` contains two `admin` foreign keys (sender_admin_id, recipient_admin_id) for admin-to-admin messages. `is_broadcast` can indicate all-admin messages.
- `admin_activity_logs.actor_type` designates whether the actor is `admin` or `super_admin` to make the single log table usable for both actor types.

## Index and FK Recommendations
- Add indexes on all FK columns: `user_id`, `billing_id`, `admin_id`, `sender_admin_id`, `recipient_admin_id`.
- Add unique index on `payments.payment_intent_id` to protect idempotency for gateway integrations.
- Add `external_id` as a unique index on `chat_messages` where used for idempotency.
- Add `created_at` indexes on major tables for fast range queries used in reporting.

## Constraints & Nullability
- Some fields are intentionally nullable:
  - `billing_id` in `payments` for off-system/manual payments
  - `user_id` in `chat_messages` for system broadcasts
  - `recipient_admin_id` in `admin_chats` for broadcast messages

## Recommendations
- Normalize heavy denormalized columns like `user_name` in `chat_messages` if space and accuracy are major concerns; otherwise maintain it for historic copies of user names at the time of a message.
- Add a moderation/audit trail table for `chat_messages` to store moderation actions if compliance or audits are required.

## Next Steps
- Create a migration snapshot in the `app/Database/Migrations` folder capturing these relationships if not already present.
- Add foreign key constraints in migrations (or via ORM) with `ON DELETE SET NULL` for messages and `ON DELETE CASCADE` for billing rows if appropriate.

If you'd like, I can generate SQL DDL migration snippets that match this ER diagram and add them as a migration file.
