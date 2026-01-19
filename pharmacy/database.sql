-- Create the pharmacy database
CREATE DATABASE IF NOT EXISTS pharmacy;
USE pharmacy;

-- Create users table (both employees and customers)
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    address VARCHAR(255),
    age INT,
    cnp VARCHAR(13),
    role ENUM('customer', 'employee', 'admin') DEFAULT 'customer',
    is_active TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_username (username)
);

-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create medicines table
CREATE TABLE IF NOT EXISTS medicines (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    net_weight VARCHAR(50),
    description TEXT,
    short_description VARCHAR(255),
    requires_prescription BOOLEAN DEFAULT FALSE,
    stock_quantity INT DEFAULT 0,
    price DECIMAL(10, 2),
    side_effects TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name),
    INDEX idx_requires_prescription (requires_prescription)
);

-- Create medicine_categories table (many-to-many relationship)
CREATE TABLE IF NOT EXISTS medicine_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    medicine_id INT NOT NULL,
    category_id INT NOT NULL,
    FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    UNIQUE KEY unique_medicine_category (medicine_id, category_id)
);

-- Create orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) DEFAULT 0,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_order_date (order_date)
);

-- Create order_items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    medicine_id INT NOT NULL,
    quantity INT DEFAULT 1,
    unit_price DECIMAL(10, 2),
    subtotal DECIMAL(10, 2),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE CASCADE,
    INDEX idx_order_id (order_id)
);

-- Insert sample categories
INSERT INTO categories (name, description) VALUES
('Răceală și Gripă', 'Medicamente pentru tratarea răcelii și gripei'),
('Dureri în Gât', 'Medicamente pentru dureri în gât'),
('Tuse', 'Medicamente pentru tuse'),
('Analgezice', 'Medicamente pentru dureri'),
('Antibiotice', 'Antibiotice și antimicrobiene'),
('Vitamine', 'Vitamine și suplineri nutritivi'),
('Suplimente', 'Suplimente alimentare'),
('Antialergice', 'Medicamente pentru alergii'),
('Cardiovascular', 'Medicamente cardiovasculare'),
('Digestiv', 'Medicamente digestive'),
('Respirator', 'Medicamente respiratorii');

-- Insert sample medicines
INSERT INTO medicines (name, net_weight, short_description, description, requires_prescription, stock_quantity, price, side_effects) VALUES
('Theraflu Extra Grip', '10 plicuri', 'Pulbere pentru gripă', 'Pulbere pentru soluție orală. Tratamentul simptomatic al răcelii și gripei. Dizolvați conținutul plicului în apă caldă.', FALSE, 45, 35.99, 'Nervozitate,Insomnie,Amețeli'),
('Parasinus', '12 comprimate', 'Pentru congestie nazală', 'Pentru congestie nazală și dureri sinusale. 12 comprimate. 1 comprimat la 8–12 ore, maxim 3 comprimate/zi.', FALSE, 60, 28.99, 'Somnolență,Uscăciunea gurii'),
('Strepsils Intensiv Miere & Lămâie', '24 pastile', 'Pastile pentru dureri în gât', 'Pastile pentru dureri în gât. 24 pastile. Sugeți câte o pastilă la nevoie, până la 8 pe zi.', FALSE, 80, 25.99, 'Iritație locală'),
('ACC 600mg', '10 plicuri', 'Expectorant pentru tuse', 'Expectorant pentru tuse productivă. 10 plicuri efervescente. Dizolvați 1 plic în apă.', FALSE, 40, 32.99, 'Alergii,Rash'),
('Vitamina C 1000mg', '20 comprimate', 'Susține sistemul imunitar', 'Susține sistemul imunitar. 20 comprimate efervescente. 1 comprimat pe zi.', FALSE, 100, 29.99, 'Risc de calculi renali la doze mari'),
('Augmentin 1000mg', '10 comprimate', 'Antibiotic cu spectru larg', 'Antibiotic cu spectru larg. 10 comprimate filmate. Necesită rețetă medicală.', TRUE, 25, 65.99, 'Alergii,Diaree,Afecțiuni hepatice'),
('Paracetamol 500mg', '20 comprimate', 'Antitermic și analgezic', 'Antitermic și analgezic. 20 comprimate. 1 comprimat la 6-8 ore, maxim 4 pe zi.', FALSE, 150, 15.99, 'Reacții alergice,Problemă hepatică rară'),
('Ibuprofen 400mg', '24 comprimate', 'Antiinflamator și analgezic', 'Antiinflamator și analgezic. 24 comprimate. 1 comprimat la 6-8 ore.', FALSE, 80, 18.99, 'Probleme gastrice,Alergii'),
('Omeprazol 20mg', '30 capsule', 'Pentru aciditate gastrică', 'Reduce aciditatea gastrică. 30 capsule. 1 capsulă pe zi.', TRUE, 35, 22.50, 'Durere de cap,Diaree'),
('Nurofen Plus', '12 comprimate', 'Antiinflamator puternic', 'Antiinflamator și analgezic puternic. 12 comprimate. Necesită rețetă pentru unele versiuni.', FALSE, 55, 28.50, 'Probleme gastrice,Reacții cutanate'),
('Coldrex Max', '16 comprimate', 'Pentru simptomele gripei', 'Pentru simptomele gripei și răcelii. 16 comprimate. 1-2 comprimate la 6 ore.', FALSE, 70, 31.99, 'Somnolență,Nervozitate'),
('Fervex', '8 plicuri', 'Tratament gripal', 'Tratament gripal complet. 8 plicuri efervescente. 1 plic la 6 ore.', FALSE, 90, 24.99, 'Alergii la componentele active');

