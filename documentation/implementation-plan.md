# Implementation Plan (short)

Date: 2026-03-03

Goal: Implement the migration/decommissioning tasks from `TODO.md` and `Mikrotik_Radius_architecture.md`.

Completed so far:
- Scanned repository and identified RADIUS-related services, commands, and models.
- Removed test suites from `tests/` to match requested decommissioning.
- Added initial migrations for `nas`, `radcheck`, `radreply`, `radacct`, `pppoe_profiles`, `ipv4_pools`.
- Documented progress in `documentation/migration-progress.md`.

Next steps (execution order):
1. Consolidate migrations: ensure `radius` DB connection is present in `config/database.php` and migrations are targeted appropriately.
2. Create or update controllers and services required in `TODO.md` (priority):
   - `RouterConfigurationController` (exists) — validate and extend with CoA/Disconnect endpoints.
   - `RadreplyController` (exists) — add bulk import/export and backup hooks.
   - `RouterManagementService` / `RouterMigrationService` (exists) — add robust error handling and logging.
   - `BillingService` (exists) — add support for daily/monthly cycles and invoice generation jobs.
3. Implement jobs and console commands to migrate PPP secrets and run verification (`MigratePppSecrets`, `RadiusInstall`, `pull:radaccts`).
4. Update routes (web/api) to include new endpoints for router configuration and RADIUS management.
5. Add views / Blade templates for admin panels where necessary under `resources/views`.
6. Add policies and middleware to protect router and billing endpoints.
7. Update `README.md` and `DOCUMENTATION_INDEX.md` once validation is complete.

Notes:
- I will proceed to implement the above steps incrementally and will update `documentation/migration-progress.md` with each completed action.
