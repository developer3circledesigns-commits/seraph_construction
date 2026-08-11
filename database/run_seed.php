<?php
/**
 * SERAPH RUN — CLI database seeder (optional demo data).
 * Usage: php database/run_seed.php
 * Password for all seeded users: Seraph@123
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit("This script must be run from the command line.\n");
}

require dirname(__DIR__) . '/api/config/database.php';

// Never seed demo/login users into a production database.
if (is_production()) {
    fwrite(STDERR, "Refusing to seed demo data. This script is dev/demo only and must not run with APP_ENV=production.\n");
    fwrite(STDERR, "Set APP_ENV=local in .env to run the seeder, or import demo_data.sql on a scratch database.\n");
    exit(1);
}

// Re-hash real Argon2id hashes at runtime so the SQL seed becomes accurate.
$c = db_config();
$pdo = new PDO(
    sprintf('mysql:host=%s;port=%s;charset=utf8mb4', $c['host'], $c['port']),
    $c['username'],
    $c['password'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);
$pdo->exec('USE `' . $c['database'] . '`');

$hash = password_hash('Seraph@123', PASSWORD_ARGON2ID);

// Clear existing to make seeding idempotent
foreach (['update_images', 'daily_updates', 'notifications', 'login_attempts', 'admin_projects', 'user_sessions', 'audit_log', 'sse_events', 'projects', 'clients', 'admins'] as $t) {
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0; TRUNCATE TABLE `$t`; SET FOREIGN_KEY_CHECKS=1;");
}

$pdo->exec("INSERT INTO admins (email, password_hash, full_name, phone, role) VALUES
('suresh@seraphbuild.com', " . $pdo->quote($hash) . ", 'Sureshkumar M', '+91 90925 57722', 'super_admin'),
('admin@seraphbuild.com', " . $pdo->quote($hash) . ", 'Admin User', '+91 90000 00001', 'admin')");

$pdo->exec("INSERT INTO clients (email, password_hash, company_name, contact_person, phone, address) VALUES
('client1@example.com', " . $pdo->quote($hash) . ", 'Azure Enterprises', 'Rajesh Kumar', '+91 90000 00002', '12, Anna Nagar, Chennai'),
('client2@example.com', " . $pdo->quote($hash) . ", 'Camel Living', 'Priya Sharma', '+91 90000 00003', '45, T. Nagar, Chennai')");

$pdo->exec("INSERT INTO projects (client_id, name, category, description, location, plot_size, built_up_area, floors, bedrooms, bathrooms, style, start_date, estimated_end_date, status, progress_percentage, budget) VALUES
(1, 'Villa Azure', 'Villa', 'Luxury villa with basement, 4 bedrooms and a garden.', 'Chennai', '60x80', '4200 sqft', 3, 4, 5, 'Contemporary Luxury', '2026-01-10', '2026-12-20', 'in_progress', 80, 8500000.00),
(1, 'Office Skyline', 'Office', 'Commercial office fit-out, 3 floors.', 'OMR, Chennai', '100x120', '15000 sqft', 3, 0, 8, 'Corporate Modern', '2026-03-01', '2026-09-15', 'in_progress', 40, 4200000.00),
(2, 'Penthouse Camel', 'Apartment', 'Premium penthouse interior + modular kitchen.', 'Egmore, Chennai', NULL, '3200 sqft', 2, 3, 4, 'Warm Contemporary', '2026-02-01', '2026-10-30', 'in_progress', 60, 5600000.00)");

$pdo->exec("INSERT INTO admin_projects (admin_id, project_id) VALUES
(1,1),(1,2),(2,1),(2,3)");

// Recent updates for project 1
for ($i = 4; $i >= 1; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $pct = 80 - ($i * 3);
    $pdo->exec("INSERT INTO daily_updates (project_id, admin_id, update_date, status, progress_percentage, title, description, is_milestone) VALUES
    (1,1," . $pdo->quote($d) . ",'in_progress'," . $pct . "," . $pdo->quote('Foundation and structural work day ' . $i) . "," . $pdo->quote('Steel reinforcement and shuttering progressed on schedule.') . "," . ($i === 1 ? 1 : 0) . ")");
}

echo "Seeded demo data. All seeded logins use password: Seraph@123\n";
echo "  Admin:  suresh@seraphbuild.com  /  Seraph@123\n";
echo "  Admin:  admin@seraphbuild.com   /  Seraph@123\n";
echo "  Client: client1@example.com     /  Seraph@123\n";
echo "  Client: client2@example.com     /  Seraph@123\n";