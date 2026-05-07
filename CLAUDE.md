# iAudit - Project Context

## Overview
iAudit is a Laravel-based audit management system with a mobile app frontend. The mobile app consumes GET APIs to render dynamic screens, with initial data seeded via Excel imports (provided by client). Audit submissions are received via `/api/answers` and stored in the database.

## Tech Stack
- **Backend**: Laravel (PHP)
- **Frontend**: Mobile app (consumes APIs) + Admin panel (Blade views)
- **Database**: MySQL (tables: answers_iaudit, questions_iaudit, ships, users, etc.)
- **PDF Generation**: Barryvdh\DomPDF, Spatie\Browsershot
- **Authentication**: Sanctum (mobile API tokens)

## Key Models
- `AnswerIaudit`: Stores individual audit answers (table: `answers_iaudit`)
  - Fields: user_id, ship_id, question_id, answer, note, files (JSON), audit_id (FK to audits table)
- `QuestionIaudit`: Audit questions (table: `questions_iaudit`)
- `Ship`: Ship details (table: `ships`)
- `User`: Users (table: `users`)
- `Audit`: Groups answers into a single audit (table: `audits`) - *pending creation*
  - Fields: id, user_id, ship_id, reference_number, status, score, created_at

## Key API Endpoints
- `POST /api/login`: User login (returns token)
- `GET /api/companies`: Get companies → fleets → ships hierarchy
- `GET /api/questions`: Get all audit questions (grouped by department/heading/subheading/category)
- `POST /api/answers`: Submit audit answer (protected by Sanctum)
- `GET /api/trap-locations`: Get CRT trap locations
- `GET /api/efk-locations`: Get EFK locations

## Admin Routes (Web)
- `GET /audit-report`: Plain HTML audit report (static mockup)
- `GET /audit-report/pdf`: Download PDF report (static mockup)
- `GET /audit-pdf-report`: PDF report view (static mockup)
- `GET /audit-report/preview`: Preview PDF in browser

## Controllers
- `AuditReportController`: Handles audit report views (plain, PDF, download)
- `ApiController`: Handles mobile API endpoints

## Key Views
- `resources/views/audit-report.blade.php`: Vessel Sanitation Inspection Report (static, needs dynamization)
- `resources/views/audit-pdf-report.blade.php`: IPM Audit Report (static, needs dynamization)

## Current Tasks (In Progress)
1. Create audit listing page with search, filters, pagination
2. Add actions menu (3 dots) per row: Plain Report, PDF Report, Download Report
3. Make plain report dynamic (fetch data from DB, group by audit_id)
4. Make PDF report dynamic (generate from Blade, clean printable layout)
5. Implement PDF download with filename `audit-report-{id}.pdf`
6. Ensure eager loading to avoid N+1 issues
7. Maintain UI consistency with existing admin panel

## DB Relationships
- User has many AnswerIaudit
- Ship has many AnswerIaudit
- QuestionIaudit has many AnswerIaudit
- Audit has many AnswerIaudit (one-to-many)
- AnswerIaudit belongs to Audit, User, Ship, QuestionIaudit

## Notes
- All audit answers are submitted per-question via `/api/answers`
- A single audit (for a ship) consists of multiple answers (one per question)
- Need to group answers by audit_id to form a complete audit
- Initial data (questions, ships, etc.) seeded via Excel imports
- PDF reports use base64-encoded images from `public/assets/` directory
