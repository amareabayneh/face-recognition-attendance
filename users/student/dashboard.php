
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

<title>Student Dashboard - Smart Attendance System</title>

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

<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <h4 class="text-center mt-3">
        <span class="text-yellow">Student</span> Panel
    </h4>

    <!-- DASHBOARD -->
    <a href="dashboard.php" class="nav-link active">

        <i class="bi bi-speedometer2 me-2"></i>
        <span>Dashboard</span>

    </a>

    <!-- ATTENDANCE REPORTS -->
    <a href="attendance_reports.php">

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
            <span>Dashboard</span>
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


	<div class="container-fluid p-4">

	    <h2 class="mb-4">
	        My Attendance Analytics
	    </h2>

	    <div class="row">

	        <div class="col-md-4 mb-4">
	            <div class="card shadow">
	                <div class="card-body text-center">
	                    <h5>Attendance %</h5>
	                    <h2 id="attendancePercent">0%</h2>
	                </div>
	            </div>
	        </div>

	    </div>

	    <div class="row">

	        <div class="col-md-6 mb-4">
	            <div class="card shadow">
	                <div class="card-body">
	                    <canvas id="studentDoughnut"></canvas>
	                </div>
	            </div>
	        </div>

	        <div class="col-md-6 mb-4">
	            <div class="card shadow">
	                <div class="card-body">
	                    <canvas id="studentRadar"></canvas>
	                </div>
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

async function loadStudentDashboard(){

    const response = await fetch("../../backend/process.php",{
        method:"POST",
        headers:{
            "Content-Type":"application/x-www-form-urlencoded"
        },
        body:"action=load_student_dashboard"
    });

    const data = await response.json();

    // console.log(data);

    document.getElementById("attendancePercent")
    .innerText = data.percent + "%";

    const labels = ['Present','Late','Absent'];

    const values = [
        data.present,
        data.late,
        data.absent
    ];

    new Chart(document.getElementById('studentDoughnut'),{
        type:'doughnut',
        data:{
            labels:labels,
            datasets:[{
                data:values,
                backgroundColor:[
                    '#28a745',
                    '#ffc107',
                    '#dc3545'
                ]
            }]
        }
    });

    new Chart(document.getElementById('studentRadar'),{
        type:'radar',
        data:{
            labels:labels,
            datasets:[{
                label:'Attendance',
                data:values
            }]
        }
    });

}

loadStudentDashboard();

</script>


</body>
</html>












