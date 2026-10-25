<?php

/*
|-------------------------------
| DATABASE CONFIGURATION
|-------------------------------
*/

$host = "localhost";
$user = "root";
$password = "";
$database = "face_attendance_system";

/*
|------------------------------------
| CREATE DATABASE CONNECTION
|------------------------------------
*/

$conn = mysqli_connect($host, $user, $password);

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

/*
|--------------------------------
| CREATE DATABASE
|--------------------------------
*/

$sql = "CREATE DATABASE IF NOT EXISTS $database";

if (mysqli_query($conn, $sql)) {
    // echo "Database created successfully<br>";
} else {
    // echo "Error creating database: " . mysqli_error($conn);
}

/*
|-------------------------------
| SELECT DATABASE
|-------------------------------
*/

mysqli_select_db($conn, $database);

/*
|-----------------------------------
| ROLES TABLE
|-----------------------------------
*/

$roles = "
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

mysqli_query($conn, $roles);

/*
|----------------------------
| USERS TABLE
|-----------------------------
*/

$users = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,

    role_id INT NOT NULL,

    first_name VARCHAR(100) NULL,
    last_name VARCHAR(100) NULL,
    gender ENUM('Male','Female') NULL,
    date_of_birth DATE NULL,
    address TEXT NULL,
    bio TEXT NULL,

    username VARCHAR(100) NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    phone VARCHAR(20),

    profile_photo VARCHAR(255) NULL,

    password VARCHAR(255) NOT NULL,

    is_active BOOLEAN DEFAULT TRUE,

    last_login DATETIME,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (role_id) REFERENCES roles(id)
)";

mysqli_query($conn, $users);




/*
|---------------------------
| DEPARTMENTS TABLE
|---------------------------
*/

$departments = "
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,

    department_name VARCHAR(100) NOT NULL,
    department_code VARCHAR(20) UNIQUE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

mysqli_query($conn, $departments);

/*
|----------------------------
| CLASSES TABLE
|----------------------------
*/

$classes = "
CREATE TABLE IF NOT EXISTS classes (
    id INT AUTO_INCREMENT PRIMARY KEY,

    department_id INT,

    class_name VARCHAR(100) NOT NULL,
    section VARCHAR(20),

    academic_year VARCHAR(20),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (department_id)
    REFERENCES departments(id)
)";

mysqli_query($conn, $classes);

/*
|---------------------------------
| STUDENTS TABLE
|---------------------------------
*/

$students = "
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT UNIQUE,

    student_code VARCHAR(50) UNIQUE NOT NULL,

    first_name VARCHAR(100),
    last_name VARCHAR(100),

    gender ENUM('Male','Female'),

    date_of_birth DATE,

    address TEXT,

    photo VARCHAR(255),

    department_id INT,
    class_id INT,

    enrollment_year YEAR,

    status ENUM('Active','Inactive','Graduated')
    DEFAULT 'Active',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE,

    FOREIGN KEY (department_id)
    REFERENCES departments(id),

    FOREIGN KEY (class_id)
    REFERENCES classes(id)
)";

mysqli_query($conn, $students);

/*
|---------------------------------
| INSTRUCTORS TABLE
|---------------------------------
*/

$instructors = "
CREATE TABLE IF NOT EXISTS instructors (
    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT UNIQUE,

    instructor_code VARCHAR(50) UNIQUE,

    first_name VARCHAR(100),
    last_name VARCHAR(100),

    phone VARCHAR(20),

    department_id INT,

    specialization VARCHAR(100),

    photo VARCHAR(255),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE,

    FOREIGN KEY (department_id)
    REFERENCES departments(id)
)";

mysqli_query($conn, $instructors);

/*
|---------------------------------
| SUBJECTS TABLE
|---------------------------------
*/

$subjects = "
CREATE TABLE IF NOT EXISTS subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,

    subject_name VARCHAR(100) NOT NULL,
    subject_code VARCHAR(50) UNIQUE,

    credit_hour INT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

