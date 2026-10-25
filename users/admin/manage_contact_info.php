<?php
session_start();
require_once("../../backend/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../../auth/login.php");
    exit;
}


$contacts = mysqli_query($conn,
    "SELECT * FROM contact_info ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>

    <title>Manage Contact Info</title>

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

            <h3>Manage Contact Information</h3>

            <button class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#createModal">

                <i class="bi bi-plus-circle"></i>
                Add Contact

            </button>

        </div>






        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-dark">

                    <tr>

                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Facebook</th>
                        <th>Telegram</th>
                        <th>Status</th>
                        <th width="180">Actions</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while($row = mysqli_fetch_assoc($contacts)) { ?>

                    <tr id="row_<?php echo $row['id']; ?>">


                        <td><?php echo $row['email']; ?></td>

                        <td><?php echo $row['phone']; ?></td>

                        <td><?php echo $row['office_address']; ?></td>

                        <td><?php echo $row['facebook_link']; ?></td>

                        <td><?php echo $row['telegram_link']; ?></td>

                        <td>

                            <span class="badge bg-success">
                                <?php echo ucfirst($row['status']); ?>
                            </span>

                        </td>

                        <td>

                            <button class="btn btn-warning btn-sm editBtn"

                                    data-id="<?php echo $row['id']; ?>"
                                    data-email="<?php echo htmlspecialchars($row['email']); ?>"
                                    data-phone="<?php echo htmlspecialchars($row['phone']); ?>"
                                    data-address="<?php echo htmlspecialchars($row['office_address']); ?>"
                                    data-facebook="<?php echo htmlspecialchars($row['facebook_link']); ?>"
                                    data-telegram="<?php echo htmlspecialchars($row['telegram_link']); ?>"
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
                        Add Contact Information
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>

                </div>






                <div class="modal-body">

       
                    <div class="mb-3">

                        <label>Email</label>

                        <input type="email"
                               class="form-control"
                               name="email">

                        <small class="text-danger email_error"></small>

                    </div>





                    <div class="mb-3">

                        <label>Phone</label>

                        <input type="text"
                               class="form-control"
                               name="phone">

                        <small class="text-danger phone_error"></small>

                    </div>





                    <div class="mb-3">

                        <label>Address</label>

                        <textarea class="form-control"
                                  name="address"
                                  rows="3"></textarea>

                        <small class="text-danger address_error"></small>

                    </div>





                    <div class="mb-3">

                        <label>Facebook Link</label>

                        <input type="text"
                               class="form-control"
                               name="facebook">

                    </div>





                    <div class="mb-3">

                        <label>Telegram Link</label>

                        <input type="text"
                               class="form-control"
                               name="telegram">

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

                        Save Contact

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
                        Edit Contact Information
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>

                </div>






                <div class="modal-body">


                    <div class="mb-3">

                        <label>Email</label>

                        <input type="email"
                               class="form-control"
                               name="email"
                               id="edit_email">

                    </div>





                    <div class="mb-3">

                        <label>Phone</label>

                        <input type="text"
                               class="form-control"
                               name="phone"
                               id="edit_phone">

                    </div>





                    <div class="mb-3">

                        <label>Address</label>

                        <textarea class="form-control"
                                  name="address"
                                  rows="3"
                                  id="edit_address"></textarea>

                    </div>





                    <div class="mb-3">

                        <label>Facebook Link</label>

                        <input type="text"
                               class="form-control"
                               name="facebook"
                               id="edit_facebook">

                    </div>





                    <div class="mb-3">

                        <label>Telegram Link</label>

                        <input type="text"
                               class="form-control"
                               name="telegram"
                               id="edit_telegram">

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

                        Update Contact

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
| CREATE CONTACT
|--------------------------------------------------------------------------
*/

document.getElementById("createForm")
.addEventListener("submit", async function(e){

    e.preventDefault();

    document.querySelector(".email_error").innerHTML = "";
    document.querySelector(".phone_error").innerHTML = "";
    document.querySelector(".address_error").innerHTML = "";

    let formData = new FormData(this);

    formData.append("action", "create_contact_info");

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


        if(result.errors.email){
            document.querySelector(".email_error").innerHTML = result.errors.email;
        }

        if(result.errors.phone){
            document.querySelector(".phone_error").innerHTML = result.errors.phone;
        }

        if(result.errors.address){
            document.querySelector(".address_error").innerHTML = result.errors.address;
        }

    }

});






/*
|----------------------
| EDIT BUTTON
|----------------------
*/

document.querySelectorAll(".editBtn")
.forEach(button => {

    button.addEventListener("click", function(){

        document.getElementById("edit_id").value = this.dataset.id;

        document.getElementById("edit_email").value = this.dataset.email;

        document.getElementById("edit_phone").value = this.dataset.phone;

        document.getElementById("edit_address").value = this.dataset.address;

        document.getElementById("edit_facebook").value = this.dataset.facebook;

        document.getElementById("edit_telegram").value = this.dataset.telegram;

        document.getElementById("edit_status").value = this.dataset.status;

    });

});






/*
|--------------------------------------------------------------------------
| UPDATE CONTACT
|--------------------------------------------------------------------------
*/

document.getElementById("editForm")
.addEventListener("submit", async function(e){

    e.preventDefault();

    let formData = new FormData(this);

    formData.append("action", "update_contact_info");

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
|--------------------------------------------------------------------------
| DELETE CONTACT
|--------------------------------------------------------------------------
*/

document.querySelectorAll(".deleteBtn")
.forEach(button => {

    button.addEventListener("click", function(){

        let id = this.dataset.id;

        Swal.fire({

            title : "Are you sure?",

            text : "You want to delete this contact?",

            icon : "warning",

            showCancelButton : true,

            confirmButtonText : "Yes Delete"

        })

        .then(async (result) => {

            if(result.isConfirmed){

                let formData = new FormData();

                formData.append("action", "delete_contact_info");

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

