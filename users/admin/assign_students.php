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

<title>Assign Students</title>

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
            <div style="float:right;">
                <button class="btn btn-primary mb-3"
                        data-bs-toggle="modal"
                        data-bs-target="#assignModal"
                        onclick="openAssignModal()">

                    + Assign Student

                </button>
            </div>

        <table class="table table-bordered">

            <thead>
            <tr>
                <th>ID</th>
                <th>Student</th>
                <th>Class</th>
                <th>Department</th>
                <th>Year</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody id="assignTable"></tbody>

        </table>

    </div>

</div>

<!-- MODAL -->
<div class="modal fade" id="assignModal">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">
                <h5 id="modalTitle">Assign Student</h5>
            </div>

            <div class="modal-body">

                <input type="hidden" id="assign_id">

                <!-- STUDENT -->
                <div class="mb-2">
                    <label>Student</label>
                    <select id="student_id" class="form-control"></select>
                    <small class="text-danger" id="student_error"></small>
                </div>

                <!-- DEPARTMENT -->
                <div class="mb-2">
                    <label>Department</label>
                    <select id="department_id" class="form-control"></select>
                </div>

                <!-- CLASS -->
                <div class="mb-2">
                    <label>Class</label>
                    <select id="class_id" class="form-control"></select>
                    <small class="text-danger" id="class_error"></small>
                </div>

            </div>

            <div class="modal-footer">

                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                <button class="btn btn-success" onclick="saveAssignment()">Save</button>

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

/* ---------------- LOAD ASSIGNMENTS ---------------- */
function loadAssignments(){

    fetch("../../backend/process.php?action=read_assignments")
    .then(res=>res.json())
    .then(data=>{
        let rows="";

        data.forEach(a=>{
            rows += `
                <tr>

                <td>${a.id}</td>

                <td>${a.first_name} ${a.last_name}</td>

                <td>${a.class_name}</td>

                <td>${a.department_name}</td>

                <td>${a.enrollment_year ?? ''}</td>

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

/* ---------------- LOAD DROPDOWNS ---------------- */
function loadDropdowns(){

    fetch("../../backend/process.php?action=students")
    .then(r=>r.json())
    .then(data=>{

        let opt="<option value=''>Select Student</option>";

        data.forEach(s=>{
            opt+=`<option value="${s.id}">
                ${s.first_name} ${s.last_name}
                </option>`;
        });

        document.getElementById("student_id").innerHTML=opt;

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

    fetch("../../backend/process.php?action=departments")
    .then(r=>r.json())
    .then(data=>{

        let opt="<option value=''>Select Department</option>";

        data.forEach(d=>{
            opt+=`<option value="${d.id}">
            ${d.department_name}
            </option>`;
        });

        document.getElementById("department_id").innerHTML=opt;
    });

}

/* ---------------- OPEN MODAL ---------------- */
function openAssignModal(){

    document.getElementById("modalTitle").innerText="Assign Student";

    document.getElementById("assign_id").value="";
    document.getElementById("student_id").value="";
    document.getElementById("class_id").value="";
    document.getElementById("department_id").value="";

}

/* ---------------- SAVE ---------------- */
function saveAssignment(){

    let id=document.getElementById("assign_id").value;
    let student=document.getElementById("student_id").value;
    let cls=document.getElementById("class_id").value;
    let dept=document.getElementById("department_id").value;

    document.getElementById("student_error").innerText="";
    document.getElementById("class_error").innerText="";

    let err=false;

    if(student==""){
    document.getElementById("student_error").innerText="Required";
    err=true;
    }

    if(cls==""){
    document.getElementById("class_error").innerText="Required";
    err=true;
    }

    if(err) return;

    let fd=new FormData();

    fd.append("id",id);
    fd.append("student_id",student);
    fd.append("class_id",cls);
    fd.append("department_id",dept);

    fd.append("action", id ? "update_assignment" : "create_assignment");

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

    fetch("../../backend/process.php?action=single_assignment&id="+id)
    .then(r=>r.json())
    .then(a=>{

        document.getElementById("modalTitle").innerText="Edit Assignment";
        document.getElementById("assign_id").value=a.id;
        document.getElementById("student_id").value=a.student_id;
        document.getElementById("department_id").value=a.department_id;
        document.getElementById("class_id").value=a.class_id;
        
        new bootstrap.Modal(document.getElementById("assignModal")).show();

    });

}

/* ---------------- DELETE ---------------- */
function deleteAssign(id){

Swal.fire({
title:"Delete Assignment?",
icon:"warning",
showCancelButton:true
}).then(res=>{

if(res.isConfirmed){

fetch("../../backend/process.php?action=delete_assignment&id="+id)
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
