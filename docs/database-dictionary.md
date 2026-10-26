# Database Dictionary

## Money convention
All monetary values are stored as integer minor units (cents) in `*_cents` / `*_amount` / `unit_amount` / `base_amount` columns. Currency is stored as ISO 4217 code in `currency` / `*_currency` columns. No floating-point money columns are used.

## Timestamps
All timestamp columns are stored in UTC. Tenant display conversion uses `organizations.timezone` or tenant settings.

## Tenant isolation
Tenant-scoped tables contain `tenant_id` (FK to `tenants.id`). The `HasTenant` global scope filters by the resolved tenant context.

## Core platform tables
- users: accounts, authentication, profile, platform admin flag, current organization.
- organizations: tenant-owning business accounts.
- tenants: provisioned portal instances for an organization.
- organization_memberships: user-to-organization membership and status.
- roles, permissions, role_permissions, organization_membership_roles: normalized RBAC.
- invitations: pending organization invitations.
- audit_logs: immutable audit events.
- notifications, sessions, cache, jobs, failed_jobs, password_reset_tokens: framework tables.
- social_auth_accounts: OAuth identities.

## News CMS tables
- categories, tags, articles, article_tag, article_revisions, article_media, media_assets, menus, menu_items, pages, advertisements, article_views, editorial_workflows, comments.

## Billing tables
- products, product_prices, plans, features, plan_features, subscriptions, subscription_items, subscription_usage, billing_customers, billing_invoices, billing_invoice_items, payments, payment_refunds, webhook_events, entitlements, entitlement_overrides, organization_seats.

## Domain and tenant settings
- tenant_domains, domain_verification_tokens, tenant_settings, tenant_themes, tenant_navigation_settings, tenant_social_links, tenant_email_settings.

## SaaS platform tables
- platform_settings, support_tickets, support_ticket_messages, changelog_entries, release_versions, blog_categories, blog_posts, blog_tags, blog_post_tag, email_templates, api_tokens.