-- Link medicines to categories
INSERT INTO medicine_categories (medicine_id, category_id) VALUES
(1, 1), -- Theraflu -> Răceală și Gripă
(2, 1), -- Parasinus -> Răceală și Gripă
(3, 2), -- Strepsils -> Dureri în Gât
(4, 3), -- ACC -> Tuse
(5, 6), -- Vitamina C -> Vitamine
(6, 5), -- Augmentin -> Antibiotice
(7, 4), -- Paracetamol -> Analgezice
(8, 4), -- Ibuprofen -> Analgezice
(9, 10), -- Omeprazol -> Digestiv
(10, 4), -- Nurofen -> Analgezice
(11, 1), -- Coldrex -> Răceală și Gripă
(12, 1); -- Fervex -> Răceală și Gripă

-- Insert sample customers (5 customers)
INSERT INTO users (username, email, password, first_name, last_name, phone, address, age, cnp, role, is_active) VALUES
('ion.popescu', 'ion.popescu@example.com', '5e884898da28047151d0e56f8dc62927e8f0d3f5a9e3c7b3c4d5e6f7a8b9c0d1', 'Ion', 'Popescu', '0721234567', 'Strada Libertatii 10, Bucuresti', 35, '1900112345678', 'customer', 1),
('maria.ionescu', 'maria.ionescu@example.com', '5e884898da28047151d0e56f8dc62927e8f0d3f5a9e3c7b3c4d5e6f7a8b9c0d1', 'Maria', 'Ionescu', '0722345678', 'Bulevardul Unirii 25, Bucuresti', 28, '1950223456789', 'customer', 1),
('andrei.vasilescu', 'andrei.vasilescu@example.com', '5e884898da28047151d0e56f8dc62927e8f0d3f5a9e3c7b3c4d5e6f7a8b9c0d1', 'Andrei', 'Vasilescu', '0723456789', 'Strada Doamnei 5, Bucuresti', 42, '1932334567890', 'customer', 1),
('elena.nicolescu', 'elena.nicolescu@example.com', '5e884898da28047151d0e56f8dc62927e8f0d3f5a9e3c7b3c4d5e6f7a8b9c0d1', 'Elena', 'Nicolescu', '0724567890', 'Piata Amzei 12, Bucuresti', 31, '1945445678901', 'customer', 1),
('mihai.georgescu', 'mihai.georgescu@example.com', '5e884898da28047151d0e56f8dc62927e8f0d3f5a9e3c7b3c4d5e6f7a8b9c0d1', 'Mihai', 'Georgescu', '0725678901', 'Strada Moldovei 8, Bucuresti', 39, '1923556789012', 'customer', 1);

-- Insert sample employees (3 employees)
INSERT INTO users (username, email, password, first_name, last_name, phone, address, age, cnp, role, is_active) VALUES
('cristina.pharmacist', 'cristina.pharmacist@pharmacy.com', '7c6a180b36896a0a8c02787eeafb0e4649c6e8f3d0b1b0c6f8f5e8d4c3b2a1f5', 'Cristina', 'Marinescu', '0726789012', 'Strada Farmaciutei 1, Bucuresti', 45, '1934667890123', 'employee', 1),
('tudor.pharmacist', 'tudor.pharmacist@pharmacy.com', '7c6a180b36896a0a8c02787eeafb0e4649c6e8f3d0b1b0c6f8f5e8d4c3b2a1f5', 'Tudor', 'Popescu', '0727890123', 'Strada Sanatatii 15, Bucuresti', 50, '1924778901234', 'employee', 1),
('alina.manager', 'alina.manager@pharmacy.com', '7c6a180b36896a0a8c02787eeafb0e4649c6e8f3d0b1b0c6f8f5e8d4c3b2a1f5', 'Alina', 'Gheorghe', '0728901234', 'Strada Medicului 20, Bucuresti', 38, '1955889012345', 'employee', 1);
