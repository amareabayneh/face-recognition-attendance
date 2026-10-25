<?php
session_start();
require_once("../../backend/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../../auth/login.php");
    exit;
}

$testimonials = mysqli_query($conn,
    "SELECT * FROM testimonials ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>

    <title>Manage Testimonials</title>

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

            <h3>Manage Testimonials</h3>

            <button class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#createModal">

                <i class="bi bi-plus-circle"></i>
                Add Testimonial

            </button>

        </div>






        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-dark">

                    <tr>


                        <th>Photo</th>

                        <th>Full Name</th>

                        <th>Profession</th>

                        <th>Message</th>

                        <th>Rating</th>

                        <th>Status</th>

                        <th width="180">Actions</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while($row = mysqli_fetch_assoc($testimonials)) { ?>

                    <tr id="row_<?php echo $row['id']; ?>">


                        <td>

                            <?php if(!empty($row['photo'])) { ?>

                            <img src="../../uploads/testimonials/<?php echo $row['photo']; ?>"
                                 width="70"
                                 height="70"
                                 class="rounded-circle"
                                 style="object-fit:cover;">

                            <?php } ?>

                        </td>

                        <td><?php echo $row['full_name']; ?></td>

                        <td><?php echo $row['profession']; ?></td>

                        <td width="300">
                            <?php echo $row['message']; ?>
                        </td>

                        <td>

                            <?php for($i = 1; $i <= $row['rating']; $i++) { ?>

                                <i class="bi bi-star-fill text-warning"></i>

                            <?php } ?>

                        </td>

                        <td>

                            <span class="badge bg-success">
                                <?php echo ucfirst($row['status']); ?>
                            </span>

                        </td>

                        <td>

                            <button class="btn btn-warning btn-sm editBtn"

                                    data-id="<?php echo $row['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($row['full_name']); ?>"
                                    data-profession="<?php echo htmlspecialchars($row['profession']); ?>"
                                    data-message="<?php echo htmlspecialchars($row['message']); ?>"
                                    data-rating="<?php echo $row['rating']; ?>"
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

    <div class="modal-dialog">

        <div class="modal-content">

            <form id="createForm">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Add Testimonial
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>

                </div>






                <div class="modal-body">

                    <div class="mb-3">

                        <label>Full Name</label>

                        <input type="text"
                               class="form-control"
                               name="full_name">

                        <small class="text-danger full_name_error"></small>

                    </div>





                    <div class="mb-3">

                        <label>Profession</label>

                        <input type="text"
                               class="form-control"
                               name="profession">

                        <small class="text-danger profession_error"></small>

                    </div>





                    <div class="mb-3">

                        <label>Message</label>

                        <textarea class="form-control"
                                  name="message"
                                  rows="4"></textarea>

                        <small class="text-danger message_error"></small>

                    </div>





                    <div class="mb-3">

                        <label>Rating</label>

                        <select class="form-control"
                                name="rating">

                            <option value="1">1 Star</option>
                            <option value="2">2 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="5" selected>5 Stars</option>

                        </select>

                    </div>





                    <div class="mb-3">

                        <label>Photo</label>

                        <input type="file"
                               class="form-control"
                               name="photo">

                        <small class="text-danger photo_error"></small>

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

                        Save Testimonial

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>






<!-- EDIT MODAL -->

<div class="modal fade" id="editModal">

    <div class="modal-dialog">

        <div class="modal-content">

            <form id="editForm">

                <input type="hidden"
                       name="id"
                       id="edit_id">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Edit Testimonial
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>

                </div>






                <div class="modal-body">

                    <div class="mb-3">

                        <label>Full Name</label>

                        <input type="text"
                               class="form-control"
                               name="full_name"
                               id="edit_full_name">

                    </div>





                    <div class="mb-3">

                        <label>Profession</label>

                        <input type="text"
                               class="form-control"
                               name="profession"
                               id="edit_profession">

                    </div>





                    <div class="mb-3">

                        <label>Message</label>

                        <textarea class="form-control"
                                  name="message"
                                  rows="4"
                                  id="edit_message"></textarea>

                    </div>





                    <div class="mb-3">

                        <label>Rating</label>

                        <select class="form-control"
                                name="rating"
                                id="edit_rating">

                            <option value="1">1 Star</option>
                            <option value="2">2 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="5">5 Stars</option>

                        </select>

                    </div>





                    <div class="mb-3">

                        <label>New Photo</label>

                        <input type="file"
                               class="form-control"
                               name="photo">

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

                        Update Testimonial

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
|--------------------------------------------------------------------------
| CREATE TESTIMONIAL
|--------------------------------------------------------------------------
*/

document.getElementById("createForm")
.addEventListener("submit", async function(e){

    e.preventDefault();

    document.querySelector(".full_name_error").innerHTML = "";
    document.querySelector(".profession_error").innerHTML = "";
    document.querySelector(".message_error").innerHTML = "";
    document.querySelector(".photo_error").innerHTML = "";

    let formData = new FormData(this);

    formData.append("action", "create_testimonial");

    let response = await fetch("../../backend/process.php", {

        method : "POST",
        body : formData

    });

    let result = await response.json();





    if(result.status == "success"){

        Swal.fire({
            icon : "success",
            title : "Success",
            text : result.message
        }).then(() => {

            location.reload();

        });

    }else{

        if(result.errors.full_name){
            document.querySelector(".full_name_error").innerHTML = result.errors.full_name;
        }

        if(result.errors.profession){
            document.querySelector(".profession_error").innerHTML = result.errors.profession;
        }

        if(result.errors.message){
            document.querySelector(".message_error").innerHTML = result.errors.message;
        }

        if(result.errors.photo){
            document.querySelector(".photo_error").innerHTML = result.errors.photo;
        }

    }

});






/*
|--------------------------------------------------------------------------
| EDIT BUTTON
|--------------------------------------------------------------------------
*/

document.querySelectorAll(".editBtn")
.forEach(button => {

    button.addEventListener("click", function(){

        document.getElementById("edit_id").value = this.dataset.id;

        document.getElementById("edit_full_name").value = this.dataset.name;

        document.getElementById("edit_profession").value = this.dataset.profession;

        document.getElementById("edit_message").value = this.dataset.message;

        document.getElementById("edit_rating").value = this.dataset.rating;

        document.getElementById("edit_status").value = this.dataset.status;

    });

});






/*
|--------------------------------------------------------------------------
| UPDATE TESTIMONIAL
|--------------------------------------------------------------------------
*/

document.getElementById("editForm")
.addEventListener("submit", async function(e){

    e.preventDefault();

    let formData = new FormData(this);

    formData.append("action", "update_testimonial");

    let response = await fetch("../../backend/process.php", {

        method : "POST",
        body : formData

    });

    let result = await response.json();





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
|--------------------------------------------------------------------------
| DELETE TESTIMONIAL
|--------------------------------------------------------------------------
*/

document.querySelectorAll(".deleteBtn")
.forEach(button => {

    button.addEventListener("click", function(){

        let id = this.dataset.id;

        Swal.fire({

            title : "Are you sure?",

            text : "You want to delete this testimonial?",

            icon : "warning",

            showCancelButton : true,

            confirmButtonText : "Yes Delete"

        })

        .then(async (result) => {

            if(result.isConfirmed){

                let formData = new FormData();

                formData.append("action", "delete_testimonial");

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

