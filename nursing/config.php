<?php
/**
 * LifeCare Nursing & Medical Services - Configuration & Helper File
 * Supports dual persistence: MySQL Database (phpMyAdmin) & JSON Storage Fallback
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();

// Site Configurations
define('SITE_NAME', 'LifeCare Nursing & Medical Services');
define('CONTACT_EMAIL', 'lifecarenursing5@gmail.com');

// Default Admin Credentials
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD_HASH', '$2y$10$8v5p.aO8H3Llh9L8dGZ.eu1Dk0.x3J8Xb8iF0JpM5c4c9j7PqL2OS'); // 'admin123'

// MySQL Database Settings (For phpMyAdmin / XAMPP)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'lifecare_db');

// File Paths
define('BASE_DIR', __DIR__);
define('DATA_DIR', BASE_DIR . '/data/');
define('UPLOADS_DIR', BASE_DIR . '/uploads/');
define('UPLOADS_URL', 'uploads/');

// Ensure directories exist
if (!file_exists(DATA_DIR)) {
    mkdir(DATA_DIR, 0777, true);
}
if (!file_exists(UPLOADS_DIR)) {
    mkdir(UPLOADS_DIR, 0777, true);
}

/**
 * Get PDO Database Connection (Returns PDO object or null if MySQL not configured)
 */
function getDBConnection() {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (Exception $e) {
        // Fallback to JSON file mode if MySQL connection fails
        return null;
    }
}

/**
 * Read data from MySQL Database or JSON storage file
 */
function getData($type) {
    $db = getDBConnection();
    if ($db) {
        try {
            $allowedTables = ['doctors', 'staff', 'team', 'blogs', 'contact_messages'];
            if (in_array($type, $allowedTables)) {
                $stmt = $db->query("SELECT * FROM `{$type}` ORDER BY id DESC");
                $data = $stmt->fetchAll();
                if (!empty($data)) {
                    foreach ($data as &$item) {
                        if ($type === 'doctors') {
                            if (empty($item['speciality'])) {
                                $item['speciality'] = $item['role'] ?? 'General Physician';
                            }
                        }
                        if (empty($item['status'])) {
                            $item['status'] = 'approved';
                        }
                    }
                    return $data;
                }
            }
        } catch (Exception $e) {
            // Fallback to JSON if table missing
        }
    }

    // JSON Storage Fallback
    $filePath = DATA_DIR . $type . '.json';
    if (!file_exists($filePath)) {
        return getInitialData($type);
    }
    $content = file_get_contents($filePath);
    $data = json_decode($content, true);
    if (is_array($data)) {
        foreach ($data as &$item) {
            if ($type === 'doctors') {
                if (empty($item['speciality'])) {
                    $item['speciality'] = $item['role'] ?? 'General Physician';
                }
            }
            if (empty($item['status'])) {
                $item['status'] = 'approved';
            }
        }
        return $data;
    }
    return [];
}

/**
 * Save data to MySQL Database or JSON storage file
 */
