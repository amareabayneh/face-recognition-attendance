<?php require_once("backend/db.php"); ?>

<!DOCTYPE html>
<html>
<head>

<title>Student Attendance System</title>

<link href="static/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="static/vendor/bootstrap-icons/bootstrap-icons.css">
<link rel="stylesheet" href="static/css/style.css">

<script src="static/js/sweetalert2.all.min.js"></script>

</head>

<body>

<?php include 'navbar.php'; ?>






<!-- ================= CAROUSEL ================= -->
<div id="carouselExample" class="carousel slide carousel-fade">

  <div class="carousel-inner" id="carouselContainer"></div>

  <button class="carousel-control-prev" type="button"
          data-bs-target="#carouselExample" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>

  <button class="carousel-control-next" type="button"
          data-bs-target="#carouselExample" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>

</div>






<!-- ================= SERVICES ================= -->
<section class="container mt-5">

<h2 class="text-center mb-4">Our Services</h2>

<div class="row" id="servicesContainer"></div>

</section>






<!-- ================= HOW IT WORKS ================= -->
<section class="container mt-5">

<h2 class="text-center mb-4">How It Works</h2>

<div class="row g-4 text-center" id="howItWorksContainer"></div>

</section>






<!-- ================= TESTIMONIALS ================= -->
<section class="container mt-5">

<h2 class="text-center mb-4">Testimonials</h2>

<div id="testimonialCarousel" class="carousel slide carousel-fade">

  <div class="carousel-inner" id="testimonialContainer"></div>

  <button class="carousel-control-prev" type="button"
          data-bs-target="#testimonialCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>

  <button class="carousel-control-next" type="button"
          data-bs-target="#testimonialCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>

</div>

</section>






<?php include 'footer.html'; ?>

<script src="static/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>

document.addEventListener("DOMContentLoaded", function(){

/*
|---------------
| LOAD CAROUSEL
|---------------
*/

fetch("backend/process.php", {

    method: "POST",
    body: new URLSearchParams({action: "get_carousel"})

})

.then(res => res.json())

.then(data => {

    let html = "";

    data.forEach((item, index) => {

        html += `
        <div class="carousel-item ${index === 0 ? 'active' : ''}">
            <img src="uploads/carousel/${item.image}"
                 class="d-block w-100 carousel-img">

            <div class="carousel-caption text-center">
                <h2>${item.title}</h2>
                <p>${item.description}</p>
            </div>
        </div>`;
    });

    document.getElementById("carouselContainer").innerHTML = html;

});






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
        <div class="col-md-3">
            <div class="card text-center p-3 mb-3">

                <i class="bi ${item.icon} display-4 text-warning"></i>

                <h5 class="mt-2">${item.service_name}</h5>

                <p>${item.description}</p>

            </div>
        </div>`;
    });

    document.getElementById("servicesContainer").innerHTML = html;

});






/*
|--------------------------------------------------------------------------
| LOAD HOW IT WORKS
|--------------------------------------------------------------------------
*/

fetch("backend/process.php", {

    method: "POST",
    body: new URLSearchParams({action: "get_how_it_works"})

})

.then(res => res.json())

.then(data => {

    let html = "";

    data.forEach(item => {

        html += `
        <div class="col-md-4">

            <div class="card p-3">

                <i class="bi ${item.icon} display-3 text-warning"></i>

                <h5 class="mt-2">Step ${item.step_number}: ${item.title}</h5>

                <p>${item.description}</p>

            </div>

        </div>`;
    });

    document.getElementById("howItWorksContainer").innerHTML = html;

});






/*
|--------------------------------------------------------------------------
| LOAD TESTIMONIALS
|--------------------------------------------------------------------------
*/

fetch("backend/process.php", {

    method: "POST",
    body: new URLSearchParams({action: "get_testimonials"})

})

.then(res => res.json())

.then(data => {

    let html = "";

    data.forEach((item, index) => {

        html += `
        <div class="carousel-item ${index === 0 ? 'active' : ''}">

            <div class="text-center">

                <img src="uploads/testimonials/${item.photo}"
                     class="rounded-circle mb-3"
                     width="100" height="100">

                <p>"${item.message}"</p>

                <h6>- ${item.full_name}</h6>

            </div>

        </div>`;
    });

    document.getElementById("testimonialContainer").innerHTML = html;

});





});

</script>

</body>
</html>
