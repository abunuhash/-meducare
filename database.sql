-- Create database
CREATE DATABASE IF NOT EXISTS meducare_db;
USE meducare_db;

-- Users table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    role ENUM('patient', 'admin') DEFAULT 'patient',
    profile_pic VARCHAR(255),
    address TEXT,
    city VARCHAR(50),
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'),
    date_of_birth DATE,
    emergency_contact VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Medicines table
CREATE TABLE medicines (
    medicine_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    generic_name VARCHAR(100),
    category VARCHAR(50),
    manufacturer VARCHAR(100),
    country_of_origin VARCHAR(50),
    dosage VARCHAR(50),
    form ENUM('Tablet', 'Capsule', 'Syrup', 'Injection', 'Cream', 'Ointment', 'Drops', 'Inhaler', 'Patch'),
    strength VARCHAR(50),
    price DECIMAL(10,2),
    stock INT DEFAULT 0,
    requires_prescription BOOLEAN DEFAULT TRUE,
    description TEXT,
    usage_instructions TEXT,
    side_effects TEXT,
    contraindications TEXT,
    warnings TEXT,
    storage_instructions TEXT,
    expiry_date DATE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(user_id),
    INDEX idx_category (category),
    INDEX idx_name (name)
);

-- Medicine categories
CREATE TABLE medicine_categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) UNIQUE,
    description TEXT
);

-- Medicine interactions
CREATE TABLE medicine_interactions (
    interaction_id INT AUTO_INCREMENT PRIMARY KEY,
    medicine_id_1 INT,
    medicine_id_2 INT,
    severity ENUM('Mild', 'Moderate', 'Severe'),
    description TEXT,
    FOREIGN KEY (medicine_id_1) REFERENCES medicines(medicine_id),
    FOREIGN KEY (medicine_id_2) REFERENCES medicines(medicine_id)
);

