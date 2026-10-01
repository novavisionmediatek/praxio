# Praxia website

Marketing website and admin panel for Praxia (asset-backed operating-lease financing), built with plain PHP 8 and MySQL. No framework and no build step.

## Features
- Public site: Home, Product, About, Case studies (one page per case study), FAQ, Careers (with CV upload), Contact.
- Enquiry popup, quick-quote, proposal and contact forms, all saved to MySQL with CSRF, honeypot and rate limiting.
- Admin panel at `/admin/`:
  - Inbox for enquiries and job applications (search, status, CSV export, CV download).
  - Edit every page's text and images, the logo, top bar, menu, footer and popup.
  - Manage case studies (rich-text description, gallery, quote), FAQs, team, testimonials, investors, partner logos, job openings and more.
  - Media library with uploads and a picker.

## Requirements
PHP 8.1+ (with `pdo_mysql`, `mbstring`, `fileinfo`, `dom`) and MySQL 5.7+ / MariaDB 10.3+. XAMPP works out of the box.

## Install (XAMPP)
1. Copy this folder to `htdocs/praxia` and start Apache and MySQL.
2. Copy `config/config.sample.php` to `config/config.php` and adjust the database login if needed.
3. Open `http://localhost/praxia/setup.php`, choose an admin username and password, and click **Install**. This creates the database, tables and starting content, then locks itself.
4. Site: `http://localhost/praxia/`. Admin: `http://localhost/praxia/admin/`.

CLI alternative: `php setup.php <admin-username> <admin-password>`.

## Updating an existing install
Copy the new files over, log in to the admin panel, then open `/migrate.php` (or run `php migrate.php`). It adds new tables/columns and never overwrites your content.

## Folders
| Path | Purpose |
|---|---|
| `*.php` (root) | Public pages |
| `admin/` | Admin panel (`admin/vendor/tinymce` is the bundled rich-text editor, GPL) |
| `api/submit.php` | Endpoint for all public forms |
| `includes/` | Bootstrap, helpers, shared header/footer, content defaults |
| `database/` | `schema.sql`, `seed.php` (starting content), installer library |
| `css/`, `js/` | Front-end assets |
| `media/` | Uploaded images (not tracked by git) |
| `uploads/` | Uploaded CVs, blocked from direct web access (not tracked by git) |

## Security notes
- Keep `config/config.php` out of git (already ignored) and set `debug` to `false` in production.
- The host must honour `.htaccess` (Apache `AllowOverride All`) so `config/`, `includes/`, `database/` and `uploads/` stay private.
- Change the admin password after install (Admin → Settings).
