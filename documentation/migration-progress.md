## Migration & Decommissioning Progress

- Date: 2026-03-03
- Actions performed:
  - Created and started tracking a TODO plan for the migration.
  - Scanned the repository for RADIUS references, migrations, tests, and related commands.
  - Removed the `tests/` files (per request).

- Quick findings from repo scan:
  - `phpunit.xml` exists and pointed at `tests/Unit` and `tests/Feature` (tests removed).
  - Many services and commands reference FreeRADIUS-related models and tables: `RadCheck`, `RadReply`, `RadAcct`, `nas`.
  - Console commands exist for RADIUS installation/migration: `RadiusInstall`, `MigratePppSecrets`.
  - `app/Services/RadiusService.php`, `RadiusSyncService.php`, `MikrotikService.php` implement core logic referenced in `Mikrotik_Radius_architecture.md`.

- README status:
  - `README.md` contains claims about active development and installation scripts that may be outdated. Treat README content as unreliable until validated against current code and migration status.

- Next recommended steps (will proceed as part of TODO):
  1. Review and consolidate database migrations for both app and radius DBs (confirm `database/migrations` and any radius migration scripts).
  2. Extract exact migration requirements from `Mikrotik_Radius_architecture.md` and `TODO.md` and implement missing migrations (`nas`, `rad*` tables, `pppoe_profiles`, `ipv4_pools`).
  3. Implement or update controllers/services listed in `TODO.md` (`RouterConfigurationController`, `RadreplyController`, `BillingService`, `RouterManagementService`).
  4. Prepare documentation updates for `README.md` and `DOCUMENTATION_INDEX.md` to reflect current status and next actions.

Implementations completed in this session:

- Added `RadiusService::disconnectUser()` to mark active sessions stopped in `radacct`.
- Added `disconnectUser` endpoint under `routers/configuration` at route name `routers.configuration.disconnect-user` handled by `RouterConfigurationController::disconnectUser()`.
- Cleaned up duplicate migrations and consolidated to existing radius migrations under `database/migrations/radius`.
 - Added job scaffolds for billing and radius processing: `GenerateMonthlyInvoices`, `PullRadacct`, `DeleteStaleRadSessions`.
 - Added event `ImportPppCustomersRequested` and listener `ImportPppCustomersListener` to trigger PPP import flows.
 - Added admin Blade views for Radreply import/export and onboarding pages.

Database reset performed on 2026-03-03:

- Previous `database/` contents: 229 files and 3 subdirectories (seeders, factories, migrations). A partial listing of files was captured before deletion and archived by the operator.
- Action taken: Removed all files under `database/` and created a fresh migrations layout at `database/migrations` and `database/migrations/radius`.

New migrations created (baseline):
- `2026_03_03_000001_create_users_table.php`
- `2026_03_03_000002_create_roles_table.php`
- `2026_03_03_000003_create_nas_table.php` (routers represented as `nas`)
- `2026_03_03_000004_create_packages_table.php`
- `2026_03_03_000005_create_billing_profiles_table.php`
- `2026_03_03_000006_create_invoices_table.php`
- `2026_03_03_000007_create_customer_payments_table.php`
- `2026_03_03_000008_create_customer_bills_table.php`
- `2026_03_03_000009_create_ipv4_pools_table.php`
- `2026_03_03_000010_create_pppoe_profiles_table.php`
- `radius/2026_03_03_000011_create_radcheck_table.php`
- `radius/2026_03_03_000012_create_radreply_table.php`
- `radius/2026_03_03_000013_create_radacct_table.php`

Notes:
- All new migrations include `tenant_id`, `admin_id`, and `operator_id` columns as requested.
- `nas` is used as the single source for routers (per architecture document) — no separate `routers` table was created.
- I intentionally created a minimal baseline set; further domain-specific fields and indexes should be added after reviewing business rules in `Mikrotik_Radius_architecture.md`.

If you'd like, I can now start creating the migration files and scaffolding the controllers listed in `TODO.md` (I'll proceed step-by-step and document each change).
