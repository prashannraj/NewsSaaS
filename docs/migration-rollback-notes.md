# Migration Rollback Notes

- Rollback has been verified on a disposable MySQL database (`news_saas_phase2_test`) using `php artisan migrate` followed by `php artisan migrate:reset`.
- SQLite rollback is limited by SQLite foreign-key/table rebuild behavior; production MySQL is the supported rollback target.
- Several migrations use `Schema::disableForeignKeyConstraints()` during `down()` to avoid ordering issues.
- Do not run `migrate:fresh` against production databases.
- Renamed tables (`organization_users` -> `organization_memberships`, `domains` -> `tenant_domains`, `invoices` -> `billing_invoices`) have updated rollback paths.
