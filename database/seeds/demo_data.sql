-- =====================================================================
-- SERAPH BUILD CONSTRUCTION — Demo seed data
-- Insert AFTER running 001_initial_schema.sql
-- Password for all seeded users is: Seraph@123
-- =====================================================================

USE seraph_construction;

-- Admins
INSERT INTO admins (email, password_hash, full_name, phone, role) VALUES
('suresh@seraphbuild.com', '$argon2id$v=19$m=65536,t=4,p=1$R1hIR1dIQ0cuSzU0L3hmdw$DAzg92G5Dh58tZ81JPC/eCb8zbDzAjyFW8u2WhhJKY8', 'Sureshkumar M', '+91 90925 57722', 'super_admin'),
('admin@seraphbuild.com',  '$argon2id$v=19$m=65536,t=4,p=1$R1hIR1dIQ0cuSzU0L3hmdw$DAzg92G5Dh58tZ81JPC/eCb8zbDzAjyFW8u2WhhJKY8', 'Admin User',      '+91 90000 00001', 'admin');

-- Clients
INSERT INTO clients (email, password_hash, company_name, contact_person, phone, address) VALUES
('client1@example.com', '$argon2id$v=19$m=65536,t=4,p=1$R1hIR1dIQ0cuSzU0L3hmdw$DAzg92G5Dh58tZ81JPC/eCb8zbDzAjyFW8u2WhhJKY8', 'Azure Enterprises', 'Rajesh Kumar', '+91 90000 00002', '12, Anna Nagar, Chennai'),
('client2@example.com', '$argon2id$v=19$m=65536,t=4,p=1$R1hIR1dIQ0cuSzU0L3hmdw$DAzg92G5Dh58tZ81JPC/eCb8zbDzAjyFW8u2WhhJKY8', 'Camel Living',       'Priya Sharma',  '+91 90000 00003', '45, T. Nagar, Chennai');

-- Projects
INSERT INTO projects (client_id, name, category, description, location, plot_size, built_up_area, floors, bedrooms, bathrooms, style, start_date, estimated_end_date, status, progress_percentage, budget) VALUES
(1, 'Villa Azure',      'Villa', 'Luxury villa with basement, 4 bedrooms and a garden.', 'Chennai', '60x80', '4200 sqft', 3, 4, 5, 'Contemporary Luxury', '2026-01-10', '2026-12-20', 'in_progress', 80, 8500000.00),
(1, 'Office Skyline',   'Office', 'Commercial office fit-out, 3 floors.', 'OMR, Chennai', '100x120', '15000 sqft', 3, 0, 8, 'Corporate Modern', '2026-03-01', '2026-09-15', 'in_progress', 40, 4200000.00),
(2, 'Penthouse Camel',  'Apartment', 'Premium penthouse interior + modular kitchen.', 'Egmore, Chennai', '—', '3200 sqft', 2, 3, 4, 'Warm Contemporary', '2026-02-01', '2026-10-30', 'in_progress', 60, 5600000.00);

-- Admin project assignments
INSERT INTO admin_projects (admin_id, project_id) VALUES
(1, 1), (1, 2), (2, 1), (2, 3);

-- Daily updates (project 1, last several days)
INSERT INTO daily_updates (project_id, admin_id, update_date, status, progress_percentage, title, description, materials_used, labor_count, weather_condition, next_day_plan, is_milestone) VALUES
(1, 1, '2026-08-01', 'in_progress', 70, 'Foundation and plinth beam completed', 'Plinth beam shuttering and RCC concreting finished on schedule. Curing in progress.', 'Cement, Steel, M20 Concrete', 24, 'Sunny', 'Start ground floor column reinforcement.', 1),
(1, 1, '2026-08-02', 'in_progress', 72, 'Ground floor columns reinforcement', 'Column cage fabrication complete for grid A-G. Shuttering progressing.', 'TMT bars 12mm/16mm, Binding wire', 26, 'Partly cloudy', 'Dewatering pump on site; column shuttering.', 0),
(1, 1, '2026-08-03', 'in_progress', 75, 'Ground floor slab shuttering', 'Slab shuttering and steel placement in progress. Electrical conduits being laid.', 'Ply sheets, MS pipes, Steel mesh', 30, 'Rainy', 'Slab concrete pour scheduled tomorrow morning.', 0),
(1, 1, '2026-08-04', 'in_progress', 80, 'Ground floor slab concrete pour', 'Slab concreting completed using transit mixer. Cube samples sent for testing.', 'M25 Ready-mix concrete, Curing compound', 32, 'Sunny', 'Curing and slab finishing; start first floor masonry.', 1);

INSERT INTO daily_updates (project_id, admin_id, update_date, status, progress_percentage, title, description, materials_used, labor_count, weather_condition, next_day_plan, is_milestone) VALUES
(2, 1, '2026-08-03', 'in_progress', 38, 'False ceiling framework', 'Ground floor false ceiling metal framework installed for lobbies.', 'G.I. channels, Gypsum board', 12, 'Sunny', 'Gypsum board cladding.', 0),
(2, 1, '2026-08-04', 'in_progress', 40, 'Gypsum board fixing', 'Lobby ceiling boarded and skim coat applied. Painting prep next.', 'Gypsum board, Joint compound', 12, 'Sunny', 'Painting works start.', 0);

INSERT INTO daily_updates (project_id, admin_id, update_date, status, progress_percentage, title, description, materials_used, labor_count, weather_condition, next_day_plan, is_milestone) VALUES
(3, 2, '2026-08-02', 'in_progress', 58, 'Modular kitchen island installation', 'Kitchen island carcass installed, quartz countertop templating done.', 'Plywood, Laminate, Quartz', 8, 'Sunny', 'Countertop fabrication and fit.', 0),
(3, 2, '2026-08-04', 'in_progress', 60, 'Quartz countertop fit', 'Island quartz countertop fitted and sealed. Sink plumbing connected.', 'Quartz slab, Silicone, Chrome fittings', 8, 'Sunny', 'Cabinet hardware and lighting.', 0);

-- Notifications
INSERT INTO notifications (recipient_type, recipient_id, type, title, message, reference_id) VALUES
('client', 1, 'milestone', 'Villa Azure milestone reached', 'Ground floor slab concrete pour completed. Progress now at 80%.', 1),
('admin', 1, 'status_update', 'Daily update posted', 'Villa Azure daily update for 2026-08-04 added.', 1);
