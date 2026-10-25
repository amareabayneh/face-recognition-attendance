
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

    <title>
        Manage Attendance Sessions
    </title>

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
        <span class="text-yellow">Instructor</span> Panel
    </h4>

    <!-- DASHBOARD -->
    <a href="dashboard.php">

        <i class="bi bi-speedometer2 me-2"></i>
        <span>Dashboard</span>

    </a>

    <!-- ATTENDANCE SESSIONS -->
    <a href="manage_attendance_sessions.php" class="nav-link active">

        <i class="bi bi-camera-video me-2"></i>
        <span>Manage Sessions</span>

    </a>

    <!-- REPORTS -->
    <a href="attendance_reports.php">

        <i class="bi bi-clipboard-data me-2"></i>
        <span>Attendance Reports</span>

    </a>

    <!-- STUDENTS -->
    <a href="manage_students.php">

        <i class="bi bi-mortarboard me-2"></i>
        <span>Students</span>

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
            <span>Manage sessions</span>
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

        <div class="d-flex
                    justify-content-between
                    align-items-center
                    mb-3">

            <h3>
                Attendance Sessions
            </h3>

            <button class="btn btn-primary"

            onclick="openCreateModal()"

            data-bs-toggle="modal"
            data-bs-target="#sessionModal">

                + Create Session

            </button>

        </div>

        <table class="table table-bordered">

            <thead>

            <tr>

                <th>ID</th>
                <th>Session</th>
                <th>Subject</th>
                <th>Class</th>
                <th>Date</th>
                <th>Time</th>
                <th>Room</th>
                <th>Status</th>
                <th>Actions</th>

            </tr>

            </thead>

            <tbody id="sessionTable"></tbody>

        </table>

    </div>
</div>



<!-- SESSION MODAL -->
<div class="modal fade" id="sessionModal">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header">

                <h5 id="modalTitle">
                    Create Attendance Session
                </h5>

                <button type="button"
                class="btn-close"
                data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <input type="hidden"
                id="session_id">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label>
                            Class Subject
                        </label>

                        <select id="class_subject_id"
                        class="form-control">
                        </select>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>
                            Session Name
                        </label>

                        <input type="text"
                        id="session_name"
                        class="form-control">

                    </div>

                </div>



                <div class="row">

                    <div class="col-md-4 mb-3">

                        <label>
                            Session Date
                        </label>

                        <input type="date"
                        id="session_date"
                        class="form-control">

                    </div>

                    <div class="col-md-4 mb-3">

                        <label>
                            Start Time
                        </label>

                        <input type="time"
                        id="start_time"
                        class="form-control">

                    </div>

                    <div class="col-md-4 mb-3">

                        <label>
                            End Time
                        </label>

                        <input type="time"
                        id="end_time"
                        class="form-control">

                    </div>

                </div>



                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label>
                            Room
                        </label>

                        <input type="text"
                        id="room"
                        class="form-control">

                    </div>


                </div>

            </div>

            <div class="modal-footer">

                <button class="btn btn-secondary"
                data-bs-dismiss="modal">

                    Close

                </button>

                <button class="btn btn-success"
                onclick="saveSession()">

                    Save Session

                </button>

            </div>

        </div>

    </div>

</div>


<button id="scrollTop">
    <i class="bi bi-arrow-up"></i>
</button>

<script src="../../static/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script src="../../static/vendor/sweetalert/js/sweetalert2.all.js"></script>
<script src="../../static/vendor/face/js/face-api.min.js"></script>
<script src="../../static/js/main.js"></script>

<?php include 'profile.html'; ?>

<script>

/* =====================================================
   LOAD ATTENDANCE SESSIONS
===================================================== */
function loadSessions(){

    fetch(
    "../../backend/process.php?action=read_attendance_sessions"
    )

    .then(res => res.json())

    .then(data => {

        let rows = "";

        data.forEach(session => {

            let status = getSessionStatus(session);
            console.log(session.start_time)
            console.log(session.end_time)
            rows += `

            <tr>

                <td>${session.id}</td>

                <td>${session.session_name}</td>

                <td>

                    ${session.subject_name ?? ''}

                </td>

                <td>

                    ${session.class_name ?? ''}

                </td>

                <td>${session.session_date}</td>

                <td>
                ${formatTime(session.start_time)} - ${formatTime(session.end_time)}

                </td>

                <td>${session.room}</td>


                <td>

                    ${
                        status === "Open"
                        ? `<span class="badge bg-success">Open</span>`
                        : status === "Upcoming"
                        ? `<span class="badge bg-warning text-dark">Upcoming</span>`
                        : `<span class="badge bg-danger">Closed</span>`
                    }

                </td>


                <td>

                    ${
                        getSessionStatus(session) === "Open"
                        ? `<a href="scan_attendance.php?session_id=${session.id}"
                             class="btn btn-success btn-sm">
                             Start Attendance
                           </a>`
                        : `<button class="btn btn-secondary btn-sm" disabled>
                             Not Available
                           </button>`
                    }


                    <button
                    class="btn btn-warning btn-sm"
                    onclick="editSession(${session.id})">

                        Edit

                    </button>

                    <button
                    class="btn btn-danger btn-sm"
                    onclick="deleteSession(${session.id})">

                        Delete

                    </button>

                </td>


            </tr>

            `;
        });

        document.getElementById("sessionTable")
        .innerHTML = rows;

    });

}



