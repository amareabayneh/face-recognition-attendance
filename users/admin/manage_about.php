
<?php
session_start();
require_once("../../backend/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../../auth/login.php");
    exit;
}

$about = mysqli_query($conn,
    "SELECT * FROM about ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>

    <title>Manage About Us</title>

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
            <span>Dashboard</span>
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

    <div class="container mt-5">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h3>Manage About Us</h3>

            <button class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#createModal">

                <i class="bi bi-plus-circle"></i>
                Add About

            </button>

        </div>

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-dark">

                    <tr>

                        <th>ID</th>

                        <th>Image</th>

                        <th>Title</th>

                        <th>Description</th>

                        <th>Mission</th>

                        <th>Vision</th>

                        <th>Status</th>

                        <th width="180">Actions</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while($row = mysqli_fetch_assoc($about)) { ?>

                    <tr id="row_<?php echo $row['id']; ?>">

                        <td><?php echo $row['id']; ?></td>

                        <td>

                            <?php if(!empty($row['image'])) { ?>

                            <img src="../../uploads/about/<?php echo $row['image']; ?>"
                                 width="80"
                                 height="70"
                                 style="object-fit:cover;">

                            <?php } ?>

                        </td>

                        <td><?php echo $row['title']; ?></td>

                        <td width="250">
                            <?php echo substr($row['description'],0,120); ?>...
                        </td>

                        <td width="200">
                            <?php echo substr($row['mission'],0,80); ?>...
                        </td>

                        <td width="200">
                            <?php echo substr($row['vision'],0,80); ?>...
                        </td>

                        <td>

                            <span class="badge bg-success">
                                <?php echo ucfirst($row['status']); ?>
                            </span>

                        </td>

                        <td>

                            <button class="btn btn-warning btn-sm editBtn"

                                    data-id="<?php echo $row['id']; ?>"
                                    data-title="<?php echo htmlspecialchars($row['title']); ?>"
                                    data-description="<?php echo htmlspecialchars($row['description']); ?>"
                                    data-mission="<?php echo htmlspecialchars($row['mission']); ?>"
                                    data-vision="<?php echo htmlspecialchars($row['vision']); ?>"
                                    data-status="<?php echo $row['status']; ?>"

                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal">

                                <i class="bi bi-pencil-square"></i>

                            </button>





                            <button class="btn btn-danger btn-sm deleteBtn"
                                    data-id="<?php echo $row['id']; ?>">

                                <i class="bi bi-trash"></i>

                            </button>

                        </td>

                    </tr>

                    <?php } ?>

                </tbody>

            </table>

        </div>

    </div>
</div>






<!-- CREATE MODAL -->

<div class="modal fade" id="createModal">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <form id="createForm">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Add About Us
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>

                </div>






                <div class="modal-body">

                    <div class="mb-3">

                        <label>Title</label>

                        <input type="text"
                               class="form-control"
                               name="title">

                        <small class="text-danger title_error"></small>

                    </div>





                    <div class="mb-3">

                        <label>Description</label>

                        <textarea class="form-control"
                                  name="description"
                                  rows="5"></textarea>

                        <small class="text-danger description_error"></small>

                    </div>





                    <div class="mb-3">

                        <label>Mission</label>

                        <textarea class="form-control"
                                  name="mission"
                                  rows="3"></textarea>

                        <small class="text-danger mission_error"></small>

                    </div>





                    <div class="mb-3">

                        <label>Vision</label>

                        <textarea class="form-control"
                                  name="vision"
                                  rows="3"></textarea>

                        <small class="text-danger vision_error"></small>

                    </div>





                    <div class="mb-3">

                        <label>Image</label>

                        <input type="file"
                               class="form-control"
                               name="image">

                        <small class="text-danger image_error"></small>

                    </div>





                    <div class="mb-3">

                        <label>Status</label>

                        <select class="form-control"
                                name="status">

                            <option value="active">Active</option>

                            <option value="inactive">Inactive</option>

                        </select>

                    </div>

                </div>






                <div class="modal-footer">

                    <button class="btn btn-primary"
                            type="submit">

                        Save About

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>






<!-- EDIT MODAL -->

<div class="modal fade" id="editModal">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <form id="editForm">

                <input type="hidden"
                       name="id"
                       id="edit_id">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Edit About Us
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>

                </div>






                <div class="modal-body">

                    <div class="mb-3">

                        <label>Title</label>

                        <input type="text"
                               class="form-control"
                               name="title"
                               id="edit_title">

                    </div>





                    <div class="mb-3">

                        <label>Description</label>

                        <textarea class="form-control"
                                  name="description"
                                  rows="5"
                                  id="edit_description"></textarea>

                    </div>





                    <div class="mb-3">

                        <label>Mission</label>

                        <textarea class="form-control"
                                  name="mission"
                                  rows="3"
                                  id="edit_mission"></textarea>

                    </div>





                    <div class="mb-3">

                        <label>Vision</label>

                        <textarea class="form-control"
                                  name="vision"
                                  rows="3"
                                  id="edit_vision"></textarea>

                    </div>





                    <div class="mb-3">

                        <label>New Image</label>

                        <input type="file"
                               class="form-control"
                               name="image">

                    </div>





                    <div class="mb-3">

                        <label>Status</label>

                        <select class="form-control"
                                name="status"
                                id="edit_status">

                            <option value="active">Active</option>

                            <option value="inactive">Inactive</option>

                        </select>

                    </div>

                </div>






                <div class="modal-footer">

                    <button class="btn btn-success"
                            type="submit">

                        Update About

                    </button>

                </div>

            </form>

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





/*
|--------------------------
| CREATE ABOUT
|--------------------------
*/

document.getElementById("createForm")
.addEventListener("submit", async function(e){

    e.preventDefault();

    document.querySelector(".title_error").innerHTML = "";
    document.querySelector(".description_error").innerHTML = "";
    document.querySelector(".mission_error").innerHTML = "";
    document.querySelector(".vision_error").innerHTML = "";
    document.querySelector(".image_error").innerHTML = "";

    let formData = new FormData(this);

    formData.append("action", "create_about");

    let response = await fetch("../../backend/process.php", {

        method : "POST",
        body : formData

    });

    let result = await response.json();
    // console.log(result);

    if(result.status == "success"){

        Swal.fire({
            icon : "success",
            title : "Success",
            text : result.message
        }).then(() => {

            location.reload();

        });

    }else{

        if(result.errors.title){
            document.querySelector(".title_error").innerHTML = result.errors.title;
        }

        if(result.errors.description){
            document.querySelector(".description_error").innerHTML = result.errors.description;
        }

        if(result.errors.mission){
            document.querySelector(".mission_error").innerHTML = result.errors.mission;
        }

        if(result.errors.vision){
            document.querySelector(".vision_error").innerHTML = result.errors.vision;
        }

        if(result.errors.image){
            document.querySelector(".image_error").innerHTML = result.errors.image;
        }

    }

});






/*
|--------------------------
| EDIT BUTTON
|--------------------------
*/

document.querySelectorAll(".editBtn")
.forEach(button => {

    button.addEventListener("click", function(){

        document.getElementById("edit_id").value = this.dataset.id;

        document.getElementById("edit_title").value = this.dataset.title;

        document.getElementById("edit_description").value = this.dataset.description;

        document.getElementById("edit_mission").value = this.dataset.mission;

        document.getElementById("edit_vision").value = this.dataset.vision;

        document.getElementById("edit_status").value = this.dataset.status;

    });

});



/*
|---------------------------
| UPDATE ABOUT
|---------------------------
*/

document.getElementById("editForm")
.addEventListener("submit", async function(e){

    e.preventDefault();

    let formData = new FormData(this);

    formData.append("action", "update_about");

    let response = await fetch("../../backend/process.php", {

        method : "POST",
        body : formData

    });

    let result = await response.json();
    // console.log(result)





    if(result.status == "success"){

        Swal.fire({
            icon : "success",
            title : "Updated",
            text : result.message
        }).then(() => {

            location.reload();

        });

    }

});






/*
|--------------------------
| DELETE ABOUT
|-------------------------
*/

document.querySelectorAll(".deleteBtn")
.forEach(button => {

    button.addEventListener("click", function(){

        let id = this.dataset.id;

        Swal.fire({

            title : "Are you sure?",

            text : "You want to delete this about record?",

            icon : "warning",

            showCancelButton : true,

            confirmButtonText : "Yes Delete"

        })

        .then(async (result) => {

            if(result.isConfirmed){

                let formData = new FormData();

                formData.append("action", "delete_about");

                formData.append("id", id);

                let response = await fetch("../../backend/process.php", {

                    method : "POST",
                    body : formData

                });

                let data = await response.json();





                if(data.status == "success"){

                    Swal.fire({

                        icon : "success",

                        title : "Deleted",

                        text : data.message

                    });

                    document.getElementById("row_" + id).remove();

                }

            }

        });

    });

});

</script>

</body>
</html>