function saveData($type, $data) {
    $db = getDBConnection();
    if ($db) {
        try {
            // If saving whole array to MySQL table
            $allowedTables = ['doctors', 'staff', 'team', 'blogs', 'contact_messages'];
            if (in_array($type, $allowedTables)) {
                if ($type === 'doctors') {
                    $db->exec("CREATE TABLE IF NOT EXISTS `doctors` (
                        `id` INT AUTO_INCREMENT PRIMARY KEY,
                        `username` VARCHAR(100) NULL,
                        `password` VARCHAR(255) NULL,
                        `status` VARCHAR(20) DEFAULT 'approved',
                        `name` VARCHAR(255) NOT NULL,
                        `speciality` VARCHAR(255) NULL,
                        `role` VARCHAR(255) NULL,
                        `qualifications` VARCHAR(255) NULL,
                        `badge` VARCHAR(255) NULL,
                        `experience` VARCHAR(100) NULL,
                        `waitTime` VARCHAR(100) NULL,
                        `satisfaction` VARCHAR(100) NULL,
                        `hospitalName` VARCHAR(255) NULL,
                        `hospitalFee` VARCHAR(100) NULL,
                        `hospitalSchedule` VARCHAR(255) NULL,
                        `videoFee` VARCHAR(100) NULL,
                        `videoSchedule` VARCHAR(255) NULL,
                        `image` VARCHAR(255) NULL,
                        `description` TEXT NULL,
                        `aboutBio` TEXT NULL,
                        `whatsapp` VARCHAR(50) NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

                    $existingCols = [];
                    $colQuery = $db->query("SHOW COLUMNS FROM `doctors`");
                    if ($colQuery) {
                        foreach ($colQuery->fetchAll() as $c) {
                            $existingCols[] = $c['Field'];
                        }
                    }

                    $colsToAdd = [
                        'username' => 'VARCHAR(100) NULL',
                        'password' => 'VARCHAR(255) NULL',
                        'status' => "VARCHAR(20) DEFAULT 'approved'",
                        'speciality' => 'VARCHAR(255) NULL',
                        'qualifications' => 'VARCHAR(255) NULL',
                        'experience' => 'VARCHAR(100) NULL',
                        'waitTime' => 'VARCHAR(100) NULL',
                        'satisfaction' => 'VARCHAR(100) NULL',
                        'hospitalName' => 'VARCHAR(255) NULL',
                        'hospitalFee' => 'VARCHAR(100) NULL',
                        'hospitalSchedule' => 'VARCHAR(255) NULL',
                        'videoFee' => 'VARCHAR(100) NULL',
                        'videoSchedule' => 'VARCHAR(255) NULL',
                        'aboutBio' => 'TEXT NULL'
                    ];

                    foreach ($colsToAdd as $colName => $colDef) {
                        if (!in_array($colName, $existingCols)) {
                            try {
                                $db->exec("ALTER TABLE `doctors` ADD COLUMN `{$colName}` {$colDef}");
                            } catch (Exception $e) {}
                        }
                    }

                    $db->exec("TRUNCATE TABLE `doctors`");
                    foreach ($data as $item) {
                        $stmt = $db->prepare("INSERT INTO `doctors` (id, username, password, status, name, speciality, role, qualifications, badge, experience, waitTime, satisfaction, hospitalName, hospitalFee, hospitalSchedule, videoFee, videoSchedule, image, description, aboutBio, whatsapp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([
                            $item['id'] ?? null,
                            $item['username'] ?? null,
                            $item['password'] ?? null,
                            $item['status'] ?? 'approved',
                            $item['name'] ?? '',
                            $item['speciality'] ?? ($item['role'] ?? ''),
                            $item['role'] ?? ($item['speciality'] ?? ''),
                            $item['qualifications'] ?? 'MBBS, FCPS',
                            $item['badge'] ?? 'PMC Verified',
                            $item['experience'] ?? '10 Years',
                            $item['waitTime'] ?? 'Under 15 Mins',
                            $item['satisfaction'] ?? '97%',
                            $item['hospitalName'] ?? 'LifeCare Clinical Center',
                            $item['hospitalFee'] ?? '1500',
                            $item['hospitalSchedule'] ?? 'Mon - Sat: 02:00 PM - 05:00 PM',
                            $item['videoFee'] ?? '1000',
                            $item['videoSchedule'] ?? 'Mon - Sun: 09:00 AM - 04:00 PM',
                            $item['image'] ?? 'assets/doctor_1.jpg',
                            $item['description'] ?? '',
                            $item['aboutBio'] ?? '',
                            $item['whatsapp'] ?? '923008053198'
                        ]);
                    }
                } elseif ($type === 'staff') {
                    $db->exec("CREATE TABLE IF NOT EXISTS `staff` (
                        `id` INT AUTO_INCREMENT PRIMARY KEY,
                        `username` VARCHAR(100) NULL,
                        `password` VARCHAR(255) NULL,
                        `status` VARCHAR(20) DEFAULT 'approved',
                        `name` VARCHAR(255) NOT NULL,
                        `role` VARCHAR(255) NOT NULL,
                        `city` VARCHAR(100) NULL,
                        `badge` VARCHAR(100) NULL,
                        `shift` VARCHAR(100) NULL,
                        `rate` VARCHAR(100) NULL,
                        `skills` TEXT NULL,
                        `image` VARCHAR(255) NULL,
                        `description` TEXT NULL,
                        `whatsapp` VARCHAR(50) NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

                    $existingCols = [];
                    $colQuery = $db->query("SHOW COLUMNS FROM `staff`");
                    if ($colQuery) {
                        foreach ($colQuery->fetchAll() as $c) {
                            $existingCols[] = $c['Field'];
                        }
                    }

                    $colsToAdd = [
                        'username' => 'VARCHAR(100) NULL',
                        'password' => 'VARCHAR(255) NULL',
                        'status' => "VARCHAR(20) DEFAULT 'approved'",
                        'city' => 'VARCHAR(100) NULL',
                        'shift' => 'VARCHAR(100) NULL',
                        'rate' => 'VARCHAR(100) NULL',
                        'skills' => 'TEXT NULL'
                    ];

                    foreach ($colsToAdd as $colName => $colDef) {
                        if (!in_array($colName, $existingCols)) {
                            try {
                                $db->exec("ALTER TABLE `staff` ADD COLUMN `{$colName}` {$colDef}");
                            } catch (Exception $e) {}
                        }
                    }

                    $db->exec("TRUNCATE TABLE `staff`");
                    foreach ($data as $item) {
                        $skillsStr = is_array($item['skills'] ?? null) ? implode(',', $item['skills']) : ($item['skills'] ?? '');
                        $stmt = $db->prepare("INSERT INTO `staff` (id, username, password, status, name, role, city, badge, shift, rate, skills, image, description, whatsapp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([
                            $item['id'] ?? null,
                            $item['username'] ?? null,
                            $item['password'] ?? null,
                            $item['status'] ?? 'approved',
                            $item['name'] ?? '',
                            $item['role'] ?? '',
                            $item['city'] ?? 'Faisalabad',
                            $item['badge'] ?? 'Registered Nurse',
                            $item['shift'] ?? '12-Hour Shift',
                            $item['rate'] ?? 'Rs. 2,200 / Day',
                            $skillsStr,
                            $item['image'] ?? 'assets/staff_1.jpg',
                            $item['description'] ?? '',
                            $item['whatsapp'] ?? '923008053198'
                        ]);
                    }
                } else {
                    $db->exec("TRUNCATE TABLE `{$type}`");
                    foreach ($data as $item) {
                        if ($type === 'contact_messages') {
                            $stmt = $db->prepare("INSERT INTO contact_messages (name, phone, email, service, message) VALUES (?, ?, ?, ?, ?)");
                            $stmt->execute([$item['name'] ?? '', $item['phone'] ?? '', $item['email'] ?? '', $item['service'] ?? '', $item['message'] ?? '']);
                        } elseif ($type === 'blogs') {
                            $stmt = $db->prepare("INSERT INTO blogs (id, title, category, date, author, image, excerpt, content) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                            $stmt->execute([
                                $item['id'] ?? null,
                                $item['title'] ?? '',
                                $item['category'] ?? 'Health',
                                $item['date'] ?? date('d M Y'),
                                $item['author'] ?? 'LifeCare Team',
                                $item['image'] ?? '',
                                $item['excerpt'] ?? '',
                                $item['content'] ?? ''
                            ]);
                        } else {
                            $stmt = $db->prepare("INSERT INTO `{$type}` (id, name, role, badge, image, description, whatsapp) VALUES (?, ?, ?, ?, ?, ?, ?)");
                            $stmt->execute([
                                $item['id'] ?? null,
                                $item['name'] ?? '',
                                $item['role'] ?? '',
                                $item['badge'] ?? '',
                                $item['image'] ?? '',
                                $item['description'] ?? '',
                                $item['whatsapp'] ?? '923008053198'
                            ]);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            // Ignore DB error, proceed to sync JSON file
        }
    }

    // Always keep JSON synchronized
    $filePath = DATA_DIR . $type . '.json';
    return file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/**
 * Upload Image Helper
 */
function uploadImage($file) {
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($fileExt, $allowedExts)) {
        return null;
    }

    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME)) . '.' . $fileExt;
    $targetPath = UPLOADS_DIR . $filename;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return UPLOADS_URL . $filename;
    }

    return null;
}