/* =====================================================
   LOAD CLASS SUBJECTS
===================================================== */
function loadClassSubjects(){

    fetch(
    "../../backend/process.php?action=load_class_subjects"
    )

    .then(res => res.json())

    .then(data => {

        let options =
        `<option value="">Select</option>`;

        data.forEach(item => {

            options += `

            <option value="${item.id}">

                ${item.subject_name}
                (${item.subject_code})
                -
                ${item.class_name}

            </option>

            `;

        });

        document.getElementById(
        "class_subject_id"
        ).innerHTML = options;

    });

}



/* =====================================================
   OPEN CREATE MODAL
===================================================== */
function openCreateModal(){

    document.getElementById("modalTitle")
    .innerText =
    "Create Attendance Session";

    document.getElementById("session_id")
    .value = "";

    document.getElementById("session_name")
    .value = "";

    document.getElementById("session_date")
    .value = "";

    document.getElementById("start_time")
    .value = "";

    document.getElementById("end_time")
    .value = "";

    document.getElementById("room")
    .value = "";

}



/* =====================================================
   SAVE SESSION
===================================================== */
function saveSession(){

    let id =
    document.getElementById("session_id")
    .value;

    let formData = new FormData();

    formData.append(
        "id",
        id
    );

    formData.append(
        "class_subject_id",
        document.getElementById("class_subject_id").value
    );

    formData.append(
        "session_name",
        document.getElementById("session_name").value
    );

    formData.append(
        "session_date",
        document.getElementById("session_date").value
    );


    formData.append(
        "start_time",
        to24Hour(document.getElementById("start_time").value)
    );

    formData.append(
        "end_time",
        to24Hour(document.getElementById("end_time").value)
    );

    formData.append(
        "room",
        document.getElementById("room").value
    );


    let action = id
    ? "update_attendance_session"
    : "create_attendance_session";

    formData.append(
        "action",
        action
    );

    fetch("../../backend/process.php", {

        method:"POST",
        body:formData

    })

    .then(res => res.json())

    .then(data => {

        Swal.fire(
            "Success",
            data.message,
            "success"
        );

        bootstrap.Modal
        .getInstance(
            document.getElementById("sessionModal")
        ).hide();

        loadSessions();

    });

}



/* =====================================================
   EDIT SESSION
===================================================== */
function editSession(id){

    fetch(

    "../../backend/process.php?action=single_attendance_session&id="
    + id

    )

    .then(res => res.json())

    .then(session => {

        document.getElementById("modalTitle")
        .innerText =
        "Edit Attendance Session";

        document.getElementById("session_id")
        .value = session.id;

        document.getElementById("class_subject_id")
        .value = session.class_subject_id;

        document.getElementById("session_name")
        .value = session.session_name;

        document.getElementById("session_date")
        .value = session.session_date;

        document.getElementById("start_time")
        .value = session.start_time;

        document.getElementById("end_time")
        .value = session.end_time;

        document.getElementById("room")
        .value = session.room;


        new bootstrap.Modal(
            document.getElementById("sessionModal")
        ).show();

    });

}



/* =====================================================
   DELETE SESSION
===================================================== */
function deleteSession(id){

    Swal.fire({

        title:
        "Delete attendance session?",

        text:
        "This action cannot be undone!",

        icon:"warning",

        showCancelButton:true

    })

    .then(result => {

        if(result.isConfirmed){

            fetch(

            "../../backend/process.php?action=delete_attendance_session&id="
            + id

            )

            .then(res => res.json())

            .then(data => {

                Swal.fire(
                    "Deleted",
                    data.message,
                    "success"
                );

                loadSessions();

            });

        }

    });

}




function getSessionStatus(session){

    let now = new Date();
    let currentTime = now.toTimeString().slice(0,5);

    let start = session.start_time.slice(0,5);
    let end = session.end_time.slice(0,5);

    if(end < start){
        // overnight session
        if(currentTime >= start || currentTime <= end){
            return "Open";
        }
    } else {
        if(currentTime >= start && currentTime <= end){
            return "Open";
        }
    }

    if(currentTime < start){
        return "Upcoming";
    }

    return "Closed";
}



function formatTime(time24) {

    let [hour, minute] = time24.split(':');

    hour = parseInt(hour);

    let ampm = hour >= 12 ? 'PM' : 'AM';

    hour = hour % 12;
    hour = hour ? hour : 12;

    return `${hour}:${minute} ${ampm}`;
}


function to24Hour(time12) {

    let [time, modifier] = time12.split(' ');

    let [hours, minutes] = time.split(':');

    hours = parseInt(hours);

    if (modifier === 'PM' && hours !== 12) {
        hours += 12;
    }

    if (modifier === 'AM' && hours === 12) {
        hours = 0;
    }

    return `${String(hours).padStart(2,'0')}:${minutes}`;
}


function isSessionActive(start, end, now){

    if(end < start){
        // crosses midnight
        return (now >= start || now <= end);
    }

    return (now >= start && now <= end);
}



/* =====================================================
   INIT
===================================================== */
setInterval(()=>{
    let rows = document.querySelectorAll('#sessionTable tr');
    loadSessions();
}, 30000);

loadSessions();
loadClassSubjects();

</script>

</body>
</html>
