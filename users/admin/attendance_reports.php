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

    <title>Attendance Reports</title>

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


<!-- MAIN -->
<div class="dashboard-content">

    <nav class="navbar bg-light shadow-sm d-flex justify-content-between px-3">

        <div class="d-flex align-items-center">
            <i id="sidebarToggle"
               class="bi bi-filter-right fs-1 me-3"
               style="cursor:pointer;"></i>

            <span>Assign Students</span>
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

    <div class="container mt-4">

        <div class="card shadow">

            <div class="card-header bg-dark text-white">
                <h4>Attendance Reports - Admin</h4>
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-3 mb-3">
                        <label>Report Type</label>

                        <select id="report_type" class="form-control">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="student">Per Student</option>
                            <option value="class">Per Class</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Date</label>
                        <input type="date" id="date" class="form-control">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Student</label>

                        <select id="student_id" class="form-control">

                            <option value="">Select Student</option>

                            <?php
                            $students = mysqli_query($conn, "
                                SELECT * FROM students
                                ORDER BY first_name ASC
                            ");

                            while($student = mysqli_fetch_assoc($students)){
                            ?>

                            <option value="<?= $student['id']; ?>">
                                <?= $student['student_code']; ?> -
                                <?= $student['first_name']; ?>
                                <?= $student['last_name']; ?>
                            </option>

                            <?php } ?>

                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Class</label>

                        <select id="class_id" class="form-control">

                            <option value="">Select Class</option>

                            <?php
                            $classes = mysqli_query($conn, "
                                SELECT * FROM classes
                                ORDER BY class_name ASC
                            ");

                            while($class = mysqli_fetch_assoc($classes)){
                            ?>

                            <option value="<?= $class['id']; ?>">
                                <?= $class['class_name']; ?>
                                (<?= $class['section']; ?>)
                            </option>

                            <?php } ?>

                        </select>
                    </div>

                </div>

                <button class="btn btn-primary" id="generateReportBtn">
                    Generate Report
                </button>

            </div>

        </div>


        <div class="card shadow mt-4">

            <div class="card-header bg-success text-white">
                <h5>Report Result</h5>
            </div>

            <div class="card-body">
                <div id="reportResult"></div>
            </div>

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

const generateReportBtn = document.getElementById("generateReportBtn");


generateReportBtn.addEventListener("click", async () => {

    const report_type = document.getElementById("report_type").value;
    const date = document.getElementById("date").value;
    const student_id = document.getElementById("student_id").value;
    const class_id = document.getElementById("class_id").value;


    const formData = new FormData();

    formData.append("action", "generate_attendance_report");
    formData.append("report_type", report_type);
    formData.append("date", date);
    formData.append("student_id", student_id);
    formData.append("class_id", class_id);


    try{

        const response = await fetch("../../backend/process.php", {
            method: "POST",
            body: formData
        });

        const data = await response.text();

        document.getElementById("reportResult").innerHTML = data;

        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: 'Report Generated Successfully'
        });

    }catch(error){

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to Generate Report'
        });

    }

});

</script>

</body>
</html>