/**
 * Admin Auth Checkers
 */
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Doctor Auth Checkers & Helpers
 */
function isDoctorLoggedIn() {
    return isset($_SESSION['doctor_id']) && (int)$_SESSION['doctor_id'] > 0;
}

function requireDoctorLogin() {
    if (!isDoctorLoggedIn()) {
        header('Location: doctor-login.php');
        exit;
    }
}

function getLoggedInDoctor() {
    if (!isDoctorLoggedIn()) return null;
    $doctors = getData('doctors');
    $docId = (int)$_SESSION['doctor_id'];
    foreach ($doctors as $d) {
        if ((int)$d['id'] === $docId) {
            return $d;
        }
    }
    return null;
}

function getDoctorByUsername($username) {
    $doctors = getData('doctors');
    $usernameClean = strtolower(trim($username));
    foreach ($doctors as $d) {
        if (isset($d['username']) && strtolower(trim($d['username'])) === $usernameClean) {
            return $d;
        }
    }
    return null;
}

/**
 * Staff Auth Checkers & Helpers
 */
function isStaffLoggedIn() {
    return isset($_SESSION['staff_id']) && (int)$_SESSION['staff_id'] > 0;
}

function requireStaffLogin() {
    if (!isStaffLoggedIn()) {
        header('Location: staff-login.php');
        exit;
    }
}

