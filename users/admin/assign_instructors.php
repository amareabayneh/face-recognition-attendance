
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

<title>Assign Instructors</title>

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

            <span>Manage Instructors</span>
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
        <div style="float:right;">
            <button class="btn btn-primary mb-3"
                    data-bs-toggle="modal"
                    data-bs-target="#assignModal"
                    onclick="openModal()">

                + Assign Instructor

            </button>
        </div>

        <table class="table table-bordered">

            <thead>
            <tr>
                <th>ID</th>
                <th>Instructor</th>
                <th>Class</th>
                <th>Subject</th>
                <th>Semester</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody id="assignTable"></tbody>

        </table>

    </div>

</div>

<!-- MODAL -->
<div class="modal fade" id="assignModal" data-bs-backdrop='static'>

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">
                <h5 id="modalTitle">Assign Instructor</h5>
            </div>

            <div class="modal-body">

                <input type="hidden" id="assign_id">

                <!-- INSTRUCTOR -->
                <div class="mb-2">
                    <label>Instructor</label>
                    <select id="instructor_id" class="form-control"></select>
                    <small class="text-danger" id="instructor_error"></small>
                </div>

                <!-- CLASS -->
                <div class="mb-2">
                    <label>Class</label>
                    <select id="class_id" class="form-control"></select>
                </div>

                <!-- SUBJECT -->
                <div class="mb-2">
                    <label>Subject</label>
                    <select id="subject_id" class="form-control"></select>
                </div>

                <!-- SEMESTER -->
                <div class="mb-2">
                    <label>Semester</label>
                    <input type="text" id="semester" class="form-control" placeholder="1st / 2nd">
                </div>

            </div>

            <div class="modal-footer">

                <button class="btn btn-secondary" data-bs-dismiss="modal">
                Close
                </button>

                <button class="btn btn-success" onclick="saveAssignment()">
                Save
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

<script src="../../static/js/main.js"></script>


<?php include 'profile.html'; ?>



<script>

/* ---------------- LOAD ---------------- */
function loadAssignments(){

    fetch("../../backend/process.php?action=read_instructor_assignments")
    .then(r=>r.json())
    .then(data=>{

    let rows="";

    data.forEach(a=>{

    rows += `
    <tr>

    <td>${a.id}</td>

    <td>${a.first_name} ${a.last_name}</td>

    <td>${a.class_name}</td>

    <td>${a.subject_name}</td>

    <td>${a.semester}</td>

    <td>

    <button class="btn btn-warning btn-sm"
    onclick="editAssign(${a.id})">
    Edit
    </button>

    <button class="btn btn-danger btn-sm"
    onclick="deleteAssign(${a.id})">
    Delete
    </button>

    </td>

    </tr>
    `;

    });

    document.getElementById("assignTable").innerHTML=rows;

    });

}

/* ---------------- DROPDOWNS ---------------- */
function loadDropdowns(){

    fetch("../../backend/process.php?action=instructors")
    .then(r=>r.json())
    .then(data=>{

        let opt="<option value=''>Select Instructor</option>";
        data.forEach(i=>{
            opt+=`<option value="${i.id}">
            ${i.first_name} ${i.last_name}
            </option>`;
        });

        document.getElementById("instructor_id").innerHTML=opt;

    });

    fetch("../../backend/process.php?action=classes")
    .then(r=>r.json())
    .then(data=>{

        let opt="<option value=''>Select Class</option>";

        data.forEach(c=>{
            opt+=`<option value="${c.id}">
            ${c.class_name}
            </option>`;
        });

        document.getElementById("class_id").innerHTML=opt;

    });

    fetch("../../backend/process.php?action=subjects")
    .then(r=>r.json())
    .then(data=>{

        let opt="<option value=''>Select Subject</option>";

        data.forEach(s=>{
            opt+=`<option value="${s.id}">
            ${s.subject_name}
            </option>`;
        });

        document.getElementById("subject_id").innerHTML=opt;

    });

}

/* ---------------- OPEN ---------------- */
function openModal(){

document.getElementById("modalTitle").innerText="Assign Instructor";

document.getElementById("assign_id").value="";
document.getElementById("semester").value="";
document.getElementById("instructor_id").value="";
document.getElementById("class_id").value="";
document.getElementById("subject_id").value="";

}

/* ---------------- SAVE ---------------- */
function saveAssignment(){

let id=document.getElementById("assign_id").value;
let instructor=document.getElementById("instructor_id").value;
let cls=document.getElementById("class_id").value;
let subject=document.getElementById("subject_id").value;
let semester=document.getElementById("semester").value;

document.getElementById("instructor_error").innerText="";

if(instructor==""){
document.getElementById("instructor_error").innerText="Required";
return;
}

let fd=new FormData();

fd.append("id",id);
fd.append("instructor_id",instructor);
fd.append("class_id",cls);
fd.append("subject_id",subject);
fd.append("semester",semester);

fd.append("action",
id ? "update_instructor_assignment" : "create_instructor_assignment"
);

fetch("../../backend/process.php",{
method:"POST",
body:fd
})
.then(r=>r.json())
.then(data=>{

if(data.status==="error"){
Swal.fire("Error",data.message,"error");
return;
}

Swal.fire("Success",data.message,"success");

bootstrap.Modal.getInstance(
document.getElementById("assignModal")
).hide();

loadAssignments();

});

}

/* ---------------- EDIT ---------------- */
function editAssign(id){

fetch("../../backend/process.php?action=single_instructor_assignment&id="+id)
.then(r=>r.json())
.then(a=>{

document.getElementById("modalTitle").innerText="Edit Assignment";

document.getElementById("assign_id").value=a.id;
document.getElementById("instructor_id").value=a.instructor_id;
document.getElementById("class_id").value=a.class_id;
document.getElementById("subject_id").value=a.subject_id;
document.getElementById("semester").value=a.semester;

new bootstrap.Modal(document.getElementById("assignModal")).show();

});

}

/* ---------------- DELETE ---------------- */
function deleteAssign(id){

Swal.fire({
title:"Delete?",
icon:"warning",
showCancelButton:true
}).then(res=>{

if(res.isConfirmed){

fetch("../../backend/process.php?action=delete_instructor_assignment&id="+id)
.then(r=>r.json())
.then(data=>{

Swal.fire("Deleted",data.message,"success");
loadAssignments();

});

}

});

}

loadAssignments();
loadDropdowns();

</script>

</body>
</html>

