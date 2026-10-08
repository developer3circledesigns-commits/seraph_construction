-- 006: Calculator estimates attached to contact inquiries.
ALTER TABLE contact_inquiries
    ADD COLUMN calc_package VARCHAR(20) DEFAULT NULL,
    ADD COLUMN calc_total   INT UNSIGNED DEFAULT NULL,
    ADD COLUMN calc_json    JSON DEFAULT NULL;
