<?php
session_start();
require_once("../../backend/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../../auth/login.php");
    exit;
}


$messages = mysqli_query($conn,
    "SELECT * FROM contact_messages ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>

    <title>Manage Contact Messages</title>

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

            <h3>Manage Contact Messages</h3>

        </div>






        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-dark">

                    <tr>

                        <th>ID</th>

                        <th>Full Name</th>

                        <th>Email</th>

                        <th>Subject</th>

                        <th>Message</th>

                        <th>Status</th>

                        <th>Date</th>

                        <th width="180">Actions</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while($row = mysqli_fetch_assoc($messages)) { ?>

                    <tr id="row_<?php echo $row['id']; ?>">

                        <td><?php echo $row['id']; ?></td>

                        <td><?php echo $row['full_name']; ?></td>

                        <td><?php echo $row['email']; ?></td>

                        <td><?php echo $row['subject']; ?></td>

                        <td width="300">
                            <?php echo substr($row['message'],0,100); ?>...
                        </td>

                        <td>

                            <?php if($row['status'] == "unread"){ ?>

                                <span class="badge bg-danger">
                                    Unread
                                </span>

                            <?php } else { ?>

                                <span class="badge bg-success">
                                    Read
                                </span>

                            <?php } ?>

                        </td>

                        <td>
                            <?php echo date("d M Y h:i A",
                            strtotime($row['created_at'])); ?>
                        </td>

                        <td>

                            <button class="btn btn-info btn-sm viewBtn"

                                    data-name="<?php echo htmlspecialchars($row['full_name']); ?>"
                                    data-email="<?php echo htmlspecialchars($row['email']); ?>"
                                    data-subject="<?php echo htmlspecialchars($row['subject']); ?>"
                                    data-message="<?php echo htmlspecialchars($row['message']); ?>"
                                    data-status="<?php echo $row['status']; ?>"
                                    data-date="<?php echo date("d M Y h:i A", strtotime($row['created_at'])); ?>"

                                    data-bs-toggle="modal"
                                    data-bs-target="#viewModal">

                                <i class="bi bi-eye"></i>

                            </button>





                            <?php if($row['status'] == "unread"){ ?>

                            <button class="btn btn-success btn-sm markReadBtn"
                                    data-id="<?php echo $row['id']; ?>">

                                <i class="bi bi-check-circle"></i>

                            </button>

                            <?php } ?>





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





<!-- VIEW MODAL -->

<div class="modal fade" id="viewModal">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Contact Message Details
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>

            </div>






            <div class="modal-body">

                <div class="mb-3">

                    <label><strong>Full Name</strong></label>

                    <p id="view_name"></p>

                </div>





                <div class="mb-3">

                    <label><strong>Email</strong></label>

                    <p id="view_email"></p>

                </div>





                <div class="mb-3">

                    <label><strong>Subject</strong></label>

                    <p id="view_subject"></p>

                </div>





                <div class="mb-3">

                    <label><strong>Message</strong></label>

                    <div class="border rounded p-3 bg-light"
                         id="view_message"></div>

                </div>





                <div class="mb-3">

                    <label><strong>Status</strong></label>

                    <p id="view_status"></p>

                </div>





                <div class="mb-3">

                    <label><strong>Date</strong></label>

                    <p id="view_date"></p>

                </div>

            </div>






            <div class="modal-footer">

                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                    Close

                </button>

            </div>

        </div>

    </div>

</div>






<button id="scrollTop"><i class="bi bi-arrow-up"></i></button>

<script src="../../static/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../static/js/main.js"></script>
<script src="../../static/vendor/chartjs/Chart.js"></script>
<script src="../../static/vendor/sweetalert/js/sweetalert2.all.js"></script>


<script>





/*
|--------------------------------------------------------------------------
| VIEW MESSAGE
|--------------------------------------------------------------------------
*/

document.querySelectorAll(".viewBtn")
.forEach(button => {

    button.addEventListener("click", function(){

        document.getElementById("view_name").innerHTML =
            this.dataset.name;

        document.getElementById("view_email").innerHTML =
            this.dataset.email;

        document.getElementById("view_subject").innerHTML =
            this.dataset.subject;

        document.getElementById("view_message").innerHTML =
            this.dataset.message;

        document.getElementById("view_status").innerHTML =
            this.dataset.status;

        document.getElementById("view_date").innerHTML =
            this.dataset.date;

    });

});






/*
|--------------------------------------------------------------------------
| MARK AS READ
|--------------------------------------------------------------------------
*/

document.querySelectorAll(".markReadBtn")
.forEach(button => {

    button.addEventListener("click", function(){

        let id = this.dataset.id;

        Swal.fire({

            title : "Mark as read?",

            icon : "question",

            showCancelButton : true,

            confirmButtonText : "Yes"

        })

        .then(async (result) => {

            if(result.isConfirmed){

                let formData = new FormData();

                formData.append("action", "mark_contact_message_read");

                formData.append("id", id);

                let response = await fetch("../../backend/process.php", {

                    method : "POST",
                    body : formData

                });

                let data = await response.json();





                if(data.status == "success"){

                    Swal.fire({

                        icon : "success",

                        title : "Success",

                        text : data.message

                    }).then(() => {

                        location.reload();

                    });

                }

            }

        });

    });

});






/*
|--------------------------------------------------------------------------
| DELETE MESSAGE
|--------------------------------------------------------------------------
*/

document.querySelectorAll(".deleteBtn")
.forEach(button => {

    button.addEventListener("click", function(){

        let id = this.dataset.id;

        Swal.fire({

            title : "Are you sure?",

            text : "You want to delete this message?",

            icon : "warning",

            showCancelButton : true,

            confirmButtonText : "Yes Delete"

        })

        .then(async (result) => {

            if(result.isConfirmed){

                let formData = new FormData();

                formData.append("action", "delete_contact_message");

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