mysqli_query($conn, $subjects);

/*
|-----------------------------------
| CLASS SUBJECTS TABLE
|-----------------------------------
*/

$classSubjects = "
CREATE TABLE IF NOT EXISTS class_subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,

    class_id INT,
    subject_id INT,
    instructor_id INT,

    semester VARCHAR(20),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (class_id)
    REFERENCES classes(id),

    FOREIGN KEY (subject_id)
    REFERENCES subjects(id),

    FOREIGN KEY (instructor_id)
    REFERENCES instructors(id)
    ON DELETE CASCADE

)";

mysqli_query($conn, $classSubjects);

/*
|--------------------------------
| ENROLLMENTS TABLE
|--------------------------------
*/

$enrollments = "
CREATE TABLE IF NOT EXISTS enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,

    student_id INT,
    class_id INT,

    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (student_id)
    REFERENCES students(id)
    ON DELETE CASCADE,

    FOREIGN KEY (class_id)
    REFERENCES classes(id)
)";

mysqli_query($conn, $enrollments);

/*
|----------------------------------
| FACE DATA TABLE
|----------------------------------
*/

$faceData = "
CREATE TABLE IF NOT EXISTS face_data (
    id INT AUTO_INCREMENT PRIMARY KEY,

    student_id INT,

    capture_type VARCHAR(50),

    face_descriptor LONGTEXT NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (student_id)
    REFERENCES students(id)
    ON DELETE CASCADE


)";

mysqli_query($conn, $faceData);


/*
|----------------------------------------
| ATTENDANCE SESSIONS TABLE
|----------------------------------------
*/

$attendanceSessions = "
CREATE TABLE IF NOT EXISTS attendance_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_subject_id INT,
    session_name VARCHAR(100),
    session_date DATE,
    start_time TIME,
    end_time TIME,
    room VARCHAR(50),
    created_by INT,
    status ENUM('Open','Closed') DEFAULT 'Open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (class_subject_id)
    REFERENCES class_subjects(id)
    ON DELETE CASCADE,


    FOREIGN KEY (created_by)
    REFERENCES users(id)
)";

mysqli_query($conn, $attendanceSessions);

/*
|---------------------------------------
| ATTENDANCE TABLE
|---------------------------------------
*/

$attendance = "
CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,

    attendance_session_id INT,

    student_id INT,

    attendance_time DATETIME,

    status ENUM(
        'Present',
        'Absent',
        'Late',
        'Excused'
    ) DEFAULT 'Present',

    recognition_confidence DECIMAL(5,2),

    attendance_method ENUM(
        'Face Recognition',
        'Manual'
    ) DEFAULT 'Face Recognition',

    notes TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (attendance_session_id)
    REFERENCES attendance_sessions(id)
    ON DELETE CASCADE,

    FOREIGN KEY (student_id)
    REFERENCES students(id),

    UNIQUE(attendance_session_id, student_id)
)";

mysqli_query($conn, $attendance);



/*
|----------------------------------------
| SYSTEM LOGS TABLE
|----------------------------------------
*/

$systemLogs = "
CREATE TABLE IF NOT EXISTS system_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT,

    action_type VARCHAR(100),

    description TEXT,

    ip_address VARCHAR(100),

    user_agent TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE

)";

mysqli_query($conn, $systemLogs);










/*
|----------------------
| CAROUSEL TABLE
|----------------------
*/

$carousels = "
CREATE TABLE IF NOT EXISTS carousel(

    id INT AUTO_INCREMENT PRIMARY KEY,

    title VARCHAR(255) UNIQUE NOT NULL,

    description TEXT,

    image VARCHAR(255) NOT NULL,

    button_text VARCHAR(100),

    button_link VARCHAR(255),

    status ENUM('active','inactive') DEFAULT 'active',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

)";

mysqli_query($conn, $carousels);

/*
|------------------------
| OUR SERVICES TABLE
|------------------------
*/

