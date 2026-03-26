# Tydy POS

Tydy POS is a modular Laravel point of sale system being built for shared hosting, local XAMPP installs, thermal receipts, multilingual support, and CodeCanyon distribution.

## Current state

Milestone 1 is in progress and includes:

- Laravel 11 foundation
- Session-based authentication
- Branch, settings, role, and permission schema
- Shared-hosting-safe `public/uploads` filesystem strategy
- Theme and branding configuration baseline
- English and Spanish locale foundation

## Local defaults

- App URL: `http://localhost/tydypos/public`
- Database: `tydypos`
- Default seeded admin: `admin@tydypos.test`
- Default seeded password: `password`

## Next milestones

- Product, inventory, sales, and checkout flows
- Receipt templates for 58mm, 80mm, and A4
- Settings UI, installer wizard, updater baseline, and hardening
