Flowcharts for Capstone (Mermaid)

Files:
- `overview.mmd` — High-level architecture and request flow.
- `auth_users.mmd` — User add/edit/view flows and validations.
- `payments.mmd` — Gateway creation, supersede/reject logic, manual confirmation, failed transactions.
- `admin_ui.mmd` — Admin dashboard polling and failed-transactions filtering rules.
- `cli_remediation.mmd` — Interactive CLI remediation script flow.

How to render

- In VS Code: Install "Markdown Preview Mermaid Support" or "Mermaid Markdown Preview" and open the `.mmd` file.
- Using mermaid-cli (npm):

```powershell
npm install -g @mermaid-js/mermaid-cli
mmdc -i overview.mmd -o overview.svg
```

- Online: paste the Mermaid source into https://mermaid.live/ to preview and export PNG/SVG.

Notes
- The diagrams are high-level and reflect the major flows in the repository attachments you provided.
- If you'd like PNG/SVG exports committed here, I can generate them (requires installing mermaid-cli locally or using an online renderer).