$service = "
CREATE TABLE IF NOT EXISTS services(

    id INT AUTO_INCREMENT PRIMARY KEY,

    service_name VARCHAR(150) UNIQUE NOT NULL,

    description TEXT,

    icon VARCHAR(100),

    image VARCHAR(255),

    status ENUM('active','inactive') DEFAULT 'active',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

)";
mysqli_query($conn, $service);



/*
|-----------------------
| HOW IT WORKS TABLE
|-----------------------
*/

$howItWorks = "
CREATE TABLE IF NOT EXISTS how_it_works(

    id INT AUTO_INCREMENT PRIMARY KEY,

    step_number INT NOT NULL,

    title VARCHAR(150) UNIQUE NOT NULL,

    description TEXT,

    icon VARCHAR(100),

    image VARCHAR(255),

    status ENUM('active','inactive') DEFAULT 'active',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

)";
mysqli_query($conn, $howItWorks);



/*
|------------------------
| TESTIMONIALS TABLE
|------------------------
*/

$testimonial = "
CREATE TABLE IF NOT EXISTS testimonials(

    id INT AUTO_INCREMENT PRIMARY KEY,

    full_name VARCHAR(150) UNIQUE NOT NULL,

    profession VARCHAR(150),

    message TEXT NOT NULL,

    photo VARCHAR(255),

    rating INT DEFAULT 5,

    status ENUM('active','inactive') DEFAULT 'active',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

)";
mysqli_query($conn, $testimonial);



/*
|--------------------------
| ABOUT TABLE
|--------------------------
*/

$aboutUs = "
CREATE TABLE IF NOT EXISTS about(

    id INT AUTO_INCREMENT PRIMARY KEY,

    title VARCHAR(255) UNIQUE NOT NULL,

    description LONGTEXT NOT NULL,

    image VARCHAR(255),

    mission TEXT,

    vision TEXT,

    status ENUM('active','inactive') DEFAULT 'active',

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP

)";
mysqli_query($conn, $aboutUs);



/*
|---------------------------
| CONTACT TABLE
|---------------------------
*/

$contactInfo = "
CREATE TABLE IF NOT EXISTS contact_info(

    id INT AUTO_INCREMENT PRIMARY KEY,

    office_address VARCHAR(255),

    email VARCHAR(150) UNIQUE,

    phone VARCHAR(50),

    working_days VARCHAR(150),

    working_hours VARCHAR(150),

    facebook_link VARCHAR(255),

    telegram_link VARCHAR(255),

    linkedin_link VARCHAR(255),

    twitter_link VARCHAR(255),

    map_iframe LONGTEXT,

    status ENUM('active','inactive') DEFAULT 'active',

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP

)";
mysqli_query($conn, $contactInfo);



/*
|--------------------------------
| CONTACT MESSAGES TABLE
|--------------------------------
*/

$contactMsg = "
CREATE TABLE IF NOT EXISTS contact_messages(

    id INT AUTO_INCREMENT PRIMARY KEY,

    full_name VARCHAR(150) NOT NULL,

    email VARCHAR(150) UNIQUE NOT NULL,

    subject VARCHAR(255),

    message TEXT NOT NULL,

    status ENUM('new','read','replied') DEFAULT 'new',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

)";
mysqli_query($conn, $contactMsg);









/*
|---------------------------------
| INSERT DEFAULT ROLES
|---------------------------------
*/

$insertRoles = "
INSERT IGNORE INTO roles(role_name)
VALUES
('admin'),
('instructor'),
('student')
";

mysqli_query($conn, $insertRoles);








/*
|---------------------------
| INSERT CAROUSEL DATA
|---------------------------
*/

$insertCarousel = "
INSERT IGNORE INTO carousel(title, description, image)
VALUES
('Smart Attendance System',
 'Automated attendance management system',
 'carousel1.jpg'),

('Face Recognition Attendance',
 'AI powered attendance tracking',
 'carousel2.jpg'),

('Real Time Reports',
 'Generate attendance analytics instantly',
 'carousel3.jpg')";

