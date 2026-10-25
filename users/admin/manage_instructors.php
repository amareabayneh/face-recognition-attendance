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

<title>Manage Instructors</title>

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

    <nav class="navbar bg-light shadow-sm d-flex justify-content-between align-items-center px-3 position-relative">

        <div class="d-flex align-items-center">
            <i id="sidebarToggle" class="bi bi-filter-right fs-1 me-3" style="cursor:pointer;"></i>
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
                    data-bs-target="#instructorModal"
                    onclick="openCreateModal()">

                + Add Instructor

            </button>

        </div>

        <table class="table table-bordered">

            <thead>
            <tr>
                <th>ID</th>
                <th>Photo</th>
                <th>Name</th>
                <th>Code</th>
                <th>Phone</th>
                <th>Department</th>
                <th>Specialization</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody id="instructorTable"></tbody>

        </table>

    </div>
</div>

<!-- MODAL -->
<div class="modal fade" id="instructorModal">

<div class="modal-dialog modal-lg">

<div class="modal-content">

<div class="modal-header">
<h5 id="modalTitle">Add Instructor</h5>
</div>

<div class="modal-body">

<input type="hidden" id="instructor_id">

<div class="row">

<div class="col-md-6 mb-2">
<label>First Name</label>
<input type="text" id="first_name" class="form-control">
<small class="text-danger" id="first_name_error"></small>
</div>

<div class="col-md-6 mb-2">
<label>Last Name</label>
<input type="text" id="last_name" class="form-control">
<small class="text-danger" id="last_name_error"></small>
</div>

</div>

<div class="row">

<div class="col-md-6 mb-2">
<label>Instructor Code</label>
<input type="text" id="instructor_code" class="form-control">
<small class="text-danger" id="instructor_code_error"></small>
</div>

<div class="col-md-6 mb-2">
<label>Phone</label>
<input type="text" id="phone" class="form-control">
</div>

</div>

<div class="mb-2">
    <label>Department</label>
    <select id="department_id" class="form-control" required></select>
    <small class="text-danger" id="department_error"></small>
</div>

<div class="mb-2">
<label>Specialization</label>
<input type="text" id="specialization" class="form-control">
</div>

<div class="mb-2">
<label>Photo</label>
<input type="file" id="photo" class="form-control">
</div>

</div>

<div class="modal-footer">

<button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

<button class="btn btn-success" onclick="saveInstructor()">Save</button>

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
function loadInstructors(){

fetch("../../backend/process.php?action=read_instructors")
.then(res => res.json())
// .then(res => console.log(res))
.then(data => {

let rows = "";

data.forEach(i => {

let img = i.photo
? "../../uploads/instructors/" + i.photo
: "https://via.placeholder.com/50";

rows += `
<tr>

<td>${i.id}</td>

<td><img src="${img}" width="50" height="50" style="border-radius:50%"></td>

<td>${i.first_name} ${i.last_name}</td>

<td>${i.instructor_code}</td>

<td>${i.phone}</td>

<td>${i.department_name ?? ''}</td>

<td>${i.specialization ?? ''}</td>

<td>

<button class="btn btn-warning btn-sm"
onclick="editInstructor(${i.id})">Edit</button>

<button class="btn btn-danger btn-sm"
onclick="deleteInstructor(${i.id})">Delete</button>

</td>

</tr>
`;
});

document.getElementById("instructorTable").innerHTML = rows;

});

}

/* ---------------- DEPARTMENTS ---------------- */
function loadDepartments(){

fetch("../../backend/process.php?action=departments")
.then(res => res.json())
.then(data => {

let opt = `<option value="">Select Department</option>`;

data.forEach(d => {
opt += `<option value="${d.id}">${d.department_name}</option>`;
});

document.getElementById("department_id").innerHTML = opt;

});

}

/* ---------------- CREATE ---------------- */
function openCreateModal(){

document.getElementById("modalTitle").innerText = "Add Instructor";

document.getElementById("instructor_id").value = "";
document.getElementById("first_name").value = "";
document.getElementById("last_name").value = "";
document.getElementById("instructor_code").value = "";
document.getElementById("phone").value = "";
document.getElementById("specialization").value = "";
document.getElementById("photo").value = "";

}

/* ---------------- SAVE ---------------- */
function saveInstructor(){


    let id = document.getElementById("instructor_id").value;
    let first_name = document.getElementById("first_name").value.trim();
    let last_name = document.getElementById("last_name").value.trim();
    let code = document.getElementById("instructor_code").value.trim();

    let department_id = document.getElementById("department_id").value;

    let hasError = false;


    document.getElementById("first_name_error").innerText = "";
    document.getElementById("last_name_error").innerText = "";
    document.getElementById("instructor_code_error").innerText = "";



    if(first_name === ""){
        document.getElementById("first_name_error").innerText = "Required";
        hasError = true;
    }

    if(last_name === ""){
        document.getElementById("last_name_error").innerText = "Required";
        hasError = true;
    }

    if(code === ""){
        document.getElementById("instructor_code_error").innerText = "Required";
        hasError = true;
    }

    if(department_id === ""){

        document.getElementById("department_error").innerText = "Required";
        hasError=true;
    }

    if(hasError) return;

    let fd = new FormData();

    fd.append("id", id);
    fd.append("first_name", first_name);
    fd.append("last_name", last_name);
    fd.append("instructor_code", code);
    fd.append("phone", document.getElementById("phone").value);
    fd.append("department_id", document.getElementById("department_id").value);
    fd.append("specialization", document.getElementById("specialization").value);

    if(document.getElementById("photo").files[0]){
        fd.append("photo", document.getElementById("photo").files[0]);
    }

    fd.append("action", id ? "update_instructor" : "create_instructor");

    fetch("../../backend/process.php",{
        method:"POST",
        body:fd
    })
    .then(res=>res.json())
    // .then(res=>console.log(res))
    .then(data=>{

        if(data.status==="error"){
            Swal.fire("Error", data.message, "error");
            return;
        }

        Swal.fire("Success", data.message, "success");

        bootstrap.Modal.getInstance(
        document.getElementById("instructorModal")
        ).hide();

        loadInstructors();

    });

}

/* ---------------- EDIT ---------------- */
function editInstructor(id){

fetch("../../backend/process.php?action=single_instructor&id="+id)
.then(res=>res.json())
.then(i=>{

document.getElementById("modalTitle").innerText="Edit Instructor";

document.getElementById("instructor_id").value=i.id;
document.getElementById("first_name").value=i.first_name;
document.getElementById("last_name").value=i.last_name;
document.getElementById("instructor_code").value=i.instructor_code;
document.getElementById("phone").value=i.phone;
document.getElementById("department_id").value=i.department_id;
document.getElementById("specialization").value=i.specialization;

new bootstrap.Modal(document.getElementById("instructorModal")).show();

});

}

/* ---------------- DELETE ---------------- */
function deleteInstructor(id){

Swal.fire({
title:"Delete?",
icon:"warning",
showCancelButton:true
}).then(res=>{

if(res.isConfirmed){

fetch("../../backend/process.php?action=delete_instructor&id="+id)
.then(res=>res.json())
.then(data=>{

Swal.fire("Deleted", data.message, "success");
loadInstructors();

});

}

});

}

loadInstructors();
loadDepartments();

</script>

</body>
</html>

