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

<title>Smart Student Attendance System</title>

<link href="../../static/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../../static/vendor/bootstrap-icons/bootstrap-icons.css">
<link rel="stylesheet" href="../../static/css/style.css">

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
<?php include 'sidebar.php'; ?>




<!-- MAIN CONTENT -->
<div class="dashboard-content">

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
                    Admin
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
            Attendance Analytics Dashboard
        </h2>

        <!-- STATISTICS -->

        <div class="row mb-4">

            <div class="col-md-3">
                <div class="card shadow border-0">
                    <div class="card-body text-center">
                        <h5>Total Students</h5>
                        <h2 id="totalStudents">0</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow border-0">
                    <div class="card-body text-center">
                        <h5>Present %</h5>
                        <h2 id="presentPercent">0%</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow border-0">
                    <div class="card-body text-center">
                        <h5>Late %</h5>
                        <h2 id="latePercent">0%</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow border-0">
                    <div class="card-body text-center">
                        <h5>Absent %</h5>
                        <h2 id="absentPercent">0%</h2>
                    </div>
                </div>
            </div>

        </div>

        <!-- CHARTS -->

        <div class="row">

            <!-- PIE -->
            <div class="col-md-6 mb-4">
                <div class="card shadow border-0">
                    <div class="card-header">
                        Attendance Pie Chart
                    </div>
                    <div class="card-body">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- BAR -->
            <div class="col-md-6 mb-4">
                <div class="card shadow border-0">
                    <div class="card-header">
                        Attendance Bar Chart
                    </div>
                    <div class="card-body">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- DOUGHNUT -->
            <div class="col-md-6 mb-4">
                <div class="card shadow border-0">
                    <div class="card-header">
                        Attendance Doughnut Chart
                    </div>
                    <div class="card-body">
                        <canvas id="doughnutChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- LINE -->
            <div class="col-md-6 mb-4">
                <div class="card shadow border-0">
                    <div class="card-header">
                        Weekly Attendance Trend
                    </div>
                    <div class="card-body">
                        <canvas id="lineChart"></canvas>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>







<button id="scrollTop"><i class="bi bi-arrow-up"></i></button>

<script src="../../static/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../static/vendor/chartjs/Chart.js"></script>
<script src="../../static/vendor/sweetalert/js/sweetalert2.all.js"></script>

<script src="../../static/js/main.js"></script>


<?php include 'profile.html'; ?>


<script>

async function loadDashboard(){

    const response = await fetch("../../backend/process.php", {
        method: "POST",
        headers: {
            "Content-Type":"application/x-www-form-urlencoded"
        },
        body: "action=load_admin_dashboard"
    });

    const data = await response.json();

    document.getElementById("totalStudents").innerText = data.total_students;

    document.getElementById("presentPercent").innerText =
        data.present_percent + "%";

    document.getElementById("latePercent").innerText =
        data.late_percent + "%";

    document.getElementById("absentPercent").innerText =
        data.absent_percent + "%";

    const labels = ['Present','Late','Absent'];

    const values = [
        data.present_count,
        data.late_count,
        data.absent_count
    ];

    // PIE CHART

    new Chart(document.getElementById('pieChart'),{
        type:'pie',
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

    // BAR CHART

    new Chart(document.getElementById('barChart'),{
        type:'bar',
        data:{
            labels:labels,
            datasets:[{
                label:'Attendance',
                data:values,
                backgroundColor:[
                    '#28a745',
                    '#ffc107',
                    '#dc3545'
                ]
            }]
        }
    });

    // DOUGHNUT

    new Chart(document.getElementById('doughnutChart'),{
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

    // LINE

    new Chart(document.getElementById('lineChart'),{
        type:'line',
        data:{
            labels:data.week_days,
            datasets:[{
                label:'Attendance',
                data:data.week_attendance,
                borderColor:'#007bff',
                fill:false,
                tension:0.3
            }]
        }
    });

}

loadDashboard();

</script>
</body>
</html>




