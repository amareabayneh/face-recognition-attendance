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

<title>Manage Subjects</title>

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

            <span>Manage Subjects</span>
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
                    data-bs-target="#subjectModal"
                    onclick="openCreateModal()">

                + Add Subject

            </button>
        </div>
        <table class="table table-bordered">

            <thead>
            <tr>
                <th>ID</th>
                <th>Subject Name</th>
                <th>Subject Code</th>
                <th>Credit Hour</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody id="subjectTable"></tbody>

        </table>

    </div>

</div>

<!-- MODAL -->
<div class="modal fade" id="subjectModal">

<div class="modal-dialog">

<div class="modal-content">

<div class="modal-header">
<h5 id="modalTitle">Add Subject</h5>
</div>

<div class="modal-body">

<input type="hidden" id="subject_id">

<div class="mb-2">
<label>Subject Name</label>
<input type="text" id="subject_name" class="form-control">
<small class="text-danger" id="subject_name_error"></small>
</div>

<div class="mb-2">
<label>Subject Code</label>
<input type="text" id="subject_code" class="form-control">
<small class="text-danger" id="subject_code_error"></small>
</div>

<div class="mb-2">
<label>Credit Hour</label>
<input type="number" id="credit_hour" class="form-control">
</div>

</div>

<div class="modal-footer">

<button class="btn btn-secondary" data-bs-dismiss="modal">
Close
</button>

<button class="btn btn-success" onclick="saveSubject()">
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
function loadSubjects(){

fetch("../../backend/process.php?action=read_subjects")
.then(res=>res.json())
.then(data=>{

let rows="";

data.forEach(s=>{

rows += `
<tr>

<td>${s.id}</td>

<td>${s.subject_name}</td>

<td>${s.subject_code}</td>

<td>${s.credit_hour ?? ''}</td>

<td>

<button class="btn btn-warning btn-sm"
onclick="editSubject(${s.id})">
Edit
</button>

<button class="btn btn-danger btn-sm"
onclick="deleteSubject(${s.id})">
Delete
</button>

</td>

</tr>
`;

});

document.getElementById("subjectTable").innerHTML=rows;

});

}

/* ---------------- CLEAR ERRORS ---------------- */
function clearErrors(){

document.querySelectorAll("small")
.forEach(e=>e.innerText="");

}

/* ---------------- OPEN CREATE ---------------- */
function openCreateModal(){

document.getElementById("modalTitle").innerText="Add Subject";

document.getElementById("subject_id").value="";
document.getElementById("subject_name").value="";
document.getElementById("subject_code").value="";
document.getElementById("credit_hour").value="";

clearErrors();

}

/* ---------------- SAVE ---------------- */
function saveSubject(){

clearErrors();

let id = document.getElementById("subject_id").value;

let name = document.getElementById("subject_name").value.trim();
let code = document.getElementById("subject_code").value.trim();
let credit = document.getElementById("credit_hour").value;

let error=false;

if(name===""){
document.getElementById("subject_name_error").innerText="Required";
error=true;
}

if(code===""){
document.getElementById("subject_code_error").innerText="Required";
error=true;
}

if(error) return;

let fd = new FormData();

fd.append("id",id);
fd.append("subject_name",name);
fd.append("subject_code",code);
fd.append("credit_hour",credit);

fd.append("action", id ? "update_subject" : "create_subject");

fetch("../../backend/process.php",{
method:"POST",
body:fd
})
.then(res=>res.json())
.then(data=>{

if(data.status==="error"){

if(data.subject_code_error){
document.getElementById("subject_code_error").innerText=data.subject_code_error;
}

return;
}

Swal.fire("Success",data.message,"success");

bootstrap.Modal.getInstance(
document.getElementById("subjectModal")
).hide();

loadSubjects();

});

}

/* ---------------- EDIT ---------------- */
function editSubject(id){

fetch("../../backend/process.php?action=single_subject&id="+id)
.then(res=>res.json())
.then(s=>{

document.getElementById("modalTitle").innerText="Edit Subject";

document.getElementById("subject_id").value=s.id;
document.getElementById("subject_name").value=s.subject_name;
document.getElementById("subject_code").value=s.subject_code;
document.getElementById("credit_hour").value=s.credit_hour;

new bootstrap.Modal(document.getElementById("subjectModal")).show();

});

}

/* ---------------- DELETE ---------------- */
function deleteSubject(id){

Swal.fire({
title:"Delete Subject?",
icon:"warning",
showCancelButton:true
}).then(res=>{

if(res.isConfirmed){

fetch("../../backend/process.php?action=delete_subject&id="+id)
.then(res=>res.json())
.then(data=>{

Swal.fire("Deleted",data.message,"success");
loadSubjects();

});

}

});

}

loadSubjects();

</script>

</body>
</html>

