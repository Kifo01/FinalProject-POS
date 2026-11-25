# TAMS Cafe & Co. POS

A standalone CodeIgniter 4 food and beverage point of sale application for IT0049. It preserves the professor's four-table schema and single-product sales workflow. Admins manage products, customers, and staff. Admins and cashiers record sales. Sales decrease stock atomically and appear in history.

## Requirements

- PHP 8.2 or newer with mysqli, fileinfo, and GD
- Composer
- MySQL 5.7+/MariaDB with InnoDB
- MAMP for the local URL below

## Local setup

1. Put this folder at `/Applications/MAMP/htdocs/FinalProject-POS`.
2. In MAMP, start Apache and MySQL. Use phpMyAdmin to import [docs/FinalProject-POS.sql](docs/FinalProject-POS.sql). It creates `finalproject_pos`, the four tables, two demo accounts, and sample products. Do **not** run migrations after importing the SQL into the same database.
3. Run `composer install`.
4. Copy `.env.example` to `.env`. Set the actual MAMP MySQL port, username, password, and base URL. The example uses Apache port 8888 and MySQL port 8889.
5. Open `http://localhost:8888/FinalProject-POS/public/`.

For a fresh database without the SQL import, create `finalproject_pos`, configure `.env`, then run `php spark migrate` and `php spark db:seed DemoSeeder`. Never run the demo seeder on a production database.

## Demo accounts

| Role | Username | Password |
| --- | --- | --- |
| Admin | `demo_admin` | `DemoAdmin!2026` |
| Cashier | `demo_cashier` | `DemoCashier!2026` |

These credentials are for local demonstration only. Replace them before hosting. The database stores password hashes, never plaintext passwords.

## Features and roles

Admins access the dashboard, product/customer/staff management, cashier terminal, and sales history. Cashiers access only the terminal. Product images and staff avatars are checked by actual MIME type, limited to 2 MB, resized into 800×600 WebP files, and stored under `public/uploads`. Product, customer, and staff records are archived or deactivated to preserve sales history.

The terminal records **one product per sale**. The server verifies product price, quantity, customer, and stock. An atomic conditional stock update and sale insert run inside one database transaction.

## Testing

Run `vendor/bin/phpunit --no-coverage`. The tests use an in-memory SQLite database and do not touch the local MySQL data. Manually check the layout at desktop and phone widths, image upload and replacement, and the complete login/sale/history flow in MAMP.

## Production deployment

Use a PHP host with a persistent MySQL/MariaDB database and durable upload storage. Point the web root to `public`, configure HTTPS and environment variables, set `CI_ENVIRONMENT=production`, and migrate the database. Set `POS_ADMIN_USERNAME`, `POS_ADMIN_NAME`, and `POS_ADMIN_PASSWORD` in the host environment, run `php spark pos:create-admin` once, then remove the password variable. Do not import the demo SQL. Mount durable storage at `public/uploads` before accepting uploads. Hosting architecture and account selection remain a project decision.

The hosted link and GitHub repository URL are pending deployment and remote repository creation.
