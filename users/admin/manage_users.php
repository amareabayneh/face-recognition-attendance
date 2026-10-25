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
    <title>Manage Users</title>

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
            <i id="sidebarToggle" class="bi bi-filter-right fs-1 me-3" style="cursor:pointer;"></i>
            <span>Manage Users</span>
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
        <div style="float: right;">
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#userModal" onclick="openCreateModal()"> + Add User</button>
        </div>
    

        <!-- USERS TABLE -->
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody id="userTable"></tbody>
        </table>

    </div>
</div>

<!-- MODAL -->
<div class="modal fade" id="userModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 id="modalTitle">Add User</h5>
            </div>

            <div class="modal-body">

                <input type="hidden" id="user_id">

                <div class="mb-2">
                    <label>Username</label>
                    <input type="text" id="username" class="form-control" required>
                    <small class="text-danger" id="username_error"></small>
                </div>

                <div class="mb-2">
                    <label>Email</label>
                    <input type="text" id="email" class="form-control" required>
                    <small class="text-danger" id="email_error"></small>
                </div>

                <div class="mb-2">
                    <label>Phone</label>
                    <input type="text" id="phone" class="form-control" required>
                </div>

                <div class="mb-2" id="passwordDiv">
                    <label>Password</label>
                    <input type="password" id="password" class="form-control">
                    <small class="text-danger" id="password_error"></small>
                </div>


                <div class="mb-2">
                    <label>Role</label>
                    <select id="role_id" class="form-control"></select>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-success" onclick="saveUser()">Save</button>
            </div>

        </div>
    </div>
</div>

<!-- CHANGE PASSWORD MODAL -->
<div class="modal fade" id="passwordModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5>Change Password</h5>
            </div>

            <div class="modal-body">

                <input type="hidden" id="password_user_id">

                <div class="mb-3">
                    <label>New Password</label>
                    <input type="password" id="new_password" class="form-control">
                    <small class="text-danger" id="new_password_error"></small>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                <button class="btn btn-primary" onclick="changePassword()">
                    Change Password
                </button>
            </div>

        </div>
    </div>
</div>




<button id="scrollTop"><i class="bi bi-arrow-up"></i></button>

<script src="../../static/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert -->
<script src="../../static/vendor/sweetalert/js/sweetalert2.all.js"></script>
<script src="../../static/js/main.js"></script>

<?php include 'profile.html'; ?>

<script>


/* -----------------------------
   LOAD USERS
------------------------------*/
function loadUsers(){

    fetch("../../backend/process.php?action=read")
    .then(res => res.json())
    .then(data => {

        let rows = "";

        data.forEach(user => {
            rows += `
            <tr>
                <td>${user.id}</td>
                <td>${user.username}</td>
                <td>${user.email}</td>
                <td>${user.role_name}</td>
                <td>${user.is_active == 1 ? "Active" : "Inactive"}</td>
                <td>

                    <button class="btn btn-sm btn-warning"
                    onclick="editUser(${user.id})">
                    Edit
                    </button>

                    <button class="btn btn-sm btn-info"
                    onclick="openPasswordModal(${user.id})">
                    Password
                    </button>

                    ${
                        user.is_active == 1

                        ?

                        `<button class="btn btn-sm btn-secondary"
                        onclick="toggleStatus(${user.id},0)">
                        Deactivate
                        </button>`

                        :

                        `<button class="btn btn-sm btn-success"
                        onclick="toggleStatus(${user.id},1)">
                        Activate
                        </button>`
                    }

                    <button class="btn btn-sm btn-danger"
                    onclick="deleteUser(${user.id})">
                    Delete
                    </button>

                </td>

            </tr>`;
        });

        document.getElementById("userTable").innerHTML = rows;
    });
}

/* -----------------------------
   LOAD ROLES
------------------------------*/
function loadRoles(){

    fetch("../../backend/process.php?action=roles")
    .then(res => res.json())
    .then(data => {

        let options = "";

        data.forEach(role => {
            options += `<option value="${role.id}">${role.role_name}</option>`;
        });

        document.getElementById("role_id").innerHTML = options;
    });
}

/* -----------------------------
   OPEN CREATE MODAL
------------------------------*/
function openCreateModal(){

    document.getElementById("modalTitle").innerText = "Add User";

    document.getElementById("user_id").value = "";
    document.getElementById("username").value = "";
    document.getElementById("email").value = "";
    document.getElementById("phone").value = "";
    document.getElementById("password").value = "";

    /* SHOW PASSWORD FIELD ONLY FOR CREATE */
    document.getElementById("passwordDiv").style.display = "block";

    /* CLEAR ERRORS */
    clearErrors();
}


/* -----------------------------
   CLEAR ERRORS
------------------------------*/
function clearErrors(){

    document.getElementById("username_error").innerText = "";
    document.getElementById("email_error").innerText = "";
    document.getElementById("password_error").innerText = "";
    document.getElementById("new_password_error").innerText = "";

}


