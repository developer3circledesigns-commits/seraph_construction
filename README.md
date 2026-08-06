# Seraph Build Construction

Full-stack construction project management platform:
- Public marketing site (`index.php` + `partials/`)
- Admin panel (`/admin`) — manage projects, clients, daily updates
- Client portal (`/client`) — clients track their projects live (SSE)

## Deploy to Hostinger shared hosting

### 1. Upload
Upload the **contents** of this repo into your `public_html/` folder
(File Manager → Upload, or git). Result — these must all live directly in public_html:

```
public_html/
├── index.php
├── .htaccess
├── admin/  client/  api/  config/  database/
├── css/  js/  images/  uploads/  partials/
└── robots.txt  llms.txt
```

### 2. Create the database
In Hostinger hPanel → Databases → MySQL:
1. Create a database (e.g. `u123456789_seraph`) and note its user/password/host.
2. Run the schema + seed scripts (via **phpMyAdmin** → Import, or the CLI runner):
   - Import `database/migrations/001_initial_schema.sql`
   - Import `database/migrations/002_sse_events.sql`
   - Import `database/migrations/003_update_images.sql`
   - Import `database/seeds/demo_data.sql` (optional — seed demo data)

### 3. Set DB credentials
Create a `.env` file **inside `public_html/`** (or one level above it) with your
Hostinger database credentials. A ready-to-fill template (your live values, kept
out of the repo) is shipped as `.env.production` (gitignored — never committed).
Copy it to `.env` on the server and adjust `DB_HOST` if your plan uses a
non-default host:

```
APP_ENV=production
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=<your_db_name>
DB_USERNAME=<your_db_user>
DB_PASSWORD=<your_db_password>
```

> Security:
> - `.env` is gitignored and blocked from HTTP by `.htaccess` (`<FilesMatch>` → 403).
> - `.env.production` is also gitignored — it is a local-only template, so your live
>   credentials never reach the repo. Do not paste real secrets into README.md or
>   `.env.example` (both are git-tracked).
> - Best practice: place `.env` one level **above** `public_html/` (the account root)
>   — the app checks `<repo>/.env` and `<repo>/../.env` — so credentials can never be
>   served over HTTP.
> - With `APP_ENV=production` the app **fails closed**: missing DB credentials cause a
>   clear error instead of falling back to local defaults. Never edit
>   `api/config/database.php` to hardcode passwords.
> - **Never import `demo_data.sql` on the live database.** It contains demo accounts
>   whose password (`Seraph@123`) is public in the repo. Use it on a scratch database
>   only, or create real accounts via the admin panel instead.

> Alternative (only if you can't use `.env`): the `SetEnv` block at the top of
> `.htaccess` can be uncommented instead — it works on Apache/LiteSpeed but keeps
> secrets inside the served folder.

### 4. Set permissions
- Ensure `uploads/` is writable by PHP (Hostinger usually 755; use 775 if uploads fail).
- `uploads/` ships with its own `.htaccess` that blocks PHP execution — keep it.

### 5. Go live
- Open `https://yourdomain/` — you should see the marketing site.
- `https://yourdomain/admin/login` — admin panel.
- `https://yourdomain/client/login` — client portal.
- `.php` extensions are hidden by `.htaccess` rewrite rules; legacy `*.php` URLs
  are 301-redirected to the clean URLs automatically.

### 6. Test the DB connection (optional)
Upload `test-db-connection.php` to `public_html/`, temporarily remove its
`<FilesMatch>` block in `.htaccess` (search "test-db-connection"), open it in the
browser, then **restore `.htaccess` and delete the file**. It reads your `.env`
and reports the DB version + tables. It is blocked by default so it can never
leak if you forget to delete it.

## What the .htaccess protects
- `api/`, `config/`, `database/`, `docker/` → HTTP 403 (still run server-side via PHP `require`).
- `.env`, `*.sql`, `Dockerfile`, `docker-compose.yml`, `nginx.conf`, `start-dev-server.cmd`, log/backup files → 403.
- PHP execution inside `uploads/` → blocked (uploads store images only).

## Local development (XAMPP)
```powershell
.\start-dev-server.cmd        # serves http://seraph.dev:8080 via Apache
```
Credentials come from `.env` (in the repo root, gitignored) or real environment
variables. In `APP_ENV=local` (the shipped `.env`) missing DB_* vars fall back to
XAMPP defaults: host `127.0.0.1`, db `seraph_construction`, user `seraph`,
password `seraph_password`. In production they never fall back.

## Docker (optional, not needed for Hostinger)
`docker-compose.yml` + `Dockerfile` + `docker/nginx.conf` are included for an
alternative containerized deployment; ignore them on shared hosting.