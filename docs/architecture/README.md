# System Architecture — Capstone

This directory contains the system architecture diagram and explanation for the Capstone project.

## Files
- `system-architecture.mmd` — Mermaid source for the diagram (recommended). Copy into any Mermaid-compatible renderer (GitHub supports Mermaid in .md files).

## Diagram Summary
The diagram represents the current and recommended architecture for Capstone (a CodeIgniter 4 application). Key components:

- Browser Clients (Users, Admin, SuperAdmin) connect via HTTPS through a Load Balancer and CDN.
- Web servers (Nginx or Apache) serve the public `index.php` entry points and static assets via the CDN.
- PHP-FPM runs the CodeIgniter app; the application handles both server-rendered and AJAX endpoints.
- Database (MySQL) is the primary persistent store for users, billings, payments, chat messages, and logs.
- Redis is used for sessions, caches, and Pub/Sub for real-time events (chat). SSE/WS Broker uses Pub/Sub to stream chat updates to Admins.
- Object Storage (S3) is recommended for user uploads and export storage.
- A background worker pool handles long-running jobs (exports, backfill, billing sync) via a queue (Redis or alternative).
- External services: Email provider (Brevo/Sendinblue), Payment provider (GCash or similar) are integrated; webhooks are handled by `WebhookController`.
- Observability: logs are centralized to a centralized logging system; metrics & monitoring are recommended

## Deployment & Scaling Notes
- Use Redis for session store and Pub/Sub to scale across multiple app servers.
- Use queue + worker processes for large export generation and background tasks.
- Add DB replicates for read-heavy workloads and backups for disaster recovery.
- Migrate static + uploads to object storage (S3) for scaling and CDN caching.

## Next Steps
- Render the Mermaid diagram in the repo (GitHub supports Mermaid blocks in Markdown) or convert to SVG for a direct preview.
- Optionally add a small readable SVG for README and documentation previews.
