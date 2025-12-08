-- Pharmacy Database Schema
-- Created for Farmacia Nucului Batran project

CREATE DATABASE IF NOT EXISTS pharmacy_db;
USE pharmacy_db;

-- Users/Students Table
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  first_name VARCHAR(100),
  last_name VARCHAR(100),
  phone VARCHAR(20),
  role ENUM('student', 'admin', 'pharmacist') DEFAULT 'student',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  is_active BOOLEAN DEFAULT TRUE
);

-- Medicines Table
CREATE TABLE IF NOT EXISTS medicines (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL UNIQUE,
  generic_name VARCHAR(150),
  description TEXT,
  dosage VARCHAR(100),
  form ENUM('tablet', 'capsule', 'liquid', 'injection', 'cream', 'powder') NOT NULL,
  manufacturer VARCHAR(150),
  price DECIMAL(10, 2) NOT NULL,
  stock_quantity INT DEFAULT 0,
  expiration_date DATE,
  requires_prescription BOOLEAN DEFAULT FALSE,
  side_effects TEXT,
  contraindications TEXT,
  active_ingredient VARCHAR(200),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Medicine Categories (Many-to-Many)
CREATE TABLE IF NOT EXISTS medicine_categories (
  medicine_id INT NOT NULL,
  category_id INT NOT NULL,
  PRIMARY KEY (medicine_id, category_id),
  FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  total_amount DECIMAL(10, 2) NOT NULL,
  status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
  delivery_date DATE,
  notes TEXT,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  medicine_id INT NOT NULL,
  quantity INT NOT NULL,
  unit_price DECIMAL(10, 2) NOT NULL,
  subtotal DECIMAL(10, 2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE CASCADE
);

-- Prescriptions Table
CREATE TABLE IF NOT EXISTS prescriptions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  medicine_id INT NOT NULL,
  prescribed_date DATE NOT NULL,
  expiration_date DATE NOT NULL,
  dosage_instructions TEXT NOT NULL,
  quantity INT NOT NULL,
  prescription_number VARCHAR(100) UNIQUE,
  prescriber_name VARCHAR(150),
  status ENUM('active', 'expired', 'used') DEFAULT 'active',
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE CASCADE
);

-- Inventory Log Table (for tracking stock changes)
CREATE TABLE IF NOT EXISTS inventory_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  medicine_id INT NOT NULL,
  quantity_change INT NOT NULL,
  previous_quantity INT NOT NULL,
  new_quantity INT NOT NULL,
  change_reason VARCHAR(100),
  changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  changed_by INT,
  FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE CASCADE,
  FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Reviews/Ratings Table
CREATE TABLE IF NOT EXISTS reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  medicine_id INT NOT NULL,
  rating INT CHECK (rating >= 1 AND rating <= 5),
  review_text TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE CASCADE,
  UNIQUE KEY unique_user_medicine (user_id, medicine_id)
);

-- Suppliers Table
CREATE TABLE IF NOT EXISTS suppliers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL UNIQUE,
  email VARCHAR(100),
  phone VARCHAR(20),
  address TEXT,
  city VARCHAR(100),
  country VARCHAR(100),
  contact_person VARCHAR(150),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Medicine Suppliers (Many-to-Many)
CREATE TABLE IF NOT EXISTS medicine_suppliers (
  medicine_id INT NOT NULL,
  supplier_id INT NOT NULL,
  supplier_price DECIMAL(10, 2),
  reorder_quantity INT,
  PRIMARY KEY (medicine_id, supplier_id),
  FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE CASCADE,
  FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE
);

-- Create Indexes for better performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_medicines_name ON medicines(name);
CREATE INDEX idx_orders_user_id ON orders(user_id);
CREATE INDEX idx_order_items_order_id ON order_items(order_id);
CREATE INDEX idx_prescriptions_user_id ON prescriptions(user_id);
CREATE INDEX idx_reviews_user_id ON reviews(user_id);
CREATE INDEX idx_inventory_log_medicine_id ON inventory_log(medicine_id);

-- Insert sample categories
INSERT INTO categories (name, description) VALUES
('Painkillers', 'Medications for pain relief'),
('Antibiotics', 'Antibacterial medications'),
('Vitamins', 'Vitamin supplements'),
('Cold & Flu', 'Medications for cold and flu symptoms'),
('Antacids', 'Medications for heartburn and indigestion'),
('Sleep Aids', 'Medications for sleep disorders'),
('Antihistamines', 'Medications for allergies');

