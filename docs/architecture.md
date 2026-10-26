# Architecture

## Overview
Multi-tenant News Portal SaaS on Laravel 13. Tenancy is resolved by host (subdomain or verified custom domain) via ResolveTenant middleware. TenantContext singleton stores active organization. Tenant-scoped models use HasTenant global scope and auto-assign organization_id.

## Layers
- HTTP: Controllers, Form Requests, Policies, Middleware
- Domain: Models with tenant scopes
- Services: OrganizationService, BillingService, TenantResolver
- Jobs/Events/Listeners/Notifications for async and side effects

## Tenancy
- Central domains configured via TENANCY_CENTRAL_DOMAINS.
- Subdomain tenants: {slug}.{central}.
- Custom domains: Domain records with verified_at.

## Billing
- Plans, subscriptions, invoices, polymorphic payments.
- Entitlements derived from plan features.
