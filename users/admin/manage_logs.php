<?php
require_once("../../backend/db.php");
?>

<!DOCTYPE html>
<html>
<head>

    <title>System Logs</title>

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

<?php include 'sidebar.php'; ?>

<div class="dashboard-content">

    <nav class="navbar bg-light shadow-sm px-3">

        <div class="d-flex align-items-center">

            <i id="sidebarToggle"
               class="bi bi-filter-right fs-1 me-3"
               style="cursor:pointer;"></i>

            <span>System Logs</span>

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

        <div class="row mb-3">

            <div class="col-md-3">

                <select id="filter_action"
                        class="form-control">

                    <option value="">
                        All Actions
                    </option>

                    <option value="LOGIN_SUCCESS">
                        LOGIN_SUCCESS
                    </option>

                    <option value="LOGIN_FAILED">
                        LOGIN_FAILED
                    </option>

                    <option value="CREATE_STUDENT">
                        CREATE_STUDENT
                    </option>

                    <option value="DELETE_STUDENT">
                        DELETE_STUDENT
                    </option>

                </select>

            </div>





            <!-- <div class="col-md-3">

                <select id="filter_severity"
                        class="form-control">

                    <option value="">
                        All Severity
                    </option>

                    <option value="Info">
                        Info
                    </option>

                    <option value="Warning">
                        Warning
                    </option>

                    <option value="Critical">
                        Critical
                    </option>

                </select>

            </div> -->





            <div class="col-md-3">

                <input type="date"
                       id="filter_date"
                       class="form-control">

            </div>





            <div class="col-md-3">

                <button class="btn btn-primary w-100"
                        onclick="loadLogs()">

                    Filter Logs

                </button>

            </div>

        </div>





        <div class="table-responsive">

            <table class="table table-bordered table-hover">

                <thead class="table-dark">

                    <tr>

                        <th>ID</th>

                        <th>User</th>

                        <th>Action</th>

                        <th>Description</th>

                        <!-- <th>Severity</th> -->

                        <th>IP Address</th>

                        <th>Date</th>

                    </tr>

                </thead>

                <tbody id="logsTable"></tbody>

            </table>

        </div>

    </div>

</div>


<button id="scrollTop"><i class="bi bi-arrow-up"></i></button>

<script src="../../static/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../static/js/main.js"></script>
<script src="../../static/vendor/chartjs/Chart.js"></script>
<script src="../../static/vendor/sweetalert/js/sweetalert2.all.js"></script>

<?php include 'profile.html'; ?>


<script>

function loadLogs(){

    let action =
    document.getElementById("filter_action").value;

    // let severity =
    // document.getElementById("filter_severity").value;

    let date =
    document.getElementById("filter_date").value;





    fetch(

    "../../backend/process.php?action=read_logs"

    + "&filter_action=" + action

    // + "&filter_severity=" + severity

    + "&filter_date=" + date

    )

    .then(async res => {

        let text = await res.text();

        console.log(text);

        return JSON.parse(text);

    })

    .then(data => {

        let rows = "";





        data.forEach(log => {

            rows += `

            <tr>

                <td>${log.id}</td>

                <td>${log.username ?? ''}</td>

                <td>${log.action_type}</td>

                <td>${log.description}</td>

                <td>${log.ip_address}</td>

                <td>${log.created_at}</td>

            </tr>

            `;

        });





        document.getElementById("logsTable")
        .innerHTML = rows;

    });

}

loadLogs();

</script>

</body>
</html>
