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

    <title>Manage Departments</title>

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

    <nav class="navbar bg-light shadow-sm d-flex justify-content-between align-items-center px-3">

        <div class="d-flex align-items-center">

            <i id="sidebarToggle"
               class="bi bi-filter-right fs-1 me-3"
               style="cursor:pointer;"></i>

            <span>Manage Departments</span>

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
                    onclick="openCreateModal()"
                    data-bs-toggle="modal"
                    data-bs-target="#departmentModal">

                + Add Department

            </button>

        </div>

        <table class="table table-bordered table-hover">

            <thead>
            <tr>

                <th>ID</th>
                <th>Department Name</th>
                <th>Department Code</th>
                <th>Created At</th>
                <th>Actions</th>

            </tr>
            </thead>

            <tbody id="departmentTable"></tbody>

        </table>

    </div>

</div>

<!-- MODAL -->
<div class="modal fade" id="departmentModal">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h5 id="modalTitle">Add Department</h5>

            </div>

            <div class="modal-body">

                <input type="hidden" id="department_id">

                <div class="mb-3">

                    <label>Department Name</label>

                    <input type="text"
                           id="department_name"
                           class="form-control">

                    <small class="text-danger"
                           id="department_name_error"></small>

                </div>

                <div class="mb-3">

                    <label>Department Code</label>

                    <input type="text"
                           id="department_code"
                           class="form-control">

                    <small class="text-danger"
                           id="department_code_error"></small>

                </div>

            </div>

            <div class="modal-footer">

                <button class="btn btn-secondary"
                        data-bs-dismiss="modal">

                    Close

                </button>

                <button class="btn btn-success"
                        onclick="saveDepartment()">

                    Save

                </button>

            </div>

        </div>

    </div>

</div>

<button id="scrollTop"><i class="bi bi-arrow-up"></i></button>

<script src="../../static/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../static/vendor/sweetalert/js/sweetalert2.all.js"></script>
<script src="../../static/js/main.js"></script>


<?php include 'profile.html'; ?>


<script>

/* -------------------------
   CLEAR ERRORS
--------------------------*/
function clearErrors(){

    document.querySelectorAll("small")
    .forEach(el => el.innerText = "");

}

/* -------------------------
   LOAD DEPARTMENTS
--------------------------*/
function loadDepartments(){

    fetch("../../backend/process.php?action=read_departments")

    .then(res => res.json())

    .then(data => {

        let rows = "";

        data.forEach(department => {

            rows += `

            <tr>

                <td>${department.id}</td>

                <td>${department.department_name}</td>

                <td>${department.department_code ?? ''}</td>

                <td>${department.created_at}</td>

                <td>

                    <button class="btn btn-sm btn-warning"
                            onclick="editDepartment(${department.id})">

                        Edit

                    </button>

                    <button class="btn btn-sm btn-danger"
                            onclick="deleteDepartment(${department.id})">

                        Delete

                    </button>

                </td>

            </tr>

            `;

        });

        document.getElementById("departmentTable")
        .innerHTML = rows;

    });

}

/* -------------------------
   OPEN CREATE MODAL
--------------------------*/
function openCreateModal(){

    clearErrors();

    document.getElementById("modalTitle")
    .innerText = "Add Department";

    document.getElementById("department_id").value = "";
    document.getElementById("department_name").value = "";
    document.getElementById("department_code").value = "";

}

/* -------------------------
   SAVE DEPARTMENT
--------------------------*/
function saveDepartment(){

    clearErrors();

    let id =
    document.getElementById("department_id").value;

    let department_name =
    document.getElementById("department_name")
    .value.trim();

    let department_code =
    document.getElementById("department_code")
    .value.trim();

    let hasError = false;

    if(department_name == ""){

        document.getElementById("department_name_error")
        .innerText = "Department name is required";

        hasError = true;
    }

    if(hasError){
        return;
    }

    let formData = new FormData();

    formData.append("id", id);
    formData.append("department_name", department_name);
    formData.append("department_code", department_code);

    let action = id
    ? "update_department"
    : "create_department";

    formData.append("action", action);

    fetch("../../backend/process.php", {

        method:"POST",
        body:formData

    })

    .then(res => res.json())

    .then(data => {

        if(data.status == "error"){

            if(data.department_name_error){

                document.getElementById("department_name_error")
                .innerText = data.department_name_error;
            }

            if(data.department_code_error){

                document.getElementById("department_code_error")
                .innerText = data.department_code_error;
            }

            return;
        }

        Swal.fire(
            "Success",
            data.message,
            "success"
        );

        bootstrap.Modal
        .getInstance(
            document.getElementById("departmentModal")
        ).hide();

        loadDepartments();

    });

}

/* -------------------------
   EDIT DEPARTMENT
--------------------------*/
function editDepartment(id){

    clearErrors();

    fetch(
    "../../backend/process.php?action=single_department&id="
    + id)

    .then(res => res.json())

    .then(department => {

        document.getElementById("modalTitle")
        .innerText = "Edit Department";

        document.getElementById("department_id")
        .value = department.id;

        document.getElementById("department_name")
        .value = department.department_name;

        document.getElementById("department_code")
        .value = department.department_code;

        new bootstrap.Modal(
            document.getElementById("departmentModal")
        ).show();

    });

}

/* -------------------------
   DELETE DEPARTMENT
--------------------------*/
function deleteDepartment(id){

    Swal.fire({

        title: "Delete Department?",
        text: "This action cannot be undone!",
        icon: "warning",
        showCancelButton: true

    })

    .then(result => {

        if(result.isConfirmed){

            fetch(
            "../../backend/process.php?action=delete_department&id="
            + id)

            .then(res => res.json())

            .then(data => {

                Swal.fire(
                    "Deleted",
                    data.message,
                    "success"
                );

                loadDepartments();

            });

        }

    });

}

loadDepartments();

</script>

</body>
</html>
