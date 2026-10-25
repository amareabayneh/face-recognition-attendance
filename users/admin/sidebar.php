<?php

$current_path = $_SERVER['PHP_SELF'];

function active($path){

    global $current_path;

    return strpos($current_path, $path) !== false
           ? 'active'
           : '';
}
?>

<!-- SIDEBAR -->
<div class="sidebar">

    <h4 class="text-center mt-3">
        <span class="text-yellow">Admin</span> Panel
    </h4>

    <!-- DASHBOARD -->
    <a href="dashboard.php"
       class="nav-link <?= active('/dashboard.php') ?>">

        <i class="bi bi-speedometer2 me-2"></i>
        <span>Dashboard</span>

    </a>

    <!-- USERS -->
    <a href="manage_users.php"
       class="nav-link <?= active('/manage_users.php') ?>">

        <i class="bi bi-person-gear me-2"></i>
        <span>Manage Users</span>

    </a>

    <!-- STUDENTS -->
    <a href="manage_students.php"
       class="nav-link <?= active('/manage_students.php') ?>">

        <i class="bi bi-mortarboard me-2"></i>
        <span>Manage Students</span>

    </a>

    <!-- INSTRUCTORS -->
    <a href="manage_instructors.php"
       class="nav-link <?= active('/manage_instructors.php') ?>">

        <i class="bi bi-person-workspace me-2"></i>
        <span>Manage Instructors</span>

    </a>

    <!-- DEPARTMENTS -->
    <a href="manage_departments.php"
       class="nav-link <?= active('/manage_departments.php') ?>">

        <i class="bi bi-building me-2"></i>
        <span>Manage Departments</span>

    </a>

    <!-- CLASSES -->
    <a href="manage_classes.php"
       class="nav-link <?= active('/manage_classes.php') ?>">

        <i class="bi bi-easel2 me-2"></i>
        <span>Manage Classes</span>

    </a>

    <!-- SUBJECTS -->
    <a href="manage_subjects.php"
       class="nav-link <?= active('/manage_subjects.php') ?>">

        <i class="bi bi-book me-2"></i>
        <span>Manage Subjects</span>

    </a>

    <!-- ASSIGN INSTRUCTORS -->
    <a href="assign_instructors.php"
       class="nav-link <?= active('/assign_instructors.php') ?>">

        <i class="bi bi-person-check me-2"></i>
        <span>Assign Instructors</span>

    </a>

    <!-- ASSIGN STUDENTS -->
    <a href="assign_students.php"
       class="nav-link <?= active('/assign_students.php') ?>">

        <i class="bi bi-people-fill me-2"></i>
        <span>Assign Students</span>

    </a>

    <!-- REPORTS -->
    <a href="attendance_reports.php"
       class="nav-link <?= active('/attendance_reports.php') ?>">

        <i class="bi bi-clipboard-data me-2"></i>
        <span>Attendance Reports</span>

    </a>

    <!-- ABOUT -->
    <a href="manage_about.php"
       class="nav-link <?= active('/manage_about.php') ?>">

        <i class="bi bi-info-circle me-2"></i>
        <span>Manage About</span>

    </a>

    <!-- CAROUSEL -->
    <a href="manage_carousel.php"
       class="nav-link <?= active('/manage_carousel.php') ?>">

        <i class="bi bi-images me-2"></i>
        <span>Manage Carousel</span>

    </a>

    <!-- CONTACT INFO -->
    <a href="manage_contact_info.php"
       class="nav-link <?= active('/manage_contact_info.php') ?>">

        <i class="bi bi-telephone me-2"></i>
        <span>Manage Contact Info</span>

    </a>

    <!-- CONTACT MESSAGES -->
    <a href="manage_contact_messages.php"
       class="nav-link <?= active('/manage_contact_messages.php') ?>">

        <i class="bi bi-chat-dots me-2"></i>
        <span>Contact Messages</span>

    </a>

    <!-- HOW IT WORKS -->
    <a href="manage_how_it_works.php"
       class="nav-link <?= active('/manage_how_it_works.php') ?>">

        <i class="bi bi-diagram-3 me-2"></i>
        <span>How It Works</span>

    </a>

    <!-- SERVICES -->
    <a href="manage_services.php"
       class="nav-link <?= active('/manage_services.php') ?>">

        <i class="bi bi-grid me-2"></i>
        <span>Manage Services</span>

    </a>

    <!-- TESTIMONIALS -->
    <a href="manage_testimonials.php"
       class="nav-link <?= active('/manage_testimonials.php') ?>">

        <i class="bi bi-chat-square-quote me-2"></i>
        <span>Testimonials</span>

    </a>

    <!-- SYSTEM LOGS -->
    <a href="manage_logs.php"
       class="nav-link <?= active('/manage_logs.php') ?>">

        <i class="bi bi-file-earmark-text me-2"></i>
        <span>System Logs</span>

    </a>

    <!-- LOGOUT -->
    <a href="../../auth/logout.php">

        <i class="bi bi-box-arrow-right me-2"></i>
        <span>Logout</span>

    </a>

</div>