-- Insert sample medicines
INSERT INTO medicines (name, generic_name, dosage, form, manufacturer, price, stock_quantity, requires_prescription, active_ingredient) VALUES
('Paracetamol 500mg', 'Paracetamol', '500mg', 'tablet', 'Pharma Ltd', 2.50, 100, FALSE, 'Paracetamol'),
('Ibuprofen 400mg', 'Ibuprofen', '400mg', 'tablet', 'Pharma Ltd', 3.00, 80, FALSE, 'Ibuprofen'),
('Amoxicillin 250mg', 'Amoxicillin', '250mg', 'capsule', 'Bio Pharma', 5.50, 50, TRUE, 'Amoxicillin'),
('Vitamin C 500mg', 'Ascorbic Acid', '500mg', 'tablet', 'Vitals Inc', 4.00, 120, FALSE, 'Ascorbic Acid'),
('Cetirizine 10mg', 'Cetirizine', '10mg', 'tablet', 'Allergy Care', 3.50, 90, FALSE, 'Cetirizine Hydrochloride'),
('Aspirin 100mg', 'Acetylsalicylic Acid', '100mg', 'tablet', 'Pharma Ltd', 1.50, 150, FALSE, 'Acetylsalicylic Acid'),
('Metformin 500mg', 'Metformin', '500mg', 'tablet', 'Diabetes Care', 4.25, 200, TRUE, 'Metformin Hydrochloride'),
('Lisinopril 10mg', 'Lisinopril', '10mg', 'tablet', 'Cardio Pharma', 6.75, 110, TRUE, 'Lisinopril'),
('Atorvastatin 20mg', 'Atorvastatin', '20mg', 'tablet', 'Cardio Pharma', 7.50, 95, TRUE, 'Atorvastatin Calcium'),
('Omeprazole 20mg', 'Omeprazole', '20mg', 'capsule', 'Gastro Care', 5.00, 140, FALSE, 'Omeprazole'),
('Loratadine 10mg', 'Loratadine', '10mg', 'tablet', 'Allergy Care', 3.75, 130, FALSE, 'Loratadine'),
('Fluconazole 150mg', 'Fluconazole', '150mg', 'capsule', 'Anti-Fungal Ltd', 8.50, 60, TRUE, 'Fluconazole'),
('Azithromycin 250mg', 'Azithromycin', '250mg', 'tablet', 'Bio Pharma', 6.25, 75, TRUE, 'Azithromycin'),
('Ciprofloxacin 500mg', 'Ciprofloxacin', '500mg', 'tablet', 'Bio Pharma', 7.00, 85, TRUE, 'Ciprofloxacin Hydrochloride'),
('Methylprednisolone 4mg', 'Methylprednisolone', '4mg', 'tablet', 'Immuno Pharma', 5.50, 100, TRUE, 'Methylprednisolone'),
('Insulin Glargine', 'Insulin Glargine', '100 U/ml', 'injection', 'Diabetes Care', 45.00, 40, TRUE, 'Insulin Glargine'),
('Salbutamol 100mcg', 'Salbutamol', '100mcg', 'powder', 'Respiratory Care', 8.00, 70, TRUE, 'Salbutamol Sulphate'),
('Fexofenadine 180mg', 'Fexofenadine', '180mg', 'tablet', 'Allergy Care', 4.50, 120, FALSE, 'Fexofenadine Hydrochloride'),
('Sertraline 50mg', 'Sertraline', '50mg', 'tablet', 'Mental Health', 6.00, 105, TRUE, 'Sertraline Hydrochloride'),
('Amlodipine 5mg', 'Amlodipine', '5mg', 'tablet', 'Cardio Pharma', 5.75, 95, TRUE, 'Amlodipine Besylate'),
('Itraconazole 100mg', 'Itraconazole', '100mg', 'capsule', 'Anti-Fungal Ltd', 9.50, 55, TRUE, 'Itraconazole'),
('Diclofenac 50mg', 'Diclofenac', '50mg', 'tablet', 'Pharma Ltd', 3.25, 140, TRUE, 'Diclofenac Sodium'),
('Naproxen 250mg', 'Naproxen', '250mg', 'tablet', 'Pharma Ltd', 3.50, 125, FALSE, 'Naproxen Sodium'),
('Prednisone 5mg', 'Prednisone', '5mg', 'tablet', 'Immuno Pharma', 4.00, 160, TRUE, 'Prednisone'),
('Gabapentin 300mg', 'Gabapentin', '300mg', 'capsule', 'Neuro Care', 7.25, 80, TRUE, 'Gabapentin'),
('Levothyroxine 50mcg', 'Levothyroxine', '50mcg', 'tablet', 'Thyroid Care', 4.50, 180, TRUE, 'Levothyroxine Sodium'),
('Enalapril 10mg', 'Enalapril', '10mg', 'tablet', 'Cardio Pharma', 5.25, 115, TRUE, 'Enalapril Maleate'),
('Metoprolol 50mg', 'Metoprolol', '50mg', 'tablet', 'Cardio Pharma', 6.00, 100, TRUE, 'Metoprolol Tartrate'),
('Amiodarone 200mg', 'Amiodarone', '200mg', 'tablet', 'Cardio Pharma', 10.50, 50, TRUE, 'Amiodarone Hydrochloride'),
('Warfarin 5mg', 'Warfarin', '5mg', 'tablet', 'Cardio Pharma', 8.75, 65, TRUE, 'Warfarin Sodium'),
('Clopidogrel 75mg', 'Clopidogrel', '75mg', 'tablet', 'Cardio Pharma', 9.25, 70, TRUE, 'Clopidogrel Bisulfate'),
('Spironolactone 25mg', 'Spironolactone', '25mg', 'tablet', 'Cardio Pharma', 5.50, 110, TRUE, 'Spironolactone'),
('Furosemide 40mg', 'Furosemide', '40mg', 'tablet', 'Cardio Pharma', 4.75, 135, TRUE, 'Furosemide'),
('Hydrochlorothiazide 25mg', 'Hydrochlorothiazide', '25mg', 'tablet', 'Cardio Pharma', 4.25, 145, TRUE, 'Hydrochlorothiazide'),
('Cimetidine 400mg', 'Cimetidine', '400mg', 'tablet', 'Gastro Care', 5.25, 115, FALSE, 'Cimetidine'),
('Ranitidine 150mg', 'Ranitidine', '150mg', 'tablet', 'Gastro Care', 5.50, 120, FALSE, 'Ranitidine Hydrochloride'),
('Nurofen Copii', 'Ibuprofen', '200mg', 'liquid', 'Pharma Ltd', 6.50, 85, FALSE, 'Ibuprofen'),
('Coldrex', 'Paracetamol si Fenilefrina', '500mg', 'tablet', 'Pharma Ltd', 4.25, 110, FALSE, 'Paracetamol'),
('Strepsils', 'Amilmetacresol si Dihexilamine', 'lozenges', 'tablet', 'Throat Care', 3.50, 100, FALSE, 'Amilmetacresol'),
('Fervex', 'Paracetamol si Vitamina C', 'plic', 'powder', 'Pharma Ltd', 2.75, 180, FALSE, 'Paracetamol'),
('Bonetableta', 'Calcium si Vitamina D', '500mg', 'tablet', 'Vitals Inc', 5.25, 95, FALSE, 'Calcium Carbonate'),
('Magnerot', 'Magnesiu Orotat', '500mg', 'tablet', 'Vitals Inc', 4.75, 120, FALSE, 'Magnesium Orotate'),
('Actavis Linctus', 'Cough Syrup', '200ml', 'liquid', 'Respiratory Care', 4.50, 60, FALSE, 'Dextromethorphan'),
('Tantum Verde', 'Benzidamin HCl', '0.15%', 'cream', 'Throat Care', 5.50, 75, FALSE, 'Benzidaminium Chloride'),
('Fludipar', 'Docusate', '100mg', 'capsule', 'Gastro Care', 3.75, 130, FALSE, 'Docusate Sodium'),
('Biofeel', 'Lactobacillus', 'probiotics', 'capsule', 'Probiotics Ltd', 6.25, 85, FALSE, 'Lactobacillus Acidophilus'),
('Smecta', 'Diosmectite', '3g', 'powder', 'Gastro Care', 4.00, 150, FALSE, 'Diosmectite'),
('Grasantin', 'Rauwolfia Serpentina', '40mg', 'tablet', 'Cardio Pharma', 3.50, 110, FALSE, 'Rauwolfia Serpentina'),
('Locosol', 'Ketoconazol', '2%', 'cream', 'Anti-Fungal Ltd', 6.75, 70, FALSE, 'Ketoconazole'),
('Bepanthen', 'Dexpanthenol', '5%', 'cream', 'Skin Care', 7.50, 65, FALSE, 'Dexpanthenol'),
('Nivea Creme', 'Lanolin si Ulei de Migdale', '100ml', 'cream', 'Skin Care', 5.00, 95, FALSE, 'Lanolin'),
('Sudocrem', 'Zinc Oxide', '15%', 'cream', 'Skin Care', 6.00, 80, FALSE, 'Zinc Oxide'),
('Heparol', 'Heparin', '1000U/g', 'cream', 'Cardio Pharma', 8.50, 50, FALSE, 'Heparin Sodium'),
('Bepanthol Tatici', 'Panthenol', '100mg', 'liquid', 'Skin Care', 4.50, 120, FALSE, 'Panthenol'),
('Detumex', 'Aescin', '50mg', 'tablet', 'Vascular Care', 5.75, 105, FALSE, 'Aescin'),
('Detralex', 'Diosmin', '500mg', 'tablet', 'Vascular Care', 7.25, 75, TRUE, 'Diosmin'),
('Trombless', 'Acetylsalicylic Acid', '75mg', 'tablet', 'Cardio Pharma', 3.25, 140, TRUE, 'Acetylsalicylic Acid'),
('Varicin', 'Podophyllum si Salicylic Acid', 'solution', 'liquid', 'Dermatology', 6.50, 45, FALSE, 'Podophyllum'),
('Fucidine', 'Fusidic Acid', '2%', 'cream', 'Antibacterial', 7.75, 60, FALSE, 'Fusidic Acid'),
('Bactroban', 'Mupirocin', '2%', 'cream', 'Antibacterial', 8.00, 55, FALSE, 'Mupirocin'),
('Betadine', 'Povidone-Iodine', '10%', 'liquid', 'Antiseptic', 4.25, 125, FALSE, 'Povidone-Iodine'),
('Rivanol', 'Ethacridine', '0.1%', 'liquid', 'Antiseptic', 3.75, 135, FALSE, 'Ethacridine'),
('Calendula Tinctura', 'Calendula Officinalis', '100ml', 'liquid', 'Natural Care', 3.50, 110, FALSE, 'Calendula Extract'),
('Argento Gel', 'Silver Nitrate', '1%', 'gel', 'Antibacterial', 6.50, 70, FALSE, 'Silver Nitrate');