/* -----------------------------
   SAVE (CREATE / UPDATE)
------------------------------*/

function saveUser(){

    clearErrors();

    let id = document.getElementById("user_id").value;
    let username = document.getElementById("username").value.trim();
    let email = document.getElementById("email").value.trim();
    let phone = document.getElementById("phone").value.trim();
    let password = document.getElementById("password").value.trim();
    let role_id = document.getElementById("role_id").value;

    let hasError = false;

    /* -----------------------
       JS VALIDATION
    ------------------------*/

    if(username == ""){

        document.getElementById("username_error").innerText =
        "Username is required";

        hasError = true;
    }

    if(email == ""){

        document.getElementById("email_error").innerText =
        "Email is required";

        hasError = true;
    }

    /* PASSWORD REQUIRED ONLY DURING CREATE */
    if(id == "" && password == ""){

        document.getElementById("password_error").innerText =
        "Password is required";

        hasError = true;
    }

    if(hasError){
        return;
    }

    let formData = new FormData();

    formData.append("id", id);
    formData.append("username", username);
    formData.append("email", email);
    formData.append("phone", phone);
    formData.append("password", password);
    formData.append("role_id", role_id);

    let action = id ? "update" : "create";

    formData.append("action", action);

    fetch("../../backend/process.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        clearErrors();

        if(data.status == "error"){

            if(data.username_error){
                document.getElementById("username_error").innerText =
                data.username_error;
            }

            if(data.email_error){
                document.getElementById("email_error").innerText =
                data.email_error;
            }

            if(data.password_error){
                document.getElementById("password_error").innerText =
                data.password_error;
            }

            return;
        }

        Swal.fire({
            icon: "success",
            title: "Success",
            text: data.message
        });

        bootstrap.Modal
        .getInstance(document.getElementById("userModal"))
        .hide();

        loadUsers();

    });

}

/* -----------------------------
   EDIT USER
------------------------------*/
function editUser(id){

    clearErrors();

    fetch("../../backend/process.php?action=single&id=" + id)
    .then(res => res.json())
    .then(user => {

        document.getElementById("modalTitle").innerText =
        "Edit User";

        document.getElementById("user_id").value = user.id;
        document.getElementById("username").value = user.username;
        document.getElementById("email").value = user.email;
        document.getElementById("phone").value = user.phone;
        document.getElementById("role_id").value = user.role_id;

        /* HIDE PASSWORD FIELD */
        document.getElementById("passwordDiv").style.display =
        "none";

        new bootstrap.Modal(
            document.getElementById("userModal")
        ).show();

    });

}




/* -----------------------------
   DELETE USER
------------------------------*/
function deleteUser(id){

    Swal.fire({
        title: "Delete User?",
        text: "This action cannot be undone!",
        icon: "warning",
        showCancelButton: true
    })

    .then(result => {

        if(result.isConfirmed){

            fetch("../../backend/process.php?action=delete&id=" + id)

            .then(res => res.json())

            .then(data => {

                if(data.status == "error"){

                    Swal.fire(
                        "Error",
                        data.message,
                        "error"
                    );

                    return;
                }

                Swal.fire(
                    "Deleted",
                    data.message,
                    "success"
                );

                loadUsers();

            });

        }

    });

}



/* -----------------------------
   OPEN PASSWORD MODAL
------------------------------*/
function openPasswordModal(id){

    document.getElementById("password_user_id").value = id;

    document.getElementById("new_password").value = "";

    clearErrors();

    new bootstrap.Modal(
        document.getElementById("passwordModal")
    ).show();

}

/* -----------------------------
   CHANGE PASSWORD
------------------------------*/
function changePassword(){

    clearErrors();

    let password =
    document.getElementById("new_password").value.trim();

    if(password == ""){

        document.getElementById("new_password_error").innerText =
        "Password is required";

        return;
    }

    let formData = new FormData();

    formData.append(
        "id",
        document.getElementById("password_user_id").value
    );

    formData.append("password", password);

    formData.append("action", "change_password");

    fetch("../../backend/process.php",{
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        Swal.fire(
            "Success",
            data.message,
            "success"
        );

        bootstrap.Modal
        .getInstance(document.getElementById("passwordModal"))
        .hide();

    });

}

/* -----------------------------
   ACTIVATE / DEACTIVATE
------------------------------*/
function toggleStatus(id, status){

    let text =
    status == 1
    ? "Activate this user?"
    : "Deactivate this user?";

    Swal.fire({
        title: text,
        icon: "question",
        showCancelButton: true
    })
    .then(result => {

        if(result.isConfirmed){

            fetch(
            "../../backend/process.php?action=toggle_status&id="
            + id + "&status=" + status
            )
            .then(res => res.json())
            .then(data => {

                Swal.fire(
                    "Success",
                    data.message,
                    "success"
                );

                loadUsers();

            });

        }

    });

}



/* INIT */
loadUsers();
loadRoles();

</script>

</body>
</html>
