
<?php
session_start();
require_once("../../backend/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/*
|-------------------------------
| GET STUDENT INFO
|-------------------------------
*/

$studentQuery = mysqli_query($conn,"
SELECT students.*, departments.department_name, classes.class_name
FROM students
LEFT JOIN departments
ON students.department_id = departments.id
LEFT JOIN classes
ON students.class_id = classes.id
WHERE students.user_id = '$user_id'
");

$student = mysqli_fetch_assoc($studentQuery);

if(!$student){

    $student = [

        'first_name' => '',
        'last_name' => '',
        'student_code' => '',
        'department_name' => '',
        'class_name' => ''

    ];
}


?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Attendance Portal</title>

    <link href="../../static/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../static/css/style.css">
    <link rel="stylesheet" href="../../static/vendor/bootstrap-icons/bootstrap-icons.css">

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

<body style="background:#f5f7fb;">

<!-- SIDEBAR -->
<div class="sidebar">

    <h4 class="text-center mt-3">
        <span class="text-yellow">Student</span> Panel
    </h4>

    <!-- DASHBOARD -->
    <a href="dashboard.php">

        <i class="bi bi-speedometer2 me-2"></i>
        <span>Dashboard</span>

    </a>

    <!-- ATTENDANCE REPORTS -->
    <a href="attendance_reports.php" class="nav-link active">

        <i class="bi bi-clipboard-check me-2"></i>
        <span>Attendance Reports</span>

    </a>

    <!-- LOGOUT -->
    <a href="../../auth/logout.php">

        <i class="bi bi-box-arrow-right me-2"></i>
        <span>Logout</span>

    </a>

</div>


<!-- MAIN CONTENT -->
<div class="dashboard-content">

    <!-- TOP NAV -->
    <nav class="navbar bg-light shadow-sm d-flex justify-content-between align-items-center px-3 position-relative">

        <div class="d-flex align-items-center">
            <i id="sidebarToggle" class="bi bi-filter-right fs-1 me-3" style="cursor:pointer;"></i>
            <span>Attendance Reports</span>
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
                    Student
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

    <div class="container-fluid py-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">

                <div class="row align-items-center">

                    <div class="col-md-8">

                        <h3 class="fw-bold mb-1">
                            Student Self Attendance Portal
                        </h3>

                        <p class="text-muted mb-0">
                            View your attendance history and reports
                        </p>

                    </div>

                    <div class="col-md-4 text-end">

                        <h5 class="mb-1">
                            <?php
                            echo htmlspecialchars(
                                $student['first_name']." ".$student['last_name']
                            );
                            ?>

                        </h5>

                        <small class="text-muted">
                            <?php
                            echo htmlspecialchars(
                                ($student['first_name'] ?? '') .
                                " " .
                                ($student['last_name'] ?? '')
                            );
                            ?>

                        </small>

                    </div>

                </div>

            </div>
        </div>

        <!-- STATISTICS -->

        <div class="row mb-4">

            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">

                        <i class="fas fa-check-circle text-success fa-2x mb-2"></i>

                        <h5>Total Present</h5>

                        <h2 id="totalPresent">0</h2>

                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">

                        <i class="fas fa-times-circle text-danger fa-2x mb-2"></i>

                        <h5>Total Absent</h5>

                        <h2 id="totalAbsent">0</h2>

                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">

                        <i class="fas fa-clock text-warning fa-2x mb-2"></i>

                        <h5>Total Late</h5>

                        <h2 id="totalLate">0</h2>

                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">

                        <i class="fas fa-chart-pie text-primary fa-2x mb-2"></i>

                        <h5>Attendance %</h5>

                        <h2 id="attendancePercentage">0%</h2>

                    </div>
                </div>
            </div>

        </div>

        <!-- REPORT FILTER -->

        <div class="card shadow-sm border-0 mb-4">

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-4">

                        <label class="form-label">
                            Start Date
                        </label>

                        <input type="date"
                               class="form-control"
                               id="startDate">

                    </div>

                    <div class="col-md-4">

                        <label class="form-label">
                            End Date
                        </label>

                        <input type="date"
                               class="form-control"
                               id="endDate">

                    </div>

                    <div class="col-md-4 d-flex align-items-end">

                        <button class="btn btn-primary w-100"
                                onclick="loadAttendanceHistory()">

                            <i class="fas fa-search"></i>
                            Generate Report

                        </button>

                    </div>

                </div>

            </div>

        </div>

        <!-- ATTENDANCE HISTORY -->

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white">

                <h5 class="mb-0">
                    Attendance History
                </h5>

            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle">

                        <thead class="table-dark">

                        <tr>

                            <th>#</th>
                            <th>Session</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Method</th>
                            <th>Confidence</th>

                        </tr>

                        </thead>

                        <tbody id="attendanceTableBody">

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>
</div>


<!-- SCROLL TOP -->
<button id="scrollTop">
    <i class="bi bi-arrow-up"></i>
</button>

<script src="../../static/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../static/js/main.js"></script>
<script src="../../static/vendor/chartjs/Chart.js"></script>
<script src="../../static/vendor/sweetalert/js/sweetalert2.all.js"></script>

<?php include 'profile.html'; ?>


<script>

/*
|---------------------------------
| LOAD ATTENDANCE SUMMARY
|---------------------------------
*/

async function loadAttendanceSummary(){

    try{

        const response = await fetch("../../backend/process.php?action=getStudentAttendanceSummary");

        const data = await response.json();

        document.getElementById("totalPresent").innerText =
            data.present;

        document.getElementById("totalAbsent").innerText =
            data.absent;

        document.getElementById("totalLate").innerText =
            data.late;

        document.getElementById("attendancePercentage").innerText =
            data.percentage + "%";

    }catch(error){

        Swal.fire(
            "Error",
            "Failed to load attendance summary",
            "error"
        );

    }

}

/*
|------------------------------------
| LOAD ATTENDANCE HISTORY
|------------------------------------
*/

async function loadAttendanceHistory(){

    try{

        const startDate =
            document.getElementById("startDate").value;

        const endDate =
            document.getElementById("endDate").value;

        let url =
            "../../backend/process.php?action=getStudentAttendanceHistory";

        if(startDate && endDate){

            url += "&start_date=" + startDate +
                   "&end_date=" + endDate;
        }

        const response = await fetch(url);

        const data = await response.json();

        let html = "";

        if(data.length > 0){

            data.forEach((row,index)=>{

                let badge = "";

                if(row.status === "Present"){
                    badge = `<span class="badge bg-success">${row.status}</span>`;
                }else if(row.status === "Absent"){
                    badge = `<span class="badge bg-danger">${row.status}</span>`;
                }else if(row.status === "Late"){
                    badge = `<span class="badge bg-warning">${row.status}</span>`;
                }else{
                    badge = `<span class="badge bg-info">${row.status}</span>`;
                }

                html += `
                <tr>

                    <td>${index + 1}</td>

                    <td>${row.session_name}</td>

                    <td>${row.subject_name}</td>

                    <td>${row.session_date}</td>

                    <td>${row.attendance_time}</td>

                    <td>${badge}</td>

                    <td>${row.attendance_method}</td>

                    <td>${row.recognition_confidence ?? 0}%</td>

                </tr>
                `;
            });

        }else{

            html = `
            <tr>
                <td colspan="8" class="text-center text-muted">
                    No attendance records found
                </td>
            </tr>
            `;
        }

        document.getElementById("attendanceTableBody").innerHTML =
            html;

    }catch(error){

        Swal.fire(
            "Error",
            "Failed to load attendance history",
            "error"
        );

    }

}

/*
|------------------------------
| INITIAL LOAD
|------------------------------
*/

loadAttendanceSummary();
loadAttendanceHistory();

</script>

</body>
</html>