-- Insert sample users
INSERT INTO users (username, email, password, first_name, last_name, role) VALUES
('admin_user', 'admin@pharmacy.com', SHA2('admin123', 256), 'Admin', 'Administrator', 'admin'),
('pharmacist1', 'pharmacist@pharmacy.com', SHA2('pharma123', 256), 'Maria', 'Popescu', 'pharmacist'),
-- Students
('student_andi', 'andi.student@pharmacy.com', SHA2('pass123', 256), 'Andi', 'Ionescu', 'student'),
('student_maria', 'maria.student@pharmacy.com', SHA2('pass123', 256), 'Maria', 'Gheorghe', 'student'),
('student_alex', 'alex.student@pharmacy.com', SHA2('pass123', 256), 'Alex', 'Mihai', 'student'),
('student_sara', 'sara.student@pharmacy.com', SHA2('pass123', 256), 'Sara', 'Stanescu', 'student'),
-- Normal Adults
('adult_ion', 'ion.adulti@pharmacy.com', SHA2('pass123', 256), 'Ion', 'Vasile', 'student'),
('adult_ana', 'ana.adulti@pharmacy.com', SHA2('pass123', 256), 'Ana', 'Popescu', 'student'),
('adult_george', 'george.adulti@pharmacy.com', SHA2('pass123', 256), 'George', 'Petre', 'student'),
('adult_elena', 'elena.adulti@pharmacy.com', SHA2('pass123', 256), 'Elena', 'Stoica', 'student'),
('adult_radu', 'radu.adulti@pharmacy.com', SHA2('pass123', 256), 'Radu', 'Marin', 'student'),
('adult_lidia', 'lidia.adulti@pharmacy.com', SHA2('pass123', 256), 'Lidia', 'Baciu', 'student'),
-- Elderly People
('elderly_nicolae', 'nicolae.pensionar@pharmacy.com', SHA2('pass123', 256), 'Nicolae', 'Sandu', 'student'),
('elderly_constanta', 'constanta.pensionar@pharmacy.com', SHA2('pass123', 256), 'Constanta', 'Toma', 'student'),
('elderly_grigore', 'grigore.pensionar@pharmacy.com', SHA2('pass123', 256), 'Grigore', 'Zidaru', 'student'),
('elderly_margareta', 'margareta.pensionar@pharmacy.com', SHA2('pass123', 256), 'Margareta', 'Antal', 'student'),
('elderly_viktor', 'viktor.pensionar@pharmacy.com', SHA2('pass123', 256), 'Viktor', 'Nemeth', 'student'),
('elderly_silvia', 'silvia.pensionar@pharmacy.com', SHA2('pass123', 256), 'Silvia', 'Kovacs', 'student');

-- Insert sample categories for medicines
INSERT INTO medicine_categories (medicine_id, category_id) VALUES
(1, 1), -- Paracetamol is a painkiller
(2, 1), -- Ibuprofen is a painkiller
(3, 2), -- Amoxicillin is an antibiotic
(4, 3), -- Vitamin C is a vitamin
(5, 7); -- Cetirizine is an antihistamine