function getLoggedInStaff() {
    if (!isStaffLoggedIn()) return null;
    $staff = getData('staff');
    $staffId = (int)$_SESSION['staff_id'];
    foreach ($staff as $s) {
        if ((int)$s['id'] === $staffId) {
            return $s;
        }
    }
    return null;
}

function getStaffByUsername($username) {
    $staff = getData('staff');
    $usernameClean = strtolower(trim($username));
    foreach ($staff as $s) {
        if (isset($s['username']) && strtolower(trim($s['username'])) === $usernameClean) {
            return $s;
        }
    }
    return null;
}

/**
 * Initial Default Data Seeder
 */
function getInitialData($type) {
    $defaults = [
        'doctors' => [
            [
                'id' => 1,
                'name' => 'Dr. Arthur Pendleton',
                'role' => 'Senior Medical Consultant',
                'badge' => 'Internal Medicine',
                'image' => 'assets/doctor_1.jpg',
                'description' => 'Provides consultations for general health concerns, ongoing medical conditions and health needs of older adults.',
                'whatsapp' => '923008053198'
            ],
            [
                'id' => 2,
                'name' => 'Dr. Sarah Jenkins',
                'role' => 'Medical Consultant',
                'badge' => 'Home Care',
                'image' => 'assets/doctor_2.jpg',
                'description' => 'Provides consultations for routine check-ups, recovery after surgery and health concerns that can be managed at home.',
                'whatsapp' => '923008053198'
            ],
            [
                'id' => 3,
                'name' => 'Dr. Hamza Tariq',
                'role' => 'Cardiology Consultant',
                'badge' => 'Heart Care',
                'image' => 'assets/bp-check.jpg',
                'description' => 'Provides consultations for blood pressure, heart-related concerns, ECG review and follow-up care.',
                'whatsapp' => '923008053198'
            ],
            [
                'id' => 4,
                'name' => 'Dr. Ayesha Malik',
                'role' => 'Physiotherapy Consultant',
                'badge' => 'Rehabilitation',
                'image' => 'assets/home-care-facility.jpg',
                'description' => 'Provides rehabilitation support for recovery after stroke, joint movement and common mobility problems.',
                'whatsapp' => '923008053198'
            ]
        ],
        'staff' => [
            [
                'id' => 1,
                'name' => 'James N.',
                'role' => 'Senior Registered Nurse',
                'badge' => 'ICU Nurse',
                'image' => 'assets/staff_1.jpg',
                'description' => '8+ years experience in ICU nursing, ventilator care, tracheostomy support, and IV medication administration.',
                'whatsapp' => '923008053198'
            ],
            [
                'id' => 2,
                'name' => 'Maria K.',
                'role' => 'Elderly Care Specialist',
                'badge' => 'Senior Caregiver',
                'image' => 'assets/staff_2.jpg',
                'description' => 'Compassionate caregiver specializing in elderly assistance, dementia support, and daily personal care routines.',
                'whatsapp' => '923008053198'
            ],
            [
                'id' => 3,
                'name' => 'Tariq Mahmood',
                'role' => 'Home Physiotherapist',
                'badge' => 'Physiotherapist',
                'image' => 'assets/why-choose-us.jpg',
                'description' => 'Expert physical trainer for post-stroke mobility rehabilitation, muscle strengthening, and gait training.',
                'whatsapp' => '923008053198'
            ],
            [
                'id' => 4,
                'name' => 'Fatima Zahra',
                'role' => 'Clinical Assistant',
                'badge' => 'Patient Care Attendant',
                'image' => 'assets/who-we-are.jpg',
                'description' => 'Trained healthcare assistant providing round-the-clock bedside care, vitals tracking, and patient hygiene support.',
                'whatsapp' => '923008053198'
            ]
        ],
        'team' => [
            [
                'id' => 1,
                'name' => 'Dr. Haris Abbasi',
                'role' => 'Medical Director',
                'badge' => 'Management',
                'image' => 'assets/doctor_1.jpg',
                'description' => 'Oversees clinical operations, patient care quality, and doctor panel coordination.',
                'whatsapp' => '923008053198'
            ],
            [
                'id' => 2,
                'name' => 'Zainab Bibi',
                'role' => 'Nursing Superintendent',
                'badge' => 'Staff Lead',
                'image' => 'assets/staff_2.jpg',
                'description' => 'Head of nursing services, staff assignment, and emergency home care deployments.',
                'whatsapp' => '923008053198'
            ]
        ]
    ];

    $initial = isset($defaults[$type]) ? $defaults[$type] : [];
    saveData($type, $initial);
    return $initial;
}
