# Implementation Notes

## Scope Alignment

The implementation follows `PROJECT.md` as the primary specification and uses the two project proposal documents as functional references. The first codebase includes:

- Public homepage, about, announcements, contact
- Student/staff self-registration
- Login/logout and password recovery placeholder
- Role-based dashboards for admin, staff, and student
- Admin user activation/deactivation/suspension/deletion
- Course management
- Timetable form and drag-ready timetable board
- Payment history and Remita reference generation structure
- Expense tracking with categories
- Report export screen prepared for DomPDF
- Dynamic document requirements
- Normalized MySQL schema with 40 tables

## Design System

The UI uses CSS variables for theme tokens and supports light/dark mode. The visual style avoids Bootstrap, shadows, gradients, and glassmorphism. Components use restrained borders, compact layouts, bold headings, and responsive grids.

## Database

Import order:

1. `database/schema.sql`
2. `database/seed.sql`

The schema includes foreign keys, indexes, normalized operational tables, logs, settings, document requirements, payments, expenses, timetable data, and report export support.

## Next Integration Steps

- Add DomPDF through Composer and implement PDF generation in `admin/reports.php`.
- Add live Remita credentials and implement verification webhooks using `payment_webhooks`.
- Expand upload handling to move validated files into `storage/uploads`.
- Add richer edit/delete flows for courses, timetable slots, expenses, and documents.