-- Q&A table
CREATE TABLE qa (
    qa_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category VARCHAR(50),
    question TEXT NOT NULL,
    answer TEXT,
    answered_by INT,
    views INT DEFAULT 0,
    likes INT DEFAULT 0,
    status ENUM('pending', 'answered', 'closed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    answered_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (answered_by) REFERENCES users(user_id)
);

-- First Aid table
CREATE TABLE first_aid (
    aid_id INT AUTO_INCREMENT PRIMARY KEY,
    condition_name VARCHAR(100) NOT NULL,
    symptoms TEXT,
    causes TEXT,
    first_aid_steps TEXT,
    do_not TEXT,
    warning_signs TEXT,
    when_to_see_doctor TEXT,
    prevention_tips TEXT,
    category VARCHAR(50),
    icon_class VARCHAR(50),
    severity ENUM('Mild', 'Moderate', 'Severe', 'Emergency') DEFAULT 'Mild',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Medical History (Educational) table
CREATE TABLE medical_history (
    history_id INT AUTO_INCREMENT PRIMARY KEY,
    year_discovered VARCHAR(20),
    title VARCHAR(200) NOT NULL,
    description TEXT,
    discoverer VARCHAR(100),
    country VARCHAR(50),
    category VARCHAR(50),
    fun_fact TEXT,
    image_url VARCHAR(255),
    impact TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- User Medical Records
CREATE TABLE user_medical_records (
    record_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    record_date DATE,
    record_type ENUM('Checkup', 'Test', 'Surgery', 'Vaccination', 'Prescription'),
    description TEXT,
    doctor_name VARCHAR(100),
    hospital_name VARCHAR(200),
    diagnosis TEXT,
    prescription TEXT,
    notes TEXT,
    file_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Appointments
CREATE TABLE appointments (
    appointment_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    doctor_name VARCHAR(100),
    doctor_specialty VARCHAR(100),
    hospital_name VARCHAR(200),
    appointment_date DATE,
    appointment_time TIME,
    reason TEXT,
    status ENUM('scheduled', 'completed', 'cancelled', 'rescheduled') DEFAULT 'scheduled',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- User Questions (for Q&A)
CREATE TABLE user_questions (
    question_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    question TEXT NOT NULL,
    category VARCHAR(50),
    is_anonymous BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Medicine reviews
CREATE TABLE medicine_reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    medicine_id INT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    review TEXT,
    effectiveness ENUM('Poor', 'Fair', 'Good', 'Excellent'),
    side_experienced TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (medicine_id) REFERENCES medicines(medicine_id)
);

-- Health articles
CREATE TABLE health_articles (
    article_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT,
    author VARCHAR(100),
    category VARCHAR(50),
    tags VARCHAR(255),
    views INT DEFAULT 0,
    image_url VARCHAR(255),
    published_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Pharmacies (Bangladeshi focus)
CREATE TABLE pharmacies (
    pharmacy_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    address TEXT,
    city VARCHAR(50),
    district VARCHAR(50),
    phone VARCHAR(20),
    emergency_phone VARCHAR(20),
    opening_hours VARCHAR(100),
    has_delivery BOOLEAN DEFAULT FALSE,
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Medicine availability in pharmacies
CREATE TABLE pharmacy_medicines (
    pharmacy_medicine_id INT AUTO_INCREMENT PRIMARY KEY,
    pharmacy_id INT,
    medicine_id INT,
    price DECIMAL(10,2),
    in_stock BOOLEAN DEFAULT TRUE,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pharmacy_id) REFERENCES pharmacies(pharmacy_id),
    FOREIGN KEY (medicine_id) REFERENCES medicines(medicine_id)
);

-- Insert medicine categories
INSERT INTO medicine_categories (category_name, description) VALUES
('Antibiotics', 'Medicines that fight bacterial infections'),
('Pain Relief', 'Analgesics and pain management medications'),
('Antihistamines', 'Allergy relief medications'),
('Antacids', 'Digestive health and acid reflux medications'),
('Vitamins & Supplements', 'Nutritional supplements and vitamins'),
('Blood Pressure', 'Antihypertensive medications'),
('Diabetes', 'Anti-diabetic medications'),
('Cardiovascular', 'Heart and circulatory system medications'),
('Respiratory', 'Asthma, COPD and breathing medications'),
('Neurological', 'Brain and nervous system medications'),
('Mental Health', 'Antidepressants and anxiety medications'),
('Dermatological', 'Skin care and treatment medications'),
('Eye Care', 'Ophthalmic preparations'),
('Ear Care', 'Otic preparations'),
('Gastrointestinal', 'Digestive system medications'),
('Hormonal', 'Hormone therapy medications'),
('Vaccines', 'Immunization and vaccines'),
('Antifungals', 'Fungal infection treatments'),
('Antivirals', 'Viral infection treatments'),
('Traditional Medicine', 'Ayurvedic, Unani and herbal medicines');

-- Insert users (including Bangladeshi users) admin123
INSERT INTO users (first_name, last_name, email, phone, password, role, address, city, blood_group, emergency_contact) VALUES
-- Admin users
('Admin', 'User', 'admin@gmail.com', '01712345678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Dhaka Medical College', 'Dhaka', 'O+', '01987654321'),
('Mahmud', 'Un Nabi', 'mahmud@gmail.com', '01811223344', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Square Hospital', 'Dhaka', 'B+', '01711223344'),

-- Patient users (Bangladeshi) patient123
('S M', 'Pritun', 'pritun@gmail.com', '01712345679', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient', '123 Gulshan Avenue', 'Dhaka', 'A+', '01719876543'),
('Abu', 'Nuhash', 'abu@gmail.com', '01812345678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient', '456 Banani', 'Dhaka', 'B-', '01912345678'),
('Rahima', 'Begum', 'rahima@gmail.com', '01912345679', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient', '78 Mirpur Road', 'Dhaka', 'AB+', '01812345670'),
('Karim', 'Mia', 'karim@gmail.com', '01798765432', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient', '45 Chittagong Road', 'Chittagong', 'O-', '01898765432'),
('Fatema', 'Khatun', 'fatema@gmail.com', '01887654321', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient', '12 Sylhet', 'Sylhet', 'B+', '01787654321'),
('Rana', 'Hossain', 'rana@gmail.com', '01956789012', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient', '34 Rajshahi', 'Rajshahi', 'A-', '01956789013');

-- Insert Bangladeshi medicines
INSERT INTO medicines (name, generic_name, category, manufacturer, country_of_origin, dosage, form, strength, price, stock, requires_prescription, description, usage_instructions, side_effects, contraindications, warnings, storage_instructions) VALUES
-- Square Pharmaceuticals (Bangladesh's largest pharma company)
('Napa', 'Paracetamol', 'Pain Relief', 'Square Pharmaceuticals Ltd.', 'Bangladesh', '500mg', 'Tablet', '500mg', 1.50, 5000, FALSE, 'Napa is Bangladesh\'s most trusted paracetamol brand for fever and pain relief', 'Take 1-2 tablets every 4-6 hours as needed. Do not exceed 8 tablets in 24 hours.', 'Rare skin rash, liver damage with overdose', 'Severe liver disease', 'Do not take with alcohol. Consult doctor if pain persists for more than 3 days.', 'Store below 30°C, protect from light and moisture'),
('Napa Extra', 'Paracetamol + Caffeine', 'Pain Relief', 'Square Pharmaceuticals Ltd.', 'Bangladesh', '500mg + 65mg', 'Tablet', '500mg/65mg', 2.00, 3500, FALSE, 'Enhanced pain relief with caffeine for faster action', 'Take 1 tablet every 6 hours', 'Insomnia, restlessness, increased heart rate', 'Caffeine sensitivity', 'Avoid in evening to prevent sleep disturbance', 'Store in cool dry place'),
('Seclo', 'Omeprazole', 'Gastrointestinal', 'Square Pharmaceuticals Ltd.', 'Bangladesh', '20mg', 'Capsule', '20mg', 5.00, 2500, TRUE, 'Proton pump inhibitor for acid reflux and gastric ulcers', 'Take 1 capsule daily before breakfast', 'Headache, nausea, diarrhea', 'Liver disease', 'Long-term use may increase fracture risk', 'Store below 25°C'),
('Afixime', 'Cefixime', 'Antibiotics', 'Square Pharmaceuticals Ltd.', 'Bangladesh', '200mg', 'Tablet', '200mg', 12.00, 1800, TRUE, 'Third-generation cephalosporin antibiotic', 'Take 1 tablet twice daily for 7-10 days', 'Diarrhea, abdominal pain', 'Penicillin allergy', 'Complete full course', 'Store below 30°C'),
('Ciprocin', 'Ciprofloxacin', 'Antibiotics', 'Square Pharmaceuticals Ltd.', 'Bangladesh', '500mg', 'Tablet', '500mg', 8.00, 2200, TRUE, 'Fluoroquinolone antibiotic for bacterial infections', 'Take 1 tablet twice daily', 'Nausea, tendonitis', 'Tendon disorders', 'Avoid in athletes', 'Store in original package'),
('Montair', 'Montelukast', 'Respiratory', 'Square Pharmaceuticals Ltd.', 'Bangladesh', '10mg', 'Tablet', '10mg', 7.50, 1500, TRUE, 'For asthma and allergy symptom relief', 'Take once daily in evening', 'Headache, thirst', 'Phenylketonuria', 'Not for acute asthma attacks', 'Store below 25°C'),

-- Beximco Pharmaceuticals
('Fexo', 'Fexofenadine', 'Antihistamines', 'Beximco Pharmaceuticals Ltd.', 'Bangladesh', '120mg', 'Tablet', '120mg', 4.50, 3000, FALSE, 'Non-drowsy allergy relief', 'Take 1 tablet daily', 'Headache', 'Kidney disease', 'Avoid fruit juices close to dose', 'Store at room temperature'),
('Maxpro', 'Pantoprazole', 'Gastrointestinal', 'Beximco Pharmaceuticals Ltd.', 'Bangladesh', '40mg', 'Tablet', '40mg', 6.00, 2000, TRUE, 'PPI for gastric acid disorders', 'Take 1 tablet daily before breakfast', 'Flatulence', 'Liver impairment', 'Short-term use only', 'Store below 25°C'),
('Rupcom', 'Rupatadine', 'Antihistamines', 'Beximco Pharmaceuticals Ltd.', 'Bangladesh', '10mg', 'Tablet', '10mg', 8.00, 1200, FALSE, 'For allergic rhinitis and urticaria', 'Take 1 tablet daily', 'Somnolence', 'Pregnancy', 'May cause drowsiness', 'Store in cool place'),
('Ace', 'Paracetamol', 'Pain Relief', 'Beximco Pharmaceuticals Ltd.', 'Bangladesh', '500mg', 'Tablet', '500mg', 1.20, 10000, FALSE, 'Economical paracetamol brand', 'As directed', 'Same as Napa', 'Liver disease', 'Do not exceed dose', 'Room temperature'),
('Napa Jnr', 'Paracetamol', 'Pain Relief', 'Beximco Pharmaceuticals Ltd.', 'Bangladesh', '120mg/5ml', 'Syrup', '120mg/5ml', 35.00, 800, FALSE, 'Children\'s paracetamol syrup', 'As per weight chart', 'Rare allergic reactions', 'Liver disease', 'Use measuring cup', 'Keep refrigerated after opening'),
('Osartil', 'Losartan', 'Blood Pressure', 'Beximco Pharmaceuticals Ltd.', 'Bangladesh', '50mg', 'Tablet', '50mg', 6.50, 2500, TRUE, 'ARB for hypertension', 'Once daily', 'Dizziness', 'Pregnancy', 'Monitor BP regularly', 'Store below 30°C'),

-- Incepta Pharmaceuticals
('Alatrol', 'Cetirizine', 'Antihistamines', 'Incepta Pharmaceuticals Ltd.', 'Bangladesh', '10mg', 'Tablet', '10mg', 2.00, 4000, FALSE, 'Antiallergic medication', 'Once daily', 'Drowsiness', 'Glaucoma', 'Avoid driving', 'Cool dry place'),
('Esonix', 'Esomeprazole', 'Gastrointestinal', 'Incepta Pharmaceuticals Ltd.', 'Bangladesh', '40mg', 'Capsule', '40mg', 7.00, 1800, TRUE, 'For GERD and ulcers', 'Before breakfast', 'Headache', 'Severe liver disease', 'Swallow whole', 'Store below 25°C'),
('Tusca', 'Dextromethorphan + Chlorpheniramine', 'Respiratory', 'Incepta Pharmaceuticals Ltd.', 'Bangladesh', '10mg/2mg', 'Syrup', '10mg/2mg per 5ml', 45.00, 600, FALSE, 'Cough suppressant with antihistamine', '10ml three times daily', 'Drowsiness', 'MAOI use', 'Not for asthma', 'Keep tightly closed'),
('Ateno', 'Atenolol', 'Cardiovascular', 'Incepta Pharmaceuticals Ltd.', 'Bangladesh', '50mg', 'Tablet', '50mg', 4.50, 2200, TRUE, 'Beta-blocker for hypertension', 'Once daily', 'Fatigue', 'Bradycardia', 'Do not stop abruptly', 'Room temperature'),
('Glip', 'Glibenclamide', 'Diabetes', 'Incepta Pharmaceuticals Ltd.', 'Bangladesh', '5mg', 'Tablet', '5mg', 3.00, 1800, TRUE, 'Oral hypoglycemic for type 2 diabetes', 'With meals', 'Hypoglycemia', 'Type 1 diabetes', 'Monitor blood sugar', 'Store below 30°C'),

-- Renata Pharmaceuticals
('Amoxy', 'Amoxicillin', 'Antibiotics', 'Renata Limited', 'Bangladesh', '500mg', 'Capsule', '500mg', 10.00, 2200, TRUE, 'Broad-spectrum antibiotic', 'Three times daily', 'Diarrhea', 'Penicillin allergy', 'Complete course', 'Cool place'),
('Dox', 'Doxycycline', 'Antibiotics', 'Renata Limited', 'Bangladesh', '100mg', 'Tablet', '100mg', 6.00, 1500, TRUE, 'Tetracycline antibiotic', 'Once or twice daily', 'Photosensitivity', 'Children under 8', 'Use sunscreen', 'Protect from light'),
('Cardivas', 'Carvedilol', 'Cardiovascular', 'Renata Limited', 'Bangladesh', '6.25mg', 'Tablet', '6.25mg', 5.50, 1000, TRUE, 'For heart failure and hypertension', 'Twice daily', 'Dizziness', 'Severe liver disease', 'Monitor weight', 'Store in original container'),
('Pred', 'Prednisolone', 'Hormonal', 'Renata Limited', 'Bangladesh', '5mg', 'Tablet', '5mg', 3.50, 2800, TRUE, 'Corticosteroid for inflammation', 'As directed by physician', 'Weight gain', 'Systemic fungal infection', 'Do not stop abruptly', 'Room temperature'),
('Sertina', 'Sertraline', 'Mental Health', 'Renata Limited', 'Bangladesh', '50mg', 'Tablet', '50mg', 8.00, 900, TRUE, 'SSRI antidepressant', 'Once daily', 'Nausea', 'MAOI use', 'May take weeks to work', 'Store below 30°C'),

-- Acme Laboratories
('Adovas', 'Atorvastatin', 'Cardiovascular', 'Acme Laboratories Ltd.', 'Bangladesh', '10mg', 'Tablet', '10mg', 5.00, 3000, TRUE, 'Statin for cholesterol control', 'Once daily evening', 'Muscle pain', 'Liver disease', 'Report muscle symptoms', 'Room temperature'),
('Clopid', 'Clopidogrel', 'Cardiovascular', 'Acme Laboratories Ltd.', 'Bangladesh', '75mg', 'Tablet', '75mg', 7.00, 1800, TRUE, 'Antiplatelet for heart patients', 'Once daily', 'Bleeding', 'Active bleeding', 'Watch for bruising', 'Store below 25°C'),
('Diamet', 'Metformin', 'Diabetes', 'Acme Laboratories Ltd.', 'Bangladesh', '500mg', 'Tablet', '500mg', 3.50, 3500, TRUE, 'First-line diabetes medication', 'With meals', 'GI upset', 'Metabolic acidosis', 'Monitor kidney function', 'Cool dry place'),
('Zentel', 'Albendazole', 'Antiparasitic', 'Acme Laboratories Ltd.', 'Bangladesh', '400mg', 'Tablet', '400mg', 10.00, 2000, FALSE, 'For worm infections', 'Single dose', 'Abdominal pain', 'Pregnancy', 'Can repeat after 2 weeks', 'Store below 25°C'),
('Xepa', 'Cetirizine', 'Antihistamines', 'Acme Laboratories Ltd.', 'Bangladesh', '10mg', 'Tablet', '10mg', 1.80, 5000, FALSE, 'Antiallergic', 'Once daily', 'Mild drowsiness', 'Severe kidney disease', 'Avoid alcohol', 'Room temperature'),

-- Popular over-the-counter Bangladeshi medicines
('Savlon', 'Chlorhexidine + Cetrimide', 'Antiseptic', 'ACI Limited', 'Bangladesh', 'Antiseptic', 'Cream', '50g tube', 45.00, 2000, FALSE, 'Antiseptic cream for cuts and wounds', 'Apply 2-3 times daily', 'Skin irritation', 'Known allergy', 'For external use only', 'Below 25°C'),
('Voltral', 'Diclofenac', 'Pain Relief', 'Novartis Bangladesh', 'Bangladesh', '50mg', 'Tablet', '50mg', 2.50, 4000, FALSE, 'Pain relief for arthritis and muscle pain', '2-3 times daily with food', 'Stomach upset', 'Peptic ulcer', 'Not for long-term use', 'Room temperature'),
('Benadon', 'Vitamin B6', 'Vitamins', 'GlaxoSmithKline Bangladesh', 'Bangladesh', '25mg', 'Tablet', '25mg', 1.20, 6000, FALSE, 'Vitamin B6 supplement', 'Once daily', 'None', 'None known', 'Store in cool place', 'Room temperature'),
('Calbo D', 'Calcium + Vitamin D3', 'Vitamins', 'Sanofi Bangladesh', 'Bangladesh', '500mg/200IU', 'Tablet', '500mg/200IU', 6.00, 2500, FALSE, 'Calcium and vitamin D supplement', '1-2 tablets daily', 'Constipation', 'Hypercalcemia', 'Take with food', 'Store below 30°C'),
('Neurobion', 'Vitamin B Complex', 'Vitamins', 'Merck Bangladesh', 'Bangladesh', 'B1+B6+B12', 'Tablet', 'High dose', 8.00, 1800, FALSE, 'Neurotropic B vitamins', 'Once daily', 'None', 'None', 'For nerve health', 'Room temperature'),
('Zinc', 'Zinc Sulfate', 'Vitamins', 'Popular Pharmaceuticals', 'Bangladesh', '20mg', 'Tablet', '20mg', 2.00, 3500, FALSE, 'Zinc supplement for diarrhea and immunity', 'Once daily', 'Nausea if empty stomach', 'None', 'Take after food', 'Cool place'),
('ORS', 'Oral Rehydration Salt', 'Electrolyte', 'Multiple Manufacturers', 'Bangladesh', 'WHO formula', 'Powder', '20.5g/L', 10.00, 10000, FALSE, 'For dehydration from diarrhea', 'Dissolve in 1L clean water', 'None', 'None', 'Use fresh solution', 'Store in cool place'),

-- Traditional Bangladeshi medicines
('Hamdard Safi', 'Herbal Blood Purifier', 'Traditional Medicine', 'Hamdard Laboratories (Waqf) Bangladesh', 'Bangladesh', 'Syrup', 'Syrup', '200ml', 85.00, 500, FALSE, 'Traditional Unani blood purifier', '2 tablespoons twice daily', 'Mild laxative effect', 'Pregnancy', 'Shake well before use', 'Cool place'),
('Kalbishudi', 'Herbal Digestive', 'Traditional Medicine', 'Kalbishudi Ayurvedic', 'Bangladesh', 'Tablet', 'Tablet', '500mg', 3.00, 800, FALSE, 'Ayurvedic digestive aid', '2 tablets after meals', 'None reported', 'None', 'Traditional use only', 'Dry place'),
('Shakti Chawanprash', 'Herbal Jam', 'Traditional Medicine', 'Shakti Food', 'Bangladesh', 'Immunity booster', 'Jam', '500g', 120.00, 300, FALSE, 'Traditional Ayurvedic immunity booster', '1 teaspoon twice daily', 'None', 'Diabetes', 'Sugar content high', 'Refrigerate after opening'),
('Himalaya Liv.52', 'Liver Support', 'Traditional Medicine', 'Himalaya Bangladesh', 'Bangladesh', 'Liver tonic', 'Tablet', '500mg', 5.00, 600, FALSE, 'Ayurvedic liver support', '2 tablets twice daily', 'None', 'None', 'For liver health', 'Room temperature');

-- Insert more Bangladeshi medicines (additional 50)
INSERT INTO medicines (name, generic_name, category, manufacturer, country_of_origin, dosage, form, strength, price, stock, requires_prescription, description) VALUES
('Alatrol Plus', 'Cetirizine + Phenylephrine', 'Antihistamines', 'Incepta Pharmaceuticals Ltd.', 'Bangladesh', '5mg/5mg', 'Tablet', '5mg/5mg', 3.50, 1200, FALSE, 'Allergy plus decongestant'),
('Napa Plus', 'Paracetamol + Phenylephrine', 'Pain Relief', 'Square Pharmaceuticals Ltd.', 'Bangladesh', '500mg/5mg', 'Tablet', '500mg/5mg', 3.00, 2500, FALSE, 'Cold and flu relief'),
('Tufnil', 'Paracetamol + Caffeine', 'Pain Relief', 'Opsonin Pharma', 'Bangladesh', '500mg/65mg', 'Tablet', '500mg/65mg', 2.20, 3000, FALSE, 'Fast pain relief'),
('Rolac', 'Ranitidine', 'Gastrointestinal', 'Aristopharma Ltd.', 'Bangladesh', '150mg', 'Tablet', '150mg', 4.00, 2000, TRUE, 'H2 blocker for acid reduction'),
('Gastrin', 'Omeprazole', 'Gastrointestinal', 'Eskayef Bangladesh', 'Bangladesh', '20mg', 'Capsule', '20mg', 5.50, 1800, TRUE, 'Acid controller'),
('Protonix', 'Pantoprazole', 'Gastrointestinal', 'Healthcare Pharma', 'Bangladesh', '40mg', 'Tablet', '40mg', 6.50, 1500, TRUE, 'PPI for GERD'),
('Losartas', 'Losartan', 'Blood Pressure', 'Aristopharma Ltd.', 'Bangladesh', '50mg', 'Tablet', '50mg', 5.00, 2000, TRUE, 'ARB antihypertensive'),
('Amlovas', 'Amlodipine', 'Blood Pressure', 'Incepta Pharmaceuticals Ltd.', 'Bangladesh', '5mg', 'Tablet', '5mg', 4.00, 2500, TRUE, 'Calcium channel blocker'),
('Telma', 'Telmisartan', 'Blood Pressure', 'Eskayef Bangladesh', 'Bangladesh', '40mg', 'Tablet', '40mg', 8.00, 1800, TRUE, 'ARB for hypertension'),
('Cardopan', 'Valsartan', 'Blood Pressure', 'Square Pharmaceuticals Ltd.', 'Bangladesh', '80mg', 'Tablet', '80mg', 7.50, 1600, TRUE, 'ARB antihypertensive'),
('Gliben', 'Glibenclamide', 'Diabetes', 'Renata Limited', 'Bangladesh', '5mg', 'Tablet', '5mg', 3.20, 2200, TRUE, 'Sulfonylurea for diabetes'),
('Metform', 'Metformin', 'Diabetes', 'Beximco Pharmaceuticals Ltd.', 'Bangladesh', '850mg', 'Tablet', '850mg', 4.50, 3000, TRUE, 'Biguanide for diabetes'),
('Sitagliptin', 'Sitagliptin', 'Diabetes', 'Square Pharmaceuticals Ltd.', 'Bangladesh', '50mg', 'Tablet', '50mg', 15.00, 1000, TRUE, 'DPP-4 inhibitor'),
('Xalos', 'Losartan + Amlodipine', 'Blood Pressure', 'Incepta Pharmaceuticals Ltd.', 'Bangladesh', '50mg/5mg', 'Tablet', '50mg/5mg', 9.00, 1200, TRUE, 'Combination for BP'),
('Atorva', 'Atorvastatin', 'Cardiovascular', 'Renata Limited', 'Bangladesh', '20mg', 'Tablet', '20mg', 7.00, 2000, TRUE, 'Statin for cholesterol'),
('Rosuvas', 'Rosuvastatin', 'Cardiovascular', 'Square Pharmaceuticals Ltd.', 'Bangladesh', '10mg', 'Tablet', '10mg', 10.00, 1500, TRUE, 'Strong statin'),
('Simvastatin', 'Simvastatin', 'Cardiovascular', 'Beximco Pharmaceuticals Ltd.', 'Bangladesh', '20mg', 'Tablet', '20mg', 6.00, 1800, TRUE, 'Statin for cholesterol'),
('Clopivas', 'Clopidogrel', 'Cardiovascular', 'Square Pharmaceuticals Ltd.', 'Bangladesh', '75mg', 'Tablet', '75mg', 8.00, 2000, TRUE, 'Antiplatelet'),
('Ecospirin', 'Aspirin', 'Cardiovascular', 'US-Bangla Pharma', 'Bangladesh', '75mg', 'Tablet', '75mg', 2.50, 5000, FALSE, 'Low dose aspirin'),
('Azithro', 'Azithromycin', 'Antibiotics', 'Incepta Pharmaceuticals Ltd.', 'Bangladesh', '500mg', 'Tablet', '500mg', 15.00, 1800, TRUE, 'Macrolide antibiotic'),
('Cef-3', 'Cefixime', 'Antibiotics', 'Square Pharmaceuticals Ltd.', 'Bangladesh', '200mg', 'Capsule', '200mg', 14.00, 1600, TRUE, 'Cephalosporin antibiotic'),
('Cefpod', 'Cefpodoxime', 'Antibiotics', 'Beximco Pharmaceuticals Ltd.', 'Bangladesh', '200mg', 'Tablet', '200mg', 18.00, 1200, TRUE, 'Cephalosporin'),
('Levoflox', 'Levofloxacin', 'Antibiotics', 'Renata Limited', 'Bangladesh', '500mg', 'Tablet', '500mg', 12.00, 1500, TRUE, 'Fluoroquinolone'),
('Moxacil', 'Amoxicillin', 'Antibiotics', 'Acme Laboratories Ltd.', 'Bangladesh', '500mg', 'Capsule', '500mg', 8.00, 2500, TRUE, 'Penicillin antibiotic'),
('Doxacil', 'Doxycycline', 'Antibiotics', 'Aristopharma Ltd.', 'Bangladesh', '100mg', 'Tablet', '100mg', 5.50, 2000, TRUE, 'Tetracycline'),
('Flucan', 'Fluconazole', 'Antifungals', 'Square Pharmaceuticals Ltd.', 'Bangladesh', '150mg', 'Capsule', '150mg', 7.00, 1500, TRUE, 'Antifungal'),
('Itrizol', 'Itraconazole', 'Antifungals', 'Incepta Pharmaceuticals Ltd.', 'Bangladesh', '100mg', 'Capsule', '100mg', 20.00, 800, TRUE, 'Antifungal'),
('Clobet', 'Clobetasol', 'Dermatological', 'Square Pharmaceuticals Ltd.', 'Bangladesh', '0.05%', 'Cream', '15g', 35.00, 1000, TRUE, 'Topical steroid'),
('Betnovate', 'Betamethasone', 'Dermatological', 'GlaxoSmithKline Bangladesh', 'Bangladesh', '0.1%', 'Cream', '15g', 40.00, 1200, TRUE, 'Topical steroid'),
('Fucidin', 'Fusidic Acid', 'Dermatological', 'LEO Pharma Bangladesh', 'Bangladesh', '2%', 'Cream', '15g', 55.00, 800, TRUE, 'Antibiotic cream'),
('Candid', 'Clotrimazole', 'Dermatological', 'Square Pharmaceuticals Ltd.', 'Bangladesh', '1%', 'Cream', '20g', 25.00, 1500, FALSE, 'Antifungal cream'),
('Eczina', 'Hydrocortisone', 'Dermatological', 'Beximco Pharmaceuticals Ltd.', 'Bangladesh', '1%', 'Cream', '15g', 28.00, 1300, FALSE, 'For eczema'),
('Monas', 'Mometasone', 'Dermatological', 'Incepta Pharmaceuticals Ltd.', 'Bangladesh', '0.1%', 'Cream', '15g', 45.00, 900, TRUE, 'Topical steroid'),
('Rhinase', 'Oxymetazoline', 'Respiratory', 'Square Pharmaceuticals Ltd.', 'Bangladesh', '0.05%', 'Nasal Spray', '10ml', 65.00, 600, FALSE, 'Nasal decongestant'),
('Flexon', 'Chlorzoxazone + Paracetamol', 'Pain Relief', 'Incepta Pharmaceuticals Ltd.', 'Bangladesh', '250mg/500mg', 'Tablet', '250mg/500mg', 4.50, 2000, FALSE, 'Muscle relaxant'),
('Myolax', 'Thiocolchicoside', 'Pain Relief', 'Square Pharmaceuticals Ltd.', 'Bangladesh', '4mg', 'Tablet', '4mg', 6.00, 1800, TRUE, 'Muscle relaxant'),
('Etosid', 'Etoricoxib', 'Pain Relief', 'Beximco Pharmaceuticals Ltd.', 'Bangladesh', '90mg', 'Tablet', '90mg', 8.50, 1500, TRUE, 'COX-2 inhibitor'),
('Parax', 'Paracetamol', 'Pain Relief', 'Acme Laboratories Ltd.', 'Bangladesh', '500mg', 'Tablet', '500mg', 1.00, 10000, FALSE, 'Economy paracetamol'),
('Dolo', 'Paracetamol', 'Pain Relief', 'Renata Limited', 'Bangladesh', '500mg', 'Tablet', '500mg', 1.40, 8000, FALSE, 'Paracetamol'),
('Neo-Dex', 'Dexamethasone', 'Hormonal', 'Square Pharmaceuticals Ltd.', 'Bangladesh', '0.5mg', 'Tablet', '0.5mg', 2.50, 3500, TRUE, 'Corticosteroid'),
('Oradexon', 'Dexamethasone', 'Hormonal', 'Renata Limited', 'Bangladesh', '0.5mg', 'Tablet', '0.5mg', 2.20, 3000, TRUE, 'Steroid'),
('Calcimax', 'Calcium + Vitamin D3', 'Vitamins', 'Sanofi Bangladesh', 'Bangladesh', '500mg/200IU', 'Tablet', '500mg/200IU', 6.50, 2000, FALSE, 'Calcium supplement'),
('Beconase', 'Beclomethasone', 'Respiratory', 'GlaxoSmithKline Bangladesh', 'Bangladesh', '50mcg', 'Inhaler', '200 doses', 350.00, 300, TRUE, 'Asthma inhaler'),
('Ventolin', 'Salbutamol', 'Respiratory', 'GlaxoSmithKline Bangladesh', 'Bangladesh', '100mcg', 'Inhaler', '200 doses', 280.00, 400, TRUE, 'Reliever inhaler'),
('Budeson', 'Budesonide', 'Respiratory', 'Square Pharmaceuticals Ltd.', 'Bangladesh', '200mcg', 'Inhaler', '100 doses', 320.00, 350, TRUE, 'Preventer inhaler'),
('Tiotropium', 'Tiotropium', 'Respiratory', 'Beximco Pharmaceuticals Ltd.', 'Bangladesh', '18mcg', 'Inhaler', '30 doses', 450.00, 200, TRUE, 'COPD inhaler'),
('Formeterol', 'Formoterol', 'Respiratory', 'Incepta Pharmaceuticals Ltd.', 'Bangladesh', '12mcg', 'Inhaler', '60 doses', 380.00, 250, TRUE, 'Long-acting bronchodilator');

-- Insert first aid data
INSERT INTO first_aid (condition_name, symptoms, causes, first_aid_steps, do_not, warning_signs, when_to_see_doctor, prevention_tips, category, icon_class, severity) VALUES
-- Fever
('Fever', 'High temperature (above 100.4°F or 38°C), sweating, chills, body aches, headache, weakness', 'Infection (viral or bacterial), heat exhaustion, certain medications, vaccinations', '1. Rest and drink plenty of fluids\n2. Take acetaminophen (paracetamol) as directed\n3. Apply cool compress to forehead\n4. Remove excess clothing\n5. Keep room temperature comfortable\n6. Take lukewarm bath (not cold)', 'Do not bundle up in heavy blankets\nDo not take aspirin for fever in children\nDo not give both acetaminophen and ibuprofen together\nDo not use cold baths or ice', 'Fever above 103°F (39.4°C)\nFever lasting more than 3 days\nSevere headache\nStiff neck\nRash\nDifficulty breathing\nConfusion\nSeizures', 'See doctor if:\n- Fever exceeds 103°F\n- Lasts more than 3 days\n- Infant under 3 months with any fever\n- Accompanied by severe symptoms', 'Wash hands frequently\nAvoid contact with sick people\nStay up to date on vaccinations\nMaintain healthy immune system', 'General', 'temperature-high', 'Mild'),

-- COVID-19
('COVID-19', 'Fever or chills, cough, shortness of breath, fatigue, muscle or body aches, headache, loss of taste or smell, sore throat, congestion, nausea, diarrhea', 'SARS-CoV-2 virus infection, airborne transmission, close contact with infected person', '1. Isolate immediately in a separate room\n2. Wear mask when around others\n3. Rest and stay hydrated\n4. Monitor temperature and oxygen levels\n5. Take fever-reducing medication if needed\n6. Use pulse oximeter to check oxygen saturation\n7. Inform close contacts', 'Do not go to public places\nDo not share personal items\nDo not ignore worsening symptoms\nDo not take steroids without doctor advice\nDo not rely solely on home remedies', 'Oxygen saturation below 94%\nDifficulty breathing\nChest pain or pressure\nConfusion\nBluish lips or face\nInability to stay awake\nSevere dehydration', 'Seek emergency care for severe symptoms\nCall doctor if symptoms worsen\nHigh-risk patients should contact doctor immediately\nPregnant women should inform obstetrician', 'Get vaccinated and boosted\nWear masks in crowded places\nPractice hand hygiene\nMaintain physical distancing\nImprove ventilation', 'Infectious', 'virus', 'Severe'),

-- Headache
('Headache', 'Pain in head, pressure, throbbing sensation, sensitivity to light or sound, nausea (with migraines)', 'Stress, tension, dehydration, lack of sleep, eye strain, sinus problems, certain foods, hormonal changes, medication overuse', '1. Rest in dark, quiet room\n2. Apply cold or warm compress to head or neck\n3. Drink water if dehydrated\n4. Gently massage neck and shoulders\n5. Take over-the-counter pain reliever if needed\n6. Practice deep breathing or relaxation', 'Do not ignore severe sudden headache\nDo not skip meals\nDo not consume too much caffeine\nDo not overuse pain medications\nDo not drive if vision affected', 'Sudden severe headache (thunderclap)\nHeadache with fever and stiff neck\nHeadache after head injury\nHeadache with confusion or seizures\nHeadache with vision changes\nWorst headache of your life', 'See doctor if:\n- Headaches become more frequent or severe\n- Interfere with daily activities\n- Accompanied by neurological symptoms\n- New type of headache after age 50', 'Stay hydrated\nGet adequate sleep\nManage stress\nMaintain regular meals\nLimit caffeine and alcohol\nPractice good posture', 'Neurological', 'head-side-brain', 'Mild'),

-- Sunburn
('Sunburn', 'Red, painful skin that feels hot to touch, swelling, blisters, peeling skin, headache, fever, nausea (in severe cases)', 'Prolonged exposure to UV rays from sun or tanning beds, reflection from water/sand/snow, certain medications that increase sensitivity', '1. Get out of sun immediately\n2. Take cool bath or shower\n3. Apply cool compresses\n4. Use aloe vera or moisturizer\n5. Drink extra water\n6. Take ibuprofen for pain and inflammation\n7. Let blisters heal, don\'t pop them\n8. Use over-the-counter hydrocortisone cream for severe discomfort', 'Do not pop blisters\nDo not use petroleum-based products immediately\nDo not apply ice directly to skin\nDo not use products with benzocaine\nDo not scrub skin or peel loose skin', 'Sunburn with severe blistering over large area\nSunburn accompanied by high fever, chills, nausea\nSigns of infection (pus, increasing pain, swelling)\nDehydration\nHeat exhaustion symptoms', 'See doctor for severe sunburn with blisters over large area, signs of infection, or if accompanied by heat-related illness', 'Use broad-spectrum sunscreen SPF 30+\nReapply sunscreen every 2 hours\nSeek shade during peak hours (10am-4pm)\nWear protective clothing and hat\nAvoid tanning beds', 'Dermatological', 'sun', 'Mild'),

-- Food Poisoning
('Food Poisoning', 'Nausea, vomiting, watery or bloody diarrhea, abdominal pain and cramps, fever, loss of appetite', 'Bacteria (Salmonella, E. coli, Listeria), viruses (Norovirus), parasites, toxins from contaminated food, improper food handling or storage', '1. Stop eating solid foods temporarily\n2. Sip clear fluids slowly (water, oral rehydration solutions)\n3. Rest and let stomach settle\n4. Gradually introduce bland foods (BRAT diet: bananas, rice, applesauce, toast)\n5. Avoid dairy, caffeine, alcohol, fatty foods\n6. Replace electrolytes with sports drinks or ORS', 'Do not eat solid foods immediately\nDo not take anti-diarrheal medications without doctor advice (may trap bacteria)\nDo not consume dairy products\nDo not eat spicy or greasy foods\nDo not prepare food for others while sick', 'Bloody diarrhea\nFever above 102°F\nFrequent vomiting, unable to keep liquids down\nSigns of dehydration (dry mouth, excessive thirst, little urination, dizziness)\nDiarrhea lasting more than 3 days\nSevere abdominal pain', 'Seek medical help for severe dehydration, bloody stool, high fever, or if you are pregnant, elderly, or have weakened immune system', 'Wash hands before handling food\nCook food to proper temperatures\nRefrigerate perishable foods promptly\nAvoid cross-contamination\nWash fruits and vegetables\nBe cautious with street food when traveling', 'Gastrointestinal', 'biohazard', 'Moderate'),

-- Common Cold
('Common Cold', 'Runny or stuffy nose, sneezing, sore throat, cough, mild headache, mild body aches, low-grade fever (rare in adults)', 'Viral infection (rhinoviruses most common), airborne droplets, contact with contaminated surfaces', '1. Rest to help immune system fight virus\n2. Drink warm fluids (tea, soup, water with honey)\n3. Use saline nasal spray for congestion\n4. Gargle with warm salt water for sore throat\n5. Use humidifier to ease congestion\n6. Take over-the-counter cold medications as directed\n7. Honey for cough (for adults and children over 1 year)', 'Do not take antibiotics (they don\'t work for viruses)\nDo not give honey to infants under 1 year\nDo not smoke or expose to secondhand smoke\nDo not overuse decongestant sprays (rebound congestion)\nDo not suppress productive cough completely', 'Symptoms lasting more than 10 days\nHigh fever\nSevere sinus pain\nWorsening symptoms after initial improvement\nShortness of breath\nChest pain', 'See doctor if symptoms severe, persistent, or if you have underlying health conditions that could complicate recovery', 'Wash hands frequently\nAvoid touching face\nDisinfect frequently touched surfaces\nStay away from sick people\nBoost immune system with healthy diet, exercise, sleep', 'Respiratory', 'head-side-cough', 'Mild'),

-- Influenza (Flu)
('Influenza (Flu)', 'Sudden high fever (100-104°F), severe body aches, extreme fatigue, dry cough, sore throat, headache, chills, sometimes vomiting and diarrhea (more common in children)', 'Influenza virus types A and B, airborne transmission, contact with infected surfaces', '1. Rest in bed and isolate from others\n2. Drink plenty of fluids (water, clear broths, electrolyte drinks)\n3. Take acetaminophen or ibuprofen for fever and aches\n4. Use cough medicine or lozenges\n5. Use humidifier for congestion\n6. Take antiviral medication if prescribed within 48 hours\n7. Stay home until fever-free for 24 hours without meds', 'Do not go to work or school\nDo not share food or utensils\nDo not smoke\nDo not drink alcohol (causes dehydration)\nDo not give aspirin to children or teens (Reye\'s syndrome risk)', 'Difficulty breathing or shortness of breath\nChest or abdominal pain\nSudden dizziness\nConfusion\nSevere vomiting\nFlu-like symptoms that improve then return with fever and worse cough', 'Seek emergency care for warning signs. High-risk individuals (elderly, pregnant, chronic conditions) should contact doctor early.', 'Annual flu vaccination\nHand hygiene\nAvoid close contact with sick people\nCover coughs and sneezes\nStay home when sick', 'Infectious', 'lungs', 'Moderate'),

-- Cough
('Cough', 'Dry hacking cough, productive cough with mucus, chest congestion, sore throat from coughing, sleep disruption', 'Viral infections, allergies, asthma, GERD, smoking, environmental irritants, post-nasal drip, medications (ACE inhibitors)', '1. Stay hydrated to thin mucus\n2. Drink warm honey and lemon water (for adults)\n3. Use cough drops or hard candy\n4. Elevate head while sleeping\n5. Use steam inhalation or humidifier\n6. Gargle with warm salt water\n7. Avoid irritants like smoke\n8. Try over-the-counter cough suppressants or expectorants as appropriate', 'Do not suppress productive cough completely (it clears lungs)\nDo not give honey to infants under 1 year\nDo not smoke\nDo not use multiple cough medications together\nDo not give cough/cold medicine to children under 4', 'Cough lasting more than 3 weeks\nCoughing up blood\nShortness of breath\nHigh fever\nChest pain\nUnexplained weight loss\nNight sweats\nDifficulty swallowing', 'See doctor for persistent cough, especially if accompanied by worrying symptoms or if you have underlying health conditions', 'Avoid smoking and secondhand smoke\nManage allergies\nStay hydrated\nUse humidifier in dry environments\nWash hands to prevent infections', 'Respiratory', 'cough', 'Mild'),

-- Stomach Ache
('Stomach Ache', 'Abdominal pain or cramping, bloating, nausea, indigestion, gas', 'Indigestion, gas, constipation, food intolerance, infections, stress, menstrual cramps, overeating', '1. Drink clear fluids (water, ginger ale, peppermint tea)\n2. Apply heating pad to abdomen\n3. Rest in comfortable position\n4. Avoid solid foods temporarily\n5. Try the BRAT diet (bananas, rice, applesauce, toast) when ready to eat\n6. Use over-the-counter antacids for heartburn\n7. Peppermint or chamomile tea for gas', 'Do not eat spicy or fatty foods\nDo not consume caffeine or alcohol\nDo not take pain relievers that may irritate stomach (ibuprofen, aspirin) without food\nDo not lie flat immediately after eating\nDo not overeat when symptoms improve', 'Severe, constant pain\nPain that wakes you from sleep\nBloody stool\nFever above 101°F\nPersistent vomiting\nUnable to pass gas or stool\nPain during pregnancy\nYellowing of skin or eyes', 'Seek immediate care for severe pain, blood in stool, or if you have had abdominal surgery or trauma', 'Eat smaller, frequent meals\nChew food thoroughly\nAvoid trigger foods\nManage stress\nStay hydrated\nRegular exercise', 'Gastrointestinal', 'stomach', 'Mild'),

-- Diarrhea
('Diarrhea', 'Frequent loose, watery stools, urgent need to have bowel movement, abdominal cramps, nausea, possibly fever', 'Viral or bacterial infections, food poisoning, parasites, medications (especially antibiotics), food intolerances, digestive disorders', '1. Drink oral rehydration solutions (ORS) to replace lost fluids and electrolytes\n2. Eat bland, binding foods (bananas, rice, applesauce, toast)\n3. Rest and let digestive system recover\n4. Take probiotics to restore gut bacteria\n5. Avoid solid foods for few hours if vomiting also present\n6. Gradually reintroduce regular foods', 'Do not consume dairy products temporarily\nDo not eat fatty, spicy, or high-fiber foods\nDo not drink caffeine or alcohol\nDo not take anti-diarrheal medications if you have fever or bloody stool (may prolong infection)', 'Diarrhea lasting more than 2 days in adults, 24 hours in children\nBloody or black, tarry stool\nHigh fever (above 102°F)\nSevere abdominal pain\nSigns of dehydration: excessive thirst, dry mouth, little or no urination, severe weakness, dizziness\nDiarrhea in infants', 'Seek medical help for severe dehydration, bloody stool, prolonged symptoms, or if you have underlying health conditions', 'Wash hands thoroughly\nPractice food safety\nDrink clean water\nAvoid unsafe food when traveling\nGet vaccinated for rotavirus (infants)', 'Gastrointestinal', 'toilet', 'Mild'),

-- Pink Eye (Conjunctivitis)
('Pink Eye (Conjunctivitis)', 'Redness in white of eye, increased tearing, itching or burning sensation, discharge (watery or thick), crusting of eyelids or lashes, sensitivity to light', 'Viral or bacterial infection, allergies, chemical exposure, foreign object in eye, blocked tear duct (in infants), contact lens wear', '1. For infectious pink eye: wash hands frequently\n2. Apply warm compresses to soothe discomfort\n3. Clean eyes with warm water and clean cloth\n4. Use artificial tears for lubrication\n5. For allergic conjunctivitis: use cold compresses and antihistamine eye drops\n6. Remove contact lenses immediately\n7. Discard eye makeup and replace after infection clears', 'Do not touch or rub eyes\nDo not share towels, pillows, or eye drops\nDo not wear contact lenses until cleared\nDo not use the same bottle of drops for both eyes if one is infected\nDo not use red-eye reducing drops (they don\'t treat infection)', 'Moderate to severe eye pain\nSensitivity to light (photophobia)\nBlurred vision that doesn\'t clear with blinking\nIntense redness in one or both eyes\nSymptoms that worsen or don\'t improve\nWeakened immune system', 'See doctor for severe symptoms, if you wear contacts, or if symptoms persist beyond a few days. Bacterial conjunctivitis may require antibiotic drops.', 'Don\'t share personal items\nWash hands frequently\nReplace eye makeup regularly\nClean contact lenses properly\nAvoid touching eyes\nManage allergies', 'Eye Care', 'eye', 'Mild'),

-- Nosebleed
('Nosebleed', 'Bleeding from one or both nostrils, blood draining down back of throat', 'Dry air, nose picking, allergies, colds, injury, blood thinners, high blood pressure, deviated septum', '1. Sit upright and lean forward slightly\n2. Pinch soft part of nose firmly with thumb and index finger\n3. Breathe through mouth\n4. Maintain pressure for 10-15 minutes\n5. Apply ice pack to nose and cheeks\n6. After bleeding stops, rest quietly\n7. Use saline spray or petroleum jelly to keep nostrils moist', 'Do not lean back (blood can flow down throat)\nDo not lie down\nDo not pick or blow nose for several hours after\nDo not pack nose with tissues or gauze\nDo not lift heavy objects or strain', 'Bleeding lasting more than 20 minutes\nHeavy bleeding with large amount of blood\nDifficulty breathing\nBleeding after head injury or accident\nFrequent recurrent nosebleeds\nBlood thinners medication\nFeeling weak or dizzy', 'Seek emergency care for severe bleeding, after head injury, or if accompanied by other concerning symptoms', 'Use humidifier in dry environments\nAvoid nose picking\nKeep nasal passages moist with saline spray\nQuit smoking\nTreat allergies\nWear protective gear during sports', 'General', 'nose', 'Mild'),

-- Sprain
('Sprain', 'Pain, swelling, bruising, limited ability to move affected joint, popping sensation at time of injury', 'Overstretching or tearing of ligaments, falls, twists, sports injuries, awkward landing', '1. R.I.C.E. method:\n   - Rest: avoid activities that cause pain\n   - Ice: apply ice packs 20 minutes at a time\n   - Compression: wrap with elastic bandage\n   - Elevation: keep injured area raised above heart\n2. Take over-the-counter pain relievers\n3. Gentle range of motion exercises after initial pain subsides\n4. Use crutches for severe ankle/knee sprains', 'Do not apply heat immediately (increases swelling)\nDo not massage injured area\nDo not resume sports until healed\nDo not ignore severe pain\nDo not put weight on injury if too painful', 'Severe pain and swelling\nNumbness in injured area\nUnable to bear any weight\nJoint looks deformed or out of place\nPopping sound at time of injury with immediate inability to use joint\nSigns of infection (fever, redness spreading)', 'See doctor for severe sprains, if you can\'t bear weight, or if symptoms don\'t improve after few days of home care', 'Warm up before exercise\nWear proper footwear\nUse appropriate protective equipment\nStrengthen supporting muscles\nMaintain good balance\nAvoid uneven surfaces when possible', 'Musculoskeletal', 'bone', 'Mild'),

-- Allergic Reaction
('Allergic Reaction', 'Hives (raised, itchy welts), skin redness and itching, runny or stuffy nose, sneezing, watery eyes, swelling of lips, tongue, or face, difficulty breathing (severe)', 'Food allergies (peanuts, shellfish, eggs), medication allergies, insect stings, pollen, latex, pet dander', '1. For mild reactions: take oral antihistamine (cetirizine, loratadine)\n2. Apply calamine lotion or hydrocortisone cream for skin reactions\n3. Use cold compresses for swelling\n4. Avoid known triggers\n5. For known severe allergies: use epinephrine auto-injector if prescribed\n6. Remove stingers if insect sting\n7. Loosen tight clothing', 'Do not ignore breathing difficulties\nDo not take antihistamines if severely allergic (may mask symptoms)\nDo not apply heat to affected areas\nDo not scratch hives\nDo not eat suspected trigger foods again without testing', 'Difficulty breathing or wheezing\nSwelling of throat, tongue, or lips\nDizziness or fainting\nRapid, weak pulse\nNausea or vomiting\nHives over large area of body\nFeeling of doom\nLoss of consciousness', 'Call emergency immediately for signs of anaphylaxis (severe allergic reaction). Even if epinephrine is used, still need emergency follow-up.', 'Avoid known allergens\nRead food labels carefully\nWear medical alert bracelet\nCarry epinephrine auto-injector if prescribed\nInform restaurants of food allergies', 'Allergy', 'allergies', 'Severe'),

-- Burn (Minor)
('Burn (Minor)', 'Red skin, pain, swelling, small blisters, peeling skin', 'Heat (fire, hot liquids, steam), sun exposure, chemicals, electricity, friction', '1. Cool the burn with cool (not cold) running water for 10-15 minutes\n2. Remove jewelry or tight items near burned area\n3. Cover with sterile, non-stick gauze\n4. Take over-the-counter pain relievers\n5. Apply antibiotic ointment to prevent infection\n6. Keep burned area moisturized with aloe vera\n7. Drink extra fluids', 'Do not use ice (can cause further tissue damage)\nDo not apply butter, oil, or home remedies\nDo not pop blisters\nDo not use cotton balls (fibers can stick)\nDo not apply adhesive bandages directly on burn', 'Burn larger than 2-3 inches\nDeep burn (white or charred skin)\nBurns on face, hands, feet, genitals, or major joints\nChemical or electrical burns\nSigns of infection (increased pain, redness, pus, fever)\nBurns with smoke inhalation', 'See doctor for major burns, burns in sensitive areas, or if you have concerns about healing or infection', 'Use sunscreen\nTest water temperature before bathing\nKeep hot objects away from children\nInstall smoke detectors\nBe careful with hot liquids\nUse oven mitts', 'Dermatological', 'burn', 'Mild'),

-- Asthma Attack
('Asthma Attack', 'Severe shortness of breath, wheezing, coughing, chest tightness, difficulty speaking, anxiety, blue lips or fingernails (severe)', 'Triggers: allergens, respiratory infections, exercise, cold air, smoke, stress, medications (aspirin, beta-blockers)', '1. Sit upright, don\'t lie down\n2. Take quick-relief inhaler (usually blue) immediately\n3. Use spacer if available for better medication delivery\n4. Take slow, controlled breaths\n5. Loosen tight clothing\n6. Try to stay calm (anxiety worsens attack)\n7. If no improvement after 10 minutes, seek emergency care\n8. Use rescue inhaler every 30-60 seconds if severe', 'Do not lie down\nDo not panic\nDo not take deep breaths if it triggers coughing\nDo not rely solely on home remedies\nDo not ignore worsening symptoms\nDo not use expired inhalers', 'Peak flow reading in red zone\nNo improvement after using rescue inhaler\nDifficulty speaking in full sentences\nChest retractions (skin pulls in between ribs)\nBlue lips or fingernails\nConfusion or agitation\nSevere wheezing', 'Call emergency if severe symptoms, no improvement with inhaler, or if patient is exhausted from struggling to breathe', 'Take controller medications regularly\nAvoid triggers\nMonitor peak flow\nHave asthma action plan\nGet vaccinated for flu and pneumonia\nRegular check-ups', 'Respiratory', 'lungs', 'Severe'),

-- Heart Attack Symptoms
('Heart Attack', 'Chest pain or pressure (may feel like squeezing), pain spreading to shoulders, arms, neck, jaw, or back, shortness of breath, cold sweat, nausea, lightheadedness', 'Blockage in coronary artery, blood clot, atherosclerosis, coronary artery spasm', '1. Call emergency immediately\n2. Have person sit or lie down in comfortable position\n3. Loosen tight clothing\n4. Chew and swallow aspirin (162-325 mg) if not allergic and no contraindications\n5. If person is unconscious and not breathing, begin CPR\n6. Stay calm and reassure the person\n7. Note when symptoms started', 'Do not drive yourself to hospital\nDo not wait to see if symptoms go away\nDo not take aspirin if allergic or bleeding risk\nDo not give anything by mouth if unconscious\nDo not let person exert themselves\nDo not ignore symptoms even if mild', 'Any suspicion of heart attack is a medical emergency. Call emergency even if symptoms seem mild or intermittent. Every minute matters.', 'Call emergency immediately. Do not wait. Early treatment saves heart muscle and lives.', 'Healthy diet low in saturated fats\nRegular exercise\nDon\'t smoke\nManage stress\nControl blood pressure, cholesterol, diabetes\nMaintain healthy weight\nRegular check-ups', 'Cardiovascular', 'heart', 'Emergency'),

-- Stroke
('Stroke', 'Sudden numbness or weakness of face, arm, or leg (especially on one side), sudden confusion, trouble speaking or understanding, sudden trouble seeing in one or both eyes, sudden severe headache with no known cause, loss of balance or coordination', 'Blockage of blood vessel to brain (ischemic stroke), bleeding in brain (hemorrhagic stroke)', '1. Call emergency immediately\n2. Note time when symptoms started\n3. Keep person lying flat on side with head elevated\n4. Loosen tight clothing\n5. Do not give anything to eat or drink\n6. If unconscious, check breathing and begin CPR if needed\n7. Stay with person until help arrives', 'Do not drive to hospital\nDo not let person sleep it off\nDo not give aspirin (may worsen hemorrhagic stroke)\nDo not give food or drink (may cause choking)\nDo not move person unnecessarily\nDo not wait to see if symptoms resolve', 'Use FAST test:\nF - Face drooping\nA - Arm weakness\nS - Speech difficulty\nT - Time to call emergency\nAny stroke symptoms are emergency', 'Call emergency immediately. Stroke is time-sensitive - "time is brain". Treatment must begin within hours.', 'Control blood pressure\nDon\'t smoke\nManage diabetes\nHealthy diet\nRegular exercise\nLimit alcohol\nTreat atrial fibrillation', 'Neurological', 'brain', 'Emergency');

-- Insert medical history (educational)
INSERT INTO medical_history (year_discovered, title, description, discoverer, country, category, fun_fact, impact) VALUES
-- Major medical discoveries
('1928', 'Penicillin - The First Antibiotic', 'Alexander Fleming accidentally discovered penicillin when he noticed mold (Penicillium notatum) killing bacteria in a petri dish. This revolutionized medicine and saved millions of lives during World War II. Mass production was later developed by Florey and Chain.', 'Alexander Fleming', 'Scotland', 'Antibiotics', 'Fleming\'s petri dish was contaminated while he was on vacation. Instead of throwing it away, he noticed something interesting!', 'Penicillin reduced death rates from bacterial pneumonia from 30% to 6% and virtually eliminated deaths from infected wounds. It sparked the golden age of antibiotics.'),
('1921', 'Insulin - Diabetes Treatment', 'Frederick Banting and Charles Best extracted insulin from dogs, proving it could treat diabetes. They isolated the hormone from pancreatic islets and successfully treated diabetic dogs. Before insulin, diabetes was a death sentence within months of diagnosis.', 'Frederick Banting, Charles Best', 'Canada', 'Hormone Therapy', 'Banting and Best sold insulin to the University of Toronto for $1, wanting it to be available to all who needed it.', 'Insulin transformed type 1 diabetes from fatal disease to manageable chronic condition. Banting and Macleod received Nobel Prize in 1923. Today, millions use insulin daily.'),
('1897', 'Aspirin - The Wonder Drug', 'Felix Hoffmann synthesized aspirin (acetylsalicylic acid) at Bayer to help his father\'s arthritis. Derived from salicin in willow bark, which had been used for centuries by Hippocrates and traditional healers for pain and fever.', 'Felix Hoffmann', 'Germany', 'Pain Relief', 'Willow bark was used by ancient Egyptians, Greeks, and Native Americans long before aspirin was synthesized.', 'Aspirin became the world\'s most commonly used medicine, with over 100 billion tablets taken annually. It\'s used for pain, fever, inflammation, and low-dose for heart attack and stroke prevention.'),
('1796', 'Smallpox Vaccine', 'Edward Jenner developed the first vaccine by using cowpox material to protect against smallpox. He observed that milkmaids who caught cowpox didn\'t get smallpox. He inoculated 8-year-old James Phipps with cowpox, then exposed him to smallpox with no effect.', 'Edward Jenner', 'England', 'Immunology', 'The word "vaccine" comes from "vacca," Latin for cow, honoring the cowpox connection.', 'Jenner\'s work led to the complete eradication of smallpox in 1980 - the only human disease ever eliminated. It saved more lives than any other medical intervention and launched the field of immunology.'),
('1895', 'X-Ray Imaging', 'Wilhelm Röntgen discovered X-rays while experimenting with cathode rays. The first X-ray was of his wife\'s hand, showing her wedding ring. He called them "X-rays" because X stood for unknown. This revolutionized medical diagnosis forever.', 'Wilhelm Röntgen', 'Germany', 'Medical Imaging', 'Röntgen refused to patent his discovery, wanting humanity to benefit freely. He received the first Nobel Prize in Physics in 1901.', 'X-rays transformed medicine by allowing doctors to see inside the living body without surgery. Led to CT scans, mammography, and interventional radiology.'),
('1953', 'DNA Structure Discovered', 'James Watson and Francis Crick, using X-ray diffraction data from Rosalind Franklin and Maurice Wilkins, discovered the double helix structure of DNA. This opened the door to genetic medicine, personalized treatments, and understanding hereditary diseases.', 'Watson, Crick, Franklin, Wilkins', 'UK/USA', 'Genetics', 'Rosalind Franklin\'s "Photo 51" was critical evidence, though she wasn\'t initially credited. Her contribution was recognized later.', 'DNA discovery enabled genetic testing, gene therapy, personalized medicine, forensics, and the Human Genome Project. It\'s the foundation of modern biotechnology.'),
('1955', 'Polio Vaccine', 'Jonas Salk developed the first safe and effective polio vaccine using inactivated (killed) virus. Before this, polio paralyzed hundreds of thousands of children annually. The vaccine trials involved 1.8 million children - the largest medical experiment in history.', 'Jonas Salk', 'USA', 'Vaccination', 'When asked who owned the patent, Salk replied, "There is no patent. Could you patent the sun?"', 'Polio has been nearly eradicated worldwide, with only a few cases annually compared to 350,000 in 1988. Sabin\'s oral vaccine later enabled mass immunization.'),
('1846', 'First Public Surgery with Anesthesia', 'William Morton demonstrated ether anesthesia at Massachusetts General Hospital (the "Ether Dome"). Patient Edward Gilbert Abbott had a neck tumor removed painlessly. This transformed surgery from torture to painless procedure.', 'William Morton', 'USA', 'Surgery', 'Before anesthesia, surgeons were judged by speed - some amputations took under a minute. Patients were held down or given alcohol/opium.', 'Anesthesia enabled complex, lengthy surgeries and made surgery humane. It spawned the fields of anesthesiology and pain management. Surgery became about precision, not speed.'),
('1628', 'Discovery of Blood Circulation', 'William Harvey described how blood circulates through the body pumped by the heart, overturning 1500 years of Galen\'s theory that blood was made in the liver and consumed by tissues. He used careful observation and experimentation.', 'William Harvey', 'England', 'Physiology', 'Harvey\'s work was initially ridiculed. His practice declined after publication, but his ideas eventually prevailed.', 'This fundamental understanding enabled modern cardiology, vascular surgery, and transfusion medicine. It\'s the foundation of cardiovascular physiology.'),
('1861', 'Germ Theory of Disease', 'Louis Pasteur proved that germs cause disease, disproving spontaneous generation. He showed that microorganisms cause fermentation and spoilage, and later developed pasteurization and vaccines for rabies and anthrax.', 'Louis Pasteur', 'France', 'Microbiology', 'Pasteur said, "The role of the infinitely small in nature is infinitely great." His work led to sterilization, antiseptic surgery, and modern hygiene.', 'Germ theory revolutionized medicine, leading to sanitation, antisepsis (Lister), vaccines, and antibiotics. It\'s the foundation of infectious disease control and public health.'),
('1940s', 'First Chemotherapy Treatment', 'Nitrogen mustard, a chemical warfare agent from WWI, was found to kill cancer cells. Goodman and Gilman used it to treat lymphoma, beginning the first chemotherapy drugs and the fight against cancer.', 'Goodman, Gilman', 'USA', 'Oncology', 'The observation came from autopsies of soldiers exposed to mustard gas - they had destroyed lymphatic tissue, suggesting it might fight lymphoma.', 'Chemotherapy, along with surgery and radiation, became one of three main cancer treatments. It led to curative treatments for leukemias, lymphomas, and testicular cancer.'),
('1977', 'First MRI Scan of Human', 'Raymond Damadian performed the first whole-body MRI scan of a human, creating "Indomitable" - the first commercial MRI machine. MRI uses magnetic fields and radio waves, not radiation, to create detailed images of soft tissues.', 'Raymond Damadian', 'USA', 'Diagnostic Imaging', 'Damadian\'s first scan took nearly 5 hours. Today, MRI scans take 30-60 minutes with much higher resolution.', 'MRI revolutionized diagnosis of brain disorders, spinal cord injuries, joint problems, and cancer. Over 100 million scans are performed yearly worldwide.'),
('1929', 'Discovery of Penicillin\'s Power', 'Alexander Fleming published his discovery of penicillin, but it was Howard Florey and Ernst Chain who developed methods to mass-produce it during WWII, saving countless lives. They shared the 1945 Nobel Prize with Fleming.', 'Florey, Chain, Fleming', 'Australia/UK', 'Antibiotics', 'Penicillin production increased from enough to treat one patient in 1941 to treating all Allied forces by D-Day 1944.', 'Mass production of penicillin marked the beginning of the antibiotic era, dramatically reducing deaths from infections and enabling modern surgery and cancer treatment.'),
('1943', 'Discovery of Streptomycin', 'Selman Waksman and Albert Schatz discovered streptomycin, the first antibiotic effective against tuberculosis, which was once called "captain of the men of death." It was the first cure for TB.', 'Waksman, Schatz', 'USA', 'Antibiotics', 'Waksman coined the term "antibiotic." Schatz, a graduate student, was initially not credited but later received recognition.', 'Streptomycin was the first effective TB treatment and also worked against many other bacteria. It launched the search for antibiotics from soil bacteria.'),
('1983', 'Discovery of HIV', 'Luc Montagnier and Françoise Barré-Sinoussi at the Pasteur Institute in France discovered HIV as the cause of AIDS. Robert Gallo also contributed. The discovery enabled testing, treatment, and prevention.', 'Montagnier, Barré-Sinoussi', 'France', 'Infectious Disease', 'HIV was initially called LAV (lymphadenopathy-associated virus) in France and HTLV-III in the US, later unified as HIV.', 'The discovery led to blood testing (saving transfusion recipients), antiretroviral drugs (transforming AIDS from death sentence to chronic disease), and prevention strategies.'),
('2005', 'First HPV Vaccine', 'Ian Frazer and Jian Zhou developed the first vaccine against human papillomavirus (HPV), which causes cervical cancer. Gardasil was approved in 2006, preventing infections responsible for 70% of cervical cancers.', 'Frazer, Zhou', 'Australia', 'Vaccination', 'Zhou died in 1999, never seeing the vaccine he co-developed save millions of lives. Frazer continues his work.', 'HPV vaccination can eliminate cervical cancer, which kills over 300,000 women annually. It\'s the first vaccine specifically designed to prevent cancer.'),
('2012', 'CRISPR Gene Editing', 'Emmanuelle Charpentier and Jennifer Doudna developed CRISPR-Cas9, a revolutionary gene-editing tool that allows precise DNA modification. It\'s faster, cheaper, and more accurate than previous methods.', 'Charpentier, Doudna', 'USA/France', 'Genetics', 'CRISPR was adapted from a bacterial immune system that fights viruses. Bacteria "remember" viral DNA and cut it if reinfected.', 'CRISPR enables gene therapy for genetic diseases, development of disease models, agricultural improvements, and potential treatments for cancer and inherited disorders.'),
('2020', 'mRNA COVID-19 Vaccines', 'The first mRNA vaccines (Pfizer-BioNTech and Moderna) were developed in record time for COVID-19. They use messenger RNA to instruct cells to produce spike protein, triggering immune response. Katalin Karikó and Drew Weissman pioneered mRNA technology.', 'Karikó, Weissman', 'Germany/USA', 'Vaccination', 'mRNA vaccine technology was researched for decades before COVID-19. Karikó persisted despite years of rejection and funding challenges.', 'mRNA vaccines saved millions of lives during the pandemic and represent a platform technology that can be rapidly adapted for future diseases, including cancer and other infections.'),
('1847', 'Discovery of Chloroform Anesthesia', 'James Simpson discovered chloroform\'s anesthetic properties and used it in childbirth, despite religious opposition (some cited Genesis "in pain you shall bring forth children"). Queen Victoria used it for childbirth in 1853, increasing acceptance.', 'James Simpson', 'Scotland', 'Surgery', 'Simpson and friends tested chemicals by inhaling them at dinner parties. They woke up under the table after chloroform.', 'Chloroform, along with ether, made painless surgery and childbirth possible, though later replaced by safer agents. Simpson also revolutionized obstetrics.'),
('1867', 'Antiseptic Surgery', 'Joseph Lister introduced antiseptic surgery using carbolic acid (phenol) to sterilize wounds and instruments, dramatically reducing post-surgical infections and death rates. He was inspired by Pasteur\'s germ theory.', 'Joseph Lister', 'UK', 'Surgery', 'Listerine mouthwash is named after him, though he had nothing to do with its development.', 'Lister\'s work reduced surgical mortality from 45% to 15%. He pioneered sterile technique, leading to modern aseptic surgery and infection control.'),
('1901', 'Discovery of Blood Groups', 'Karl Landsteiner discovered the ABO blood group system, explaining why some blood transfusions succeeded and others failed fatally. This enabled safe blood transfusion.', 'Karl Landsteiner', 'Austria', 'Hematology', 'Landsteiner received the Nobel Prize in 1930. He also discovered the Rh factor in 1937 with Alexander Wiener.', 'Blood typing made transfusion medicine possible, saving millions of lives in surgery, trauma, and blood disorders. It also enabled understanding of hemolytic disease of newborn.'),
('1922', 'First Insulin Treatment', 'Leonard Thompson, 14, became the first person with diabetes to receive insulin injections. Before insulin, he weighed only 65 pounds and was near death. After treatment, he gained weight and lived another 13 years.', 'Banting, Best, Collip', 'Canada', 'Hormone Therapy', 'Eli Lilly began mass-producing insulin in 1923. Before that, extracting insulin from animal pancreases was extremely difficult and inefficient.', 'Insulin transformed diabetes care and remains essential for type 1 diabetes. It established hormone replacement therapy and launched the biotechnology industry.');

-- Insert pharmacies (Bangladesh focus)
INSERT INTO pharmacies (name, address, city, district, phone, emergency_phone, opening_hours, has_delivery, latitude, longitude) VALUES
('Square Hospital Pharmacy', '18/F, West Panthapath, Dhaka', 'Dhaka', 'Dhaka', '02-8142345', '01712345678', '24/7', TRUE, 23.7376, 90.3917),
('Lazz Pharma - Dhanmondi', '27, Satmasjid Road, Dhanmondi', 'Dhaka', 'Dhaka', '02-9123456', '01911223344', '8am - 12am', TRUE, 23.7465, 90.3745),
('Shah Ali Pharmacy', 'Shah Ali Market, Mirpur-1', 'Dhaka', 'Dhaka', '02-9001234', '01812345678', '9am - 11pm', TRUE, 23.8062, 90.3678),
('Ibn Sina Pharmacy', '69, Green Road, Dhaka', 'Dhaka', 'Dhaka', '02-9666789', '01798765432', '8am - 10pm', TRUE, 23.7461, 90.3816),
('New Market Pharmacy', 'New Market, Dhaka', 'Dhaka', 'Dhaka', '02-8623456', '01876543210', '10am - 9pm', FALSE, 23.7342, 90.3853),
('Osudher Dak', '102, Gulshan Avenue, Dhaka', 'Dhaka', 'Dhaka', '02-8854321', '01912345678', '9am - 11pm', TRUE, 23.7937, 90.4066),
('Mediastore Pharmacy', 'House 45, Road 11, Banani', 'Dhaka', 'Dhaka', '02-9876543', '01711122334', '8am - 12am', TRUE, 23.7939, 90.4045),
('Arogga Pharmacy', 'Uttara, Sector-3, Dhaka', 'Dhaka', 'Dhaka', '09612-123456', '01811223344', '24/7', TRUE, 23.8759, 90.3795),
('Chittagong Medical Pharmacy', 'Chittagong Medical College, Chittagong', 'Chittagong', 'Chittagong', '031-612345', '01812345678', '8am - 8pm', FALSE, 22.3569, 91.8347),
('Anderkilla Pharmacy', 'Anderkilla, Chittagong', 'Chittagong', 'Chittagong', '031-654321', '01912345678', '9am - 10pm', TRUE, 22.3351, 91.8314),
('Sylhet Osudhaloy', 'Zindabazar, Sylhet', 'Sylhet', 'Sylhet', '0821-715678', '01712345678', '9am - 9pm', TRUE, 24.8990, 91.8715),
('Rajshahi Medical Pharmacy', 'Rajshahi Medical College, Rajshahi', 'Rajshahi', 'Rajshahi', '0721-775123', '01812345678', '8am - 8pm', FALSE, 24.3745, 88.6042),
('Khulna Pharmacy', 'Dakbangla Mor, Khulna', 'Khulna', 'Khulna', '041-721234', '01912345678', '9am - 9pm', TRUE, 22.8456, 89.5403),
('Barisal Osudh Bhandar', 'Sadar Road, Barisal', 'Barisal', 'Barisal', '0431-64567', '01712345678', '9am - 8pm', FALSE, 22.7010, 90.3535),
('Rangpur Pharmacy', 'Station Road, Rangpur', 'Rangpur', 'Rangpur', '0521-61234', '01812345678', '9am - 9pm', TRUE, 25.7439, 89.2559),
('Mymensingh Medical Pharmacy', 'Mymensingh Medical College', 'Mymensingh', 'Mymensingh', '091-54321', '01912345678', '8am - 8pm', FALSE, 24.7471, 90.4203),
('Comilla Pharmacy', 'Jail Road, Comilla', 'Comilla', 'Comilla', '081-76543', '01712345678', '9am - 9pm', TRUE, 23.4610, 91.1858),
('Bogra Pharmacy', 'Satmatha, Bogra', 'Bogra', 'Bogra', '051-67890', '01812345678', '9am - 10pm', TRUE, 24.8466, 89.3745),
('Jessore Pharmacy', 'Chitra More, Jessore', 'Jessore', 'Jessore', '0421-67890', '01912345678', '9am - 9pm', FALSE, 23.1667, 89.2167),
('Dinajpur Pharmacy', 'Kachari Bazar, Dinajpur', 'Dinajpur', 'Dinajpur', '0531-65432', '01712345678', '9am - 8pm', FALSE, 25.6278, 88.6378);

-- Insert pharmacy medicines (availability)
INSERT INTO pharmacy_medicines (pharmacy_id, medicine_id, price, in_stock) VALUES
(1, 1, 1.50, TRUE),   -- Napa
(1, 2, 2.00, TRUE),   -- Napa Extra
(1, 3, 5.00, TRUE),   -- Seclo
(1, 4, 12.00, TRUE),  -- Afixime
(1, 5, 8.00, TRUE),   -- Ciprocin
(1, 6, 7.50, TRUE),   -- Montair
(1, 7, 4.50, TRUE),   -- Fexo
(1, 8, 6.00, TRUE),   -- Maxpro
(2, 1, 1.60, TRUE),   -- Napa
(2, 3, 5.20, TRUE),   -- Seclo
(2, 9, 8.00, TRUE),   -- Rupcom
(2, 10, 1.20, TRUE),  -- Ace
(2, 11, 35.00, TRUE), -- Napa Jnr
(2, 12, 6.50, TRUE),  -- Osartil
(3, 13, 2.00, TRUE),  -- Alatrol
(3, 14, 7.00, TRUE),  -- Esonix
(3, 15, 45.00, TRUE), -- Tusca
(3, 16, 4.50, TRUE),  -- Ateno
(3, 17, 3.00, TRUE),  -- Glip
(4, 18, 10.00, TRUE), -- Amoxy
(4, 19, 6.00, TRUE),  -- Dox
(4, 20, 5.50, TRUE),  -- Cardivas
(4, 21, 3.50, TRUE),  -- Pred
(4, 22, 8.00, TRUE),  -- Sertina
(5, 1, 1.40, TRUE),   -- Napa
(5, 2, 1.90, TRUE),   -- Napa Extra
(5, 10, 1.10, TRUE),  -- Ace
(6, 23, 5.00, TRUE),  -- Adovas
(6, 24, 7.00, TRUE),  -- Clopid
(6, 25, 3.50, TRUE),  -- Diamet
(6, 26, 10.00, TRUE), -- Zentel
(7, 27, 1.80, TRUE),  -- Xepa
(7, 28, 45.00, TRUE), -- Savlon
(7, 29, 2.50, TRUE),  -- Voltral
(8, 30, 1.20, TRUE),  -- Benadon
(8, 31, 6.00, TRUE),  -- Calbo D
(8, 32, 8.00, TRUE),  -- Neurobion
(8, 33, 2.00, TRUE),  -- Zinc
(8, 34, 10.00, TRUE), -- ORS
(9, 1, 1.70, TRUE),   -- Napa
(9, 3, 5.30, TRUE),   -- Seclo
(9, 4, 13.00, TRUE),  -- Afixime
(10, 5, 8.50, TRUE),  -- Ciprocin
(10, 7, 4.80, TRUE),  -- Fexo
(10, 18, 11.00, TRUE); -- Amoxy

-- Insert Q&A data
INSERT INTO qa (user_id, category, question, answer, answered_by, status, views, likes) VALUES
(2, 'Respiratory', 'How long does COVID-19 symptoms typically last?', 'Most people with COVID-19 recover within 2-4 weeks. Mild cases may resolve in 1-2 weeks, while severe cases can take 6 weeks or longer. Common symptoms like cough and fatigue may persist for several weeks. "Long COVID" refers to symptoms lasting beyond 12 weeks.', 1, 'answered', 234, 45),
(3, 'General Medicine', 'What\'s the difference between common cold and flu?', 'The flu usually comes on suddenly with high fever (100-102°F), severe body aches, extreme fatigue, and dry cough. Colds develop gradually with runny nose, sneezing, and sore throat, with mild or no fever. Flu symptoms are generally more intense and can lead to complications like pneumonia.', 1, 'answered', 567, 89),
(4, 'Gastrointestinal', 'Best home remedies for stomach ache?', '1. Ginger tea (anti-inflammatory)\n2. Peppermint tea (relieves gas)\n3. Chamomile tea (relaxes muscles)\n4. BRAT diet (bananas, rice, applesauce, toast)\n5. Heating pad on abdomen\n6. Avoid spicy, fatty, or dairy foods\n7. Stay hydrated with clear fluids\nIf symptoms persist or worsen, see a doctor.', 1, 'answered', 892, 120),
(5, 'Infectious', 'How to treat diarrhea at home?', 'Stay hydrated with oral rehydration solutions (ORS) or clear fluids. Eat bland, binding foods like bananas, rice, applesauce, and toast (BRAT diet). Avoid dairy, caffeine, alcohol, and fatty foods. Rest and let your digestive system recover. Probiotics may help restore gut bacteria. If diarrhea persists beyond 2 days or with blood/fever, seek medical attention.', 1, 'answered', 423, 67),
(6, 'Eye Care', 'Home treatment for pink eye (conjunctivitis)?', 'Clean eyes with warm water and cotton balls (use separate for each eye). Apply warm compresses for viral/bacterial pink eye, cold compresses for allergic. Use artificial tears for lubrication. Avoid touching eyes, wash hands frequently, don\'t share towels. If bacterial, you may need antibiotic eye drops from a doctor. See doctor if severe pain, light sensitivity, or vision changes.', 1, 'answered', 678, 95),
(7, 'Neurological', 'Natural remedies for tension headaches?', 'Apply cold or warm compress to head/neck. Practice relaxation techniques like deep breathing or meditation. Gently massage neck and shoulders. Rest in dark, quiet room. Stay hydrated. Peppermint or lavender oil may help. Avoid triggers like stress, poor posture, and eye strain. Regular exercise and adequate sleep can prevent headaches.', 1, 'answered', 345, 52),
(2, 'Cardiovascular', 'What is normal blood pressure?', 'Normal blood pressure is below 120/80 mmHg. Elevated: 120-129/<80. Stage 1 hypertension: 130-139/80-89. Stage 2: 140+/90+. Hypertensive crisis: >180/>120 (seek emergency care). These are guidelines; individual targets may vary based on age and health conditions. Regular monitoring is important.', 1, 'answered', 789, 112),
(3, 'Dermatological', 'How to treat sunburn at home?', 'Cool the skin with cool baths or compresses. Apply aloe vera or moisturizer. Drink extra water to rehydrate. Take ibuprofen for pain and inflammation. Don\'t pop blisters; let them heal naturally. Use over-the-counter hydrocortisone cream for severe discomfort. Avoid sun until fully healed. See doctor for severe burns with blistering over large area or signs of infection.', 1, 'answered', 456, 78),
(4, 'Mental Health', 'Natural ways to reduce anxiety?', 'Deep breathing exercises (4-7-8 technique). Regular physical activity. Adequate sleep (7-9 hours). Limit caffeine and alcohol. Practice mindfulness or meditation. Connect with others. Spend time in nature. Journal thoughts. Consider therapy if anxiety interferes with daily life. If severe or persistent, consult mental health professional.', 1, 'answered', 901, 156),
(5, 'Women\'s Health', 'What helps with menstrual cramps?', 'Apply heating pad to lower abdomen. Take over-the-counter pain relievers (ibuprofen, naproxen). Gentle exercise like walking or yoga. Stay hydrated. Avoid caffeine, salt, and sugar. Try warm baths with Epsom salts. Certain herbs like chamomile or ginger tea may help. If cramps are severe or interfere with life, see gynecologist to rule out conditions like endometriosis.', 1, 'answered', 654, 98);

-- Insert health articles
INSERT INTO health_articles (title, content, author, category, tags, views, published_date) VALUES
('Understanding COVID-19 Vaccines: What You Need to Know', 'Comprehensive guide to COVID-19 vaccines including how they work, side effects, and importance of vaccination...', 'Dr. Sarah Rahman', 'Vaccination', 'covid, vaccine, immunity', 12500, '2026-01-15'),
('Diabetes Management in Bangladesh: Diet and Lifestyle Tips', 'Practical advice for managing diabetes with traditional Bangladeshi foods and lifestyle modifications...', 'Dr. Kamal Hossain', 'Diabetes', 'diabetes, diet, bangladesh', 8900, '2026-02-10'),
('Heart Health: Preventing Cardiovascular Disease', 'Essential information about maintaining heart health, risk factors, and prevention strategies...', 'Dr. Ayesha Begum', 'Cardiovascular', 'heart, prevention, health', 7600, '2026-01-20'),
('Childhood Vaccination Schedule in Bangladesh', 'Complete guide to recommended vaccinations for children in Bangladesh, including EPI schedule...', 'Dr. Nazmul Islam', 'Pediatrics', 'vaccination, children, EPI', 15200, '2026-02-05'),
('Managing Allergies During Bangladesh Spring', 'Tips for dealing with seasonal allergies common in Bangladesh during spring and monsoon seasons...', 'Dr. Farhana Ahmed', 'Allergy', 'allergy, spring, bangladesh', 5400, '2026-03-01'),
('First Aid Guide for Common Household Accidents', 'Step-by-step first aid instructions for cuts, burns, falls, and other common accidents at home...', 'Dr. Rafiqul Islam', 'First Aid', 'first aid, safety, home', 11200, '2026-01-25'),
('Understanding Antibiotic Resistance', 'Why antibiotic resistance is a growing concern in Bangladesh and how to prevent it...', 'Dr. Shahidul Alam', 'Antibiotics', 'antibiotics, resistance, AMR', 6800, '2026-02-18'),
('Mental Health Awareness: Breaking the Stigma', 'Addressing mental health issues in Bangladeshi society and encouraging help-seeking behavior...', 'Dr. Tahmina Akter', 'Mental Health', 'mental health, stigma, awareness', 14300, '2026-02-28'),
('Nutrition Guide for Pregnant Women', 'Essential nutrition advice for pregnant women in Bangladesh, including traditional foods to include and avoid...', 'Dr. Shamima Nasrin', 'Women\'s Health', 'pregnancy, nutrition, maternal health', 9800, '2026-01-30'),
('Dengue Fever: Prevention and Treatment', 'Complete guide to dengue fever, common in Bangladesh during monsoon, including prevention and when to seek care...', 'Dr. Mahmudul Hasan', 'Infectious Disease', 'dengue, mosquito, monsoon', 21000, '2026-03-05');

-- Insert medicine interactions
INSERT INTO medicine_interactions (medicine_id_1, medicine_id_2, severity, description) VALUES
(1, 29, 'Mild', 'Paracetamol and ibuprofen can be taken together but should be staggered. Maximum doses apply to each.'),
(1, 2, 'Mild', 'Napa and Napa Extra contain paracetamol - do not take together to avoid overdose.'),
(3, 8, 'Moderate', 'Omeprazole may reduce absorption of certain medications. Space doses appropriately.'),
(4, 19, 'Moderate', 'Cephalosporins and tetracyclines may have antagonistic effects. Avoid combination unless prescribed.'),
(5, 18, 'Moderate', 'Ciprofloxacin and amoxicillin may have additive effects but should be used only when prescribed together.'),
(12, 29, 'Moderate', 'Losartan and ibuprofen may reduce antihypertensive effect and increase kidney risk. Monitor BP.'),
(16, 20, 'Severe', 'Atenolol and carvedilol are both beta-blockers - should not be combined without specialist advice.'),
(17, 25, 'Mild', 'Glibenclamide and metformin are commonly combined for diabetes, but monitor blood sugar closely.'),
(22, 30, 'Moderate', 'Sertraline and St. John\'s Wort should not be combined due to serotonin syndrome risk.'),
(23, 20, 'Moderate', 'Atorvastatin and carvedilol combination may increase statin levels. Monitor for muscle symptoms.');

-- Create indexes for performance
CREATE INDEX idx_medicines_category ON medicines(category);
CREATE INDEX idx_medicines_manufacturer ON medicines(manufacturer);
CREATE INDEX idx_medicines_price ON medicines(price);
CREATE INDEX idx_qa_status ON qa(status);
CREATE INDEX idx_qa_category ON qa(category);
CREATE INDEX idx_first_aid_category ON first_aid(category);
CREATE INDEX idx_pharmacies_city ON pharmacies(city);
CREATE INDEX idx_pharmacies_district ON pharmacies(district);

-- Create views for common queries
CREATE VIEW vw_medicine_details AS
SELECT 
    m.medicine_id,
    m.name,
    m.generic_name,
    m.category,
    m.manufacturer,
    m.country_of_origin,
    m.dosage,
    m.form,
    m.strength,
    m.price,
    m.stock,
    m.requires_prescription,
    m.description
FROM medicines m;

CREATE VIEW vw_popular_medicines AS
SELECT 
    m.name,
    m.category,
    COUNT(mr.review_id) as review_count,
    AVG(mr.rating) as avg_rating
FROM medicines m
LEFT JOIN medicine_reviews mr ON m.medicine_id = mr.medicine_id
GROUP BY m.medicine_id
HAVING COUNT(mr.review_id) > 0
ORDER BY avg_rating DESC;

CREATE VIEW vw_pharmacy_inventory AS
SELECT 
    p.name as pharmacy_name,
    p.city,
    p.phone,
    m.name as medicine_name,
    pm.price,
    pm.in_stock
FROM pharmacies p
JOIN pharmacy_medicines pm ON p.pharmacy_id = pm.pharmacy_id
JOIN medicines m ON pm.medicine_id = m.medicine_id
WHERE pm.in_stock = TRUE;

-- Insert sample medicine reviews
INSERT INTO medicine_reviews (user_id, medicine_id, rating, review, effectiveness, side_experienced) VALUES
(2, 1, 5, 'Napa works great for my headaches and fever. Always in my medicine cabinet.', 'Excellent', 'None'),
(3, 1, 4, 'Reliable paracetamol brand. Works as expected.', 'Good', 'None'),
(4, 3, 5, 'Seclo cured my acid reflux completely. Life-changer!', 'Excellent', 'Mild headache initially'),
(5, 7, 4, 'Fexo works well for my allergies without making me drowsy.', 'Good', 'None'),
(6, 13, 5, 'Alatrol is my go-to for allergies. Affordable and effective.', 'Excellent', 'Mild drowsiness'),
(7, 18, 5, 'Amoxy cleared my bacterial infection quickly.', 'Excellent', 'Mild diarrhea'),
(2, 25, 4, 'Diamet helps control my blood sugar. No major issues.', 'Good', 'Mild stomach upset initially'),
(3, 29, 5, 'Voltral is great for my joint pain. Works fast.', 'Excellent', 'None'),
(4, 31, 5, 'Calbo D helps with my calcium needs. Good supplement.', 'Good', 'None'),
(5, 34, 5, 'ORS saves me during stomach bugs. Essential in every home.', 'Excellent', 'None');

-- Create stored procedures
DELIMITER //

CREATE PROCEDURE GetMedicinesByCategory(IN cat VARCHAR(50))
BEGIN
    SELECT * FROM medicines WHERE category = cat ORDER BY name;
END //

CREATE PROCEDURE SearchMedicines(IN search_term VARCHAR(100))
BEGIN
    SELECT * FROM medicines 
    WHERE name LIKE CONCAT('%', search_term, '%') 
       OR generic_name LIKE CONCAT('%', search_term, '%')
       OR manufacturer LIKE CONCAT('%', search_term, '%')
    ORDER BY name;
END //

CREATE PROCEDURE GetPendingQuestions()
BEGIN
    SELECT q.*, u.first_name, u.last_name 
    FROM qa q
    JOIN users u ON q.user_id = u.user_id
    WHERE q.status = 'pending'
    ORDER BY q.created_at DESC;
END //

CREATE PROCEDURE GetUserMedicalHistory(IN uid INT)
BEGIN
    SELECT * FROM user_medical_records 
    WHERE user_id = uid 
    ORDER BY record_date DESC;
END //

CREATE PROCEDURE GetNearbyPharmacies(IN lat DECIMAL(10,8), IN lng DECIMAL(11,8), IN radius_km INT)
BEGIN
    SELECT *, 
        (6371 * acos(cos(radians(lat)) * cos(radians(latitude)) * cos(radians(longitude) - radians(lng)) + sin(radians(lat)) * sin(radians(latitude)))) AS distance
    FROM pharmacies
    HAVING distance < radius_km
    ORDER BY distance;
END //

DELIMITER ;

-- Create triggers
DELIMITER //

CREATE TRIGGER update_medicine_stock AFTER INSERT ON pharmacy_medicines
FOR EACH ROW
BEGIN
    UPDATE medicines 
    SET stock = stock + 1 
    WHERE medicine_id = NEW.medicine_id;
END //

CREATE TRIGGER update_qa_stats AFTER UPDATE ON qa
FOR EACH ROW
BEGIN
    IF NEW.status = 'answered' AND OLD.status = 'pending' THEN
        UPDATE qa SET answered_at = NOW() WHERE qa_id = NEW.qa_id;
    END IF;
END //

DELIMITER ;

-- Create events for scheduled tasks
DELIMITER //

CREATE EVENT IF NOT EXISTS clean_old_sessions
ON SCHEDULE EVERY 1 DAY
DO
BEGIN
    -- Cleanup old session data if using custom session table
    DELETE FROM user_sessions WHERE last_activity < DATE_SUB(NOW(), INTERVAL 7 DAY);
END //

CREATE EVENT IF NOT EXISTS update_medicine_expiry
ON SCHEDULE EVERY 1 DAY
DO
BEGIN
    UPDATE medicines 
    SET stock = 0 
    WHERE expiry_date < CURDATE() AND stock > 0;
END //

DELIMITER ;

-- Enable events
SET GLOBAL event_scheduler = ON;

-- Create user for application (optional)
CREATE USER IF NOT EXISTS 'meducare_user'@'localhost' IDENTIFIED BY 'Meducare@2026';
GRANT SELECT, INSERT, UPDATE, DELETE ON meducare_db.* TO 'meducare_user'@'localhost';
FLUSH PRIVILEGES;

-- Add some statistics queries for dashboard
CREATE TABLE site_statistics (
    stat_id INT AUTO_INCREMENT PRIMARY KEY,
    stat_date DATE,
    total_visits INT DEFAULT 0,
    total_searches INT DEFAULT 0,
    total_qa_views INT DEFAULT 0,
    total_medicine_views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert initial statistics
INSERT INTO site_statistics (stat_date, total_visits, total_searches, total_qa_views, total_medicine_views) VALUES
(CURDATE(), 15420, 3421, 5678, 7890);

-- Create fulltext indexes for search
ALTER TABLE medicines ADD FULLTEXT INDEX ft_medicine_search (name, generic_name, description);
ALTER TABLE first_aid ADD FULLTEXT INDEX ft_first_aid_search (condition_name, symptoms, first_aid_steps);
ALTER TABLE health_articles ADD FULLTEXT INDEX ft_article_search (title, content, tags);

-- Add some sample data for user medical records
INSERT INTO user_medical_records (user_id, record_date, record_type, description, doctor_name, hospital_name, diagnosis, prescription, notes) VALUES
(2, '2026-02-15', 'Checkup', 'Annual physical examination', 'Dr. Kamal Hossain', 'Square Hospital', 'Healthy, no issues', 'None', 'BP 120/80, weight normal'),
(2, '2026-01-10', 'Prescription', 'Fever and cough', 'Dr. Ayesha Begum', 'Labaid Hospital', 'Viral fever', 'Napa 500mg 3x daily for 3 days', 'Rest and fluids recommended'),
(3, '2026-02-20', 'Test', 'Blood test for thyroid', 'Dr. Farhana Ahmed', 'Popular Diagnostic', 'Normal thyroid function', 'None', 'All parameters normal'),
(4, '2026-02-25', 'Checkup', 'Diabetes follow-up', 'Dr. Shahidul Alam', 'BIRDEM Hospital', 'Type 2 diabetes, controlled', 'Diamet 500mg twice daily', 'HbA1c 6.8, good control'),
(5, '2026-03-01', 'Vaccination', 'COVID-19 booster', 'Dr. Nazmul Islam', 'Dhaka Medical College', 'Vaccination', 'None', 'Moderna booster administered'),
(6, '2026-02-05', 'Surgery', 'Appendectomy', 'Dr. Mahmudul Hasan', 'United Hospital', 'Acute appendicitis', 'Antibiotics and pain meds', 'Surgery successful, recovering well');

-- Add appointments data
INSERT INTO appointments (user_id, doctor_name, doctor_specialty, hospital_name, appointment_date, appointment_time, reason, status) VALUES
(2, 'Dr. Kamal Hossain', 'General Medicine', 'Square Hospital', '2026-03-20', '10:30:00', 'Annual checkup', 'scheduled'),
(3, 'Dr. Ayesha Begum', 'Cardiology', 'Labaid Hospital', '2026-03-22', '14:00:00', 'Heart palpitations', 'scheduled'),
(4, 'Dr. Shahidul Alam', 'Endocrinology', 'BIRDEM', '2026-03-18', '11:15:00', 'Diabetes follow-up', 'scheduled'),
(5, 'Dr. Farhana Ahmed', 'Allergy', 'Popular Diagnostic', '2026-03-25', '09:30:00', 'Allergy testing', 'scheduled'),
(2, 'Dr. Mahmudul Hasan', 'Infectious Disease', 'United Hospital', '2026-03-15', '15:45:00', 'Fever follow-up', 'completed'),
(3, 'Dr. Nazmul Islam', 'Pediatrics', 'Dhaka Medical', '2026-03-10', '12:00:00', 'Child vaccination', 'completed');

-- Add user questions
INSERT INTO user_questions (user_id, question, category, is_anonymous) VALUES
(2, 'What is the best time to take blood pressure medication?', 'Cardiovascular', FALSE),
(3, 'Can I take painkillers with my diabetes medication?', 'Diabetes', FALSE),
(4, 'How often should I get my eyes checked?', 'Eye Care', FALSE),
(5, 'Is it safe to take herbal supplements with prescription meds?', 'General Medicine', TRUE),
(6, 'What exercises are safe for back pain?', 'Musculoskeletal', FALSE),
(7, 'How to manage stress during exams?', 'Mental Health', TRUE);

-- Add final indexes for performance optimization
CREATE INDEX idx_appointments_user_date ON appointments(user_id, appointment_date);
CREATE INDEX idx_medical_records_user_date ON user_medical_records(user_id, record_date);
CREATE INDEX idx_qa_user_status ON qa(user_id, status);
CREATE INDEX idx_medicine_reviews_medicine ON medicine_reviews(medicine_id);
CREATE INDEX idx_pharmacy_medicines_medicine ON pharmacy_medicines(medicine_id);

-- Add foreign key constraints for data integrity
ALTER TABLE pharmacy_medicines 
ADD CONSTRAINT fk_pharmacy_medicines_pharmacy 
FOREIGN KEY (pharmacy_id) REFERENCES pharmacies(pharmacy_id) ON DELETE CASCADE,
ADD CONSTRAINT fk_pharmacy_medicines_medicine 
FOREIGN KEY (medicine_id) REFERENCES medicines(medicine_id) ON DELETE CASCADE;

ALTER TABLE medicine_reviews
ADD CONSTRAINT fk_medicine_reviews_user 
FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
ADD CONSTRAINT fk_medicine_reviews_medicine 
FOREIGN KEY (medicine_id) REFERENCES medicines(medicine_id) ON DELETE CASCADE;

ALTER TABLE user_medical_records
ADD CONSTRAINT fk_medical_records_user 
FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE;

ALTER TABLE appointments
ADD CONSTRAINT fk_appointments_user 
FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE;

ALTER TABLE user_questions
ADD CONSTRAINT fk_user_questions_user 
FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE;

-- Create a backup table for logging
CREATE TABLE system_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    log_type VARCHAR(50),
    log_message TEXT,
    user_id INT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_log_type (log_type),
    INDEX idx_created_at (created_at)
);

-- Insert sample log entries
INSERT INTO system_logs (log_type, log_message, user_id, ip_address) VALUES
('INFO', 'Database initialized successfully', NULL, '127.0.0.1'),
('INFO', 'Sample data inserted', NULL, '127.0.0.1');

-- Create a view for popular medicines in Bangladesh
CREATE VIEW vw_bangladesh_popular_medicines AS
SELECT 
    m.name,
    m.category,
    m.manufacturer,
    COUNT(DISTINCT pm.pharmacy_id) as available_pharmacies,
    AVG(pm.price) as avg_price,
    COUNT(DISTINCT mr.review_id) as review_count,
    AVG(mr.rating) as avg_rating
FROM medicines m
LEFT JOIN pharmacy_medicines pm ON m.medicine_id = pm.medicine_id
LEFT JOIN medicine_reviews mr ON m.medicine_id = mr.medicine_id
WHERE m.country_of_origin = 'Bangladesh' OR m.manufacturer IN (
    'Square Pharmaceuticals Ltd.',
    'Beximco Pharmaceuticals Ltd.',
    'Incepta Pharmaceuticals Ltd.',
    'Renata Limited',
    'Acme Laboratories Ltd.'
)
GROUP BY m.medicine_id
HAVING available_pharmacies > 0
ORDER BY avg_rating DESC, review_count DESC;

-- Create a stored procedure for emergency medicine search
DELIMITER //

CREATE PROCEDURE FindEmergencyMedicine(
    IN medicine_name VARCHAR(100),
    IN user_lat DECIMAL(10,8),
    IN user_lng DECIMAL(11,8)
)
BEGIN
    SELECT 
        m.name,
        m.generic_name,
        p.name as pharmacy_name,
        p.address,
        p.city,
        p.phone,
        p.emergency_phone,
        p.opening_hours,
        pm.price,
        (6371 * acos(cos(radians(user_lat)) * cos(radians(p.latitude)) * cos(radians(p.longitude) - radians(user_lng)) + sin(radians(user_lat)) * sin(radians(p.latitude)))) AS distance
    FROM medicines m
    JOIN pharmacy_medicines pm ON m.medicine_id = pm.medicine_id
    JOIN pharmacies p ON pm.pharmacy_id = p.pharmacy_id
    WHERE m.name LIKE CONCAT('%', medicine_name, '%')
        AND pm.in_stock = TRUE
    ORDER BY distance
    LIMIT 10;
END //

DELIMITER ;

-- Add comments for documentation
COMMENT ON TABLE medicines IS 'Complete medicine database including Bangladeshi pharmaceuticals';
COMMENT ON TABLE first_aid IS 'First aid information for common conditions';
COMMENT ON TABLE medical_history IS 'Educational content about medical discoveries';
COMMENT ON TABLE pharmacies IS 'Bangladeshi pharmacy directory with location data';

-- Final message
SELECT 'Meducare database successfully created with Bangladeshi medicine data!' as 'Status';

-- Show counts for verification
SELECT 'Users' as 'Table', COUNT(*) as 'Count' FROM users
UNION ALL
SELECT 'Medicines', COUNT(*) FROM medicines
UNION ALL
SELECT 'First Aid', COUNT(*) FROM first_aid
UNION ALL
SELECT 'Medical History', COUNT(*) FROM medical_history
UNION ALL
SELECT 'Pharmacies', COUNT(*) FROM pharmacies
UNION ALL
SELECT 'Q&A', COUNT(*) FROM qa;