-- Hospital Management System Database Schema
-- Created: 2025-11-01

CREATE DATABASE IF NOT EXISTS hospital_management;
USE hospital_management;

-- Users table (main authentication table)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('patient', 'doctor', 'admin') NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Patients table
CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE NOT NULL,
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other'),
    blood_group VARCHAR(10),
    medical_history TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Doctors table
CREATE TABLE doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    license_number VARCHAR(50) UNIQUE NOT NULL,
    qualification VARCHAR(255),
    experience_years INT DEFAULT 0,
    salary DECIMAL(10,2) DEFAULT 0.00,
    join_date DATE NOT NULL,
    availability_status ENUM('available', 'unavailable', 'on_leave') DEFAULT 'available',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Appointments table
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    symptoms TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Medicines table
CREATE TABLE medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT DEFAULT 0,
    manufacturer VARCHAR(100),
    expiry_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Prescriptions table
CREATE TABLE prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    medicine_id INT NOT NULL,
    dosage VARCHAR(100) NOT NULL,
    frequency VARCHAR(100),
    duration VARCHAR(100),
    instructions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE CASCADE
);

-- Bills table
CREATE TABLE bills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT,
    appointment_id INT,
    amount DECIMAL(10,2) NOT NULL,
    description TEXT,
    status ENUM('unpaid', 'paid', 'partially_paid') DEFAULT 'unpaid',
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    due_date DATE,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE SET NULL,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL
);

-- Payments table
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bill_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash', 'card', 'online', 'insurance') NOT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'completed',
    transaction_id VARCHAR(100),
    notes TEXT,
    FOREIGN KEY (bill_id) REFERENCES bills(id) ON DELETE CASCADE
);

-- Salaries table
CREATE TABLE salaries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_month VARCHAR(20) NOT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'paid') DEFAULT 'pending',
    notes TEXT,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Insert default admin user (password: admin123)
INSERT INTO users (name, email, password, role, phone, address) VALUES
('Admin User', 'admin@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '1234567890', 'Hospital Main Office');

-- Insert sample doctors (password: doctor123)
INSERT INTO users (name, email, password, role, phone, address) VALUES
('Dr. John Smith', 'john.smith@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', '1234567891', '123 Medical Lane'),
('Dr. Sarah Johnson', 'sarah.johnson@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', '1234567892', '456 Health Street'),
('Dr. Michael Brown', 'michael.brown@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', '1234567893', '789 Care Avenue'),
('Dr. Emily Davis', 'emily.davis@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', '1234567894', '321 Wellness Road');

INSERT INTO doctors (user_id, specialization, license_number, qualification, experience_years, salary, join_date) VALUES
(2, 'Cardiology', 'LIC001', 'MD, Cardiology', 10, 75000.00, '2020-01-15'),
(3, 'Pediatrics', 'LIC002', 'MD, Pediatrics', 8, 65000.00, '2021-03-20'),
(4, 'Orthopedics', 'LIC003', 'MD, Orthopedics', 12, 80000.00, '2019-06-10'),
(5, 'Dermatology', 'LIC004', 'MD, Dermatology', 6, 60000.00, '2022-02-05');

-- Insert sample patients (password: patient123)
INSERT INTO users (name, email, password, role, phone, address) VALUES
('Alice Williams', 'alice@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient', '2234567890', '111 Patient Street'),
('Bob Miller', 'bob@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient', '2234567891', '222 Patient Avenue');

INSERT INTO patients (user_id, date_of_birth, gender, blood_group, medical_history) VALUES
(6, '1990-05-15', 'female', 'A+', 'No major medical history'),
(7, '1985-08-20', 'male', 'O+', 'Diabetic');

-- Insert sample medicines
INSERT INTO medicines (name, description, price, stock_quantity, manufacturer, expiry_date) VALUES
('Paracetamol 500mg', 'Pain reliever and fever reducer', 5.00, 500, 'PharmaCorp', '2026-12-31'),
('Amoxicillin 250mg', 'Antibiotic for bacterial infections', 12.00, 300, 'MediLife', '2026-06-30'),
('Ibuprofen 400mg', 'Anti-inflammatory pain reliever', 8.00, 400, 'HealthPlus', '2026-09-30'),
('Cetirizine 10mg', 'Antihistamine for allergies', 6.00, 350, 'AllergyFree', '2026-11-30'),
('Omeprazole 20mg', 'Proton pump inhibitor for acid reflux', 15.00, 250, 'GastroMed', '2026-08-31'),
('Metformin 500mg', 'Diabetes medication', 10.00, 200, 'DiabCare', '2026-10-31'),
('Aspirin 75mg', 'Blood thinner', 4.00, 600, 'CardioHealth', '2027-01-31'),
('Vitamin D3 1000IU', 'Vitamin supplement', 7.00, 450, 'VitaBoost', '2027-03-31'),
('Azithromycin 500mg', 'Broad-spectrum antibiotic', 18.00, 280, 'AntiBioTech', '2026-07-15'),
('Losartan 50mg', 'Blood pressure medication', 13.00, 320, 'CardioMed', '2026-09-20'),
('Atorvastatin 20mg', 'Cholesterol lowering medication', 16.00, 275, 'HeartCare', '2026-11-10'),
('Salbutamol Inhaler', 'Bronchodilator for asthma', 22.00, 150, 'RespiHealth', '2026-05-30'),
('Ciprofloxacin 500mg', 'Antibiotic for infections', 14.00, 220, 'InfectCure', '2026-08-25'),
('Diclofenac 75mg', 'Pain and inflammation relief', 9.00, 380, 'PainRelief', '2026-12-15'),
('Ranitidine 150mg', 'Heartburn and ulcer treatment', 11.00, 290, 'GastroPlus', '2026-06-20'),
('Montelukast 10mg', 'Asthma and allergy medication', 20.00, 240, 'AllergyMed', '2026-10-05'),
('Prednisone 5mg', 'Corticosteroid anti-inflammatory', 17.00, 195, 'SteroPharma', '2026-07-30'),
('Clopidogrel 75mg', 'Blood clot prevention', 19.00, 265, 'ClotCare', '2026-09-12');

-- Insert sample appointments
INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, status, symptoms) VALUES
(1, 1, '2025-11-05', '10:00:00', 'confirmed', 'Chest pain and shortness of breath'),
(2, 2, '2025-11-06', '14:00:00', 'pending', 'Child has fever and cold');
