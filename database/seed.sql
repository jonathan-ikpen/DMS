USE department_management_system;

INSERT INTO departments (name, code, institution) VALUES
('Computer Science Department', 'CSC', 'Petroleum Training Institute');

INSERT INTO roles (name, slug) VALUES
('Administrator', 'admin'),
('Staff', 'staff'),
('Student', 'student');

INSERT INTO levels (name) VALUES ('ND1'), ('ND2'), ('HND1'), ('HND2');
INSERT INTO semesters (name, is_current) VALUES ('First', 1), ('Second', 0);
INSERT INTO academic_sessions (name, starts_on, ends_on, is_current) VALUES ('2025/2026', '2025-09-01', '2026-08-31', 1);

INSERT INTO users (role_id, name, email, password, status, email_verified_at) VALUES
((SELECT id FROM roles WHERE slug = 'admin'), 'Department Admin', 'admin@dms.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi', 'active', NOW()),
((SELECT id FROM roles WHERE slug = 'staff'), 'Ada Lecturer', 'staff@dms.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi', 'active', NOW()),
((SELECT id FROM roles WHERE slug = 'student'), 'Tega Student', 'student@dms.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi', 'active', NOW());

INSERT INTO staff (user_id, staff_no, designation, phone, office) VALUES
((SELECT id FROM users WHERE email = 'staff@dms.test'), 'CSC/STF/001', 'Lecturer I', '08030000001', 'Block A, Room 4');

INSERT INTO students (user_id, matric_no, level, phone, address, admission_year) VALUES
((SELECT id FROM users WHERE email = 'student@dms.test'), 'M.24/HND/SWD/11579', 'HND1', '08030000002', 'Effurun, Delta State', 2024);

INSERT INTO courses (code, title, credit_units, level, semester) VALUES
('COM 311', 'Database Management Systems', 3, 'HND1', 'First'),
('COM 312', 'Software Engineering', 3, 'HND1', 'First'),
('COM 313', 'Web Application Development', 3, 'HND1', 'First'),
('COM 221', 'Data Structures', 3, 'ND2', 'Second');

INSERT INTO venues (name, capacity) VALUES ('Lecture Hall A', 120), ('Computer Lab 1', 60);

INSERT INTO timetable (course_id, staff_id, day_of_week, start_time, end_time, venue, level, semester) VALUES
((SELECT id FROM courses WHERE code = 'COM 311'), (SELECT id FROM staff WHERE staff_no = 'CSC/STF/001'), 'Monday', '09:00', '11:00', 'Lecture Hall A', 'HND1', 'First'),
((SELECT id FROM courses WHERE code = 'COM 312'), (SELECT id FROM staff WHERE staff_no = 'CSC/STF/001'), 'Wednesday', '12:00', '14:00', 'Computer Lab 1', 'HND1', 'First');

INSERT INTO payment_items (name, amount) VALUES ('Departmental Due', 15000.00), ('Project Defense Fee', 25000.00);

INSERT INTO payments (user_id, payment_item_id, gateway, reference, amount, status, paid_at) VALUES
((SELECT id FROM users WHERE email = 'student@dms.test'), (SELECT id FROM payment_items WHERE name = 'Departmental Due'), 'Remita', 'RMT-SEED-0001', 15000.00, 'paid', NOW());

INSERT INTO expense_categories (name, is_system) VALUES
('Maintenance', 1), ('Academic Materials', 1), ('Logistics', 1), ('Custom', 0);

INSERT INTO expenses (category_id, title, amount, expense_date, status, created_by, approved_by) VALUES
((SELECT id FROM expense_categories WHERE name = 'Academic Materials'), 'Printing course allocation sheets', 8500.00, CURDATE(), 'approved', (SELECT id FROM users WHERE email = 'admin@dms.test'), (SELECT id FROM users WHERE email = 'admin@dms.test'));

INSERT INTO qualifications (staff_id, qualification, institution, year_awarded) VALUES
((SELECT id FROM staff WHERE staff_no = 'CSC/STF/001'), 'MSc Computer Science', 'University of Benin', 2021);

INSERT INTO document_requirements (audience, name, description) VALUES
('student', 'Passport Photograph', 'Recent passport photograph for student records.'),
('student', 'Admission Letter', 'Admission letter or departmental clearance evidence.'),
('staff', 'Curriculum Vitae', 'Updated academic and professional CV.'),
('staff', 'Highest Qualification', 'Certificate or statement of result.');

INSERT INTO announcements (title, body, status, published_at, created_by) VALUES
('Registration is open', 'Students and staff can now complete departmental registration through the DMS portal.', 'published', NOW(), (SELECT id FROM users WHERE email = 'admin@dms.test')),
('Timetable draft published', 'The first semester timetable is available for review.', 'published', NOW(), (SELECT id FROM users WHERE email = 'admin@dms.test'));

INSERT INTO settings (setting_key, setting_value, is_secret) VALUES
('department_name', 'Computer Science Department', 0),
('remita_merchant_id', '', 1),
('remita_api_key', '', 1),
('session_timeout_minutes', '30', 0);
