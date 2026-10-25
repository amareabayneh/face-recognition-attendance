<?php require_once("backend/db.php"); ?>

<!DOCTYPE html>
<html>
<head>

<title>Our Services - Student Attendance System</title>

<link href="static/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="static/vendor/bootstrap-icons/bootstrap-icons.css">
<link rel="stylesheet" href="static/css/style.css">

</head>

<body>

<?php include 'navbar.php'; ?>




<!-- ================= SERVICES HEADER ================= -->
<section class="container mt-5 text-center" style="padding-top: 80px;">

<h2>Our Services</h2>

<p class="text-muted">
    Smart digital solutions for modern student attendance management
</p>

</section>






<!-- ================= SERVICES LIST ================= -->
<section class="container mt-4">

<div class="row" id="servicesContainer"></div>

</section>






<?php include 'footer.html'; ?>

<script src="static/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="static/vendor/sweetalert/js/sweetalert2.all.js"></script>
<script>

document.addEventListener("DOMContentLoaded", function(){





/*
|--------------------------------------------------------------------------
| LOAD SERVICES
|--------------------------------------------------------------------------
*/

fetch("backend/process.php", {

    method: "POST",
    body: new URLSearchParams({action: "get_services"})

})

.then(res => res.json())

.then(data => {

    let html = "";

    data.forEach(item => {

        html += `

        <div class="col-md-3 mb-4">

            <div class="card shadow-sm text-center p-3 h-100">

                <i class="bi ${item.icon} display-4 text-warning"></i>

                <h5 class="mt-3">${item.service_name}</h5>

                <p class="text-muted">${item.description}</p>

            </div>

        </div>

        `;
    });

    document.getElementById("servicesContainer").innerHTML = html;

});





});

</script>

</body>
</html>
