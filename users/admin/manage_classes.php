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

    <title>Manage Classes</title>

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

            <span>Manage Classes</span>

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
                    data-bs-target="#classModal">

                + Add Class

            </button>

        </div>

        <table class="table table-bordered table-hover">

            <thead>

            <tr>

                <th>ID</th>
                <th>Department</th>
                <th>Class Name</th>
                <th>Section</th>
                <th>Academic Year</th>
                <th>Created At</th>
                <th>Actions</th>

            </tr>

            </thead>

            <tbody id="classTable"></tbody>

        </table>

    </div>

</div>

<!-- CLASS MODAL -->
<div class="modal fade" id="classModal">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h5 id="modalTitle">Add Class</h5>

            </div>

            <div class="modal-body">

                <input type="hidden" id="class_id">

                <div class="mb-3">

                    <label>Department</label>

                    <select id="department_id"
                            class="form-control">

                    </select>

                    <small class="text-danger"
                           id="department_id_error"></small>

                </div>

                <div class="mb-3">

                    <label>Class Name</label>

                    <input type="text"
                           id="class_name"
                           class="form-control">

                    <small class="text-danger"
                           id="class_name_error"></small>

                </div>

                <div class="mb-3">

                    <label>Section</label>

                    <input type="text"
                           id="section"
                           class="form-control">

                </div>

                <div class="mb-3">

                    <label>Academic Year</label>

                    <input type="text"
                           id="academic_year"
                           class="form-control"
                           placeholder="2025/2026">

                </div>

            </div>

            <div class="modal-footer">

                <button class="btn btn-secondary"
                        data-bs-dismiss="modal">

                    Close

                </button>

                <button class="btn btn-success"
                        onclick="saveClass()">

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
   LOAD CLASSES
--------------------------*/
function loadClasses(){

    fetch("../../backend/process.php?action=read_classes")

    .then(res => res.json())

    .then(data => {

        let rows = "";

        data.forEach(item => {

            rows += `

            <tr>

                <td>${item.id}</td>

                <td>${item.department_name ?? ''}</td>

                <td>${item.class_name}</td>

                <td>${item.section ?? ''}</td>

                <td>${item.academic_year ?? ''}</td>

                <td>${item.created_at}</td>

                <td>

                    <button class="btn btn-sm btn-warning"
                            onclick="editClass(${item.id})">

                        Edit

                    </button>

                    <button class="btn btn-sm btn-danger"
                            onclick="deleteClass(${item.id})">

                        Delete

                    </button>

                </td>

            </tr>

            `;

        });

        document.getElementById("classTable")
        .innerHTML = rows;

    });

}

/* -------------------------
   LOAD DEPARTMENTS
--------------------------*/
function loadDepartments(){

    fetch("../../backend/process.php?action=read_departments")

    .then(res => res.json())

    .then(data => {

        let options =
        `<option value="">Select Department</option>`;

        data.forEach(department => {

            options += `

            <option value="${department.id}">
                ${department.department_name}
            </option>

            `;

        });

        document.getElementById("department_id")
        .innerHTML = options;

    });

}

/* -------------------------
   OPEN CREATE MODAL
--------------------------*/
function openCreateModal(){

    clearErrors();

    document.getElementById("modalTitle")
    .innerText = "Add Class";

    document.getElementById("class_id").value = "";
    document.getElementById("department_id").value = "";
    document.getElementById("class_name").value = "";
    document.getElementById("section").value = "";
    document.getElementById("academic_year").value = "";

}

/* -------------------------
   SAVE CLASS
--------------------------*/
function saveClass(){

    clearErrors();

    let id =
    document.getElementById("class_id").value;

    let department_id =
    document.getElementById("department_id").value;

    let class_name =
    document.getElementById("class_name")
    .value.trim();

    let section =
    document.getElementById("section")
    .value.trim();

    let academic_year =
    document.getElementById("academic_year")
    .value.trim();

    let hasError = false;

    if(department_id == ""){

        document.getElementById("department_id_error")
        .innerText = "Department is required";

        hasError = true;
    }

    if(class_name == ""){

        document.getElementById("class_name_error")
        .innerText = "Class name is required";

        hasError = true;
    }

    if(hasError){
        return;
    }

    let formData = new FormData();

    formData.append("id", id);
    formData.append("department_id", department_id);
    formData.append("class_name", class_name);
    formData.append("section", section);
    formData.append("academic_year", academic_year);

    let action = id
    ? "update_class"
    : "create_class";

    formData.append("action", action);

    fetch("../../backend/process.php", {

        method:"POST",
        body:formData

    })

    .then(res => res.json())

    .then(data => {

        if(data.status == "error"){

            if(data.department_id_error){

                document.getElementById("department_id_error")
                .innerText = data.department_id_error;
            }

            if(data.class_name_error){

                document.getElementById("class_name_error")
                .innerText = data.class_name_error;
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
            document.getElementById("classModal")
        ).hide();

        loadClasses();

    });

}

/* -------------------------
   EDIT CLASS
--------------------------*/
function editClass(id){

    clearErrors();

    fetch(
    "../../backend/process.php?action=single_class&id="
    + id)

    .then(res => res.json())

    .then(item => {

        document.getElementById("modalTitle")
        .innerText = "Edit Class";

        document.getElementById("class_id")
        .value = item.id;

        document.getElementById("department_id")
        .value = item.department_id;

        document.getElementById("class_name")
        .value = item.class_name;

        document.getElementById("section")
        .value = item.section;

        document.getElementById("academic_year")
        .value = item.academic_year;

        new bootstrap.Modal(
            document.getElementById("classModal")
        ).show();

    });

}

/* -------------------------
   DELETE CLASS
--------------------------*/
function deleteClass(id){

    Swal.fire({

        title: "Delete Class?",
        text: "This action cannot be undone!",
        icon: "warning",
        showCancelButton: true

    })

    .then(result => {

        if(result.isConfirmed){

            fetch(
            "../../backend/process.php?action=delete_class&id="
            + id)

            .then(res => res.json())

            .then(data => {

                Swal.fire(
                    "Deleted",
                    data.message,
                    "success"
                );

                loadClasses();

            });

        }

    });

}

loadClasses();
loadDepartments();

</script>

</body>
</html>