mysqli_query($conn, $insertCarousel);



/*
|----------------------
| INSERT SERVICES
|----------------------
*/

$insertServices = "
INSERT IGNORE INTO services(service_name, description, icon)
VALUES
('Face Recognition',
 'Automatic attendance using face recognition',
 'bi-camera-video'),

('Attendance Reports',
 'Generate detailed attendance reports',
 'bi-bar-chart'),

('Student Management',
 'Manage students and attendance records',
 'bi-people'),

('Secure Login',
 'Protected user authentication system',
 'bi-shield-lock')";

mysqli_query($conn, $insertServices);


/*
|--------------------------
| INSERT HOW IT WORKS
|--------------------------
*/

$insertHowItWorks = "
INSERT IGNORE INTO how_it_works(step_number, title, description, icon)
VALUES
(1,
 'Register Students',
 'Add students and capture face data',
 'bi-person-plus'),

(2,
 'Detect Faces',
 'System detects faces automatically',
 'bi-camera'),

(3,
 'Mark Attendance',
 'Attendance saved instantly',
 'bi-check-circle')";
mysqli_query($conn, $insertHowItWorks);



/*
|-----------------------------
| INSERT TESTIMONIALS
|-----------------------------
*/

$insertTestimonals = "
INSERT IGNORE INTO testimonials(full_name, profession, message, photo)
VALUES
('Daniel',
 'Instructor',
 'This system made attendance management easier.',
 'testimonial1.jpg'),

('Selam',
 'Administrator',
 'Reports are fast and accurate.',
 'testimonial2.jpg')";
mysqli_query($conn, $insertTestimonals);



/*
|--------------------------
| INSERT ABOUT DATA
|--------------------------
*/

$insertAbout = "
INSERT IGNORE INTO about(title, description, image, mission, vision)
VALUES
(
 'About Smart Attendance System',

 'Smart Attendance System is a modern web-based attendance management platform designed for schools, colleges and universities.',

 'about.jpg',

 'To simplify attendance management using modern technology.',

 'To become the leading digital attendance platform.'
)";
mysqli_query($conn, $insertAbout);



/*
|--------------------------------
| INSERT CONTACT INFO
|--------------------------------
*/

$insertContactInfo = "
INSERT IGNORE INTO contact_info(
office_address,
email,
phone,
working_days,
working_hours
)

VALUES
(
 'Arbaminch, Gamo, Ethiopia',
 'student@attendance.com',
 '+251900000000',
 'Monday - Friday',
 '8:00AM - 5:00PM'
)";
mysqli_query($conn, $insertContactInfo);


/*
|--------------------------
| CREATE INDEXES
|--------------------------
*/


$checkIndex = mysqli_query($conn,
"SHOW INDEX FROM students
 WHERE Key_name = 'idx_student_code'");

if(mysqli_num_rows($checkIndex) == 0){

    mysqli_query($conn,
    "CREATE INDEX idx_student_code
     ON students(student_code)");
}


$checkIndex = mysqli_query($conn,
"SHOW INDEX FROM attendance
 WHERE Key_name = 'idx_attendance_date'");

if(mysqli_num_rows($checkIndex) == 0){

    mysqli_query($conn,
    "CREATE INDEX idx_attendance_date
     ON attendance(attendance_time)");

}



$checkIndex = mysqli_query($conn,
"SHOW INDEX FROM face_data
 WHERE Key_name = 'idx_face_student'");

if(mysqli_num_rows($checkIndex) == 0){

    mysqli_query($conn,
    "CREATE INDEX idx_face_student
     ON face_data(student_id)");

}



// echo "<h2>All Database Tables Created Successfully</h2>";




/*
|----------------------------------------
| AUTO DELETE OLD LOGS (1 MONTH)
|----------------------------------------
*/

mysqli_query($conn,

"DELETE FROM system_logs
WHERE created_at < NOW() - INTERVAL 1 MONTH"

);



// mysqli_close($conn);





?>
