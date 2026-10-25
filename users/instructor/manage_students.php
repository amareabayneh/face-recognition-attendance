
<?php
session_start();
require_once("../../backend/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>

    <title>My Students</title>

    <link href="../../static/vendor/bootstrap/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet"
          href="../../static/vendor/bootstrap-icons/bootstrap-icons.css">

    <link rel="stylesheet"
          href="../../static/css/style.css">


<style> 
    /* NAVBAR */
    .navbar{
        position: relative;
        z-index: 1000;
        overflow: visible !important;
    }

    /* DROPDOWN */
    .dropdown{
        position: relative;
    }

    .dropdown-menu{
        z-index: 3000 !important;
        position: absolute !important;
    }

    /* CONTENT */
    .dashboard-content{
        overflow: visible !important;
    }
</style>


</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <h4 class="text-center mt-3">
        <span class="text-yellow">Instructor</span> Panel
    </h4>

    <!-- DASHBOARD -->
    <a href="dashboard.php">

        <i class="bi bi-speedometer2 me-2"></i>
        <span>Dashboard</span>

    </a>

    <!-- ATTENDANCE SESSIONS -->
    <a href="manage_attendance_sessions.php">

        <i class="bi bi-camera-video me-2"></i>
        <span>Manage Sessions</span>

    </a>

    <!-- REPORTS -->
    <a href="attendance_reports.php">

        <i class="bi bi-clipboard-data me-2"></i>
        <span>Attendance Reports</span>

    </a>

    <!-- STUDENTS -->
    <a href="manage_students.php" class="nav-link active">

        <i class="bi bi-mortarboard me-2"></i>
        <span>Students</span>

    </a>

    <!-- LOGOUT -->
    <a href="../../auth/logout.php">

        <i class="bi bi-box-arrow-right me-2"></i>
        <span>Logout</span>

    </a>

</div>


<div class="dashboard-content">

    <nav class="navbar bg-light shadow-sm d-flex justify-content-between align-items-center px-3 position-relative">

        <div class="d-flex align-items-center">
            <i id="sidebarToggle" class="bi bi-filter-right fs-1 me-3" style="cursor:pointer;"></i>
            <span>Student lists</span>
        </div>

        <div class="dropdown">

            <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2"
                    data-bs-toggle="dropdown">

                <img id="navProfilePhoto"
                     src="../../static/img/default.jpg"
                     width="40"
                     height="40"
                     class="rounded-circle border object-fit-cover">

                <span id="navUserName">
                    Instructor
                </span>

            </button>


            <ul class="dropdown-menu dropdown-menu-end">

                <li>
                    <a class="dropdown-item"
                       href="#"
                       onclick="openProfileModal()">

                        <i class="bi bi-person"></i>
                        My Profile

                    </a>
                </li>

                <li>
                    <a class="dropdown-item"
                       href="#"
                       onclick="openChangePasswordModal()">

                        <i class="bi bi-key"></i>
                        Change Password

                    </a>
                </li>

                <li><hr class="dropdown-divider"></li>

                <li>
                    <a class="dropdown-item text-danger"
                       href="../../auth/logout.php">

                        <i class="bi bi-box-arrow-right"></i>
                        Logout

                    </a>
                </li>

            </ul>

        </div>
        

    </nav>





    <div class="container mt-4">

        <div class="table-responsive">

            <table class="table table-bordered table-hover">

                <thead class="table-dark">

                    <tr>

                        <th>ID</th>

                        <th>Photo</th>

                        <th>Student Code</th>

                        <th>Full Name</th>

                        <th>Gender</th>

                        <th>Department</th>

                        <th>Class</th>

                        <th>Subject</th>

                    </tr>

                </thead>

                <tbody id="studentTable"></tbody>

            </table>

        </div>

    </div>

</div>

<button id="scrollTop">
    <i class="bi bi-arrow-up"></i>
</button>

<script src="../../static/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script src="../../static/vendor/sweetalert/js/sweetalert2.all.js"></script>

<script src="../../static/js/main.js"></script>

<?php include 'profile.html'; ?>

<script>

function loadStudents(){

    fetch(
    "../../backend/process.php?action=read_instructor_students"
    )

    .then(async res => {

        let text = await res.text();

        console.log(text);

        return JSON.parse(text);

    })

    .then(data => {

        let rows = "";





        data.forEach(student => {

            rows += `

            <tr>

                <td>${student.id}</td>

                <td>

                    <img src="../../uploads/students/${student.photo}"

                         width="50"
                         height="50"

                         style="
                            object-fit:cover;
                            border-radius:50%;
                         ">

                </td>

                <td>${student.student_code}</td>

                <td>

                    ${student.first_name}
                    ${student.last_name}

                </td>

                <td>${student.gender}</td>

                <td>${student.department_name ?? ''}</td>

                <td>${student.class_name ?? ''}</td>

                <td>${student.subject_name ?? ''}</td>

            </tr>

            `;

        });





        document.getElementById("studentTable")
        .innerHTML = rows;

    });

}

loadStudents();

</script>

</body>
</html>
